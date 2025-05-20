<?php
/**
 * Here the REST API endpoint of the plugin are registered.
 *
 * @package import-markdown
 */

/**
 * This class should be used to work with the REST API endpoints of the plugin.
 */
class Daimma_Rest {

	/**
	 * The singleton instance of the class.
	 *
	 * @var null
	 */
	protected static $instance = null;

	/**
	 * An instance of the shared class.
	 *
	 * @var Daimma_Shared|null
	 */
	private $shared = null;

	/**
	 * Constructor.
	 */
	private function __construct() {

		// Assign an instance of the shared class.
		$this->shared = Daimma_Shared::get_instance();

		/**
		 * Add custom routes to the Rest API.
		 */
		add_action( 'rest_api_init', array( $this, 'rest_api_register_route' ) );
	}

	/**
	 * Create a singleton instance of the class.
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
	 * Add custom routes to the Rest API.
	 *
	 * @return void
	 */
	public function rest_api_register_route() {

		// Add the GET 'daext-import-markdown/v1/read-options' endpoint to the Rest API.
		register_rest_route(
			'daext-import-markdown/v1',
			'/read-options/',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_daext_import_markdown_read_options_callback' ),
				'permission_callback' => array( $this, 'rest_api_daext_import_markdown_read_options_callback_permission_check' ),
			)
		);

		// Add the POST 'daext-import-markdown/v1/options' endpoint to the Rest API.
		register_rest_route(
			'daext-import-markdown/v1',
			'/options',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_daext_import_markdown_update_options_callback' ),
				'permission_callback' => array( $this, 'rest_api_daext_import_markdown_update_options_callback_permission_check' ),

			)
		);

		// Add the POST 'daext-import-markdown/v1/log/' endpoint to the Rest API.
		register_rest_route(
			'daext-import-markdown/v1',
			'/log/',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'rest_api_daext_import_markdown_read_log_statistics_callback' ),
				'permission_callback' => array( $this, 'rest_api_daext_import_markdown_read_log_statistics_callback_permission_check' ),
			)
		);
	}

	/**
	 * Callback for the GET 'daext-import-markdown/v1/options' endpoint of the Rest API.
	 *
	 * @return WP_REST_Response
	 */
	public function rest_api_daext_import_markdown_read_options_callback() {

		// Generate the response.
		$response = array();
		foreach ( $this->shared->get( 'options' ) as $key => $value ) {
			$response[ $key ] = get_option( $key );
		}

		// Prepare the response.
		$response = new WP_REST_Response( $response );

		return $response;
	}

	/**
	 * Check the user capability.
	 *
	 * @return true|WP_Error
	 */
	public function rest_api_daext_import_markdown_read_options_callback_permission_check() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_read_error',
				'Sorry, you are not allowed to read the Import Markdown options.',
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Callback for the POST 'daext-import-markdown/v1/options' endpoint of the Rest API.
	 *
	 * This method is in the following contexts:
	 *
	 *  - To update the plugin options in the "Options" menu.
	 *
	 * @param object $request The request data.
	 *
	 * @return WP_REST_Response
	 */
	public function rest_api_daext_import_markdown_update_options_callback( $request ) {

		$options = array();

		// Get and sanitize data --------------------------------------------------------------------------------------.

		// Conversion - Tab -------------------------------------------------------------------------------------------.

		// Markdown Parser - Section ----------------------------------------------------------------------------------.
		$options['daimma_markdown_parser']                      = $request->get_param( 'daimma_markdown_parser' ) !== null ? sanitize_key( $request->get_param( 'daimma_markdown_parser' ) ) : null;

		// Cebe Markdown - Section ------------------------------------------------------------------------------------.
		$options['daimma_cebe_markdown_html5']                  = $request->get_param( 'daimma_cebe_markdown_html5' ) !== null ? intval( $request->get_param( 'daimma_cebe_markdown_html5' ), 10 ) : null;
		$options['daimma_cebe_markdown_keep_list_start_number'] = $request->get_param( 'daimma_cebe_markdown_keep_list_start_number' ) !== null ? intval( $request->get_param( 'daimma_cebe_markdown_keep_list_start_number' ), 10 ) : null;
		$options['daimma_cebe_markdown_enable_new_lines']       = $request->get_param( 'daimma_cebe_markdown_enable_new_lines' ) !== null ? intval( $request->get_param( 'daimma_cebe_markdown_enable_new_lines' ), 10 ) : null;

		// League CommonMark - Section --------------------------------------------------------------------------------.
		$options['daimma_commonmark_enable_em']              = $request->get_param( 'daimma_commonmark_enable_em' ) !== null ? intval( $request->get_param( 'daimma_commonmark_enable_em' ), 10 ) : null;
		$options['daimma_commonmark_enable_strong']          = $request->get_param( 'daimma_commonmark_enable_strong' ) !== null ? intval( $request->get_param( 'daimma_commonmark_enable_strong' ), 10 ) : null;
		$options['daimma_commonmark_use_asterisk']           = $request->get_param( 'daimma_commonmark_use_asterisk' ) !== null ? intval( $request->get_param( 'daimma_commonmark_use_asterisk' ), 10 ) : null;
		$options['daimma_commonmark_use_underscore']         = $request->get_param( 'daimma_commonmark_use_underscore' ) !== null ? intval( $request->get_param( 'daimma_commonmark_use_underscore' ), 10 ) : null;
		$options['daimma_commonmark_unordered_list_markers'] = $request->get_param( 'daimma_commonmark_unordered_list_markers' ) !== null && is_array( $request->get_param( 'daimma_commonmark_unordered_list_markers' ) ) ? array_map( 'sanitize_text_field', $request->get_param( 'daimma_commonmark_unordered_list_markers' ) ) : null;
		$options['daimma_commonmark_html_input']         = $request->get_param( 'daimma_commonmark_html_input' ) !== null ? sanitize_key( $request->get_param( 'daimma_commonmark_html_input' ) ) : null;
		$options['daimma_commonmark_allow_unsafe_links'] = $request->get_param( 'daimma_commonmark_allow_unsafe_links' ) !== null ? intval( $request->get_param( 'daimma_commonmark_allow_unsafe_links' ), 10 ) : null;
		$options['daimma_commonmark_max_nesting_level']  = $request->get_param( 'daimma_commonmark_max_nesting_level' ) !== null ? intval( $request->get_param( 'daimma_commonmark_max_nesting_level' ), 10 ) : null;

		// Advanced - Tab ---------------------------------------------------------------------------------------------.

		// General - Section ------------------------------------------------------------------------------------------.
		$options['daimma_import_post_type']                     = $request->get_param( 'daimma_import_post_type' ) !== null ? sanitize_key( $request->get_param( 'daimma_import_post_type' ) ) : null;
		$options['daimma_log_import_data']                      = $request->get_param( 'daimma_log_import_data' ) !== null ? intval( $request->get_param( 'daimma_log_import_data' ), 10 ) : null;

		// Capabilities - Section -------------------------------------------------------------------------------------.
		$options['daimma_import_menu_required_capability']      = $request->get_param( 'daimma_import_menu_required_capability' ) !== null ? sanitize_key( $request->get_param( 'daimma_import_menu_required_capability' ) ) : null;
		$options['daimma_log_menu_required_capability']         = $request->get_param( 'daimma_log_menu_required_capability' ) !== null ? sanitize_key( $request->get_param( 'daimma_log_menu_required_capability' ) ) : null;
		$options['daimma_maintenance_menu_required_capability'] = $request->get_param( 'daimma_maintenance_menu_required_capability' ) !== null ? sanitize_key( $request->get_param( 'daimma_maintenance_menu_required_capability' ) ) : null;

		// Update the options -----------------------------------------------------------------------------------------.
		foreach ( $options as $key => $option ) {
			if ( null !== $option ) {
				update_option( $key, $option );
			}
		}

		$response = new WP_REST_Response( 'Data successfully added.', '200' );

		return $response;
	}

	/**
	 * Check the user capability.
	 *
	 * @return true|WP_Error
	 */
	public function rest_api_daext_import_markdown_update_options_callback_permission_check() {

		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error(
				'rest_update_error',
				'Sorry, you are not allowed to update the options.',
				array( 'status' => 403 )
			);
		}

		return true;
	}

	/**
	 * Callback for the POST 'daext-import-markdown/v1/log' endpoint of the Rest API.
	 *
	 * This method is in the following contexts:
	 *
	 * - In the "Dashboard" menu to retrieve the statistics of the links on the posts.
	 *
	 * @param object $request The request data.
	 *
	 * @return WP_Error|WP_REST_Response
	 */
	public function rest_api_daext_import_markdown_read_log_statistics_callback( $request ) {

		// Attempt to increase the memory limit.
		wp_raise_memory_limit();

		$data_update_required = intval( $request->get_param( 'data_update_required' ), 10 );

		if ( 0 === $data_update_required ) {

			// Use the provided form data.
			$search_string  = sanitize_text_field( $request->get_param( 'search_string' ) );
			$sorting_column = sanitize_text_field( $request->get_param( 'sorting_column' ) );
			$sorting_order  = sanitize_text_field( $request->get_param( 'sorting_order' ) );

		} else {

			// Set the default values of the form data.
			$search_string  = '';
			$sorting_column = 'log_id';
			$sorting_order  = 'desc';

			// Run update_log() to update the archive with the statistics.
			$this->shared->update_log();

		}

		// Create the WHERE part of the query based on the $optimization_status value.
		global $wpdb;
		$filter = '';

		// Create the WHERE part of the string based on the $search_string value.
		if ( '' !== $search_string ) {
			if ( strlen( $filter ) === 0 ) {
				$filter .= $wpdb->prepare( 'WHERE (post_title LIKE %s)', '%' . $search_string . '%' );
			} else {
				$filter .= $wpdb->prepare( ' AND (post_title LIKE %s)', '%' . $search_string . '%' );

			}
		}

		// Create the ORDER BY part of the query based on the $sorting_column and $sorting_order values.
		if ( '' !== $sorting_column ) {
			$filter .= ' ORDER BY ' . sanitize_key( $sorting_column );
		} else {
			$filter .= ' ORDER BY log_id';
		}

		if ( 'desc' === $sorting_order ) {
			$filter .= ' DESC';
		} else {
			$filter .= ' ASC';
		}

		// Get the data from the "_archive" db table using $wpdb and put them in the $response array.

		$offset = 0;
		$limit  = 100; // Set the limit to 100 records per iteration.

		// Set a hardcoded maximum number of records to fetch equal to 10000.
		$max = 10000; // Set the maximum number of records to fetch.

		$all_requests = array(); // Initialize an array to hold all requests.

		do {

			// Fetch the records in batches of 100.

			// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- $filter is prepared.
			// phpcs:disable WordPress.DB.DirectDatabaseQuery
			$requests = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT log_id, date, file_name, post_id, post_title, post_permalink, post_edit_link FROM {$wpdb->prefix}daimma_log $filter LIMIT %d OFFSET %d",
					$limit,
					$offset
				)
			);
			// phpcs:enable

			// Store the number of records fetched in the $request_count variable.
			$request_count = is_array( $requests ) ? count( $requests ) : 0;

			if ( is_array( $requests ) && count( $requests ) > 0 ) {

				foreach ( $requests as $key => $request ) {

					/**
					 * Add the formatted date (based on the date format defined in the WordPress settings) to the $requests
					 * array.
					 */
					$request->formatted_date = mysql2date( get_option( 'date_format' ), $request->date );

					// Append the processed request to the all_requests array.
					$all_requests[] = $request;

				}

				// Increase the offset for the next batch.
				$offset += $limit;

				// Exit if the maximum number of records has been reached.
				if ( $offset >= $max ) {
					break;
				}
			}
		} while ( $request_count > 0 );

		if ( count( $all_requests ) > 0 ) {

			$response = array(
				'statistics' => array(
					'all_records' => count( $all_requests ),
				),
				'table'      => $all_requests,
			);

		} else {

			$response = array(
				'statistics' => array(
					'all_records' => 0,
				),
				'table'      => array(),
			);

		}

		// Prepare the response.
		$response = new WP_REST_Response( $response );

		return $response;
	}

	/**
	 * Check the user capability.
	 *
	 * @return true|WP_Error
	 */
	public function rest_api_daext_import_markdown_read_log_statistics_callback_permission_check() {

		if ( ! current_user_can( get_option( $this->shared->get( 'slug' ) . '_log_menu_required_capability' ) ) ) {
			return new WP_Error(
				'rest_update_error',
				'Sorry, you are not allowed to read the Import Markdown statistics.',
				array( 'status' => 403 )
			);
		}

		return true;
	}
}
