<?php
	$tpiece="Musings from Mastodon";
	$meta3="These are my toots on the Mastodon decentralized and open-source social media platform.";
	define("ROOT_LOC",$_SERVER['DOCUMENT_ROOT']);
	require_once ROOT_LOC . "/includes/init.php";
	require_once ROOT_LOC . "/includes/header.php";
 ?>
 <div class="jumbotron pt-5 pb-5">
<div class="container ps-md-3 pe-md-3 mb-4">
<h1 class="mb-5"><?php echo $tpiece; ?></h1>
<?php require_once ROOT_LOC . "/includes/taster_photos.php"; ?>
<?php
 include ROOT_LOC . '/vendor/autoload.php';
 use League\CommonMark\CommonMarkConverter;
 $converter = new CommonMarkConverter();
 echo $converter->convertToHtml(file_get_contents(ROOT_LOC . '/markdown/mastodon.md'));
?>
</div>
</div>
<div class="container mt-5 mb-5" id="mastodon">
<?php
    $files = scandir(ROOT_LOC . "/markdown/mastodon/");
    for ($i=1; $i < 3; $i++) {
        array_shift($files);
    }
    foreach (array_reverse($files) as $file) {
        echo '<div class="mastodon-box mb-3">';
        echo $converter->convertToHtml(file_get_contents(ROOT_LOC . '/markdown/mastodon/' . $file));
        echo "</div>";
    }
?>
</div>
 <?php require_once ROOT_LOC . "/includes/footer.php"; ?>