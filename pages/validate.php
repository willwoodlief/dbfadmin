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
                <h1 class="page-header text-center">
                    Validation
                </h1>
                <!-- Content goes here -->
                <div id="validate-tab-container"  >
                    <ul class="nav nav-tabs tabs-to-lazy-load-iframes" id="validate-tabs" >
                        <li class="nav active"><a href="#validate-batch" data-toggle="tab">Validate Batch</a></li>
                        <li class="nav"><a href="#property-debug" data-toggle="tab">Property Debug</a></li>

                    </ul>
                    <div class="tab-content">


                        <div class="tab-pane fade in active" id="validate-batch" >

                            <h3 style="">Validate Batch</h3>
                            <div class="row">
                                <div class="form-group col-sm-6">

                                    <div class=" col-sm-9">
                                        <label for="batch_id">
                                        Enter Batch Number to preview it.
                                        Click <a href="upload_history.php"> here </a> for a list of batches
                                        </label>
                                    </div>
                                    <div class="input-group col-sm-3 ">
                                        <input id="batch_id" type="text" class="form-control">
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" type="button">Go!</button>
                                        </span>
                                    </div>




                                </div>

                                <div class="col-sm-2 pull-right">
                                    <button type="button" class="btn btn-warning">Delete Batch</button>
                                </div>
                            </div>


                            <ul class="nav nav-tabs tabs-to-lazy-load-iframes" id="validate-tabs" >
                                <li class="nav active"><a href="#property-list" data-toggle="tab">Property List</a></li>
                                <li class="nav"><a href="#property-items" data-toggle="tab">Selected Property Items</a></li>

                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="property-list" >
                                    Property List
                                </div>

                                <div class="tab-pane fade" id="property-items" data-src="alias_column_grid.php" >
                                    Property Items
                                </div>


                            </div>

                        </div>

                        <div class="tab-pane fade" id="property-debug" data-src="last_property_dump.php" >
                            <h3>This displays the raw dump of the last property uploaded</h3>
                            <div class="well well-sm">
                                <iframe src="" id="property-dump" height="2000px" width="1000px" style="border:0 none;"></iframe>
                            </div>
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
