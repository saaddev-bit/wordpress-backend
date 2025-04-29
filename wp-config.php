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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',          'v1#Grmq#,Wv&NzSi/o(qv(NFDcZHH~|5KOiXd@+tjT+ChQUlk}Ss/.?>t>$q$7Df' );
define( 'SECURE_AUTH_KEY',   'Em|u)nL5eo,6/~NLF^A,&5P:o(&PHDy6_U)]8y/1SN#6ZGO(o~_;yac=]0fSTR|H' );
define( 'LOGGED_IN_KEY',     ',FwtlBSr^ODs{XR-.Cw K_=W&$JZ/TN)2$:%f81Wn&=Q5m_Um[eG23Calslx)LG_' );
define( 'NONCE_KEY',         'Fnt`X#g:1A,[(L;??NjwDhp+&Bix@,wDCk7P7sIW,|:Bs]O@[h(>WSx5-@b)%F*F' );
define( 'AUTH_SALT',         '%V=9In{Vwo7d3xp)xM=]:W_c*-B|b&ml4,C:~$et/BQT#AFGX-kgK.=eV|D ^MtU' );
define( 'SECURE_AUTH_SALT',  '_Z$H.C#}l$Z2(8uu*[X?#.CEIN+$U?2&XjIxG.pT)Xqz1vb<CsT93m!0x*`+hlT7' );
define( 'LOGGED_IN_SALT',    'CY7frqqx+h^DO]%(7@MgrhcU2E>:I-ka!&+;15,V{FA,e#&yOtE{Pa8r=Y[+$Pk,' );
define( 'NONCE_SALT',        '9z?0nyCQ|ke{B9qmdf^$]B?azQ)o7aURSl/2MHM4}?Ee(:pSF/R,_^>L*|mW375z' );
define( 'WP_CACHE_KEY_SALT', 'gSGjo]y0LGCb{fW2YT,]BRwavp$b|NB,_UpKqYue}>Wl4B*X:.+!9T^OlmXc5o2~' );


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
	define( 'WP_DEBUG', true );
}
define('WP_DEBUG_LOG', true);
define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
