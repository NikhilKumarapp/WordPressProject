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
define( 'DB_NAME', 'wordpress' );

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
define( 'AUTH_KEY',         '1S+fiOW*vv5#Cw)cK)6w9wfwcr!Elf);RVCoo%_x=D^Yx!fpgr9xQ h9*gu}W88V' );
define( 'SECURE_AUTH_KEY',  'S$|DKf ,vq27C[5OPgj5g.t/$-D.L2[y0jMwHycdkOs.cA}WNRw-F1~0*C42xYxw' );
define( 'LOGGED_IN_KEY',    'O^x:$LZXATfmB5,PU}0kcYXu$1U/EYqLwGOeoa:JDH3tow(#<MZ@#X4S~6?tYH0b' );
define( 'NONCE_KEY',        '#tKM^;<HlU6p]:VA8B?_0&i2zti0VK%$!q-1;,LcGucx2.j.g`?]L-dC3(}~F#u`' );
define( 'AUTH_SALT',        'x9SDfen ,u=[=CvDC6Ccl2+?8(^%@+6t$yg@XdMt?,X|G[1bIy]1XY/{y~?Uw#tF' );
define( 'SECURE_AUTH_SALT', 't/HLM(r =+*d?oA{X9(E~ndE;(:6V*KK=Lwk63ND|_<5i_RUN%z[3-:?1+niC{+Z' );
define( 'LOGGED_IN_SALT',   ';rE@E6Flia]*^} FCp^%j69%N:Gn(LS#%U)7^-xvs]z%Z98}/A0`HSb{NAX!-#./' );
define( 'NONCE_SALT',       'ZeY6E|=^w1k<dn=q[Esh>jlD0Mtp{gB<;VY-)A7o}C=?[Kq[}@#hlO_Z{@OQl&D3' );

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
