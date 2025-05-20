<?php

DB::useDB('ae_surroundings');

// Extracting title information for blog posts and printing list of links to screen

$sections = DB::query("SELECT distinct c.adi_menu_sort, c.Section, c.section_title FROM (select a.*, b.Title as section_title, b.adi_menu_sort from kpl05tt_textpattern a left join kpl05tt_txp_section b on a.Section=b.Name) c where Section != 'hidden' and Section != 'default' and Status=4 order by adi_menu_sort, Section");

foreach ($sections as $section) {
	
	$osection=$section["Section"];
	$tsection=$section["section_title"];
	echo "<p class='bold left'><a href='/surroundings/$osection' >$tsection</a></p>";
	
	$select="SELECT c.Section, c.Title, c.url_title, c.custom_1, c.section_title, c.adi_menu_sort FROM (select a.*, b.Title as section_title, b.adi_menu_sort from kpl05tt_textpattern a left join kpl05tt_txp_section b on a.Section=b.Name) c where Section = '" . $osection . "' and Status=4 order by adi_menu_sort, Section, custom_1, Title";

	$articles = DB::query($select);
		   
	foreach ($articles as $article) {
				$a_title=preg_replace("/\&/","&amp;",$article["Title"]);
				$line="<p class='left'><a href='/surroundings/" . $article["Section"] . "/" . $article["url_title"] . "' >" . $article["Title"] . "</a></p>";
				echo "\t\t\t$line\n";
	}

}

?>
