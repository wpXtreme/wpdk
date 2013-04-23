<?php
/**
 * Screen helper
 *
 * ## Overview
 *
 * You have to subclass this class
 *
 * @class           WPDKScreenHelp
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-03-13
 * @version         1.0.0
 *
 */
class WPDKScreenHelp {

  /**
   * The WordPres current screen
   *
   * @brief Current screen
   *
   * @var WP_Screen $currentScreen
   */
  public $currentScreen;

  /**
   * Create an instance of WPDKScreenHelp class
   *
   * @brief Construct
   *
   * @return WPDKScreenHelp
   */
  public function __construct() {
    $this->currentScreen = get_current_screen();
    $this->display();
  }

  /**
   * Used this method to add tab and sidebar to the screen
   *
   * @brief Display
   * @note To override
   */
  public function display() {
    die( 'WPDKScreenHelp:.display() must be override in subclass' );
  }

  /**
   * Add a help tab to current screen
   *
   * @brief Add help tab
   *
   * @param string $title        Tab title. The title is sanitize and used as id
   * @param bool   $callback     Optional. A callback to generate the tab content.
   * @param string $html_content Optional. HTML markup for the content
   */
  public function addTab( $title, $callback = false, $html_content = '' ) {
    $help_tab = array(
      'id'    => sanitize_key( $title ),
      'title' => $title,
    );

    if ( !empty( $html_content ) ) {
      $help_tab['content'] = $html_content;
    }

    if ( !empty( $callback ) ) {
      $help_tab['callback'] = $callback;
    }

    $this->currentScreen->add_help_tab( $help_tab );
  }

  /**
   * Utility (alias) to set the sidebar
   *
   * @brief Set sidebar
   *
   * @param string $sidebar HTML markup for sidebar
   */
  public function sidebar( $sidebar ) {
    if ( !empty( $sidebar ) ) {
      $this->currentScreen->set_help_sidebar( $sidebar );
    }
  }

}