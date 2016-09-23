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

</head>

<body style="background-color: floralwhite">

<?php
$table_names_for_us = ['contractor', 'homebuilder', 'parking', 'pricecomparison',
    'propertyactivity', 'propertyconstruction', 'propertydeed', 'propertydocuments',
    'propertygf', 'propertyitem', 'propertyiteminstallation', 'propertypaint',
    'propertypermit', 'storage', 'swimmingpool' ];

$column_names = get_column_names_hash($table_names_for_us);

$alias_query = $db->get('wx_alias_column',[]);
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
<button type="button" class="bgb-button " id="make-new-row" onclick="create_new_row()">
    New Column Alias
</button>

    <div style="width:890px;" style="padding: 0px;margin: 0px">
        <div id="myGrid" data-gridvar='grid_var' style="width:100%;height:500px;padding: 0px;margin: 0px"></div>
    </div>

<script>
    var table_data = <?= json_encode($alias) ;?>;
    var options_for_table_names = '<?=$table_name_string?>';
    var options_for_columns = <?= json_encode($cols_lookup) ;?>;
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
