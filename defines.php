<?php
/**
 * Defines
 *
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-09-28
 * @version            1.0.5
 *
 * @history            1.0.5 - Renamed defines and add defines for components
 *
 */

// WPDK version
define( 'WPDK_VERSION', '1.7.3' );

// ---------------------------------------------------------------------------------------------------------------------
// Path unix: /var/
// ---------------------------------------------------------------------------------------------------------------------

// Path unix wpdk forlder
define( 'WPDK_PATH', trailingslashit( dirname( __FILE__ ) ) );

// Path unix wpdk classes folder
define( 'WPDK_PATH_CLASS', WPDK_PATH . 'classes/' );

// Path unix wpdk javascript and styles folder for components
define( 'WPDK_PATH_JAVASCRIPT', WPDK_PATH . 'assets/js/' );
define( 'WPDK_PATH_CSS', WPDK_PATH . 'assets/css/' );

// ---------------------------------------------------------------------------------------------------------------------
// URI
// ---------------------------------------------------------------------------------------------------------------------

// Set constant path: plugin URL
define( 'WPDK_URI', plugin_dir_url( __FILE__ ) );

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
define( 'WPDK_TEXTDOMAIN_PATH', 'wpxtreme/' . trailingslashit( basename( dirname( __FILE__ ) ) ) . 'localization' );

// ---------------------------------------------------------------------------------------------------------------------
// Utilities
// ---------------------------------------------------------------------------------------------------------------------

define( 'WPDK_CR', "\r" );
define( 'WPDK_LF', "\n" );
define( 'WPDK_CRLF', WPDK_CR . WPDK_LF );

// @since 1.5.6 - Disable LOG
define( 'WPDK_WATCHDOG_LOG', true );