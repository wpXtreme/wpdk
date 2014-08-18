<?php
/**
 * Defines
 *
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-07-29
 * @version            1.0.3
 *
 */

// WPDK version
define( 'WPDK_VERSION', '1.5.11' );

// ---------------------------------------------------------------------------------------------------------------------
// Path unix: /var/
// ---------------------------------------------------------------------------------------------------------------------

// Path unix wpdk forlder
define( 'WPDK_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

// Path unix wpdk classes folder
define( 'WPDK_DIR_CLASS', WPDK_DIR . 'classes/' );

// ---------------------------------------------------------------------------------------------------------------------
// URI
// ---------------------------------------------------------------------------------------------------------------------

// Set constant path: plugin URL
define( 'WPDK_URI', trailingslashit( plugin_dir_url( __FILE__ ) ) );

// Set constant path: assets
define( 'WPDK_URI_ASSETS', WPDK_URI . 'assets/' );
define( 'WPDK_URI_CSS', WPDK_URI_ASSETS . 'css/' );
define( 'WPDK_URI_JAVASCRIPT', WPDK_URI_ASSETS . 'js/' );

// ---------------------------------------------------------------------------------------------------------------------
// wpXtreme Plugin Path integration
// ---------------------------------------------------------------------------------------------------------------------

define( 'WPDK_WPXTREME_PATH_CLASSES', trailingslashit( dirname( __FILE__ ) ) . '../classes/' );

// ---------------------------------------------------------------------------------------------------------------------
// Useful images
// ---------------------------------------------------------------------------------------------------------------------

define( 'LOGO_48', WPDK_URI_CSS . 'images/logo-48x48.png' );
define( 'LOGO_64', WPDK_URI_CSS . 'images/logo-64x64.png' );
define( 'LOGO_64_GREY', WPDK_URI_CSS . 'images/logo-64x64-grey.png' );
define( 'LOGO_128', WPDK_URI_CSS . 'images/logo-128x128.png' );

// ---------------------------------------------------------------------------------------------------------------------
// Localization
// ---------------------------------------------------------------------------------------------------------------------

define( 'WPDK_TEXTDOMAIN', 'wpdk' );
define( 'WPDK_TEXTDOMAIN_PATH', 'wpxtreme/' . trailingslashit( basename( dirname( __FILE__ ) )) . 'localization' );

// ---------------------------------------------------------------------------------------------------------------------
// Utilities
// ---------------------------------------------------------------------------------------------------------------------

define( 'WPDK_CR', "\r" );
define( 'WPDK_LF', "\n" );
define( 'WPDK_CRLF', WPDK_CR . WPDK_LF );