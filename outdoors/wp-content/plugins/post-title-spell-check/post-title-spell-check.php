<?php
/**
 * Plugin Name:  Post Title Spell Check
 * Plugin URI:   https://github.com/FlagshipWP/post-title-spell-check/
 * Description:  Adds spell check functionality to the WordPress post title input field.
 * Version:      1.0.4
 * Author:       Flagship
 * Author URI:   http://flagshiwp.com
 * License:      GPL-2.0+
 * License URI:  http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Git URI:           https://github.com/FlagshipWP/post-title-spell-check
 * GitHub Plugin URI: https://github.com/FlagshipWP/post-title-spell-check
 * GitHub Branch:     master
 *
 * @package  PostTitleSpellCheck
 * @category Core
 * @author   Robert Neu
 * @author   Brady Vercher
 * @version  1.0.4
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'edit_form_after_title', 'flagship_spell_check_post_title' );
/**
 * Output a JavaScript snippet to enable spell checking after the post title
 * input on the WordPress admin edit screen.
 *
 * @since    1.0.1
 * @uses     post_type_supports()
 * @uses     get_post_type()
 * @return   null if the post type doesn't support titles.
 */
function flagship_spell_check_post_title() {
	// Do nothing if the current post type doesn't support a title.
	if ( ! post_type_supports( get_post_type(), 'title' ) ) {
		return;
	}
	echo "<script>document.getElementById( 'title' ).spellcheck = true;</script>";
}
