<?php $tpiece="Privacy Policy";?>
<?php $meta3="This is my personal website and it is my policy to respect your privacy regarding any information that may be collected while operating the website.";?>
<?php
 define("ROOT_LOC",$_SERVER['DOCUMENT_ROOT']);
 require_once ROOT_LOC . "/includes/init.php";
 require_once ROOT_LOC . "/includes/header.php";
 ?>
<div class="jumbotron pt-5 pb-5">
<div class="container ps-md-3 pe-md-3 mb-4">
<h1 class="mb-5">Privacy Policy</h1>
<?php require_once ROOT_LOC . "/includes/taster_photos.php"; ?>
</div>
</div>
<div id="pp" class="container mt-5 mb-5">

<?php
 include ROOT_LOC . '/vendor/autoload.php';
 use League\CommonMark\CommonMarkConverter;
 $converter = new CommonMarkConverter();
 echo $converter->convertToHtml(file_get_contents(ROOT_LOC . '/markdown/pp.md')); 
?>

</div>
<?php require_once ROOT_LOC . "/includes/footer.php"; ?>
