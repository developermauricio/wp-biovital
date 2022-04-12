<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'biovital' );

/** MySQL database username */
define( 'DB_USER', 'forge' );

/** MySQL database password */
define( 'DB_PASSWORD', 'Q2b2TQEDvfeD0jiIyF6R' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '{!Xkc$=SE%Q4~a3<K(XJJfa[iN88,S:!h&;S%}EVCVoco6#$;j`,&#@=Bgn>fME[' );
define( 'SECURE_AUTH_KEY',  '+rNNowxpqrW>qO T}:s{Ag2bm{;)$KtPT},3)M _b$ eJ;M#7#zNxU0E<%b_ ,~z' );
define( 'LOGGED_IN_KEY',    'wiL9hcE$Hpx9g3VYo[n%{Bq7$>n:z.r)TUgBCsQ-Lx^f{L=kUX;`q;`[2@U{UukQ' );
define( 'NONCE_KEY',        '$+dpHul$Nctb#f`i|3dIO/A?%`&[Ms^T2/KpU+|^Ip|;aY}u7sFQ/zK:13*eh(7!' );
define( 'AUTH_SALT',        'jQf:s`Hb*Zw,KV&T43onEO]=Qf$kO5#Hrj)Qlb^*wk76CnxCUwgzzHY;{eOHK#9U' );
define( 'SECURE_AUTH_SALT', 'J?)3C;)){/dU_0FDZq?@@)|f7=]/hFm*w*m)6tye.Cf`veIu9X/k^Kt 8+emBb{A' );
define( 'LOGGED_IN_SALT',   'IQT:/M+Qai&618g7OdqG_E{m+P5ou.O6Z^.L/DqM8WuxYYW-DxTlJ pj!x(TAYFC' );
define( 'NONCE_SALT',       '<u_:Ma MmXjIDGJwMmLKkQT=}g2#To4),^EcUQnay2u~2|X%y*#|ma<LSV+f* G(' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'bv_';

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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
