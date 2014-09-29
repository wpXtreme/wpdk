<?php

/**
 * The WPDKViewCntroller is the main view likely WordPress
 *
 * ## Overview
 * A view controller to allow to manage a standard WordPress view. A standard view is:
 *
 * [ header with icon and title - optional button add]
 * [single or more view]
 *
 * ### Subclassing notes
 *
 *
 * @class              WPDKViewController
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-02-26
 * @version            0.9.22
 *
 */

class WPDKViewController extends WPDKObject {

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $__version
   */
  public $__version = '0.9.22';

  /**
   * The unique id for this view controller
   *
   * @brief ID
   *
   * @var string $id
   */
  public $id = '';

  /**
   * The title of this view controller. This is displayed on top header
   *
   * @brief Title
   *
   * @var string $title
   */
  public $title = '';

  /**
   * The view stored in this property represents the root view for the view controller hierarchy.
   *
   * @brief The root view
   *
   * @var WPDKView $view
   */
  public $view;

  /**
   * An instance of WPDKHeaderView
   *
   * @brief The header view
   *
   * @var WPDKHeaderView $viewHead
   */
  public $viewHead;

  /**
   * Create an instance of WPDKViewController class
   *
   * @brief Construct
   *
   * @param string $id         The unique id for this view controller
   * @param string $title      The title of this view controller. This is displayed on top header
   *
   * @return WPDKViewController
   */
  public function __construct( $id, $title )
  {
    $this->id       = sanitize_title( $id );
    $this->title    = $title;
    $this->view     = new WPDKView( $id . '-view-root', array( 'wrap' ) );
    $this->viewHead = new WPDKHeaderView( $id . '-header-view', $this->title );

    $this->view->addSubview( $this->viewHead );
  }

  /**
   * Return an instance of WPDKViewController class. This static method create a view controller with a view.
   *
   * @brief Init a view controller with a view
   *
   * @param string   $id         The unique id for this view controller
   * @param string   $title      The title of this view controller. This is displayed on top header
   * @param WPDKView $view       A instance of WPDKView class. This will be a subview.
   *
   * @return bool|WPDKViewController The view controller or FALSE if error
   */
  public static function initWithView( $id, $title, $view )
  {
    if ( !is_object( $view ) || !is_a( $view, 'WPDKView' ) ) {
      return false;
    }
    else {
      $instance = new WPDKViewController( $id, $title );
      $instance->view->addSubview( $view );
    }
    return $instance;
  }

  /**
   * Fires when styles are printed for a specific admin page based on $hook_suffix.
   *
   * @since WP 2.6.0
   */
  public function print_styles()
  {
    // To override
  }

  /**
   * This method is called when the head of this view controller is loaded by WordPress.
   * It is used by WPDKMenu for example, as 'admin_head-' action.
   *
   * @brief Head
   * @since 1.4.18
   */
  public function admin_head()
  {
    // To override
  }

  /**
   * This method is called when the head of this view controller is loaded by WordPress.
   * It is used by WPDKMenu for example, as 'admin_head-' action.
   * Internal view controller use this method to auto-load components. See for example `WPDKPreferencesViewController`
   *
   * @brief Head
   * @since 1.4.21
   */
  public function _admin_head()
  {
    // To override by WPDK class
  }

  /**
   * This static method is called when the head of this view controller is loaded by WordPress.
   * It is used by WPDKMenu for example, as 'admin_head-' action.
   *
   * @brief Head
   */
  public static function didHeadLoad()
  {
    // To override
  }

  /**
   * This method is called when the head of this view controller is loaded by WordPress.
   * It is used by WPDKMenu for example, as 'load-' action.
   *
   * @brief Head
   * @since 1.4.18
   */
  public function load()
  {
    // To override
  }

  /**
   * This static method is called when the head of this view controller is loaded by WordPress.
   * It is used by WPDKMenu for example, as 'load-' action.
   *
   * @brief Head
   */
  public static function willLoad()
  {
    // To override
  }

  /**
   * Return the HTML markup content of this view
   *
   * @brief Get HTML markup content
   *
   * @return string
   */
  public function html()
  {
    WPDKHTML::startCompress();
    $this->display();
    return WPDKHTML::endCompress();
  }

  /**
   * Display the content of this view controller
   *
   * @brief Display the view controller
   */
  public function display()
  {
    do_action( $this->id . '_will_view_appear', $this );

    // @deprecated
    do_action( 'wpdk_view_controller_will_view_appear', $this->view, $this );

    $this->view->display();

    // @deprecated
    do_action( 'wpdk_view_controller_did_view_appear', $this->view, $this );

    do_action( $this->id . '_did_view_appear', $this );
  }
}

/**
 * Standard header view
 *
 * @class              WPDKHeaderView
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-02-21
 * @version            0.8.2
 *
 */
class WPDKHeaderView extends WPDKView {

  /**
   * The title of this view.
   *
   * @brief Title
   *
   * @var string $title
   */
  public $title;

  /**
   * Create an instance of WPDKHeaderView class
   *
   * @brief Construct
   *
   * @param string $id    The unique id for this view
   * @param string $title The title of this view controller. This is displayed on top header
   *
   * @return WPDKHeaderView
   */
  public function __construct( $id, $title = '' )
  {
    parent::__construct( $id, 'clearfix wpdk-header-view' );

    // WPDKHeaderView property
    $this->title = $title;
  }

  /**
   * Draw the content of this view
   *
   * @brief Draw content
   */
  public function draw()
  {
    ?>
    <div data-type="wpdk-header-view" id="<?php echo $this->id ?>" class="wpdk-vc-header-icon"></div>

    <h2><?php echo $this->title ?>
      <?php

      /**
       * Fires into the the title TAG.
       *
       * @since 1.5.16
       *
       * @param WPDKHeaderView $header_view An instance of WPDKHeaderView class.
       */
      do_action( 'wpdk_header_view_inner_title', $this );

      /**
       * Fires into the the title TAG.
       *
       * The dynamic portion of the hook name, $id, refers to the header view id.
       *
       * @since 1.5.16
       *
       * @param WPDKHeaderView $header_view An instance of WPDKHeaderView class.
       */
      do_action( 'wpdk_header_view_inner_title-' . $this->id, $this );

      /**
       * Fires into the the title TAG.
       *
       * @deprecated since 1.5.16
       * @param WPDKHeaderView $header_view An instance of WPDKHeaderView class.
       */
      do_action( 'wpdk_header_view_' . $this->id . '_title_did_appear', $this );

      // @deprecated
      //do_action( 'wpdk_header_view_title_did_appear', $this );
      ?></h2>
    <?php

    /**
     * Fires after the the title.
     *
     * @param WPDKHeaderView $header_view An instance of WPDKHeaderView class.
     */
    do_action( 'wpdk_header_view_after_title', $this );

    /**
     * Fires after the the title.
     *
     * The dynamic portion of the hook name, $id, refers to the header view id.
     *
     * @since 1.5.16
     *
     * @param WPDKHeaderView $header_view An instance of WPDKHeaderView class.
     */
    do_action( 'wpdk_header_view_after_title-' . $this->id, $this );

    // @deprecated
    do_action( 'wpdk_header_view_' . $this->id . '_after_title', $this );

    // @deprecated
    //do_action( 'wpdk_header_view_after_title', $this );
    ?>
    <?php
    parent::draw();
  }

}