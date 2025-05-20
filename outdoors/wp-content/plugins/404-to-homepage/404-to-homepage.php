<?php if (!defined('ABSPATH')) die;
/*
Plugin Name: Redirect 404 to Homepage
Plugin URI: https://wordpress.org/plugins/404-to-homepage/
Description: Redirect 404 missing pages to the homepage.
Author: pipdig
Author URI: https://www.pipdig.co/
Version: 1.0
License: GPLv2 or later
*/

add_action('wp', function() {
	
	if (!is_404() || is_admin()) return;
	
	if ( (defined('DOING_CRON') && DOING_CRON) || (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) ) return;
	
	if ( (function_exists('is_bbpress') && is_bbpress()) || (function_exists('bp_is_user_profile') && bp_is_user_profile()) ) return;
	
	wp_redirect(home_url(), 301); exit;
	
});