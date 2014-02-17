<?php
/**
 * Manange color conversions and more
 *
 * @class           WPDKColors
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-08
 * @version         1.0.2
 *
 */
class WPDKColors extends WPDKObject {

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $__version
   */
  public $__version = '1.0.2';

  /**
   * Convert a hex decimal color code to its RGB equivalent and vice versa.
   *
   *     echo WPDKColors::rgb2hex( 'FFCC00' );   // array( 255, 240, 0 )
   *     echo WPDKColors::rgb2hex( '1,200,16' ); // '#01C810'
   *
   * @param string $value
   *
   * @brief RGB to HEX
   * @since 1.2.0
   *
   * @return bool|string!array
   */
  public static function rgb2hex( $value )
  {
    if ( empty( $value ) ) {
      return false;
    }
    $value = trim( $value );
    $out   = false;
    if ( preg_match( "/^[0-9ABCDEFabcdef\#]+$/i", $value ) ) {
      $value = str_replace( '#', '', $value );
      $l     = strlen( $value ) == 3 ? 1 : ( strlen( $value ) == 6 ? 2 : false );

      if ( $l ) {
        unset( $out );
        $out['red'] = hexdec( substr( $value, 0, 1 * $l ) );
        $out['green'] = hexdec( substr( $value, 1 * $l, 1 * $l ) );
        $out['blue'] = hexdec( substr( $value, 2 * $l, 1 * $l ) );
      }
      else {
        $out = false;
      }

    }
    elseif ( preg_match( "/^[0-9]+(,| |.)+[0-9]+(,| |.)+[0-9]+$/i", $value ) ) {
      $spr = str_replace( array( ',', ' ', '.' ), ':', $value );
      $e   = explode( ":", $spr );
      if ( count( $e ) != 3 ) {
        return false;
      }
      $out = '#';
      for ( $i = 0; $i < 3; $i++ ) {
        $e[$i] = dechex( ( $e[$i] <= 0 ) ? 0 : ( ( $e[$i] >= 255 ) ? 255 : $e[$i] ) );
      }

      for ( $i = 0; $i < 3; $i++ ) {
        $out .= ( ( strlen( $e[$i] ) < 2 ) ? '0' : '' ) . $e[$i];
      }
      $out = strtoupper( $out );
    }
    else {
      $out = false;
    }
    return $out;
  }

  /**
   * Perform adding (or subtracting) operation on a hexadecimal colour code
   *
   *     echo WPDKColors::hexAddition( 'C00001', '1' ); // #C10102
   *
   * @brief Hex addition
   * @since 1.2.0
   *
   * @param string $hex Color hexdecimal value like 'C00001'
   * @param string $num Value hexdecimal to add like 1
   *
   * @return string
   */
  public static function hexAddition( $hex, $num )
  {
    $rgb = self::rgb2hex( $hex );
    if ( empty( $rgb ) ) {
      return $hex;
    }

    foreach ( $rgb as $key => $val ) {
      $rgb[$key] += $num;
      $rgb[$key] = ( $rgb[$key] < 0 ) ? 0 : $rgb[$key];
    }
    $hex = self::rgb2hex( implode( ',', $rgb ) );

    return $hex;
  }

}