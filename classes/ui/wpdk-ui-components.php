<?php

/**
 * Manage the Javascript/css components under the WPDK assets folder.
 * You can override this class for register and manage your own components
 *
 * @class           WPDKUIComponents
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2015 wpXtreme Inc. All Rights Reserved.
 * @date            2015-01-12
 * @version         1.0.7
 *
 * @history         1.0.6 - Added fonts
 * @history         1.0.7 - Added browser detect for `wp-color-picker` support.
 *
 */
final class WPDKUIComponents {

  // Main core
  const WPDK = 'wpdk';

  /*
   * The unique scritpt and style id (eg: `{id-handle}.js` or `{id-handle}.css`
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
  const LIST_TABLE    = 'wpdk-list-table';
  const TABLE         = 'wpdk-table';
  const PAGE          = 'wpdk-page';
  const FONTS         = 'wpdk-fonts';

  // jQuery
  const JQUERY_TIMEPICKER = 'jquery.timepicker';
  const JQUERY_UI_CUSTOM  = 'jquery-ui.custom';

  /**
   * List of components
   *
   * @since 1.6.0
   *
   * @var array $components
   */
  public $components = array();

  /**
   * List of scripts handle to load and concat with new WPDK `wpdk-load-scripts-php`
   *
   * @var array $enqueue_scripts
   */
  public $enqueue_scripts = array();

  /**
   * List of styles handle to load and concat with new WPDK `wpdk-load-styles-php`
   *
   * @var array $enqueue_styles
   */
  public $enqueue_styles = array();

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

      // @since 1.6.0 - You can now access to this singleton class by global
      $GLOBALS[ __CLASS__ ] = $instance;
    }
    return $instance;
  }

  /**
   * Create an instance of WPDKUIComponents class
   *
   * @return WPDKUIComponents
   */
  public function __construct()
  {
    // Store the components
    $this->components = $this->components();

    // Fires in <head> for all admin pages.
    // Use priority 100 in order to load all registered CSS from view controller.
    add_action( 'admin_head', array( $this, 'load_styles' ), 100 );

    // Fires before styles in the $handles queue are printed.
    // Use priority 100 in order to load all registered CSS from view controller.
    add_action( 'wp_head', array( $this, 'load_styles' ), 100 );

    // Prints any scripts and data queued for the footer admin and frontned.
    add_filter( 'print_footer_scripts', array( $this, 'load_scripts' ) );
  }

  /**
   * Fires in <head> for all admin pages.
   *
   * @since WP 2.1.0
   */
  public function load_styles()
  {
    global $compress_css;
    global $wp_scripts, $wp_styles, $concatenate_scripts;

    $wp_styles->do_concat = $wp_scripts->do_concat = $concatenate_scripts = true;

    // If no scripts exit
    if ( empty( $this->enqueue_styles ) ) {
      return;
    }

    $zip = $compress_css ? 1 : 0;
    if ( $zip && defined( 'ENFORCE_GZIP' ) && ENFORCE_GZIP ) {
      $zip = 'gzip';
    }

    $concat = implode( ',', $this->enqueue_styles );
    $concat = str_split( $concat, 128 );
    $concat = 'load%5B%5D=' . implode( '&load%5B%5D=', $concat );

    $src = WPDK_URI . "wpdk-load-styles.php?" . $concat . '&ver=' . WPDK_VERSION;
    echo "<link rel='stylesheet' id='wpdk-css-loader' type='text/css' href='" . esc_attr( $src ) . "'/>\n";
  }

  /**
   * Filter whether to print the footer scripts.
   *
   * @since WP 2.8.0
   *
   * @param bool $print Whether to print the footer scripts. Default true.
   */
  public function load_scripts( $print )
  {

    global $wp_scripts, $compress_scripts;

    // If no scripts exit
    if( empty( $this->enqueue_scripts ) ) {
      return $print;
    }

    $zip = $compress_scripts ? 1 : 0;
    if( $zip && defined( 'ENFORCE_GZIP' ) && ENFORCE_GZIP ) {
      $zip = 'gzip';
    }

    $concat = implode( ',', $this->enqueue_scripts );
    $concat = str_split( $concat, 128 );
    $concat = 'load%5B%5D=' . implode( '&load%5B%5D=', $concat );

    $src                    = WPDK_URI . "wpdk-load-scripts.php?" . $concat . '&ver=' . WPDK_VERSION;
    $wp_scripts->print_html = "<script type='text/javascript' src='" . esc_attr( $src ) . "'></script>\n" . $wp_scripts->print_html;

    return $print;
  }

  /**
   * List of registered WPDK components
   *
   * @return array
   */
  private function components()
  {
    // Browser detect
    global $is_chrome, $is_gecko, $is_opera;

    // Firefox and Chrome supports a native color picker input field. Others browsers will be use the WordPress Color Picker
    $wp_color_picker = ( $is_chrome || $is_gecko || $is_opera ) ? '' : 'wp-color-picker';

    $components = array(

      self::JQUERY_TIMEPICKER => array(
        'js' => self::JQUERY_TIMEPICKER
      ),
      self::JQUERY_UI_CUSTOM => array(
        'css' => self::JQUERY_UI_CUSTOM
      ),

      // WPDK base core
      self::WPDK => array(
        'js'   => self::WPDK,
        'css'  => self::WPDK,
        'deps' => array(
          'jquery',
          'jquery-ui-core',
          'jquery-ui-tabs',
          'jquery-ui-dialog',
          'jquery-ui-datepicker',
          'jquery-ui-autocomplete',
          'jquery-ui-slider',
          'jquery-ui-sortable',
          'jquery-ui-draggable',
          'jquery-ui-droppable',
          'jquery-ui-resizable',
          self::JQUERY_TIMEPICKER,
          self::JQUERY_UI_CUSTOM,
          'thickbox'
        )
      ),
      // WPDK controls
      self::CONTROLS      => array(
        'js'   => self::CONTROLS,
        'css'  => self::CONTROLS,
        'deps' => array( self::WPDK, $wp_color_picker )
      ),
      // WPDK Alert
      self::ALERT         => array(
        'js'   => self::ALERT,
        'css'  => self::ALERT,
        'deps' => array( self::CONTROLS, self::TRANSITION )
      ),
      // WPDK Dynamic table
      self::DYNAMIC_TABLE => array(
        'js'   => self::DYNAMIC_TABLE,
        'css'  => self::DYNAMIC_TABLE,
        'deps' => array( self::CONTROLS, self::TOOLTIP )
      ),
      // WPDK List table
      self::LIST_TABLE    => array(
        'js'   => self::LIST_TABLE,
        'deps' => array( self::CONTROLS, self::TOOLTIP )
      ),
      // WPDK TABLE
      self::TABLE         => array(
        'css'  => self::TABLE,
        'deps' => array( self::CONTROLS, self::TOOLTIP )
      ),
      // WPDK Tooltip
      self::TOOLTIP => array(
        'js'   => self::TOOLTIP,
        'css'  => self::TOOLTIP,
        'deps' => array( self::TRANSITION )
      ),
      // WPDK Transitions
      self::TRANSITION    => array(
        'js' => self::TRANSITION,
      ),
      // WPDK Buttons
      self::BUTTON        => array(
        'js'   => self::BUTTON,
        'css'  => self::BUTTON,
        'deps' => array( self::CONTROLS )
      ),
      // WPDK Ribonize
      self::RIBBONIZE     => array(
        'js'  => self::RIBBONIZE,
        'css' => self::RIBBONIZE,
      ),
      // WPDK Popover
      self::POPOVER       => array(
        'js'   => self::POPOVER,
        'css'  => self::POPOVER,
        'deps' => array( self::CONTROLS, self::TOOLTIP )
      ),
      // WPDK Modal
      self::MODAL         => array(
        'js'   => self::MODAL,
        'css'  => self::MODAL,
        'deps' => array( self::CONTROLS, self::BUTTON, self::TRANSITION )
      ),
      // WPDK Page
      self::PAGE         => array(
        'js'  => self::PAGE,
        'css' => self::PAGE,
      ),
      // WPDK Progress
      self::PROGRESS      => array(
        'js'   => self::PROGRESS,
        'css'  => self::PROGRESS,
        'deps' => array( self::CONTROLS )
      ),
      // WPDK Preferences
      self::PREFERENCES      => array(
        'js'   => self::PREFERENCES,
        'deps' => array( self::CONTROLS )
      ),
      // WPDK Fonts
      self::FONTS => array(
        'css' => self::FONTS,
      ),
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

    // Handles, one or more
    $handles = (array)$component_handles;

    // Magic
    if( func_num_args() > 1 ) {
      $handles = func_get_args();
    }

    foreach ( $handles as $handle ) {

      // Javascript part
      if ( isset( $this->components[ $handle ]['js'] ) ) {

        // Check for dependences
        if ( isset( $this->components[ $handle ]['deps'] ) ) {

          // Recursive into the dependence and check if register
          $this->enqueue( $this->components[ $handle ]['deps'] );
        }
        $this->enqueue_scripts[] = $handle;
      }
      // Enqueue external
      else {
        wp_enqueue_script( $handle );
      }

      // CSS style part
      if ( isset( $this->components[ $handle ]['css'] ) ) {

        // Check dependences
        if ( isset( $this->components[ $handle ]['deps'] ) ) {

          // Recursive into the dependence and check if register
          $this->enqueue( $this->components[ $handle ]['deps'] );
        }
        $this->enqueue_styles[] = $handle;
      }
      // Enqueue external
      else {
        wp_enqueue_style( $handle );
      }
    }

    // Makes unique
    $this->enqueue_scripts = array_unique( $this->enqueue_scripts );
    $this->enqueue_styles  = array_unique( $this->enqueue_styles );

  }

}