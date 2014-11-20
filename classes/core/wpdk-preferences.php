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
 * @since              1.2.0
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
   * Used to store the preferences for user
   *
   * @brief User ID
   *
   * @var int $user_id
   */
  public $user_id;

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
   * If you wish store preferences for each user use:
   *
   *     public static function init() {
   *       $user_id = get_current_user_id();
   *       return parent::init( self::PREFERENCES_NAME, __CLASS__, LAST_VERSION, $user_id );
   *     }
   *
   * @params string      $name       A string used as name for options. Make it unique more possible.
   * @params string      $class_name The subclass class name
   * @params bool|string $version    Optional. Version compare
   * @params bool|int    $user_id    Optional. User ID
   *
   * @return WPDKPreferences
   */
  public static function init()
  {
    /*
     * since 1.5.1
     * try to avoid 'PHP Strict Standards:  Declaration of ::init() should be compatible with WPDKPreferences::init'
     *
     * Remeber that if a params is missing it is NULL
     */
    $args = func_get_args();
    list( $name, $class_name ) = $args;
    $version = isset( $args[2] ) ? $args[2] : false;
    $user_id = isset( $args[3] ) ? $args[3] : false;

    static $instance = array();
    static $busy = false;

    /**
     * @var WPDKPreferences $preferences
     */
    $preferences = null;

    $name        = sanitize_title( $name );
    $preferences = isset( $instance[$name] ) ? $instance[$name] : ( empty( $user_id ) ? get_option( $name ) : get_user_meta( $user_id, $name, true ) );

    if ( !is_object( $preferences ) || !is_a( $preferences, $class_name ) ) {
      $preferences = new $class_name( $name, $user_id );
    }

    if ( !empty( $version ) ) {

      // Or if the onfly version is different from stored version
      if ( version_compare( $preferences->version, $version ) < 0 ) {

        // For i.e. you would like update the version property
        $preferences->version = $version;
        $preferences->update();
      }
    }

    // Check for post data
    if ( !isset( $instance[$name] ) && !wpdk_is_ajax() ) {
      if ( false === $busy && isset( $_POST['wpdk_preferences_class'] ) && !empty( $_POST['wpdk_preferences_class'] ) &&
        $_POST['wpdk_preferences_class'] == get_class( $preferences )
      ) {
        $busy = true;
        if ( isset( $_POST['wpdk_preferences_branch'] ) && !empty( $_POST['wpdk_preferences_branch'] ) ) {
          $branch = $_POST['wpdk_preferences_branch'];

          // Reset to default a specified branch
          if ( isset( $_POST['reset-to-default-preferences'] ) ) {
            add_action( 'wpdk_preferences_feedback-' . $branch, array( $preferences, 'wpdk_preferences_feedback_reset' ) );

            $preferences->$branch->defaults();
            $preferences->update();

            /**
             * Fires when preferences branch are reset to default.
             *
             * @since 1.7.3
             *
             * @param WPDKPreferencesBranch $branch An instance of WPDKPreferencesBranch class.
             */
            do_action( 'wpdk_preferences_reset_to_default_branch-' . $branch, $preferences->$branch );
          }

          // Update a specified branch
          elseif ( isset( $_POST['update-preferences'] ) ) {
            
            // TODO Replace (asap) with
            //do_action( 'wpdk_flush_cache_third_parties_plugins' );

            // Since 1.5.2 - WP SuperCache patch
            if ( function_exists( 'wp_cache_clear_cache' ) ) {
              wp_cache_clear_cache();
            }

            // Since 1.5.16 - W3 Total Cache Plugin
            if ( function_exists( 'w3tc_pgcache_flush' ) ) {
              w3tc_pgcache_flush();
            }

            add_action( 'wpdk_preferences_feedback-' . $branch, array( $preferences, 'wpdk_preferences_feedback_update' ) );

            $preferences->$branch->update();
            $preferences->update();

            /**
             * Fires when preferences branch are updated.
             *
             * @since 1.7.3
             *
             * @param WPDKPreferencesBranch $branch An instance of WPDKPreferencesBranch class.
             */
            do_action( 'wpdk_preferences_update_branch-' . $branch, $preferences->$branch );
          }
        }

        // Reset all preferences
        elseif ( isset( $_POST['wpdk_preferences_reset_all'] ) ) {
          $preferences->defaults();
          $preferences->update();
        }

        // Repair with delta
        elseif ( isset( $_POST['wpdk_preferences_repair'] ) ) {
          $preferences->delta();
        }

        // Try for import/export
        else {
          $preferences = WPDKPreferencesImportExport::init( $preferences );
        }
      }
      $busy = false;
    }

    $instance[$name] = $preferences;

    return $instance[$name];
  }

  /**
   * Return an instance of WPDKPreferences class
   *
   * @brief Construct
   *
   * @param string   $name    A string used as name for options. Make it unique more possible.
   * @param bool|int $user_id Optional. User ID
   */
  protected function __construct( $name, $user_id = false )
  {
    $this->name    = sanitize_title( $name );
    $this->user_id = $user_id;
    $this->defaults();
  }

  /**
   * Restored feedback message
   *
   * @brief Feedback
   */
  public function wpdk_preferences_feedback_reset()
  {
    $message = __( 'Your preferences were successfully restored to defaults values!', WPDK_TEXTDOMAIN );
    $alert   = new WPDKUIAlert( 'info', $message, WPDKUIAlertType::SUCCESS, __( 'Information', WPDK_TEXTDOMAIN ) );
    $alert->display();
  }

  /**
   * Updated feedback message
   *
   * @brief Feedback
   */
  public function wpdk_preferences_feedback_update()
  {
    $message = __( 'Your preferences values were successfully updated!', WPDK_TEXTDOMAIN );
    $alert   = new WPDKUIAlert( 'info', $message, WPDKUIAlertType::SUCCESS, __( 'Information', WPDK_TEXTDOMAIN ) );
    $alert->display();
  }

  /**
   * Helper to get the preferences from global options or for single users.
   *
   * @brief Get preferences from store
   *
   * @return WPDKPreferences
   */
  public function get()
  {
    return empty( $this->user_id ) ? get_option( $this->name ) : get_user_meta( $this->user_id, $this->name, true );
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

    // Check if exists a store version
    $store_version = $this->get();

    // Get subclass name
    $subclass_name = get_class( $this );

    // Prepare an onfly instance
    $delta = $instance = new $subclass_name( $this->name );

    if ( !empty( $store_version ) ) {

      // In rare case could happen that the stored class is different from onfly class
      if ( !is_a( $store_version, $subclass_name ) ) {
        $this->delete();
        $instance->update();
      }

      // Do delta
      else {
        $delta = WPDKObject::__delta( $instance, $store_version );
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
    if ( empty( $this->user_id ) ) {
      update_option( $this->name, $this );
    }
    else {
      update_user_meta( $this->user_id, $this->name, $this );
    }
  }

  /**
   * Delete this configuration
   *
   * @brief Delete
   */
  public function delete()
  {
    if ( empty( $this->user_id ) ) {
      delete_option( $this->name );
    }
    else {
      delete_user_meta( $this->user_id, $this->name );
    }
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
   * Create an instance of WPDKPreferencesBranch class
   *
   * @brief Construct
   *
   * @return WPDKPreferencesBranch
   */
  public function __construct()
  {
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
   * @note  You can to override this method in order to updated your branch preferences
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


/**
 * Manage a generic import/export of preferences
 *
 * @class           WPDKPreferencesImportExport
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-08-22
 * @version         1.0.1
 *
 * @note            In this release you can import preferences from other users
 *
 */
class WPDKPreferencesImportExport {

  const ERROR_NONE           = false;
  const ERROR_READ_FILE      = 1;
  const ERROR_MALFORMED_FILE = 2;
  const ERROR_VERSION        = 3;
  const ERROR_USER_ID        = 4; // Not used at this momnent

  /**
   * Used for feedback hook
   *
   * @brief Error
   *
   * @var bool|int $error
   */
  private $error;

  /**
   * An instance of WPDKPreferences read from disk
   *
   * @brief Importanted file
   *
   * @var WPDKPreferences $import
   */
  private $import;

  /**
   * An instance of class WPDKPreferences
   *
   * @brief Preferences
   *
   * @var WPDKPreferences $preferences
   */
  public $preferences;

  /**
   * Return the original or imported preferences
   *
   * @brief Init the import/export
   *
   * @param WPDKPreferences $preferences An instance of WPDKPreferences class
   *
   * @return WPDKPreferences
   */
  public static function init( $preferences )
  {
    $import_export = new WPDKPreferencesImportExport( $preferences );
    return $import_export->preferences;
  }

  /**
   * Create an instance of WPDKPreferencesImportExport class
   *
   * @brief Construct
   *
   * @param WPDKPreferences $preferences An instance of WPDKPreferences class
   *
   * @return WPDKPreferencesImportExport
   */
  private function __construct( $preferences )
  {
    $this->preferences = $preferences;
    $this->import      = '';
    $this->error       = false;

    // Check post data
    if ( isset( $_POST['wpdk_preferences_export'] ) ) {
      $this->download();
    }

    // Import
    elseif ( isset( $_POST['wpdk_preferences_import'] ) ) {

      //add_filter( 'wpdk_preferences_import_export_feedback', array( $this, 'wpdk_preferences_import_export_feedback' ) );

      // Fires after the the title.
      add_action( 'wpdk_header_view_after_title-' . $preferences->name . '-header-view', array( $this, 'wpdk_preferences_import_export_feedback' ), 99 );
      //add_action( 'wpdk_header_view_' . $preferences->name . '-header-view_after_title', array( $this, 'wpdk_preferences_import_export_feedback' ), 99 );

      if ( $_FILES['file']['error'] > 0 ) {
        $this->error = self::ERROR_READ_FILE;
      }
      else {
        $this->import( $_FILES['file']['tmp_name'] );
      }
    }
  }

  /**
   * Import procedure
   *
   * @brief Import
   *
   * @param string $filename Unix path of the import file
   */
  private function import( $filename )
  {
    $this->import = unserialize( gzinflate( file_get_contents( $filename ) ) );

    // Check for error in file structure
    if ( !is_object( $this->import ) || !is_a( $this->import, get_class( $this->preferences ) ) ) {
      $this->error = self::ERROR_MALFORMED_FILE;
      return;
    }

    // Check for wrong version
    if ( version_compare( $this->import->version, $this->preferences->version ) > 0 ) {
      $this->error = self::ERROR_VERSION;
      return;
    }

    /* @todo Check for import preferences from other users
     * if ( !empty( $this->import->user_id ) ) {
     * $user_id = get_current_user_id();
     * if ( $user_id !== $this->import->user_id ) {
     * $this->error = self::ERROR_USER_ID;
     * return;
     * }
     * }
     */

    $this->preferences = $this->import;

    // Apply
    $this->preferences->update();
  }

  /**
   * Do a download by filename and buffer
   *
   * @brief Download
   */
  private function download()
  {
    // Create a filtrable filename. Default `name-preferences.wpx`
    $filename = sprintf( '%s.wpx', $this->preferences->name );
    $filename = apply_filters( 'wpdk_preferences_export_filename', $filename, $this->preferences );

    // GZIP the object
    $buffer = gzdeflate( serialize( $this->preferences ) );

    @header( 'Content-Type: application/download' );
    @header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
    @header( 'Cache-Control: public' );
    @header( "Content-Length: " . strlen( $buffer ) );
    @header( 'Pragma: no-cache' );
    @header( 'Expires: 0' );

    echo $buffer;
    exit;
  }

  /**
   * Hook for feedback. See `error` property too.
   *
   * @brief Feedback
   *
   * @return array
   */
  public function wpdk_preferences_import_export_feedback()
  {
    $title   = __( 'Warning!', WPDK_TEXTDOMAIN );
    $content = '';

    switch ( $this->error ) {

      // ALL OK
      case self::ERROR_NONE;
        $title   = __( 'Successfully!', WPDK_TEXTDOMAIN );
        $content = __( 'Import complete.', WPDK_TEXTDOMAIN );
        break;

      // ERROR while reading upload file
      case self::ERROR_READ_FILE:
        $content = sprintf( '%s %s', __( 'Error while read file! Error code:', WPDK_TEXTDOMAIN ), $_FILES['file']['error'] );
        break;

      // ERROR while uncompress upload file
      case self::ERROR_MALFORMED_FILE:
        $content = __( 'Malformed file.', WPDK_TEXTDOMAIN );
        break;

      // Version export error
      case self::ERROR_VERSION:
        $content = __( 'Wrong file version! You are try to import a most recent of export file. Please update your plugin before continue.', WPDK_TEXTDOMAIN );
        break;
    }

    $alert = new WPDKUIAlert( 'feedback', $content, empty( $this->error ) ? WPDKUIAlertType::SUCCESS : WPDKUIAlertType::WARNING, $title );
    $alert->display();
  }

}