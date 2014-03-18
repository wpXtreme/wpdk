<?php

/**
 * Manage the Javascript/css components under the WPDK assets folder.
 * You can override this class for register and manage your own components
 *
 *     class MyComponents extends WPDKUIComponents {
 *
 *         const MY_COMPONENT_ID = 'my-component-id';
 *
 *         public function components()
 *        {
 *           $components = array(
 *             'url_js'        => 'your url javascript',
 *             'url_css'       => 'your url css',
 *             'version'       => 'your plugin version',

 *             'components'    => array(
 *                self::MY_COMPONENT_ID => array(
 *                  'has_js'  => array( // deps ),
 *                  'has_css' => array( // deps ),
 *                ),
 *                // Other components
 *              ),
 *           );
 *        }
 *     }
 *
 * @class           WPDKUIComponents
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-03-03
 * @version         1.0.2
 *
 */
class WPDKUIComponents {

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
      $instance = new self;
    }
    return $instance;
  }

  /**
   * Create an instance of WPDKUIComponents class
   *
   * @brief Construct
   *
   * @param string $url_js  Optional. URL Javascript
   * @param string $url_css Optional. URL CSS styles
   * @param string $version Optional. Your version
   *
   * @return WPDKUIComponents
   */
  public function __construct()
  {
    $this->register();
  }

  /**
   * Register the components
   *
   * @brief Brief
   */
  protected function register()
  {
    // Get components info
    $component_info = $this->components();

    // Get urls
    $url_js   = $component_info['url_js'];
    $url_jcss = $component_info['url_css'];
    $version  = $component_info['version'];

    // Get components list
    $components = $component_info['components'];

    // Register WPDK Javascript components
    foreach ( $components as $handle => $libs ) {
      foreach ( $libs as $extension => $deps ) {

        // Script
        if ( 'has_js' == $extension ) {
          $filename = sprintf( '%s%s.js', $url_js, $handle );
          wp_register_script( $handle, $filename, $deps, $version, true );
        }

        // Styles
        elseif ( 'has_css' == $extension ) {
          $filename = sprintf( '%s%s.css', $url_jcss, $handle );
          wp_register_style( $handle, $filename, $deps, $version );
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
  public function components()
  {
    $components = array(

      // Components info
      'url_js'     => WPDK_URI_JAVASCRIPT,
      'url_css'    => WPDK_URI_CSS,
      'version'    => WPDK_VERSION,

      // Components list
      'components' => array(

        self::CONTROLS      => array(
          'has_js'  => array(),
          'has_css' => array()
        ),
        self::ALERT         => array(
          'has_js'  => array(
            self::CONTROLS,
            self::TRANSITION
          ),
          'has_css' => array( self::CONTROLS )
        ),
        self::DYNAMIC_TABLE => array(
          'has_js'  => array(
            self::CONTROLS,
            self::TOOLTIP
          ),
          'has_css' => array(
            self::CONTROLS,
            self::TOOLTIP
          )
        ),
        self::TOOLTIP       => array(
          'has_js'  => array( self::TRANSITION ),
          'has_css' => array()
        ),
        self::TRANSITION    => array(
          'has_js' => array(),
        ),
        self::BUTTON        => array(
          'has_js'  => array( self::CONTROLS ),
          'has_css' => array( self::CONTROLS )
        ),
        self::RIBBONIZE     => array(
          'has_js'  => array(),
          'has_css' => array()
        ),
        self::POPOVER       => array(
          'has_js'  => array(
            self::CONTROLS,
            self::TOOLTIP
          ),
          'has_css' => array(
            self::CONTROLS,
            self::TOOLTIP
          )
        ),
        self::MODAL         => array(
          'has_js'  => array(
            self::CONTROLS,
            self::BUTTON,
            self::TRANSITION
          ),
          'has_css' => array(
            self::CONTROLS,
            self::BUTTON
          )
        ),
        self::PROGRESS      => array(
          'has_css' => array(),
        ),
        // Internal - without css
        self::PREFERENCES   => array(
          'has_js' => array( self::CONTROLS ),
        ),
      )
    );

    return $components;
  }

  /**
   * Perform an enqueue of scripts and styles of a one or more components
   *
   *     // Load a single components
   *     WPDKUIComponents::init()->enqueue( WPDKUIComponents::TOOLTIP );
   *
   *     // Load one or more components
   *     WPDKUIComponents::init()->enqueue( array( WPDKUIComponents::MODAL, WPDKUIComponents::TOOLTIP ) );
   *
   *     // Or... like kind of magic
   *     WPDKUIComponents::init()->enqueue( WPDKUIComponents::MODAL, WPDKUIComponents::TOOLTIP );
   *
   * @brief Enqueue component
   * @since 1.5.0
   *
   * @param string|array $component_handles One or more Component handle
   *
   */
  public function enqueue( $component_handles )
  {
    // Get components list
    $component_info = $this->components();

    // Get components list
    $components = $component_info['components'];

    // Handles, one or more
    $handles = (array)$component_handles;

    // Magic
    if( func_num_args() > 1 ) {
      $handles = func_get_args();
    }

    // Loop
    foreach( $handles as $handle ) {

      // Exists this component?
      if ( in_array( $handle, array_keys( $components ) ) ) {

        // Get scripts and styles
        $component = $components[$handle];

        // Load scripts?
        if ( isset( $component['has_js'] ) ) {
          wp_enqueue_script( $handle );
        }

        // Load styles?
        if ( isset( $component['has_css'] ) ) {
          wp_enqueue_style( $handle );
        }
      }
    }
  }

}