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
    $this->id             = sanitize_title( $id );
    $this->menuTitle      = $menu_title;
    $this->pageTitle      = $menu_title;
    $this->capability     = $capability;
    $this->position       = $position;
    $this->icon           = $icon;
    $this->subMenus       = array();
  }

  /**
   * Return a sanitize view controller for a callable
   *
   * @brief Sanitize
   *
   * @param string|array $view_controller Callable
   *
   * @return string
   */
  public static function sanitizeViewController( $view_controller )
  {
    if ( is_string( $view_controller ) ) {
      $result = $view_controller;
    }
    elseif ( is_array( $view_controller ) ) {
      $result = get_class( $view_controller[0] ) . '-' . $view_controller[1];
    }
    return $result;
  }

  /**
   * Return the WPDK menu info by name of view controller of submenu item
   *
   * @param string $view_controller The view controller class name
   *
   * @return array
   */
  public static function menu( $view_controller )
  {
    $global_key = self::sanitizeViewController( $view_controller );

    if ( isset( $GLOBALS[self::GLOBAL_MENU] ) ) {
      if ( !empty( $global_key ) && !empty( $GLOBALS[self::GLOBAL_MENU][$global_key] ) ) {
        return $GLOBALS[self::GLOBAL_MENU][$global_key];
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
  public static function url( $view_controller )
  {
    $info = self::menu( $view_controller );

    $url = '';
    if ( !empty( $info ) ) {

      if ( false === strpos( '.php', $info['parent'] ) ) {
        $url = add_query_arg( array( 'page' => $info['page'] ), admin_url( 'admin.php' ) );
      }
      else {
        $url = add_query_arg( array( 'page' => $info['page'] ), admin_url( $info['parent'] ) );
      }
    }
    return $url;
  }

  /**
   * Return the hook id for a view controller
   *
   * @brief Hook id
   * @since 1.2.0
   *
   * @param string $view_controller Class name of view controller
   *
   * @return string
   */
  public static function hook( $view_controller )
  {
    $info = self::menu( $view_controller );

    if ( !empty( $info ) ) {
      return $info['hook'];
    }
    return false;
  }

  /**
   * Return the page id for a view controller
   *
   * @brief Page id
   * @since 1.2.0
   *
   * @param string $view_controller Class name of view controller
   *
   * @return string
   */
  public static function page( $view_controller )
  {
    $info = self::menu( $view_controller );

    if ( !empty( $info ) ) {
      return $info['page'];
    }
    return false;
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

  /**
   * Add a submenu item at index position into the `$menus` array,
   *
   * ### Sample
   *
   *     $menus = array(
   *       'wpx_ras_main' => array(
   *         'menuTitle'  => __( 'REST API Server', WPXRESTAPISERVER_TEXTDOMAIN ),
   *         'capability' => self::MENU_CAPABILITY,
   *         'icon'       => $icon_menu,
   *         'subMenus'   => array(
   *
   *           array(
   *             'menuTitle' =>  __( 'Servers', WPXRESTAPISERVER_TEXTDOMAIN ),
   *             'viewController' => 'WPXRESTAPIServerServersListTableViewController',
   *             'capability'     => self::MENU_CAPABILITY,
   *           ),
   *
   *           WPDKSubMenuDivider::DIVIDER,
   *
   *           array(
   *             'menuTitle' =>  __( 'Preferences' ),
   *             'viewController' => 'WPXRESTAPIServerConfigurationCoreViewController',
   *             'capability'     => self::MENU_CAPABILITY,
   *           ),
   *           array(
   *             'menuTitle' =>  __( 'About' ),
   *             'capability'     => self::MENU_CAPABILITY,
   *             'viewController' => 'WPXRESTAPIServerAboutViewController',
   *           ),
   *         )
   *       )
   *     );
   *
   *     $new = array(
   *       'menuTitle'      => __( 'New Menu' ),
   *       'viewController' => 'WPXRESTAPIServerConfigurationCoreViewController',
   *       'capability'     => self::MENU_CAPABILITY,
   *     );
   *
   *     WPDKMenu::addSubMenuAt( $menus, $new, 2 );
   *
   *     WPDKMenu::renderByArray( $menus );
   *
   *
   * @brief Add submenu
   *
   * @param array $menus     Array menu used in WPDKMenu::renderByArray() method
   * @param array $menu_item Single new array to add
   * @param int   $index     Start position from the first menu item (zero base).
   *
   * @return array
   */
  public static function addSubMenuAt( &$menus, $menu_item, $index )
  {
    $key = key( $menus );
    if ( isset( $menus[$key]['subMenus'] ) ) {
      $index = ( $index < 0 ) ? count( $menus[$key]['subMenus'] ) + 1 : $index;
      $menus[$key]['subMenus'] = WPDKArray::insert( $menus[$key]['subMenus'], array( $menu_item ), $index );
    }
    else {
      $menus[$key] = WPDKArray::insert( $menus[$key], array( $menu_item ), $index );
    }
    return $menus;
  }

  /**
   * Recursive version of self::addSubMenuAt()
   *
   *     $submenus = array(
   *       WPDKSubMenuDivider::DIVIDER,
   *       array(
   *         'menuTitle'      => __( 'Extensions', WPXTREME_TEXTDOMAIN ),
   *         'capability'     => self::MENU_CAPABILITY,
   *         'viewController' => array( $this, 'about' ),
   *       ),
   *       array(
   *         'menuTitle'      => __( 'About', WPXTREME_TEXTDOMAIN ),
   *         'capability'     => self::MENU_CAPABILITY,
   *         'viewController' => array( $this, 'about' ),
   *       )
   *     );
   *
   *     WPDKMenu::addSubMenusAt( $this->menus, $submenus, -1 );
   *
   * @brief Brief
   * @since 1.3.1
   *
   * @param array $menus     Array menu used in WPDKMenu::renderByArray() method
   * @param array $submenus  Array of new menu
   * @param int   $index     Start position from the first menu item (zero base).
   *
   * @return mixed
   */
  public static function addSubMenusAt( &$menus, $submenus, $index )
  {
    $key = key( $menus );
    if ( isset( $menus[$key]['subMenus'] ) ) {
      $pos = ( $index < 0 ) ? count( $menus[$key]['subMenus'] ) + 1 : $index;
      foreach ( $submenus as $menu ) {
        $menus[$key]['subMenus'] = WPDKArray::insert( $menus[$key]['subMenus'], array( $menu ), $pos++ );
      }
    }
    else {
      $pos = ( $index < 0 ) ? count( $menus[$key] ) + 1 : $index;
      foreach ( $submenus as $menu ) {
        $menus[$key] = WPDKArray::insert( $menus[$key], array( $menu ), $pos++ );
      }
    }
    return $menus;
  }

  /**
   * Return TRUE if the displayed page is the view controller
   *
   * @brief Check displayed page
   * @since 1.2.0
   *
   * @param string $id The menu id
   *
   * @return bool
   */
  public static function isPageWithMenu( $id )
  {
    global $plugin_page;
    if ( $id === $plugin_page ) {
      return true;
    }
    return false;
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

  public $capability = self::DEFAULT_CAPABILITY;
  public $hookName = '';
  public $id = '';
  public $menuTitle = '';
  public $pageTitle = '';
  public $parent = '';
  public $viewController = '';

  /**
   * Query args to add to url page
   *
   * @brief Query args
   * @since 1.3.1
   *
   * @var array $query_args
   */
  public $query_args = array();

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
    $this->id             = sanitize_title( $id );
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
        $item = false;
        if ( is_string( $sub_item ) && WPDKSubMenuDivider::DIVIDER === $sub_item ) {
          $item = new WPDKSubMenuDivider( $parent );
        }
        elseif ( is_array( $sub_item ) ) {
          if ( isset( $sub_item[WPDKSubMenuDivider::DIVIDER] ) ) {
            $item = new WPDKSubMenuDivider( $parent, $sub_item[WPDKSubMenuDivider::DIVIDER] );
          }
          else {
            if( is_array( $sub_item['viewController'] ) ) {
              $id = sprintf( '%s-submenu-%s', sanitize_title( $sub_item['viewController'][1] ), $index++ );
            } else {
              $id = sprintf( '%s-submenu-%s', sanitize_title( $sub_item['viewController'] ), $index++ );
            }
            $item = new WPDKSubMenu( $parent, $id, $sub_item['menuTitle'], $sub_item['viewController'] );
            /* Extra properties */
            foreach ( $sub_item as $property => $svalue ) {
              $item->$property = $svalue;
            }
          }
        }
        if( !empty( $item ) ) {
          $item->render();
        }
      }
      if( !empty( $item ) ) {
        $result[] = $item;
      }
    }
    return $result;
  }

  /**
   * Register this sub menu tree to WordPress menu
   *
   * @brief Render
   */
  public function render() {

    global $plugin_page;

    $hook       = '';
    $global_key = WPDKMenu::sanitizeViewController( $this->viewController );

    if ( !empty( $this->viewController ) ) {
      if ( is_string( $this->viewController ) && !function_exists( $this->viewController ) ) {
        /* @todo Think $vc = %s::init() - in this way we can use the singleton in the head hook below */
        $hook = create_function( '', sprintf( '$view_controller = new %s; $view_controller->display();', $this->viewController ) );
      }
      // If the callable is in the form array( obj, method ), I have to properly init $hook anyway
      elseif ( is_callable( $this->viewController ) ) {
        $hook = $this->viewController;
      }
    }

    if( !empty( $global_key ) ) {
      /* Create a global list of my own menu. */
      $GLOBALS[WPDKMenu::GLOBAL_MENU][$global_key ] = array(
        'parent'     => $this->parent,
        'page'       => $this->id,
        'hook'       => '',
        'menu_title' => ''
      );
    }

    /* Apply filter for change the title. */
    $menu_title = apply_filters( 'wpdk_submenu_title', $this->menuTitle, $this->id, $this->parent );

    /* Create the menu item. */
    $this->hookName = add_submenu_page( $this->parent, $this->pageTitle, $menu_title, $this->capability, $this->id, $hook );

    /* Check for query args. */
    if ( isset( $this->query_args ) && !empty( $this->query_args ) ) {
      $stack = array();
      foreach ( $this->query_args as $var => $value ) {
        $stack[] = sprintf( '$_GET["%s"] = $_REQUEST["%s"] = "%s";', $var, $var, $value );
      }
      $func = create_function( '', implode( '', $stack ) );
      add_action( 'load-' . $this->hookName, $func );
    }

    /* Execute this action when the page displayed ids for this submenu view. */
    if( !empty( $plugin_page ) ) {
      if( $this->id === $plugin_page ) {
        do_action( 'wpdk_submenu_page', $this, $plugin_page );
      }
    }

    if ( !empty( $this->viewController ) && is_string( $this->viewController ) && !function_exists( $this->viewController ) ) {
      $will_load = create_function( '', sprintf( '%s::willLoad();', $this->viewController ) );
      add_action( 'load-' . $this->hookName, $will_load );

      $head = create_function( '', sprintf( '%s::didHeadLoad();', $this->viewController ) );
      add_action( 'admin_head-' . $this->hookName, $head );
    }

    if ( !empty( $global_key ) ) {
      $GLOBALS[WPDKMenu::GLOBAL_MENU][$global_key]['hook']       = $this->hookName;
      $GLOBALS[WPDKMenu::GLOBAL_MENU][$global_key]['menu_title'] = $menu_title;
    }
  }

}

/**
 * Submenu divider
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