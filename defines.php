<?php
/**
 * Defines
 *
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */

/* WPDK version. */
define( 'WPDK_VERSION', '1.1.0' );

/*
 * Path unix: /var/
 */

/* Path unix wpdk forlder. */
define( 'WPDK_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

/* Path unix wpdk classes folder. */
define( 'WPDK_DIR_CLASS', WPDK_DIR . 'classes/' );

/*
 * URI
 */

/* Set constant path: plugin URL. */
define( 'WPDK_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/* Set constant path: assets */
define( 'WPDK_URI_ASSETS', WPDK_URI . 'assets/' );
define( 'WPDK_URI_CSS', WPDK_URI_ASSETS . 'css/' );
define( 'WPDK_URI_JAVASCRIPT', WPDK_URI_ASSETS . 'js/' );

// -----------------------------------------------------------------------------------------------------------------
// wpXtreme Plugin Path
// -----------------------------------------------------------------------------------------------------------------
define( 'WPDK_WPXTREME_PATH_CLASSES', trailingslashit( dirname( __FILE__ ) ) . '../classes/' );

/*
 * Localization
 */

define( 'WPDK_TEXTDOMAIN', 'wpdk' );
define( 'WPDK_TEXTDOMAIN_PATH', 'wpxtreme/' . trailingslashit( basename( dirname( __FILE__ ) )) . 'localization' );

/*
 * Utility
 * Alias CRLF
 */
define( 'WPDK_CR', "\r" );
define( 'WPDK_LF', "\n" );
define( 'WPDK_CRLF', WPDK_CR . WPDK_LF );