<?php
/// @cond private

/* Avoid directly access */
if ( !defined( 'ABSPATH' ) ) {
  exit;
}

if ( !class_exists( 'WPDK' ) ) {

  /* Include config. */
  require_once( trailingslashit( dirname( __FILE__ ) ) . 'config.php' );

  /**
   * Static/singleton class for load WPDK Framework.
   * This class is in singleton mode to avoid double init of action, filters and includes.
   *
   * @class              WPDK
   * @author             =undo= <info@wpxtre.me>
   * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
   * @date               2013-11-08
   * @version            0.10.5
   */
  final class WPDK {

    /**
     * The array of loading path related to any WPDK class.
     *
     * @brief The array of loading path related to any WPDK class.
     *
     * @var array $_wpdkClassLoadingPath
     *
     * @since 0.10.0
     */
    private $_wpdkClassLoadingPath;

    /**
     * Init the framework in singleton mode to avoid double include, action and inits.
     *
     * @brief This init method
     *
     * @return WPDK
     */
    public static function init()
    {
      static $instance = null;
      if ( is_null( $instance ) ) {
        $instance = new WPDK();
      }
      return $instance;
    }

    /**
     * Create an instance of WPDK class and init the franework
     *
     * @brief Construct
     *
     * @return WPDK
     */
    private function __construct()
    {

      // First of all, load SPL autoload logic
      $this->_wpdkClassLoadingPath = array();
      spl_autoload_extensions( '.php' ); // for faster execution
      spl_autoload_register( array( $this, 'autoloadWPDKEnvironment' ) );

      // Load the framework in SPL autoload logic
      $this->defines();
      $this->registerClasses();

      /* Load the translation of WPDK */
      add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

      /* Users enhancer. */
      add_action( 'set_current_user', array( 'WPDKUsers', 'init' ) );

      /* Shortcode. */
      add_action( 'wp_loaded', array( 'WPDKServiceShortcode', 'init' ) );

      /* Ajax. */
      if ( wpdk_is_ajax() ) {
        add_action( 'wp_loaded', array( 'WPDKServiceAjax', 'init' ) );
      }

      /* Loading Script & style for backend */
      add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts_styles' ), 1 );

      /* Loading script & style for frontend */
      add_action( 'wp_head', array( $this, 'enqueue_scripts_styles' ) );

      /* Add some special WPDK class to body */
      add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );

      /* Callback hook */
      do_action( 'WPDK' );
    }

    /**
     * This function records a WPDK class into autoloading register, joined with its loading path. The function has some
     * facility in its first param, in order to allow both string and array loading of class names ( useful in case of a
     * group of classes that are defined in a single file ):
     *
     * 1. $this->registerAutoloadClass( 'file.php', 'ClassName' );
     *
     * 2. $this->registerAutoloadClass( array( 'file.php' => 'ClassName' ) );
     *
     * 3. $this->registerAutoloadClass( array( 'file.php' => array( 'ClassName', 'ClassName', ... ) ) );
     *
     * @brief Records a WPDK class into autoloading register.
     *
     * @param string|array $sLoadingPath Path of class when $mClassName is a string
     * @param string       $mClassName   Optional. The single class name or key value pairs array with path => classes
     *
     * @since 0.10.0
     *
     */
    public function registerAutoloadClass( $sLoadingPath, $mClassName = '' )
    {

      /* 1. */
      if ( is_string( $sLoadingPath ) && is_string( $mClassName ) && !empty( $mClassName ) ) {
        $sClassNameLowerCased                               = strtolower( $mClassName );
        $this->_wpdkClassLoadingPath[$sClassNameLowerCased] = $sLoadingPath;
      }

      /* 2. */
      elseif ( is_array( $sLoadingPath ) ) {
        foreach ( $sLoadingPath as $path => $classes ) {
          if ( is_string( $classes ) ) {
            $class_name                               = strtolower( $classes );
            $this->_wpdkClassLoadingPath[$class_name] = $path;
          }

          /* 3. */
          elseif ( is_array( $classes ) ) {
            foreach ( $classes as $class_name ) {
              $class_name                               = strtolower( $class_name );
              $this->_wpdkClassLoadingPath[$class_name] = $path;
            }
          }
        }
      }
    }


    /**
     * This function performs runtime autoloading of all WPDK classes, based on previous class registering executed
     * in includes method.
     *
     * @brief Runtime autoloading of WPDK classes.
     *
     * @since 0.10.0
     *
     * @param string $sClassName - The class that has to be loaded right now
     *
     */
    public function autoloadWPDKEnvironment( $sClassName )
    {
      // For backward compatibility and for better matching
      $sClassNameLowerCased = strtolower( $sClassName );
      if ( isset( $this->_wpdkClassLoadingPath[$sClassNameLowerCased] ) ) {
        require_once( $this->_wpdkClassLoadingPath[$sClassNameLowerCased] );
      }
    }

    /**
     * Include external defines
     *
     * @brief Dynamic define
     */
    private function defines()
    {
      /* define WPDK constants. */
      require_once( trailingslashit( dirname( __FILE__ ) ) . 'defines.php' );
    }

    /**
     * Register all autoload classes and include all framework class files through SPL autoload logic
     *
     * @brief Autoload classes
     */
    private function registerClasses()
    {

      $sPathPrefix = trailingslashit( dirname( __FILE__ ) );

      /* Put here files that have to be directly included without autoloading */
      require_once( $sPathPrefix . 'classes/core/wpdk-functions.php' );

      /* Start autoloading register */

      $includes = array(

        // -------------------------------------------------------------------------------------------------------------
        // USER INTERFACE
        // -------------------------------------------------------------------------------------------------------------

        $sPathPrefix . 'classes/ui/wpdk-view.php'                       => 'WPDKView',
        $sPathPrefix . 'classes/ui/wpdk-viewcontroller.php'             => array(
          'WPDKViewController',
          'WPDKHeaderView',
        ),
        $sPathPrefix . 'classes/ui/wpdk-preferences-viewcontroller.php' => 'WPDKPreferencesViewController',
        $sPathPrefix . 'classes/ui/wpdk-preferences-view.php'           => 'WPDKPreferencesView',

        $sPathPrefix . 'classes/ui/wpdk-metabox.php'                    => array(
          'WPDKMetaBoxView',
          'WPDKMetaBoxContext',
          'WPDKMetaBoxPriority',
        ),

        $sPathPrefix . 'classes/ui/wpdk-jquery.php'                     => array(
          'WPDKjQuery',
          'WPDKjQueryTab',
          'WPDKjQueryTabsView',
          'WPDKjQueryTabsViewController',
        ),

        $sPathPrefix . 'classes/ui/wpdk-ui.php'                         => array(
          'WPDKUIControlType',
          'WPDKUIControlAlert',
          'WPDKUIControlButton',
          'WPDKUIControlCheckbox',
          'WPDKUIControlChoose',
          'WPDKUIControlCustom',
          'WPDKUIControlDate',
          'WPDKUIControlDateTime',
          'WPDKUIControlEmail',
          'WPDKUIControlFile',
          'WPDKUIControlHidden',
          'WPDKUIControlLabel',
          'WPDKUIControlNumber',
          'WPDKUIControlPassword',
          'WPDKUIControlPhone',
          'WPDKUIControlRadio',
          'WPDKUIControlSelect',
          'WPDKUIControlSelectList',
          'WPDKUIControlSubmit',
          'WPDKUIControlSwipe',
          'WPDKUIControlText',
          'WPDKUIControlTextarea',
          'WPDKUIControl',
          'WPDKUIControlsLayout',
          'WPDKUI'
        ),

        $sPathPrefix . 'classes/ui/wpdk-glyphicons.php'                 => 'WPDKGlyphIcons',

        $sPathPrefix . 'classes/ui/wpdk-twitter-bootstrap.php'          => array(
          'WPDKTwitterBootstrap',
          'WPDKTwitterBootstrapModal',
          'WPDKTwitterBootstrapButtonType',
          'WPDKTwitterBootstrapButtonSize',
          'WPDKTwitterBootstrapButton',
          'WPDKTwitterBoostrapPopover'
        ),

        $sPathPrefix . 'classes/ui/wpdk-tbs-alert.php'                => array(
          'WPDKTwitterBootstrapAlert',
          'WPDKTwitterBootstrapAlertType',
        ),

        $sPathPrefix . 'classes/ui/wpdk-html.php'                     => 'WPDKHTML',
        $sPathPrefix . 'classes/ui/wpdk-html-tag.php'                 => array(
          'WPDKHTMLTag',
          'WPDKHTMLTagName',
          'WPDKHTMLTagInputType',
          'WPDKHTMLTagA',
          'WPDKHTMLTagButton',
          'WPDKHTMLTagFieldset',
          'WPDKHTMLTagForm',
          'WPDKHTMLTagInput',
          'WPDKHTMLTagLabel',
          'WPDKHTMLTagLegend',
          'WPDKHTMLTagSelect',
          'WPDKHTMLTagSpan',
          'WPDKHTMLTagTextarea',
        ),

        $sPathPrefix . 'classes/ui/wpdk-dynamic-table.php'            => 'WPDKDynamicTable',
        $sPathPrefix . 'classes/ui/wpdk-menu.php'                     => array(
          'WPDKMenu',
          'WPDKSubMenu',
          'WPDKSubMenuDivider',
        ),
        $sPathPrefix . 'classes/ui/wpdk-listtable-viewcontroller.php' => 'WPDKListTableViewController',
        $sPathPrefix . 'classes/ui/wpdk-pointer.php'                  => 'WPDKPointer',
        $sPathPrefix . 'classes/ui/wpdk-tinymce-plugin.php'           => array(
          'WPDKEditorButton',
          'WPDKTinyMCEPlugin'
        ),

        // -------------------------------------------------------------------------------------------------------------
        // CORE
        // -------------------------------------------------------------------------------------------------------------

        $sPathPrefix . 'classes/core/wpdk-mail.php'                        => array(
          'WPDKMail',
          'WPDKMailPlaceholder',
        ),

        $sPathPrefix . 'classes/core/wpdk-result.php'                      => array(
          'WPDKResultType',
          'WPDKResult',
          'WPDKError',
          'WPDKWarning',
          'WPDKStatus'
        ),

        $sPathPrefix . 'classes/core/wpdk-preferences.php'                 => array(
          'WPDKPreferences',
          'WPDKPreferencesBranch',
          'WPDKPreferencesImportExport',
        ),

        $sPathPrefix . 'classes/core/wpdk-wordpress-plugin.php'            => array(
          'WPDKWordPressPlugin',
          'WPDKPlugin',
          'WPDKPlugins',
          'WPDKWordPressPaths'
        ),

        $sPathPrefix . 'classes/core/wpdk-shortcode.php'                   => 'WPDKShortcode',
        $sPathPrefix . 'classes/core/wpdk-wordpress-theme.php'             => array(
          'WPDKWordPressTheme',
          'WPDKTheme',
          'WPDKThemeSetup',
        ),
        $sPathPrefix . 'classes/core/wpdk-theme-customize.php'             => array(
          'WPDKThemeCustomizeControlType',
          'WPDKThemeCustomize',
        ),
        $sPathPrefix . 'classes/core/wpdk-wordpress-admin.php'             => 'WPDKWordPressAdmin',
        $sPathPrefix . 'classes/core/wpdk-watchdog.php'                    => 'WPDKWatchDog',
        $sPathPrefix . 'classes/core/wpdk-ajax.php'                        => array(
          'WPDKAjax',
          'WPDKAjaxResponse'
        ),
        $sPathPrefix . 'classes/core/wpdk-object.php'                      => 'WPDKObject',

        // -------------------------------------------------------------------------------------------------------------
        // DATABASE
        // -------------------------------------------------------------------------------------------------------------

        $sPathPrefix . './classes/database/wpdk-db.php'                    => array(
          'WPDKDBTableStatus',
          '__WPDKDBTable',
          'WPDKDBTableRow'
        ),

        // -------------------------------------------------------------------------------------------------------------
        // WordPress & common Helper
        // -------------------------------------------------------------------------------------------------------------

        $sPathPrefix . 'classes/helper/wpdk-array.php'                     => 'WPDKArray',
        $sPathPrefix . 'classes/helper/wpdk-colors.php'                    => 'WPDKColors',
        $sPathPrefix . 'classes/helper/wpdk-datetime.php'                  => 'WPDKDateTime',
        $sPathPrefix . 'classes/helper/wpdk-math.php'                      => 'WPDKMath',
        $sPathPrefix . 'classes/helper/wpdk-screen-help.php'               => 'WPDKScreenHelp',
        $sPathPrefix . 'classes/helper/wpdk-crypt.php'                     => 'WPDKCrypt',
        $sPathPrefix . 'classes/helper/wpdk-filesystem.php'                => 'WPDKFilesystem',
        $sPathPrefix . 'classes/helper/wpdk-http.php'                      => array(
          'WPDKHTTPRequest',
          'WPDKHTTPVerbs'
        ),

        // -------------------------------------------------------------------------------------------------------------
        // Post
        // -------------------------------------------------------------------------------------------------------------

        $sPathPrefix . 'classes/post/wpdk-post.php'                        => array(
          '_WPDKPost',
          'WPDKPostStatus',
          'WPDKPostType',
          'WPDKPostMeta'
        ),

        $sPathPrefix . 'classes/post/wpdk-custom-post-type.php'            => array(
          'WPDKCustomPostType',
        ),

        // -------------------------------------------------------------------------------------------------------------
        // Taxonomies and Terms
        // -------------------------------------------------------------------------------------------------------------

        $sPathPrefix . 'classes/taxonomies/wpdk-terms.php'                 => array(
          'WPDKTerm',
          'WPDKTerms',
        ),

        $sPathPrefix . 'classes/taxonomies/wpdk-custom-taxonomy.php'       => array(
          'WPDKCustomTaxonomy',
        ),

        // -------------------------------------------------------------------------------------------------------------
        // Users, Roles and Capabilities
        // -------------------------------------------------------------------------------------------------------------

        $sPathPrefix . 'classes/users/wpdk-user.php'                       => array(
          'WPDKUser',
          'WPDKUsers',
          'WPDKRole',
          'WPDKRoles',
          'WPDKCapabilities',
        ),

        // -------------------------------------------------------------------------------------------------------------
        // Users, Roles and Capabilities
        // -------------------------------------------------------------------------------------------------------------

        $sPathPrefix . 'classes/widget/wpdk-widget.php'                     => array(
          'WPDKWidget',
        ),

        // -------------------------------------------------------------------------------------------------------------
        // Services
        // -------------------------------------------------------------------------------------------------------------

        $sPathPrefix . 'services/wpdk-service-ajax.php'                    => 'WPDKServiceAjax',
        $sPathPrefix . 'services/wpdk-service-shortcode.php'               => 'WPDKServiceShortcode',


        // -------------------------------------------------------------------------------------------------------------
        // Deprecated
        // -------------------------------------------------------------------------------------------------------------

        $sPathPrefix . 'classes/deprecated/wpdk-db-table.php'              => array(
          'WPDKDBTable',
          '_WPDKDBTable',
        ),

        $sPathPrefix . 'classes/deprecated/wpdk-configuration.php'               => array(
          'WPDKConfig',
          'WPDKConfigBranch',
          'WPDKConfiguration',
          'WPDKConfigurationView',
        ),

        /* Extra libs */

      );

      $this->registerAutoloadClass( $includes );

    }

    /**
     * Load a text domain for WPDK, like a plugin. In this relase WPDK has an own text domain. This feature could
     * miss in future release
     *
     * @brief Load WPDK text domain
     */
    public function load_plugin_textdomain()
    {
      load_plugin_textdomain( WPDK_TEXTDOMAIN, false, WPDK_TEXTDOMAIN_PATH );
    }

    /**
     * WPDK scripts and styles. These client file are always loaded.
     *
     * @brief WPDK Scripts and styles
     */
    public function enqueue_scripts_styles()
    {
      /* Dato che attualmente non vi è distinizione, riuso stili e script del backend */

      /* WPDK CSS styles. */
      $this->admin_styles();

      /* WPDK Javascript framework engine. */
      $this->admin_scripts();
    }

    /**
     * Append some special WPDK class to body
     *
     * @brief Class body
     *
     * @param string $classes Class body
     *
     * @return string
     */
    public function admin_body_class( $classes )
    {
      return $classes . ' wpdk-jquery-ui';
    }

    /**
     * Load all backend admin Styles. These client file are always loaded.
     *
     * @brief Admin styles
     */
    private function admin_styles()
    {
      $deps = array(
        'thickbox'
      );

      wp_enqueue_style( 'wpdk-jquery-ui', WPDK_URI_CSS . 'jquery-ui/jquery-ui.custom.css', $deps, WPDK_VERSION );
      wp_enqueue_style( 'wpdk-style', WPDK_URI_CSS . 'wpdk.css', $deps, WPDK_VERSION );
    }

    /**
     * Load all admin backend script.
     *
     * @brief Admin scripts
     */
    private function admin_scripts()
    {
      /* Registro tutte le chiavi/percorso degli script che andrò ad utilizzare */
      $deps = array(
        'jquery',
        'jquery-ui-core',
        'jquery-ui-tabs',
        'jquery-ui-dialog',
        'jquery-ui-datepicker',
        'jquery-ui-autocomplete',
        'jquery-ui-slider',
        'jquery-ui-sortable',
        'jquery-ui-draggable',
        'jquery-ui-droppable',
        'jquery-ui-resizable',
        'thickbox'
      );

      // Own
      wp_enqueue_script( 'wpdk-jquery-ui-timepicker', WPDK_URI_JAVASCRIPT . 'timepicker/jquery.timepicker.js', $deps, WPDK_VERSION, true );
      wp_enqueue_script( 'wpdk-jquery-validation', WPDK_URI_JAVASCRIPT . 'validate/jquery.validate.js', array( 'jquery' ), WPDK_VERSION, true );
      wp_enqueue_script( 'wpdk-jquery-validation-additional-method', WPDK_URI_JAVASCRIPT . 'validate/additional-methods.js', array( 'jquery-validation' ), WPDK_VERSION, true );

      /* Main wpdk. */
      wp_enqueue_script( 'wpdk-script', WPDK_URI_JAVASCRIPT . 'wpdk.js', $deps, WPDK_VERSION, true );

      /* Localize wpdk_i18n*/
      wp_localize_script( 'wpdk-script', 'wpdk_i18n', $this->scriptLocalization() );
    }

    /**
     * Return a Key values pairs array to localize Javascript
     *
     * @brief Localize string
     *
     * @return array
     */
    private function scriptLocalization()
    {
      $result = array(
        'ajaxURL'            => WPDKWordPressPlugin::urlAjax(),

        'messageUnLockField' => __( "Please confirm before unlock this form field.\nDo you want unlock this form field?", WPDK_TEXTDOMAIN ),

        'timeOnlyTitle'      => __( 'Choose Time', WPDK_TEXTDOMAIN ),
        'timeText'           => __( 'Time', WPDK_TEXTDOMAIN ),
        'hourText'           => __( 'Hour', WPDK_TEXTDOMAIN ),
        'minuteText'         => __( 'Minute', WPDK_TEXTDOMAIN ),
        'secondText'         => __( 'Seconds', WPDK_TEXTDOMAIN ),
        'currentText'        => __( 'Now', WPDK_TEXTDOMAIN ),
        'dayNamesMin'        => __( 'Su,Mo,Tu,We,Th,Fr,Sa', WPDK_TEXTDOMAIN ),
        'monthNames'         => __( 'January,February,March,April,May,June,July,August,September,October,November,December', WPDK_TEXTDOMAIN ),
        'monthNamesShort'    => __( 'Jan,Feb,Mar,Apr,May,Jun,Jul,Aug,Sep,Oct,Nov,Dec', WPDK_TEXTDOMAIN ),
        'closeText'          => __( 'Close', WPDK_TEXTDOMAIN ),
        'dateFormat'         => __( 'mm/dd/yy', WPDK_TEXTDOMAIN ),
        'timeFormat'         => __( 'HH:mm', WPDK_TEXTDOMAIN ),
      );

      return $result;
    }
  }

  /* Let's dance */
  $GLOBALS['WPDK'] = WPDK::init();
}

/// @endcond