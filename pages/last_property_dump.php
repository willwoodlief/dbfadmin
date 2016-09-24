<?php

#makes new block alias
require_once '../users/init.php';
require_once $abs_us_root.$us_url_root.'/users/includes/header_json.php';
require_once $abs_us_root.$us_url_root.'pages/helpers/pages_helper.php';

try {


    if ($settings->site_offline==1){printErrorJSONAndDie("The site is currently offline.");}
    if (!securePage($_SERVER['PHP_SELF'],false)){ printErrorJSONAndDie('Not Authorized '); }
    $db = DB::getInstance();
    $user = new User();
    if (Session::exists('last_property_uploaded')) {
        $last_mohekan = Session::get('last_property_uploaded');
        printOkJSONAndDie($last_mohekan);
    } else {
        printErrorJSONAndDie('no last uploaded property found');
    }


}
catch(Exception $e) {
    printErrorJSONAndDie($e->getMessage());
}






