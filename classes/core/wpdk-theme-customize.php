<?php

/**
 * Manage the list of Customize controls available
 *
 * @class           WPDKThemeCustomizeControlType
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-14
 * @version         1.0.0
 * @since 1.4.9
 *
 */
class WPDKThemeCustomizeControlType {

  const CHECKBOX        = 'checkbox';
  const COLOR           = 'WP_Customize_Color_Control';
  const DROP_DOWN_PAGES = 'dropdown-pages';
  const IMAGE           = 'WP_Customize_Image_Control';
  const RADIO           = 'radio';
  const SELECT          = 'select';
  const TEXT            = 'text';
  const UPLOAD          = 'WP_Customize_Upload_Control';

  /**
   * Return a key pairs value array with the controls list
   *
   * @brief Controls list
   * @todo See PHP Reflection class       
   */
  public static function controls()
  {
    $controls = array(
      self::COLOR           => self::COLOR,
      self::CHECKBOX        => self::CHECKBOX,
      self::DROP_DOWN_PAGES => self::DROP_DOWN_PAGES,
      self::IMAGE           => self::IMAGE,
      self::RADIO           => self::RADIO,
      self::SELECT          => self::SELECT,
      self::TEXT            => self::TEXT,
      self::UPLOAD          => self::UPLOAD
    );
    
    return apply_filters( 'wpdk_theme_customize_controls_type', $controls );
  }

  /**
   * Return a key pairs value array with the controls list
   *
   * @brief Controls list
   * @todo See PHP Reflection class
   */
  public static function instanceable()
  {
    $controls = array(
      self::COLOR           => self::COLOR,
      self::IMAGE           => self::IMAGE,
      self::UPLOAD          => self::UPLOAD
    );

    return apply_filters( 'wpdk_theme_customize_controls_instanceable', $controls );
  }
  
}


/**
 * Helper class to init a theme customize
 *
 * @class           WPDKThemeCustomize
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-14
 * @version         1.0.0
 * @since 1.4.9
 *
 */
class WPDKThemeCustomize {

  /**
   * Create an instance of WPDKThemeCustomize class
   *
   * @brief Construct
   *
   * @return WPDKThemeCustomize
   */
  public function __construct()
  {
    // Setup the Theme Customizer settings and controls...
    add_action( 'customize_register', array( $this, 'customize_register' ) );

    // Output custom CSS to live site
    add_action( 'wp_head', array( $this, 'wp_head' ) );

    // Enqueue live preview javascript in Theme Customizer admin screen
    add_action( 'customize_preview_init', array( $this, 'customize_preview_init' ) );
  }

  /**
   * This outputs the javascript needed to automate the live settings preview.
   * Also keep in mind that this function isn't necessary unless your settings are using 'transport'=>'postMessage'
   * instead of the default 'transport' => 'refresh'
   *
   * @brief Init
   *
   */
  public function customize_preview_init()
  {
  }

  /**
   * This hooks into 'customize_register' (available as of WP 3.4) and allows
   * you to add new sections and controls to the Theme Customize screen.
   *
   * Note: To enable instant preview, we have to actually write a bit of custom
   * javascript. See live_preview() for more.
   *
   * @brief Register
   *
   * @param WP_Customize_Manager $wp_customize An instance of WP_Customize_Manager class
   *
   */
  public function customize_register( $wp_customize )
  {
    $sections = $this->sections();

    if ( empty( $sections ) ) {
      return;
    }

    /* List of instanceable controls */
    $instanceable = WPDKThemeCustomizeControlType::instanceable();

    /* Sections */
    foreach ( $sections as $section_id => $section_args ) {

      $wp_customize->add_section( $section_id, $section_args );

      if ( isset( $section_args['_setting'] ) ) {

        /* Settings */
        foreach ( $section_args['_setting'] as $setting_id => $setting_args ) {
          $wp_customize->add_setting( $setting_id, $setting_args );

          if ( isset( $setting_args['_control'] ) ) {

            /* Controls */
            $control_args            = $setting_args['_control'];
            $control_args['section'] = $section_id;

            if ( in_array( $control_args['type'], $instanceable ) ) {
              // Get instanceable class name
              $class_name = $control_args['type'];
              // Unset unused
              unset( $control_args['type'] );
              // Set a new arg
              $control_args['settings'] = $setting_id;
              // Create the control
              $control = new $class_name( $wp_customize, $setting_id, $control_args );
              $wp_customize->add_control( $control, $control_args );
            }
            else {
              $wp_customize->add_control( $setting_id, $control_args );
            }

          }
        }
      }
    }

  }

  /**
   * Return an array of sections. You can set this array in two ways:
   *
   *     $sections = array(
   *       'id_section' => array( args... )
   *     );
   *
   * OR
   *     $sections = array(
   *       new WP_Customize_Section()
   *     );
   *
   * @brief Sections
   *
   * @return array
   */
  public function sections()
  {
    // You have to override this method
    return array();
  }

  /**
   * This will output the custom WordPress settings to the live theme's WP head.
   *
   * @brief Head
   *
   */
  public function wp_head()
  {
    // Your output in the head
  }

}