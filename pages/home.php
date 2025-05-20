<?php $tpiece="Ongoing Wandering: Tales from Mountains, Coasts and Beyond";?>
<?php $meta3="This is where you find tales of my wanderings around Europe & North America. Plenty of photos too.";?>
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
 echo $converter->convertToHtml(file_get_contents(ROOT_LOC . '/markdown/home_top.md'));
?>
</div>
</div>
<div class="container mt-4">
<div class="row">
<div class="col-md-6 pe-md-4 pb-3">
<h3>Travel Jottings</h3>
<p><a class="btn btn-secondary mt-4 mb-3 shadow-none stretch" href="/travel">Peruse Jottings</a></p>
<?php echo $converter->convertToHtml(file_get_contents(ROOT_LOC . '/markdown/tj_home.md')); ?>
</div>
<div class="col-md-6 ps-md-4 pb-3">
<h3>Outdoor Odysseys</h3>
<p><a class="btn btn-secondary mt-4 mb-3 shadow-none stretch" href="/outdoors">Experience Odysseys</a></p>
<?php echo $converter->convertToHtml(file_get_contents(ROOT_LOC . '/markdown/od_home.md')); ?>
</div>
<div class="col-md-6 pe-md-4 pb-3">
<h3>Photo Gallery</h3>
<p><a class="btn btn-secondary mt-4 mb-3 shadow-none stretch" href="/photos">View Photos</a></p>
<?php echo $converter->convertToHtml(file_get_contents(ROOT_LOC . '/markdown/pg_home.md')); ?>
</div>
<div class="col-md-6 pe-md-4 pb-3">
<h3>Varied Surroundings</h3>
<p><a class="btn btn-secondary mt-4 mb-3 shadow-none stretch" href="/surroundings">Explore Surroundings</a></p>
<?php echo $converter->convertToHtml(file_get_contents(ROOT_LOC . '/markdown/vs_home.md')); ?>
</div>
</div>
</div>
<?php require_once ROOT_LOC . "/includes/footer.php"; ?>
