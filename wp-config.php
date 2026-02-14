<?php
define( 'WP_CACHE', true );

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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u733509243_NGHzE' );

/** Database username */
define( 'DB_USER', 'u733509243_LwGZl' );

/** Database password */
define( 'DB_PASSWORD', 'T8BSAlCoei' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          '=S2(hSBY:AV0ByvpwEv).rdeUDD6K/Gg7nfCg_GR_6!kPYo1{|BO)&z57./vqbn6' );
define( 'SECURE_AUTH_KEY',   '[P~H1;O%d+U^!~{+.m4jKHOx(X71{u1IeL=`+n<4Dt0?Vrm1<0m!%8#sVa|se?JF' );
define( 'LOGGED_IN_KEY',     '6yI8=eY;j$q`A{%O-CP-:6UtOnxUg/_y`QMpv}/1OlGl4iieq52*,~?Jh1rTr1]}' );
define( 'NONCE_KEY',         'HtXsTRfP%1$7p8/cNV6{PIEE7GU2}f@k~xti(Py|w/W(W0%.c&+B<,IR/eXCzNj<' );
define( 'AUTH_SALT',         'sFA.N[K0O_0[o~2!_,+6}Y67`y_Mqe$ OO+2fZbNHQ8A6%.klRTLcb[}|Zhm8i[P' );
define( 'SECURE_AUTH_SALT',  'lFwaBlLnDlj_nn3^xoiMIe_R3.@QL5?K/dMNDzs058Qs~WS5(AQ}R +]~E7 c=gw' );
define( 'LOGGED_IN_SALT',    ',mRc@yAYMg~-65naT0L-[vLE/4VE`DK@oW$Kvd~$4<AeK<A;*YsMAj3F1jJQ@21;' );
define( 'NONCE_SALT',        '/5 O`85oF^_xdslv*-Ka:Y,I_j:1GIy=f D=j}f6.`n.8f!M2,3TSK Zli,YYN)3' );
define( 'WP_CACHE_KEY_SALT', '<C;k%]U6sp;m/d,}27nzuXykU0JDIBxy0`WI}}0k7>[1(Rs&y3<S^)[&^O^D^/B?' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'FS_METHOD', 'direct' );
define( 'COOKIEHASH', '2faa548f08dac50315c232b855f403dc' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
