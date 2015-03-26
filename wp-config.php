<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
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
define('DB_NAME', 'jknsapc');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         '2VWg}a4>0g%3|/{* 5gnl*V{xS#S.N*!FX*YVmG9%js!@rsiWHjkdj%Irr:Nz_Z{');
define('SECURE_AUTH_KEY',  '`KENH)f[B5Y!#o2Dy1X51/lXeY !)yrShZI[!Zw3(1h9F)gs;~B+QBFBjy)--~qO');
define('LOGGED_IN_KEY',    'Mkhf>M=q9czKJPJ!foGdm9C8d`:A7X)s~~[`nwdc(S0K~vb.p|9(SW9n},L=2}gK');
define('NONCE_KEY',        'Xfq])I]@-I5MU&]t*jp7M6vlVC;U;4*KZ.uVSBJO&gh.e%5OrdzSgh,L$h_~mHlI');
define('AUTH_SALT',        '[0PYD$;6#Qvtd+B~pE~4R#b<65-Fj!=v>qzpkl [eB&Z?.yETVk+PJVp3<:g>Tgz');
define('SECURE_AUTH_SALT', '!1N@.nq[Cs42zH~r..s} DY@`/_mU<n5TgO3lmHpgUjdNN4x-ZGa@[MU[Aw#{KX/');
define('LOGGED_IN_SALT',   '&3TNx_y6]lmtBFAHjfd/J@@91Z>|(.rQRy^59i65VBo50~Ymo{:beAXU^1oB_qNi');
define('NONCE_SALT',       'cHZ{h;C7m=H-HD!9A|4D:lf4q8RTmJnh@lra58cqN|W/?V-9a77?pYZHw9S =y>L');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

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
