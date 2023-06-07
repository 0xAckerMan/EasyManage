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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'easymanage' );

/** Database username */
define( 'DB_USER', 'admin' );

/** Database password */
define( 'DB_PASSWORD', 'admin' );

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
define( 'AUTH_KEY',         '(*l D}|L|1neqm9P)`e;}3y,E,AUte=u_)4qqv%R`E)-~~#O^q)JwWtg3-BFa#|g' );
define( 'SECURE_AUTH_KEY',  'HH3AhX4mqN}_Y*O>2*=9zQ]/FJ.%x7mo.Lg;j-MGq4^;&(HFJ.vfE&y*^[z`.DuF' );
define( 'LOGGED_IN_KEY',    ')bex0f_$|!k{W$Nr;)Plq~V_bLt;7o 34>plGRE)8Xfd/Qel.:4&F3a)TWV9pqd^' );
define( 'NONCE_KEY',        '8&|YYXR(4o>L|}TX8+GruP6?)zqfpCS^yb0+Eo3B4[qgQ*UU}DK`9=W^XB#|oTx&' );
define( 'AUTH_SALT',        'jfD3C`~}OB<fNE;2,>_cIEEt>^_|{tJ &AwSuW`qp!3EnB9{3gZAl`D:IsF:Tys#' );
define( 'SECURE_AUTH_SALT', '{ir%-,.IRMi+)0U}NltwMW iH+h`EFEsbzQQt0{3;~XBiOy1L_g,V^@XDT9@!lJ9' );
define( 'LOGGED_IN_SALT',   'Lx::C`S^JB}tb]&n,hm(ygaD5*J G}24-Y*av,b?{Clz+GTJtQvGgW.(jr3>f@ v' );
define( 'NONCE_SALT',       '*6]k*1zRq:&]RqQK x*pn_Ag^V^I?>*G7<-=+_LW1+X4}iH)y-E&g!B/VAC!(?;k' );

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
