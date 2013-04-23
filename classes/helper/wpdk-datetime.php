<?php
/// @cond private

/*
 * [DRAFT]
 *
 * THE FOLLOWING CODE IS A DRAFT. FEEL FREE TO USE IT TO MAKE SOME EXPERIMENTS, BUT DO NOT USE IT IN ANY CASE IN
 * PRODUCTION ENVIRONMENT. ALL CLASSES AND RELATIVE METHODS BELOW CAN CHNAGE IN THE FUTURE RELEASES.
 *
 */

/* This defines are for global access. Use static constant in WPDKDateTime class instead. */
define( 'MYSQL_DATE', 'Y-m-d' );
define( 'MYSQL_DATE_TIME', 'Y-m-d H:i:s' );

/**
 * This class is useful to convert date and date time from a lot of format. It was design for MySQL conversion and
 * jQuery date and datetime picker manage.
 *
 * @class              WPDKDateTime
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-01-11
 * @version            0.9
 *
 */

class WPDKDateTime {

  /**
   * This is the standard MySQL date format
   *
   * @brief MySQL date format
   */
  const MYSQL_DATE = MYSQL_DATE;

  /**
   * This is the standard MySQL date and time format
   *
   * @brief MySQL date time format
   */
  const MYSQL_DATE_TIME = MYSQL_DATE_TIME;

  // -----------------------------------------------------------------------------------------------------------------
  // Date
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Format a date time. You can select a source format and destination format in order to return the right output
   * for you.
   *
   * @brief Format a date time
   * @since 1.0.0.b2 Deprecated argument
   *
   * @param string $date       Source date string format
   * @param string $deprecated Optional. Not used.
   * @param string $to         Destination/outout format, default `m/d/Y H:i`
   *
   * @return string Data convertita
   */
  public static function formatFromFormat( $date, $deprecated = '', $to = 'm/d/Y H:i' ) {
    if ( !empty( $deprecated ) && func_num_args() > 2 ) {
      _deprecated_argument( __METHOD__, '1.0.0.b2' );
    }
    $result = $date;
    if ( !empty( $date ) ) {
      $timestamp = strtotime( $date );
      $result    = date( $to, $timestamp );

    }
    return $result;
  }

  /**
   * Return a timestamp from a specific format of date
   *
   * @brief Timestamp from format
   * @deprecated Since 1.0.0.b2 - Use strtotime() PHP function instead
   *
   * @param string $format Format
   * @param string $date   Date and time
   *
   * @return int Timestamp
   */
  public static function makeTimeFrom( $format, $date ) {

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
   * Return a timestamp as sum from $date and $duration/$duration_type
   *
   * @brief Return expiration timestamp date
   *
   * @param string $date          Start date in MySQL format as YYYY-MM-DD HH:MM:SS
   * @param int    $duration      Duration
   * @param string $duration_type Type as `days`, `minutes`, `months
   *
   * @sa daysToDate()
   *
   * @return int
   */
  static function expirationDate( $date, $duration, $duration_type ) {
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
  static function daysToDate( $date ) {
    $diff = $date - time();
    $days = floatval( round( $diff / ( 60 * 60 * 24 ) ) );
    return $days;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // UI
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Utility per porre l'orario su una nuova riga. Aggiunge un tag br nello spazio tra la data e l'ora
   *
   * @param string $datetime Data e ora formattati in modo che lo spazio delimiti data e ora
   *
   * @return string Data, tag br e ora
   */
  static function timeNewLine( $datetime ) {
    return str_replace( ' ', '<br/>', $datetime );
  }

  // -----------------------------------------------------------------------------------------------------------------
  // has/is zone
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return TRUE if $expiration date is past
   *
   * @brief Check if a date is expired
   *
   * @param int $exipration Timestamp date to check
   *
   * @return bool
   */
  static function isExpired( $exipration ) {
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
  public static function isInRangeDatetime( $date_start, $date_expire, $format = 'YmdHis', $timestamp = false ) {
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


  // -----------------------------------------------------------------------------------------------------------------
  // Alias
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return any date/time in simple mySQL date format: `YYYY-MM-DD`
   *
   * @brief Date for mySQL
   *
   * @param string $date       Date
   * @param string $deprecated Optional. Deprecated and not used since 1.0.0.b2
   *
   * @return string Data nel formato YYYY-MM-DD
   */
  public static function date2MySql( $date, $deprecated = '' ) {
    if ( !empty( $deprecated ) ) {
      _deprecated_argument( __METHOD__, '1.0.0.b2' );
    }
    return self::formatFromFormat( $date, '', 'Y-m-d' );
  }

  /**
   * Formatta una data e ora per essere inserita in mySQL, quindi in formato YYYY-MM-DD HH:MM:SS
   * Return any date/time in simple mySQL date format: `YYYY-MM-DD HH:MM:SS`
   *
   * @brief Date and time for mySQL
   *
   * @param string $datetime   Date
   * @param string $deprecated Optional. Deprecated and not used since 1.0.0.b2
   *
   * @return string
   */
  public static function dateTime2MySql( $datetime, $deprecated = '' ) {
    if ( !empty( $deprecated ) ) {
      _deprecated_argument( __METHOD__, '1.0.0.b2' );
    }
    return self::formatFromFormat( $datetime, '', 'Y-m-d H:i:s' );
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Time
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Elimina i secondi da un time
   *
   *
   * @param string $time Orario in hh:mm:ss
   *
   * @return string
   */
  public static function stripSecondsFromTime( $time ) {
    return substr( $time, 0, 5 ); // 00:00
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Time Calculation
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Returns number of days to start of this week.
   *
   * @brief Days number
   *
   * @author     =stid= <s.furiosi@wpxtre.me>
   *
   * @param object $date Date object
   * @param string $first_day
   *
   * @return int $date
   */
  public static function daysToWeekStart( $date, $first_day = 'monday' ) {

    $week_days = array(
      'monday'    => 0,
      'tuesday'   => 1,
      'wednesday' => 2,
      'thursday'  => 3,
      'friday'    => 4,
      'saturday'  => 5,
      'sunday'    => 6

    );

    $start_day_number   = $week_days[$first_day];
    $wday               = $date->format( "w" );
    $current_day_number = ( $wday != 0 ? $wday - 1 : 6 );
    return WPDKMath::rModulus( ( $current_day_number - $start_day_number ), 7 );

  }

  /**
   * Returns number of days to start of this week.
   *
   * @author     =stid= <s.furiosi@wpxtre.me>
   *
   *
   * @param string $date
   * @param string $first_day
   *
   * @return     $date
   */
  public static function beginningOfWeek( $date, $first_day = 'monday' ) {
    $days_to_start = WPDKDateTime::daysToWeekStart( $date, $first_day );
    return ( $date - $days_to_start );
  }

  /// TO DO
  public static function compareDate() { }

  /// TO DO
  public static function compareDatetime() { }


}

/// @endcond
