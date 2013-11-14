<?php
/// @cond private

if ( wpdk_is_ajax() ) {

  /**
   * Ajax class for extends an Ajax parent class.
   * You will use this class to extends a your own Ajax gateway class.
   *
   *     class YourClass extends WPDKAjax {}
   *
   * In this way you can access to `registerActions` method
   *
   * @class              WPDKAjax
   * @author             =undo= <info@wpxtre.me>
   * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
   * @date               2013-10-31
   * @version            1.0.2
   * @since              0.7.5
   *
   */
  class WPDKAjax extends WPDKObject {

    /**
     * Override version
     *
     * @brief Version
     *
     * @var string $version
     */

    public $version = '1.0.2';

    /**
     * Create an instance of WPXCleanFixAjax class
     *
     * @brief Construct
     *
     * @return WPXCleanFixAjax
     */
    public function __construct()
    {
      $this->registerActions();
    }

    /**
     * Register the allow ajax method in WordPress environment
     *
     * @brief Register the ajax methods
     *
     */
    public function registerActions()
    {
      $actions = $this->actions();
      foreach ( $actions as $method => $nopriv ) {
        add_action( 'wp_ajax_' . $method, array( $this, $method ) );
        if ( $nopriv ) {
          add_action( 'wp_ajax_nopriv_' . $method, array( $this, $method ) );
        }
      }
    }

    /**
     * Useful static method to add an action ajax hook
     *
     * @brief Add an Ajax hook
     * @since 1.3.0
     *
     * @param string   $method   Method name, eg: wpxkk_action_replace
     * @param callback $callable A callable function/method hook
     * @param bool     $nopriv   Set to TRUE for enable no privilege
     */
    public static function add( $method, $callable, $nopriv = false )
    {
      add_action( 'wp_ajax_' . $method, $callable );
      if ( $nopriv ) {
        add_action( 'wp_ajax_nopriv_' . $method, $callable );
      }
    }

    /**
     * Return the array list with allowed method. This is a Key value pairs array with value for not signin user ajax
     * method allowed.
     *
     * @brief Ajax actions list
     *
     * @return array
     */
    protected function actions()
    {
      /* To override. */
      return array();
    }

  } // class WPDKAjax


  /**
   * A WPDK (utility) Ajax Response class model
   *
   * @class           WPDKAjaxResponse
   * @author          =undo= <info@wpxtre.me>
   * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
   * @date            2013-11-11
   * @version         1.0.0
   * @since           1.4.0
   *
   */
  class WPDKAjaxResponse {

    /**
     * User define error code or string
     *
     * @brief Error
     *
     * @var string $error
     */
    public $error = '';

    /**
     * Usually an alert message feedback
     *
     * @brief Message
     *
     * @var string $message
     */
    public $message = '';

    /**
     * Any data
     *
     * @brief Data
     *
     * @var string $data
     */
    public $data = '';

    /**
     * Create an instance of WPDKAjaxResponse class
     *
     * @brief Construct
     *
     * @return WPDKAjaxResponse
     */
    public function __construct()
    {
    }

    /**
     * Send a jSON response
     *
     * @brief jSON
     */
    public function json()
    {
      header( 'Cache-Control: no-cache, must-revalidate' );
      header( 'Expires: Mon, 26 Jul 1997 05:00:00 GMT' );
      header( 'Content-Type: application/json' );

      echo json_encode( $this );

      if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        wp_die();
      }
      else {
        die();
      }
    }

  }


}
/// @endcond