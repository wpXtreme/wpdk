<?php
/**
 * Math utility and fix
 *
 * @class              WPDKMath
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-11-18
 * @version            1.0.2
 */

class WPDKMath extends WPDKObject {

  /**
   * Infinity const for set a value as never ending
   *
   * @brief Infinity constant
   */
  const INFINITY = 'infinity';

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $version
   */
  public static $version = '1.0.2';

  /**
   * Mimic the math function modules like Ruby, Python & TLC
   *
   * @brief      Do a modules
   *
   * @author     =stid= <s.furiosi@wpxtre.me>
   *
   * @param number $a Number a
   * @param number $n Module b of a
   *
   * @return integer
   */
  public static function rModulus( $a, $n )
  {
    return ( $a - ( $n * round( $a / $n ) ) );
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
   * Check if infinity
   *
   * @brief Infinity
   * @since 1.2.0
   *
   * @param float|string $value Check value
   *
   * @return bool true if $value is equal to INF (php) or WPDKMath::INFINITY
   *
   */
  public static function isInfinity( $value )
  {
    return ( is_infinite( floatval( $value ) ) || ( is_string( $value ) && $value == self::INFINITY ) );
  }

}
