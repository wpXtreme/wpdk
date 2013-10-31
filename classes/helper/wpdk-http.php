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
 * Standard HTTP verbs
 *
 * @class           WPDKHTTPVerbs
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-10-31
 * @version         1.0.0
 * @since           1.3.1
 *
 */
class WPDKHTTPVerbs {

  const DELETE = 'DELETE';
  const GET    = 'GET';
  const POST   = 'POST';
  const PUT    = 'PUT';
  const PATCH  = "PATCH";

  /**
   * Return the key-value array filtered request methods
   *
   * @brief Request methods
   */
  public function requestMethods()
  {
    /* Standard default verbs. */
    $verbs = array(
      self::POST   => self::POST,
      self::GET    => self::GET,
      self::DELETE => self::DELETE,
      self::PUT    => self::PUT,
    );

    return apply_filters( 'wpdk_http_verbs', $verbs );
  }
}

/**
 * HTTP Request helper class
 *
 * @class           WPDKHTTPRequest
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-10-31
 * @version         1.0.0
 * @since           1.3.1
 *
 */
class WPDKHTTPRequest {

  /**
   * Return TRUE if we are called by Ajax. Used to be sure that we are responding to an HTTPRequest request and that
   * the WordPress define DOING_AJAX is defined.
   *
   * @brief Ajax validation
   *
   * @return bool TRUE if Ajax trusted
   */
  public static function isAjax()
  {
    if ( defined( 'DOING_AJAX' ) ) {
      return true;
    }
    if ( isset( $_SERVER['HTTP_X_REQUESTED_WITH'] ) &&
      strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest'
    ) {
      return true;
    }
    else {
      return false;
    }
  }

  /**
   * Return true if the $verb param in input match with REQUEST METHOD
   *
   * @brief Check request
   *
   * @param string $verb The verb, for instance; GET, WPDKHTTPVerbs::GET, delete, etc...
   *
   * @return bool
   */
  public static function isRequest( $verb )
  {
    $verb = strtolower( $verb );
    return ( $verb == strtolower( $_SERVER['REQUEST_METHOD'] ) );
  }

  /**
   * Return true if the REQUEST METHOD is GET
   *
   * @brief Check if request is get
   *
   * @return bool
   */
  public static function isRequestGET()
  {
    return self::isRequest( WPDKHTTPVerbs::GET );
  }

  /**
   * Return true if the REQUEST METHOD is POST
   *
   * @brief Check if request is POST
   *
   * @return bool
   */
  public static function isRequestPOST()
  {
    return self::isRequest( WPDKHTTPVerbs::POST );
  }


}


/// @endcond