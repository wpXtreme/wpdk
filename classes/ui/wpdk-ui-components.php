<?php

/**
 * Manage the Javascript/css components
 *
 * @class           WPDKUIComponents
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-02-08
 * @version         1.0.0
 *
 */
final class WPDKUIComponents {

  /*
   * The unique scritpt and style id (id `wpdk-my-componenent` you have to any
   * `wpdk-my-componenent.js` or `wpdk-my-componenent.css`
   */
  const ALERT         = 'wpdk-alert';
  const BUTTON        = 'wpdk-button';
  const CONTROLS      = 'wpdk-controls';
  const DYNAMIC_TABLE = 'wpdk-dynamic-table';
  const MODAL         = 'wpdk-modal';
  const POPOVER       = 'wpdk-popover';
  const PREFERENCES   = 'wpdk-preferences';
  const PROGRESS      = 'wpdk-progress';
  const RIBBONIZE     = 'wpdk-ribbonize';
  const TOOLTIP       = 'wpdk-tooltip';
  const TRANSITION    = 'wpdk-transition';

  /**
   * Create an instance of WPDKUIComponents class
   *
   * @brief Construct
   *
   * @return WPDKUIComponents
   */
  public function __construct()
  {
    // Register WPDK Javascript components
    foreach ( $this->components() as $handle => $libs ) {
      foreach ( $libs as $extension => $deps ) {

        // Script
        if ( '.js' == $extension ) {
          $filename = sprintf( '%s%s%s', WPDK_URI_JAVASCRIPT, $handle, $extension );
          wp_register_script( $handle, $filename, $deps, WPDK_VERSION, true );
        }

        // Styles
        else if ( '.css' == $extension ) {
          $filename = sprintf( '%s%s%s', WPDK_URI_CSS, $handle, $extension );
          wp_register_style( $handle, $filename, $deps, WPDK_VERSION );
        }
      }
    }

  }

  /**
   * Return the WPDK components list
   *
   * @brief Components
   *
   * @return array
   */
  private function components()
  {
    $components = array(
      self::CONTROLS      => array(
        '.js'  => array(),
        '.css' => array()
      ),
      self::ALERT         => array(
        '.js'  => array( self::CONTROLS, self::TRANSITION ),
        '.css' => array( self::CONTROLS )
      ),
      self::DYNAMIC_TABLE => array(
        '.js'  => array( self::CONTROLS ),
        '.css' => array( self::CONTROLS )
      ),
      self::TOOLTIP       => array(
        '.js'  => array( self::TRANSITION ),
        '.css' => array()
      ),
      self::TRANSITION    => array(
        '.js' => array(),
      ),
      self::BUTTON        => array(
        '.js'  => array( self::CONTROLS ),
        '.css' => array( self::CONTROLS )
      ),
      self::RIBBONIZE     => array(
        '.js'  => array(),
        '.css' => array()
      ),
      self::POPOVER       => array(
        '.js'  => array( self::CONTROLS, self::TOOLTIP ),
        '.css' => array( self::CONTROLS )
      ),
      self::MODAL         => array(
        '.js'  => array( self::CONTROLS, self::BUTTON, self::TRANSITION ),
        '.css' => array( self::CONTROLS, self::BUTTON ) ),
      self::PROGRESS      => array(
        '.css' => array(),
      ),
      // Internal - without css
      self::PREFERENCES   => array(
        '.js' => array( self::CONTROLS ),
      ),
    );

    return $components;
  }

  /**
   * Return a singleton instance of WPDKUIComponents class
   *
   * @brief Singleton
   *
   * @return WPDKUIComponents
   */
  public static function init()
  {
    static $instance = null;
    if ( is_null( $instance ) ) {
      $instance = new WPDKUIComponents();
    }
    return $instance;
  }

}