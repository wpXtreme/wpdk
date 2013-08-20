<?php
/**
 * This class make easy plugin options management. In WPDK the plugin options are called **preferences**. We likes use
 * preferences term more that options. So is made the WPDKPreferences class.
 *
 * ## Overview
 *
 * You rarely (never) instantiate WPDKPreferences object directly. Instead, you instantiate subclasses of the
 * WPDKPreferences class.
 *
 * ### Getting started
 *
 * Write a your own custom class and extends WPDKPreferences. For example:
 *
 *     class MyPreferences extends WPDKPreferences {
 *     }
 *
 *
 * Implements your custom properties and your branch to other configuration
 *
 *     class MyPreferences extends WPDKPreferences {
 *
 *         const PREFERENCES_NAME = 'my-preferences';
 *
 *         public $version = '1.0.0';
 *
 *         public function __construct() {
 *            parent::__construct( self::PREFERENCES_NAME );
 *         }
 *     }
 *
 * You can implement this utility static method to get the configuration from database or create it onfly if missing or
 * the first time.
 *
 *     class MyPreferences extends WPDKPreferences {
 *
 *       const PREFERENCES_NAME = 'my-preferences';
 *
 *       public $version = '1.0.0';
 *
 *       public static function init() {
 *         return parent::init( self::PREFERENCES_NAME, __CLASS__ );
 *       }
 *     }
 *
 * If you have a preferences branch, or subset of preferences, use:
 *
 *     class MyPreferences extends WPDKPreferences {
 *
 *       const PREFERENCES_NAME = 'my-preferences';
 *
 *       public $version = '1.0.0';
 *
 *       // My configuration branch
 *       public $branch;
 *
 *       public static function init() {
 *         return parent::init( self::PREFERENCES_NAME, __CLASS__ );
 *       }
 *
 *       public function defaults() {
 *         $this->branch = new MyBranch();
 *       }
 *     }
 *
 *     class MyBranch extends WPDKPreferencesBranch {
 *
 *       const NUMBER_OF_SEAT = 'number_of_seat';
 *
 *       public $number_of_seat;
 *
 *       public function defaults() {
 *         $this->number_of_seat = 10; // Default value
 *       }
 *
 *       public function update() {
 *         $this->number_of_seat = $_POST[self::NUMBER_OF_SEAT];
 *       }
 *     }
 *
 * @class              WPDKPreferences
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-08-20
 * @version            1.0.0
 * @since              1.1.3
 *
 */
class WPDKPreferences {

  /**
   * Name used in WordPress option save
   *
   * @brief Preferences name
   *
   * @var string $name
   */
  public $name;

  /**
   * Version of preferences
   *
   * @brief Version
   *
   * @var string $version
   */
  public $version;

  /**
   * Return the preferences object from the option. If not exists then an object is create runtime for you.
   * This is a utility method but you have to override if you don't like insert name and class name parameters. In
   * you own class just use:
   *
   *     public static function init() {
   *       return parent::init( self::PREFERENCES_NAME, __CLASS__ );
   *     }
   *
   * Or, if you like check the prefernces version
   *
   *     public static function init() {
   *       return parent::init( self::PREFERENCES_NAME, __CLASS__, LAST_VERSION );
   *     }
   *
   * @param string      $name       A string used as name for options. Make it unique more possible.
   * @param string      $class_name The subclass class name
   * @param bool|string $version    Optional. Version compare
   *
   * @return WPDKPreferences
   */
  public static function init( $name, $class_name, $version = false )
  {
    static $instance = array();

    /**
     * @var WPDKPreferences $preferences
     */
    $preferences = null;

    $name        = sanitize_title( $name );
    $preferences = isset( $instance[$name] ) ? $instance[$name] : get_option( $name );

    if ( !is_object( $preferences ) || !is_a( $preferences, $class_name ) ) {
      $preferences = new $class_name( $name );
    }

    if ( !empty( $version ) ) {
      /* Or if the onfly version is different from stored version. */
      if ( version_compare( $preferences->version, $version ) < 0 ) {
        /* For i.e. you would like update the version property. */
        $preferences->version = $version;
        $preferences->update();
      }
    }

    /* Check for post data. */
    if ( !isset( $instance[$name] ) && !wpdk_is_ajax() ) {
      if ( isset( $_POST['wpdk_preferences_branch'] ) && !empty( $_POST['wpdk_preferences_branch'] ) ) {
        $branch = $_POST['wpdk_preferences_branch'];
        if ( isset( $_POST['reset-to-default-preferences'] ) ) {
          add_action( 'wpdk_preferences_feedback-' . $branch, array( $preferences, 'wpdk_preferences_feedback_reset' ) );
          $preferences->$branch->defaults();
          $preferences->update();
        }
        elseif ( isset( $_POST['update-preferences'] ) ) {
          add_action( 'wpdk_preferences_feedback-' . $branch, array( $preferences, 'wpdk_preferences_feedback_update' ) );
          $preferences->$branch->update();
          $preferences->update();
        }
      }

      /* @interal use only. */
      if ( isset( $_GET['reset_all'] ) ) {
        $preferences->defaults();
        $preferences->update();
      }
    }

    $instance[$name] = $preferences;

    return $instance[$name];
  }

  /**
   * Return an instance of WPDKPreferences class
   *
   * @brief Construct
   *
   * @param string $name A string used as name for options. Make it unique more possible.
   */
  protected function __construct( $name )
  {
    $this->name = sanitize_title( $name );
    $this->defaults();
  }

  // TODO
  public function wpdk_preferences_feedback_reset()
  {
    $message = __( $_POST['wpdk_preferences_branch'] . ' Your preferences were successfully restored to defaults values!', WPDK_TEXTDOMAIN );
    $alert   = new WPDKTwitterBootstrapAlert( 'info', $message, WPDKTwitterBootstrapAlertType::INFORMATION );
    $alert->display();
  }

  // TODO
  public function wpdk_preferences_feedback_update()
  {
    $message = __( 'Your preferences values were successfully updated!', WPDK_TEXTDOMAIN );
    $alert   = new WPDKTwitterBootstrapAlert( 'info', $message, WPDKTwitterBootstrapAlertType::SUCCESS );
    $alert->display();
  }



  /**
   * Override this method to set the defaults values for this preferences
   *
   * @brief Defaults
   */
  public function defaults()
  {
    die( __METHOD__ . ' must be override in your subclass' );
  }

  /**
   * Do a delta compare/combine from two tree object config
   *
   * @brief Delta (align) object
   *
   * @return object
   */
  public function delta()
  {

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
        $delta = WPDKObject::delta( $instance, $store_version );
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
  public function update()
  {
    update_option( $this->name, $this );
  }

  /**
   * Delete this configuration
   *
   * @brief Delete
   */
  public function delete()
  {
    delete_option( $this->name );
  }

}

/**
 * Utility for preferences branch
 *
 * @class           WPDKPreferencesBranch
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-08-20
 * @version         1.0.0
 *
 */
class WPDKPreferencesBranch {

  /**
   * An instance of WPDKPreferences parent class
   *
   * @brief Parent preferences
   *
   * @var WPDKPreferences $preferences
   */
  private $preferences;

  /**
   * Create an instance of WPDKPreferencesBranch class
   *
   * @brief Construct
   *
   * @param WPDKPreferences $preferences
   *
   * @return WPDKPreferencesBranch
   */
  public function __construct( $preferences )
  {
    //$this->preferences = $preferences;
    $this->defaults();
  }

  /**
   * Use this method to set the default vaues for this preferences branch
   *
   * @brief Default preferences
   */
  public function defaults()
  {
    die( __METHOD__ . ' must be override in your subclass' );
  }

  /**
   * Update preferences branch
   *
   * @note You can to override this method in order to updated your branch preferences
   *
   *     public function update()
   *     {
   *       $this->property = $_POST['property'];
   *     }
   *
   * @brief Update
   */
  public function update()
  {
    // Override to process post data
  }

}