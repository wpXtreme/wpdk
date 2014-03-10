<?php
/// @cond private

/**
 * This class make easy plugin options management. In WPDK the plugin options are called **configuration**. We likes use
 * configuration term more that option. So is made the WPDKConfig class.
 *
 * ## Overview
 *
 * You rarely (never) instantiate WPDKConfig object directly. Instead, you instantiate subclasses of the
 * WPDKConfig class.
 *
 * ### Getting started
 *
 * Write a your own custom class and extends WPDKConfig. For example:
 *
 *     class MySettings extends WPDKConfig {
 *     }
 *
 *
 * You have to implement two methods. The first method is static:
 *
 *     class MySettings extends WPDKConfig {
 *
 *         const CONFIGURATION_NAME = 'mysetting-config';
 *
 *         static function config() {
 *            $config = parent::config( self::CONFIGURATION_NAME, __CLASS__ );
 *            return $config;
 *         }
 *     }
 *
 * If you have sub branch, implements defaults() method to create these classes:
 *
 *     class MySettings extends WPDKConfig {
 *
 *         public $my_setting_branch;
 *
 *         static function config() {
 *            $config = parent::config( self::CONFIGURATION_NAME, __CLASS__ );
 *            return $config;
 *         }
 *
 *         function defaults() {
 *             $this->my_setting_branch = new MySettingsBranch();
 *         }
 *     }
 *
 *     class MySettingsBranch extends WPDKConfigBranch {
 *         public $number_of_seat;
 *
 *         function __construct() {
 *             $this->number_of_seat = 10; // Deafaul value
 *         }
 *     }
 *
 * ### Developing
 *
 * When you are in develop your settings change and the store object on db could be different from last develop version.
 * So, just add a simple static method `doDelta()` for merge and combine the last onfly version with stored version.
 * This method usually is called on activation of plugin. In this way you can align the configuration setting just
 * deactive and re active your plugin.
 *
 *     class MySettings extends WPDKConfig {
 *
 *         public $my_setting_branch;
 *
 *         static function config() {
 *            $config = parent::config( self::CONFIGURATION_NAME, __CLASS__ );
 *            return $config;
 *         }
 *
 *         function defaults() {
 *             $this->my_setting_branch = new MySettingsBranch();
 *         }
 *
 *         static function doDelta() {
 *             parent::delta( self::CONFIGURATION_NAME, __CLASS__ );
 *         }
 *     }
 *
 *     class MySettingsBranch extends WPDKConfigBranch {
 *         public $number_of_seat;
 *
 *         function __construct() {
 *             $this->number_of_seat = 10; // Deafaul value
 *         }
 *     }
 *
 *
 * ### Reset to default
 *
 * To reset to default all config use
 *
 *     MySettings::config()->resetToDefault();
 *
 *
 * ### Reset to default a branch
 *
 * To reset to default a single branch use
 *
 *     MySettings::config()->my_setting_branch->defaults();
 *     MySettings::config()->update();
 *
 *
 * See WPDKConfigBranch class for detail
 *
 * @class              WPDKConfig
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               03/09/12
 * @version            0.7.5
 *
 * @deprecated         Since 0.6.2 - Use WPDKConfiguration instead
 *
 */
class WPDKConfig {

  /**
   * This singleton instance
   *
   * @brief Singleton instance
   *
   * @var WPDKConfig $instance
   */
  private static $instance = null;

  /**
   * This is the true config tree load from database
   *
   * @brief The WPDKConfig subclass instance
   *
   * @var WPDKConfig $root
   */
  private $root;

  /**
   * Unique name for this config
   *
   * @brief Config name
   *
   * @var string $config_name
   */
  public $config_name;

  /**
   * External Class name
   *
   * @brief External Class name
   *
   * @var string $class_name
   */
  public $class_name;

  /**
   * Return an singleton instance of WPXtremeConfig subclass as specified in param inputs. This method create a
   * single instance of parent/subclasses named $class_name.
   *
   * @brief Get configuration in singleton pattern
   *
   * @param string $config_name Unique string id
   * @param string $class_name  Class name
   *
   * @return WPDKConfig
   */
  static function config( $config_name, $class_name ) {
    if ( is_null( self::$instance ) || !is_a( self::$instance->root, $class_name ) ) {
      self::$instance       = new WPDKConfig( $config_name );
      self::$instance->root = get_option( $config_name );
      if ( empty( self::$instance->root ) ) {
        self::$instance->root = new $class_name( $config_name );
        self::$instance->root->defaults();
        self::$instance->update();
      }
    }
    return self::$instance->root;
  }

  /**
   * Create a WPDKConfig instance
   *
   * @brief Construct
   *
   * @param string $config_name Unique name (slug) for this config.
   *
   * @return WPDKConfig
   */
  private function __construct( $config_name ) {
    $this->config_name = $config_name;
    $this->class_name  = __CLASS__;
  }

  /**
   * Do a delta compare/combine from two tree object config
   *
   * @brief Delta (align) object
   *
   * @param string $config_name Unique string id
   * @param string $class_name  Class name
   *
   */
  public static function delta( $config_name, $class_name ) {

    /* Check if exists a store version. */
    $store_version = get_option( $config_name );

    if ( !empty( $store_version ) ) {
      /* Prepare parent instance. */
      self::$instance = new WPDKConfig( $config_name );

      /* Get the onfly default config tree. */
      $last_version = new $class_name( $config_name );
      $last_version->defaults();

      /* Do delta. */
      self::$instance->root = wpdk_delta_object( $last_version, $store_version );
      self::update();
    }
  }

  /**
   * Update on database this configuration from root pointer.
   *
   * @brief Update from this root
   */
  function update() {
    update_option( self::$instance->config_name, self::$instance->root );
  }

  /**
   * Reset the configuration to head and update
   *
   * @brief Reset all config to default
   */
  function resetToDeafult() {
    self::$instance->root = new self::$instance->root->class_name( self::$instance->root->config_name );
    self::$instance->root->defaults();
    self::$instance->update();
  }

  /**
   * This is an override method
   *
   * @brief Alias reset to default
   */
  function defaults() { }

}

/**
 * Useful class for define sub branch
 *
 * @class              WPDKConfigBranch
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               03/09/12
 * @version            0.5
 * @deprecated         Since 0.6.2 - Removed use a simple object (stdClass) or array
 *
 */
class WPDKConfigBranch {

  /**
   * Return an instance of WPDKConfigBranch class
   *
   * @brief Construct
   *
   * @note This class is ever subclassed
   *
   * @return WPDKConfigBranch
   *
   */
  function __construct() {
    /* to override */
  }

  /**
   * Reset this branch configuration to default values
   *
   * @brief Call construct for set to defaults
   *
   */
  function defaults() {
    $this->__construct();
  }
}

/**
 * This class make easy plugin options management. In WPDK the plugin options are called **configuration**. We likes use
 * configuration term more that option. So is made the WPDKConfiguration class.
 *
 * ## Overview
 *
 * You rarely (never) instantiate WPDKConfiguration object directly. Instead, you instantiate subclasses of the
 * WPDKConfiguration class.
 *
 * ### Getting started
 *
 * Write a your own custom class and extends WPDKConfiguration. For example:
 *
 *     class MySettings extends WPDKConfiguration {
 *     }
 *
 *
 * Implements your custom properties and your branch to other configuration
 *
 *     class MySettings extends WPDKConfiguration {
 *
 *         const CONFIGURATION_NAME = 'mysetting-config';
 *
 *         public $version = '1.0';
 *
 *         function __construct() {
 *            parent::__construct( self::CONFIGURATION_NAME );
 *         }
 *     }
 *
 * You can implement this utility static method to get the configuration from database or create it onfly if missing or
 * the first time.
 *
 *     class MySettings extends WPDKConfiguration {
 *
 *         const CONFIGURATION_NAME = 'mysetting-config';
 *
 *         public $version = '1.0';
 *
 *         static function init() {
 *             return parent::init( self::CONFIGURATION_NAME, __CLASS__ );
 *         }
 *
 *         function __construct() {
 *            parent::__construct( self::CONFIGURATION_NAME );
 *         }
 *     }
 *
 * If you have a sub configuration branch, or subset of configuration, use:
 *
 *     class MySettings extends WPDKConfiguration {
 *
 *         const CONFIGURATION_NAME = 'mysetting-config';
 *
 *         public $version = '1.0';
 *
 *         // My configuration branch
 *         public $branch;
 *
 *         static function init() {
 *             return parent::init( self::CONFIGURATION_NAME, __CLASS__ );
 *         }
 *
 *         function __construct() {
 *            parent::__construct( self::CONFIGURATION_NAME );
 *
 *            $this->branch = new MyConfigurationBranch();
 *         }
 *     }
 *
 *     class MyConfigurationBranch {
 *         public $number_of_seat;
 *
 *         function __construct() {
 *             $this->number_of_seat = 10; // Default value
 *         }
 *     }
 *
 *
 * ### Reset to default values
 *
 * The code above shows how it possible reset all or a portion of your configuration. To reset a branch to default
 * values just:
 *
 *     $myconfiguration->branch = new MyConfigurationBranch();
 *
 * If you like it possible implement a simple `resetToDefault()` method as:
 *
 *     class MyConfigurationBranch {
 *         public $number_of_seat;
 *
 *         function __construct() {
 *             $this->resetToDefault();
 *         }
 *
 *         function resetToDefault() {
 *              $this->number_of_seat = 10; // Default value
 *         }
 *     }
 *
 * ### Developing
 *
 * When you are in develop your settings change and the store object on db could be different from last develop version.
 * No problem, you can invoke the `delta()` method to perform a delta from the database version and the onfly (last)
 * version.
 * This method usually is called on activation of plugin. In this way you can align the configuration setting just
 * deactive and re active your plugin.
 *
 *
 * @class              WPDKConfiguration
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-07-16
 * @version            0.8.2
 * @since              0.6.2
 * @deprecated         since 1.2.0 use WPDKPreferences instead
 *
 */
class WPDKConfiguration {

  /**
   * Name used in WordPress option save
   *
   * @brief Configuration name
   *
   * @var string $name
   */
  private $name;

  /**
   * Return the configuration object from the option. If not exists then an object is create runtime for you.
   * This is a utility method but you have to override if you don't like insert name and class name parameters. In
   * you own class just use:
   *
   *     static function init() {
   *         return parent::init( self::CONFIGURATION_NAME, __CLASS__ );
   *     }
   *
   * @param string $name       A string used as name for options. Make it unique more possible.
   * @param string $class_name The subclass class name,
   *
   * @return WPDKConfiguration
   */
  //public static function init( $name = null, $class_name = null ) {
  public static function init() {

    /*
     * since 1.5.1
     * try to avoid 'PHP Strict Standards:  Declaration of ::init() should be compatible with WPDKPreferences::init'
     *
     * Remeber that if a params is missing it is NULL
     */
    $args = func_get_args();
    list( $name, $class_name ) = $args;

    static $instance = array();

    $name = sanitize_title( $name );
    if ( !isset( $instance[$name] ) ) {
      $instance[$name] = get_option( $name );
    }

    if ( is_object( $instance[$name] ) && is_a( $instance[$name], $class_name ) ) {
      return $instance[$name];
    }
    /* Create onfly. */
    else {
      $instance[$name] = new $class_name( $name );
    }

    return $instance[$name];
  }

  /**
   * Return an instance of WPDKConfiguration class
   *
   * @brief Construct
   *
   * @param string $name A string used as name for options. Make it unique more possible.
   */
  protected function __construct( $name ) {
    $this->name = sanitize_title( $name );
  }

  /**
   * Do a delta compare/combine from two tree object config
   *
   * @brief Delta (align) object
   *
   * @return object
   */
  public function delta() {

    /* Check if exists a store version. */
    $store_version = get_option( $this->name );

    /* Get subclass name. */
    $subclass_name = get_class( $this );

    /* Prepare an onfly instance. */
    $delta = $instance = new $subclass_name( $this->name );

    if ( !empty( $store_version ) ) {

      /* In rare case could happen that the stored class is different from onfly class. */
      if ( !is_a( $store_version, $subclass_name ) ) {
        $this->delete();
        $instance->update();
      }
      else {
        /* Do delta. */
        $delta = wpdk_delta_object( $instance, $store_version );
        $delta->update();
      }
    }
    return $delta;
  }

  /**
   * Update on database this configuration.
   *
   * @brief Update
   */
  public function update() {
    update_option( $this->name, $this );
  }

  /**
   * Delete this configuration
   *
   * @brief Delete
   */
  public function delete() {
    delete_option( $this->name );
  }

}

/**
 * Configuration view
 *
 * ## Overview
 *
 * You can subclass this class to build an easy view config. This class help you to render a standard view for the
 * configuration panel. It create for you the wrap HTML content, dispplay the form fields and update or reset the
 * data. In addition display the message for feedback.
 *
 * ### Implement method
 *
 * You can implement some standard method for manage this view
 *
 * * fields() This method return an SDF array to display the form fields
 * * content() This method is used for custom view control. Overwrite the fields()
 * * save() Your own custom save data. If this method id not implement your data aren't saved.
 *
 * @class              WPDKConfigurationView
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 * @deprecated         since 1.2.0 Use WPDKPreferencesViewController and WPDKPreferencesView instead
 */

class WPDKConfigurationView extends WPDKView {

  /**
   *
   * This is an optional string information
   *
   * @brief Top most introduction
   *
   * @var string $introduction
   */
  public $introduction;

  /**
   * The configuration title
   *
   * @brief Title
   *
   * @var string $title
   */
  public $title;

  /**
   * This property is usualy override from subclass class. In this way each class can store its branch.
   *
   * @brief Entry point in configuration model
   *
   * @var WPDKConfiguration $_configuration
   */
  private $_configuration;

  /**
   * This is a pointer to your own sun configuration branch. This can be a object, array or anything else.
   * Will be your job manage this pointer.
   *
   * @brief Sub configuration brach
   *
   * @var array|null|object $_subConfiguration
   */
  private $_subConfiguration;

  /**
   * Create WPDKConfigurationView instance object
   *
   * @brief Construct
   *
   * @param string            $id                ID key of configuration
   * @param string            $title             Title of configuration view
   * @param WPDKConfiguration $configuration     Optional. Main configuration pointer
   * @param object|array      $sub_configuration Optional. Sub configuration
   * @param string            $introduction      Optional. An introduction text message
   *
   * @return WPDKConfigurationView
   */
  public function __construct( $id, $title, $configuration = null, $sub_configuration = null, $introduction = '' )
  {
    $this->id                = sanitize_key( $id );
    $this->title             = $title;
    $this->_configuration    = $configuration;
    $this->_subConfiguration = $sub_configuration;
    $this->introduction      = $introduction;

    /* Check if update */
    $this->_processPost();
  }

  /**
   * Update or reset configuration
   *
   * @brief Update sequence
   *
   */
  private function _processPost()
  {

    $nonce = md5( $this->id );

    if ( isset( $_POST[$nonce] ) && wp_verify_nonce( $_POST[$nonce], $this->id ) ) {

      /* Reset to default? */
      if ( isset( $_POST['resetToDefault'] ) ) {
        $this->resetToDefault();
      }

      /* Update */
      else {
        $bStatus = $this->updatePostData();
        if ( $bStatus ) {
          if ( !is_null( $this->_configuration ) ) {
            $this->_configuration->update();
          }
          add_action( 'wpdk_header_view_after_title', array( $this, 'wpdk_header_view_after_title' ) );
        }
      }
    }
  }

  /**
   * Return the introduction string, if exists, for HTML output
   *
   * @brief Introduction in section
   *
   * @return string
   */
  private function _introduction()
  {
    if ( !empty( $this->introduction ) ) {
      $alert                = new WPDKTwitterBootstrapAlert( 'introduction', $this->introduction, WPDKTwitterBootstrapAlertType::INFORMATION );
      $alert->dismissButton = false;
      $alert->block         = true;
      return $alert->html();
    }
    return $this->introduction;
  }

  /**
   * Reset to default values of sub configuration branch. You can override this method if your sub configuration
   * branch is not an object or not implements a `defaults()` method.
   *
   * @note  You can override this method
   *
   * @brief Reset to default
   */
  public function resetToDefault()
  {
    /* Check if sub configuration is set and if it implements the standard defaults() method. */
    if ( is_object( $this->_subConfiguration ) && method_exists( $this->_subConfiguration, 'resetToDefault' ) ) {
      $this->_subConfiguration->resetToDefault();
      if ( !is_null( $this->_configuration ) ) {
        $this->_configuration->update();
      }

      /* Display message. */
      $message = sprintf( __( '<strong>%s</strong> settings were successfully restored to defaults values!', WPDK_TEXTDOMAIN ), $this->title );
      $alert   = new WPDKTwitterBootstrapAlert( 'success', $message, WPDKTwitterBootstrapAlertType::SUCCESS );
      $alert->display();
    }

    /* @todo In extreme case we could use: */
    /*
    elseif( is_object( $this->_subConfiguration ) ) {
        $class_name = get_class( $this->_subConfiguration );
        $this->_subConfiguration = new $class_name;
        $this->_configuration->update();
    }
    */

    else {
      $message = __( 'No Reset to default settings implement yet!', WPDK_TEXTDOMAIN );
      $alert   = new WPDKTwitterBootstrapAlert( 'no-reset-default', $message, WPDKTwitterBootstrapAlertType::ALERT );
      $alert->display();
    }
  }

  /**
   * Display the content view with form, introduction, fields or custom content. You can override this method with your
   * own drawing.
   *
   * @brief Display the content view form
   *
   */
  public function draw()
  {

    /* Create a nonce key. */
    $nonce                     = md5( $this->id );
    $input_hidden_nonce        = new WPDKHTMLTagInput( '', $nonce, $nonce );
    $input_hidden_nonce->type  = WPDKHTMLTagInputType::HIDDEN;
    $input_hidden_nonce->value = wp_create_nonce( $this->id );

    $layout       = new WPDKUIControlsLayout( $this->fields() );
    $form         = new WPDKHTMLTagForm( $input_hidden_nonce->html() . $this->_introduction() . $layout->html() .
    $this->buttonsUpdateReset() );
    $form->name   = 'wpdk_configuration_view_form-' . $this->id;
    $form->id     = $form->name;
    $form->class  = 'wpdk-form wpdk-configuration-view-' . $this->id;
    $form->method = 'post';
    $form->action = '';
    $form->display();
  }

  /**
   * Process and set you own post data
   *
   * @brief Update configuration
   *
   * @note  You can override this method
   *
   * @return bool TRUE to update the configuration and display the standard sucessfully message, or FALSE to avoid
   *        the update configuration and do a custom display.
   */
  public function updatePostData()
  {
    return true;
  }

  /**
   * Display succefully configuration updated message
   *
   * @brief Action close to after title
   *
   * @param WPDKHeaderView $header_view
   */
  public function wpdk_header_view_after_title( $header_view )
  {
    $message = sprintf( __( '<strong>%s</strong> settings values were successfully updated!', WPDK_TEXTDOMAIN ), $this->title );
    $alert   = new WPDKTwitterBootstrapAlert( 'success', $message, WPDKTwitterBootstrapAlertType::SUCCESS );
    $alert->display();
  }

  /**
   * Return the HTML markup for standard [Reset to default] and [Update] buttons. You can override this method to hide
   * or change the default buttons on bottom form.
   *
   * @brief Buttons Reset and Update
   * @since 1.0.0.b3
   * @note  You can overide this method
   *
   * @return string
   */
  public function buttonsUpdateReset()
  {
    return WPDKUI::buttonsUpdateReset();
  }

  /**
   * Return a SDF array for build the form fields
   *
   * @brief Return a SDF array for build the form fields
   *
   * @note  You can override this method
   *
   */
  public function fields()
  {
    /* To override */
    return array();
  }
}

/// @endcond