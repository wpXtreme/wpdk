<?php
/**
 * Math utility and fix
 *
 * @class              WPDKMath
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-05-02
 * @version            1.0.0
 */

class WPDKMath {

  /**
   * Infinity const for set a value as never ending
   *
   * @brief Infinity constant
   */
  const INFINITY = 'infinity';

  // -----------------------------------------------------------------------------------------------------------------
  // Math functions
  // -----------------------------------------------------------------------------------------------------------------

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
  public static function rModulus( $a, $n ) {
    return ( $a - ( $n * round( $a / $n ) ) );
  }

  /**
   * Return true if the string `$value` in input params over with `%`
   *
   * @param string $value String value to check
   *
   * @return bool
   */
  public function isPercentage( $value ) {
    return ( substr( trim( $value ), -1, 1 ) == '%' );
  }

}
