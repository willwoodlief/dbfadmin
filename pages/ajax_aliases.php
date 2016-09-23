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
    $action = Input::get('action');
    $table_key = Input::get('table_name');
    $allowed_tables = ['column'=>'wx_alias_column','block'=>'wx_alias_block'];
    if (isset($allowed_tables[$table_key])) {
        $table_name = $allowed_tables[$table_key];
    } else {
        printErrorJSONAndDie("Invalid table name of $table_key");
    }
    if (empty($action)) {
        printErrorJSONAndDie('need action');
    }
    switch ($action) {
        case 'new': {
            $check = $db->insert($table_name,['app_user_id' => $user->data()->id]);
            if (!$check) {
                printErrorJSONAndDie('Could not create new record');
            }
            $last_id = $db->lastId();
            $row = $db->get($table_name, array('id', '=', $last_id));
            $alias = $row->results();
            $ret = ['data_array' => $alias];
            printOkJSONAndDie($ret);
            break;
        }
        case 'delete' : {
            $blob_id = intval(Input::get('id'));
            if (empty($blob_id)) {
                printErrorJSONAndDie('Need ID');
            }
            $check = $db->deleteById($table_name,$blob_id);
            if ($check === false) {
                printErrorJSONAndDie("Could not deelte record [$blob_id]");
            }

            printOkJSONAndDie(['id'=>$blob_id]);
            break;
        }
        case 'edit' : {
            $blob_id = intval(Input::get('id'));
            if (empty($blob_id)) {
                printErrorJSONAndDie('Need ID');
            }
            $changed_field = Input::get('changed_field');
            if (empty($changed_field)) {
                printErrorJSONAndDie('Need Changed Field');
            }
            $params = $_POST['param'];
            $data_to_change = $params[$changed_field];
            switch ($changed_field) {
                case 'dependency_level': {
                    $valye = intval($data_to_change);
                    break;
                }
                case 'is_ignored': {
                    if ($data_to_change == 'true') {
                        $data_to_change = 1;
                    }
                    $valye = intval($data_to_change);
                    if ($valye != 0) {
                        $valye = 1;
                    }
                    break;
                }
                default: {
                    //do nothing
                    $valye = Input::sanitize($data_to_change);
                }
            }
            $update_hash = ['app_user_id' => $user->data()->id,$changed_field => $valye];

            $db->update($table_name,$blob_id,$update_hash);
            printOkJSONAndDie();
            break;
        }
        default: {
            printErrorJSONAndDie('unknown action');
        }
    }


}
catch(Exception $e) {
    printErrorJSONAndDie($e->getMessage());
}






