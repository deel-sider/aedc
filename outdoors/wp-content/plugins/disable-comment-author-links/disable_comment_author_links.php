<?php
defined( 'ABSPATH' ) or exit;

/**
 * Plugin Name: Disable Comment Author Links
 * Plugin URI: https://github.com/csalzano/disable-comment-author-links
 * Description: Removes home page links from comment author user names
 * Author: Corey Salzano
 * Author URI: https://breakfastco.xyz
 * Version: 1.0.0
 * License: GPLv2 or later
 */

if( !function_exists("disable_comment_author_links")){
	function disable_comment_author_links( $author_link ){
		return strip_tags( $author_link );
	}
	add_filter( 'get_comment_author_link', 'disable_comment_author_links' );
}
