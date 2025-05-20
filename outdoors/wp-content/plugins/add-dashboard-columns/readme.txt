=== Add Dashboard Columns ===
Contributors: kallookoo
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=L4BFVU5HDJH8S
Tags: dashboard, widget, dashboard columns, columns, admin
Requires at least: 3.8
Requires PHP: 5.3
Tested up to: 5.2
Stable tag: 2.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Enable Dashboard Columns in WordPress 3.8 or later

== Description ==

Enable Dashboard Columns in WordPress 3.8 or later

== Installation ==

1. In your WordPress admin panel, go to *Plugins > New Plugin*, search for **add-dashboard-columns** and click "*Install now*"
1. Alternatively, download the plugin and upload the contents of `add-dashboard-columns.zip` to your plugins directory, which usually is `/wp-content/plugins/`.
1. Activate the plugin through the 'Plugins' menu in WordPress.
1. Go to the Dashboard and change the layout columns inside the Screen options.

== Frequently Asked Questions ==

= What meta delete? =
This plugin delete screen_layout_dashboard and screen_layout_dashboard-network.

= Why not delete meta-box-order_dashboard or meta-box-order_dashboard-network? =
Basically because I do not consider it necessary, and may be an established order has not been changed and the number of columns.

= Is possible disabled for a specific user or network site? =
Yes, use the `add_dashboard_columns_enabled` filter to switch.
Note: It does not remove any configuration simply prevents to fire the hook.

= I activated it but it does not show the layout, what happens? =
The most certain thing is that the assets are missing, since they are checked to avoid UX issues.

= Needs support? =
Please, use the WordPress plugin forums [support](https://wordpress.org/support/plugin/add-dashboard-columns/).

= You found an error? =
Please, create an [issue](https://github.com/kallookoo/add-dashboard-columns/issues/new).

== Changelog ==

= 2.0.0 =
* The plugin is modified to improve performance.
* Add filter `add_dashboard_columns_enabled` to switch.
* Now the meta is deleted when the plugin is deleted.

= 1.5 =
* Check support
* Register action and filters if is admin only
* Move class to child folder
* Change 3 to 2 on default layout_columns option
* Use delete_metadata to delete screen_layout_dashboard and screen_layout_dashboard-network
* Check and pass WordPress Coding Standards for PHP

= 1.4.1 =
* Change to static class and check support

= 1.4 =
* Change code for working with WordPress 4.8

= 1.3.5 =
* Rewrite plugin
* Delete meta on plugin deactivation ( Tested with 10000 users with meta, single installation and multisite installation )

= 1.3.4.2 =
* Remove register_deactivation_hook, because on many users it may not work properly

= 1.3.4.1 =
* Fix WP CLI

= 1.3.4 =
* Rewrite plugin
* Delete in WordPress 4.6+ screen_layout_dashboard, meta-box-order_dashboard,
  also delete if multisite screen_layout_dashboard-network, meta-box-order_dashboard-network on deactivation

= 1.3.3 =
* Check support
* Use bootstrap media for clean CSS
* Remove admin_enqueue_scripts, now uses admin_head-index.php to print Custom CSS

= 1.3.2 =
* Check support
* Add donation link in plugin page

= 1.3.1 =
* Check support

= 1.3 =
* Check support
* clean directories/files

= 1.2 =
* Check support

= 1.1 =
* Check support
* Fix bad url on css file

= 1.0 =
* Initial version