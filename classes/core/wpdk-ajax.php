<?php
/// @cond private

if ( wpdk_is_ajax() ) {

  /**
   * Ajax class for extends an Ajax parent class.
   * You will use this class to extends a your own Ajax gateway class.
   *
   *     class YouClass extends WPDKAjax {}
   *
   * In this way you can access to `registerActions` method
   *
   * @class              WPDKAjax
   * @author             =undo= <info@wpxtre.me>
   * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
   * @date               2012-11-28
   * @version            0.5
   * @since              0.7.5
   *
   */
  class WPDKAjax {

    /**
     * Create an instance of WPXCleanFixAjax class
     *
     * @brief Construct
     *
     * @return WPXCleanFixAjax
     */
    public function __construct() {
      $this->registerActions();
    }

    /**
     * Register the allow ajax method in WordPress environment
     *
     * @brief Register the ajax methods
     *
     */
    public function registerActions() {
      $actions = $this->actions();
      foreach ( $actions as $method => $nopriv ) {
        add_action( 'wp_ajax_' . $method, array( $this, $method ) );
        if ( $nopriv ) {
          add_action( 'wp_ajax_nopriv_' . $method, array( $this, $method ) );
        }
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
    protected function actions() {
      /* To override. */
      return array();
    }

  } // class WPDKAjax
}
/// @endcond