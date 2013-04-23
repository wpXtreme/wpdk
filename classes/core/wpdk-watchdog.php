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
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */

class WPDKWatchDog {

  /* @deprecated Use WPDKResult instead */
  const ERROR   = 'error';
  const WARNING = 'warning';
  const STATUS  = 'status';

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
  public $enabled;

  /**
   * Get/Set Enabled trigger error. Default FALSE
   *
   * @brief Enabled trigger error too
   *
   * @var bool $triggerError
   */
  public $triggerError;

  /**
   * Get/Set the line separator gap in log events
   *
   * @brief The line separator
   *
   * @var string $separator
   */
  public $separator;

  /**
   * Get the complete path of log
   *
   * @brief Path log
   *
   * @var string $path
   */
  public $path;

  /**
   * Get the generate log filename: YYYY-MM-DD.php
   *
   * @brief Log filename
   *
   * @var string $logname
   */
  public $logname;

  /**
   * Get TRUE if log is available. Could be FALSE if file is not writeable
   *
   * @brief Log available
   *
   * @var bool $available
   */
  public $available;

  /**
   * Return the Log filename extension. Default 'php'
   *
   * @brief Get Log filename extension
   *
   * @var string $extensionFilename
   */
  public $extensionFilename;

  /**
   * Return prefix to add date filename. Eg wpxss-20120912.php. Default empty
   *
   * @brief Log filename prefix
   *
   * @var string $prefix
   */
  public $prefix;

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
  public function __construct( $path, $folder = 'logs', $extension = 'php' ) {
    $this->path              = trailingslashit( trailingslashit( $path ) . $folder );
    $this->enabled           = true;
    $this->triggerError      = false;
    $this->separator         = self::LOG_SEPARATOR;
    $this->extensionFilename = $extension;

    /* Under the plugin path ($path) create a log/ folder */
    if ( !file_exists( $this->path ) ) {
      @wp_mkdir_p( $this->path );
    }

    $this->logname = sprintf( '%s%s.%s', $this->path, date( 'Ymd' ), $this->extensionFilename );

    /* Check if log file is available */
    $handle          = @fopen( $this->logname, "a+" );
    $this->available = ( false !== $handle );

    /* Remove old zero logs. */
    $yesterday_log = sprintf( '%s%s.%s', $this->path, date( 'Ymd', strtotime( '-1 days' ) ), $this->extensionFilename );
    if ( file_exists( $yesterday_log ) ) {
      $size = @filesize( $yesterday_log );
      if ( empty( $size ) ) {
        @unlink( $yesterday_log );
      }
    }
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Log
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Write a content in log.
   *
   * @brief Do log
   *
   * @param mixed  $txt   Content to log. Usually this is a string or number. However you can set this param direcly to
   *                      object or array. In this case the log method recognize the not string param and do
   *                      a `var_dump`.
   *
   * @param string $title Optional. Any free string text to context the log
   *
   * @return bool
   */
  public function log( $txt, $title = '' ) {
    if ( $this->enabled && $this->available ) {

      /* If not a pre-formatted string, grab the var dump object, array or mixed for output */
      if ( !is_string( $txt ) || !is_numeric( $txt ) ) {
        ob_start();
        var_dump( $txt );
        $content = ob_get_contents();
        ob_end_clean();
        $txt = $content;
      }

      $date   = sprintf( '[%s]', date( 'Y-m-d H:i:s' ) );
      $sepa   = substr( $this->separator, 0, ( strlen( $this->separator ) - strlen( $date ) - 1 ) );
      $output = sprintf( "%s (%s) %s\n%s\n\n", $date, $title, $sepa, $txt );
      $handle = fopen( $this->logname, 'a+' );
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


  // -----------------------------------------------------------------------------------------------------------------
  // Deprecated
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Display or return a formatted output for WP_Error object. For each error in WP_Error object a formatted
   * output is done with code error and error data.
   *
   * @deprecated Use WPDKResult instead
   *
   * @param WP_Error $error WP_Error object
   * @param bool     $echo  TRUE to display or FALSE to get output
   *
   * @return string|void Formatted output if $echo is FALSE
   */
  public static function displayWPError( $error, $echo = true ) {

    _deprecated_function( __FUNCTION__, '0.65', 'WPDKResult' );

    $message = '<div class="wpdk-watchdog-wp-error">';

    if ( is_wp_error( $error ) ) {

      foreach ( $error->errors as $code => $single ) {
        $message .= sprintf( '<code>Code: 0x%x, Description: %s</code>', $code, $single[0] );
        $error_data = $error->get_error_data( $code );
        if ( !empty( $error_data ) ) {
          if ( is_array( $error_data ) ) {
            foreach ( $error_data as $key => $data ) {
              $message .= sprintf( '<code>Key: %s, Data: %s</code>', $key, urldecode( $data ) );
            }
          }
          else {
            $message .= sprintf( '<code>Data: %s</code>', urldecode( $error_data ) );
          }
        }
      }

    }
    else {
      $message .= __( 'No error detected', WPDK_TEXTDOMAIN );
    }

    /* log to file if enabled */
    //self::watchDog( __CLASS__, esc_attr( wp_strip_all_tags( $message ) ) );

    $message .= '</div>';

    if ( $echo ) {
      echo $message;
      return true;
    }
    return $message;
  }

  /**
   * Return the WPDK error type. The WPDK error type is formatted as [type]-[code] or [prefix]_[type]-[code].
   * Example of WPDK error type are:
   *
   *     wpss_warning-too_many_file
   *     warning-too_many_file
   *
   * In both case above, getErrorType() returns ever `warning`
   *
   * @deprecated Use WPDKResult instead
   *
   * @param WP_Error $error WP_Error object
   *
   * @return bool|string WPDK Error type, FALSE if something wrong.
   */
  public static function getErrorType( WP_Error $error ) {
    _deprecated_function( __METHOD__, '0.65', 'WPDKResult' );

    if ( is_wp_error( $error ) ) {
      $code  = $error->get_error_code();
      $parts = explode( '-', $code );
      if ( false !== strpos( $parts[0], '_' ) ) {
        $prefix = explode( '_', $parts[0] );
        return $prefix[1];
      }
      return $parts[0];
    }
    return false;
  }

  /**
   * Return the WPDK error code. The WPDK error code id formatted as [type]-[code] or [prefix]_[type]-[code].
   * Example of WPDK error code are:
   *
   *     wpss_warning-too_many_file
   *     warning-too_many_file
   *
   * In both case above, getErrorCode() returns ever `too_many_file`
   *
   * @deprecated Use WPDKResult instead
   *
   * @param WP_Error $error WP_Error object
   *
   * @return bool|string WPDK Error code, FALSE if something wrong.
   */
  public static function getErrorCode( WP_Error $error ) {
    _deprecated_function( __METHOD__, '0.65', 'WPDKResult' );

    if ( is_wp_error( $error ) ) {
      $code  = $error->get_error_code();
      $parts = explode( '-', $code );
      return $parts[1];
    }
    return false;
  }

  /**
   * This is an alias for getErrorCode(). This method exists only for clean.
   *
   * @deprecated Use WPDKResult instead
   *
   * @param WP_Error $status WP_Error object
   *
   * @return bool|string WPDK Status code, FALSE if something wrong.
   */
  public static function getStatusCode( WP_Error $status ) {
    return self::getErrorCode( $status );
  }

  /**
   * Return TRUE if WPDK error
   *
   * @deprecated Use WPDKResult instead
   *
   * @param WP_Error $error WP_Error object
   *
   * @return bool Restituisce true se il codice è nel formato [prefix]_[error]-[message] o [error]-[message]
   */
  public static function isError( $error ) {
    _deprecated_function( __METHOD__, '0.65', 'WPDKResult' );

    $type = self::getErrorType( $error );
    return ( $type && $type == self::ERROR );
  }

  /**
   * Verifica se status
   *
   * @deprecated Use WPDKResult instead
   *
   * @param WP_Error $error Oggetto errore
   *
   * @return bool Restituisce true se il codice è nel formato [prefix]_[error]-[message] o [error]-[message]
   */
  public static function isStatus( $error ) {
    _deprecated_function( __METHOD__, '0.65', 'WPDKResult' );

    $type = self::getErrorType( $error );
    return ( $type && $type == self::STATUS );
  }

  /**
   * Verifica se warning
   *
   * @deprecated Use WPDKResult instead
   *
   * @param WP_Error $error Oggetto errore
   *
   * @return bool Restituisce true se il codice è nel formato [prefix]_[error]-[message] o [error]-[message]
   */
  public static function isWarning( $error ) {
    _deprecated_function( __METHOD__, '0.65', 'WPDKResult' );

    $type = self::getErrorType( $error );
    return ( $type && $type == self::WARNING );
  }

  /**
   * @deprecated Use WPDKResult instead
   *
   * @param $content
   *
   * @return string
   */
  public static function get_var_dump( $content ) {
    _deprecated_function( __METHOD__, '0.65', 'WPDKResult' );

    ob_start();
    ?>
    <pre><?php var_dump( $content ) ?></pre><?php
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }

  /**
   * Funzione proprietaria per la generazione di un log su disco. Questa crea un file di log in modalità append nella
   * cartella del plugin. Se da un lato le informazioni e la formattazione delle stesse sono a nostra discrezione e
   * completamente personalizzabili, bisogna passare le informazioni di classe, funzione e linea manualmente.
   *
   * In alternativa o in concorrnza è possibile usare trigger_error()
   *
   * @deprecated Use log() instead
   *
   * @param string $class    Nome della classe
   * @param string $txt      (opzionale) Testo libero aggiuntivo. Se omesso viene emessa una riga separatrice
   *
   */
  public static function watchDog( $class, $txt = null ) {

    _deprecated_function( __METHOD__, '0.65', 'log()' );

    if ( defined( 'WPDK_WATCHDOG_DEBUG' ) && WPDK_WATCHDOG_DEBUG ) {

      if ( is_null( $txt ) ) {
        $txt = '---------------------------------------------------------------------------------------------';
      }

      /* Comune su file o sul trigger_error() é */
      $output = sprintf( "[%s] %s: %s\n", date( 'Y-m-d H:i:s' ), $class, $txt );

      if ( defined( 'WPDK_WATCHDOG_DEBUG_ON_FILE' ) && WPDK_WATCHDOG_DEBUG_ON_FILE ) {
        $handle = fopen( WPDK_LOG_FILE, "a+" );
        fwrite( $handle, $output );
        fclose( $handle );
      }

      if ( defined( 'WPDK_WATCHDOG_DEBUG_ON_TRIGGER_ERROR' ) && WPDK_WATCHDOG_DEBUG_ON_TRIGGER_ERROR ) {
        trigger_error( $output );
      }

    }
  }

}

/// @endcond