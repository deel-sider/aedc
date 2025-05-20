<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package import-markdown
 */

/**
 * This class should be used to work with the administrative side of WordPress.
 */
class Daimma_Admin {

	/**
	 * The instance of this class.
	 *
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * The instance of the shared class.
	 *
	 * @var Daimma_Shared|null
	 */
	private $shared = null;

	/**
	 * The screen id of the "Import" menu.
	 *
	 * @var null
	 */
	private $screen_id_import = null;

	/**
	 * The screen id of the "Log" menu.
	 *
	 * @var null
	 */
	private $screen_id_log = null;

	/**
	 * The screen id of the "Maintenance" menu.
	 *
	 * @var null
	 */
	private $screen_id_maintenance = null;

	/**
	 * The screen id of the "Options" menu.
	 *
	 * @var null
	 */
	private $screen_id_options = null;

	/**
	 * Instance of the class used to generate the back-end menus.
	 *
	 * @var null
	 */
	private $menu_elements = null;

	/**
	 * Constructor.
	 */
	private function __construct() {

		// Assign an instance of the plugin info.
		$this->shared = Daimma_Shared::get_instance();

		// Load admin stylesheets and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		// Add the admin menu.
		add_action( 'admin_menu', array( $this, 'me_add_admin_menu' ) );

		// This hook is triggered during the creation of a new blog.
		add_action( 'wpmu_new_blog', array( $this, 'new_blog_create_options_and_tables' ), 10, 6 );

		// This hook is triggered during the deletion of a blog.
		add_action( 'delete_blog', array( $this, 'delete_blog_delete_options_and_tables' ), 10, 1 );

		// Require and instantiate the classes used to handle the menus.
		add_action( 'init', array( $this, 'handle_menus' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @return self|null
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * If we are in one of the plugin back-end menus require and instantiate the class used to handle the specific menu.
	 *
	 * @return void
	 */
	public function handle_menus() {

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Nonce non-necessary for menu selection.
		$page_query_param = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : null;

		// Require and instantiate the class used to register the menu options.
		if ( null !== $page_query_param ) {

			$config = array(
				'admin_toolbar' => array(
					'items'      => array(
						array(
							'link_text' => __( 'Import', 'import-markdown' ),
							'link_url'  => admin_url( 'admin.php?page=daimma-import' ),
							'icon'      => 'upload-03',
							'menu_slug' => 'daimma-import',
						),
						array(
							'link_text' => __( 'Log', 'import-markdown' ),
							'link_url'  => admin_url( 'admin.php?page=daimma-log' ),
							'icon'      => 'file-06',
							'menu_slug' => 'daimma-log',
						),
						array(
							'link_text' => __( 'Maintenance', 'import-markdown' ),
							'link_url'  => admin_url( 'admin.php?page=daimma-maintenance' ),
							'icon'      => 'tool-02',
							'menu_slug' => 'daimma-maintenance',
						),
					),
					'more_items' => array(
						array(
							'link_text' => __( 'Options', 'ultimate-markdown' ),
							'link_url'  => admin_url( 'admin.php?page=daimma-options' ),
							'pro_badge' => false,
						),
						array(
							'link_text' => __( 'Markdown Editor', 'ultimate-markdown' ),
							'link_url'  => 'https://daext.com/ultimate-markdown/#features',
							'pro_badge' => true,
						),
						array(
							'link_text' => __( 'Post Editor Integration', 'ultimate-markdown' ),
							'link_url'  => 'https://daext.com/ultimate-markdown/#features',
							'pro_badge' => true,
						),
						array(
							'link_text' => __( 'Bulk Import', 'ultimate-markdown' ),
							'link_url'  => 'https://daext.com/ultimate-markdown/#features',
							'pro_badge' => true,
						),
						array(
							'link_text' => __( 'Bulk Export', 'ultimate-markdown' ),
							'link_url'  => 'https://daext.com/ultimate-markdown/#features',
							'pro_badge' => true,
						),
						array(
							'link_text' => __( 'Front Matter Support', 'ultimate-markdown' ),
							'link_url'  => 'https://daext.com/ultimate-markdown/#features',
							'pro_badge' => true,
						),
						array(
							'link_text' => __( 'Automatic Image Upload', 'ultimate-markdown' ),
							'link_url'  => 'https://daext.com/ultimate-markdown/#features',
							'pro_badge' => true,
						),
						array(
							'link_text' => __( 'REST API Endpoints', 'ultimate-markdown' ),
							'link_url'  => 'https://daext.com/ultimate-markdown/#features',
							'pro_badge' => true,
						),
					),
				),
			);

			// The parent class.
			require_once $this->shared->get( 'dir' ) . 'admin/inc/menu/class-daimma-menu-elements.php';

			// Use the correct child class based on the page query parameter.
			if ( 'daimma-import' === $page_query_param ) {
				require_once $this->shared->get( 'dir' ) . 'admin/inc/menu/child/class-daimma-import-menu-elements.php';
				$this->menu_elements = new Daimma_Import_Menu_Elements( $this->shared, $page_query_param, $config );
			}
			if ( 'daimma-log' === $page_query_param ) {
				require_once $this->shared->get( 'dir' ) . 'admin/inc/menu/child/class-daimma-log-menu-elements.php';
				$this->menu_elements = new Daimma_Log_Menu_Elements( $this->shared, $page_query_param, $config );
			}
			if ( 'daimma-maintenance' === $page_query_param ) {
				require_once $this->shared->get( 'dir' ) . 'admin/inc/menu/child/class-daimma-maintenance-menu-elements.php';
				$this->menu_elements = new Daimma_Maintenance_Menu_Elements( $this->shared, $page_query_param, $config );
			}
			if ( 'daimma-options' === $page_query_param ) {
				require_once $this->shared->get( 'dir' ) . 'admin/inc/menu/child/class-daimma-options-menu-elements.php';
				$this->menu_elements = new Daimma_Options_Menu_Elements( $this->shared, $page_query_param, $config );
			}
		}

	}

	/**
	 * Enqueue admin-specific styles.
	 *
	 * @return void
	 */
	public function enqueue_admin_styles() {

		$screen = get_current_screen();

		// Menu Import.
		if ( $screen->id === $this->screen_id_import ) {

			wp_enqueue_style( $this->shared->get( 'slug' ) . '-framework-menu', $this->shared->get( 'url' ) . 'admin/assets/css/framework-menu/main.css', array(), $this->shared->get( 'ver' ) );

		}

		// Menu Log.
		if ( $screen->id === $this->screen_id_log ) {

			wp_enqueue_style( $this->shared->get( 'slug' ) . '-framework-menu', $this->shared->get( 'url' ) . 'admin/assets/css/framework-menu/main.css', array(), $this->shared->get( 'ver' ) );

		}

		// Menu Maintenance.
		if ( $screen->id === $this->screen_id_maintenance ) {

			wp_enqueue_style( $this->shared->get( 'slug' ) . '-framework-menu', $this->shared->get( 'url' ) . 'admin/assets/css/framework-menu/main.css', array(), $this->shared->get( 'ver' ) );

			// Select2.
			wp_enqueue_style(
				$this->shared->get( 'slug' ) . '-select2',
				$this->shared->get( 'url' ) . 'admin/assets/inc/select2/css/select2.min.css',
				array(),
				$this->shared->get( 'ver' )
			);

			// jQuery UI Dialog.
			wp_enqueue_style(
				$this->shared->get( 'slug' ) . '-jquery-ui-dialog',
				$this->shared->get( 'url' ) . 'admin/assets/css/jquery-ui-dialog.css',
				array(),
				$this->shared->get( 'ver' )
			);

		}

		// Menu options.
		if ( $screen->id === $this->screen_id_options ) {

			wp_enqueue_style( $this->shared->get( 'slug' ) . '-framework-menu', $this->shared->get( 'url' ) . 'admin/assets/css/framework-menu/main.css', array( 'wp-components' ), $this->shared->get( 'ver' ) );

		}
	}

	/**
	 * Enqueue admin-specific JavaScript.
	 *
	 * @return void
	 */
	public function enqueue_admin_scripts() {

		$wp_localize_script_data = array(
			'confirmText'        => esc_html__( 'Confirm', 'import-markdown' ),
			'cancelText'         => esc_html__( 'Cancel', 'import-markdown' ),
			'chooseAnOptionText' => wp_strip_all_tags( __( 'Choose an Option ...', 'import-markdown' ) ),
		);

		$screen = get_current_screen();

		// Menu Import.
		if ( $screen->id === $this->screen_id_import ) {

			wp_enqueue_script( $this->shared->get( 'slug' ) . '-menu', $this->shared->get( 'url' ) . 'admin/assets/js/framework-menu/menu.js', array( 'jquery' ), $this->shared->get( 'ver' ), true );

		}

		// Menu Log.
		if ( $screen->id === $this->screen_id_log ) {

			// Store the JavaScript parameters in the window.DAIMMA_PARAMETERS object.
			$initialization_script  = 'window.DAIMMA_PARAMETERS = {';
			$initialization_script .= 'ajax_url: "' . admin_url( 'admin-ajax.php' ) . '",';
			$initialization_script .= 'admin_url: "' . get_admin_url() . '",';
			$initialization_script .= 'site_url: "' . get_site_url() . '",';
			$initialization_script .= 'plugin_url: "' . $this->shared->get( 'url' ) . '",';
			$initialization_script .= '};';

			wp_enqueue_script(
				$this->shared->get( 'slug' ) . '-log-menu',
				$this->shared->get( 'url' ) . 'admin/react/log-menu/build/index.js',
				array( 'wp-element', 'wp-api-fetch', 'wp-i18n' ),
				$this->shared->get( 'ver' ),
				true
			);

			wp_add_inline_script( $this->shared->get( 'slug' ) . '-log-menu', $initialization_script, 'before' );

			wp_enqueue_script( $this->shared->get( 'slug' ) . '-menu', $this->shared->get( 'url' ) . 'admin/assets/js/framework-menu/menu.js', array( 'jquery' ), $this->shared->get( 'ver' ), true );

		}

		// Menu Maintenance.
		if ( $screen->id === $this->screen_id_maintenance ) {

			// Select2.
			wp_enqueue_script(
				$this->shared->get( 'slug' ) . '-select2',
				$this->shared->get( 'url' ) . 'admin/assets/inc/select2/js/select2.min.js',
				array( 'jquery' ),
				$this->shared->get( 'ver' ),
				true
			);

			wp_enqueue_script( $this->shared->get( 'slug' ) . '-menu', $this->shared->get( 'url' ) . 'admin/assets/js/framework-menu/menu.js', array( 'jquery' ), $this->shared->get( 'ver' ), true );

			// Maintenance Menu.
			wp_enqueue_script(
				$this->shared->get( 'slug' ) . '-menu-maintenance',
				$this->shared->get( 'url' ) . 'admin/assets/js/menu-maintenance.js',
				array( 'jquery', 'jquery-ui-dialog', $this->shared->get( 'slug' ) . '-select2' ),
				$this->shared->get( 'ver' ),
				true
			);
			wp_localize_script(
				$this->shared->get( 'slug' ) . '-menu-maintenance',
				'objectL10n',
				$wp_localize_script_data
			);

		}

		// Menu options.
		if ( $screen->id === $this->screen_id_options ) {

			// Store the JavaScript parameters in the window.DAIMMA_PARAMETERS object.
			$initialization_script  = 'window.DAIMMA_PARAMETERS = {';
			$initialization_script .= 'options_configuration_pages: ' . wp_json_encode( $this->shared->menu_options_configuration() );
			$initialization_script .= '};';

			wp_enqueue_script(
				$this->shared->get( 'slug' ) . '-menu-options',
				$this->shared->get( 'url' ) . 'admin/react/options-menu/build/index.js',
				array( 'wp-element', 'wp-api-fetch', 'wp-i18n', 'wp-components' ),
				$this->shared->get( 'ver' ),
				true
			);

			wp_add_inline_script( $this->shared->get( 'slug' ) . '-menu-options', $initialization_script, 'before' );

			wp_enqueue_script( $this->shared->get( 'slug' ) . '-menu', $this->shared->get( 'url' ) . 'admin/assets/js/framework-menu/menu.js', array( 'jquery' ), $this->shared->get( 'ver' ), true );

		}
	}

	/**
	 * Plugin activation.
	 *
	 * @param bool $networkwide True if the plugin is being activated network-wide.
	 *
	 * @return void
	 */
	public static function ac_activate( $networkwide ) {

		// Delete options and tables for all the sites in the network.
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			// If this is a "Network Activation" create the options and tables for each blog.

			if ( $networkwide ) {

				// Get the current blog id.
				global $wpdb;
				$current_blog = $wpdb->blogid;

				// Create an array with all the blog ids.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

				// Iterate through all the blogs.
				foreach ( $blogids as $blog_id ) {

					// Switch to the iterated blog.
					switch_to_blog( $blog_id );

					// Create options and tables for the iterated blog.
					self::ac_initialize_options();
					self::ac_create_database_tables();

				}

				// Switch to the current blog.
				switch_to_blog( $current_blog );

			} else {

				// If this is not a "Network Activation" create options and tables only for the current blog.
				self::ac_initialize_options();
				self::ac_create_database_tables();

			}
		} else {

			// If this is not a multisite installation create options and tables only for the current blog.
			self::ac_initialize_options();
			self::ac_create_database_tables();

		}
	}

	/**
	 * Create the options and tables for the newly created blog.
	 *
	 * @param int $blog_id The id of the blog.
	 *
	 * @return void
	 */
	public function new_blog_create_options_and_tables( $blog_id ) {

		global $wpdb;

		// If the plugin is "Network Active" create the options and tables for this new blog.
		if ( is_plugin_active_for_network( 'import-markdown/init.php' ) ) {

			// Get the id of the current blog.
			$current_blog = $wpdb->blogid;

			// Switch to the blog that is being activated.
			switch_to_blog( $blog_id );

			// Create options and database tables for the new blog.
			$this->ac_initialize_options();
			self::ac_create_database_tables();

			// Switch to the current blog.
			switch_to_blog( $current_blog );

		}
	}

	/**
	 * Delete options and tables for the deleted blog.
	 *
	 * @param int $blog_id The id of the blog.
	 *
	 * @return void
	 */
	public function delete_blog_delete_options_and_tables( $blog_id ) {

		global $wpdb;

		// Get the id of the current blog.
		$current_blog = $wpdb->blogid;

		// Switch to the blog that is being activated.
		switch_to_blog( $blog_id );

		// Create options and database tables for the new blog.
		$this->un_delete_options();

		// Switch to the current blog.
		switch_to_blog( $current_blog );
	}

	/**
	 * Initialize plugin options.
	 *
	 * @return void
	 */
	public static function ac_initialize_options() {

		if ( intval( get_option( 'daimma_options_version' ), 10 ) < 1 ) {

			// Assign an instance of Daimma_Shared.
			$shared = Daimma_Shared::get_instance();

			foreach ( $shared->get( 'options' ) as $key => $value ) {
				add_option( $key, $value );
			}

			// Update options version.
			update_option( 'daimma_options_version', '1' );

		}
	}

	/**
	 * Create the plugin database tables.
	 *
	 * @return void
	 */
	public static function ac_create_database_tables() {

		// assign an instance of Daimma_Shared.
		$shared = Daimma_Shared::get_instance();

		global $wpdb;

		// Get the database character collate that will be appended at the end of each query.
		$charset_collate = $wpdb->get_charset_collate();

		// check database version and create the database.
		if ( intval( get_option( $shared->get( 'slug' ) . '_database_version' ), 10 ) < 1 ) {

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			// Create *prefix*_log.
			$table_name = $wpdb->prefix . $shared->get( 'slug' ) . '_log';
			$sql        = "CREATE TABLE $table_name (
                log_id BIGINT UNSIGNED AUTO_INCREMENT,
                 date DATETIME,
                 file_name TEXT,
                 post_id BIGINT UNSIGNED,
                 post_title TEXT NOT NULL DEFAULT '',
                 post_permalink TEXT NOT NULL DEFAULT '',
                 post_edit_link TEXT NOT NULL DEFAULT '',
                PRIMARY KEY  (log_id)
            ) $charset_collate";
			dbDelta( $sql );

			// Update database version.
			update_option( $shared->get( 'slug' ) . '_database_version', '1' );

		}
	}

	/**
	 * Plugin delete.
	 *
	 * @return void
	 */
	public static function un_delete() {

		// Delete options and tables for all the sites in the network.
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			// Get the current blog id.
			global $wpdb;
			$current_blog = $wpdb->blogid;

			// Create an array with all the blog ids.

			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

			// Iterate through all the blogs.
			foreach ( $blogids as $blog_id ) {

				// Switch to the iterated blog.
				switch_to_blog( $blog_id );

				// Create options and tables for the iterated blog.
				self::un_delete_options();
				self::un_delete_database_tables();

			}

			// Switch to the current blog.
			switch_to_blog( $current_blog );

		} else {

			// If this is not a multisite installation delete options and tables only for the current blog.
			self::un_delete_options();
			self::un_delete_database_tables();

		}
	}

	/**
	 * Delete plugin options.
	 *
	 * @return void
	 */
	public static function un_delete_options() {

		// Assign an instance of Daimma_Shared.
		$shared = Daimma_Shared::get_instance();

		foreach ( $shared->get( 'options' ) as $key => $value ) {
			delete_option( $key );
		}
	}

	/**
	 * Delete plugin database tables.
	 *
	 * @return void
	 */
	public static function un_delete_database_tables() {

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->query( "DROP TABLE {$wpdb->prefix}daimma_log" );
	}

	/**
	 * Register the admin menu.
	 *
	 * @return void
	 */
	public function me_add_admin_menu() {

		$icon_svg = '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
		     viewBox="0 0 40 40" style="enable-background:new 0 0 40 40;" xml:space="preserve">
		<style type="text/css">
		    .st1{fill:#fff;}
		</style>
		    <g id="Layer_5_copy">
			<g>
				<path class="st1" d="M2.9,9.6c-0.5,0-1,0.4-1,1v18.8c0,0.5,0.4,1,1,1h34.2c0.5,0,1-0.4,1-1V10.6c0-0.5-0.4-1-1-1H2.9z M0,10.6
					C0,9,1.3,7.7,2.9,7.7h34.2c1.6,0,2.9,1.3,2.9,2.9v18.8c0,1.6-1.3,2.9-2.9,2.9H2.9C1.3,32.3,0,31,0,29.4V10.6z"/>
		        <path class="st1" d="M5.8,26.5V13.5h3.8l3.8,4.8l3.8-4.8h3.8v13.1h-3.8V19l-3.8,4.8L9.6,19v7.5H5.8z M29.8,26.5L24,20.2h3.8v-6.7h3.8v6.7h3.8
					L29.8,26.5z"/>
			</g>
		</g>
		</svg>';

		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode -- Base64 encoding is used to embed the SVG in the HTML.
		$icon_svg = 'data:image/svg+xml;base64,' . base64_encode( $icon_svg );

		add_menu_page(
			esc_attr__( 'IM' ),
			esc_attr__( 'Markdown' ),
			get_option( $this->shared->get( 'slug' ) . '_import_menu_required_capability' ),
			$this->shared->get( 'slug' ) . '-import',
			array( $this, 'me_display_menu_import' ),
			$icon_svg
		);

		$this->screen_id_import = add_submenu_page(
			$this->shared->get( 'slug' ) . '-import',
			esc_attr__( 'IM - Import' ),
			esc_attr__( 'Import' ),
			get_option( $this->shared->get( 'slug' ) . '_import_menu_required_capability' ),
			$this->shared->get( 'slug' ) . '-import',
			array( $this, 'me_display_menu_import' )
		);

		$this->screen_id_log = add_submenu_page(
			$this->shared->get( 'slug' ) . '-import',
			esc_attr__( 'IM - Log' ),
			esc_attr__( 'Log' ),
			get_option( $this->shared->get( 'slug' ) . '_log_menu_required_capability' ),
			$this->shared->get( 'slug' ) . '-log',
			array( $this, 'me_display_menu_log' )
		);

		$this->screen_id_maintenance = add_submenu_page(
			$this->shared->get( 'slug' ) . '-import',
			esc_attr__( 'IM - Maintenance' ),
			esc_attr__( 'Maintenance' ),
			get_option( $this->shared->get( 'slug' ) . '_maintenance_menu_required_capability' ),
			$this->shared->get( 'slug' ) . '-maintenance',
			array( $this, 'me_display_menu_maintenance' )
		);

		$this->screen_id_options = add_submenu_page(
			$this->shared->get( 'slug' ) . '-import',
			esc_attr__( 'IM - Options' ),
			esc_attr__( 'Options', 'import-markdown' ),
			'manage_options',
			$this->shared->get( 'slug' ) . '-options',
			array( $this, 'me_display_menu_options' )
		);
	}

	/**
	 * Includes the import log view.
	 *
	 * @return void
	 */
	public function me_display_menu_log() {
		include_once 'view/log.php';
	}

	/**
	 * Includes the maintenance view.
	 *
	 * @return void
	 */
	public function me_display_menu_maintenance() {
		include_once 'view/maintenance.php';
	}

	/**
	 * Includes the import view.
	 *
	 * @return void
	 */
	public function me_display_menu_import() {
		include_once 'view/import.php';
	}

	/**
	 * Includes the options view.
	 *
	 * @return void
	 */
	public function me_display_menu_options() {
		include_once 'view/options.php';
	}
}
