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
 * @date            2014-02-03
 * @version         1.1.0
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
   * A key value pairs array with the list of tabs
   *
   * @brief tabs
   *
   * @var array $tabs
   */
  private $tabs = array();

  /**
   * Create an instance of WPDKScreenHelp class
   *
   * @brief Construct
   *
   * @return WPDKScreenHelp
   */
  public function __construct()
  {
    $this->currentScreen = get_current_screen();
  }

  /**
   * Return a key value pairs array with the list of tabs
   *
   * @brief Tabs
   * @since 1.4.18
   *
   * @param array $tabs List of tabs
   *
   * @return array
   */
  public function tabs( $tabs = array() )
  {
    die( __METHOD__ . ' must be override in your subclass' );
  }

  /**
   * Used this method to add tab and sidebar to the screen
   *
   * @brief Display
   */
  public function display()
  {
    // Merging
    $this->tabs = array_merge( $this->tabs, (array)$this->tabs() );

    // Add the tabs
    foreach ( $this->tabs as $title => $callable_content ) {
      $this->addTab( $title, $callable_content );
    }

    // Add the sidebar if exists
    $sidebar = $this->sidebar();
    if ( !empty( $sidebar ) ) {
      $this->currentScreen->set_help_sidebar( $sidebar );
    }
  }

  /**
   * Add a help tab to current screen
   *
   * @brief Add help tab
   *
   * @param string          $title    Tab title. The title is sanitize and used as id
   * @param callable|string $callback Any string content or callable function for content.
   */
  public function addTab( $title, $callable_content )
  {
    // If $callable_content is empty exit
    if ( empty( $callable_content ) ) {
      return;
    }

    // Store in global
    $this->tabs[$title] = $callable_content;

    $help_tab = array(
      'id'    => sanitize_key( $title ),
      'title' => $title,
    );

    if ( is_string( $callable_content ) && !is_callable( $callable_content ) ) {
      $help_tab['content'] = $callable_content;
    }

    if ( is_callable( $callable_content ) ) {
      $help_tab['callback'] = $callable_content;
    }

    $this->currentScreen->add_help_tab( $help_tab );
  }

  /**
   * Return the HTML markup for sidebar
   *
   * @brief Sidebar
   *
   * @return string
   */
  public function sidebar()
  {
    // You can override if you will support a sidebar content
    return '';
  }

}