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

    if (empty($action)) {
        printErrorJSONAndDie('need action');
    }
    $table_name = 'wx_templates';
    switch ($action) {

        case 'set_default' : {
            $blob_id = intval(Input::get('template_id'));

            if ($blob_id == 0) {
                $blob_id = null;
            }
            $fields=array('wx_template_id'=>$blob_id);
            $db->update('app_settings',1,$fields);
            printOkJSONAndDie('set default to '.$blob_id);
        }

        case 'new': {

            $check = $db->insert($table_name,['created_by_user_id' => $user->data()->id,'ts_created_at'=>time()]);
            if (!$check) {
                printErrorJSONAndDie('Could not create new template');
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
                case 'name': {
                    $valye = to_utf8(Input::sanitize($data_to_change));
                    break;
                }
                case 'notes': {
                    $valye = to_utf8(Input::sanitize($data_to_change));
                    break;
                }

                default: {
                    //do nothing
                    $valye = to_utf8(Input::sanitize($data_to_change));
                }
            }
            $update_hash = ['ts_modified_at' => time(),$changed_field => $valye];

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






