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
                        <li class="nav active"><a href="#completed-jobs" data-toggle="tab">Block Alias</a></li>
                        <li class="nav"><a href="#waiting-for-checks" data-toggle="tab">Column Alias</a></li>

                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade in active" id="completed-jobs" >
                            <iframe src="alias_block_grid.php" name="completed-grid-table" id="completed-grid-table" height="600px" width="950px" style="border:0 none;"></iframe>

                        </div>

                        <div class="tab-pane fade" id="waiting-for-checks" data-src="alias_column_grid.php" >
                            <iframe src="" id="waiting-checks-grid-table" height="550px" width="1100px" style="border:0 none;"></iframe>
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



<?php require_once $abs_us_root.$us_url_root.'users/includes/html_footer.php'; // currently just the closing /body and /html ?>
