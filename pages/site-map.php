<?php $tpiece="Site Map";?>
<?php $meta3="This is where you find tales of my wanderings around Europe & North America. Plenty of photos too.";?>
<?php
 define("ROOT_LOC",$_SERVER['DOCUMENT_ROOT']);
 require_once ROOT_LOC . "/includes/init.php";
 require_once ROOT_LOC . "/includes/header.php";
 ?>
<div class="jumbotron pt-5 pb-5">
<div class="container ps-md-3 pe-md-3 mb-4">
<h1 class="mb-5">Site Map</h1>
<?php require_once ROOT_LOC . "/includes/taster_photos.php"; ?>
<?php
 include ROOT_LOC . '/vendor/autoload.php';
 use League\CommonMark\CommonMarkConverter;
 $converter = new CommonMarkConverter();
 echo $converter->convertToHtml(file_get_contents(ROOT_LOC . '/markdown/sm.md'));
?>
</div>
</div>
<?php require_once ROOT_LOC . "/includes/sm-listing.php"; ?>
<?php require_once ROOT_LOC . "/includes/footer.php"; ?>
