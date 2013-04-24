<?php
/// @cond private

/**
 * Class for create and managment WordPress menu Page.
 *
 * ## Overview
 * This class could create one or more menu tree with sub menu. This class allow to set admin_head hook and menu loaded
 * hook directly array menu definition.
 *
 * ### Create a menu
 * To create single or tree menu, just define an array like this:
 *
 *     $menus = array(
 *       'wpxm_menu_wpxtreme' => array(
 *         'page_title' => 'wpXtreme',
 *         'menu_title' => 'wpXtreme',
 *         'capability' => self::MENU_CAPABILITY,
 *         'hook'       => array( $this, 'menu_plugin_store' ),
 *         'icon'       => $icon_menu,
 *         'position'   => 1,
 *         'items'      => array(
 *             'wpxm_menu_wpxtreme' => array(
 *               'page_title'  => __( 'Breaking News', WPXTREME_TEXTDOMAIN ),
 *               'menu_title'  => __( 'Breaking News', WPXTREME_TEXTDOMAIN ),
 *               'capability'  => self::MENU_CAPABILITY,
 *               'hook'        => array( $this, 'menu_plugin_store' ),
 *             ),
 *             'wpxm_menu_wpxtreme_info' => array(
 *              'menu_title' => __( 'More', WPXUSERSMANAGER_TEXTDOMAIN ),
 *              'capability' => self::MENU_CAPABILITY,
 *             ),
 *            'wpxm_menu_wpxtreme_info' => array(
 *             'page_title'  => __( 'Info', WPXTREME_TEXTDOMAIN ),
 *             'menu_title'  => __( 'Info', WPXTREME_TEXTDOMAIN ),
 *             'capability'  => self::MENU_CAPABILITY,
 *             'hook'        => array( $this, 'menu_plugin_store' ),
 *           ),
 *         )
 *       ),
 *     )
 *
 * When your array is complete, you can invoke the `doMenu()` method after instance the class:
 *
 *     $menu = new WPDKMenuPage( $array );
 *     $menu->doMenu();
 *
 *
 *
 * ### Array parameters
 *
 * If `page_title` is not set then `menu_title` is used instead.
 *
 *
 *
 * ### WordPress hooks
 *
 * The settable hooks are:
 *
 * * hook - Display the content
 * * load - add action on 'load-[id menu]'
 * * admin_head - add action on 'admin_head-[id menu]'
 *
 *
 *
 * ### View Controller
 *
 * For make it too easy you can use a view controller. For example you can use the key `view_controller` instead `hook`,
 * `load` and `admin_head`:
 *
 *     $menus = array(
 *       'wpxm_menu_wpxtreme' => array(
 *         'page_title' => 'wpXtreme',
 *         'menu_title' => 'wpXtreme',
 *         'capability' => self::MENU_CAPABILITY,
 *         'hook'       => array( $this, 'menu_plugin_store' ),
 *         'icon'       => $icon_menu,
 *         'position'   => 1,
 *         'items'      => array(
 *           'wpxm_menu_wpxtreme' => array(
 *               'page_title'  => __( 'Breaking News', WPXTREME_TEXTDOMAIN ),
 *               'menu_title'  => __( 'Breaking News', WPXTREME_TEXTDOMAIN ),
 *               'capability'  => self::MENU_CAPABILITY,
 *               'hook'        => array( $this, 'menu_plugin_store' ),
 *           ),
 *           'wpxm_menu_wpxtreme_info' => array(
 *               'page_title'      => __( 'Info', WPXTREME_TEXTDOMAIN ),
 *               'menu_title'      => __( 'Info', WPXTREME_TEXTDOMAIN ),
 *               'capability'      => self::MENU_CAPABILITY,
 *               'view_controller' => array( $this->plugin->classesPath . 'vc/my-vc.php', 'MyClassViewController' ),
 *               'view_controller' => 'MyClassViewController' // If autoload,
 *           ),
 *         )
 *       ),
 *     );
 *
 * The key `view_controller` create an instance of class `MyClassViewController` and load the source only the view has
 * been required. The WPDKViewController class has two static method that you can override for catch `load-` and
 * `admin_head-` action.
 *
 *
 * @class              WPDKMenuPage
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-02-06
 * @version            0.20.1
 * @deprecated         Since 1.0.0.b4 - Use WPDKMenu instead
 *
 */
class WPDKMenuPage {

  /**
   * Prefix to use on unique key for create a separator menu item
   *
   * @brief Separator
   */
  const SEPARATOR = 'wpxm_menu_title';

  /**
   * List of menus and sub menus
   *
   * @brief Menus
   *
   * @var array $menus
   */
  public $menus;

  /**
   * Create an instance of WPDKMenuPage class
   *
   * @brief Construct
   *
   * @param array $menus List of menu and submenu to create
   *
   * @return WPDKMenuPage
   */
  public function __construct( $menus = array() ) {
    $this->menus = $menus;

  }

  /**
   * Create new (add) or modify a menu page item. If $menu_slug exists the method change the values.
   *
   * @brief Create or modify a menu page item
   *
   * @param string       $menu_slug        This sub menu slug
   * @param string       $page_title       The title of page on browser
   * @param string       $menu_title       Title of sub menu displayed on WordPress menu
   * @param string       $capability       Capability for display this sub menu
   * @param string|array $hook             Callable to display
   * @param string       $icon             URL image
   * @param int|null     $position         Position in WordPress tree
   *
   */
  public function menu( $menu_slug, $page_title, $menu_title, $capability, $hook, $icon, $position = null ) {
    $this->menus[$menu_slug] = array(
      'page_title' => $page_title,
      'menu_title' => $menu_title,
      'capability' => $capability,
      'hook'       => $hook,
      'icon'       => $icon,
      'position'   => $position,
    );
  }

  /**
   * Create or modify a sub menu item
   *
   * @brief Create or modify a sub menu item
   *
   * @param string       $parent_menu_slug Parent slug menu
   * @param string       $menu_slug        This sub menu slug
   * @param string       $page_title       The title of page on browser
   * @param string       $menu_title       Title of sub menu displayed on WordPress menu
   * @param string       $capability       Capability for display this sub menu
   * @param string|array $hook             Callable to display
   */
  public function submenu( $parent_menu_slug, $menu_slug, $page_title, $menu_title, $capability, $hook ) {
    $this->menus[$parent_menu_slug]['items'][$menu_slug] = array(
      'page_title' => $page_title,
      'menu_title' => $menu_title,
      'capability' => $capability,
      'hook'       => $hook,
    );
  }


  /**
   * Register menus array to WordPress environment for display in admin backend.
   *
   * @brief Register menu in WordPress environment
   */
  public function doMenu() {

    if ( !empty( $this->menus ) ) {
      foreach ( $this->menus as $menu_page_slug => &$menu_page ) {

        $menu_title = apply_filters( 'wpdk_menupage_menutitle_' . $menu_page_slug, $menu_page['menu_title'] );

        /* Check for view controller. */
        if ( isset( $menu_page['view_controller'] ) ) {
          /* $menu_page['view_controller'] can be an array or a string. If string is only class name beacuse the autoload is ready. */
          $class_name      = is_array( $menu_page['view_controller'] ) ? $menu_page['view_controller'][1] : $menu_page['view_controller'];
          $hook            = create_function( '', sprintf( '$view_controller = new %s; $view_controller->display();', $class_name ) );
          $menu_page['id'] = add_menu_page( isset( $menu_page['page_title'] ) ? $menu_page['page_title'] : $menu_title, $menu_title, $menu_page['capability'], $menu_page_slug, $hook, isset( $menu_page['icon'] ) ? $menu_page['icon'] : '', isset( $menu_page['position'] ) ? $menu_page['position'] : null );
        }
        else {
          if ( !isset( $menu_page['hook'] ) ) {
            $menu_page['hook'] = null;
          }
          $menu_page['id'] = add_menu_page( isset( $menu_page['page_title'] ) ? $menu_page['page_title'] : $menu_title, $menu_title, $menu_page['capability'], $menu_page_slug, $menu_page['hook'], isset( $menu_page['icon'] ) ? $menu_page['icon'] : '', isset( $menu_page['position'] ) ? $menu_page['position'] : null );
        }

        /* Submenus items. */
        if ( !empty( $menu_page['items'] ) ) {
          foreach ( $menu_page['items'] as $sub_menu_slug => &$sub_menu ) {

            $sub_menu_title = apply_filters( 'wpdk_menupage_submenutitle_' .
              $sub_menu_slug, isset( $sub_menu['menu_title'] ) ? $sub_menu['menu_title'] : '' );

            /* Check for view controller. */
            if ( isset( $sub_menu['view_controller'] ) ) {
              $class_name     = is_array( $sub_menu['view_controller'] ) ? $sub_menu['view_controller'][1] : $sub_menu['view_controller'];
              $hook           = create_function( '', sprintf( '$view_controller = new %s; $view_controller->display();', $class_name ) );
              $sub_menu['id'] = add_submenu_page( $menu_page_slug, isset( $sub_menu['page_title'] ) ? $sub_menu['page_title'] : $sub_menu_title, $sub_menu_title, $sub_menu['capability'], $sub_menu_slug, $hook );

              $hook_id = $sub_menu['id'];
              if ( $sub_menu_slug === $menu_page_slug ) {
                $hook_id = $menu_page['id'];
              }

              if ( is_array( $sub_menu['view_controller'] ) ) {
                $load = create_function( '', sprintf( 'require_once( \'%s\' );', $sub_menu['view_controller'][0] ) );
                add_action( 'load-' . $hook_id, $load );
              }

              $will_load = create_function( '', sprintf( '%s::willLoad();', $class_name ) );
              add_action( 'load-' . $hook_id, $will_load );

              $head = create_function( '', sprintf( '%s::didHeadLoad();', $class_name ) );
              add_action( 'admin_head-' . $hook_id, $head );
            }

            /* Custom behaviour. */
            else {
              if ( !isset( $sub_menu['hook'] ) ) {
                $sub_menu['hook'] = null;
              }

              $sub_menu['id'] = add_submenu_page( $menu_page_slug, isset( $sub_menu['page_title'] ) ? $sub_menu['page_title'] : $sub_menu_title, $sub_menu_title, $sub_menu['capability'], $sub_menu_slug, $sub_menu['hook'] );

              $hook_id = $sub_menu['id'];
              if ( $sub_menu_slug === $menu_page_slug ) {
                $hook_id = $menu_page['id'];
              }

              /* Check for admin_head- hook */
              if ( isset( $sub_menu['admin_head'] ) ) {
                add_action( 'admin_head-' . $hook_id, $sub_menu['admin_head'] );
              }

              /* Check for load- hook */
              if ( isset( $sub_menu['load'] ) ) {
                add_action( 'load-' . $hook_id, $sub_menu['load'] );
              }
            }
          }
        }
      }
    }
  }

  /**
   * Experimental: return a seprator key autoincrement
   *
   * @brief Separator
   *
   * @return string
   */
  public static function separator() {
    static $sepratore_index = 1;
    return WPDKMenuPage::SEPARATOR . '#' . $sepratore_index++;
  }


}

/// @endcond