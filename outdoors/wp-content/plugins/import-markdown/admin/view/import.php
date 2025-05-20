<?php
/**
 * The file used to display the "Import" menu in the admin area.
 *
 * @package import-markdown
 */

$this->menu_elements->capability = get_option( $this->shared->get( 'slug' ) . '_import_menu_required_capability' );
$this->menu_elements->context    = null;
$this->menu_elements->display_menu_content();
