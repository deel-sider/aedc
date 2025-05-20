<?php

	// Initialisation
	require_once ROOT_LOC . "/outdoors/wp-includes/functions.php";

	// Getting data
	$query_main = "SELECT album_code, photo_order, photo_file_name, photo_date, photo_main_label, photo_image_label, photo_height, photo_width, photo_camera_used, photo_date FROM "
				. "pg_album_entries where photo_path='$gallerySelection'";
	$photo_row = $wpdb->get_row("$query_main");
	$query_texts = "select photo_text from pg_album_entry_texts where album_code='$photo_row->album_code' and photo_order='$photo_row->photo_order'";
	$photo_texts = $wpdb->get_col("$query_texts");
	$query_album = "select album_name, album_slug, album_path from pg_album_intros where album_code='$photo_row->album_code'";
	$album_row = $wpdb->get_row("$query_album");

	// Page description for meta tag
	$orig = '/"/';
	$repl = "";
	$meta1 = substr(strip_tags($photo_texts[0]),0,155);
	$meta2 = preg_replace($orig,$repl,$meta1);
	$last_pos_amp = strrpos($meta2,"&");
	$last_pos_scn = strrpos($meta2,";");
	if ($last_pos_amp > $last_pos_scn) {
		$meta3 = substr($meta2,0,$last_pos_amp);
	}
	else {
		$meta3 = $meta2;
	}

	// Header
	$tpiece = $photo_row->photo_image_label . " &raquo; Photo Gallery";
	require_once ROOT_LOC . "/includes/header.php";

?>

<div class="jumbotron pt-5 pb-5">
	<div class="container ps-md-3 pe-md-3 mt-4 mb-4">
		<h1 class="mb-5"><?php echo "$photo_row->photo_main_label"; ?></h1>
		<div class="centre mt-5 mb-5">
			<?php
				echo "<img class='img-main' src='/photo_gallery_images/$album_row->album_slug/full_size/$photo_row->photo_file_name' width='$photo_row->photo_width' height='$photo_row->photo_height' alt='$photo_row->photo_image_label' style='max-width: $photo_row->photo_width;' />\n";
			?>
		</div>
		<?php
			foreach ($photo_texts as $photo_text) {
				echo "<p class='photo-text'>$photo_text</p>\n";
			}
		?>
		<a class="btn btn-secondary stretch mt-5 pt-3 pb-3" href="/photos<?php echo $album_row->album_path; ?>">See more photos from this album (<?php echo $album_row->album_name; ?>)</a>
	</div>
</div>

<?php require_once ROOT_LOC . "/includes/photo_albums.php"; ?>
<?php require_once ROOT_LOC . "/includes/footer.php"; ?>
