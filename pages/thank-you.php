<?php
	$tpiece="Send a Message";
	$meta3="This is where you can send me a message.";
	define("ROOT_LOC",$_SERVER['DOCUMENT_ROOT']);
	require_once ROOT_LOC . "/includes/init.php";
	require_once ROOT_LOC . "/includes/header.php";
 ?>
<div class="jumbotron pt-5 pb-5 mb-5">
	<div class="container ps-md-3 pe-md-3 mb-4">
		<h1 class="mb-5">Thank You!</h1>
		<?php require_once ROOT_LOC . "/includes/taster_photos.php"; ?>
	</div>
</div>
<div class="container mb-5">
	<p class="large-text ms-5 me-5">Your message has been received. If it needs a response, that will happen as soon as possible. In the meantime, feel free to look around at what else is here. New material gets added on an ongoing basis so you are more than welcome to return at any time. Hope you are enjoying your visit.</p>
</div>
<?php require_once ROOT_LOC . "/includes/footer.php"; ?>
