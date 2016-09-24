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

$alias_query = $db->get('wx_alias_block',[]);
$alias = $alias_query->results();
$alias = json_decode(json_encode($alias),true);
//have to add something to delete the row
for($i=0; $i<sizeof($alias); $i++) {
    $alias[$i]['delete_row'] = 1;
}


$table_names_for_us = ['contractor', 'homebuilder', 'parking', 'pricecomparison',
    'propertyactivity', 'propertyconstruction', 'propertydeed', 'propertydocuments',
    'propertygf', 'propertyitem', 'propertyiteminstallation', 'propertymain','propertypaint',
    'propertypermit', 'storage', 'swimmingpool' ];
sort($table_names_for_us);

$table_name_string = ':';
for($i=0; $i < sizeof($table_names_for_us); $i++) {
    $name = $table_names_for_us[$i];
    $table_name_string .= ",$name:$name";
}

?>
<!-- important to keep class main-header as this is where notifications from grid system go -->
<div class="main-header" style="width: 830px"></div>
<button type="button" class="bgb-button " id="make-new-row" onclick="create_new_row()">
    New Block Alias
</button>

    <div style="width:810px;" style="padding: 0px;margin: 0px">
        <div id="myGrid" data-gridvar='grid_var' style="width:100%;height:500px;padding: 0px;margin: 0px"></div>
    </div>

<script>
    var table_data = <?= json_encode($alias) ;?>;
    var options_for_table_names = '<?=$table_name_string?>';
</script>

<?php require_once  'includes/slickgrid_js_includes.php'?>
<script src="js/slickgrids/table_setups/block_alias_setup.js"></script>


</body>
</html>
