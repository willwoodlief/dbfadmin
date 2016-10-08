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



$template_info = $db->query('select * from wx_templates where 1 order by name', []);
$templateO = $template_info->results();
if (!$templateO) {
    die("Cound not find template info for any template ! " );
}
$templates = json_decode(json_encode($templateO),true);

for($i=0; $i<sizeof($templates); $i++) {
    $templates[$i]['delete_row'] = 1;
}



?>
<!-- important to keep class main-header as this is where notifications from grid system go -->
<div class="main-header" style="width: 1060px"></div>
<div style="text-align: center;width: 1060px">
    <h2>Select Template for Uploading</h2>
    <table style="text-align: left">
        <tr>
            <td style="width: 50%;padding: 20px"> This sets the template to use automatically when a file is uploaded, whoever is logged on
                <br> To edit a template click on the template name and goto the Columns tab
            </td>
            <td style="text-align: left;width: 50%;padding: 20px">

                <select id="set-template-for-upload">
                    <option
                        value="0"
                        <?=  ($settings->wx_template_id == null) ? 'selected="selected"' : ''?>
                    >
                        (none)
                    </option>

                    <?php for($i=0; $i<sizeof($templates); $i++) { ?>
                        <option
                            value=" <?= $templates[$i]['id']  ?>"
                            <?=  ($settings->wx_template_id == $templates[$i]['id']) ? 'selected="selected"' : ''?>
                        >
                            <?= $templates[$i]['name']  ?>
                        </option>
                    <?php }?>
                </select>

            </td>
        </tr>
    </table>



</div>
<button type="button" class="bgb-button " id="make-new-row" onclick="create_new_row()">
    New Template
</button>

<div  style="width:1060px;padding: 0px;margin: 0px">
    <div id="myGrid" data-gridvar='grid_var' style="width:100%;height:300px;padding: 0px;margin: 0px"></div>
</div>

<script>
    var table_data = <?= json_encode($templates) ;?>;


</script>

<?php require_once  'includes/slickgrid_js_includes.php'?>
<script src="js/slickgrids/table_setups/template_setup.js"></script>




</body>
</html>
