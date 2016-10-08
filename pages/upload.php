<?php
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'users/includes/header.php';
require_once $abs_us_root.$us_url_root.'users/includes/navigation.php';
require_once $abs_us_root.$us_url_root.'pages/helpers/pages_helper.php';
require_once $abs_us_root.$us_url_root.'pages/helpers/ziphelper.php';
require_once $abs_us_root.$us_url_root.'lib/blocks.php';
require_once $abs_us_root.$us_url_root.'lib/ForceUTF8/Encoding.php';

require_once $abs_us_root.$us_url_root.'users/includes/navigation.php';
require_once $abs_us_root.$us_url_root.'lib/dbf/dbf_class.php';
?>

<?php if (!securePage($_SERVER['PHP_SELF'])){die();}
if ($settings->site_offline==1){die("The site is currently offline.");}

?>


<?php
$b_show_disconect_message = false;
$validation = new Validate();
$error_count = 0;
$dbf_file_path = false ;


if(isset($_POST['uploads'])) {


    $token = $_POST['csrf'];
    if (!Token::check($token)) {
        // die('Token doesn\'t match!');
        //do not include in the page, as it will be idle for long periods and that expires the token, its already protected by login
    }


    if (isset( $_FILES['dbf_file']) && !empty( $_FILES['dbf_file']['tmp_name'])) {
        $dbf_file_path = $_FILES['dbf_file']['tmp_name'];

        //run it past the zip checker
        try {

            $zipper = new GetOneFileFromZip($dbf_file_path);
            if ($zipper->isZipFile()) {
                $dbf_file_path = $zipper->getExtractedTempFilePath();
                //zipper will delete the file on the close of this script
            }
        }
        catch (Exception $e) {
            $validation->addError($e->getMessage());
            $error_count++;
        }

    } else {
        $validation->addError('Need a file uploaded');
        $error_count++;
    }


    if ($dbf_file_path && $error_count == 0) {
        // do something with $dbf_file_path
        //is it zipped ? if so then we need to unzip it and put it in another temp folder
        $errors = upload_data_from_file($dbf_file_path);
        for($i=0; $i < sizeof($errors); $i++) {
            $error_count++;
            $validation->addError($errors[$i]);
        }
    }




}
?>

<div id="page-wrapper">

    <div class="row">
        <?php if ($error_count == 0 && $dbf_file_path) { ?>

            <div class=" col-sm-offset-3 col-sm-6 alert alert-success" >
                <strong><i class="fa fa-fw fa-check"></i> Uploaded to be Processed</strong>
            </div>



        <?php } else {?>

        <?php } ?>

        <div id="form-errors" class="col-sm-offset-2 col-sm-8">
            <?=$validation->display_errors();?>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Page Heading -->
        <div class="row">
            <div class="col-sm-offset-3 col-sm-6">
                <h1 class="page-header">
                    Upload Page
                </h1>

                <form class="" enctype="multipart/form-data"  action="upload.php" name="uploads" method="post">



                    <div class="form-group">
                        <label for="front_of_card">Database file to be uploaded, this can be zipped</label>
                        <input type="file" class="form-control" name="dbf_file" id="dbf_file" value="">
                    </div>


                    <input type="hidden" name="csrf" value="<?=Token::generate();?>" />

                    <p><input class='btn btn-primary' type='submit' name="uploads" value='Upload Database File' /></p>
                </form>



            </div>
        </div> <!-- /.row -->
    </div> <!-- /.container -->
</div> <!-- /.wrapper -->


<?php require_once $abs_us_root.$us_url_root.'users/includes/page_footer.php'; // the final html footer copyright row + the external js calls ?>

<!-- Place any per-page javascript here -->




<?php require_once $abs_us_root.$us_url_root.'users/includes/html_footer.php'; // currently just the closing /body and /html ?>
