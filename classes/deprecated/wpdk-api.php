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
 * The wpXtreme API interface
 *
 * @class              WPDKAPI
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-01-21
 * @version            0.8.2
 *
 * @deprecated         Since 1.0.0.b4 - Use WPX version
 *
 */

class WPDKAPI {

  /**
   * Transient ID for token
   *
   * @brief Transient ID
   */
  const TOKEN_KEY = 'token';

  /**
   * Number of seconds to keep token stored in transient
   *
   * @brief Token timeout
   */
  const TOKEN_EXPIRED = 3600;

  /**
   * Timeout connection request
   *
   * @brief Timeout connection
   */
  const CONNECTION_TIMEOUT = 45;

  /**
   * The User Agent request
   *
   * @brief User agent
   */
  const USER_AGENT = 'wpXtreme/1.0';

  /**
   * The wpXtreme API endpoint
   *
   * @brief API endpoint
   */
  const API_ENDPOINT = 'http://server.wpxtre.me/api/';

  /**
   * Who send API.
   *
   * @brief Sender
   *
   * @note Not used yet
   *
   * @var string $sender
   */
  public $sender;


  /**
   * Create an instance of WPDKAPI class
   *
   * @brief Construct
   *
   * @param string $sender Who that send
   *
   * @return WPDKAPI
   */
  public function __construct( $sender = '' ) {
    $this->sender = $sender;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Token
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Store the user token in transient
   *
   * @param string $token The returned token
   */
  public function setToken( $token ) {
    set_transient( self::TOKEN_KEY, $token, self::TOKEN_EXPIRED );
  }

  /**
   * Return the stored user token, FALSE otherwise.
   *
   * @brief Get the user token
   *
   * @return string|bool
   */
  public function getToken() {
    $token = get_transient( self::TOKEN_KEY );
    if ( empty( $token ) ) {
      return false;
    }
    return $token;
  }

  /**
   * Delete user token
   *
   * @brief Delete user token
   */
  public function deleteToken() {
    delete_transient( self::TOKEN_KEY );
  }


  // -----------------------------------------------------------------------------------------------------------------
  // Request
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Do a request to the wpXtreme Server.
   *
   * @brief Request
   *
   * @param string $resource Resource, ie. `profile/user/11`
   * @param array  $args     (Optional) parameters
   * @param string $method   (Optional) default is WPDKAPIMethod::POST
   *
   * @return WPDKAPIResponse|WP_Error|bool
   */
  protected function request( $resource, $args = array(), $method = WPDKAPIMethod::POST ) {
    global $wp_version;

    /* Se passo un singolo parametro ci penso io a renderlo array, gli d'ho una chiave arbitraria 'param' */
    if ( !is_array( $args ) ) {
      $args = array( 'param' => $args );
    }

    /* Get transient secure key. */
    $token = $this->getToken();

    if ( false !== $token ) {
      $args['token'] = $token;
    }

    /* Set referrer */
    $args['referrer'] = WPDKWordPressPlugin::currentURL();

    /* Send version of WordPress, PHP and wpXtreme to server. */
    $args['wp_version']  = $wp_version;
    $args['php_version'] = PHP_VERSION;
    $args['wpx_version'] = WPXTREME_VERSION;

    /* Prepare array for request. */
    $params = array(
      'method'      => $method,
      'timeout'     => self::CONNECTION_TIMEOUT,
      'redirection' => 5,
      'httpversion' => '1.0',
      'user-agent'  => self::USER_AGENT,
      'blocking'    => true,
      'headers'     => array(),
      'cookies'     => array(),
      'body'        => $args,
      'compress'    => false,
      'decompress'  => true,
      'sslverify'   => true,
    );

    if ( !empty( $resource ) ) {
      $endpoint = sprintf( '%s%s', trailingslashit( self::API_ENDPOINT ), $resource );
      $request  = wp_remote_request( $endpoint, $params );

      if ( 200 != wp_remote_retrieve_response_code( $request ) ) {
        return false;
      }

      $response = new WPDKAPIResponse( $request );
      if ( false !== $response->token ) {
        $this->setToken( $response->token );
      }
      return $response;
    }
    return false;
  }


  // -----------------------------------------------------------------------------------------------------------------
  // Utility
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Alter the standard WordPress transient with the plugin list to update. Every single plugin have to check it self
   * for update.
   *
   * @param array $args WPX Plugin info
   *
   *     $args = array(
   *         'action'      => 'update-check',
   *         'plugin_name' => $this->_plugin_slug,
   *         'version'     => $transient->checked[$this->_plugin_slug]
   *     );
   *
   * @see WPDKUpdate::pre_set_site_transient_update_plugins()
   *
   * @return bool|mixed
   */
  public function updatePlugins( $args ) {

    $response = $this->request( WPDKAPIResource::WPX_STORE_PLUGIN_UPDATES, $args );

    /* This is a special response. I do not see the content property but look for original body. */
    if ( !empty( $response->body ) ) {
      if ( is_serialized( $response->body ) ) {
        $response = unserialize( $response->body );

        if ( is_object( $response ) ) {
          return $response;
        }
      }
    }
    return false;
  }

  /**
   * Questo viene utilizzato quando dalla lista dei plugin di WordPress esiste un aggiornamento e si chiedono i
   * dettagli della nuova versione.
   *
   * @static
   *
   * @param array $args
   *
   * @see WPDKUpdate::plugins_api()
   *
   * @return bool|mixed
   */
  public function pluginInformation( $args ) {
    $response = $this->request( WPDKAPIResource::WPX_STORE_PLUGIN_INFORMATION, $args );

    /* This is a special response. I do not see the content property but look for original body. */
    if ( !empty( $response->body ) ) {
      if ( is_serialized( $response->body ) ) {
        $response = unserialize( $response->body );

        if ( is_object( $response ) ) {
          return $response;
        }
      }
    }
    return false;
  }


  // -----------------------------------------------------------------------------------------------------------------
  // Utility
  // -----------------------------------------------------------------------------------------------------------------

}

/**
 * The wpXtreme API response request interface. This class describe the jSON response request interface.
 *
 * @class              WPDKAPI
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.9.0
 *
 * @deprecated         Since 1.0.0.b4 - Use WPX version
 *
 */
class WPDKAPIResponse {

  /**
   * The user token
   *
   * @brief User token
   *
   * @var string $token;
   */
  public $token;

  /**
   * The content response
   *
   * @brief Content
   *
   * @var string $content
   */
  public $content;

  /**
   * An error
   *
   * @brief Error
   *
   * @var string $error
   */
  public $error;

  /**
   * Original body
   *
   * @brief Body of request response
   *
   * @var string $json
   */
  public $body;

  /**
   * A key value pairs array with your own extra data
   *
   * @brief User data params
   *
   * @var array $params
   */
  public $params;

  /**
   * Create an instance of WPDKAPIResponse class
   *
   * @brief Construct
   *
   * @param $response (Optional) default null. If null you can create a response custom object
   *
   * @return WPDKAPIResponse
   */
  public function __construct( $response = null ) {

    $this->token = false;

    if ( !is_null( $response ) ) {
      $body = wp_remote_retrieve_body( $response );
      if ( !empty( $body ) ) {
        $this->body = $body;
        /**
         * @var WPDKAPIResponse $json_decode
         */
        $json_decode = json_decode( $body );
        if ( is_object( $json_decode ) && empty( $json_decode->error ) ) {
          $this->content = isset( $json_decode->content ) ? $json_decode->content : '';
          $this->error   = isset( $json_decode->error ) ? $json_decode->error : '';
          $this->token   = isset( $json_decode->token ) ? $json_decode->token : false;
          $this->params  = isset( $json_decode->params ) ? $json_decode->params : '';
        }
      }
    }
  }
}


/**
 * The wpXtreme API REST methods
 *
 * @class              WPDKAPIMethod
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 * @deprecated         Since 1.0.0.b4 - Use WPX version
 *
 */
class WPDKAPIMethod {

  const POST   = 'POST';
  const GET    = 'GET';
  const PUT    = 'PUT';
  const DELETE = 'DELETE';
}


/**
 * The wpXtreme API REST error code
 *
 * @class              WPDKAPIErrorCode
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 * @deprecated         Since 1.0.0.b4 - Use WPX version
 *
 */
class WPDKAPIErrorCode {

  /**
   * @brief Unrecognized resource
   */
  const UNRECOGNIZE_RESOURCE = '#80001000';
}


/**
 * The wpXtreme API REST resource
 *
 * @class              WPDKAPIResource
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 * @deprecated         Since 1.0.0.b4 - Use WPX version
 *
 */
class WPDKAPIResource {

  // -----------------------------------------------------------------------------------------------------------------
  // WPX Store
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Get the HTML markup for the entire WPX Store plugin, comprenive of tabs
   *
   * @brief GET 'store/plugin'
   */
  const WPX_STORE_PLUGIN = 'store/plugin';

  /**
   * Get the HTML markup for the WPX Store plugin showcase only
   *
   * @brief GET 'store/plugin/showcase'
   */
  const WPX_STORE_PLUGIN_SHOWCASE = 'store/plugin/showcase';

  /**
   * GET 'store/plugin_information'
   *
   * Get the plugin information in standard WordPress
   *
   * @brief Plugin information
   */
  const WPX_STORE_PLUGIN_INFORMATION = 'store/plugin_information';

  /**
   * GET 'store/plugin_update'
   *
   * Set the plugin information in standard WordPress
   *
   * @brief Plugin update information
   * @since 1.0.0.b3
   */
  const WPX_STORE_PLUGIN_UPDATE = 'store/plugin_update';

  /**
   * Get the plugin download url
   *
   * @brief GET 'store/plugin_download_url'
   */
  const WPX_STORE_PLUGIN_DOWNLOAD_URL = 'store/plugin_download_url';

  /**
   * Download plugin
   *
   * @brief GET 'store/plugin_download'
   */
  const WPX_STORE_PLUGIN_DOWNLOAD = 'store/plugin_download';

  /**
   * Get the updates WPX plugin list
   *
   * @brief GET 'store/plugin/updates'
   */
  const WPX_STORE_PLUGIN_UPDATES = 'store/plugin/updates';

  /**
   * Get the WPX plugin card
   *
   * @brief GET 'store/plugin/card'
   */
  const WPX_STORE_PLUGIN_CARD = 'store/plugin/card';

  /**
   * Get the HTML markup for toolbar with login/profile
   *
   * @brief GET 'store/toolbar_profile'
   */
  const WPX_STORE_TOOLBAR_PROFILE = 'store/toolbar_profile';

  /**
   * Do signin
   *
   * @brief GET 'user/signin'
   *
   */
  const USER_SIGNIN = 'user/signin';

  /**
   * Get (internal) iframe status after reload
   *
   * @brief GET 'iframe/status'
   *
   */
  const IFRAME_STATUS = 'iframe/status';
}

/// @endcond