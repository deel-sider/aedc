<?php

define("ROOT_LOC",$_SERVER['DOCUMENT_ROOT']);
$file1 = ROOT_LOC . '/bootstrap/dist/css/bootstrap.min.css';
$file2 = ROOT_LOC . '/css/main.css';
$file3 = ROOT_LOC . '/css/cc.css';

header('Content-type: text/css');
ob_clean();
flush();

readfile($file1);
readfile($file2);
readfile($file3);

?>
