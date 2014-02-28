<?php
/**
 * Manage the frontend theme area
 *
 * ## Overview
 *
 * This class is used when the frontend is loaded. You can subclassing this class for get a lot of facilities when you
 * have to manage the theme interactions.
 *
 * ### Benefits
 * This class prepare for us some useful and common action/filter hook.
 *
 * @class              WPDKWordPressTheme
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2014-01-08
 * @version            0.9.1
 */

class WPDKWordPressTheme extends WPDKObject {

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $__version
   */
  public $__version = '0.9.1';

  /**
   * Your main plugin instance
   *
   * @brief Plugin pointer
   *
   * @var WPDKWordPressPlugin $plugin
   */
  public $plugin;

  /**
   * Create a WPDKWordPressTheme object instance
   *
   * @brief Construct
   *
   * @param WPDKWordPressPlugin $plugin Optional. Your main plugin instance
   *
   * @return WPDKWordPressTheme
   */
  public function __construct( WPDKWordPressPlugin $plugin = null ) {
    $this->plugin = $plugin;

    /* Before init */
    add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );

    /* Core actions */
    add_action( 'wp', array( $this, 'wp' ) );
    add_action( 'wp_head', array( $this, 'wp_head' ) );
    add_action( 'wp_footer', array( $this, 'wp_footer' ) );

    /* Add classes to body class. */
    add_filter( 'body_class', array( $this, '_body_class' ) );

    /* Scripts and styles. */
    add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

    /* Template loader */
    add_action( 'template_redirect', array( $this, 'template_redirect' ) );
    add_filter( 'template_include', array( $this, 'template_include' ) );
  }

  // -----------------------------------------------------------------------------------------------------------------
  // WordPress Hook
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Called when WordPress makes the class attribute of `body` tag
   *
   * @brief Adding class in body tag
   *
   * @param array $classes List of classes
   *
   * @return array New list class
   */
  public function _body_class( $classes )
  {
    /* Auto insert the plugin slug in body class */
    if ( !is_null( $this->plugin ) ) {
      $classes[] = sprintf( ' %s-body', $this->plugin->slug );
    }
    return $classes;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Override
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Called by after_setup_theme action
   *
   * @brief WordPress action to setup theme
   */
  public function after_setup_theme()
  {
    /* To override */
  }

  /**
   * Called by `template_redirect` action. This action is called before the frontend theme is displayed.
   *
   * @brief WordPress action before theme is displayed
   */
  public function template_redirect()
  {
    /* To override */
  }

  /**
   * Called by `template_include` filter. This filter is useful to change the defaul theme filename.
   *
   * @brief WordPress filter before a template is loaded
   *
   * @param string $template The URL of template
   *
   * @return string A new URl template
   */
  public function template_include( $template )
  {
    /* To override */
    return $template;
  }

  /**
   * Called by `wp` action
   *
   * @brief WordPress action for start
   */
  public function wp()
  {
    /* To override */
  }

  /**
   * Called by `wp_head` action. This action is called after the `head` section and before the `body` tag.
   *
   * @brief WordPress action in head theme
   *
   */
  public function wp_head()
  {
    /* To override */
  }

  /**
   * Called by wp_footer action. This action is called in the footer theme.
   *
   * @brief WordPress action for theme footer
   *
   */
  public function wp_footer()
  {
    /* To override */
  }

  /**
   * Called by `wp_enqueue_scripts` action. You will use this action to register (do a queue) scripts and styles.
   *
   * @brief WordPress action for scripts and styles
   */
  public function wp_enqueue_scripts()
  {
    /* To override */
  }

}

/// @cond private

/*
 * [DRAFT]
 *
 * THE FOLLOWING CODE IS A DRAFT. FEEL FREE TO USE IT TO MAKE SOME EXPERIMENTS, BUT DO NOT USE IT IN ANY CASE IN
 * PRODUCTION ENVIRONMENT. ALL CLASSES AND RELATIVE METHODS BELOW CAN CHNAGE IN THE FUTURE RELEASES.
 *
 */

/**
 * Base class for front end theme. This class is used to develop a theme.
 *
 * @class           WPDKTheme
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-08
 * @version         1.1.1
 *
 */
class WPDKTheme extends WPDKObject {

  /**
   * Standard defines constants file
   */
  const DEFINES = 'wpx-defines.php';

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $__version
   */
  public $__version = '1.1.1';

  /**
   * The Theme URL more `assets/`. This property is very useful for read style sheet and Javascript file in the
   * 'assets' folder.
   *
   * @brief Assets URL
   *
   * @var string $assetsURL
   */
  public $assetsURL;

  /**
   * The Theme URL more `assets/css/`
   *
   * @brief Style sheet URL
   *
   * @var string $cssURL
   */
  public $cssURL;

  /**
   * The Theme URL more `assets/css/images/`
   *
   * @brief Images URL
   *
   * @var string $imagesURL
   */
  public $imagesURL;

  /**
   * The Theme URL more `assets/js/`
   *
   * @brief Javascript URL
   *
   * @var string $javascriptURL
   */
  public $javascriptURL;

  /**
   * Instance of WP_Theme class
   *
   * @brief WP Theme
   *
   * @var WP_Theme $theme
   */
  public $theme;

  /**
   * Filesystem theme path. Alias TEMPLATEPATH
   *
   * @brief Filesystem theme path
   *
   * @var string $path
   */
  public $path;

  /**
   * The Filesystem theme path more `classes/`
   *
   * @brief Classes path
   *
   * @var string $classesPath
   */
  public $classPath;

  /**
   * The array of loading path related to any WPX theme class. This array is related to the specific WPX theme
   * that extends this base class.
   *
   * @brief The array of loading path related to any WPX theme class.
   *
   * @var array $_wpxThemeClassLoadingPath
   *
   * @since 0.10.0
   */
  private $_wpxThemeClassLoadingPath;

  /**
   * Theme Setup class model
   *
   * @brief WPDKThemeSetup
   *
   * @var WPDKThemeSetup $setup
   */
  public $setup;

  /**
   * Create an instance of WPDKTheme class
   *
   * @brief Construct
   *
   * @param string              $file
   * @param bool|WPDKThemeSetup $setup Optional. A your custom theme setup class model
   *
   * @return WPDKTheme
   */
  public function __construct( $file, $setup = false ) {

    if ( false == $setup ) {
      $this->setup = $setup = new WPDKThemeSetup();
    }
    $this->setup = apply_filters( 'wpdk_theme_setup-' . $file, $setup );

    // Autoload
    $this->_wpxThemeClassLoadingPath = array();
    spl_autoload_extensions( '.php' ); // for faster execution
    spl_autoload_register( array( $this, 'autoloadEnvironment' ) );

    // Path unix
    $this->path        = trailingslashit( dirname( $file ) );
    $this->classesPath = $this->path . 'classes/';

    // URLs.
    $this->url           = trailingslashit( get_template_directory_uri() );
    $this->assetsURL     = $this->url . 'assets/';
    $this->cssURL        = $this->assetsURL . 'css/';
    $this->imagesURL     = $this->assetsURL . 'images/';
    $this->javascriptURL = $this->assetsURL . 'js/';

    $theme_key         = basename( dirname( $file ) );
    $theme_directories = search_theme_directories();

    $theme_file = $theme_directories[$theme_key]['theme_file'];
    $theme_root = $theme_directories[$theme_key]['theme_root'];

    // WP_Theme is a final class, so I must create a part object
    $this->theme = new WP_Theme( $theme_key, $theme_root );

    // Loading constants defnies
    $defines = trailingslashit( dirname( $file ) ) . self::DEFINES;
    if ( file_exists( $defines ) ) {
      require_once( $defines );
    }

    // Register autoload classes
    if ( method_exists( $this, 'registerClasses' ) ) {
      $this->registerClasses();
    }

    // Avoid access to admin
    add_action( 'admin_init', array( $this, 'admin_init' ) );

    // Cleanup
    add_action( 'init', array( $this, '_init' ) );

    // Setup
    add_action( 'init', array( $this, 'init_theme' ) );

    // Shortcodes
    add_action( 'init', array( $this, 'init_shortcode' ) );

    // Ajax setup
    if ( wpdk_is_ajax() ) {
      add_action( 'init', array( $this, 'ajax' ) );
    }

    // After setup
    add_action( 'after_setup_theme', array( $this, '_after_setup_theme' ) );
    add_action( 'after_setup_theme', array( $this, 'after_setup_theme' ) );

    // Add script and styles
    add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

    // Head
    add_action( 'wp_head', array( $this, '_wp_head' ), 0 );
    add_action( 'wp_head', array( $this, 'wp_head' ) );

    // Footer
    add_action( 'wp_footer', array( $this, 'wp_footer' ) );

    // Add classes to body class
    add_filter( 'body_class', array( $this, '_body_classes' ) );

    // Custom size
    add_filter( 'image_size_names_choose', array( $this, 'image_size_names_choose' ) );

  }

  /**
   * Do a several Clean Up
   *
   * @brief Clean Up
   */
  public function _init()
  {
    /* Text Domain */
    if( $this->setup->autoload_text_domain ) {
      load_theme_textdomain( $this->theme->get( 'TextDomain' ), trailingslashit( TEMPLATEPATH ) . $this->theme->get( 'DomainPath' ) );
    }

    /* Clean up wp_head */
    if ( $this->setup->cleanup_wp_head ) {
      remove_action( 'wp_head', 'feed_links_extra', 3 ); // Category Feeds
      remove_action( 'wp_head', 'feed_links', 2 ); // Post and Comment Feeds
      remove_action( 'wp_head', 'rsd_link' ); // EditURI link
      remove_action( 'wp_head', 'wlwmanifest_link' ); // Windows Live Writer
      remove_action( 'wp_head', 'index_rel_link' ); // index link
      remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // previous link
      remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // start link
      remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 ); // Links for Adjacent Posts
      remove_action( 'wp_head', 'wp_generator' ); // WP vers
    }
  }

  /**
   * Internal use
   *
   * @brief Setup theme
   */
  public function _after_setup_theme()
  {
    // Admin bar
    if ( !is_admin() && $this->setup->hide_admin_bar ) {
      show_admin_bar( false );
    }

    // Theme support
    if ( !empty( $this->setup->theme_support ) ) {
      $theme_supports = (array)$this->setup->theme_support;
      foreach ( $theme_supports as $args => $theme_support ) {
        if ( !is_numeric( $args ) && is_array( $theme_support ) ) {
          add_theme_support( $args, $theme_support );
        }
        else {
          add_theme_support( $theme_support );
        }
      }
    }

    // Images size
    if ( !empty( $this->setup->image_sizes ) ) {
      foreach ( $this->setup->image_sizes as $key => $size ) {
        $size = array_merge( $size, array( 0, 0, false ) );
        list( $w, $h, $crop ) = $size;
        add_image_size( $key, $w, $h, is_null( $crop ) ? false : $crop );
      }
    }

    // Set post thumbnail size
    if ( !empty( $this->setup->post_thumbnail_size ) ) {
      list( $w, $h, $crop ) = $this->setup->post_thumbnail_size;
      set_post_thumbnail_size( $w, $h, is_null( $crop ) ? false : $crop );
    }

    // Navigation menus
    if ( !empty( $this->setup->nav_menus ) ) {
      register_nav_menus( $this->setup->nav_menus );
    }

    // Sidebars
    if ( !empty( $this->setup->sidebars ) ) {
      foreach ( $this->setup->sidebars as $sidebar ) {
        if ( is_array( $sidebar ) ) {
          register_sidebar( $sidebar );
        }
      }
    }

    // Editor style
    if ( !empty( $this->setup->editor_styles ) ) {
      add_editor_style( $this->setup->editor_styles );
    }
  }

  /**
   * This action is used to avoid display the admin backend area to subscriber user
   *
   * @brief Avoid admin
   */
  public function admin_init()
  {

    if ( wpdk_is_ajax() ) {
      return;
    }

    if ( !is_user_logged_in() ) {
      return;
    }

    /* Check for roles */
    if ( !empty( $this->setup->disable_admin_for_roles ) ) {
      // Not implement
    }

    /* Check for capabilies */
    if ( !empty( $this->setup->disable_admin_if_user_has_not_caps ) ) {
      $pass = false;
      $roles = $this->setup->disable_admin_if_user_has_not_caps;
      if ( !empty( $roles ) && is_array( $roles ) ) {
        foreach ( $roles as $role ) {
          if ( ( $pass = current_user_can( $role ) ) ) {
            break;
          }
        }
      }
      elseif ( !empty( $roles ) && is_string( $roles ) ) {
        $pass = current_user_can( $roles );
      }
      if ( !$pass ) {
        die();
      }
    }
  }

  /**
   * Init the theme
   *
   * @brief Set Up
   */
  public function init_theme() {
    /* You can override in your subclass. */
  }

  /**
   * Subclass for inti your shortcode.
   *
   * @brief Init shortcodes
   */
  public function init_shortcode() {
    /* You can override in your subclass. */
  }

  /**
   * Subclass for init Ajax gateway
   *
   * @brief Your main init
   */
  public function ajax() {
    /* You can override in your subclass. */
  }

  /**
   * Setup theme
   *
   * @brief Setup theme
   * @note To override
   *
   */
  public function after_setup_theme() {
    die( __METHOD__ . ' must be override in your subclass' );
  }

  /**
   * Description
   *
   * @brief Script and styles
   */
  public function wp_enqueue_scripts() {
    /* You can override in your subclass */
  }

  /**
   * Include standard WPDK Theme styles and script
   *
   * @brief WPDK Theme
   */
  public function _wp_head()
  {
    if ( !empty( $this->setup->normalize_css ) ) {
      wp_enqueue_style( 'normalize', WPDK_URI_CSS . 'normalize.css', array(), WPDK_VERSION );
    }

    if ( !empty( $this->setup->theme_js ) ) {
      wp_enqueue_script( 'wpdk-theme', WPDK_URI_JAVASCRIPT . 'wpdk-theme.js', array(), WPDK_VERSION );
    }
  }

  /**
   * Subclass to insert output in head
   *
   * @brief wp_head
   */
  public function wp_head()
  {
    /* You can override in your subclass. */
  }

  /**
   * Subclass to insert output in the fotter
   *
   * @brief wp_footer
   */
  public function wp_footer()
  {
    /* You can override in your subclass. */
  }

  /**
   * Add a your custom class in body tag class attribute and make the array (classes attribute) unique.
   *
   * @brief BODY class
   */
  public function _body_classes( $classes )
  {
    if ( !empty( $this->setup->body_classes ) ) {
      if ( is_string( $this->setup->body_classes ) ) {
        $classes[] = $this->setup->body_classes;
      }
      elseif ( is_array( $this->setup->body_classes ) ) {
        $classes = array_merge( $classes, $this->setup->body_classes );
      }
    }
    return array_unique( $classes );
  }

  /**
   * Allows modification of the list of image sizes that are available to administrators in the WordPress Media Library.
   *
   * @brief Brief
   *
   * @param array $sizes Sizes list
   *
   * @return array
   */
  public function image_size_names_choose( $sizes )
  {
    if ( !empty( $this->setup->image_sizes ) ) {
      foreach ( $this->setup->image_sizes as $key => $size ) {
        $size = array_merge( $size, array( 0, 0, 0, 0 ) );
        list( $w, $h, $crop, $label ) = $size;
        $sizes[$key] = empty( $label ) ? $key : $label;
      }
    }
    return $sizes;
  }

  /**
   * This function records a WPX theme class into autoloading register, joined with its loading path.
   * The function has some facility in its first param, in order to allow both string and array loading of class
   * names ( useful in case of a group of classes that are defined in a single file ).
   *
   * 1. $this->registerAutoloadClass( 'file.php', 'ClassName' );
   *
   * 2. $this->registerAutoloadClass( array( 'file.php' => 'ClassName' ) );
   *
   * 3. $this->registerAutoloadClass( array( 'file.php' => array( 'ClassName', 'ClassName', ... ) ) );
   *
   *
   * @brief Records a WPX theme class into autoloading register.
   *
   * @param string|array $sLoadingPath Path of class when $mClassName is a string
   * @param string       $mClassName   Optional. The single class name or key value pairs array with path => classes
   */
  public function registerAutoloadClass( $sLoadingPath, $mClassName = '' ) {

    // 1
    if ( is_string( $sLoadingPath ) && is_string( $mClassName ) && !empty( $mClassName ) ) {
      $sClassNameLowerCased                                   = strtolower( $mClassName );
      $this->_wpxThemeClassLoadingPath[$sClassNameLowerCased] = $sLoadingPath;
    }

    // 2.
    elseif ( is_array( $sLoadingPath ) ) {
      foreach ( $sLoadingPath as $path => $classes ) {
        if ( is_string( $classes ) ) {
          $class_name                                   = strtolower( $classes );
          $this->_wpxThemeClassLoadingPath[$class_name] = $path;
        }

        // 3.
        elseif ( is_array( $classes ) ) {
          foreach ( $classes as $class_name ) {
            $class_name                                   = strtolower( $class_name );
            $this->_wpxThemeClassLoadingPath[$class_name] = $path;
          }
        }
      }
    }
  }

  /**
   * This function performs runtime autoloading of a class specifically related to this instance; this autoloading
   * is based on previous class registering that has to be executed before.
   *
   * NOTE: this SPL autoloading logic is encapsulated in every single plugin instance that extends this class,
   * because it is embedded into an instance of WPDKWordPressPlugin. So any plugin has its own SPL autoloading logic.
   *
   * @brief Runtime autoloading of theme classes.
   *
   * @param string $sClassName - The class that has to be loaded right now
   *
   */
  public function autoloadEnvironment( $sClassName ) {

    // For backward compatibility and for better matching
    $sClassNameLowerCased = strtolower( $sClassName );
    if ( isset( $this->_wpxThemeClassLoadingPath[$sClassNameLowerCased] ) ) {
      require_once( $this->_wpxThemeClassLoadingPath[$sClassNameLowerCased] );
    }

  }

}

/**
 * A model to setup your theme
 *
 * @class           WPDKThemeSetup
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2014-02-11
 * @version         1.0.2
 *
 */
class WPDKThemeSetup {

  /**
   * Disable the access to admin if a user logged in has not these roles
   *
   *     // OFF
   *     $this->disable_admin_for_roles = false;
   *     $this->disable_admin_for_roles = array();
   *
   *     // Enable admin backend for editor only
   *     $this->disable_admin_for_roles = 'editor';
   *     $this->disable_admin_for_roles = array( 'administrator', 'editor' );
   *
   * @var bool $disable_admin_for_roles
   */
  public $disable_admin_for_roles = array();

  /**
   * Disable the admin access if a user logged in has not these caps
   *
   *     // OFF
   *     $this->disable_admin_if_user_has_not_caps = false;
   *     $this->disable_admin_if_user_has_not_caps = array();
   *
   *     // Enable admin backend for admin only
   *     $this->disable_admin_if_user_has_not_caps = 'manage_options';
   *     $this->disable_admin_if_user_has_not_caps = array( 'manage_options' );
   *
   *     // Enable admin backend for admin and editor only
   *     $this->disable_admin_if_user_has_not_caps = array( 'manage_options', 'editor' );
   *
   * @var bool $disable_admin_if_user_has_not_caps
   */
  public $disable_admin_if_user_has_not_caps = array();

  /**
   * Remove standard filter to wp_head hook
   *
   *     remove_action( 'wp_head', 'feed_links_extra', 3 ); // Category Feeds
   *     remove_action( 'wp_head', 'feed_links', 2 ); // Post and Comment Feeds
   *     remove_action( 'wp_head', 'rsd_link' ); // EditURI link
   *     remove_action( 'wp_head', 'wlwmanifest_link' ); // Windows Live Writer
   *     remove_action( 'wp_head', 'index_rel_link' ); // index link
   *     remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 ); // previous link
   *     remove_action( 'wp_head', 'start_post_rel_link', 10, 0 ); // start link
   *     remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 ); // Links for Adjacent Posts
   *     remove_action( 'wp_head', 'wp_generator' ); // WP vers
   *
   * @brief Clean UP
   *
   * @var bool $cleanup_wp_head
   */
  public $cleanup_wp_head = true;

  /**
   * Autoload the localization if supported.
   * Create a 'localization' folder in you root theme
   */
  public $autoload_text_domain = true;

  /**
   * Hide the admin bar in front-end
   *
   * @brief Hide admin bar
   *
   * @var bool $hide_admin_bar
   */
  public $hide_admin_bar = true;

  /**
   * Setup theme support.
   *
   *     $this->theme_support = 'post-thumbnails';
   *     $this->theme_support = array( 'post-thumbnails', 'menus' );
   *     $this->theme_support = array( 'post-thumbnails' => array( 'aside', 'gallery', ... ), 'menus' );
   *
   * @brief Add theme support
   *
   * @var array|string $theme_support
   */
  public $theme_support = array();

  /**
   * Setup your custom image size
   *
   *     $this->image_sizes = array(
   *        'your_custom_size_id' => array( 100, 100, true ),
   *        'your_custom_size_id' => array( 100, 100 ),
   *        'your_custom_size_id' => array( 100, 100, false, 'Your Description' ),
   *     );
   *
   * @brief Image Size
   *
   * @var array $image_sizes
   */
  public $image_sizes = array();

  /**
   * Setup default post thumbnail size
   *
   *     $this->post_thumbnail_size = array( 256, 256 );
   *     $this->post_thumbnail_size = array( 256, 256, true );
   *
   * @brief Thumbnail size
   *
   * @var array $post_thumbnail_size
   */
  public $post_thumbnail_size = array();

  /**
   * Setup a navigation menus list
   *
   *     $this->nav_menus = array(
   *         'xtreme_main_menu'   =>  'Main Menu',
   *         'xtreme_footer_menu' => 'Footer Menu wpXtreme'
   *     );
   *
   * @brief Nav Menu
   *
   * @var array $nav_menus
   */
  public $nav_menus = array();

  /**
   * Setup the side bars
   *
   *     $this->sidebars = array(
   *        array(
   *          'id'            => 'sidebar_single_post',
   *          'name'          => 'Single Post',
   *          'description'   => 'Sidebar for post blog',
   *          'class'         => '',
   *          'before_widget' => '<li id="%1$s" class="widget %2$s">',
   *          'after_widget'  => '</li>',
   *          'before_title'  => '<h2 class="widgettitle">',
   *          'after_title'   => '</h2>'
   *         ), ...
   *     );
   *
   *     // All array key/values are optionals, you cau use also
   *
   *     $this->sidebars = array(
   *        array(
   *          'name' => 'Single Post',
   *         ),
   *        array(
   *          'name'          => 'Single Page',
   *          'description'   => 'Sidebar for page',
   *         ),
   *        array(
   *          'name' => 'Another sidebar',
   *          'id'   => 'with_id',
   *         ), ...
   *      );
   *
   * @brief Sidebar
   *
   * @var array $sidebars
   */
  public $sidebars = array();

  /**
   * Setup the body classes
   *
   * @brief BODY classes
   *
   * @var array|string $body_classes
   */
  public $body_classes = array();

  /**
   * Setup the style for the editor
   *
   * @brief Style for editor
   *
   * @var array|string $editor_styles
   */
  public $editor_styles = array();

  /**
   * Include standard WPDK Theme Javascript
   *
   * @brief Theme Javascript
   *
   * @var bool $theme_js
   */
  public $theme_js = true;

  /**
   * Include standard WPDK Theme (reset) styles
   *
   * @brief Theme CSS
   * @deprecated since 1.4.21 - use normalize
   *
   * @var bool $theme_css
   */
  public $theme_css = true;

  /**
   * If TRUE normalize.css will be load ( http://necolas.github.io/normalize.css/ )
   *
   * @brief Normalize
   *
   * @var bool $normalize_css
   */
  public $normalize_css = true;

  /**
   * Create an instance of WPDKThemeSetup class
   *
   * @brief Construct
   *
   * @return WPDKThemeSetup
   */
  public function __construct()
  {
  }

}

/// @endcond