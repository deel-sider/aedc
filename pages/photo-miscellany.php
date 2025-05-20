<?php $tpiece="From Celtic Roots to Pacific Coasts: A Living Gallery of Visual Journeys";?>
<?php $meta3="Here are photos from my travels around Europe & North America, arranged in albums for your viewing pleasure.";?>
<?php
 define("ROOT_LOC",$_SERVER['DOCUMENT_ROOT']);
 require_once ROOT_LOC . "/includes/init.php";
 require_once ROOT_LOC . "/includes/header.php";
 ?>
<div class="jumbotron pt-5 pb-5">
<div class="container ps-md-3 pe-md-3 mb-4">
<h1 class="mb-5"><?php echo str_replace(":", ":<br />", $tpiece);?></h1>
<?php require_once ROOT_LOC . "/includes/taster_photos.php"; ?>
<?php

include ROOT_LOC . '/vendor/autoload.php';

use League\CommonMark\CommonMarkConverter;

$converter = new CommonMarkConverter();

echo $converter->convertToHtml(file_get_contents(ROOT_LOC . '/markdown/pm_top.md'));

?>
</div>
</div>
<?php require_once ROOT_LOC . "/includes/photo_albums.php"; ?>
<?php require_once ROOT_LOC . "/includes/footer.php"; ?>
