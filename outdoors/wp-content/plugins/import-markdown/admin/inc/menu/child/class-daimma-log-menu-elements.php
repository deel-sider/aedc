<?php
/**
 * Class used to implement the back-end functionalities of the "Dashboard" menu.
 *
 * @package import-markdown
 */

/**
 * Class used to implement the back-end functionalities of the "Import Log" menu.
 */
class Daimma_Log_Menu_Elements extends Daimma_Menu_Elements {

	/**
	 * Daimma_Log_Menu_Elements constructor.
	 *
	 * @param object $shared The shared class.
	 * @param string $page_query_param The page query parameter.
	 * @param string $config The config parameter.
	 */
	public function __construct( $shared, $page_query_param, $config ) {

		parent::__construct( $shared, $page_query_param, $config );

		$this->menu_slug      = 'log';
		$this->slug_plural    = 'log';
		$this->label_singular = __( 'Log', 'import-markdown' );
		$this->label_plural   = __( 'Log', 'import-markdown' );
	}

	/**
	 * Display the content of the body.
	 *
	 * @return void
	 */
	public function display_custom_content() {

		?>

		<div id="react-root"></div>

		<?php
	}
}
