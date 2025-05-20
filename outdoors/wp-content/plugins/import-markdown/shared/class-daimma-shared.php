<?php
/**
 * This class should be used to stores properties and methods shared by the
 * admin and public side of WordPress.
 *
 * @package import-markdown
 */

// CommonMark.
use League\CommonMark\CommonMarkConverter;
use League\CommonMark\GithubFlavoredMarkdownConverter;

/**
 * This class should be used to stores properties and methods shared by the
 * admin and public side of WordPress.
 */
class Daimma_Shared {

	/**
	 * The singleton instance of the class.
	 *
	 * @var Daimma_Shared
	 */
	protected static $instance = null;

	/**
	 * The data of the plugin.
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * Constructor.
	 */
	private function __construct() {

		$this->data['slug'] = 'daimma';
		$this->data['ver']  = '1.14';
		$this->data['dir']  = substr( plugin_dir_path( __FILE__ ), 0, -7 );
		$this->data['url']  = substr( plugin_dir_url( __FILE__ ), 0, -7 );

		// Here are stored the plugin option with the related default values.
		$this->data['options'] = array(

			// Database version. (not available in the options UI).
			$this->get( 'slug' ) . '_database_version'     => '0',

			// Options version. (not available in the options UI).
			$this->get( 'slug' ) . '_options_version'      => '0',

			// Option used to save the dismissible notices of all the users. (not available in the options UI).
			$this->get( 'slug' ) . '_dismissible_notice_a' => array(),

			// General ------------------------------------------------------------------------------------------------.
			$this->get( 'slug' ) . '_import_post_type'     => 'post',
			$this->get( 'slug' ) . '_log_import_data'      => '1',
			$this->get( 'slug' ) . '_import_menu_required_capability' => 'edit_posts',
			$this->get( 'slug' ) . '_log_menu_required_capability' => 'edit_posts',
			$this->get( 'slug' ) . '_maintenance_menu_required_capability' => 'edit_posts',
			$this->get( 'slug' ) . '_markdown_parser'      => 'parsedown',
			$this->get( 'slug' ) . '_cebe_markdown_html5'  => '0',
			$this->get( 'slug' ) . '_cebe_markdown_keep_list_start_number' => '0',
			$this->get( 'slug' ) . '_cebe_markdown_enable_new_lines' => '0',

			// CommonMark ---------------------------------------------------------------------------------------------.

			// Renderer related options.
			$this->get( 'slug' ) . '_commonmark_block_separator' => '\n',
			$this->get( 'slug' ) . '_commonmark_inner_separator' => '\n',
			$this->get( 'slug' ) . '_commonmark_soft_break' => '\n',

			// CommonMark related options.
			$this->get( 'slug' ) . '_commonmark_enable_em' => '1',
			$this->get( 'slug' ) . '_commonmark_enable_strong' => '1',
			$this->get( 'slug' ) . '_commonmark_use_asterisk' => '1',
			$this->get( 'slug' ) . '_commonmark_use_underscore' => '1',
			$this->get( 'slug' ) . '_commonmark_unordered_list_markers' => array( '-', '*', '+' ),

			// HTML input related options.
			$this->get( 'slug' ) . '_commonmark_html_input' => 'escape',
			$this->get( 'slug' ) . '_commonmark_allow_unsafe_links' => '0',
			$this->get( 'slug' ) . '_commonmark_max_nesting_level' => '10',

		);
	}

	/**
	 * Get the singleton instance of the class.
	 *
	 * @return Daimma_Shared|self|null
	 */
	public static function get_instance() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Retrieve data.
	 *
	 * @param string $index The index of the data to retrieve.
	 *
	 * @return mixed
	 */
	public function get( $index ) {
		return $this->data[ $index ];
	}

	/**
	 * Returns an array with the data used by React to initialize the options.
	 *
	 * @return array[]
	 */
	public function menu_options_configuration() {

		// Get the public post types that have a UI.
		$args               = array(
			'public'  => true,
			'show_ui' => true,
		);
		$post_types_with_ui = get_post_types( $args );
		unset( $post_types_with_ui['attachment'] );
		$post_types_select_options = array();
		foreach ( $post_types_with_ui as $post_type ) {
			$post_types_select_options[] = array(
				'value' => $post_type,
				'text'  => $post_type,
			);
		}

		$configuration = array(
			array(
				'title'       => __( 'Conversion', 'import-markdown' ),
				'description' => __( 'Customize the settings for parsing Markdown.', 'import-markdown' ),
				'cards'       => array(
					array(
						'title'   => __( 'Markdown Parser', 'import-markdown' ),
						'options' => array(
							array(
								'name'          => 'daimma_markdown_parser',
								'label'         => __( 'Parser', 'import-markdown' ),
								'type'          => 'select',
								'tooltip'       => __(
									'This option determines which parser should be used to perform the conversion.',
									'import-markdown'
								),
								'help'          => __( 'This option determines which parser should be used to perform the conversion.', 'import-markdown' ),
								'selectOptions' => array(
									array(
										'value' => 'parsedown',
										'text'  => __( 'Parsedown', 'import-markdown' ),
									),
									array(
										'value' => 'parsedown_extra',
										'text'  => __( 'Parsedown Extra', 'import-markdown' ),
									),
									array(
										'value' => 'cebe_markdown',
										'text'  => __( 'Cebe Markdown', 'import-markdown' ),
									),
									array(
										'value' => 'cebe_markdown_github_flavored',
										'text'  => __( 'Cebe Markdown Github', 'import-markdown' ),
									),
									array(
										'value' => 'cebe_markdown_extra',
										'text'  => __( 'Cebe Markdown Extra', 'import-markdown' ),
									),
									array(
										'value' => 'commonmark',
										'text'  => __( 'League CommonMark', 'import-markdown' ),
									),
									array(
										'value' => 'commonmark_github',
										'text'  => __( 'League CommonMark GitHub', 'import-markdown' ),
									),
								),
							),
						),
					),
					array(
						'title'   => __( 'Cebe Markdown', 'import-markdown' ),
						'options' => array(
							array(
								'name'    => 'daimma_cebe_markdown_html5',
								'label'   => __( 'HTML5', 'import-markdown' ),
								'type'    => 'toggle',
								'tooltip' => __(
									'Enable HTML5 output instead of HTML4. This feature is applied only if one of the three "Cebe Markdown" parsers is used.',
									'import-markdown'
								),
								'help'    => __( 'Enable HTML5 output instead of HTML4.', 'import-markdown' ),
							),
							array(
								'name'    => 'daimma_cebe_markdown_keep_list_start_number',
								'label'   => __( 'List Start', 'import-markdown' ),
								'type'    => 'toggle',
								'tooltip' => __(
									'Keep the number in the ordered lists as specified in the Markdown. The default behavior is to always start from 1 and increment by one regardless of the number specified in the Markdown. This feature is applied only if one of the three "Cebe Markdown" parsers is used.',
									'import-markdown'
								),
								'help'    => __( 'Keep the number in the ordered lists as specified in the Markdown.', 'import-markdown' ),
							),
							array(
								'name'    => 'daimma_cebe_markdown_enable_new_lines',
								'label'   => __( 'New Lines', 'import-markdown' ),
								'type'    => 'toggle',
								'tooltip' => __(
									'To convert all the newlines to <br/> tags. By default only the newlines with two preceding spaces are converted to <br/> tags. This feature is applied only if the "Cebe Markdown GitHub" parser is used.',
									'import-markdown'
								),
								'help'    => __( 'Convert all the newlines to <br/> tags.', 'import-markdown' ),
							),
						),
					),
					array(
						'title'   => __( 'League CommonMark', 'import-markdown' ),
						'options' => array(
							array(
								'name'    => 'daimma_commonmark_enable_em',
								'label'   => __( 'Enable em', 'import-markdown' ),
								'type'    => 'toggle',
								'tooltip' => __(
									'Enable parsing for the "em" tag.',
									'import-markdown'
								),
								'help'    => __( 'Enable <em> tag parsing.', 'import-markdown' ),
							),
							array(
								'name'    => 'daimma_commonmark_enable_strong',
								'label'   => __( 'Enable strong', 'import-markdown' ),
								'type'    => 'toggle',
								'tooltip' => __(
									'Enable parsing for the "strong" tag.',
									'import-markdown'
								),
								'help'    => __( 'Enable <strong> tag parsing.', 'import-markdown' ),
							),
							array(
								'name'    => 'daimma_commonmark_use_asterisk',
								'label'   => __( 'Use asterisk', 'import-markdown' ),
								'type'    => 'toggle',
								'tooltip' => __(
									'Enable parsing of "*" for emphasis.',
									'import-markdown'
								),
								'help'    => __( 'Enable parsing of "*" for emphasis.', 'import-markdown' ),
							),
							array(
								'name'    => 'daimma_commonmark_use_underscore',
								'label'   => __( 'Use underscore', 'import-markdown' ),
								'type'    => 'toggle',
								'tooltip' => __(
									'Enable parsing of "_" for emphasis.',
									'import-markdown'
								),
								'help'    => __( 'Enable parsing of "_" for emphasis.', 'import-markdown' ),
							),
							array(
								'name'          => 'daimma_commonmark_unordered_list_markers',
								'label'         => __( 'Unordered List Markers', 'import-markdown' ),
								'type'          => 'select-multiple',
								'tooltip'       => __(
									'The characters that can be used to indicate a bulleted list.',
									'import-markdown'
								),
								'help'          => __( 'Select the characters that can be used to indicate a bulleted list.', 'import-markdown' ),
								'selectOptions' => array(
									array(
										'value' => '-',
										'text'  => __( 'Dash (-)', 'import-markdown' ),
									),
									array(
										'value' => '*',
										'text'  => __( 'Asterisk (*)', 'import-markdown' ),
									),
									array(
										'value' => '+',
										'text'  => __( 'Plus (+)', 'import-markdown' ),
									),
								),
							),
							array(
								'name'          => 'daimma_commonmark_html_input',
								'label'         => __( 'CommonMark HTML Input', 'import-markdown' ),
								'type'          => 'select',
								'tooltip'       => __(
									'Select how to handle HTML input.',
									'import-markdown'
								),
								'help'          => __( 'Select how to handle HTML input.', 'import-markdown' ),
								'selectOptions' => array(
									array(
										'value' => 'strip',
										'text'  => __( 'Strip', 'import-markdown' ),
									),
									array(
										'value' => 'allow',
										'text'  => __( 'Allow', 'import-markdown' ),
									),
									array(
										'value' => 'escape',
										'text'  => __( 'Escape', 'import-markdown' ),
									),
								),
							),
							array(
								'name'    => 'daimma_commonmark_allow_unsafe_links',
								'label'   => __( 'Allow Unsafe Links', 'import-markdown' ),
								'type'    => 'toggle',
								'tooltip' => __(
									'Remove risky link and image URLs.',
									'import-markdown'
								),
								'help'    => __( 'Remove risky link and image URLs.', 'import-markdown' ),
							),
							array(
								'name'      => 'daimma_commonmark_max_nesting_level',
								'label'     => __( 'Max Nesting Level', 'import-markdown' ),
								'type'      => 'range',
								'tooltip'   => __(
									'The maximum nesting level for blocks.',
									'import-markdown'
								),
								'help'      => __( 'The maximum nesting level for blocks.', 'import-markdown' ),
								'rangeMin'  => 1,
								'rangeMax'  => 100,
								'rangeStep' => 1,
							),
						),
					),
				),
			),
			array(
				'title'       => __( 'Advanced', 'import-markdown' ),
				'description' => __( 'Manage advanced plugin settings.', 'import-markdown' ),
				'cards'       => array(
					array(
						'title'   => __( 'General', 'import-markdown' ),
						'options' => array(
							array(
								'name'          => 'daimma_import_post_type',
								'label'         => __( 'Post Type', 'import-markdown' ),
								'type'          => 'select',
								'tooltip'       => __(
									'The post type to which the posts generated from the imported Markdown files should be assigned.',
									'import-markdown'
								),
								'help'          => __( 'The post type to which the posts generated from the imported Markdown files should be assigned.', 'import-markdown' ),
								'selectOptions' => $post_types_select_options,
							),
							array(
								'name'          => 'daimma_log_import_data',
								'label'         => __( 'Log Import Data', 'import-markdown' ),
								'type'          => 'toggle',
								'tooltip'       => __(
									'When the user imports files, the plugin can store the import information. The collected records are available in the "Log" menu.',
									'import-markdown'
								),
								'help'          => __( 'Enable data logging for imported files.', 'import-markdown' ),
								'selectOptions' => $post_types_select_options,
							),
						),
					),
					array(
						'title'   => __( 'Capabilities', 'import-markdown' ),
						'options' => array(
							array(
								'name'    => 'daimma_import_menu_required_capability',
								'label'   => __( 'Import Menu', 'import-markdown' ),
								'type'    => 'text',
								'tooltip' => __(
									'The capability required to get access on the "Import" menu.',
									'import-markdown'
								),
								'help'    => __( 'The capability required to get access on the "Import" menu.', 'import-markdown' ),
							),
							array(
								'name'    => 'daimma_log_menu_required_capability',
								'label'   => __( 'Log Menu', 'import-markdown' ),
								'type'    => 'text',
								'tooltip' => __(
									'The capability required to get access on the "Log" menu.',
									'import-markdown'
								),
								'help'    => __( 'The capability required to get access on the "Log" menu.', 'import-markdown' ),
							),
							array(
								'name'    => 'daimma_maintenance_menu_required_capability',
								'label'   => __( 'Maintenance Menu', 'import-markdown' ),
								'type'    => 'text',
								'tooltip' => __(
									'The capability required to get access on the "Maintenance" menu.',
									'import-markdown'
								),
								'help'    => __( 'The capability required to get access on the "Maintenance" menu.', 'import-markdown' ),
							),
						),
					),
				),
			),
		);

		return $configuration;
	}

	/**
	 * Echo the SVG icon specified by the $icon_name parameter.
	 *
	 * @param string $icon_name The name of the icon to echo.
	 *
	 * @return void
	 */
	public function echo_icon_svg( $icon_name ) {

		switch ( $icon_name ) {

			case 'check-done-02':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M16 8V5.2C16 4.0799 16 3.51984 15.782 3.09202C15.5903 2.71569 15.2843 2.40973 14.908 2.21799C14.4802 2 13.9201 2 12.8 2H5.2C4.0799 2 3.51984 2 3.09202 2.21799C2.71569 2.40973 2.40973 2.71569 2.21799 3.09202C2 3.51984 2 4.0799 2 5.2V12.8C2 13.9201 2 14.4802 2.21799 14.908C2.40973 15.2843 2.71569 15.5903 3.09202 15.782C3.51984 16 4.0799 16 5.2 16H8M12 15L14 17L18.5 12.5M11.2 22H18.8C19.9201 22 20.4802 22 20.908 21.782C21.2843 21.5903 21.5903 21.2843 21.782 20.908C22 20.4802 22 19.9201 22 18.8V11.2C22 10.0799 22 9.51984 21.782 9.09202C21.5903 8.71569 21.2843 8.40973 20.908 8.21799C20.4802 8 19.9201 8 18.8 8H11.2C10.0799 8 9.51984 8 9.09202 8.21799C8.71569 8.40973 8.40973 8.71569 8.21799 9.09202C8 9.51984 8 10.0799 8 11.2V18.8C8 19.9201 8 20.4802 8.21799 20.908C8.40973 21.2843 8.71569 21.5903 9.09202 21.782C9.51984 22 10.0799 22 11.2 22Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'upload-01':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M21 15V16.2C21 17.8802 21 18.7202 20.673 19.362C20.3854 19.9265 19.9265 20.3854 19.362 20.673C18.7202 21 17.8802 21 16.2 21H7.8C6.11984 21 5.27976 21 4.63803 20.673C4.07354 20.3854 3.6146 19.9265 3.32698 19.362C3 18.7202 3 17.8802 3 16.2V15M17 8L12 3M12 3L7 8M12 3V15" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'upload-03':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M16 12L12 8M12 8L8 12M12 8V16M22 12C22 17.5228 17.5228 22 12 22C6.47715 22 2 17.5228 2 12C2 6.47715 6.47715 2 12 2C17.5228 2 22 6.47715 22 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'upload-04':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M16 12L12 8M12 8L8 12M12 8V17.2C12 18.5907 12 19.2861 12.5505 20.0646C12.9163 20.5819 13.9694 21.2203 14.5972 21.3054C15.5421 21.4334 15.9009 21.2462 16.6186 20.8719C19.8167 19.2036 22 15.8568 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 15.7014 4.01099 18.9331 7 20.6622" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'brackets-ellipses':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M18.5708 20C19.8328 20 20.8568 18.977 20.8568 17.714V13.143L21.9998 12L20.8568 10.857V6.286C20.8568 5.023 19.8338 4 18.5708 4M5.429 4C4.166 4 3.143 5.023 3.143 6.286V10.857L2 12L3.143 13.143V17.714C3.143 18.977 4.166 20 5.429 20M7.5 12H7.51M12 12H12.01M16.5 12H16.51M8 12C8 12.2761 7.77614 12.5 7.5 12.5C7.22386 12.5 7 12.2761 7 12C7 11.7239 7.22386 11.5 7.5 11.5C7.77614 11.5 8 11.7239 8 12ZM12.5 12C12.5 12.2761 12.2761 12.5 12 12.5C11.7239 12.5 11.5 12.2761 11.5 12C11.5 11.7239 11.7239 11.5 12 11.5C12.2761 11.5 12.5 11.7239 12.5 12ZM17 12C17 12.2761 16.7761 12.5 16.5 12.5C16.2239 12.5 16 12.2761 16 12C16 11.7239 16.2239 11.5 16.5 11.5C16.7761 11.5 17 11.7239 17 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'file-02':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M14 11H8M10 15H8M16 7H8M20 6.8V17.2C20 18.8802 20 19.7202 19.673 20.362C19.3854 20.9265 18.9265 21.3854 18.362 21.673C17.7202 22 16.8802 22 15.2 22H8.8C7.11984 22 6.27976 22 5.63803 21.673C5.07354 21.3854 4.6146 20.9265 4.32698 20.362C4 19.7202 4 18.8802 4 17.2V6.8C4 5.11984 4 4.27976 4.32698 3.63803C4.6146 3.07354 5.07354 2.6146 5.63803 2.32698C6.27976 2 7.11984 2 8.8 2H15.2C16.8802 2 17.7202 2 18.362 2.32698C18.9265 2.6146 19.3854 3.07354 19.673 3.63803C20 4.27976 20 5.11984 20 6.8Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'file-check-02':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M20 12.5V6.8C20 5.11984 20 4.27976 19.673 3.63803C19.3854 3.07354 18.9265 2.6146 18.362 2.32698C17.7202 2 16.8802 2 15.2 2H8.8C7.11984 2 6.27976 2 5.63803 2.32698C5.07354 2.6146 4.6146 3.07354 4.32698 3.63803C4 4.27976 4 5.11984 4 6.8V17.2C4 18.8802 4 19.7202 4.32698 20.362C4.6146 20.9265 5.07354 21.3854 5.63803 21.673C6.27976 22 7.11984 22 8.8 22H12M14 11H8M10 15H8M16 7H8M14.5 19L16.5 21L21 16.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'file-06':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M14 2.26953V6.40007C14 6.96012 14 7.24015 14.109 7.45406C14.2049 7.64222 14.3578 7.7952 14.546 7.89108C14.7599 8.00007 15.0399 8.00007 15.6 8.00007H19.7305M16 13H8M16 17H8M10 9H8M14 2H8.8C7.11984 2 6.27976 2 5.63803 2.32698C5.07354 2.6146 4.6146 3.07354 4.32698 3.63803C4 4.27976 4 5.11984 4 6.8V17.2C4 18.8802 4 19.7202 4.32698 20.362C4.6146 20.9265 5.07354 21.3854 5.63803 21.673C6.27976 22 7.11984 22 8.8 22H15.2C16.8802 22 17.7202 22 18.362 21.673C18.9265 21.3854 19.3854 20.9265 19.673 20.362C20 19.7202 20 18.8802 20 17.2V8L14 2Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'rows-01':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M17.8 10C18.9201 10 19.4802 10 19.908 9.78201C20.2843 9.59027 20.5903 9.28431 20.782 8.90798C21 8.48016 21 7.92011 21 6.8V6.2C21 5.0799 21 4.51984 20.782 4.09202C20.5903 3.7157 20.2843 3.40973 19.908 3.21799C19.4802 3 18.9201 3 17.8 3L6.2 3C5.0799 3 4.51984 3 4.09202 3.21799C3.71569 3.40973 3.40973 3.71569 3.21799 4.09202C3 4.51984 3 5.07989 3 6.2L3 6.8C3 7.9201 3 8.48016 3.21799 8.90798C3.40973 9.28431 3.71569 9.59027 4.09202 9.78201C4.51984 10 5.07989 10 6.2 10L17.8 10Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
<path d="M17.8 21C18.9201 21 19.4802 21 19.908 20.782C20.2843 20.5903 20.5903 20.2843 20.782 19.908C21 19.4802 21 18.9201 21 17.8V17.2C21 16.0799 21 15.5198 20.782 15.092C20.5903 14.7157 20.2843 14.4097 19.908 14.218C19.4802 14 18.9201 14 17.8 14L6.2 14C5.0799 14 4.51984 14 4.09202 14.218C3.71569 14.4097 3.40973 14.7157 3.21799 15.092C3 15.5198 3 16.0799 3 17.2L3 17.8C3 18.9201 3 19.4802 3.21799 19.908C3.40973 20.2843 3.71569 20.5903 4.09202 20.782C4.51984 21 5.07989 21 6.2 21H17.8Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'rows-02':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M3 12H21M7.8 3H16.2C17.8802 3 18.7202 3 19.362 3.32698C19.9265 3.6146 20.3854 4.07354 20.673 4.63803C21 5.27976 21 6.11984 21 7.8V16.2C21 17.8802 21 18.7202 20.673 19.362C20.3854 19.9265 19.9265 20.3854 19.362 20.673C18.7202 21 17.8802 21 16.2 21H7.8C6.11984 21 5.27976 21 4.63803 20.673C4.07354 20.3854 3.6146 19.9265 3.32698 19.362C3 18.7202 3 17.8802 3 16.2V7.8C3 6.11984 3 5.27976 3.32698 4.63803C3.6146 4.07354 4.07354 3.6146 4.63803 3.32698C5.27976 3 6.11984 3 7.8 3Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'divider':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M3 12H3.01M7.5 12H7.51M16.5 12H16.51M12 12H12.01M21 12H21.01M21 21V20.2C21 19.0799 21 18.5198 20.782 18.092C20.5903 17.7157 20.2843 17.4097 19.908 17.218C19.4802 17 18.9201 17 17.8 17H6.2C5.0799 17 4.51984 17 4.09202 17.218C3.7157 17.4097 3.40973 17.7157 3.21799 18.092C3 18.5198 3 19.0799 3 20.2V21M21 3V3.8C21 4.9201 21 5.48016 20.782 5.90798C20.5903 6.28431 20.2843 6.59027 19.908 6.78201C19.4802 7 18.9201 7 17.8 7H6.2C5.0799 7 4.51984 7 4.09202 6.78201C3.71569 6.59027 3.40973 6.28431 3.21799 5.90798C3 5.48016 3 4.92011 3 3.8V3" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'rows-03':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M3 9H21M3 15H21M7.8 3H16.2C17.8802 3 18.7202 3 19.362 3.32698C19.9265 3.6146 20.3854 4.07354 20.673 4.63803C21 5.27976 21 6.11984 21 7.8V16.2C21 17.8802 21 18.7202 20.673 19.362C20.3854 19.9265 19.9265 20.3854 19.362 20.673C18.7202 21 17.8802 21 16.2 21H7.8C6.11984 21 5.27976 21 4.63803 20.673C4.07354 20.3854 3.6146 19.9265 3.32698 19.362C3 18.7202 3 17.8802 3 16.2V7.8C3 6.11984 3 5.27976 3.32698 4.63803C3.6146 4.07354 4.07354 3.6146 4.63803 3.32698C5.27976 3 6.11984 3 7.8 3Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'list':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M21 12L9 12M21 6L9 6M21 18L9 18M5 12C5 12.5523 4.55228 13 4 13C3.44772 13 3 12.5523 3 12C3 11.4477 3.44772 11 4 11C4.55228 11 5 11.4477 5 12ZM5 6C5 6.55228 4.55228 7 4 7C3.44772 7 3 6.55228 3 6C3 5.44772 3.44772 5 4 5C4.55228 5 5 5.44772 5 6ZM5 18C5 18.5523 4.55228 19 4 19C3.44772 19 3 18.5523 3 18C3 17.4477 3.44772 17 4 17C4.55228 17 5 17.4477 5 18Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'line-chart-up-03':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M17 9L11.5657 14.4343C11.3677 14.6323 11.2687 14.7313 11.1545 14.7684C11.0541 14.8011 10.9459 14.8011 10.8455 14.7684C10.7313 14.7313 10.6323 14.6323 10.4343 14.4343L8.56569 12.5657C8.36768 12.3677 8.26867 12.2687 8.15451 12.2316C8.05409 12.1989 7.94591 12.1989 7.84549 12.2316C7.73133 12.2687 7.63232 12.3677 7.43431 12.5657L3 17M17 9H13M17 9V13M7.8 21H16.2C17.8802 21 18.7202 21 19.362 20.673C19.9265 20.3854 20.3854 19.9265 20.673 19.362C21 18.7202 21 17.8802 21 16.2V7.8C21 6.11984 21 5.27976 20.673 4.63803C20.3854 4.07354 19.9265 3.6146 19.362 3.32698C18.7202 3 17.8802 3 16.2 3H7.8C6.11984 3 5.27976 3 4.63803 3.32698C4.07354 3.6146 3.6146 4.07354 3.32698 4.63803C3 5.27976 3 6.11984 3 7.8V16.2C3 17.8802 3 18.7202 3.32698 19.362C3.6146 19.9265 4.07354 20.3854 4.63803 20.673C5.27976 21 6.11984 21 7.8 21Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'tool-02':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M6 6L10.5 10.5M6 6H3L2 3L3 2L6 3V6ZM19.259 2.74101L16.6314 5.36863C16.2354 5.76465 16.0373 5.96265 15.9632 6.19098C15.8979 6.39183 15.8979 6.60817 15.9632 6.80902C16.0373 7.03735 16.2354 7.23535 16.6314 7.63137L16.8686 7.86863C17.2646 8.26465 17.4627 8.46265 17.691 8.53684C17.8918 8.6021 18.1082 8.6021 18.309 8.53684C18.5373 8.46265 18.7354 8.26465 19.1314 7.86863L21.5893 5.41072C21.854 6.05488 22 6.76039 22 7.5C22 10.5376 19.5376 13 16.5 13C16.1338 13 15.7759 12.9642 15.4298 12.8959C14.9436 12.8001 14.7005 12.7521 14.5532 12.7668C14.3965 12.7824 14.3193 12.8059 14.1805 12.8802C14.0499 12.9501 13.919 13.081 13.657 13.343L6.5 20.5C5.67157 21.3284 4.32843 21.3284 3.5 20.5C2.67157 19.6716 2.67157 18.3284 3.5 17.5L10.657 10.343C10.919 10.081 11.0499 9.95005 11.1198 9.81949C11.1941 9.68068 11.2176 9.60347 11.2332 9.44681C11.2479 9.29945 11.1999 9.05638 11.1041 8.57024C11.0358 8.22406 11 7.86621 11 7.5C11 4.46243 13.4624 2 16.5 2C17.5055 2 18.448 2.26982 19.259 2.74101ZM12.0001 14.9999L17.5 20.4999C18.3284 21.3283 19.6716 21.3283 20.5 20.4999C21.3284 19.6715 21.3284 18.3283 20.5 17.4999L15.9753 12.9753C15.655 12.945 15.3427 12.8872 15.0408 12.8043C14.6517 12.6975 14.2249 12.7751 13.9397 13.0603L12.0001 14.9999Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'code-browser':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M22 9H2M14 17.5L16.5 15L14 12.5M10 12.5L7.5 15L10 17.5M2 7.8L2 16.2C2 17.8802 2 18.7202 2.32698 19.362C2.6146 19.9265 3.07354 20.3854 3.63803 20.673C4.27976 21 5.11984 21 6.8 21H17.2C18.8802 21 19.7202 21 20.362 20.673C20.9265 20.3854 21.3854 19.9265 21.673 19.362C22 18.7202 22 17.8802 22 16.2V7.8C22 6.11984 22 5.27977 21.673 4.63803C21.3854 4.07354 20.9265 3.6146 20.362 3.32698C19.7202 3 18.8802 3 17.2 3L6.8 3C5.11984 3 4.27976 3 3.63803 3.32698C3.07354 3.6146 2.6146 4.07354 2.32698 4.63803C2 5.27976 2 6.11984 2 7.8Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'toggle-02-right':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M13.9995 16H6C3.79086 16 2 14.2091 2 12C2 9.79086 3.79086 8 6 8H13.9995M21.9995 12C21.9995 14.7614 19.7609 17 16.9995 17C14.2381 17 11.9995 14.7614 11.9995 12C11.9995 9.23858 14.2381 7 16.9995 7C19.7609 7 21.9995 9.23858 21.9995 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'palette':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M2 12C2 17.5228 6.47715 22 12 22C13.6569 22 15 20.6569 15 19V18.5C15 18.0356 15 17.8034 15.0257 17.6084C15.2029 16.2622 16.2622 15.2029 17.6084 15.0257C17.8034 15 18.0356 15 18.5 15H19C20.6569 15 22 13.6569 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M7 13C7.55228 13 8 12.5523 8 12C8 11.4477 7.55228 11 7 11C6.44772 11 6 11.4477 6 12C6 12.5523 6.44772 13 7 13Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M16 9C16.5523 9 17 8.55228 17 8C17 7.44772 16.5523 7 16 7C15.4477 7 15 7.44772 15 8C15 8.55228 15.4477 9 16 9Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M10 8C10.5523 8 11 7.55228 11 7C11 6.44772 10.5523 6 10 6C9.44772 6 9 6.44772 9 7C9 7.55228 9.44772 8 10 8Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'book-open-01':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M12 21L11.8999 20.8499C11.2053 19.808 10.858 19.287 10.3991 18.9098C9.99286 18.5759 9.52476 18.3254 9.02161 18.1726C8.45325 18 7.82711 18 6.57482 18H5.2C4.07989 18 3.51984 18 3.09202 17.782C2.71569 17.5903 2.40973 17.2843 2.21799 16.908C2 16.4802 2 15.9201 2 14.8V6.2C2 5.07989 2 4.51984 2.21799 4.09202C2.40973 3.71569 2.71569 3.40973 3.09202 3.21799C3.51984 3 4.07989 3 5.2 3H5.6C7.84021 3 8.96031 3 9.81596 3.43597C10.5686 3.81947 11.1805 4.43139 11.564 5.18404C12 6.03968 12 7.15979 12 9.4M12 21V9.4M12 21L12.1001 20.8499C12.7947 19.808 13.142 19.287 13.6009 18.9098C14.0071 18.5759 14.4752 18.3254 14.9784 18.1726C15.5467 18 16.1729 18 17.4252 18H18.8C19.9201 18 20.4802 18 20.908 17.782C21.2843 17.5903 21.5903 17.2843 21.782 16.908C22 16.4802 22 15.9201 22 14.8V6.2C22 5.07989 22 4.51984 21.782 4.09202C21.5903 3.71569 21.2843 3.40973 20.908 3.21799C20.4802 3 19.9201 3 18.8 3H18.4C16.1598 3 15.0397 3 14.184 3.43597C13.4314 3.81947 12.8195 4.43139 12.436 5.18404C12 6.03968 12 7.15979 12 9.4" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'target-03':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M22 12H18M6 12H2M12 6V2M12 22V18M20 12C20 16.4183 16.4183 20 12 20C7.58172 20 4 16.4183 4 12C4 7.58172 7.58172 4 12 4C16.4183 4 20 7.58172 20 12ZM15 12C15 13.6569 13.6569 15 12 15C10.3431 15 9 13.6569 9 12C9 10.3431 10.3431 9 12 9C13.6569 9 15 10.3431 15 12Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'message-text-square-02':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M7 8.5H12M7 12H15M7 18V20.3355C7 20.8684 7 21.1348 7.10923 21.2716C7.20422 21.3906 7.34827 21.4599 7.50054 21.4597C7.67563 21.4595 7.88367 21.2931 8.29976 20.9602L10.6852 19.0518C11.1725 18.662 11.4162 18.4671 11.6875 18.3285C11.9282 18.2055 12.1844 18.1156 12.4492 18.0613C12.7477 18 13.0597 18 13.6837 18H16.2C17.8802 18 18.7202 18 19.362 17.673C19.9265 17.3854 20.3854 16.9265 20.673 16.362C21 15.7202 21 14.8802 21 13.2V7.8C21 6.11984 21 5.27976 20.673 4.63803C20.3854 4.07354 19.9265 3.6146 19.362 3.32698C18.7202 3 17.8802 3 16.2 3H7.8C6.11984 3 5.27976 3 4.63803 3.32698C4.07354 3.6146 3.6146 4.07354 3.32698 4.63803C3 5.27976 3 6.11984 3 7.8V14C3 14.93 3 15.395 3.10222 15.7765C3.37962 16.8117 4.18827 17.6204 5.22354 17.8978C5.60504 18 6.07003 18 7 18Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'bar-chart-07':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M21 21H6.2C5.07989 21 4.51984 21 4.09202 20.782C3.71569 20.5903 3.40973 20.2843 3.21799 19.908C3 19.4802 3 18.9201 3 17.8V3M7 10.5V17.5M11.5 5.5V17.5M16 10.5V17.5M20.5 5.5V17.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'code-01':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M16 18L22 12L16 6M8 6L2 12L8 18" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'edit-05':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M11 3.99998H6.8C5.11984 3.99998 4.27976 3.99998 3.63803 4.32696C3.07354 4.61458 2.6146 5.07353 2.32698 5.63801C2 6.27975 2 7.11983 2 8.79998V17.2C2 18.8801 2 19.7202 2.32698 20.362C2.6146 20.9264 3.07354 21.3854 3.63803 21.673C4.27976 22 5.11984 22 6.8 22H15.2C16.8802 22 17.7202 22 18.362 21.673C18.9265 21.3854 19.3854 20.9264 19.673 20.362C20 19.7202 20 18.8801 20 17.2V13M7.99997 16H9.67452C10.1637 16 10.4083 16 10.6385 15.9447C10.8425 15.8957 11.0376 15.8149 11.2166 15.7053C11.4184 15.5816 11.5914 15.4086 11.9373 15.0627L21.5 5.49998C22.3284 4.67156 22.3284 3.32841 21.5 2.49998C20.6716 1.67156 19.3284 1.67155 18.5 2.49998L8.93723 12.0627C8.59133 12.4086 8.41838 12.5816 8.29469 12.7834C8.18504 12.9624 8.10423 13.1574 8.05523 13.3615C7.99997 13.5917 7.99997 13.8363 7.99997 14.3255V16Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'share-05':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M21 6H17.8C16.1198 6 15.2798 6 14.638 6.32698C14.0735 6.6146 13.6146 7.07354 13.327 7.63803C13 8.27976 13 9.11984 13 10.8V12M21 6L18 3M21 6L18 9M10 3H7.8C6.11984 3 5.27976 3 4.63803 3.32698C4.07354 3.6146 3.6146 4.07354 3.32698 4.63803C3 5.27976 3 6.11984 3 7.8V16.2C3 17.8802 3 18.7202 3.32698 19.362C3.6146 19.9265 4.07354 20.3854 4.63803 20.673C5.27976 21 6.11984 21 7.8 21H16.2C17.8802 21 18.7202 21 19.362 20.673C19.9265 20.3854 20.3854 19.9265 20.673 19.362C21 18.7202 21 17.8802 21 16.2V14" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'settings-01':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M18.7273 14.7273C18.6063 15.0015 18.5702 15.3056 18.6236 15.6005C18.6771 15.8954 18.8177 16.1676 19.0273 16.3818L19.0818 16.4364C19.2509 16.6052 19.385 16.8057 19.4765 17.0265C19.568 17.2472 19.6151 17.4838 19.6151 17.7227C19.6151 17.9617 19.568 18.1983 19.4765 18.419C19.385 18.6397 19.2509 18.8402 19.0818 19.0091C18.913 19.1781 18.7124 19.3122 18.4917 19.4037C18.271 19.4952 18.0344 19.5423 17.7955 19.5423C17.5565 19.5423 17.3199 19.4952 17.0992 19.4037C16.8785 19.3122 16.678 19.1781 16.5091 19.0091L16.4545 18.9545C16.2403 18.745 15.9682 18.6044 15.6733 18.5509C15.3784 18.4974 15.0742 18.5335 14.8 18.6545C14.5311 18.7698 14.3018 18.9611 14.1403 19.205C13.9788 19.4489 13.8921 19.7347 13.8909 20.0273V20.1818C13.8909 20.664 13.6994 21.1265 13.3584 21.4675C13.0174 21.8084 12.5549 22 12.0727 22C11.5905 22 11.1281 21.8084 10.7871 21.4675C10.4461 21.1265 10.2545 20.664 10.2545 20.1818V20.1C10.2475 19.7991 10.1501 19.5073 9.97501 19.2625C9.79991 19.0176 9.55521 18.8312 9.27273 18.7273C8.99853 18.6063 8.69437 18.5702 8.39947 18.6236C8.10456 18.6771 7.83244 18.8177 7.61818 19.0273L7.56364 19.0818C7.39478 19.2509 7.19425 19.385 6.97353 19.4765C6.7528 19.568 6.51621 19.6151 6.27727 19.6151C6.03834 19.6151 5.80174 19.568 5.58102 19.4765C5.36029 19.385 5.15977 19.2509 4.99091 19.0818C4.82186 18.913 4.68775 18.7124 4.59626 18.4917C4.50476 18.271 4.45766 18.0344 4.45766 17.7955C4.45766 17.5565 4.50476 17.3199 4.59626 17.0992C4.68775 16.8785 4.82186 16.678 4.99091 16.5091L5.04545 16.4545C5.25503 16.2403 5.39562 15.9682 5.4491 15.6733C5.50257 15.3784 5.46647 15.0742 5.34545 14.8C5.23022 14.5311 5.03887 14.3018 4.79497 14.1403C4.55107 13.9788 4.26526 13.8921 3.97273 13.8909H3.81818C3.33597 13.8909 2.87351 13.6994 2.53253 13.3584C2.19156 13.0174 2 12.5549 2 12.0727C2 11.5905 2.19156 11.1281 2.53253 10.7871C2.87351 10.4461 3.33597 10.2545 3.81818 10.2545H3.9C4.2009 10.2475 4.49273 10.1501 4.73754 9.97501C4.98236 9.79991 5.16883 9.55521 5.27273 9.27273C5.39374 8.99853 5.42984 8.69437 5.37637 8.39947C5.3229 8.10456 5.18231 7.83244 4.97273 7.61818L4.91818 7.56364C4.74913 7.39478 4.61503 7.19425 4.52353 6.97353C4.43203 6.7528 4.38493 6.51621 4.38493 6.27727C4.38493 6.03834 4.43203 5.80174 4.52353 5.58102C4.61503 5.36029 4.74913 5.15977 4.91818 4.99091C5.08704 4.82186 5.28757 4.68775 5.50829 4.59626C5.72901 4.50476 5.96561 4.45766 6.20455 4.45766C6.44348 4.45766 6.68008 4.50476 6.9008 4.59626C7.12152 4.68775 7.32205 4.82186 7.49091 4.99091L7.54545 5.04545C7.75971 5.25503 8.03183 5.39562 8.32674 5.4491C8.62164 5.50257 8.9258 5.46647 9.2 5.34545H9.27273C9.54161 5.23022 9.77093 5.03887 9.93245 4.79497C10.094 4.55107 10.1807 4.26526 10.1818 3.97273V3.81818C10.1818 3.33597 10.3734 2.87351 10.7144 2.53253C11.0553 2.19156 11.5178 2 12 2C12.4822 2 12.9447 2.19156 13.2856 2.53253C13.6266 2.87351 13.8182 3.33597 13.8182 3.81818V3.9C13.8193 4.19253 13.906 4.47834 14.0676 4.72224C14.2291 4.96614 14.4584 5.15749 14.7273 5.27273C15.0015 5.39374 15.3056 5.42984 15.6005 5.37637C15.8954 5.3229 16.1676 5.18231 16.3818 4.97273L16.4364 4.91818C16.6052 4.74913 16.8057 4.61503 17.0265 4.52353C17.2472 4.43203 17.4838 4.38493 17.7227 4.38493C17.9617 4.38493 18.1983 4.43203 18.419 4.52353C18.6397 4.61503 18.8402 4.74913 19.0091 4.91818C19.1781 5.08704 19.3122 5.28757 19.4037 5.50829C19.4952 5.72901 19.5423 5.96561 19.5423 6.20455C19.5423 6.44348 19.4952 6.68008 19.4037 6.9008C19.3122 7.12152 19.1781 7.32205 19.0091 7.49091L18.9545 7.54545C18.745 7.75971 18.6044 8.03183 18.5509 8.32674C18.4974 8.62164 18.5335 8.9258 18.6545 9.2V9.27273C18.7698 9.54161 18.9611 9.77093 19.205 9.93245C19.4489 10.094 19.7347 10.1807 20.0273 10.1818H20.1818C20.664 10.1818 21.1265 10.3734 21.4675 10.7144C21.8084 11.0553 22 11.5178 22 12C22 12.4822 21.8084 12.9447 21.4675 13.2856C21.1265 13.6266 20.664 13.8182 20.1818 13.8182H20.1C19.8075 13.8193 19.5217 13.906 19.2778 14.0676C19.0339 14.2291 18.8425 14.4584 18.7273 14.7273Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'file-code-01':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M14 2.26953V6.40007C14 6.96012 14 7.24015 14.109 7.45406C14.2049 7.64222 14.3578 7.7952 14.546 7.89108C14.7599 8.00007 15.0399 8.00007 15.6 8.00007H19.7305M14 17.5L16.5 15L14 12.5M10 12.5L7.5 15L10 17.5M20 9.98822V17.2C20 18.8802 20 19.7202 19.673 20.362C19.3854 20.9265 18.9265 21.3854 18.362 21.673C17.7202 22 16.8802 22 15.2 22H8.8C7.11984 22 6.27976 22 5.63803 21.673C5.07354 21.3854 4.6146 20.9265 4.32698 20.362C4 19.7202 4 18.8802 4 17.2V6.8C4 5.11984 4 4.27976 4.32698 3.63803C4.6146 3.07354 5.07354 2.6146 5.63803 2.32698C6.27976 2 7.11984 2 8.8 2H12.0118C12.7455 2 13.1124 2 13.4577 2.08289C13.7638 2.15638 14.0564 2.27759 14.3249 2.44208C14.6276 2.6276 14.887 2.88703 15.4059 3.40589L18.5941 6.59411C19.113 7.11297 19.3724 7.3724 19.5579 7.67515C19.7224 7.94356 19.8436 8.2362 19.9171 8.5423C20 8.88757 20 9.25445 20 9.98822Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'file-code-02':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
<path d="M5 18.5C5 18.9644 5 19.1966 5.02567 19.3916C5.2029 20.7378 6.26222 21.7971 7.60842 21.9743C7.80337 22 8.03558 22 8.5 22H16.2C17.8802 22 18.7202 22 19.362 21.673C19.9265 21.3854 20.3854 20.9265 20.673 20.362C21 19.7202 21 18.8802 21 17.2V9.98822C21 9.25445 21 8.88757 20.9171 8.5423C20.8436 8.2362 20.7224 7.94356 20.5579 7.67515C20.3724 7.3724 20.113 7.11296 19.5941 6.59411L16.4059 3.40589C15.887 2.88703 15.6276 2.6276 15.3249 2.44208C15.0564 2.27759 14.7638 2.15638 14.4577 2.08289C14.1124 2 13.7455 2 13.0118 2H8.5C8.03558 2 7.80337 2 7.60842 2.02567C6.26222 2.2029 5.2029 3.26222 5.02567 4.60842C5 4.80337 5 5.03558 5 5.5M9 14.5L11.5 12L9 9.5M5 9.5L2.5 12L5 14.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'trash-01':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M16 6V5.2C16 4.0799 16 3.51984 15.782 3.09202C15.5903 2.71569 15.2843 2.40973 14.908 2.21799C14.4802 2 13.9201 2 12.8 2H11.2C10.0799 2 9.51984 2 9.09202 2.21799C8.71569 2.40973 8.40973 2.71569 8.21799 3.09202C8 3.51984 8 4.0799 8 5.2V6M10 11.5V16.5M14 11.5V16.5M3 6H21M19 6V17.2C19 18.8802 19 19.7202 18.673 20.362C18.3854 20.9265 17.9265 21.3854 17.362 21.673C16.7202 22 15.8802 22 14.2 22H9.8C8.11984 22 7.27976 22 6.63803 21.673C6.07354 21.3854 5.6146 20.9265 5.32698 20.362C5 19.7202 5 18.8802 5 17.2V6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'trash-03':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M9 3H15M3 6H21M19 6L18.2987 16.5193C18.1935 18.0975 18.1409 18.8867 17.8 19.485C17.4999 20.0118 17.0472 20.4353 16.5017 20.6997C15.882 21 15.0911 21 13.5093 21H10.4907C8.90891 21 8.11803 21 7.49834 20.6997C6.95276 20.4353 6.50009 20.0118 6.19998 19.485C5.85911 18.8867 5.8065 18.0975 5.70129 16.5193L5 6M10 10.5V15.5M14 10.5V15.5" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					</svg>
					';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'grid-01':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M8.4 3H4.6C4.03995 3 3.75992 3 3.54601 3.10899C3.35785 3.20487 3.20487 3.35785 3.10899 3.54601C3 3.75992 3 4.03995 3 4.6V8.4C3 8.96005 3 9.24008 3.10899 9.45399C3.20487 9.64215 3.35785 9.79513 3.54601 9.89101C3.75992 10 4.03995 10 4.6 10H8.4C8.96005 10 9.24008 10 9.45399 9.89101C9.64215 9.79513 9.79513 9.64215 9.89101 9.45399C10 9.24008 10 8.96005 10 8.4V4.6C10 4.03995 10 3.75992 9.89101 3.54601C9.79513 3.35785 9.64215 3.20487 9.45399 3.10899C9.24008 3 8.96005 3 8.4 3Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M19.4 3H15.6C15.0399 3 14.7599 3 14.546 3.10899C14.3578 3.20487 14.2049 3.35785 14.109 3.54601C14 3.75992 14 4.03995 14 4.6V8.4C14 8.96005 14 9.24008 14.109 9.45399C14.2049 9.64215 14.3578 9.79513 14.546 9.89101C14.7599 10 15.0399 10 15.6 10H19.4C19.9601 10 20.2401 10 20.454 9.89101C20.6422 9.79513 20.7951 9.64215 20.891 9.45399C21 9.24008 21 8.96005 21 8.4V4.6C21 4.03995 21 3.75992 20.891 3.54601C20.7951 3.35785 20.6422 3.20487 20.454 3.10899C20.2401 3 19.9601 3 19.4 3Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M19.4 14H15.6C15.0399 14 14.7599 14 14.546 14.109C14.3578 14.2049 14.2049 14.3578 14.109 14.546C14 14.7599 14 15.0399 14 15.6V19.4C14 19.9601 14 20.2401 14.109 20.454C14.2049 20.6422 14.3578 20.7951 14.546 20.891C14.7599 21 15.0399 21 15.6 21H19.4C19.9601 21 20.2401 21 20.454 20.891C20.6422 20.7951 20.7951 20.6422 20.891 20.454C21 20.2401 21 19.9601 21 19.4V15.6C21 15.0399 21 14.7599 20.891 14.546C20.7951 14.3578 20.6422 14.2049 20.454 14.109C20.2401 14 19.9601 14 19.4 14Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				<path d="M8.4 14H4.6C4.03995 14 3.75992 14 3.54601 14.109C3.35785 14.2049 3.20487 14.3578 3.10899 14.546C3 14.7599 3 15.0399 3 15.6V19.4C3 19.9601 3 20.2401 3.10899 20.454C3.20487 20.6422 3.35785 20.7951 3.54601 20.891C3.75992 21 4.03995 21 4.6 21H8.4C8.96005 21 9.24008 21 9.45399 20.891C9.64215 20.7951 9.79513 20.6422 9.89101 20.454C10 20.2401 10 19.9601 10 19.4V15.6C10 15.0399 10 14.7599 9.89101 14.546C9.79513 14.3578 9.64215 14.2049 9.45399 14.109C9.24008 14 8.96005 14 8.4 14Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'chevron-up':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M18 15L12 9L6 15" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'chevron-down':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M6 9L12 15L18 9" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'chevron-left':
				$xml = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M15 18L9 12L15 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'chevron-left-double':
				$xml = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M18 17L13 12L18 7M11 17L6 12L11 7" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'chevron-right':
				$xml = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M9 18L15 12L9 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'chevron-right-double':
				$xml = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M6 17L11 12L6 7M13 17L18 12L13 7" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'arrow-up-right':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M7 17L17 7M17 7H7M17 7V17" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'plus':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M12 5V19M5 12H19" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'check-circle-broken':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M22 11.0857V12.0057C21.9988 14.1621 21.3005 16.2604 20.0093 17.9875C18.7182 19.7147 16.9033 20.9782 14.8354 21.5896C12.7674 22.201 10.5573 22.1276 8.53447 21.3803C6.51168 20.633 4.78465 19.2518 3.61096 17.4428C2.43727 15.6338 1.87979 13.4938 2.02168 11.342C2.16356 9.19029 2.99721 7.14205 4.39828 5.5028C5.79935 3.86354 7.69279 2.72111 9.79619 2.24587C11.8996 1.77063 14.1003 1.98806 16.07 2.86572M22 4L12 14.01L9 11.01" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'log-in-04':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M12 8L16 12M16 12L12 16M16 12H3M3.33782 7C5.06687 4.01099 8.29859 2 12 2C17.5228 2 22 6.47715 22 12C22 17.5228 17.5228 22 12 22C8.29859 22 5.06687 19.989 3.33782 17" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'log-out-04':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M18 8L22 12M22 12L18 16M22 12H9M15 4.20404C13.7252 3.43827 12.2452 3 10.6667 3C5.8802 3 2 7.02944 2 12C2 16.9706 5.8802 21 10.6667 21C12.2452 21 13.7252 20.5617 15 19.796" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			case 'clipboard-icon-svg':
				$xml = '<?xml version="1.0" encoding="utf-8"?>
				<svg version="1.1" id="Layer_3" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
					 viewBox="0 0 20 20" style="enable-background:new 0 0 20 20;" xml:space="preserve">
				<path d="M14,18H8c-1.1,0-2-0.9-2-2V7c0-1.1,0.9-2,2-2h6c1.1,0,2,0.9,2,2v9C16,17.1,15.1,18,14,18z M8,7v9h6V7H8z"/>
				<path d="M5,4h6V2H5C3.9,2,3,2.9,3,4v9h2V4z"/>
				</svg>';

				$allowed_html = array(
					'svg'  => array(
						'version' => array(),
						'id'      => array(),
						'xmlns'   => array(),
						'x'       => array(),
						'y'       => array(),
						'viewbox' => array(),
						'style'   => array(),
					),
					'path' => array(
						'd' => array(),
					),
				);

				break;

			case 'x':
				$xml = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M17 7L7 17M7 7L17 17" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'version' => array(),
						'id'      => array(),
						'xmlns'   => array(),
						'x'       => array(),
						'y'       => array(),
						'viewbox' => array(),
						'style'   => array(),
					),
					'path' => array(
						'd' => array(),
					),
				);

				break;

			case 'diamond-01':
				$xml = '<svg class="untitled-ui-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
				<path d="M2.49954 9H21.4995M9.99954 3L7.99954 9L11.9995 20.5L15.9995 9L13.9995 3M12.6141 20.2625L21.5727 9.51215C21.7246 9.32995 21.8005 9.23885 21.8295 9.13717C21.8551 9.04751 21.8551 8.95249 21.8295 8.86283C21.8005 8.76114 21.7246 8.67005 21.5727 8.48785L17.2394 3.28785C17.1512 3.18204 17.1072 3.12914 17.0531 3.09111C17.0052 3.05741 16.9518 3.03238 16.8953 3.01717C16.8314 3 16.7626 3 16.6248 3H7.37424C7.2365 3 7.16764 3 7.10382 3.01717C7.04728 3.03238 6.99385 3.05741 6.94596 3.09111C6.89192 3.12914 6.84783 3.18204 6.75966 3.28785L2.42633 8.48785C2.2745 8.67004 2.19858 8.76114 2.16957 8.86283C2.144 8.95249 2.144 9.04751 2.16957 9.13716C2.19858 9.23885 2.2745 9.32995 2.42633 9.51215L11.385 20.2625C11.596 20.5158 11.7015 20.6424 11.8279 20.6886C11.9387 20.7291 12.0603 20.7291 12.1712 20.6886C12.2975 20.6424 12.4031 20.5158 12.6141 20.2625Z" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
				</svg>
				';

				$allowed_html = array(
					'svg'  => array(
						'class'   => array(),
						'width'   => array(),
						'height'  => array(),
						'viewbox' => array(),
						'fill'    => array(),
						'xmlns'   => array(),
					),
					'path' => array(
						'd'               => array(),
						'stroke'          => array(),
						'stroke-width'    => array(),
						'stroke-linecap'  => array(),
						'stroke-linejoin' => array(),
					),
				);

				break;

			default:
				$xml = '';

				break;

		}

		echo wp_kses( $xml, $allowed_html );
	}

	/**
	 * Using $wpdb save the import log data in the "_log" database table.
	 *
	 * Note that the following fields are available in the database table:
	 *
	 * - log_id
	 * - date
	 * - operation
	 * - post_id
	 *
	 * @param array  $result Data resulted from the import process.
	 * @param string $file_name The file name of the imported file.
	 *
	 * @return int The number of rows affected by the query.
	 */
	public function save_log( $result, $file_name ) {

		$daimma_log_import_data = intval( get_option( $this->get( 'slug' ) . '_log_import_data' ), 10 );
		if ( 0 === $daimma_log_import_data ) {
			return 0;
		}

		global $wpdb;

		// Get the post title.
		$post_title = get_the_title( $result['post_id'] );

		// Get the post permalink.
		$post_permalink = get_the_permalink( $result['post_id'] );

		// Get the post edit link.
		$post_edit_link = get_edit_post_link( $result['post_id'], 'url' );

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$query_result = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$wpdb->prefix}daimma_log SET
                  date = %s,
                 file_name = %s,
                 post_id = %s,
                 post_title = %s,
                 post_permalink = %s,
                 post_edit_link = %s",
				current_time( 'mysql' ),
				$file_name,
				$result['post_id'],
				$post_title,
				$post_permalink,
				$post_edit_link
			)
		);

		return $query_result;
	}

	/**
	 * Save a dismissible notice in the "daimma_dismissible_notice_a" WordPress.
	 *
	 * @param string $data_a Data of the dismissible notice, including user_id, class and elements.
	 *
	 * @return void
	 */
	public function save_dismissible_notice( $data_a ) {

		$dismissible_notice = array(
			'user_id'  => get_current_user_id(),
			'class'    => $data_a['class'],
			'elements' => $data_a['elements'],
		);

		// Get the current option value.
		$dismissible_notice_a = get_option( 'daimma_dismissible_notice_a' );

		// If the option is not an array, initialize it as an array.
		if ( ! is_array( $dismissible_notice_a ) ) {
			$dismissible_notice_a = array();
		}

		// Add the dismissible notice to the array.
		$dismissible_notice_a[] = $dismissible_notice;

		// Save the dismissible notice in the "daimma_dismissible_notice_a" WordPress option.
		update_option( 'daimma_dismissible_notice_a', $dismissible_notice_a );
	}

	/**
	 * Display the dismissible notices stored in the "daimma_dismissible_notice_a" option.
	 *
	 * Note that the dismissible notice will be displayed only once to the user.
	 *
	 * The dismissable notice is first displayed (only to the same user with which has been generated) and then it is
	 * removed from the "daimma_dismissible_notice_a" option.
	 *
	 * @return void
	 */
	public function display_dismissible_notices() {

		$dismissible_notice_a = get_option( 'daimma_dismissible_notice_a' );

		// Iterate over the dismissible notices with the user id of the same user.
		if ( is_array( $dismissible_notice_a ) ) {
			foreach ( $dismissible_notice_a as $key => $dismissible_notice ) {

				// If the user id of the dismissible notice is the same as the current user id, display the message.
				if ( get_current_user_id() === $dismissible_notice['user_id'] ) {

					?>

					<div class="<?php echo esc_attr( $dismissible_notice['class'] ); ?> notice">
						<p>
							<?php

							foreach ( $dismissible_notice['elements'] as $element ) {

								switch ( $element['type'] ) {

									case 'text':
										echo esc_html( $element['text'] );
										break;

									case 'link':
										echo '<a href="' . esc_url( $element['url'] ) . '">' . esc_html( $element['text'] ) . '</a>';
										break;

								}
							}

							?>
						</p>
						<div class="notice-dismiss-button"><?php $this->echo_icon_svg( 'x' ); ?></div>
					</div>

					<?php

					// Remove the echoed dismissible notice from the "daimma_dismissible_notice_a" WordPress option.
					unset( $dismissible_notice_a[ $key ] );

					update_option( 'daimma_dismissible_notice_a', $dismissible_notice_a );

				}
			}
		}
	}

	/**
	 * Sanitize the data of an uploaded file.
	 *
	 * @param array $file The array with the data of the uploaded file.
	 *
	 * @return array
	 */
	public function sanitize_uploaded_file( $file ) {

		return array(
			'name'     => sanitize_text_field( $file['name'] ),
			'type'     => $file['tmp_name'],
			'tmp_name' => $file['tmp_name'],
			'error'    => intval( $file['error'], 10 ),
			'size'     => intval( $file['size'], 10 ),
		);
	}

	/**
	 * Given an array with the information related to the uploaded Markdown file
	 *  (markdown|mdown|mkdn|md|mkd|mdwn|mdtxt|mdtext|text|txt) the content of the markdown file is returned.
	 *
	 * @param array $file_info An array with the information related to the uploaded file.
	 *
	 * @return false|string
	 */
	public function get_markdown_file_content( $file_info ) {

		$file_name = $file_info['name'];

		// Verify the extension.
		if ( preg_match( '/^.+\.(markdown|mdown|mkdn|md|mkd|mdwn|mdtxt|mdtext|text|txt)$/', $file_name, $matches ) === 1 ) {

			global $wp_filesystem;
			require_once ABSPATH . '/wp-admin/includes/file.php';
			WP_Filesystem();

			// Get the file content.
			$file_content = $wp_filesystem->get_contents( $file_info['tmp_name'] );

			return $file_content;

		} else {

			return false;

		}
	}

	/**
	 * Get the document/post title from the file name.
	 *
	 * @param string $filename The name of the file.
	 *
	 * @return string The title of the document/post.
	 */
	public function get_title_from_file_name( $filename ) {

		// Remove the markdown extension from the filename.
		preg_match(
			'/.*\.(markdown|mdown|mkdn|md|mkd|mdwn|mdtxt|mdtext|text|txt)$/',
			$filename,
			$matches,
			PREG_OFFSET_CAPTURE
		);

		if ( isset( $matches[1][1] ) ) {
			$title = substr( $filename, 0, $matches[1][1] - 1 );
		} else {
			$title = $filename;
		}

		return $title;
	}

	/**
	 * Generate the HTML content from the Markdown content using the Markdown parser selected by the administrator using
	 * the plugin options.
	 *
	 * @param string $markdown_content The Markdown content.
	 *
	 * @return string The HTML content.
	 */
	public function get_html_from_markdown( $markdown_content ) {

		$markdown_parser = get_option( $this->get( 'slug' ) . '_markdown_parser' );
		switch ( $markdown_parser ) {

			case 'parsedown':
				$daimma_parsedown = new Parsedown();
				$html_content     = $daimma_parsedown->text( $markdown_content );

				break;

			case 'parsedown_extra':
				$daimma_parsedown_extra = new ParsedownExtra();
				$html_content           = $daimma_parsedown_extra->text( $markdown_content );

				break;

			case 'cebe_markdown':
				$daimma_cebe_markdown = new \cebe\markdown\Markdown();

				if ( 1 === intval( get_option( $this->get( 'slug' ) . '_cebe_markdown_html5' ), 10 ) ) {
					$daimma_cebe_markdown->html5 = true;
				}

				if ( 1 === intval(
					get_option( $this->get( 'slug' ) . '_cebe_markdown_keep_list_start_number' ),
					10
				) ) {

					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Property name is correct.
					$daimma_cebe_markdown->keepListStartNumber = true;
				}

				$html_content = $daimma_cebe_markdown->parse( $markdown_content );

				break;

			case 'cebe_markdown_github_flavored':
				$daimma_cebe_markdown_github_flavored = new \cebe\markdown\GithubMarkdown();

				if ( 1 === intval( get_option( $this->get( 'slug' ) . '_cebe_markdown_html5' ), 10 ) ) {
					$daimma_cebe_markdown_github_flavored->html5 = true;
				}

				if ( 1 === intval(
					get_option( $this->get( 'slug' ) . '_cebe_markdown_keep_list_start_number' ),
					10
				) ) {
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Property name is correct.
					$daimma_cebe_markdown_github_flavored->keepListStartNumber = true;
				}

				if ( 1 === intval(
					get_option( $this->get( 'slug' ) . '_cebe_markdown_enable_new_lines' ),
					10
				) ) {
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Property name is correct.
					$daimma_cebe_markdown_github_flavored->enableNewlines = true;
				}

				$html_content = $daimma_cebe_markdown_github_flavored->parse( $markdown_content );

				break;

			case 'cebe_markdown_extra':
				$daimma_cebe_markdown_extra = new \cebe\markdown\MarkdownExtra();

				if ( 1 === intval( get_option( $this->get( 'slug' ) . '_cebe_markdown_html5' ), 10 ) ) {
					$daimma_cebe_markdown_extra->html5 = true;
				}

				if ( 1 === intval(
					get_option( $this->get( 'slug' ) . '_cebe_markdown_keep_list_start_number' ),
					10
				) ) {
					// phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase -- Property name is correct.
					$daimma_cebe_markdown_extra->keepListStartNumber = true;
				}

				$html_content = $daimma_cebe_markdown_extra->parse( $markdown_content );

				break;

			case 'commonmark':
			case 'commonmark_github':
				// Renderer related options.
				$commonmark_block_separator = "\n";
				$commonmark_inner_separator = "\n";
				$commonmark_soft_break      = "\n";

				// CommonMark related options.
				$commonmark_enable_em              = 1 === intval( get_option( $this->get( 'slug' ) . '_commonmark_enable_em' ), 10 ) ? true : false;
				$commonmark_enable_strong          = 1 === intval( get_option( $this->get( 'slug' ) . '_commonmark_enable_strong' ), 10 ) ? true : false;
				$commonmark_use_asterisk           = 1 === intval( get_option( $this->get( 'slug' ) . '_commonmark_use_asterisk' ), 10 ) ? true : false;
				$commonmark_use_underscore         = 1 === intval( get_option( $this->get( 'slug' ) . '_commonmark_use_underscore' ), 10 ) ? true : false;
				$commonmark_unordered_list_markers = get_option( $this->get( 'slug' ) . '_commonmark_unordered_list_markers' );

				// HTML input related options.
				$commonmark_html_input         = get_option( $this->get( 'slug' ) . '_commonmark_html_input' );
				$commonmark_allow_unsafe_links = 1 === intval( get_option( $this->get( 'slug' ) . '_commonmark_allow_unsafe_links' ), 10 ) ? true : false;

				$commonmark_max_nesting_level = intval( get_option( $this->get( 'slug' ) . '_commonmark_max_nesting_level' ), 10 );

				// Create the configuration array.
				$config = array(
					'renderer'           => array(
						'block_separator' => $commonmark_block_separator,
						'inner_separator' => $commonmark_inner_separator,
						'soft_break'      => $commonmark_soft_break,
					),
					'commonmark'         => array(
						'enable_em'              => $commonmark_enable_em,
						'enable_strong'          => $commonmark_enable_strong,
						'use_asterisk'           => $commonmark_use_asterisk,
						'use_underscore'         => $commonmark_use_underscore,
						'unordered_list_markers' => $commonmark_unordered_list_markers,
					),
					'html_input'         => $commonmark_html_input,
					'allow_unsafe_links' => $commonmark_allow_unsafe_links,
					'max_nesting_level'  => $commonmark_max_nesting_level,
				);

				// Create the CommonMark converter instance.
				if ( 'commonmark' === $markdown_parser ) {
					$daimma_commonmark = new CommonMarkConverter( $config );
				} else {
					$daimma_commonmark = new GithubFlavoredMarkdownConverter( $config );
				}

				// Get the result object.
				$result_obj = $daimma_commonmark->convert( $markdown_content );

				// Get the HTML content.
				$html_content = $result_obj->getContent();

		}

		return $html_content;
	}

	/**
	 * Create a new post with the provided title and content. The post type is the one selected by the administrator
	 * using the plugin options.
	 *
	 * @param string $title The post title.
	 * @param string $html_content The post content.
	 *
	 * @return array An array with the success status and the post id.
	 */
	public function create_new_post( $title, $html_content ) {

		// Create post object.
		$new_post = array(
			'post_title'   => $title,
			'post_content' => $html_content,
			'post_type'    => get_option( $this->get( 'slug' ) . '_import_post_type' ),
		);

		// Insert the post into the database.
		$new_post_id = wp_insert_post( $new_post );

		if ( 0 !== $new_post_id && ! is_wp_error( $new_post_id ) ) {

			$this->save_dismissible_notice(
				array(
					'elements' => array(
						array(
							'type' => 'text',
							'text' => __( 'The new post has been successfully created. You can edit it', 'import-markdown' ) . ' ',
						),
						array(
							'type' => 'link',
							'text' => __( 'here', 'import-markdown' ),
							'url'  => get_edit_post_link( $new_post_id ),
						),
						array(
							'type' => 'text',
							'text' => '.',
						),
					),
					'class'    => 'updated',
				)
			);

			$result = array(
				'success' => true,
				'post_id' => $new_post_id,
			);

		} else {

			$this->shared->save_dismissible_notice(
				array(
					'elements' => array(
						array(
							'type' => 'text',
							__( 'Unable to create the new post. An error occurred.', 'import-markdown' ),
						),
					),
					'class'    => 'error',
				)
			);

			$result = array(
				'success' => false,
				'post_id' => 0,
			);

		}

		return $result;
	}

	/**
	 * Iterates over all the records in the "_log" database table and update:
	 *
	 * - post_title
	 * - post_permalink
	 * - post_edit_link
	 */
	public function update_log() {

		wp_raise_memory_limit();

		global $wpdb;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$log_a = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}daimma_log", ARRAY_A );

		if ( is_array( $log_a ) && count( $log_a ) > 0 ) {

			foreach ( $log_a as $log ) {

				// Get the post title.
				$post_title = get_the_title( $log['post_id'] );

				// Get the post permalink.
				$post_permalink = get_the_permalink( $log['post_id'] );

				// Get the post edit link.
				$post_edit_link = get_edit_post_link( $log['post_id'], 'url' );

				// Update the record in the "_log" database table.

				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->query(
					$wpdb->prepare(
						"UPDATE {$wpdb->prefix}daimma_log SET 
                        post_title = %s,
                        post_permalink = %s,
                        post_edit_link = %s
						WHERE log_id = %d",
						$post_title,
						$post_permalink,
						$post_edit_link,
						$log['log_id']
					)
				);

			}
		}
	}

	/**
	 * Reset the plugin options.
	 *
	 * Set the initial value to all the plugin options.
	 */
	public function reset_plugin_options() {

		$options = $this->get( 'options' );
		foreach ( $options as $option_name => $default_option_value ) {

			/**
			 * If the option name is different from 'daimma_database_version' and 'daimma_options_version' reset
			 * the option.
			 */
			if ( 'daimma_database_version' !== $option_name && 'daimma_options_version' !== $option_name ) {
				update_option( $option_name, $default_option_value );
			}
		}
	}
}
