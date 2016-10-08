<?php
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/helpers/helpers.php';
require_once $abs_us_root.$us_url_root.'pages/helpers/pages_helper.php';
require_once  'includes/slickgrid_header_includes.php';

$db = DB::getInstance();
$settingsQ = $db->query("Select * FROM app_settings");
$settings = $settingsQ->first();
if ($settings->site_offline==1){
    die("The site is currently offline.");
}

if (!securePage($_SERVER['PHP_SELF'])){die(); }

?>

<style>
    .template-name {
        text-align: center;
        font-size: larger;
        font-weight: bold;
    }
</style>

</head>

<body style="background-color: floralwhite">

<?php

$template_id = intval(Input::get('template_id'));
if (!$template_id) {
    die("Need Template ID");
}

$template_info = $db->query('select * from wx_templates where id = ?', [$template_id]);
$templateO = $template_info->first();
if (!$templateO) {
    die("Cound not find template info for tempate # ".$template_id );
}
$template = json_decode(json_encode($templateO),false);

$table_names_for_us = ['contractor', 'homebuilder', 'parking', 'pricecomparison',
    'propertyactivity', 'propertyconstruction', 'propertydeed', 'propertydocuments',
    'propertygf', 'propertyitem', 'propertyiteminstallation','propertymain', 'propertypaint',
    'propertypermit', 'storage', 'swimmingpool' ];

sort($table_names_for_us);



$column_names = get_column_names_hash($table_names_for_us);

$flagsQ = $db->query('select * from wx_template_flags where 1 ORDER BY name,priority ASC',[]);
$flagsO = $flagsQ->results();
$flags = json_decode(json_encode($flagsO),false);


$alias_query = $db->query('select * from wx_template_columns where wx_template_id = ? ORDER BY rank ASC',[$template_id]);
$alias = $alias_query->results();
$alias = json_decode(json_encode($alias),true);
//have to add something to delete the row
for($i=0; $i<sizeof($alias); $i++) {
    $alias[$i]['delete_row'] = 1;
}



$table_name_string = ':';
for($i=0; $i < sizeof($table_names_for_us); $i++) {
    $name = $table_names_for_us[$i];
    $table_name_string .= ",$name:$name";
}

$flag_string = ':';
for($i=0; $i < sizeof($flags); $i++) {
    $name = $flags[$i]->name;
    $id = $flags[$i]->id;
    $flag_string .= ",$name:$id";
}



//convert each entry of column names to encoded select statement

$cols_lookup = [];
foreach ($column_names as $table => $entry) {
    $cols_lookup[$table] = ':';
    for($i=0; $i < sizeof($entry); $i++) {
        $name = $entry[$i];
        $cols_lookup[$table] .= ",$name:$name";
    }
}

?>
<!-- important to keep class main-header as this is where notifications from grid system go -->
<div class="main-header" style="width: 950px"></div>
<div class="template-name">
    <span><?= $template->name ?></span>
</div>
<button type="button" class="bgb-button " id="make-new-row" onclick="create_new_row()">
    New Column Alias
</button>

    <div  style="width:1290px;padding: 0px;margin: 0px">
        <div id="myGrid" data-gridvar='grid_var' style="width:100%;height:500px;padding: 0px;margin: 0px"></div>
    </div>

<script>
    var table_data = <?= json_encode($alias) ;?>;
    var options_for_table_names = '<?=$table_name_string?>';
    var options_for_flags = '<?=$flag_string?>';
    var options_for_columns = <?= json_encode($cols_lookup) ;?>;
    var template_id = <?= $template_id ?>;
</script>

<?php require_once  'includes/slickgrid_js_includes.php'?>
<script src="js/slickgrids/table_setups/column_alias_setup.js"></script>

<script>
    //this is called when the table column is changed, and we want to display the fields for the table
    function change_column_choices(row_data,row_id) {
        return true; //so we refresh the row
    }
</script>


</body>
</html>
