<?php
$address=$_POST['archive-dropdown'];
if ($address != "") {
	header("Location: $address");
}
else {
	header("Location: /outdoors/");
}
?>
