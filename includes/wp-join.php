<?php

// ** MySQL settings ** //
define('DB_NAME', 'ae_main');    // The name of the database
define('DB_USER', '0hyuZ0ZYroZL');     // Your MySQL username
define('DB_PASSWORD', 'X9f7KRSkTjIl'); // ...and password
define('DB_HOST', 'localhost');    // 99% chance you won't need to change this value
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

// Change SECRET_KEY to a unique phrase.  You won't have to remember it later,
// so make it long and complicated.  You can visit https://www.grc.com/passwords.htm
// to get a phrase generated for you, or just make something up.
define('SECRET_KEY', '~HBEi3+}mPis}(veBlu2H3=NU;*v%BD]=\'Jm|8=;%Xj0(.kdPM}b1AUXbgR%]_)r'); // Change this to a unique phrase.

/**#@+
 * Authentication Unique Keys.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link http://api.wordpress.org/secret-key/1.1/ WordPress.org secret-key service}
 *
 * @since 2.6.0
 */
define('AUTH_KEY', 'Xm>VN(Nmt<>{6t+FuT!9;==r&|\\J%v+}(GH[8AT5tz|\'Ccr?ieF| $YV6n6\'Aw]3');
define('SECURE_AUTH_KEY', '28\'Uh3vjY^$#>}?7+f#>?:2a!#[}gQZND_jX6LlsH&|XH\"H4Z.qtl,mp$yG?*7C7');
define('LOGGED_IN_KEY', 'y~\'sc}v&O!8&Ajt\\$LgqA)%P[#Lx8P`#[}sX<n&n2b|dF._OL{?9<VjzZ;x]M{Q\\');
/**#@-*/

// You can have multiple installations in one database if you give each a unique prefix
//$table_prefix  = 'wptrunk_';   // Only numbers, letters, and underscores please!
global $table_prefix;
$table_prefix  = 'kpl05cw_';   // Only numbers, letters, and underscores please!

// Change this to localize WordPress.  A corresponding MO file for the
// chosen language must be installed to wp-content/languages.
// For example, install de.mo to wp-content/languages and set WPLANG to 'de'
// to enable German language support.
define ('WPLANG', 'en-GB');

define('WP_POST_REVISIONS','0');

define( 'WP_SHOW_ADMIN_BAR', false);

define('FS_METHOD','direct');

?>
