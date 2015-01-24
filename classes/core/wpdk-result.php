<?php

/**
 * The WPDKResultType defines the available WPDK result type.
 *
 * @class              WPDKResultType
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-08-13
 * @version            1.0.0
 *
 */
class WPDKResultType {
  const ERROR   = 'error';
  const WARNING = 'warning';
  const STATUS  = 'status';
}


/**
 * The WPDKResult is an extension of WP_Error class. This class is used for subclass the three state of WPDK result.
 * You rarely instance this class directly. The WPDK provides three classes for manage error, warning and status
 * information feedback. Please check in wpdk-functions.php for `is_wpdk_error()` too.
 *
 * @class              WPDKResult
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 */
class WPDKResult extends WP_Error {

  /**
   * Create an instance of WPDKError class
   *
   * @brief Construct
   *
   * @param string $code    A lowercase string code id, ie. wpxcf_no_user_found
   * @param string $type    A WPDKResultType, default WPDKResultType::STATUS
   * @param string $message Error message
   * @param mixed  $data    Optional. Error data.
   *
   * @return WPDKResult
   */
  public function __construct( $code, $type = WPDKResultType::STATUS, $message = '', $data = '' )
  {
    $new_code      = sprintf( '%s-%s', $type, $code );
    $sanitize_code = $this->sanitizeCode( $new_code );
    parent::__construct( $sanitize_code, $message, $data );
  }

  /**
   * Display or return a formatted output for WP_Error object. For each error in WP_Error object a formatted
   * output is done with code error and error data.
   *
   * @brief Display ot return output
   *
   * @param bool         $echo TRUE to display or FALSE to get output
   * @param WPDKWatchDog $log  A WPDKWatchDog object to log the display
   *
   * @return string|void Formatted output if $echo is FALSE
   */
  public function display( $echo = true, $log = null )
  {
    $content = '<div class="wpdk-watchdog-wp-error">';

    foreach( $this->errors as $code => $single ) {
      $content .= sprintf( '<code>Code: 0x%x, Description: %s</code>', $code, $single[ 0 ] );
      $error_data = $this->get_error_data( $code );
      if( !empty( $error_data ) ) {
        if( is_array( $error_data ) ) {
          foreach( $error_data as $key => $data ) {
            $content .= sprintf( '<code>Key: %s, Data: %s</code>', $key, urldecode( $data ) );
          }
        }
        else {
          $content .= sprintf( '<code>Data: %s</code>', urldecode( $error_data ) );
        }
      }
    }

    // log to file if enabled
    if( !is_null( $log ) ) {
      $log->log( esc_attr( wp_strip_all_tags( $content ) ) );
    }

    $content .= '</div>';

    if( $echo ) {
      echo $content;

      return true;
    }

    return $content;
  }

  /**
   * Return the var_dump of mixed input
   *
   * @brief Do a var_dump
   *
   * @param mixed $content Mixed variable to grab
   *
   * @return string
   */
  public function getVarDump( $content )
  {
    ob_start();
    ?>
    <pre><?php var_dump( $content ) ?></pre><?php
    $content = ob_get_contents();
    ob_end_clean();

    return $content;
  }

  /**
   * The code must be a lowercase string with underscore separation.
   *
   * @brief Sanitize code id
   *
   * @param string $code
   *
   * @return string
   */
  protected function sanitizeCode( $code )
  {
    $replace = array(
      ' ' => '_',
      '-' => '_'
    );
    $code    = strtr( $code, $replace );

    return strtolower( $code );
  }


  /**
   * Return true if $thing is an object of $class class name
   *
   * @brief Generic object compare
   *
   * @param mixed  $thing Check if unknown variable is WPDKError object.
   * @param string $class Class name
   *
   * @return bool
   */
  protected static function _is( $thing, $class = 'WPDKError' )
  {
    if( is_object( $thing ) && is_a( $thing, $class ) ) {
      return true;
    }

    return false;
  }

  /**
   * Looks at the object and if a WPDKError class. Does not check to see if the parent is also WPDKError or a
   * WPDKResult, so can't inherit both the classes and still use this function.
   *
   * @brief Check whether variable is a WPDK result error.
   * @since 1.2.0
   *
   * @param mixed $thing Check if unknown variable is WPDKError object.
   *
   * @return bool TRUE, if WPDKError. FALSE, if not WPDKError.
   */
  public static function isError( $thing )
  {
    return self::_is( $thing );
  }

  /**
   * Looks at the object and if a WPDKWarning class. Does not check to see if the parent is also WPDKWarning or a
   * WPDKResult, so can't inherit both the classes and still use this function.
   *
   * @brief Check whether variable is a WPDK result warning.
   * @since 1.2.0
   *
   * @param mixed $thing Check if unknown variable is WPDKWarning object.
   *
   * @return bool TRUE, if WPDKWarning. FALSE, if not WPDKWarning.
   */
  public static function isWarning( $thing )
  {
    return self::_is( $thing, 'WPDKWarning' );
  }

  /**
   * Looks at the object and if a WPDKStatus class. Does not check to see if the parent is also WPDKStatus or a
   * WPDKResult, so can't inherit both the classes and still use this function.
   *
   * @brief Check whether variable is a WPDK result status.
   * @since 1.2.0
   *
   * @param mixed $thing Check if unknown variable is WPDKStatus object.
   *
   * @return bool TRUE, if WPDKStatus. FALSE, if not WPDKStatus.
   */
  public static function isStatus( $thing )
  {
    return self::_is( $thing, 'WPDKStatus' );
  }

}


/**
 * The WPDKError is an extension of WP_Error class
 *
 * @class              WPDKError
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKError extends WPDKResult {

  /**
   * Create an instance of WPDKError class
   *
   * @brief Construct
   *
   * @param string $code    Error code, ie. wpxss_to_many_files
   * @param string $message Error message
   * @param mixed  $data    Optional. Error data.
   *
   * @return WPDKError
   */
  public function __construct( $code, $message = '', $data = '' )
  {
    parent::__construct( $code, WPDKResultType::ERROR, $message, $data );
  }

}

/**
 * The WPDKWarning is an extension of WP_Error class
 *
 * @class              WPDKWarning
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKWarning extends WPDKResult {

  /**
   * Create an instance of WPDKError class
   *
   * @brief Construct
   *
   * @param string $code    Error code, ie. wpxss_to_many_files
   * @param string $message Error message
   * @param mixed  $data    Optional. Error data.
   *
   * @return WPDKWarning
   */
  public function __construct( $code, $message = '', $data = '' )
  {
    parent::__construct( $code, WPDKResultType::WARNING, $message, $data );
  }

}

/**
 * The WPDKStatus is an extension of WP_Error class
 *
 * @class              WPDKStatus
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 */
class WPDKStatus extends WPDKResult {

  /**
   * Create an instance of WPDKError class
   *
   * @brief Construct
   *
   * @param string $code    Error code, ie. wpxss_to_many_files
   * @param string $message Error message
   * @param mixed  $data    Optional. Error data.
   *
   * @return WPDKStatus
   */
  public function __construct( $code, $message = '', $data = '' )
  {
    parent::__construct( $code, WPDKResultType::STATUS, $message, $data );
  }
}