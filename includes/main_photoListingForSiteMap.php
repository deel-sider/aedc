<?php

// Extracting title information for blog posts and printing list of links to screen

$photos = $wpdb->get_results("SELECT photo_path, photo_main_label FROM pg_album_entries order by photo_num");

foreach ($photos as $photo) {
			$line="<p class='left'><a href='/photos$photo->photo_path' class='creamBack'>$photo->photo_main_label</a></p>";
			echo "\t\t\t$line\n";
}

?>
