<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'myhope_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '_D }bT`A}!rRu#s.E7B&ZNm:x4iH[o.g9{rF9@`->,;V)RU {2!`!NwE%B J65/g' );
define( 'SECURE_AUTH_KEY',  'y];}_jUpg+0_(MPKXft foEj}@ $53uTZM}]:[$0e EDggjU3ewm]IIVbJ)JURRX' );
define( 'LOGGED_IN_KEY',    '5m`>5CPD0<xVg3>[BXtK`WA/H&Ft6ct%{%>g$MIQk%}b{~>cIJ~g;`Dr[1f:ryH2' );
define( 'NONCE_KEY',        'NV#KNl~3{UBF/)N.}{2cR33`d-0V1lUwF_:V_NksiSWg(s_=bY_Yhr,(BS9&f[zY' );
define( 'AUTH_SALT',        'o>@v+|tv>&Ga~SF+a2]b$#xX[,&^CH(73yb&DvkQ8A!hOin.g <h}r?7DINgsNNS' );
define( 'SECURE_AUTH_SALT', '#Viqi0q):4&x2/wH;P+*9mvlWo*]CBoE?;JLkkQ&z2:dT}`eo6nv%FwQIOv(/>Bz' );
define( 'LOGGED_IN_SALT',   '3H[VYV5(&)h,WnME@1_gP5quzt!A+^|V7B[}IEeLdhd)^L=3^-2pDyJoDW:]Vu1#' );
define( 'NONCE_SALT',       't~f|H02-zD&Qm1Ogibyg4t3OBr)=/G54-JU!=Z4cx-J<D3O^:esSsp0Sws<FoZTN' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
