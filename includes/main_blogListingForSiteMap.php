<?php

// Getting web address root

$siteurl = $wpdb->get_var("SELECT option_value FROM " . $table_prefix . "options where option_name='siteurl'");

// Extracting title information for blog posts and printing list of links to screen

echo "<p class='bold left'>Posts</p>";

$posts = $wpdb->get_results("SELECT ID, post_title, post_name, date_format(post_date,'%Y/%m/%d') as post_date FROM "
       . $table_prefix
       . "posts WHERE post_mime_type='' and post_status='publish' and post_type='post' and post_title != '' and post_title regexp '[^\@]' and post_name not regexp 'contact-form' ORDER BY post_date DESC, ID DESC");

foreach ($posts as $post) {
			$entry = get_post($posts->ID);
			$line="<p class='left'><a href='/outdoors/$post->post_name/'>$post->post_title</a></p>";
			echo "\t\t\t$line\n";
}

// Extracting title information for blog pages and printing list of links to screen

echo "<p class='bold left'>Pages</p>";

$pages = $wpdb->get_results("SELECT ID, post_title, post_name FROM "
       . $table_prefix
       . "posts WHERE post_mime_type='' and post_status='publish' and post_type='page' and post_title != '' and post_title regexp '[^\@]' and post_name not regexp 'contact-form' ORDER BY post_date DESC, ID DESC");

function get_full_page_path($page_id) {
    $page = get_post($page_id);
    $path = $page->post_name;

    while ($page->post_parent) {
        $page = get_post($page->post_parent);
        $path = $page->post_name . '/' . $path;
    }

    return home_url($path);
}


foreach ($pages as $page) {
        $full_url = get_full_page_path($page->ID);
        $line="<p class='left'><a href='$full_url'>$page->post_title</a></p>";
        echo "\t\t\t$line\n";
}

?>
