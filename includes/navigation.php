    <!-- Fixed navbar -->
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
		  <a class="navbar-brand" href="/home/">Assorted Explorations</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="/photoGallery_intro">Photo Gallery <b class="caret"></b></a>
              <ul class="dropdown-menu  multi-level">
                <li><a href="/photo_gallery_index">See Photos</a></li>
<?php

global $wpdb;

$countries = $wpdb->get_results("SELECT DISTINCT country_name FROM photo_gallery_intros order by photo_gallery_intro_ID");

foreach ($countries as $country) {

	$country_name=$country->country_name;

	if ($country_name=="Ireland") $country_name="&Eacute;ire";

	echo '<li class="dropdown-submenu">';
	echo '<a tabindex="-1" href="#">';
	echo "$country_name";
	echo '</a>';
	echo '<ul class="dropdown-menu">';

	$areas = $wpdb->get_results("SELECT DISTINCT area_name, area_slug FROM photo_gallery_intros where country_name='$country->country_name' order by photo_gallery_intro_ID");

	foreach ($areas as $area) {
		$area_less_country=preg_replace("/\,\s+$country_name/","",$area->area_name);
		$area_less_country=preg_replace("/\,\s+England/","",$area_less_country);
		$area_less_country=preg_replace("/\,\s+Cumbria/","",$area_less_country);
		$area_less_country=preg_replace("/\,\s+England \&amp\; Scotland/","",$area_less_country);
		$line="<li><a href='/photoGallery_galleries/$area->area_slug' class='navMenu'>$area_less_country</a></li>\n";
		echo "$line";
	}
	
	echo "</ul></li>\n";

}

?>
              </ul>
              </li>
            <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="/outdoors/">Outdoor Excursions <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="/outdoors/">Read Blog</a></li>
<?php

global $wpdb;

$table_prefix="kpl05cw_";

$cats = $wpdb->get_results("SELECT distinct meta_value FROM " 
        . $table_prefix 
        . "postmeta where meta_key='Page Category' order by meta_value desc");

foreach ($cats as $cat) {
	$cat_title=$cat->meta_value;
	echo '<li class="dropdown-submenu">';
	echo '<a tabindex="-1" href="#">';
	echo "$cat_title";
	echo '</a>';
	echo '<ul class="dropdown-menu">';
	$_posts = $wpdb->get_results("SELECT ID, post_title, post_name, date_format(post_date,'%Y/%m/%d') as post_date FROM " 
             	 . $table_prefix 
	     	 . "posts WHERE post_mime_type='' and post_status='publish' and post_type='page' and ID in (select distinct post_id from "
             	 . $table_prefix 
	     	 . "postmeta where meta_value='$cat_title') ORDER BY post_title");
	foreach ($_posts as $post) {
		$post_title=$post->post_title;
		if ($post->post_name == "hill-country-bus-services-in-eire") $post_title="Hill Country Bus Services in &Eacute;ire";
		$line="<li><a href='/outdoors/$post->post_name/' class='navMenu'>$post_title</a></li>\n";
		echo "$line";
	}
	
	echo "</ul></li>\n";
}

?>
              </ul>
              </li>
            <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="/travel/">Travel Jottings <b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="/travel/">Explore Jottings</a></li>
<?php

global $wpdb;

$sections = $wpdb->get_results("SELECT DISTINCT Name, Title, adi_menu_sort FROM kpl05tp_txp_section where Name != 'hidden' and  Name != 'default' and adi_menu_exclude=0 order by adi_menu_sort, Name");

foreach ($sections as $section) {

	$section_name=$section->Title;

	$section_slug=$section->Name;

	echo '<li class="dropdown-submenu">';
	echo '<a tabindex="-1" href="#">';
	echo "$section_name";
	echo '</a>';
	echo '<ul class="dropdown-menu">';

	$articles = $wpdb->get_results("SELECT DISTINCT Title, url_title, custom_1 FROM kpl05tp_textpattern where Section = '$section_slug' and Status=4 order by custom_1, Title");

	foreach ($articles as $article) {
			$line="<li><a href='/travel/$section_slug/$article->url_title' class='navMenu'>$article->Title</a></li>\n";
			echo "$line";
	}
	
	echo "</ul></li>\n";

}

?>
              </ul>
            </li>
            <li class="dropdown"><a class="dropdown-toggle" data-toggle="dropdown" href="/home/">Sundries <b class="caret"></b></a>
				<ul class="dropdown-menu">
					<li><a href="/deliberations">Other Intrusions</a></li>
					<li><a href="/site_map">Site Map</a></li>
					<li><a href="/privacy_policy">Privacy Policy</a></li>
					<li><a href="/terms_of_service">Terms of Service</a></li>
					<li><a href="/outdoors/get-in-touch/">Get in Touch</a></li>
				</ul>
            <li>
				<div class="google_translate_element"><div id="google_translate_element"></div></div>
				<script type="text/javascript">
					function googleTranslateElementInit() {
					  new google.translate.TranslateElement({pageLanguage: 'en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE, autoDisplay: false}, 'google_translate_element');
					}
				</script>
				<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
            </li>
          </ul>
        </div>
      </div>
    </div>
