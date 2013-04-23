<?php
/**
 * Array Helper
 *
 * ## Overview
 * The WPDKArray class is an helper that provides a lot of static method for make easy and fast to works with array.
 *
 * @class              WPDKArray
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */

class WPDKArray {

  // -----------------------------------------------------------------------------------------------------------------
  // Manipolation
  // -----------------------------------------------------------------------------------------------------------------


  /**
   * Insert a key => value into a second array to a specify index
   *
   * @brief Insert a key value pairs in array
   *
   * @param array  $arr   Source array
   * @param string $key   Key
   * @param mixed  $val   Value
   * @param int    $index Optional. Index zero base
   *
   * @return array
   */
  public static function insertKeyValuePairs( $arr, $key, $val, $index = 0 ) {
    $arrayEnd   = array_splice( $arr, $index );
    $arrayStart = array_splice( $arr, 0, $index );
    return ( array_merge( $arrayStart, array( $key => $val ), $arrayEnd ) );
  }

  /**
   * Insert an array or key value pairs array in a second array to a specify index
   *
   * @brief Insert an array in array
   *
   * @param array $arr   Source array
   * @param array $new   New array
   * @param int   $index Optional. Index zero base
   *
   * @note For key as int see this workround http://stackoverflow.com/a/15413637/205350
   *
   * @return array
   */
  public static function insert( $arr, $new, $index = 0 ) {
    $arrayEnd   = array_splice( $arr, $index );
    $arrayStart = array_splice( $arr, 0, $index );
    return ( array_merge( $arrayStart, $new, $arrayEnd ) );
  }

  /**
   * Prepend an array or key value pairs array in a destination array. The `$dest` array is changed.
   *
   * @brief Prepend an array in array
   *
   * @param array $array Source array to prepend
   * @param array $dest  Reference to destination array
   *
   * @note For key as int see this workround http://stackoverflow.com/a/15413637/205350
   *
   * @return array
   */
  public static function prepend( $array, &$dest ) {
    $dest = self::insert( $dest, $array );
    return $dest;
  }

  /**
   * Append an array or key value pairs array in a destination array. The `$dest` array is changed.
   *
   * @brief Prepend an array in array
   *
   * @param array $array Source array to prepend
   * @param array $dest  Reference to destination array
   *
   * @note For key as int see this workround http://stackoverflow.com/a/15413637/205350
   *
   * @return array
   */
  public static function append( $array, &$dest ) {
    $dest = self::insert( $dest, $array, count( $dest ) );
    return $dest;
  }

  /**
   * Warp each element of an array with an array
   *
   * @brief Warp each element of an array with an array
   *
   * @param $array An array
   *
   * @return array
   * @note Prototype
   */
  public static function wrapArray( $array ) {
    $result = array();
    foreach ( $array as $element ) {
      $result[] = array( $element );
    }
    return $result;
  }

  /**
   * Return a new key value pairs array with element that match with key of the second array. In other words, if the
   * key of $array_extract is found in keys of $array_key, then that element is catch, else is ignored.
   *
   * @brief Return an array for key
   *
   * @param array $array_key     A key value pairs array with the keys to match
   * @param array $array_extract A source key value pairs array that have to match with a specific key
   *
   * @note Well done name and synopsis in arrayExtractByKey()
   *
   * @return array
   */
  public static function arrayWithKey( $array_key, $array_extract ) {
    $keys   = array_keys( $array_key );
    $result = array();
    foreach ( $array_extract as $key => $value ) {
      if ( in_array( $key, $keys ) ) {
        $result[$key] = $value;
      }
    }
    return $result;
  }

  /**
   * Return a new key value pairs array with element that match with key of the second array. In other words, if the
   * key of $array_extract is found in keys of $array_key, then that element is catch, else is ignored.
   *
   * @brief Return an array for key
   *
   * @param array $sourceArray   A source key value pairs array that have to match with a specific key
   * @param array $arrayKeys     A key value pairs array with the keys to match
   *
   * @return array
   */
  public static function arrayExtractByKey( $sourceArray, $arrayKeys ) {
    $keys   = array_keys( $arrayKeys );
    $result = array();
    foreach ( $sourceArray as $key => $value ) {
      if ( in_array( $key, $keys ) ) {
        $result[$key] = $value;
      }
    }
    return $result;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Conversions
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Convert an array into a stdClass()
   *
   * @brief Array to object
   *
   * @param   array $array  The array we want to convert
   *
   * @return  object
   */
  public static function arrayToObject( $array ) {
    // First we convert the array to a json string
    $json = json_encode( $array );

    // The we convert the json string to a stdClass()
    $object = json_decode( $json );

    return $object;
  }


  /**
   * Convert a object to an array
   *
   * @brief Object to array
   *
   * @param   object $object The object we want to convert
   *
   * @return  array
   */
  public static function objectToArray( $object ) {
    // First we convert the object into a json string
    $json = json_encode( $object );

    // Then we convert the json string to an array
    $array = json_decode( $json, true );

    return $array;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Miscellanea
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Simulates the behavior of http_build_query() in the previous versions of php 5
   *
   * @brief http_build_query() replacement
   *
   * @param array $formdata       Array $key => $value
   * @param null  $numeric_prefix See http_build_query() documentation
   * @param null  $arg_separator  See http_build_query() documentation
   *
   * @return string
   */
  public static function httpBuildQuery( $formdata, $numeric_prefix = null, $arg_separator = null ) {

    /* Get PHP version. */
    if ( defined( 'PHP_MAJOR_VERSION' ) && PHP_MAJOR_VERSION >= 5 ) {
      return http_build_query( $formdata, $numeric_prefix, $arg_separator );
    }
    else {
      $version = absint( phpversion() );
      if ( !empty( $version ) && $version >= 5 ) {
        return http_build_query( $formdata, $numeric_prefix, $arg_separator );
      }
    }

    /* Emulate the behavior. */
    $result = '';
    $amp    = '';
    foreach ( $formdata as $key => $value ) {
      $result .= sprintf( '%s%s=%s', $amp, $key, urlencode( $value ) );
      $amp = $arg_separator;
    }

    return $result;
  }

}