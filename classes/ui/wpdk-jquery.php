<?php

/**
 * An over head class for jQuery HTML element
 *
 * @class              WPDKjQuery
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-11-05
 * @version            1.0.0
 */

class WPDKjQuery {
  // Not yet implement
}

/**
 * This is the model of a single jQuery tab.
 *
 * @class              WPDKjQueryTab
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-11-05
 * @version            1.0.0
 *
 */
class WPDKjQueryTab {

  /**
   * The HTML markup for tab content
   *
   * @brief Content tab
   *
   * @var string $content
   */
  public $content = '';

  /**
   * The jQuery tab ID
   *
   * @brief Tab ID
   *
   * @var string $id
   */
  public $id = '';

  /**
   * The tab title
   *
   * @brief Title
   *
   * @var string $title
   */
  public $title = '';

  /**
   * Create an instance of WPDKjQueryTab class
   *
   * @brief Construct
   *
   * @param string|WPDKView $id      The tab ID or an instance of WPDKView
   * @param string          $title   The tab title
   * @param string          $content Optional. HTML markup of content
   */
  function __construct( $id, $title, $content = '' )
  {
    if ( is_a( $id, 'WPDKView' ) ) {
      $this->id      = sanitize_key( $id->id );
      $this->title   = $title;
      $this->content = $id->html();
    }
    else {
      $this->id      = sanitize_key( $id );
      $this->title   = $title;
      $this->content = $content;
    }
  }
}

/**
 * This is a sub class of standard WPDKView specified design for jQuery tabs.
 *
 * @class              WPDKjQueryTabsView
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-11-05
 * @version            1.0.0
 *
 */
class WPDKjQueryTabsView extends WPDKView {

  /**
   * A key value pairs array tabs list, whew value is an instance of WPDKjQueryTab
   *
   * @brief Array of tabs
   *
   * @var array $tabs
   */
  public $tabs = array();

  /**
   * Set to true to display the border. Default is true
   *
   * @brief Border
   *
   * @var bool $border
   */
  public $border = true;

  /**
   * Makes jQuery tabs vertical.
   *
   * @since 1.7.1
   *
   * @var bool $vertical
   */
  public $vertical = false;

  /**
   * Create an instance of WPDKjQueryTabsView class
   *
   * @brief Construct
   *
   * @param string $id       The view ID
   * @param array  $tabs     Optional. A tabs list. Instances of WPDKjQueryTab class
   * @param bool   $vertical Optional. Set to TRUE in order to display vertical tab. Default is FALSE.
   *
   * @return WPDKjQueryTabsView
   */
  public function __construct( $id, $tabs = array(), $vertical = false )
  {
    parent::__construct( $id, 'wpdk-jquery-ui' );

    $this->tabs     = $tabs;
    $this->vertical = $vertical;
  }

  /**
   * Add a single tab to the tabs list
   *
   * @brief Add a single tab content
   *
   * @param WPDKjQueryTab $tab An instance of WPDKjQueryTab
   *
   */
  public function addTab( $tab )
  {
    if ( is_object( $tab ) && is_a( $tab, 'WPDKjQueryTab' ) ) {
      $id                = $tab->id;
      $this->tabs[ $id ] = $tab;
    }
  }

  /**
   * Remove a tab from tabs list
   *
   * @brief Remove a tab
   *
   * @param string|WPDKjQueryTab $tab An instance of WPDkjQueryTab class or its ID
   */
  public function removeTab( $tab )
  {
    if ( is_object( $tab ) && is_a( $tab, 'WPDKjQueryTab' ) ) {
      $id = $tab->id;
    }
    elseif ( is_string( $tab ) ) {
      $id = $tab;
    }
    if ( !empty( $id ) && isset( $this->tabs[ $id ] ) ) {
      unset( $this->tabs[ $id ] );
    }
  }

  /**
   * Display
   *
   * @brief Draw view content
   */
  public function draw()
  {
    // If no tabs...
    if( empty( $this->tabs ) ) {
      return;
    }

    $html_titles  = '';
    $html_content = '';

    // Vertical
    $vertical = $this->vertical ? 'data-layout="vertical"' : '';


    foreach ( $this->tabs as $tab ) {
      $html_titles .= sprintf( '<li class="%s"><a href="#%s">%s</a></li>', $tab->id, $tab->id, $tab->title );
      $html_content .= sprintf( '%s', $tab->content );
    }

    ?>
    <div class="<?php echo $this->border ? 'wpdk-border-container clearfix' : '' ?>">
      <div id="<?php echo $this->id . '-tabs-view' ?>"
           <?php echo $vertical ?>
           class="wpdk-tabs">
        <ul>
          <?php echo $html_titles ?>
        </ul>
        <?php echo $html_content ?>
      </div>
    </div>
  <?php

  }

}

/**
 * A jQuery tabs view controller
 *
 * @class              WPDKjQueryTabsViewController
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-11-05
 * @version            1.0.0
 *
 */
class WPDKjQueryTabsViewController extends WPDKViewController {

  /**
   * Create an instane of WPDKjQueryTabsViewController class
   *
   * @brief Construct
   *
   * @param string             $id         A lowercase id used in HTML markup
   * @param string             $title      The view controller title
   * @param WPDKjQueryTabsView $view       An instance of WPDKjQueryTabView class
   *
   * @return WPDKjQueryTabsViewController
   */
  public function __construct( $id, $title, $view )
  {
    parent::__construct( $id, $title );
    $this->view->addSubview( $view );
  }

  /**
   * Fires when styles are printed for a specific admin page based on $hook_suffix.
   */
  public function admin_print_styles()
  {
    wp_enqueue_style( 'jquery-ui-tabs' );
  }

  /**
   * This method is called when the head of this view controller is loaded by WordPress.
   * It is used by WPDKMenu for example, as 'admin_head-' action.
   *
   * @brief Head
   */
  public function admin_head()
  {
    wp_enqueue_script( 'jquery-ui-tabs' );
  }

}