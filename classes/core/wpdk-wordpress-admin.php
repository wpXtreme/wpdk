<?php
/**
 * Manage and init WordPress backend admin area
 *
 * ## Overview
 * This class is used when the backend is loaded. You can subclassing this class for get a lot of facilities when you
 * have to manage the theme interactions.
 *
 * ### Benefits
 * This class prepare for us some useful and common action/filter hook.
 *
 * @class              WPDKWordPressAdmin
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKWordPressAdmin {

  /**
   * List of CSS class to add to body
   *
   * @brief List of class for body
   *
   * @var array $bodyClasses
   */
  public $bodyClasses;

  /**
   * Parent base class
   *
   * @brief Parent base class
   *
   * @var WPDKWordPressPlugin $plugin
   */
  public $plugin;

  /**
   * Create a WPDKWordPressAdmin object instance
   *
   * @brief Construct
   *
   * @param WPDKWordPressPlugin $plugin Your main plugin instance
   *
   * @return WPDKWordPressAdmin
   */
  public function __construct( WPDKWordPressPlugin $plugin ) {
    $this->plugin = $plugin;

    /* Admin page is loaded */
    if ( is_multisite() ) {
      add_action( 'network_admin_menu', array( $this, 'admin_menu' ) );
    }
    else {
      add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }

    /* Register this plugin in body. */
    add_filter( 'admin_body_class', array( $this, '_admin_body_class' ) );

    /* Loading Script & style for backend */
    add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
  }

  // -----------------------------------------------------------------------------------------------------------------
  // WordPress Hook
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Used for load scrit and styles
   *
   * @brief Admin backend area head
   */
  public function admin_enqueue_scripts() {
    /* WordPress Pointer. */
    wp_enqueue_style( 'wp-pointer' );
    wp_enqueue_script( 'wp-pointer' );
  }

  /**
   * This is an internal method to build the extra class for body tag.
   *
   * @brief Add extra class in body
   *
   * @param array $classes Extra classes list
   *
   * @return string
   */
  public function _admin_body_class( $classes ) {
    if ( !empty( $this->bodyClasses ) ) {
      $stack = array();
      foreach ( $this->bodyClasses as $key => $enabled ) {
        if ( true == $enabled ) {
          $stack[] = $key;
        }
      }
      $classes .= ' ' . join( ' ', $stack );
    }
    return $classes;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Methods to override
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * You will use this method for create the WordPress admin menu
   *
   * @brief WP Hook when menus are building
   *
   * @note To override
   *
   */
  public function admin_menu() {
    /* To override. */
  }

}