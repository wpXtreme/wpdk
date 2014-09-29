<?php
/**
 * The WPDKWordPressPlugin class is the most important class of all in WordPress Development Kit (WPDK) because
 * performs all init procedures for a Plugin that wants to be compatible with the wpXtreme standard.
 *
 * ## Overview
 *
 * The WPDKWordPressPlugin class provides the fundamental startup of plugin. You rarely (never) instantiate
 * WPDKWordPressPlugin object directly. Instead, you instantiate subclasses of the WPDKWordPressPlugin class.
 *
 * ### Subclassing
 *
 * This class **must** be used to extend the main class of your plugin. Its function is to initialize the environment
 * will operate on the plugin itself and record the Plugin for updates from WPX Store.
 *
 * ### Benefits
 *
 * In addition to initializing and recording, WPDKWordPressPlugin class performs for us a whole series of standard
 * procedures in the writing of Plugin, in addition to providing a lot of properties and methods really comfortable.
 *
 * * Gets directly from standard WordPress comments prior information as to the description of the Plugin: plugin name, version, the text and the text domain domain path
 * * Prepare a set of standard properties with paths and urls most commonly used
 * * Provides a lot of hook to wrap (filters and actions) of most used WordPress
 * * Prepare an instance of `WPDKWatchDog` object for own log
 *
 * ### Useful properties
 * The path propertis are the filesystem unix path. The URL properties are the HTTP protocol URI.
 *
 * See below for detail
 *
 * ### Useful methods
 * See below for detail
 *
 * @class              WPDKWordPressPlugin
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-08-19
 * @version            0.10.1
 *
 * @history            0.10.1 - Improved stability
 *
 */

class WPDKWordPressPlugin extends WPDKPlugin {

  /**
   * The Plugin URL more `assets/`. This property is very useful for read style sheet and Javascript file in the
   * 'assets' folder.
   *
   * @brief Assets URL
   *
   * @var string $assetsURL
   */
  public $assetsURL;

  /**
   * The Filesystem plugin path more `classes/`
   *
   * @brief Classes path
   *
   * @var string $classesPath
   */
  public $classesPath;

  /**
   * The Plugin URL more `assets/css/`
   *
   * @brief Style sheet URL
   *
   * @var string $cssURL
   */
  public $cssURL;

  /**
   * The Filesystem plugin path more `database/`
   *
   * @brief Database path
   *
   * @var string $databasePath
   */
  public $databasePath;

  /**
   * The plugin folder, Eg. `wpx-smartshop/`
   *
   * @brief Plugin folder
   *
   * @var string $folderName
   */
  public $folderName;

  /**
   * The Plugin URL more `assets/css/images/`
   *
   * @brief Images URL
   *
   * @var string $imagesURL
   */
  public $imagesURL;

  /**
   * The Plugin URL more `assets/js/`
   *
   * @brief Javascript URL
   *
   * @var string $javascriptURL
   */
  public $javascriptURL;

  /**
   * Filesystem plugin path
   *
   * @brief Filesystem plugin path
   *
   * @var string $path
   */
  public $path;

  /**
   * The Plugin folder and main file, Eg. `wpx-smartshop/main.php`
   * This is used as unique code id.
   *
   * @brief Plugin folder and main file
   *
   * @var string $pluginBasename
   */
  public $pluginBasename;

  /**
   * The Protocol `http://` or `https://`
   *
   * @brief Protocol
   *
   * @var string $protocol
   * @see self::protocol() static method
   */
  public $protocol;

  /**
   * The plugin slug build with WordPress `sanitize_title()`
   *
   * @brief Plugin slug
   *
   * @var string $slug;
   */
  public $slug;

  /**
   * The Plugin URL
   *
   * @brief Plugin URL
   *
   * @var string $url
   */
  public $url;

  /**
   * The Default WordPress admin Ajax URL gateway
   *
   * @brief Ajax URL
   *
   * @var string $urlAjax
   * @see self::urlAjax() static method
   */
  public $urlAjax;

  /**
   * The array of loading path related to any WPX plugin class. This array is related to the specific WPX plugin
   * that extends this base class.
   *
   * @brief The array of loading path related to any WPX plugin class.
   *
   * @var array $_wpxPluginClassLoadingPath
   *
   * @since 0.10.0
   */
  private $_wpxPluginClassLoadingPath;

  // -------------------------------------------------------------------------------------------------------------------
  // DEPRECATED
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * The Plugin URL more `assets/css/images/`
   *
   * @brief Images URL
   *
   * @deprecated Since 0.6.3 - Use `imagesURL` instead
   *
   * @var string $url_images
   */
  public $url_images;

  /**
   * The Plugin URL more `assets/js/`
   *
   * @brief Javascript URL
   *
   * @deprecated Since 0.6.3 - Use `javascriptURL` instead
   *
   * @var string $url_javascript
   */
  public $url_javascript;


  /**
   * Create a WPDKWordPressPlugin instance
   *
   * @brief Construct
   *
   * @param string $file Usually you set it as `__FILE__`, which is the name of main file of plugin
   *
   * @return WPDKWordPressPlugin
   */
  public function __construct( $file = null ) {

    parent::__construct( $file );

    /*
     * Load SPL autoload logic for this instance
     * NOTE: any WPX plugin has its own SPL autoload logic
     *
     */

    $this->_wpxPluginsClassesLoadingPath = array();
    spl_autoload_extensions( '.php' ); // for faster execution
    spl_autoload_register( array( $this, 'autoloadEnvironment' ) );

    // Path unix
    $this->path         = trailingslashit( dirname( $file ) );
    $this->classesPath  = $this->path . 'classes/';
    $this->databasePath = $this->path . 'database/';

    // URL
    $this->url           = trailingslashit( plugin_dir_url( $file ) );
    $this->assetsURL     = $this->url . 'assets/';
    $this->cssURL        = $this->assetsURL . 'css/';
    $this->imagesURL     = $this->cssURL . 'images/';
    $this->javascriptURL = $this->assetsURL . 'js/';

    // Only folder name
    $this->folderName = trailingslashit( basename( dirname( $file ) ) );

    // WordPress slug plugin, Eg. wpx-smartshop/main.php
    $this->pluginBasename = plugin_basename( $file );

    // Built-in slug
    $this->slug = sanitize_title( $this->name );

    // Useful property
    $this->protocol = self::protocol();
    $this->urlAjax  = self::urlAjax();

    // Load text domain
    load_plugin_textDomain( $this->textDomain, false, $this->textDomainPath );

    // Preferences
    add_action( 'init', array( $this, 'preferences' ) );

    // Ajax
    if( wpdk_is_ajax() ) {
      add_action( 'init', array( $this, 'ajax' ) );
    }

    // Admin backend area
    if( is_admin() ) {
      add_action( 'init', array( $this, 'admin' ) );
      add_action( 'admin_init', array( $this, 'admin_init' ) );
    }
    // Improve since v1.4.13 - Frontnend
    elseif( !isset( $GLOBALS['pagenow'] ) || isset( $GLOBALS['pagenow'] ) && !in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {
      add_action( 'init', array( $this, 'theme' ) );
    }

    // Activation & Deactivation Hook
    register_activation_hook( $file, array( $this, 'activation' ) );
    register_deactivation_hook( $file, array( $this, 'deactivation' ) );

    /*
     * There are many pitfalls to using the uninstall hook. It ’ s a much cleaner, and easier, process to use the
     * uninstall.php method for removing plugin settings and options when a plugin is deleted in WordPress.
     *
     * Using uninstall.php file. This is typically the preferred method because it keeps all your uninstall code in a
     * separate file. To use this method, create an uninstall.php file and place it in the root directory of your
     * plugin. If this file exists WordPress executes its contents when the plugin is deleted from the WordPress
     * Plugins screen page.
     *
     */

    // register_uninstall_hook( $file, array( $this, 'uninstall' ) );

    // Widgets init
    add_action( 'widgets_init', array( $this, 'widgets' ) );
  }

  /**
   * Return the current web site protocol. The `protocol` property is set too.
   *
   * @brief Site protocol
   *
   * @return string `http` or `https`
   */
  public static function protocol()
  {
    return ( isset( $_SERVER['HTTPS'] ) && 'on' == $_SERVER['HTTPS'] ) ? 'https://' : 'http://';
  }

  /**
   * Returns the standard URL used by WordPress Ajax request.
   *
   * @brief Ajax URL
   *
   * @return string Standard URL for Ajax request
   */
  public static function urlAjax()
  {
    return admin_url( 'admin-ajax.php', self::protocol() );
  }

  /**
   * Return the current complete URL with protocol (`http` or `https`), server name, port and URI
   *
   * @brief Return the current URL
   *
   * @return string The current complete URL
   */
  public static function currentURL()
  {
    $protocol = self::protocol();
    $port     = ( '80' != $_SERVER['SERVER_PORT'] ) ? ':' . $_SERVER['SERVER_PORT'] : '';

    // Get host by HTTP_X_FORWARDED_HOST. This is available from PHP 5.1+ and in a proxy server
    if ( isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) {
      $host = $_SERVER['HTTP_X_FORWARDED_HOST'];
      if ( !empty( $host ) ) {
        $elements = explode( ',', $host );
        $host     = trim( end( $elements ) );
      }
    }
    else {
      $host = $_SERVER['HTTP_HOST'];
      if ( empty( $host ) ) {
        $host = $_SERVER['SERVER_NAME'];
        if ( empty( $host ) ) {
          $host = !empty( $_SERVER['SERVER_ADDR'] ) ? $_SERVER['SERVER_ADDR'] : '';
        }
      }
    }

    return sprintf( '%s%s%s%s', $protocol, $host, $port, $_SERVER['REQUEST_URI'] );
  }

  /**
   * This function records a WPX plugin class into autoloading register, joined with its loading path.
   * The function has some facility in its first param, in order to allow both string and array loading of class
   * names ( useful in case of a group of classes that are defined in a single file ).
   *
   *     1. $this->registerAutoloadClass( 'file.php', 'ClassName' );
   *
   *     2. $this->registerAutoloadClass( array( 'file.php' => 'ClassName' ) );
   *
   *     3. $this->registerAutoloadClass( array( 'file.php' => array( 'ClassName', 'ClassName', ... ) ) );
   *
   *
   * @brief Records a WPX plugin class into autoloading register.
   *
   * @param string|array  $sLoadingPath Path of class when $mClassName is a string
   * @param string        $mClassName   Optional. The single class name or key value pairs array with path => classes
   *
   */
  public function registerAutoloadClass( $sLoadingPath, $mClassName = '' )
  {

    // 1.
    if ( is_string( $sLoadingPath ) && is_string( $mClassName ) && !empty( $mClassName  ) ) {
      $sClassNameLowerCased                                    = strtolower( $mClassName );
      $this->_wpxPluginClassLoadingPath[$sClassNameLowerCased] = $sLoadingPath;
    }

    // 2.
    elseif ( is_array( $sLoadingPath ) ) {
      foreach ( $sLoadingPath as $path => $classes ) {
        if ( is_string( $classes ) ) {
          $class_name                                    = strtolower( $classes );
          $this->_wpxPluginClassLoadingPath[$class_name] = $path;
        }

        // 3.
        elseif ( is_array( $classes ) ) {
          foreach ( $classes as $class_name ) {
            $class_name                                    = strtolower( $class_name );
            $this->_wpxPluginClassLoadingPath[$class_name] = $path;
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
   * @brief Runtime autoloading of plugin classes.
   *
   * @since 1.0.0.b4
   *
   * @param string $sClassName - The class that has to be loaded right now
   *
   */
  public function autoloadEnvironment( $sClassName )
  {
    if( class_exists( $sClassName, false ) ) {
      return;
    }

    // For backward compatibility and for better matching
    $sClassNameLowerCased = strtolower( $sClassName );

    if ( isset( $this->_wpxPluginClassLoadingPath[$sClassNameLowerCased] ) ) {
      require_once( $this->_wpxPluginClassLoadingPath[$sClassNameLowerCased] );
    }
  }

  /**
   * Reload the text domain for multilingual. This method is useful when the loading procedure for the text domain
   * (which occurs in `plugins_loaded()`) is not complete for some reason.
   *
   * @brief Force text domain reload
   */
  public function reloadTextDomain()
  {
    load_plugin_textDomain( $this->textDomain, false, $this->textDomainPath );
  }

  // -------------------------------------------------------------------------------------------------------------------
  // HOOK DELEGATE
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Called by action hook 'admin_init'
   *
   * @brief Admin init
   */
  public function admin_init()
  {
    // To override
  }

  /**
   * Called after `load_plugin_textDomain()` in `_init`
   *
   * @brief Init Preferences
   */
  public function preferences()
  {
    // To override
  }

  /**
   * This method is to override and it is called when an Ajax request is performed.
   *
   * @brief Ajax request
   */
  public function ajax()
  {
    // To override
  }

  /**
   * See `_init()`
   *
   * @brief Admin
   *
   * @sa theme()
   */
  public function admin()
  {
    // To override
  }

  /**
   * Called when a plugin is activate; `register_activation_hook()`
   *
   * @brief Activation
   *
   * @sa deactivation()
   */
  public function activation()
  {
    // To override
  }

  /**
   * Called when a plugin is deactivate; `register_deactivation_hook()`
   *
   * @brief Deactivation
   *
   * @sa activation()
   */
  public function deactivation()
  {
    // To override
  }

  /**
   * See _init()``
   *
   * @brief Theme
   *
   */
  public function theme()
  {
    // To override
  }

  /**
   * Called by action hook `widget_init`
   *
   * @brief Widget init
   *
   * @since 0.7.5
   */
  public function widgets()
  {
    // To override
  }

}


/**
 * Wrap for generic plugin object. This class is used to manage any plugin installed (enabled or disabled).
 * This class is very different from WPDKWordPressPlugin because can describe any WordPress Plugin. Also, it is the
 * logical model of any plugin.
 *
 * @class              WPDKPlugin
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-09-24
 * @version            1.0.1
 *
 * @history            1.0.1 - Removed deprecated
 *
 */

class WPDKPlugin {

  /**
   * The Active flag
   *
   * @brief Active
   *
   * @var bool $active
   */
  public $active = false;

  /**
   * The Author get from `get_plugin_data()`, 'Author' parameter
   *
   * @brief Author
   *
   * @var string $author
   */
  public $author = '';

  /**
   * The Author name from `get_plugin_data()`, 'AuthorName' parameter
   *
   * @brief Author
   *
   * @var string $authorName
   */
  public $authorName = '';

  /**
   * The Author URI from `get_plugin_data()`, 'AuthorURI' parameter
   *
   * @brief Author URI
   *
   * @var string $authorURI
   */
  public $authorURI = '';

  /**
   * Long description of plugin
   *
   * @brief Description
   *
   * @var string $description
   */
  public $description = '';

  /**
   * Usually __FILE__
   *
   * @brief Path file
   *
   * @var string $file
   *
   * @since 1.0.0.b4
   */
  public $file = '';

  /**
   * The standard WPDK 64x64 icon path
   *
   * @brief Icon
   *
   * @var string $icon
   */
  public $icon = '';

  /**
   * This is the ID of plugin. This property is 'folder/main file.php'
   *
   * @brief ID
   *
   * @var string $id
   */
  public $id = '';

  /**
   * Name of plugin
   *
   * @brief Plugin name
   *
   * @var string $name
   */
  public $name = '';

  /**
   * The Network activation get from `get_plugin_data()`, 'Network' parameter
   *
   * @brief Network
   *
   * @var string $network
   */
  public $network = '';

  /**
   * Address of plugin repository
   *
   * @brief Plugin URI
   *
   * @var string $pluginURI
   */
  public $pluginURI = '';

  /**
   * The plugin text domain get from `get_plugin_data()`, 'Text Domain' parameter
   *
   * @brief Plugin Text Domain
   *
   * @var string $textDomain
   */
  public $textDomain = '';

  /**
   * The complete Text domain Plugin url get from `get_plugin_data()`, 'Domain Path' parameter
   *
   * @brief Text domain Plugin url
   *
   * @var string $textDomainPath
   */
  public $textDomainPath = '';

  /**
   * The plugin title. This is the same as name
   *
   * @brief Title
   *
   * @var string $title
   */
  public $title = '';

  /**
   * Plugin version
   *
   * @brief Version
   *
   * @var string $version
   */
  public $version = '';

  /**
   * Create an instance of WPDKPlugin class
   *
   * @brief Construct
   *
   * @param null $file Optional. Usually constant __FILE__
   *
   * @return WPDKPlugin
   */
  public function __construct( $file = null ) {

    $this->file = $file;

    if ( !is_null( $this->file ) ) {

      // Use WordPress get_plugin_data() function for auto retrive plugin information.
      if ( !function_exists( 'get_plugin_data' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
      }
      $result = get_plugin_data( $this->file, false );

      $this->id             = plugin_basename( $this->file );
      $this->author         = $result['Author'];
      $this->authorURI      = $result['AuthorURI'];
      $this->authorName     = $result['AuthorName'];
      $this->description    = $result['Description'];
      $this->icon           = sprintf( '%s%s%s', WPDKWordPressPaths::pluginsURL(), trailingslashit( basename( dirname( $this->file ) ) ), 'assets/css/images/logo-64x64.png' );
      $this->name           = $result['Name'];
      $this->network        = $result['Network'];
      $this->pluginURI      = $result['PluginURI'];
      $this->textDomain     = $result['TextDomain'];
      $this->textDomainPath = trailingslashit( basename( dirname( $this->file ) ) ) . $result['DomainPath'];
      $this->title          = $result['Title'];
      $this->version        = $result['Version'];
      $this->active         = is_plugin_active( $this->id );

    }
  }
}

/**
 * Manage list and group of WPDKPlugin objects.
 *
 * @class              WPDKPlugins
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 * @deprecated         since 1.6.0 - Not more used
 *
 */
class WPDKPlugins {

  /**
   * List of the installed plugins. No wpXtreme plugin. This is a key pairs value array with value as WPDKPlugin
   * object
   *
   * @brief All plugins list
   *
   * @var array $plugins
   */
  public $plugins;

  /**
   * List of the wpXtreme plugins. This is a key pairs value array with value as WPDKPlugin object.
   *
   * @brief wpXtreme Plugins list
   *
   * @var array $wpxPlugins
   */
  public $wpxPlugins;

  /**
   * Index array with the list of active plugins slug.
   *
   * @brief List of active plugins
   *
   * @var array $_activePlugins
   */
  private $_activePlugins;

  /**
   * Create an instance of WPDKPlugins class
   *
   * @brief Construct
   *
   * @return WPDKPlugins
   */
  private function __construct()
  {
    $this->_init();
    $this->_initPluginsLists();
  }

  /**
   * Initialize the properties to default values
   *
   * @brief Init the properties
   */
  private function _init()
  {
    $this->_activePlugins = array();
    $this->plugins        = array();
    $this->wpxPlugins     = array();
  }

  /**
   * Init and build the plugins list
   *
   * @brief Build the plugins list
   */
  private function _initPluginsLists()
  {

    if ( empty( $this->plugins ) ) {

      // Get all installed plugins
      $all_plugins = get_plugins();

      // Get all active plugins
      $this->_activePlugins = get_option( 'active_plugins' );

      foreach ( $all_plugins as $key => $value ) {
        $file   = sprintf( '%s%s', trailingslashit( WP_PLUGIN_DIR ), $key );
        $plugin = new WPDKPlugin( $file );

        // Put this WPDKPlugin in wpXtreme list
        if ( 'https://wpxtre.me' == $value['PluginURI'] ) {
          $this->wpxPlugins[$key] = $plugin;
        }

        // Put this WPDKPlugin in generic list
        else {
          $this->plugins[$key] = $plugin;
        }
      }
    }
  }

  /**
   * Return a singleton instance of WPDKPlugins class
   *
   * @return WPDKPlugins
   */
  static function getInstance()
  {
    static $instance = null;
    if ( is_null( $instance ) ) {
      $instance = new WPDKPlugins();
    }
    return $instance;
  }

}

/**
 * This class allow to access to the WordPress and WPDK standard path and URI.
 * All path and URL are auto termianted with "/" slash.
 *
 * @class              WPDKWordPressPaths
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 * @note Please visit http://codex.wordpress.org/Determining_Plugin_and_Content_Directories before add any methods
 *
 */
class WPDKWordPressPaths {

  /**
   * Retrieve the home url for a given site.
   *
   * Returns the 'home' option with the appropriate protocol, 'https' if
   * is_ssl() and 'http' otherwise. If $scheme is 'http' or 'https', is_ssl() is
   * overridden.
   *
   * @brief Home URL
   *
   * @param  int    $blog_id   (optional) Blog ID. Defaults to current blog.
   * @param  string $path      (optional) Path relative to the home url.
   * @param  string $scheme    (optional) Scheme to give the home url context. Currently 'http', 'https', or 'relative'.
   *
   * @return string Home url link with optional path appended.
   */
  public static function homeURL( $blog_id = null, $path = '', $scheme = null )
  {
    return trailingslashit( get_home_url( $blog_id, $path, $scheme ) );
  }

  /**
   * Retrieve the url to the admin area for a given site.
   *
   * @brief Admin URL
   *
   * @param int    $blog_id (optional) Blog ID. Defaults to current blog.
   * @param string $path    Optional path relative to the admin url.
   * @param string $scheme  The scheme to use. Default is 'admin', which obeys force_ssl_admin() and is_ssl(). 'http' or 'https' can be passed to force those schemes.
   *
   * @return string Admin url link with optional path appended.
   */
  public static function adminURL( $blog_id = null, $path = '', $scheme = 'admin' )
  {
    return trailingslashit( get_admin_url( $blog_id, $path, $scheme ) );
  }

  /**
   * Retrieve the url to the includes directory.
   *
   * @brief Includes URL
   *
   * @param string $path Optional. Path relative to the includes url.
   *
   * @return string Includes url link with optional path appended.
   */
  public static function includesURL( $path = '' )
  {
    return trailingslashit( includes_url( $path ) );
  }

  /**
   * Retrieve the url to the plugins directory or to a specific file within that directory.
   * You can hardcode the plugin slug in $path or pass __FILE__ as a second argument to get the correct folder name.
   *
   * @brief Plugins URL
   *
   * @param string $path   Optional. Path relative to the plugins url.
   * @param string $plugin Optional. The plugin file that you want to be relative to - i.e. pass in __FILE__
   *
   * @return string Plugins url link with optional path appended.
   */
  public static function pluginsURL( $path = '', $plugin = '' )
  {
    return trailingslashit( plugins_url( $path, $plugin ) );
  }

}