<?php

/// @cond private

/*
 * [DRAFT]
 *
 * THE FOLLOWING CODE IS A DRAFT. FEEL FREE TO USE IT TO MAKE SOME EXPERIMENTS, BUT DO NOT USE IT IN ANY CASE IN
 * PRODUCTION ENVIRONMENT. ALL CLASSES AND RELATIVE METHODS BELOW CAN CHNAGE IN THE FUTURE RELEASES.
 *
 */

/**
 * Debugger manager
 *
 * ## Overview
 *
 * The WPDKWatchDog class manage a standard log system to trace your own data. A log file is formatted with the time of
 * the day. So, every day you found a log file. All log file are put into the default folder 'logs', on main path of
 * instance class.
 *
 * ## Getting started
 *
 * You rarely need to instantiate this class. The WPDKWordPressPlugin class create a WPDKWatchDog's instance for us and
 * then set WPDKWatchDog pointer in `log` property of WPDKWordPressPlugin subclasses.
 *
 * @class              WPDKWatchDog
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-09-11
 * @version            1.0.3
 *
 * @history            1.0.3 - Added enable/disable log via WPDK_WATCHDOG_LOG define
 *
 */
class WPDKWatchDog {

  /**
   * Used as separator between log events
   *
   * @brief Default separator
   */
  const LOG_SEPARATOR = '---------------------------------------------------------------------------------------------';

  /**
   * Get/Set Enabled log. Default TRUE
   *
   * @brief Enabled log
   *
   * @var bool $enabled
   */
  public $enabled = true;

  /**
   * Get/Set Enabled trigger error. Default FALSE
   *
   * @brief Enabled trigger error too
   *
   * @var bool $triggerError
   */
  public $triggerError = false;

  /**
   * Get/Set the line separator gap in log events
   *
   * @brief The line separator
   *
   * @var string $separator
   */
  public $separator = self::LOG_SEPARATOR;

  /**
   * Get the complete path of log
   *
   * @brief Path log
   *
   * @var string $path
   */
  public $path = '';

  /**
   * Get the generate log filename: YYYY-MM-DD.php
   *
   * @brief Log filename
   *
   * @var string $logname
   */
  public $logname = '';

  /**
   * Get TRUE if log is available. Could be FALSE if file is not writeable
   *
   * @brief Log available
   *
   * @var bool $available
   */
  public $available = false;

  /**
   * Return the Log filename extension. Default 'php'
   *
   * @brief Get Log filename extension
   *
   * @var string $extensionFilename
   */
  public $extensionFilename = 'php';

  /**
   * Return prefix to add date filename. Eg wpxss-20120912.php. Default empty.
   *
   * @brief Log filename prefix
   *
   * @var string $prefix
   */
  public $prefix = '';

  /**
   * Create an instance of WPDKWatchDog class
   *
   * @brief Construct
   *
   * @param string $path      Path of main file
   * @param string $folder    Name of log folder, default 'logs'
   * @param string $extension This is the extension of single log filename, default 'php'
   *
   * @return WPDKWatchDog
   *
   */
  public function __construct( $path, $folder = 'logs', $extension = 'php' )
  {
    // Get WPDK_WATCHDOG_LOG via option
    // NOTE: you can use this option to enable/disable watchdog by UI
    $wpdk_watchdog_log = get_site_option( 'wpdk_watchdog_log', false );

    // Enable/disable
    if ( ( ! defined( 'WPDK_WATCHDOG_LOG' ) || true !== WPDK_WATCHDOG_LOG ) || empty( $wpdk_watchdog_log ) ) {

      // Set properties
      $this->enabled = $this->available = false;

      return;
    }

    // Populate propertyes
    $this->path              = trailingslashit( trailingslashit( $path ) . $folder );
    $this->enabled           = true;
    $this->triggerError      = false;
    $this->separator         = self::LOG_SEPARATOR;
    $this->extensionFilename = $extension;

    // Under the plugin path ($path) create a log/ folder
    if ( ! file_exists( $this->path ) ) {
      @wp_mkdir_p( $this->path );
    }

    $this->logname = sprintf( '%s%s.%s', $this->path, date( 'Ymd' ), $this->extensionFilename );

    // Check if log file is available
    $handle          = @fopen( $this->logname, "a+" );
    $this->available = ( false !== $handle );

    // Remove old zero logs
    $yesterday_log = sprintf( '%s%s.%s', $this->path, date( 'Ymd', strtotime( '-1 days' ) ), $this->extensionFilename );
    if ( file_exists( $yesterday_log ) ) {
      $size = @filesize( $yesterday_log );
      if ( empty( $size ) ) {
        @unlink( $yesterday_log );
      }
    }
  }

  /**
   * Write a content in log.
   *
   * @brief Do log
   *
   * @param mixed $txt     Content to log. Usually this is a string or number. However you can set this param direcly to
   *                       object or array. In this case the log method recognize the not string param and do
   *                       a `var_dump`.
   *
   * @param mixed $args    Optional. ...
   *
   * @return bool
   */
  public function log( $txt, $args = null )
  {
    if ( $this->enabled && $this->available ) {

      // Get num args
      if ( func_num_args() > 2 ) {

        // Get format
        $format = func_get_arg( 0 );

        // Print the format as title
        $title = $format;

        // Get args
        $args = func_get_args();

        // Removed the first (format)
        unset( $args[0] );

        // Convert
        $values = array();
        foreach ( $args as $txt ) {
          ob_start();
          var_dump( $txt );
          $values[] = ob_get_contents();
          ob_end_clean();
        }

        // Build the log
        $txt = vsprintf( $format, $values );

      }
      // Value, title
      else {
        $title = $args;
      }

      // If not a pre-formatted string, grab the var dump object, array or mixed for output
      if ( ! is_string( $txt ) && ! is_numeric( $txt ) ) {
        ob_start();
        var_dump( $txt );
        $txt = ob_get_contents();
        ob_end_clean();
      }

      $date   = sprintf( '[%s]', date( 'Y-m-d H:i:s' ) );
      $sepa   = substr( $this->separator, 0, ( strlen( $this->separator ) - strlen( $date ) - 1 ) );
      $output = sprintf( "%s (%s) %s\n%s\n\n", $date, $title, $sepa, $txt );

      /**
       * Fires when a log is write.
       *
       * @param string $content The log content
       */
      do_action( 'wpdk_watchdog_log', $output );

      $handle = fopen( $this->logname, 'a+' );

      // If handle is ok log
      if ( false !== $handle ) {
        fwrite( $handle, $output );
        fclose( $handle );
      }
      else {
        $this->available = false;
      }

      if ( $this->triggerError ) {
        trigger_error( $output );
      }
    }

    return $this->available;
  }
}

/// @endcond