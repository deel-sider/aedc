<?php
/**
 * Plugin Name: Add Dashboard Columns
 * Plugin URI: https://wordpress.org/plugins/add-dashboard-columns/
 * Description: Enable Dashboard Columns in WordPress after version 3.8
 * Version: 2.0.0
 * Author: Sergio ( kallookoo )
 * Author URI: https://dsergio.com/
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package AddDashboardColumns
 */

defined( 'ABSPATH' ) || exit;

/**
 * This plugin only run inside the administration.
 *
 * Anonymous functions are used because it does not need any external interaction,
 * but a filter is provided in case you want to deactivate at some time,
 * for example for a specific user.
 */
if ( is_admin() ) {
	add_action(
		'load-index.php',
		function () {
			/**
			 * Filters to switch the dashboard layout columns.
			 *
			 * @param bool False for disabled, Default: True.
			 */
			if ( apply_filters( 'add_dashboard_columns_enabled', true ) ) {
				$asset_path = dirname( __FILE__ ) . '/assets/add-dashboard-columns';
				/** Check the assets to prevent UX issues. */
				if ( is_readable( "{$asset_path}.css" ) && is_readable( "{$asset_path}.js" ) ) {
					add_screen_option(
						'layout_columns',
						array(
							'max'     => 4,
							'default' => 2,
						)
					);
					add_action(
						'admin_enqueue_scripts',
						function () {
							$asset_url = plugins_url( 'assets/add-dashboard-columns', __FILE__ );
							wp_enqueue_style( 'add-dashboard-columns', "{$asset_url}.css", array(), '2.0.0' );
							wp_enqueue_script( 'add-dashboard-columns', "{$asset_url}.js", array( 'jquery' ), '2.0.0', true );
						},
						999 /** Use high priority for the css properties. */
					);
				}

			}
		}
	);
}
