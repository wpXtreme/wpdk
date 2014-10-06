<?php

/**
 * Widget model class
 *
 * @class           WPDKWidget
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-09-30
 * @version         1.0.2
 * @since           1.4.0
 *
 * @history         1.0.2 - Minor improves
 */
class WPDKWidget extends WP_Widget {

  /**
   * Create an instance of WPDKWidget class
   *
   * @brief Construct
   *
   * @param string $id_base         Base ID for the widget, lower case,
   *                                if left empty a portion of the widget's class name will be used. Has to be unique.
   * @param string $name            Name for the widget displayed on the configuration page.
   * @param array  $widget_options  Optional. Passed to wp_register_sidebar_widget()
   *                                - description: shown on the configuration page
   *                                - classname
   * @param array  $control_options Optional. Passed to wp_register_widget_control()
   *                                - width: required if more than 250px
   *                                - height: currently not used but may be needed in the future
   *                                - wpdk_version: additional version insert to the right of title
   *                                - wpdk_icon: additional icon insert to the left of title
   *
   * @return WPDKWidget
   */
  public function __construct( $id_base, $name, $widget_options = array(), $control_options = array() )
  {
    $this->id_base = $id_base;

    // Fires when styles are printed for a specific admin page based on $hook_suffix.
    add_action( 'admin_print_styles-widgets.php', array( $this, '_admin_print_styles' ) );

    // Fires when styles are printed for a specific admin page based on $hook_suffix.
    add_action( 'admin_print_styles-widgets.php', array( $this, 'admin_print_styles' ) );

    // LFires in <head> for a specific admin page based on $hook_suffix.
    add_action( 'admin_head-widgets.php', array( $this, 'admin_head' ) );

    // Fires before the Widgets administration page content loads.
    add_action( 'widgets_admin_page', array( $this, 'widgets_admin_page') );

    // Create the widget
    parent::__construct( $id_base, $name, $widget_options, $control_options );
  }

  /**
   * Fires when styles are printed for a specific admin page based on $hook_suffix.
   *
   * @since WP 2.6.0
   * @ignore
   */
  public function admin_print_styles( )
  {
    // To override
  }

  /**
   * Fires when styles are printed for a specific admin page based on $hook_suffix.
   *
   * @since WP 2.6.0
   * @access private
   */
  public function _admin_print_styles( )
  {
    $logo    = isset( $this->control_options['wpdk_icon'] ) ? $this->control_options['wpdk_icon'] : '';
    $version = isset( $this->control_options['wpdk_version'] ) ? $this->control_options['wpdk_version'] : '';

    // If no wpdk extends info set exit
    if ( empty( $logo ) && empty( $version ) ) {
      return;
    }

    WPDKHTML::startCompress();
    ?>
  <style id="wpdk-inline-styles-<?php echo $this->id_base ?>" type="text/css">
      <?php if( !empty( $logo ) ) : ?>
  div[id*=<?php echo $this->id_base ?>] .widget-title h4
  {
    padding-left        : 28px;
    line-height         : 150%;
    background-image    : url(<?php echo $logo ?>) !important;
    background-repeat   : no-repeat;
    background-position : 6px center;
  }
    <?php endif ?>

    <?php if( !empty( $logo ) ) : ?>
  div[id*=<?php echo $this->id_base ?>] .widget-title h4 span.in-widget-title:before
  {
    content : ' <?php echo $version ?>';
  }
      <?php endif ?>
  </style>
    <?php
    echo WPDKHTML::endCSSCompress();
  }

  /**
   * Widgets admin head
   *
   * @brief Widgets admin head
   *
   * @since 1.5.3
   */
  public function admin_head()
  {
    // Override
  }

  /**
   * Fires before the Widgets administration page content loads.
   *
   * @since WP 3.0.0
   */
  public function widgets_admin_page()
  {
    // You can override this delegate method
  }

  /**
   * Fires before the Widgets administration page content loads
   *
   * @brief      Widgets admin page
   * @deprecated since 1.5.3 use widgets_admin_page instead
   */
  public function willWidgetsAdminPage()
  {
    // You can override this delegate method
  }

}