<?php
require_once '../users/init.php';


require_once $abs_us_root.$us_url_root.'users/includes/header.php';
require_once $abs_us_root.$us_url_root.'users/includes/navigation.php';
require_once $abs_us_root.$us_url_root.'pages/helpers/pages_helper.php';
require_once $abs_us_root.$us_url_root.'users/includes/navigation.php';
?>

<?php if (!securePage($_SERVER['PHP_SELF'])){die();}
if ($settings->site_offline==1){die("The site is currently offline.");}

?>

<div id="page-wrapper">
    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-sm-12">
                <h1 class="page-header">
                    Mappings Page
                </h1>
                <!-- Content goes here -->
                <div id="maping-tab-container"  >
                    <ul class="nav nav-tabs tabs-to-lazy-load-iframes" id="mapping-tabs" >
                        <li class="nav active"><a href="#completed-jobs" data-toggle="tab">Templates</a></li>
                        <li class="nav"><a href="#template_columns" data-toggle="tab">Columns for templates</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="completed-jobs" >
                            <iframe src="template_grid.php" name="template-table" id="template-table" height="600px" width="1300px" style="border:0 none;"></iframe>

                        </div>

                        <div class="tab-pane fade" id="template_columns" data-src="alias_column_grid.php?template_id=1" >
                            <iframe src="" id="waiting-checks-grid-table" height="600px" width="1300px" style="border:0 none;"></iframe>
                        </div>


                    </div>
                </div>




                <!-- Content Ends Here -->
            </div> <!-- /.col -->
        </div> <!-- /.row -->
    </div> <!-- /.container -->
</div> <!-- /.wrapper -->





<?php require_once $abs_us_root.$us_url_root.'users/includes/page_footer.php'; // the final html footer copyright row + the external js calls ?>

<!-- Place any per-page javascript here -->
<script src="js/lazy_load_tabs.js"></script>
<script src="js/mapping.js"></script>




<?php require_once $abs_us_root.$us_url_root.'users/includes/html_footer.php'; // currently just the closing /body and /html ?>
