<?php
/**
 * This class is the top level menu model
 *
 * ## Overview
 *
 *     $menu = new WPDKMenu( 'wpdk-sample-menu', __( 'WPDK Sample', WPXWPDKSAMPLEMENU_TEXTDOMAIN ), self::MENU_CAPABILITY, $icon_menu );
 *     $menu->addSubMenu( __( 'First item' ), 'WPXWPDKSamplemenuViewController' );
 *     $menu->addDivider();
 *     $menu->addSubMenu( __( 'Second item' ), 'WPXWPDKSamplemenuViewController' );
 *     $menu->addSubMenu( __( 'Options' ), 'WPXWPDKSamplemenuViewController' );
 *     $menu->addDivider( 'More' );
 *     $menu->addSubMenu( __( 'About' ), 'WPXWPDKSamplemenuViewController' );
 *     $menu->render();
 *
 * ## WPDK Sample
 *
 * * https://github.com/wpXtreme/wpdk-sample-menu
 *
 *
 * @class           WPDKMenu
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-02-22
 * @version         1.0.0
 *
 */
class WPDKMenu {

  /**
   * Default capability
   *
   * @brief Capability
   */
  const DEFAULT_CAPABILITY = 'read';
  /**
   * The global key for access to the wpdk menu list
   *
   * @brief Global key
   */
  const GLOBAL_MENU = 'wpdk_menus';
  /**
   * Minumun capabilty require to display this menu
   *
   * @brief Capability
   *
   * @var string $capability
   */
  public $capability;
  /**
   * WorkPress hook name - returned from `add_menu_page()`
   *
   * @brief Hook name
   *
   * @var string $hookName
   */
  public $hookName;
  /**
   * The url to the icon to be used for this menu.
   *
   * @brief Icon
   *
   * @var string $icon
   */
  public $icon;
  /**
   * Unique menu ID
   *
   * @brief Menu ID
   *
   * @var string $id
   */
  public $id;
  /**
   * The top level menu title
   *
   * @brief Menu title
   *
   * @var string $menuTitle
   */
  public $menuTitle;
  /**
   * The position in the menu order this one should appear
   *
   * @brief Position
   *
   * @var int $position
   */
  public $position;
  /**
   * An array with the sub menus list
   *
   * @brief Sub menus
   *
   * @var array $subMenus
   */
  public $subMenus;

  /**
   * Create an instance of WPDKMenu class
   *
   * @brief Construct
   *
   * @param string $id              menu unique string id
   * @param string $menu_title      The menu title
   * @param string $capability      Optional. Minimun capabilties to show this item. Default WPDKMenu::DEFAULT_CAPABILITY
   * @param string $icon            Optional. The url to the icon to be used for this menu. Using 'none' would leave
   *                                div.wp-menu-image empty so an icon can be added as background with CSS.
   * @param int    $position        Optional. The position in the menu order this one should appear
   *
   * @return WPDKMenu
   */
  public function __construct( $id, $menu_title, $capability = self::DEFAULT_CAPABILITY, $icon = '', $position = null ) {
    $this->id             = sanitize_key( $id );
    $this->menuTitle      = $menu_title;
    $this->pageTitle      = $menu_title;
    $this->capability     = $capability;
    $this->position       = $position;
    $this->icon           = $icon;
    $this->subMenus       = array();
  }

  /**
   * Return the WPDK menu info by name of view controller of submenu item
   *
   * @param string $view_controller The view controller class
   *
   * @return array
   */
  public static function menu( $view_controller ) {
    if ( isset( $GLOBALS[self::GLOBAL_MENU] ) ) {
      if ( !empty( $view_controller ) && !empty( $GLOBALS[self::GLOBAL_MENU][$view_controller] ) ) {
        return $GLOBALS[self::GLOBAL_MENU][$view_controller];
      }
    }
    else {
      $GLOBALS[self::GLOBAL_MENU] = array();
    }
    return null;
  }

  /**
   * Return the compute URL of menu item from view controller name
   *
   * @param string $view_controller Class name of view controller
   *
   * @return string
   */
  public static function url( $view_controller ) {
    $info = self::menu( $view_controller );

    $url = '';
    if ( !empty( $info ) ) {
      if ( $info['parent'] == $info['page'] ) {
        $url = add_query_arg( array( 'page' => $info['page'] ), admin_url( 'admin.php' ) );
      }
      else {
        $url = add_query_arg( array( 'page' => $info['page'] ), admin_url( $info['parent'] ) );
      }
    }
    return $url;
  }

  /**
   * Return an array with WPDKMenu instance as render a menu from an array
   *
   *     $menus = array(
   *       'wpdk-sample-menu' => array(
   *         'menuTitle'  => __( 'WPDK Sample', WPXWPDKSAMPLEMENU_TEXTDOMAIN ),
   *         'capability' => self::MENU_CAPABILITY,
   *         'icon'       => $icon_menu,
   *         'subMenus'   => array(
   *           array(
   *             'menuTitle' =>  __( 'First item' ),
   *             'viewController' => 'WPXWPDKSamplemenuViewController',
   *           ),
   *           WPDKSubMenuDivider::DIVIDER,
   *           array(
   *             'menuTitle' =>  __( 'Second item' ),
   *             'viewController' => 'WPXWPDKSamplemenuViewController',
   *           ),
   *           array(
   *             'menuTitle' =>  __( 'Options' ),
   *             'viewController' => 'WPXWPDKSamplemenuViewController',
   *           ),
   *           array( WPDKSubMenuDivider::DIVIDER => 'More' ),
   *           array(
   *             'menuTitle' =>  __( 'About' ),
   *             'viewController' => 'WPXWPDKSamplemenuViewController',
   *           ),
   *         )
   *       )
   *     );
   *
   * @brief Render by array
   *
   * @param array $menus A key value pairs array with the list of menu
   *
   * @return array
   */
  public static function renderByArray( $menus ) {

    $result = array();

    foreach ( $menus as $key => $value ) {
      if ( is_array( $value ) ) {

        $menu = new WPDKMenu( $key, $value['menuTitle'], $value['capability'], $value['icon'] );

        foreach ( $value['subMenus'] as $skey => $svalue ) {

          if ( is_string( $svalue ) && WPDKSubMenuDivider::DIVIDER === $svalue ) {
            $menu->addDivider();
          }
          elseif ( is_array( $svalue ) ) {
            if ( isset( $svalue[WPDKSubMenuDivider::DIVIDER] ) ) {
              $menu->addDivider( $svalue[WPDKSubMenuDivider::DIVIDER] );
            }
            else {
              $sub_menu = $menu->addSubMenu( $svalue['menuTitle'], $svalue['viewController'] );
              /* Extra properties for sub menu */
              foreach ( $svalue as $property => $pvalue ) {
                $sub_menu->$property = $pvalue;
              }
            }
          }
        }

        /* Extra properties for menu */
        foreach( $value as $property => $pvalue ) {
          if ( !in_array( $property, array( 'subMenus' ) ) ) {
            $menu->$property = $pvalue;
          }
        }

        /* Over */
        $menu->render();
        $result[$key] = $menu;
      }
    }
    return $result;
  }

  /**
   * Add a special submenu as separator
   *
   * @brief Divider
   *
   * @param string $title
   *
   * @return WPDKSubMenu
   */
  public function addDivider( $title = '' ) {
    if ( count( $this->subMenus ) > 0 ) {
      $divider                      = new WPDKSubMenuDivider( $this->id, $title );
      $this->subMenus[$divider->id] = $divider;
      return $divider;
    }
  }

  /**
   * Return an instance of WPDKSubMenu after add a sub menu to this main menu
   *
   * @brief Add sub menus
   *
   * @param string $menu_title      Menu title
   * @param string $view_controller Name of view controller
   * @param string $capability      Optional. Minimun capabilties to show this item. Default WPDKMenu::DEFAULT_CAPABILITY
   *
   * @return WPDKSubMenu
   */
  public function addSubMenu( $menu_title, $view_controller, $capability = WPDKMenu::DEFAULT_CAPABILITY ) {
    if ( empty( $this->subMenus ) ) {
      $id = $this->id;
    }
    else {
      $id = sprintf( '%s-submenu-%s', $this->id, count( $this->subMenus ) );
    }

    $sub_menu            = new WPDKSubMenu( $this->id, $id, $menu_title, $view_controller, $capability );
    $this->subMenus[$id] = $sub_menu;
    return $sub_menu;
  }

  /**
   * Register this menu tree to WordPress menu
   *
   * @brief Render
   */
  public function render() {
    $this->hookName = add_menu_page( $this->pageTitle, $this->menuTitle, $this->capability, $this->id, '', $this->icon, $this->position );

    while ( $sub_menu = current( $this->subMenus ) ) {
      $next = next( $this->subMenus );
      if ( is_a( $sub_menu, 'WPDKSubMenuDivider' ) ) {
        if ( isset( $next ) ) {
          $sub_menu->capability = $next->capability;
        }
      }
      $sub_menu->render();
    }
  }
}

/**
 * Model for a sub menu
 *
 * ## WPDK Sample
 *
 * * https://github.com/wpXtreme/wpdk-sample-menu
 *
 *
 * @class           WPDKSubMenu
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-02-26
 * @version         1.0.1
 *
 */
class WPDKSubMenu {

  /**
   * Default capability
   *
   * @brief Capability
   */
  const DEFAULT_CAPABILITY = 'read';
  public $capability;
  public $hookName;
  public $id;
  public $menuTitle;
  public $pageTitle;
  public $parent;
  public $viewController;

  /**
   * Create an instance of WPDKSubMenu class
   *
   * @brief Construct
   *
   * @param string|object $parent          Any WPDKMenu object or string id of parent
   * @param string        $id              Submenu unique string id
   * @param string        $menu_title      The submenu title
   * @param string        $view_controller Optional. Name of view controller or a callback function.
   * @param string        $capability      Optional. Minum capabilties to show thhi item.
   *                                       Default WPDKSubMenu::DEFAULT_CAPABILITY
   *
   * @return WPDKSubMenu
   */
  public function __construct( $parent, $id, $menu_title, $view_controller = '', $capability = self::DEFAULT_CAPABILITY ) {
    $this->parent = $parent;
    if ( is_object( $parent ) && is_a( $parent, 'WPDKMenu' ) ) {
      $this->parent = $parent->id;
    }
    $this->id             = sanitize_key( $id );
    $this->menuTitle      = $menu_title;
    $this->pageTitle      = $menu_title;
    $this->capability     = $capability;
    $this->viewController = $view_controller;
  }

  /**
   * Return an array of sub menu tems.
   * Render a list of sub menu from any top level menu. Useful to add sub menus to Custom Post Type top level menu or
   * to any WordPress top level menu
   *
   * ## Example
   *
   *     $sub_menus = array(
   *       'edit.php?post_type=' . WPXMailManagerCustomPostType::ID => array(
   *
   *         WPDKSubMenuDivider::DIVIDER,
   *
   *         array(
   *           'menuTitle'      => __( 'Settings', WPXMAILMANAGER_TEXTDOMAIN ),
   *           'capability'     => self::MENU_CAPABILITY,
   *           'viewController' => 'WPXMailManagerConfigurationViewController'
   *         ),
   *
   *         WPDKSubMenuDivider::DIVIDER,
   *
   *         array(
   *           'menuTitle'      => __( 'About', WPXMAILMANAGER_TEXTDOMAIN ),
   *           'capability'     => self::MENU_CAPABILITY,
   *           'viewController' => 'WPXMailManagerConfigurationViewController'
   *         ),
   *       )
   *     );
   *
   *     WPDKSubMenu::renderByArray( $sub_menus );
   *
   * @brief Render by array
   *
   * @param array $sub_menus A key value pairs list of sub menus
   *
   * @return array
   *
   */
  public static function renderByArray( $sub_menus ) {
    $result = array();
    $index  = 1;
    foreach ( $sub_menus as $parent => $sub_menu ) {
      foreach ( $sub_menu as $sub_item ) {

        if ( is_string( $sub_item ) && WPDKSubMenuDivider::DIVIDER === $sub_item ) {
          $item = new WPDKSubMenuDivider( $parent );
        }
        elseif ( is_array( $sub_item ) ) {
          if ( isset( $sub_item[WPDKSubMenuDivider::DIVIDER] ) ) {
            $item = new WPDKSubMenuDivider( $parent, $sub_item[WPDKSubMenuDivider::DIVIDER] );
          }
          else {
            $id   = sprintf( '%s-submenu-%s', sanitize_key( $sub_item['viewController'] ), $index++ );
            $item = new WPDKSubMenu( $parent, $id, $sub_item['menuTitle'], $sub_item['viewController'] );
            /* Extra properties */
            foreach ( $sub_item as $property => $svalue ) {
              $item->$property = $svalue;
            }
          }
        }
        $item->render();

      }
      $result[] = $item;
    }
    return $result;
  }

  /**
   * Register this sub menu tree to WordPress menu
   *
   * @brief Render
   */
  public function render() {

    $hook = '';

    if ( !empty( $this->viewController ) ) {
      if( is_string( $this->viewController ) && !function_exists( $this->viewController ) ) {
        $hook = create_function( '', sprintf( '$view_controller = new %s; $view_controller->display();', $this->viewController ) );

        /* Create a global list of my own menu. */
        $GLOBALS[ WPDKMenu::GLOBAL_MENU ][$this->viewController] = array(
          'parent'     => $this->parent,
          'page'       => $this->id,
          'hook'       => '',
          'menu_title' => ''
        );
      }
      // If the callable is in the form array( obj, method ), I have to properly init $hook anyway
      elseif( is_callable( $this->viewController ) ) {
        $hook = $this->viewController;
      }
    }

    /* Apply filter for change the title. */
    $menu_title = apply_filters( 'wpdk_submenu_title', $this->menuTitle, $this->id, $this->parent );

    /* Create the menu item. */
    $this->hookName = add_submenu_page( $this->parent, $this->pageTitle, $menu_title, $this->capability, $this->id, $hook );

    if ( !empty( $this->viewController ) && is_string( $this->viewController ) && !function_exists( $this->viewController ) ) {

      $GLOBALS[WPDKMenu::GLOBAL_MENU][$this->viewController]['hook']       = $this->hookName;
      $GLOBALS[WPDKMenu::GLOBAL_MENU][$this->viewController]['menu_title'] = $menu_title;

      $will_load = create_function( '', sprintf( '%s::willLoad();', $this->viewController ) );
      add_action( 'load-' . $this->hookName, $will_load );

      $head = create_function( '', sprintf( '%s::didHeadLoad();', $this->viewController ) );
      add_action( 'admin_head-' . $this->hookName, $head );
    }
  }

}

/**
 * Description
 *
 * ## Overview
 *
 * Description
 *
 * @class           WPDKSubMenuDivider
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-02-22
 * @version         1.0.0
 *
 */
class WPDKSubMenuDivider extends WPDKSubMenu {

  /**
   * Default capability
   *
   * @brief Capability
   */
  const DEFAULT_CAPABILITY = 'manage_options';
  /**
   * Prefix to use on unique key for create a separator menu item
   *
   * @brief Divider
   */
  const DIVIDER = 'wpdk_menu_divider';

  /**
   * Create an instance of WPDKSubMenuDivider class
   *
   * @brief Construct
   *
   * @param object|string $parent Parent id menu
   * @param string        $title  Optional. Title of divider
   *
   * @return WPDKSubMenuDivider
   */
  public function __construct( $parent, $title = '' ) {
    static $index = 1;
    $id = sprintf( '%s-%s', self::DIVIDER, $index++ );
    parent::__construct( $parent, $id, $title );
    $this->capability = self::DEFAULT_CAPABILITY;
  }

}