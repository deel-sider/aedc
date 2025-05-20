<?php

// Extracting title information for blog posts and printing list of links to screen

$sections = $wpdb->get_results("SELECT distinct c.adi_menu_sort, c.Section, c.section_title FROM (select a.*, b.Title as section_title, b.adi_menu_sort from kpl05tp_textpattern a left join kpl05tp_txp_section b on a.Section=b.Name) c where Section != 'hidden' and Section != 'default' and Status=4 order by adi_menu_sort, Section");

foreach ($sections as $section) {
	
	echo "<li class='left sm-bottom-offset bold'><a href='/travel/$section->Section' >$section->section_title</a></li>";
	
	$select="SELECT c.Section, c.Title, c.url_title, c.custom_1, c.section_title, c.adi_menu_sort FROM (select a.*, b.Title as section_title, b.adi_menu_sort from kpl05tp_textpattern a left join kpl05tp_txp_section b on a.Section=b.Name) c where Section = '" . $section->Section . "' and Status=4 order by adi_menu_sort, Section, custom_1, Title";

	$articles = $wpdb->get_results($select);
		   
	foreach ($articles as $article) {
				$a_title=preg_replace("/\&/","&amp;",$article->Title);
				$line="<li class='left sm-bottom-offset tjentry'><a href='/travel/$article->Section/$article->url_title' >$article->Title</a></li>";
				echo "\t\t\t$line\n";
	}

}

?>
