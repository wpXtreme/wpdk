<?php
/**
 * WPDK WordPress load styles replacement
 *
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-09-28
 * @version         1.0.0
 * @since           1.5.18
 *
 */

/**
 * Disable error reporting
 *
 * Set this to error_reporting( -1 ) for debugging.
 */
error_reporting( E_ALL );

/**
 * @ignore
 */
function trailingslashit( $string )
{
  return rtrim( $string, '/\\' ) . '/';
}

/**
 * @ignore
 */
function plugin_dir_url() {}

/**
 * @ignore
 */
function add_action() {}

/**
 * @ignore
 */
function add_filter() {}

/**
 * @ignore
 */
function esc_attr() {}


// Flag
define( 'WPDK_LOAD_SCRIPTS', 1 );

// Include define for version
require_once( dirname( __FILE__ ) . '/defines.php' );

/**
 * Get file content
 *
 * @param $path
 *
 * @return string
 */
function get_file( $path )
{

  if ( function_exists( 'realpath' ) ) {
    $path = realpath( $path );
  }

  if ( ! $path || ! @is_file( $path ) ) {
    return '';
  }

  return @file_get_contents( $path );
}

// Get request
$load = $_GET['load'];

// Convert in string
if ( is_array( $load ) ) {
  $load = implode( '', $load );
}

// Filter
$load = preg_replace( '/[^a-z0-9,._-]+/i', '', $load );

// Render unique
$load = array_unique( explode( ',', $load ) );

// Stability
if ( empty( $load ) ) {
  exit;
}

// Other params
$compress       = ( isset( $_GET['c'] ) && $_GET['c'] );
$force_gzip     = ( $compress && 'gzip' == $_GET['c'] );
$expires_offset = 31536000; // 1 year
$out            = '';


// Registered components
require_once( WPDK_PATH_CLASS .'ui/wpdk-ui-components.php' );

// Keep WordPress variable name
$components = new WPDKUIComponents;

foreach ( $load as $handle ) {

  if ( ! array_key_exists( $handle, $components->components ) ) {
    continue;
  }

  $path = WPDK_PATH_CSS . $components->components[ $handle ]['css'] . '.css';
  $content = get_file( $path ) . "\n";

  // Path replace
  if ( $handle == WPDKUIComponents::WPDK || $handle == WPDKUIComponents::JQUERY_UI_CUSTOM ) {
    $content = str_replace( '{WPDK_URI_ASSETS}', WPDK_URI_ASSETS, $content );
  }

  // Path replace
  if ( $handle == WPDKUIComponents::JQUERY_UI_CUSTOM ) {
    $content = str_replace( '{WPDK_URI_CSS}', WPDK_URI_CSS, $content );
  }

  $out .= $content;
}

header('Content-Type: text/css; charset=UTF-8');
header( 'Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expires_offset ) . ' GMT' );
header( "Cache-Control: public, max-age=$expires_offset" );

if ( $compress && ! ini_get( 'zlib.output_compression' ) && 'ob_gzhandler' != ini_get( 'output_handler' ) &&
     isset( $_SERVER['HTTP_ACCEPT_ENCODING'] )
) {
  header( 'Vary: Accept-Encoding' ); // Handle proxies
  if ( false !== stripos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'deflate' ) && function_exists( 'gzdeflate' ) &&
       ! $force_gzip
  ) {
    header( 'Content-Encoding: deflate' );
    $out = gzdeflate( $out, 3 );
  }
  elseif ( false !== stripos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) && function_exists( 'gzencode' ) ) {
    header( 'Content-Encoding: gzip' );
    $out = gzencode( $out, 3 );
  }
}

echo $out;
exit;
