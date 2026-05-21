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
define( 'DB_NAME', 'pronetwork_blog' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '102004' );

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
define( 'AUTH_KEY',         '(4}6@NV;fQKq342]1<;C-j8|hP4$/,F=#EB8%li$YWv+;UaJZ^Ll^?D3e0T59wp@' );
define( 'SECURE_AUTH_KEY',  '8*XRYfv1+*:]itaGN8]n#!ZhU~=G_J%G $kZxM;tAQlJRA2{I_5VqfrquM^x1}nU' );
define( 'LOGGED_IN_KEY',    '(`Xid@.[q73D>A@|#wK2bqw-MV,SIrWR_}TYD[T,mG.,BK:]5 ?]~ 7y0|^b(cI_' );
define( 'NONCE_KEY',        'i}-9=>a6;4GW./X8~PtaH&%BG83_9/T5=@UBkB2l7ARKt(Y,pmL%i&+?]G#t_<I-' );
define( 'AUTH_SALT',        '6S*3+95)=2B(0,dJ([#Uc5Iw&xXj%r):NMZuIY>6H>|uGeOC*RXGp$D@/`Hyh$}W' );
define( 'SECURE_AUTH_SALT', 'TbHO Z`}+kAW[C0<*j1&^X#Y9a~,MkUea{BH)0s)Y8/&*8C$N=rm.{|vKJ(:ZfI?' );
define( 'LOGGED_IN_SALT',   'Kb8gdWI2Ac]MRjfx{cq=g!F4sN4g<:/T1VOYyx*wYi+J>T).1h7COE( E9l}y#*.' );
define( 'NONCE_SALT',       '+oAFe7?Dr0ere7!C2h-!193v}~4zDa[*[qpJ_}Dws3a/Ct%Ao ds&KkT4,sJ]?=j' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
