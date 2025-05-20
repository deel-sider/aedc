<?php

	// Initialisation
	define("ROOT_LOC",$_SERVER['DOCUMENT_ROOT']);
	require_once ROOT_LOC . "/includes/init.php";
//	require_once ROOT_LOC . "/outdoors/wp-includes/functions.php";

	// Initialising photo numeric identifiers for section and country
	$gallerySelection=$_SERVER["PATH_INFO"];
	if ($gallerySelection == "") {
		$gallerySelection=$_SERVER["SCRIPT_URI"];
		$gallerySelection=ltrim(preg_replace("/http:\/\/www.assortedexplorations.com\/photoGallery_galleries\//"," ",$gallerySelection));
	}

	// Check if album required
	$query_base = "SELECT count(*) as ct";
	$query_num = "$query_base FROM pg_album_intros where album_path in ('" . $gallerySelection . "')";
	$resultNum=$wpdb->get_var($query_num);
	if ($resultNum > 0) {
		require_once ROOT_LOC . "/includes/photo_album.php";
	}

	// Check if photo required
	$query_base = "SELECT count(*) as ct";
	$query_num = "$query_base FROM pg_album_entries where photo_path in ('" . $gallerySelection . "')";
	$resultNum=$wpdb->get_var($query_num);
	if ($resultNum > 0) {
		require_once ROOT_LOC . "/includes/photo_enlargement.php";
	}

	// Redirect to error page when there is no match
	if ($resultNum == 0) {
		header('Location: /photos');
	}

?>
