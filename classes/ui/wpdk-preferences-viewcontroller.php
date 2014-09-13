<?php

/**
 * Useful view controller (tabs) for preferences
 *
 * @class           WPDKPreferencesViewController
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2014-02-06
 * @version         1.0.0
 *
 */
class WPDKPreferencesViewController extends WPDKjQueryTabsViewController {

  /**
   * Preferences
   *
   * @brief Preferences
   *
   * @var WPDKPreferences $preferences
   */
  private $preferences;

  /**
   * Create an instance of WPDKPreferencesViewController class
   *
   * @brief Construct
   *
   * @param WPDKPreferences $preferences    An instance of WPDKPreferences class
   * @param string          $title          The title of view controller
   * @param array           $tabs           Tabs array
   *
   * @return WPDKPreferencesViewController
   */
  public function __construct( $preferences, $title, $tabs )
  {
    $this->preferences = $preferences;
    $view = new WPDKjQueryTabsView( $preferences->name, $tabs );
    parent::__construct( $preferences->name, $title, $view );

    // Fires after the the title.
    //add_action( 'wpdk_header_view_' . $this->id . '-header-view_after_title', array( $this, 'display_toolbar' ) );
    add_action( 'wpdk_header_view_after_title-' . $this->id . '-header-view', array( $this, 'display_toolbar' ) );
  }

  /**
   * This method is called when the head of this view controller is loaded by WordPress.
   * It is used by WPDKMenu for example, as 'admin_head-' action.
   *
   * @brief Head
   * @since 1.4.18
   */
  public function _admin_head()
  {
    WPDKUIComponents::init()->enqueue( WPDKUIComponents::PREFERENCES );
  }


  /**
   * Hook used to display a form toolbar preferences
   *
   * @brief Tolbar
   */
  public function display_toolbar()
  {
    $confirm = __( "Are you sure to reset All preferences to default value?\n\nThis operation is not reversible!", WPDK_TEXTDOMAIN );
    $confirm = apply_filters( 'wpdk_preferences_reset_all_confirm_message', $confirm );
    ?>
    <div class="tablenav top">
      <form id="wpdk-preferences"
            enctype="multipart/form-data"
            method="post">

        <input type="hidden"
               name="wpdk_preferences_class"
               value="<?php echo get_class( $this->preferences ) ?>" />
        <input type="file" name="file" />
        <input type="submit"
                 name="wpdk_preferences_import"
                 class="button button-primary"
                 value="<?php _e( 'Import', WPDK_TEXTDOMAIN ) ?>" />

        <input type="submit"
               name="wpdk_preferences_export"
               class="button button-secondary"
               value="<?php _e( 'Export', WPDK_TEXTDOMAIN ) ?>" />

        <input type="submit"
               name="wpdk_preferences_reset_all"
               class="button button-primary right"
               data-confirm="<?php echo $confirm ?>"
               value="<?php _e( 'Reset All', WPDK_TEXTDOMAIN ) ?>" />

        <input type="submit"
               name="wpdk_preferences_repair"
               class="button right"
               data-confirm="<?php echo $confirm ?>"
               value="<?php _e( 'Repair', WPDK_TEXTDOMAIN ) ?>" />

        <?php do_action( 'wpdk_preferences_view_controller-' . $this->id . '-tablenav-top', $this ) ?>
      </form>
    </div>
  <?php
  }

}