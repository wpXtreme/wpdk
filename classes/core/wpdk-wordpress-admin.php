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
 * @date               2014-01-08
 * @version            0.9.0
 *
 */
class WPDKWordPressAdmin extends WPDKObject {

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $__version
   */
  public $__version = '0.9.0';

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
  public function __construct( WPDKWordPressPlugin $plugin )
  {
    // Save plugin
    $this->plugin = $plugin;

    // Admin page is loaded
    if ( is_multisite() ) {
      add_action( 'network_admin_menu', array( $this, 'admin_menu' ) );
    }
    else {
      // Let's add menu at last
      add_action( 'admin_menu', array( $this, 'admin_menu' ), 99 );
    }

    // Register this plugin in body.
    add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );

    // Loading Script & style for backend
    add_action( 'admin_enqueue_scripts', array( $this, 'wp_pointer' ) );
    add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

  }

  /**
   * Used for load wp pointer scripts & styles
   *
   * @brief WP Pointer
   */
  public function wp_pointer()
  {
    // WordPress Pointer
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
  public function admin_body_class( $classes )
  {
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

  // -------------------------------------------------------------------------------------------------------------------
  // Methods to override
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * You will use this method for create the WordPress admin menu
   *
   * @brief WP Hook when menus are building
   */
  public function admin_menu()
  {
    /* Override. */
  }

  /**
   * Used for load scripts and styles in admin backend area.
   *
   * @brief Admin backend area head
   *
   * @param string $hook_suffix Hook suffix
   */
  public function admin_enqueue_scripts( $hook_suffix )
  {
    /* Override */
  }

}