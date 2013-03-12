<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'db049594_start');

/** MySQL database username */
define('DB_USER', 'u049594_start');

/** MySQL database password */
define('DB_PASSWORD', '3BtaUKJDCQJ(vQM1');

/** MySQL hostname */
define('DB_HOST', 'dbint049594');

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
define('AUTH_KEY',         'aC-!&q{ad3.)H?WYj3JFA=X@/o9|LX@TBrF7W6 K3OAA~hnpm1bS7_|J)@d`ve-B');
define('SECURE_AUTH_KEY',  'E+CL=9[z&XOh4;e|$qWXd[v}zc}[WFh#FwTB/(Y?D A)fM[6U@i+=y%.8$^L@xH2');
define('LOGGED_IN_KEY',    'cfX_Gp LQAtzC;q!dp=A4RPv&SVa_QA.y4sci7H{V7]{Cdw-qt6ru<C@Q!OF *+Z');
define('NONCE_KEY',        '2Kq|O$B}iU0|hq9O>O7f7`&Bf@x2tM,-/C>^_zQC/+T+|4@Idh< CrhAr+ac5(<y');
define('AUTH_SALT',        '|@&byHM_O(GRg7@JCq?D7|fJlGuY aVh+Jkm}!Vm!Dt-D2r)*o]S3_+IhMo|9COy');
define('SECURE_AUTH_SALT', 'PFV6a^RmpTa:W@1];9{vd%Nx-s=Y4/VNfCa+*|){[br)k@t:,-%$_S`|;wszAe(M');
define('LOGGED_IN_SALT',   ',GjWz/|)H]>p8$r@:M(|1(p:d)T|latTw+W+4jf.dZ^~nh `#.&sKoxw0l>`_^0`');
define('NONCE_SALT',       '+)..(8QF5}z/E<hY.s|u4%Y<msFE VXC+~IWrMoFF7l=E0,D{OVB8}=-RK{QT(bv');

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
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', 'nl_NL');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
