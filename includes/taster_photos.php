<div class="container mb-4">
	<div class="row">
		<?php

		// Getting number of real photos

		$rowno = $wpdb->get_var("SELECT count(*) FROM pg_album_entries");

		// Generating random number and normalising value

		$query_string="";

		$c=0;

		// Build query string  for random photos
		while ($c<3) {
			$photoNum = mt_rand(0,$rowno);
			if ($photoNum >= 0 && !preg_match("/$photoNum/",$query_string)) {
				$result = $wpdb->get_var("select photo_file_name from pg_album_entries where photo_num=$photoNum and cast(thumb_width as UNSIGNED) > cast(thumb_height as UNSIGNED) and (photo_camera_used like 'Pentax%' or photo_camera_used like 'Olympus%' or photo_camera_used like '%0D' or photo_camera_used like '%5D' or photo_camera_used like '%EOS RP');");
				if (preg_match("/\w+/",$result)) {
					if ($c == 0) $query_string = "$photoNum";
					else $query_string = "$query_string,$photoNum";
					$c++;
				}
			}
		}

		// Get photos
		$tempters = $wpdb->get_results("select * from pg_album_entries where photo_num in ($query_string)");

		// Output photos to page
		foreach ($tempters as $tempter) {
			$line='<div class="col-xl-4 centre"><img src="/photo_gallery_images/'
				  . $wpdb->get_var("select album_slug from pg_album_intros where album_code in ('$tempter->album_code')")
				  . '/small_size/'
				  . $tempter->photo_file_name
				  . '" alt="'
				  . $tempter->photo_image_label
				  . '" title="'
				  . $tempter->photo_image_label
				  . '" class="img-thumbnail mt-3 mb-3 taster" /></div>';
			echo $line;
		}

		?>
	</div>
</div>
