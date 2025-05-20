<div class="container">
<div class="row albums">

<?php

global $wpdb;

$countries = $wpdb->get_results("SELECT DISTINCT country_name FROM photo_gallery_intros order by photo_gallery_intro_ID");

foreach ($countries as $country) {

	$country_name=$country->country_name;
	$lc_country_name=preg_replace("/\s/","-",strtolower($country_name));

	if ($country_name=="Ireland") $country_name="&Eacute;ire";

	if ($country_name!="Walking Trails" && $country_name!="Urban England" && $country_name!="Wales" && $country_name!="European Alps" && $country_name!="Other Islands" && $country_name!="North America") {
        $div='<div class="col-md-3" id="' . $lc_country_name . '">';
    	echo $div;
	    echo '<h3 class="red bottom-offset">';
	    echo "$country_name";
	    echo "</h3>\n";
    }
    else {
	    echo '<h3 class="red top-offset bottom-offset">';
	    echo "$country_name";
	    echo "</h3>\n";
    }

	$areas = $wpdb->get_results("SELECT DISTINCT area_name, area_slug FROM photo_gallery_intros where country_name='$country->country_name' order by photo_gallery_intro_ID");

	foreach ($areas as $area) {
		$area_less_country=preg_replace("/\,\s+$country_name/","",$area->area_name);
		$area_less_country=preg_replace("/England \&amp\; Scotland/","England&nbsp;&amp;&nbsp;Scotland",$area_less_country);
		if (preg_match("/Scotland/",$area_less_country) == 0) $area_less_country=preg_replace("/\,\s+England/","",$area_less_country);
		$line="<p><a href='/photoGallery_galleries/$area->area_slug'>$area_less_country</a></p>\n";
		echo "$line";
	}

	if ($country_name!="Scotland" && $country_name!="Rural England" && $country_name!="&Eacute;ire" && $country_name!="Scandinavia" && $country_name!="European Alps" && $country_name!="Other Islands") echo "</div>\n";

}

?>
      </div>
      <hr>
      <footer>
        <p class="centre">Copyright &copy; 1998-<?php date_default_timezone_set('Europe/London'); echo date("Y"); ?>, AssortedExplorations.com</p>
      </footer>
    </div>
