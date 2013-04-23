<?php
/**
 * An over head class for jQuery HTML element
 *
 * @class              WPDKjQuery
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 */

class WPDKjQuery {
  /* Not yet implement */
}

/**
 * This is the model of a single jQuery tab
 *
 * @class              WPDKjQueryTab
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
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
  public $content;
  /**
   * The jQuery tab ID
   *
   * @brief Tab ID
   *
   * @var string $id
   */
  public $id;
  /**
   * The tab title
   *
   * @brief Title
   *
   * @var string $title
   */
  public $title;

  /**
   * Create an instance of WPDKjQueryTab class
   *
   * @brief Construct
   *
   * @param string $id      The tab ID
   * @param string $title   The tab title
   * @param string $content HTML markup of content
   */
  function __construct( $id, $title, $content = '' ) {
    $this->id      = sanitize_key( $id );
    $this->title   = $title;
    $this->content = $content;
  }
}

/**
 * This is a sub class of standard WPDKView specified design for jQuery tabs
 *
 * @class              WPDKjQueryTabsView
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
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
  public $tabs;

  /**
   * Create an instance of WPDKjQueryTabsView class
   *
   * @brief Construct
   *
   * @param string $id   The view ID
   * @param array  $tabs A tabs list
   *
   * @return WPDKjQueryTabsView
   */
  public function __construct( $id, $tabs = array() ) {
    parent::__construct( $id, 'wpdk-jquery-ui' );
    $this->tabs = $tabs;
  }

  /**
   * Add a single tab to the tabs list
   *
   * @brief Add a single tab content
   *
   * @param WPDKjQueryTab $tab An instance of WPDKjQueryTab
   *
   */
  public function addTab( $tab ) {
    if ( is_object( $tab ) && is_a( $tab, 'WPDKjQueryTab' ) ) {
      $id              = $tab->id;
      $this->tabs[$id] = $tab;
    }
  }

  /**
   * Remove a tab from tabs list
   *
   * @brief Remove a tab
   *
   * @param string|WPDKjQueryTab $tab An instance of WPDkjQueryTab class or its ID
   */
  public function removeTab( $tab ) {
    if ( is_object( $tab ) && is_a( $tab, 'WPDKjQueryTab' ) ) {
      $id = $tab->id;
    }
    elseif ( is_string( $tab ) ) {
      $id = $tab;
    }
    if ( !empty( $id ) && isset( $this->tabs[$id] ) ) {
      unset( $this->tabs[$id] );
    }
  }

  /**
   * Display
   *
   * @brief Draw view content
   */
  public function draw() {

    $html_titles  = '';
    $html_content = '';

    foreach ( $this->tabs as $tab ) {
      $html_titles .= sprintf( '<li class="%s"><a href="#%s">%s</a></li>', $tab->id, $tab->id, $tab->title );
      //$html_content .= sprintf( '<div id="%s" class="clearfix">%s</div>', $tab->id, $tab->content );
      $html_content .= sprintf( '%s', $tab->content );
    }
    ?>
    <div class="wpdk-border-container">
      <div id="<?php echo $this->id . '-tabs-view' ?>" class="wpdk-tabs">
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
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
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
  public function __construct( $id, $title, $view ) {
    parent::__construct( $id, $title );
    $this->view->addSubview( $view );
  }
}













/**
 * Manage the jQuery Tabs.
 *
 * ## Overview
 * This is a class to create and manage the jQuery tabs.
 *
 * @class              WPDKjQueryTabs
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 * @deprecated         Use WPDKjQueryTab, WPDKjQueryTabsView and WPDKjQueryTabsViewController instead
 *
 */
class WPDKjQueryTabs {

  /**
   * @brief Addition classes
   *
   * Addition classes for HTMl markup
   *
   * @var string|array $classes
   */
  public $classes;
  /**
   * @brief Tab ID
   *
   * The jQuery tab ID
   *
   * @var string $id
   */
  public $id;
  /**
   * @brief Array of tabs
   *
   * A key value pairs array tabs list
   *
   * @var array $tabs
   */
  public $tabs;

  /**
   * @brief Construct
   *
   * Create an instance of WPDKjQueryTabs class
   *
   * @param string $id ID del TAB
   *
   * @return WPDKjQueryTabs
   */
  public function __construct( $id ) {
    $this->id      = $id;
    $this->tabs    = array();
    $this->classes = '';
  }

  /**
   * @brief Add a single tab
   *
   * Add a single tab to the tabs list
   *
   * @param string $id      ID of single tab
   * @param string $title   Title tab
   * @param string $content HTML content
   *
   */
  public function add( $id, $title, $content = '' ) {
    $this->tabs[$id] = array(
      'title'   => $title,
      'content' => $content
    );
  }

  /**
   * @brief Display the jQuery tab
   *
   * Display (or return) the HTML markup for jQuery tab
   *
   * @param bool $echo TRUE to display, FALSE to get HTML markup.
   *
   * @return string|void
   */
  public function display( $echo = true ) {
    $html = $this->html();
    if ( false === $echo ) {
      return $html;
    }
    echo $html;
  }

  /**
   * @brief Get jQuery HTML markup for tabs
   *
   * Return the jQuery HTML markup for tabs
   *
   * @return bool|string FALSE if error
   *
   */
  private function html() {
    if ( empty( $this->tabs ) ) {
      return false;
    }

    /* Unique id for jQuery cookie. */
    $uniq_id_tabs_for_cookie = $this->id;

    /* Classi css aggiuntive, o sottoforma di stringa o di array */
    $classes = '';
    if ( !empty( $this->classes ) ) {
      if ( is_array( $this->classes ) ) {
        $classes = join( ' ', $this->classes );
      }
      elseif ( is_string( $this->classes ) ) {
        $classes = $this->classes;
      }
      else {
        $classes = '';
      }
    }

    $html_titles  = '';
    $html_content = '';

    foreach ( $this->tabs as $key => $tab ) {
      $html_titles .= sprintf( '<li class="%s"><a href="#%s">%s</a></li>', $key, $key, $tab['title'] );
      $html_content .= sprintf( '<div id="%s" class="clearfix">%s</div>', $key, $tab['content'] );
    }

    /* @todo wpdk-border-container va eliminata */
    $html = <<< HTML
<div class="wpdk-border-container">
    <div id="{$uniq_id_tabs_for_cookie}" class="wpdk-tabs {$classes}">
        <ul>
            {$html_titles}
        </ul>
        {$html_content}
    </div>
</div>
HTML;
    return $html;
  }
}