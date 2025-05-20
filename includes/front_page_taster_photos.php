<ul class="photopanel ppw">
<?php

// Database prefix

define("PG_DB_PREFIX","photo_gallery_");

// Getting number of real photos

$rowno = $wpdb->get_var("SELECT count(*) FROM " . PG_DB_PREFIX . "members");

// Generating random number and normalising value

$query_string="";

$c=0;

while ($c<6) {
	$photoNum=mt_rand(0,$rowno);
	if ($photoNum >= 0 && !preg_match("/$photoNum/",$query_string)) {
		$result=$wpdb->get_var("select photo_name from " . PG_DB_PREFIX . "members where photo_member_ID=$photoNum and cast(thumb_width as UNSIGNED) > cast(thumb_height as UNSIGNED);");
		if (preg_match("/\w+/",$result)) {
			if ($c==0) $query_string=$photoNum;
			else $query_string="$query_string,$photoNum";
			$c++;
		}
	}
}

$tempters=$wpdb->get_results("select * from " . PG_DB_PREFIX . "members where photo_member_ID in ($query_string)");

foreach ($tempters as $tempter) {
	echo "<li class='inline iwb'><img src='/photo_gallery_images/$tempter->area_slug/small_size/$tempter->photo_name' alt='$tempter->photo_label' title='$tempter->photo_label' class='img-thumbnail itwb' /></li>";
}

?>
</ul>
