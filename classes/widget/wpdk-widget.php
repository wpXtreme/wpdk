<?php
/// @cond private

/*
 * [DRAFT]
 *
 * THE FOLLOWING CODE IS A DRAFT. FEEL FREE TO USE IT TO MAKE SOME EXPERIMENTS, BUT DO NOT USE IT IN ANY CASE IN
 * PRODUCTION ENVIRONMENT. ALL CLASSES AND RELATIVE METHODS BELOW CAN CHNAGE IN THE FUTURE RELEASES.
 *
 */

/**
 * Widget model class
 *
 * @class           WPDKWidget
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-11-08
 * @version         1.0.0
 * @since           1.3.3
 *
 */
class WPDKWidget extends WP_Widget {

  /**
   * Create an instance of WPDKWidget class
   *
   * @brief Construct
   *
   * @return WPDKWidget
   */
  public function __construct()
  {
    /* Load scripts and styles for admin page */
    add_action( 'widgets_admin_page', array( $this, '_widgets_admin_page') );
  }

  /**
   * Called when widgets admin page is loaded
   *
   * @brief Auto formatted
   */
  public function _widgets_admin_page()
  {
    $this->widgets_admin_page();
  }

  /**
   * alled when widgets admin page is loaded
   *
   * @brief Widgets admin page
   */
  public function willLoadWidgetAdminPage()
  {
    /* You can override this delegate method */
  }

}

/// @endcond