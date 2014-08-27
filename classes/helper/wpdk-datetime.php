<?php

/**
 * This class is useful to convert date and date time from a lot of format. It was design for MySQL conversion and
 * jQuery date and datetime picker manage.
 *
 * @class              WPDKDateTime
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-08-25
 * @version            1.1.2
 *
 * @history            1.1.2 - Improved timeNewLine() method.
 *
 */
class WPDKDateTime extends WPDKObject {

  /**
   * This is the standard MySQL date format
   *
   * @brief MySQL date format
   */
  const MYSQL_DATE = 'Y-m-d';

  /**
   * This is the standard MySQL date and time format
   *
   * @brief MySQL date time format
   */
  const MYSQL_DATE_TIME = 'Y-m-d H:i:s';

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $__version
   */
  public $__version = '1.1.1';

  /**
   * Format a date time in your custom own format. The source format is auto-detect.
   *
   * @brief Format a date time
   * @since 1.0.0
   *
   * @param string $date Source date string format
   * @param string $to   Optional. Destination/outout format, default `m/d/Y H:i`
   *
   * @return string
   */
  public static function format( $date, $to = 'm/d/Y H:i' )
  {
    $result = $date;
    if ( !empty( $date ) ) {
      $timestamp = strtotime( $date );
      $result    = date( $to, $timestamp );
    }

    return $result;
  }

  /**
   * Return a timestamp as sum from $date and $duration/$duration_type
   *
   * @brief Return expiration timestamp date
   *
   * @param string $date          Start date in MySQL format as YYYY-MM-DD HH:MM:SS
   * @param int    $duration      Duration
   * @param string $duration_type Type as `days`, `minutes`, `months
   *
   * @sa    daysToDate()
   *
   * @return int
   */
  public static function expirationDate( $date, $duration, $duration_type )
  {
    $expiredate = strtotime( "+{$duration} {$duration_type}", strtotime( $date ) );

    return $expiredate;
  }

  /**
   * Return the days to a expiration date from now. Return the days to up ($date > now) or days expired ($date < now).
   *
   * @brief Return the number of days to a expiration date
   *
   * @param int $date A timestamp date
   *
   * @return float
   */
  public static function daysToDate( $date )
  {
    $diff = $date - time();
    $days = floatval( round( $diff / ( 60 * 60 * 24 ) ) );

    return $days;
  }

  /**
   * Description
   *
   * @brief Brief
   *
   * @param string|int $date String date or timestamp
   * @param string $format
   *
   * @return string
   */
  public static function human( $date, $format = 'j M, Y H:i' )
  {
    // Check for timestamp
    if( is_numeric( $date ) ) {
      $date = date( $date );
    }

    $value  = mysql2date( $format, $date );
    $expiry = strtotime( $date );
    $ago    = ( time() - $expiry ) <= 0 ? '' : __( 'ago' );
    $in     = ( time() - $expiry ) <= 0 ? __( 'in' ) : '';

    return sprintf( '%s %s %s<br/><small>%s</small>', $in, human_time_diff( strtotime( $date ) ), $ago, $value );

  }

  /**
   * This method is similar to WordPress human_time_diff(), with the different that every amount is display.
   * For example if WordPress human_time_diff() display '10 hours', this method display '9 Hours 47 Minutes 56 Seconds'.
   *
   * @brief More readable time elapsed
   *
   * @param int  $timestamp  Date from elapsed
   * @param bool $hide_empty Optional. If TRUE '0 Year' will not return. Default TRUE.
   * @param int  $to         Optional. Date to elapsed. If empty time() is used
   *
   * @return string
   */
  public static function elapsed_string( $timestamp, $hide_empty = true, $to = 0 )
  {
    // If no $to then now
    if ( empty( $to ) ) {
      $to = time();
    }

    // Key and string output
    $useful = array(
      'y' => array(
        __( 'Year' ),
        __( 'Years' )
      ),
      'm' => array(
        __( 'Month' ),
        __( 'Months' )
      ),
      'd' => array(
        __( 'Day' ),
        __( 'Days' )
      ),
      'h' => array(
        __( 'Hour' ),
        __( 'Hours' )
      ),
      'i' => array(
        __( 'Minute' ),
        __( 'Minutes' )
      ),
      's' => array(
        __( 'Second' ),
        __( 'Seconds' )
      ),
    );

    $matrix = array(
      'y' => array(
        12 * 30 * 24 * 60 * 60,
        12
      ),
      'm' => array(
        30 * 24 * 60 * 60,
        30
      ),
      'd' => array(
        24 * 60 * 60,
        24
      ),
      'h' => array(
        60 * 60,
        60
      ),
      'i' => array(
        60,
        60
      ),
      's' => array(
        1,
        60
      ),
    );

    $diff = $timestamp - $to;

    $stack = array();
    foreach ( $useful as $w => $strings ) {

      $value = floor( $diff / $matrix[ $w ][0] ) % $matrix[ $w ][1];

      if ( empty( $value ) || $value < 0 ) {
        if ( $hide_empty ) {
          continue;
        }
        $value = 0;
      }

      $stack[] = sprintf( '%s %s', $value, _n( $strings[0], $strings[1], $value ) );
    }

    return implode( ' ', $stack );

  }

  // TODO proto
  public static function elapsed( $timestamp, $to = false, $w = 's' )
  {
    // If no $to then now
    if ( empty( $to ) ) {
      $to = time();
    }

    // Useful to convert request
    $useful = array(
      'y' => array(
        'y',
        'year'
      ),
      'm' => array(
        'm',
        'month'
      ),
      'd' => array(
        'd',
        'day'
      ),
      'h' => array(
        'h',
        'hour'
      ),
      'i' => array(
        'i',
        'minute'
      ),
      's' => array(
        's',
        'second'
      ),
    );

    // Easy search
    $w = strtolower( $w );

    // What you want?
    foreach ( $useful as $key => $array ) {
      if ( in_array( $w, $array ) ) {
        $w = $key;
        break;
      }
    }

    // Convesion matrix
    $matrix = array(
      'y' => 12 * 30 * 24 * 60 * 60,
      'm' => 30 * 24 * 60 * 60,
      'd' => 24 * 60 * 60,
      'h' => 60 * 60,
      'i' => 60,
      's' => 1
    );

    // Calculate difference
    $elapsed = $timestamp - $to;

    // Get days or months or ...
    $return = floatval( round( $elapsed / $matrix[ $w ] ) );

    return $return;

  }

  /**
   * Put a new line before time.
   *
   * @param string $datetime Date with time.
   *
   * @return string
   */
  public static function timeNewLine( $datetime )
  {
    // Get the first ':'
    $pos = strpos( $datetime, ':' );

    // No time?
    if( false === $pos ) {
      return $datetime;
    }

    // Hour start
    $pos = $pos - 2;

    $date = substr( $datetime, 0, $pos );
    $time = substr( $datetime, $pos );

    return sprintf( '%s<br/>%s', $date, $time );
  }

  /**
   * Return TRUE if $expiration date is past.
   *
   * @brief Check if a date is expired
   *
   * @param int $exipration Timestamp date to check
   *
   * @return bool
   */
  public static function isExpired( $exipration )
  {
    return ( ( $exipration - time() ) <= 0 );
  }

  /**
   * Return TRUE if now (today) is between from two date.
   *
   * @brief Check for date range
   *
   * @param string|int $date_start  Start date in string or timestamp.
   * @param string|int $date_expire Expire date in string or timestamp.
   * @param string     $format      Date format for start and expire.
   * @param bool       $timestamp   TRUE if the date are in timestamp format.
   *
   * @return bool
   */
  public static function isInRangeDatetime( $date_start, $date_expire, $format = 'YmdHis', $timestamp = false )
  {
    if ( !empty( $date_start ) || !empty( $date_expire ) ) {

      /* Get now in timestamp */
      $now = mktime();

      /* Le date sono in chiaro o anch'esse in timestamp? */
      /* @todo qui si potrebbe provare a capire in automatico se la data è in timestamp o stringa, ad esempio usando is_numeric() */
      if ( !$timestamp ) {
        $date_start  = !empty( $date_start ) ? strtotime( $date_start ) : $now;
        $date_expire = !empty( $date_expire ) ? strtotime( $$date_expire ) : $now;
      }
      else {
        $date_start  = !empty( $date_start ) ? $date_start : $now;
        $date_expire = !empty( $date_expire ) ? $date_expire : $now;
      }

      /* Verifico il range. */
      if ( $now >= $date_start && $now <= $date_expire ) {
        return true;
      }
    }

    return false;
  }

  // -------------------------------------------------------------------------------------------------------------------
  // MYSQL
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return the accuracy date format
   *
   * @brief Brief
   * @since 1.5.3
   *
   * @param string $v Optional. Any acuracy 'seconds', 'hours', etc...
   *
   * @return string
   */
  public static function accuracy( $v = false )
  {
    $conversion = array(
      'seconds' => '%Y-%m-%d %H:%i:%s',
      'minutes' => '%Y-%m-%d %H:%i',
      'hours'   => '%Y-%m-%d %H',
      'days'    => '%Y-%m-%d',
      'months'  => '%Y-%m',
      'years'   => '%Y',
    );

    // Check for keys
    if ( in_array( strtolower( $v ), array_keys( $conversion ) ) ) {
      return $conversion[ strtolower( $v ) ];
    }

    // Check for values
    if ( in_array( $v, array_values( $conversion ) ) ) {
      return $v;
    }

    return current( $conversion );
  }

  /**
   * Return a date in MySQL format `YYYY-MM-DD`.
   *
   * @brief MySQL Date
   * @since 1.3.0
   *
   * @param string $date A string date
   *
   * @return string
   */
  public static function mySQLDate( $date )
  {
    return self::format( $date, self::MYSQL_DATE );
  }

  /**
   * Return a date and time in MySQL format `YYYY-MM-DD HH:MM:SS`.
   *
   * @brief MySQL Date
   * @since 1.3.0
   *
   * @param string $datetime A string date
   *
   * @return string
   */
  public static function mySQLDateTime( $datetime )
  {
    return self::format( $datetime, self::MYSQL_DATE_TIME );
  }

  /**
   * Strip seconds from a date string (with time)
   *
   * @param string $time Time in hh:mm:ss
   *
   * @return string
   */
  public static function stripSecondsFromTime( $time )
  {
    return substr( $time, 0, 5 ); // 00:00
  }

  /**
   * Returns number of days to start of this week.
   *
   * @brief      Days number
   *
   * @author     =stid= <s.furiosi@wpxtre.me>
   *
   * @param object $date Date object
   * @param string $first_day
   *
   * @return int $date
   */
  public static function daysToWeekStart( $date, $first_day = 'monday' )
  {

    $week_days = array(
      'monday'    => 0,
      'tuesday'   => 1,
      'wednesday' => 2,
      'thursday'  => 3,
      'friday'    => 4,
      'saturday'  => 5,
      'sunday'    => 6

    );

    $start_day_number   = $week_days[ $first_day ];
    $wday               = $date->format( "w" );
    $current_day_number = ( $wday != 0 ? $wday - 1 : 6 );

    return WPDKMath::rModulus( ( $current_day_number - $start_day_number ), 7 );

  }

  /**
   * Returns number of days to start of this week.
   *
   * @author     =stid= <s.furiosi@wpxtre.me>
   *
   * @param string $date
   * @param string $first_day Optional. Start from `monday`
   *
   * @return     int
   */
  public static function beginningOfWeek( $date, $first_day = 'monday' )
  {
    $days_to_start = WPDKDateTime::daysToWeekStart( $date, $first_day );

    return ( $date - $days_to_start );
  }

  // -------------------------------------------------------------------------------------------------------------------
  // TODOS
  // -------------------------------------------------------------------------------------------------------------------


  /// TO DO
  public static function compareDate()
  {
  }

  /// TO DO
  public static function compareDatetime()
  {
  }


  // -------------------------------------------------------------------------------------------------------------------
  // DEPRECATED
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Format a date time. You can select a source format and destination format in order to return the right output
   * for you.
   *
   * @brief      Format a date time
   * @since      1.0.0.b2 Deprecated argument
   *
   * @param string $date       Source date string format
   * @param string $deprecated Optional. Not used.
   * @param string $to         Destination/outout format, default `m/d/Y H:i`
   *
   * @deprecated Use format() instead
   *
   * @return string
   */
  public static function formatFromFormat( $date, $deprecated = '', $to = 'm/d/Y H:i' )
  {
    if ( !empty( $deprecated ) && func_num_args() > 2 ) {
      _deprecated_argument( __METHOD__, '1.0.0.b2' );
    }

    _deprecated_function( __METHOD__, '1.0.0', 'format()' );

    return self::format( $date, $to );
  }

  /**
   * Return a timestamp from a specific format of date
   *
   * @brief      Timestamp from format
   * @deprecated Since 1.0.0.b2 - Use strtotime() PHP function instead
   *
   * @param string $format Format
   * @param string $date   Date and time
   *
   * @return int Timestamp
   */
  public static function makeTimeFrom( $format, $date )
  {

    _deprecated_function( __METHOD__, '1.0.0.b2', 'strtotime() PHP function' );

    /* Get date in m/d/Y H:i */
    $sanitize_date = self::formatFromFormat( $date, $format );
    $split         = explode( ' ', $sanitize_date );
    $date_part     = explode( '/', $split[0] );
    $time_part     = explode( ':', $split[1] );
    $time          = mktime( $time_part[0], $time_part[1], 0, $date_part[0], $date_part[1], $date_part[2] );

    return $time;
  }

  /**
   * Return any date/time in simple mySQL date format: `YYYY-MM-DD`
   *
   * @brief      Date for mySQL
   * @deprecated since 1.3.0 use mySQLDate() instead
   *
   * @param string $date       Date
   * @param string $deprecated Optional. Deprecated and not used since 1.0.0.b2
   *
   * @return string Data nel formato YYYY-MM-DD
   */
  public static function date2MySql( $date, $deprecated = '' )
  {
    if ( !empty( $deprecated ) ) {
      _deprecated_argument( __METHOD__, '1.0.0.b2' );
    }
    _deprecated_function( __CLASS__ . '::' . __FUNCTION__, '1.3.0', 'mySQLDate()' );

    return self::mySQLDate( $date );
  }

  /**
   * Formatta una data e ora per essere inserita in mySQL, quindi in formato YYYY-MM-DD HH:MM:SS
   * Return any date/time in simple mySQL date format: `YYYY-MM-DD HH:MM:SS`
   *
   * @brief      Date and time for mySQL
   * @deprecated since 1.3.0 use mySQLDateTime() instead
   *
   * @param string $datetime   Date
   * @param string $deprecated Optional. Deprecated and not used since 1.0.0.b2
   *
   * @return string
   */
  public static function dateTime2MySql( $datetime, $deprecated = '' )
  {
    if ( !empty( $deprecated ) ) {
      _deprecated_argument( __METHOD__, '1.0.0.b2' );
    }
    _deprecated_function( __CLASS__ . '::' . __FUNCTION__, '1.3.0', 'mySQLDateTime()' );

    return self::mySQLDateTime( $datetime );
  }


}