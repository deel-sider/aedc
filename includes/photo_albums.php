<div class="container mt-4 mb-4 pb-3">
	<div class="row albums">
		<?php

		global $wpdb;

		$album_section = $wpdb->get_results("SELECT DISTINCT album_section FROM pg_album_intros order by album_code");

		foreach ($album_section as $album_section) {

			$album_section = $album_section->album_section;
			$raw_album_section = $album_section;
			if ($album_section == "Ireland") $album_section = "&Eacute;ire";

			$tops=array("&Eacute;ire", "Rural England", "Scotland", "Scandinavia");
			if (in_array($album_section, $tops)) echo '<div class="col-md-3">';

			echo '<h3 class="mt-4 mb-4">' . "$album_section" . "</h3>\n";

			$areas = $wpdb->get_results("SELECT DISTINCT country_name, album_name, album_path FROM pg_album_intros where album_section='$raw_album_section' order by album_code");

			foreach ($areas as $area) {
				$album_name = $area->album_name;
				$add_countries = array("Mallorca", "Tenerife", "British Columbia", "California");
				if ((in_array($album_name, $add_countries))) $album_name = $album_name . ", " . $area->country_name;
				$line="<p><a href='/photos$area->album_path'>$album_name</a></p>\n";
				echo "$line";
			}

			$bottoms = array("Urban England", "Wales", "Walking Trails", "North America");
			if (in_array($album_section, $bottoms)) echo "</div>\n";

		}

		?>
	</div>
</div>
