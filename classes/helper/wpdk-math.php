<?php

/**
 * Math utility and fix
 *
 * @class              WPDKMath
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2014-10-16
 * @version            1.0.5
 */
class WPDKMath extends WPDKObject {

  // Infinity const for set a value as never ending
  const INFINITY = 'infinity';

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $__version
   */
  public $__version = '1.0.5';

  /**
   * Mimic the math function modules like Ruby, Python & TLC
   *
   * @author     =stid= <s.furiosi@wpxtre.me>
   * @brief      Do a modules
   *
   * @param number $a Number a
   * @param number $n Module b of a
   *
   * @return int
   */
  public static function rModulus( $a, $n )
  {
    return ( $a - ( $n * round( $a / $n ) ) );
  }

  /**
   * Remove all alfa-char from a string and return number only.
   *
   * @brief Number
   * @sice  1.5.6
   *
   * @param string $value Any string with number inside.
   *
   * @return int|float
   */
  public static function number( $value )
  {
    return preg_replace( '/\D/', '', $value );
  }

  /**
   * Return true if the string `$value` in input params over with `%`
   *
   * @param string $value String value to check
   *
   * @return bool
   */
  public static function isPercentage( $value )
  {
    return ( substr( trim( $value ), -1, 1 ) == '%' );
  }

  /**
   * Return TRUE if $value is equal to INF (php) or WPDKMath::INFINITY
   *
   * @brief Check if infinity
   * @since 1.2.0
   *
   * @param float|string $value Value to check.
   *
   * @return bool
   */
  public static function isInfinity( $value )
  {
    return ( is_infinite( floatval( $value ) ) || ( is_string( $value ) && $value == self::INFINITY ) );
  }

  /**
   * Return the string value (byte) well formatted.
   *
   * @brief Display bytes.
   * @since 1.6.1
   *
   * @param int $value     The bytes value.
   * @param int $precision Optional. Precision after comma.
   *
   * @return string
   */
  public static function bytes( $value, $precision = 2 )
  {
    static $units = array(
      'Bytes',
      'KB',
      'MB',
      'G',
      'T',
      'P',
      'E',
      'Z',
      'Y'
    );

    // hardcoded maximum number of units @ 9 for minor speed increase
    $e = empty( $value ) ? 0 : floor( log( $value ) / log( 1024 ) );

    return sprintf( '%.' . $precision . 'f ' . $units[ $e ], ( $value / pow( 1024, floor( $e ) ) ) );
  }

}
