<?php
	$tpiece="Send a Message";
	$meta3="This is where you can send me a message.";
	$mextras="<meta name='robots' content='none'>\n";
	define("ROOT_LOC",$_SERVER['DOCUMENT_ROOT']);
	require_once ROOT_LOC . "/includes/init.php";
	require_once ROOT_LOC . "/includes/header.php";
 ?>
<div class="jumbotron pt-5 pb-5 mb-5">
	<div class="container ps-md-3 pe-md-3 mb-4">
		<h1 class="mb-5">Send a Message</h1>
		<?php require_once ROOT_LOC . "/includes/taster_photos.php"; ?>
	</div>
</div>
<div class="container mb-5">
	<form action="https://getform.io/f/7e86714d-6189-4d77-86d8-30c1bba9ec7d" method="post" accept-charset="utf-8">
		<div class="mb-3">
			<label class="form-label">Name</label>
			<input class="form-control shadow-none" name="fullname" placeholder="John Smith">
		</div>
		<div class="mb-3">
			<label class="form-label">Email</label>
			<input class="form-control shadow-none" type="email" name="email" placeholder="name@example.com">
		</div>
		<div class="mb-3">
			<label class="form-label">Message</label>
			<textarea class="form-control shadow-none" name="message" rows="10"></textarea>
		</div>
		<label class="form-label">GDPR Agreement</label>
		<div class="form_check mb-3">
			<input type="checkbox" value="I consent to the storage of my submitted information in order to receive a reply." name="gdpr" id="gdpr">
			<label class="form-label ms-1" for="gdpr">I consent to the storage of my submitted information in order to receive a reply.</label>
		</div>
		<div class="d-grid gap-2">
			<button class="btn btn-secondary shadow-none stretch">Send</button>
		</div>
	</form>
</div>
<?php require_once ROOT_LOC . "/includes/footer.php"; ?>
