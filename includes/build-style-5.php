<?php

define("__ROOT_LOC",$_SERVER['DOCUMENT_ROOT']);
$file1 = __ROOT_LOC . '/bootstrap5/css/bootstrap.min.css';
$file2 = __ROOT_LOC . '/css/main5.css';
$file3 = __ROOT_LOC . '/css/cc.css';
$file5 = __ROOT_LOC . '/css/cache.css';

$ft1=filemtime($file1);
$ft2=filemtime($file2);
$ft3=filemtime($file3);
$ft5=filemtime($file5);

if ( $ft1 > $ft5 || $ft2 > $ft5 || $ft3 > $ft5 ) {

	$handle1 = fopen($file1,"r");
	$handle2 = fopen($file2,"r");
	$handle3 = fopen($file3,"r");

	$contents1 = fread($handle1, filesize($file1));
	$contents2 = fread($handle2, filesize($file2));
	$contents3 = fread($handle3, filesize($file3));

	$contents = $contents1 . " " . $contents2 . " " . $contents3;

	$mcontents = str_replace( array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $contents);

	fclose($handle1);
	fclose($handle2);
	fclose($handle3);

	$handle5 = fopen($file5, 'w');
	fwrite($handle5, $contents);
	fclose($handle5);

}

?>
