<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress_portfolio-db' );

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
define( 'AUTH_KEY',         'pMv<<kvZ;]@|_^3REWWyKh}`N:)3RiR]ap-$6$RV@%?b:or~EM{`xDAYkJXagEG<' );
define( 'SECURE_AUTH_KEY',  '5B|&X?VL;r|qp[,`-w=VZxpWg0b~fCrT-oc ?EHQ[H<_CrSH,ib!Y8_,QOtM=Hf~' );
define( 'LOGGED_IN_KEY',    't/@p3rORnB/wt.muZ0o4f`fF)6VmRxV<vZo] /$`T?P%=KxYqhZ:9&vg&D(R6rkn' );
define( 'NONCE_KEY',        '@$)RxUC6UZ_@[zoy[[qcohTp+BKQo<xlt5^W7Mz9p=g(0)^av{Cc[NEe38@mx6Ba' );
define( 'AUTH_SALT',        'M{s7I~wLbN-Ty~PvU>J?TY?2jI:=<[ig`otjyn_zt8m>99M^M:VVK`Gy2!0HvPJP' );
define( 'SECURE_AUTH_SALT', '*(tU8.|WeH75LOoUA2/KelmB*[FVJ&!#?W>9#8I; B&euuwbo*HTA#uaD5*w{E~6' );
define( 'LOGGED_IN_SALT',   'pP6[.2.m*1MJYJbz$#6as*bmd14/^OtXqE=M;K}jD35oG1?+4{[:cAlV)EroiH,b' );
define( 'NONCE_SALT',       'ML**V%T#;q<4&_@s#;A;)~4K$y!g0wP;FTraWc,DqFajUDG8~v?A/8Xayr IV@!N' );

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
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
