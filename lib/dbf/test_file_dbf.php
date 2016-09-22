<?php
include 'dbf_class.php';

$what = 'ok';

try {
    $dbf = new dbf_class('/home/will/htdocs/dbfadmin/tmp/long.dbf');
    $row1 = $dbf->getRow(0);
    $row2 = $dbf->getRow(1);
    $row3 = $dbf->getRow(1209);
    $row4 = $dbf->getRow(1210);
    $names = $dbf->dbf_names;
}
catch (Exception $e) {
    $what = $e->getMessage();
}
finally {
    echo  $what;
    echo "total memory usage was ".  memory_get_peak_usage();
    //dechex(ord($empty_string[1]))
    //dbf->getBytes(16,20)
}
