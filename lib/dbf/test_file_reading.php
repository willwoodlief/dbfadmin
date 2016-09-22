<?php
include 'file_byte_reader.php';

$what = 'ok';
$dbf = new FileByteReader('/home/will/htdocs/dbfadmin/tmp/short.dbf');
try {
    $first_32_bytes = $dbf->getBytes(0,32);
    $empty_string = $dbf->getBytes(16,0);
    $next_10_bytes = $dbf->getBytes(32,10);
    $last_100_bytes = $dbf->getBytes($dbf->getFileSize() - 100,100);
}
catch (Exception $e) {
    $what = $e->getMessage();
}
finally {
    echo  $what;
    //dechex(ord($empty_string[1]))
    //dbf->getBytes(16,20)
}
