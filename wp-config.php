<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME','wordpressblog');

/** MySQL database username */
define('DB_USER','wordpress');

/** MySQL database password */
define('DB_PASSWORD','');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY','ghhxQ+lyEVh0qoyIyDGjIJveOCMqYzVhTAOEp/4OzDcL1n1QhBmWI7kDGVI+uD+j');
define('SECURE_AUTH_KEY','NSrKA0X0FR8/IZFAoLJHD0XT/wgEM2DI10sv4uFUT1bKz0DAv6lfTpZWyxHbZhsa');
define('LOGGED_IN_KEY','nhn4JCmocVT7i9NhvfB6Ws0x2nf9X48ydD84rzanFHmqLYC0yaj9T/+9hqJ3ZK8I');
define('NONCE_KEY','P+ecDEZoei8Y5aNA5tFQE9/VfSpjF4rlT6tbyFFo8QXzX9YW/uKmx1ZvcykqbpH9');
define('AUTH_SALT','9XkSc+sQtxtKJkEDOtF9MUmFfEX4ehpe4TROwMFJ4Y9Q7jB6wQ8LELcs5XlwB0Rd');
define('SECURE_AUTH_SALT','pkh9tD/xyqDsDEcgZpMCaCmxWh9TR9/bWED6eQf7kMHsROMC/Tl6Fw8s9vtzdHvz');
define('LOGGED_IN_SALT','t8gHl+FkJebY6gmb6TSchOVwXCAPDC6tOY4Qm476CTNLASDXO76NCa6JHmelOzOB');
define('NONCE_SALT','xCdCM0v+gTWtg4qy1W78Yzv8Dc245F77+gBmV9vPjEyT1+aDPmKIpNMKnZgdmvhK');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/language-selector/languages. For example, install
 * de_DE.mo to wp-content/language-selector/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');
define('WP_LANG_DIR', dirname(__FILE__) . '/wp-content/plugins/language-selector/languages');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */
$pageURL = 'http';
if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
$pageURL .= "://";
if ($_SERVER["SERVER_PORT"] != "80") {
	$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"];
} else {
	$pageURL .= $_SERVER["SERVER_NAME"];
}

$virtual_host_file = file_get_contents("/etc/httpd/sites-enabled-user/httpd-vhost.conf-user");
if (preg_match('/ServerName '.$_SERVER["SERVER_NAME"].'/', $virtual_host_file)) {
	define('WP_SITEURL', $pageURL);
} else {
        define('WP_SITEURL', $pageURL.'/wordpress');
}

if (!defined('SYNOWORDPRESS'))
	define('SYNOWORDPRESS', 'Synology Inc.');

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
require_once(ABSPATH . 'syno-misc.php');
