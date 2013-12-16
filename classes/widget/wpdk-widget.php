<?php
/**
 * Widget model class
 *
 * @class           WPDKWidget
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-11-08
 * @version         1.0.0
 * @since           1.4.0
 *
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

    /* Load scripts and styles for admin page */
    add_action( 'widgets_admin_page', array( $this, 'willWidgetsAdminPage') );

    /* Load scripts and styles for admin page */
    add_action( 'admin_print_styles-widgets.php', array( $this, 'admin_print_styles' ) );

    /* Create the widget */
    parent::__construct( $id_base, $name, $widget_options, $control_options );
  }

  /**
   * Admin widget head page
   *
   * @brief Admin widget head page
   */
  public function admin_print_styles( )
  {
    $logo    = isset( $this->control_options['wpdk_icon'] ) ? $this->control_options['wpdk_icon'] : '';
    $version = isset( $this->control_options['wpdk_version'] ) ? $this->control_options['wpdk_version'] : '';

    /* If no wpdk extends info set exit. */
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
    $this->willWidgetsHeadAdminPage();
  }

  /**
   * This delegate action hook is called in the head of Widgets admin page
   *
   * @brief Widgets head admin page
   */
  public function willWidgetsHeadAdminPage()
  {
    /* You can override this delegate method */
  }

  /**
   * Fires before the Widgets administration page content loads
   *
   * @brief Widgets admin page
   */
  public function willWidgetsAdminPage()
  {
    /* You can override this delegate method */
  }

}