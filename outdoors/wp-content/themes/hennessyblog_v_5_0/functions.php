<?php

/* Add widgets area to sidebar */


if (function_exists('register_sidebar')) {
        register_sidebar(array('name'=>'Site Welcome',));
}

if (function_exists('register_sidebar')) {
	register_sidebar(array('name'=>'Right Sidebar',));
}

/* Add stylesheet to post and page editor */

add_editor_style();

/* Disable the emojis */

function disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	remove_filter( 'embed_head', 'print_emoji_detection_script' );

	// Remove from TinyMCE
	add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );
	add_filter( 'option_use_smilies', '__return_false' );
}
add_action( 'init', 'disable_emojis' );

/* Filter out the tinymce emoji plugin. */

function disable_emojis_tinymce( $plugins ) {
	if ( is_array( $plugins ) ) {
		return array_diff( $plugins, array( 'wpemoji' ) );
	} else {
		return array();
	}
}

// Disable core update emails
add_filter( 'auto_core_update_send_email', '__return_false' );

// Disable plugin update emails
add_filter( 'auto_plugin_update_send_email', '__return_false' );

// Disable theme update emails
add_filter( 'auto_theme_update_send_email', '__return_false' );

// Exclude pages from search
function exclude_pages_from_search($query) {
    if ($query->is_search && !is_admin()) {
        $query->set('post_type', 'post');
    }
    return $query;
}
add_filter('pre_get_posts', 'exclude_pages_from_search');

// Ensure that commenting times use the 24-hour clock
function custom_comment_time_24_hour($time_string) {

    // Convert the time string to a DateTime object
    $date_time = new DateTime($time_string);

    // Format it in 24-hour format
    return $date_time->format('H:i'); // Change 'H:i' to 'Y-m-d H:i' for full date and time
}

add_filter('get_comment_time', 'custom_comment_time_24_hour');

function add_canonical_link() {
    global $post;

    // Check if we're on a single post/page
    if (is_singular()) {
        $canonical_url = get_permalink($post->ID);
    } 
    // For the homepage
    elseif (is_home() || is_front_page()) {
        $canonical_url = home_url('/');
    }
    // For category archives
    elseif (is_category()) {
        $canonical_url = get_category_link(get_query_var('cat'));
    }
    // For tag archives
    elseif (is_tag()) {
        $canonical_url = get_tag_link(get_query_var('tag_id'));
    }
    // For other archive pages
    elseif (is_archive()) {
        $canonical_url = get_permalink();
    }
    // Fallback for other pages
    else {
        $canonical_url = get_permalink();
    }

    // Output the canonical link
    echo '<link rel="canonical" href="' . esc_url($canonical_url) . '" />' . "\n";
}

// Hook the function to wp_head
add_action('wp_head', 'add_canonical_link');

// Remove default canonical link
remove_action('wp_head', 'rel_canonical');

?>
