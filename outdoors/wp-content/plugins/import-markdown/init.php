<?php
/**
 * Plugin Name: Import Markdown
 * Description: Generates posts based on the imported markdown files.
 * Version: 1.14
 * Author: DAEXT
 * Author URI: https://daext.com
 * Text Domain: import-markdown
 * License: GPLv3
 *
 * @package import-markdown
 */

// Prevent direct access to this file.
if ( ! defined( 'WPINC' ) ) {
	die();
}

// Set constants.
define( 'DAIMMA_EDITION', 'FREE' );

// Class shared across public and admin.
require_once plugin_dir_path( __FILE__ ) . 'shared/class-daimma-shared.php';

// Rest API.
require_once plugin_dir_path( __FILE__ ) . 'inc/class-daimma-rest.php';
add_action( 'plugins_loaded', array( 'Daimma_Rest', 'get_instance' ) );

// Admin.
if ( is_admin() ) {

	// Load the autoloader for the libraries used to convert Markdown to HTML.
	require plugin_dir_path( __FILE__ ) . '/vendor/autoload.php';

	// Admin.
	require_once plugin_dir_path( __FILE__ ) . 'admin/class-daimma-admin.php';

	// If this is not an AJAX request, create a new singleton instance of the admin class.
	if ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) {

		add_action( 'plugins_loaded', array( 'Daimma_Admin', 'get_instance' ) );

	}

	// Activate the plugin using only the class static methods.
	register_activation_hook( __FILE__, array( 'Daimma_Admin', 'ac_activate' ) );

	// Update the plugin db tables and options if they are not up-to-date.
	Daimma_Admin::ac_create_database_tables();
	Daimma_Admin::ac_initialize_options();

}

/**
 * Load the plugin text domain for translation.
 *
 * @return void
 */
function daimma_load_plugin_textdomain() {
	load_plugin_textdomain( 'import-markdown', false, 'import-markdown/lang/' );
}

add_action( 'init', 'daimma_load_plugin_textdomain' );

/**
 * Customize the action links in the "Plugins" menu.
 *
 * @param array $actions An array of plugin action links.
 *
 * @return mixed
 */
function daimma_customize_action_links( $actions ) {
	$actions[] = '<a href="https://daext.com/ultimate-markdown/" target="_blank">' . esc_html__( 'Buy the Pro Version', 'import-markdown' ) . '</a>';
	return $actions;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'daimma_customize_action_links' );
