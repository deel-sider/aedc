<?php

// Database prefix

define("PG_DB_PREFIX","photo_gallery_");

function photo_search_box() {
$sbox = <<< end_of_search_box
<form class="search dashedhrs" action="/photoGallery_searchResults" method="post">
<input class="rightInput" type="text" name="searchString" size="22" maxlength="255" value="" alt="Enter text and click on Search to find you want" />
<input id="searchSubmit" type="submit" name="btnG" value="Search the Gallery" onmouseover="javascript:linkLike('searchSubmit');" onmouseout="javascript:linkLike('searchSubmit');" />
</form>
end_of_search_box;
echo $sbox;
}

function pg_stats($photo_query,$visit_type) {
	global $wpdb;
	if (!preg_match("/^\d+$/",$photo_query)) {
		$stat_query="select count(*) from " . PG_DB_PREFIX . "stats where photo_slug='$photo_query' and photo_visit_type='$visit_type';";
		$already_present=$wpdb->get_var($stat_query);
		$db_table=PG_DB_PREFIX . "stats";
		if (!$already_present) {
			$wpdb->insert($db_table,array('photo_slug' => $photo_query, 'photo_visit_number' => 1, 'photo_visit_type' => $visit_type));
		}
		else {
			$next_ct=$wpdb->get_var("select photo_visit_number+1 as ct from $db_table where photo_slug='$photo_query' and photo_visit_type='$visit_type';");
			$wpdb->update($db_table,array('photo_visit_number' => $next_ct),array('photo_slug' => $photo_query, 'photo_visit_type' => $visit_type));
		}
	}
}

?>
