<?php


function upload_data_from_file($filepath) {

    $errors = [];
    try {
        $blocker = new Blocks($filepath);
        if ($blocker->hasErrors()) {
            $errors = $blocker->getErrors();
        } else {
            $blocks = $blocker->getBlocks();
            Session::put('last_property_uploaded',$blocks);
            writeBlocksToDB($blocks);
        }
    }
    catch (Exception $e) {
        array_push($errors,$e->getMessage());
    }
    return $errors;
}

function writeBlocksToDB($blocks) {

}



function get_property_hash($dbf) {

    /*
    logic:
1) seperate everything into blocks, if single lines then they go into main blocks, all other blocks go into block_list

2)
structure is {main,installs}
Go through each block in block list,
see if its a child block, or parent block

 its a child block if
1) has tile,siding,stone,paint,trim in the first line name
2) a block has a brand,type,color
 else its a parent block

  last_parent_block = nil
  loop (blocks in list) {
  do
    if child block attach to parent_block_children, error if no last_parent_block
    if parent block put in installs, and make this the last parent_block
  block has type: ii
  children: []
  data: []
     *
     */
    $ret = [];

    # first pass, add to main block, or add data to node and put in other blocks
    $num_rec=$dbf->dbf_num_rec;
    $main_block = [];
    $blocks = [];
    $this_block = [];
    for($i=0; $i<$num_rec; $i++) {
        $row = $dbf->getRow($i);
        if (empty($row[1])) {
            if (sizeof($this_block) == 1) {
                foreach ($this_block as $k=>$v) {
                    $main_block[$k] = $v;
                }

            } else if(sizeof($this_block) == 0 ) {
                continue;
            } else {
                array_push($blocks,$this_block);
            }
            $this_block = [];
        } else {
            $w_unkwn = (empty($row[2])) ? $row[3] : $row[2];
            $w = ForceUTF8\Encoding::toUTF8($w_unkwn);
            $kk = ForceUTF8\Encoding::toUTF8($row[1]);
            $this_block[$kk] = $w;
        }




    }

    //now go through each of the $blocks, and put the children with the parents
    $node_list = [];

    $last_parent_index = -1;
    $last_type_block = false;
    for($i = 0; $i < sizeof($blocks); $i++) {
        $b = $blocks[$i];
        $type_block = determine_block_type($b,$last_type_block);
        $first_line = reset($b);
        foreach ($b as $k=>$v) {
            if (empty($k)) {
                $first_line = $v;
            } else {
                $first_line = $k;
            }
            break;
        }
        if (empty($first_line)) {
            throw new Exception("Empty for $i");
        }
        #has tile,siding,stone,paint,trim in the first line name, but not painting
        $words_for_children = ['tile','siding','stone','paint','trim'];
        $words_for_grownups = ['painting'];
        $b_is_child = false;
        foreach ($words_for_children as $word) {
            if (stripos($first_line, $word) !== false) {
                $b_is_child = true;
                break;
            }
        }
        if ($b_is_child) {
            foreach ($words_for_grownups as $word) {
                if (stripos($first_line, $word) !== false) {
                    $b_is_child = false;
                    break;
                }
            }
        } else {
            //if not a child , it might still be if it has the keys: brand, type, color
            $match_columns = ['brand'=>0,'type'=>0,'color'=>0];
            $count_matches = 0;
            foreach ($b as $k=>$v) {
                foreach ($match_columns as $word=>$count) {
                    if ($count == 0) {
                        if (stripos($k, $word) !== false) {
                            $count_matches ++;
                            $match_columns[$word] += 1;
                        }
                    }

                }
            }
            if ($count_matches == sizeof($match_columns)) { //matched all three columns
                $b_is_child = true;
            }

        }



        if ($b_is_child) {
            if ($last_parent_index < 0) {
                throw new Exception('Parent was null for the child');
            }
            $node = ['type'=>'xx','data'=>$b];
            $node_list[$last_parent_index]['children'][] = $node;
        } else {
            $node = ['type'=>'ii','data'=>$b,'children'=>[]];
            $node_list[] = $node;
            $last_parent_index = sizeof($node_list) -1;
        }



    }
    $ret = ['main'=>$main_block,'blocks'=>$node_list];
    return $ret;
}

function determine_block_type($b,$last_type_block) {

}

function add_single_property_to_db($propertyHash) {

}

function add_list_to_db($dbf) {
    $num_rec=$dbf->dbf_num_rec;
    for($i=0; $i<$num_rec; $i++) {
        if ($row = $dbf->getRow($i)) {
            $line_hash = getRowHash($row);
            addRowHashToDB($line_hash);
        } else {

        }
    }
}

function getRowHash($row) {
    return [];
}

function addRowHashToDB($line_hash) {

}

#takes the string value, pads it to the left with 0 and makes 3 wide sections
function get_string_filepath_from_id($i) {
    $number_folders = 4;
    $t = str_pad($i,$number_folders * 3,'0',STR_PAD_LEFT);
    $ret = '';
    for($p = 0; $p < $number_folders ; $p++) {
        $ret =  $ret.'/'.substr($t,$p * 3,3);
    }
    return $ret;

}


#returns true or false depending on if it can connect to url
function is_connected($url_to_check)
{
    //http://stackoverflow.com/questions/4860365/determine-in-php-script-if-connected-to-internet
    $connected = fsockopen($url_to_check, 80);

    //website, port  (try 80 or 443)
    if ($connected){
        $is_conn = true; //action when connected
        fclose($connected);
    }else{
        $is_conn = false; //action in connection failure
    }
    return $is_conn;

}




function base64_to_image($base64_string, $output_file) {
    //http://stackoverflow.com/questions/15153776/convert-base64-string-to-an-image-file
    $ifp = fopen($output_file, "wb");

    $data = explode(',', $base64_string);

    fwrite($ifp, base64_decode($data[1]));
    fclose($ifp);

    return $output_file;
}


function getGUID(){
    if (function_exists('com_create_guid')){
        return trim(com_create_guid(),'{}');
    }else{
        $charid = strtoupper(md5(uniqid( mt_rand(), true)));
        $hyphen = chr(45);// "-"
        $uuid = ''
            .substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);

        return $uuid;
    }
}

//this allows us to post stuff without relying on curl, which some php environments do not have configured
function rest_helper($url, $params = null, $verb = 'GET', $format = 'json')
{
    $cparams = array(
        'http' => array(
            'method' => $verb,
            'ignore_errors' => true,
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n".
                                        "User-Agent:MyAgent/1.0\r\n"
        )
    );
    if ($params !== null) {
        $params = http_build_query($params);
        if ($verb == 'POST') {
            $cparams['http']['content'] = $params;
        } else {
            $url .= '?' . $params;
        }
    }

    $context = stream_context_create($cparams);
    $fp = @fopen($url, 'rb', false, $context);
    if (!$fp) {
        $res = false;
    } else {
        // If you're trying to troubleshoot problems, try uncommenting the
        // next two lines; it will show you the HTTP response headers across
        // all the redirects:
        // $meta = stream_get_meta_data($fp);
        // var_dump($meta['wrapper_data']);
        $res = @stream_get_contents($fp);
    }

    if ($res === false) {
        throw new Exception("$verb $url failed: $php_errormsg");
    }

    switch ($format) {
        case 'json':
            $r = json_decode($res);
            if ($r === null) {
                throw new Exception("failed to decode $res as json");
            }
            return $r;

        case 'xml':
            $r = simplexml_load_string($res);
            if ($r === null) {
                throw new Exception("failed to decode $res as xml");
            }
            return $r;
    }
    return $res;
}


function printOkJSONAndDie($phpArray=[]) {
    header('Content-Type: application/json');
    $phpArray['status'] = 'ok';
    $phpArray['valid'] = true;
    $out = json_encode($phpArray);
    if ($out) {
        print $out;
    } else {
        printErrorJSONAndDie( json_last_error_msg());
    }
    exit;
}

function printErrorJSONAndDie($message,$phpArray=[]) {
    header('Content-Type: application/json');
    $phpArray['status'] = 'error';
    $phpArray['valid'] = false;
    $phpArray['message'] = $message;
    $out = json_encode($phpArray);
    if ($out) {
        print $out;
    } else {
        print json_last_error_msg();
    }

    exit;
}

function printLastJsonError() {
    if (!function_exists('json_last_error_msg')) {
        function json_last_error_msg() {
            static $ERRORS = array(
                JSON_ERROR_NONE => 'No error',
                JSON_ERROR_DEPTH => 'Maximum stack depth exceeded',
                JSON_ERROR_STATE_MISMATCH => 'State mismatch (invalid or malformed JSON)',
                JSON_ERROR_CTRL_CHAR => 'Control character error, possibly incorrectly encoded',
                JSON_ERROR_SYNTAX => 'Syntax error',
                JSON_ERROR_UTF8 => 'Malformed UTF-8 characters, possibly incorrectly encoded'
            );

            $error = json_last_error();
            return isset($ERRORS[$error]) ? $ERRORS[$error] : 'Unknown error';
        }
    }
}


//for debugging
function print_nice($elem,$max_level=15,$print_nice_stack=array()){
    //if (is_object($elem)) {$elem = object_to_array($elem);}
    if(is_array($elem) || is_object($elem)){
        if(in_array($elem,$print_nice_stack,true)){
            echo "<span style='color:red'>RECURSION</span>";
            return;
        }
        $print_nice_stack[]=&$elem;
        if($max_level<1){
            echo "<span style='color:red'>reached maximum level</span>";
            return;
        }
        $max_level--;
        echo "<table border=1 cellspacing=0 cellpadding=3 width=100%>";
        if(is_array($elem)){
            echo '<tr><td colspan=2 style="background-color:#333333;"><strong><span style="color:white">ARRAY</span></strong></td></tr>';
        }else{
            echo '<tr><td colspan=2 style="background-color:#333333;"><strong>';
            echo '<span style="color:white">OBJECT Type: '.get_class($elem).'</span></strong></td></tr>';
        }
        $color=0;
        foreach($elem as $k => $v){
            if($max_level%2){
                $rgb=($color++%2)?"#888888":"#BBBBBB";
            }else{
                $rgb=($color++%2)?"#8888BB":"#BBBBFF";
            }
            echo '<tr><td valign="top" style="width:40px;background-color:'.$rgb.';">';
            echo '<strong>'.$k."</strong></td><td style='background-color:white;color:black'>";
            print_nice($v,$max_level,$print_nice_stack);
            echo "</td></tr>";
        }
        echo "</table>";
        return;
    }
    if($elem === null){
        echo "<span style='color:green'>NULL</span>";
    }elseif($elem === 0){
        echo "0";
    }elseif($elem === true){
        echo "<span style='color:green'>TRUE</span>";
    }elseif($elem === false){
        echo "<span style='color:green'>FALSE</span>";
    }elseif($elem === ""){
        echo "<span style='color:green'>EMPTY STRING</span>";
    }else{
        echo str_replace("\n","<strong><span style='color:green'>*</span></strong><br>\n",$elem);
    }
}


function TO($object){ //Test Object
    if(!is_object($object)){
        throw new Exception("This is not a Object");
    }
    if(class_exists(get_class($object), true)) echo "<pre>CLASS NAME = ".get_class($object);
    $reflection = new ReflectionClass(get_class($object));
    echo "<br />";
    echo $reflection->getDocComment();

    echo "<br />";

    $metody = $reflection->getMethods();
    foreach($metody as $key => $value){
        echo "<br />". $value;
    }

    echo "<br />";

    $vars = $reflection->getProperties();
    foreach($vars as $key => $value){
        echo "<br />". $value;
    }
    echo "</pre>";
}


# this protects from having a umask set in a shared environment
function mkdir_r($dirName, $rights=0777){
    $dirs = explode('/', $dirName);
    $dir='';
    foreach ($dirs as $part) {
        $dir.=$part.'/';
        if (!is_dir($dir) && strlen($dir)>0) {
            mkdir($dir);
            chmod($dir, $rights);
        }

    }
}

function test_site_connection($theURL) {
    if(intval(get_http_response_code($theURL)) < 400){
        return true;
    }

    return false;
}

function get_http_response_code($theURL) {
    $headers = get_headers($theURL);
    return substr($headers[0], 9, 3);
}

function get_column_names_hash($table_name_array) {
    $db = DB::getInstance();
    $quoted_table_names = [];
        for($i=0;$i<sizeof($table_name_array); $i++) {
            $paz = "'" . $table_name_array[$i] . "'" ;
            array_push($quoted_table_names,$paz);
        }
    $table_name_list = implode(',',$quoted_table_names);
    $database_name = getenv('DB_NAME');
    /** @noinspection SqlResolve */
    $query = $db->query( "SELECT table_name,column_name
                            FROM INFORMATION_SCHEMA.COLUMNS
                            WHERE table_name in ( $table_name_list )
                            AND table_schema = ?
                            and column_name not in 
                            ('id','created_at','modified_at','UserId','EntryDate','app_user')
                            and column_name NOT REGEXP 'id$'",
                    [$database_name]);
    // do not match any id columns and not some other columns
    $cols = $query->results();
    $ret = [];
    foreach ($cols as $col) {

        $tablename = $col->table_name;
        $column_name = $col->column_name;
        if (empty($ret[$tablename])) {
            $ret[$tablename] = [];
        }
        array_push($ret[$tablename],$column_name);
    }
    return $ret;

}
