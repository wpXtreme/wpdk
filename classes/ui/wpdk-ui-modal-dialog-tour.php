<?php

/**
 * Useful Modal Dialog Tour.
 *
 * @class           WPDKUIModalDialogTour
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-07-29
 * @version         1.0.0
 *
 */
class WPDKUIModalDialogTour extends WPDKUIModalDialog {

  /**
   * An instance of WPDKUIPageView class
   *
   * @brief Page view
   *
   * @var WPDKUIPageView $page_view
   */
  private $page_view;

  /**
   * Create an instance of WPDKUIModalDialogTour class
   *
   * @brief Construct
   *
   * @return WPDKUIModalDialogTour
   */
  public function __construct( $id, $title )
  {
    // Remember, change the id to reopen this dialog tour
    parent::__construct( $id, $title );

    // Permanent dismiss
    $this->permanent_dismiss = true;

  }

  /**
   * Return the complete HTML mark with the pages to display.
   *
   * @brief Pages
   *
   * @return string
   */
  public function pages()
  {
    die( __METHOD__ . ' must be override in your subclass' );
  }

  /**
   * Display and open dialog.
   *
   * Override the parent method.
   *
   * @brief Open
   */
  public function open()
  {

    // Check if dismissed
    if( false === $this->is_dismissed() && 0 === did_action( 'wpxm_open_tour' ) ) {

      // Enqueue page view
      WPDKUIComponents::init()->enqueue( WPDKUIComponents::PAGE );

      // Display the page view
      $this->page_view = WPDKUIPageView::initWithHTML( $this->pages() );

      /**
       * Fires before open the modal dialog.
       */
      do_action( 'wpxm_open_tour' );

      $this->display();
      $this->show();
    }
  }

  /**
   * Content
   *
   * @brief Content
   * @return string
   */
  public function content()
  {
    return $this->page_view->html();
  }

  /**
   * Footer
   *
   * @brief Footer
   * @return string
   */
  public function footer()
  {
    return $this->page_view->navigator();
  }

}