<?php

	// Initialisation
	ob_start();
	require_once ROOT_LOC . "/outdoors/wp-includes/functions.php";

	// Initialising photo numeric identifiers for section and country
	$gallerySelection = $_SERVER["PATH_INFO"];
	if ($gallerySelection == "") {
		$gallerySelection = $_SERVER["SCRIPT_URI"];
		$gallerySelection = ltrim(preg_replace("/http:\/\/www.assortedexplorations.com\/photoGallery_galleries\//"," ",$gallerySelection));
	}

	// Getting path for subsetting
	$query = "SELECT album_code, album_slug, album_name, country_name FROM pg_album_intros where album_path='$gallerySelection'";
	$album_row = $wpdb->get_row($query);
	$query = "select intro_text from pg_album_intro_texts where album_code=$album_row->album_code";
	$photo_texts = $wpdb->get_col("$query");

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
	if ($album_row->album_name == $album_row->country_name) $tpiece = $album_row->album_name . " &raquo; Photo Gallery";
	else $tpiece=$album_row->album_name . ", " . $album_row->country_name . " &raquo; Photo Gallery";
	require_once ROOT_LOC . "/includes/header.php";

?>

<div class="jumbotron pt-5 pb-5">
	<div class="container ps-md-3 pe-md-3 mt-4 mb-4">
		<h1 class="mb-5"><?php echo "$album_row->album_name"; if ($album_row->album_name != $album_row->country_name) echo ", $album_row->country_name"; ?></h1>
		<div class="container mb-4">
			<div class="row">
				<?php
					$query_base = "SELECT DISTINCT photo_file_name, photo_album_label, thumb_height, thumb_width, photo_path";
					$query = "$query_base FROM pg_album_entries where album_code in ('$album_row->album_code')";
					$results = $wpdb->get_results($query);
					$_i = 0;
					foreach ($results as $result) {
						$line="<div class='col-xl-4 centre'><a href='/photos$result->photo_path'><img class='img-thumbnail mt-3 mb-3' src='/photo_gallery_images/$album_row->album_slug/small_size/$result->photo_file_name' width='$result->thumb_width' height='$result->thumb_height' alt='$result->photo_album_label' title=" . '"Click to enlarge: ' . $result->photo_album_label. '" /></a></div>';
						echo $line;
					}
				?>
			</div>
		</div>
		<?php
			foreach ($photo_texts as $photo_text) {
				echo "<p>$photo_text</p>";
			}
		?>
	</div>
</div>

<?php require_once ROOT_LOC . "/includes/photo_albums.php"; ?>
<?php require_once ROOT_LOC . "/includes/footer.php"; ?>

<?php ob_end_flush();  ?>
