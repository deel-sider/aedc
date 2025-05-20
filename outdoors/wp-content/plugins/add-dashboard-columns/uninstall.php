<?php
/**
 * Add Dashboard Columns Uninstall file.
 *
 * @package AddDashboardColumns
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_metadata( 'user', 0, 'screen_layout_dashboard', '', true );
delete_metadata( 'user', 0, 'screen_layout_dashboard-network', '', true );
