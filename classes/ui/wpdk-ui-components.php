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
  const DYNAMIC_TABLE = 'wpdk-dynamic-table';
  const POPOVER       = 'wpdk-popover';
  const TOOLTIP       = 'wpdk-tooltip';

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

    // Dynamic Table
    wp_register_script( self::DYNAMIC_TABLE, WPDK_URI_JAVASCRIPT . self::DYNAMIC_TABLE . '.js', array(), WPDK_VERSION, true );
    wp_register_style( self::DYNAMIC_TABLE, WPDK_URI_CSS . self::DYNAMIC_TABLE . '.css', array(), WPDK_VERSION );

    // Tooltip
    wp_register_script( self::TOOLTIP, WPDK_URI_JAVASCRIPT . self::TOOLTIP . '.js', array(), WPDK_VERSION, true );
    wp_register_style( self::TOOLTIP, WPDK_URI_CSS . self::TOOLTIP . '.css', array(), WPDK_VERSION );

    // Popover
    wp_register_script( self::POPOVER, WPDK_URI_JAVASCRIPT . self::POPOVER . '.js', array( self::TOOLTIP ), WPDK_VERSION, true );
    wp_register_style( self::POPOVER, WPDK_URI_CSS . self::POPOVER . '.css', array(), WPDK_VERSION );
  }

}