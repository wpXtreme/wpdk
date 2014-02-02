<?php

/**
 * This class describe the WPDK user meta extra fields
 *
 * @class           WPDKUserMeta
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-02-26
 * @version         1.0.0
 *
 */
class WPDKUserMeta {

  /**
   * Store the user status.
   *
   * @brief User status
   */
  const STATUS = '_wpdk_user_status';

  /**
   * Store the user status description.
   *
   * @brief User status description
   */
  const STATUS_DESCRIPTION = '_wpdk_user_status_description';

  /**
   * Number of success login
   *
   * @brief Number of success login
   */
  const COUNT_SUCCESS_LOGIN = '_wpdk_user_count_success_login';

  /**
   * Number of wrong login
   *
   * @brief Number of wrong login
   */
  const COUNT_WRONG_LOGIN = '_wpdk_user_count_wrong_login';

  /**
   * Time stamp of last success login
   *
   * @brief Time stamp of last success login
   */
  const LAST_TIME_SUCCESS_LOGIN = '_wpdk_user_last_time_success_login';

  /**
   * Time stamp of last wrong login
   *
   * @brief Time stamp of last wrong login
   */
  const LAST_TIME_WRONG_LOGIN = '_wpdk_user_last_time_wrong_login';

  /**
   * Time stamp of last logout
   *
   * @brief Time stamp of last success login
   */
  const LAST_TIME_LOGOUT = '_wpdk_user_last_time_logout';

  /**
   * Remote Address when an user when created
   *
   * @brief Remote Address
   * @since 1.4.6
   */
  const REMOTE_ADDR = '_wpdk_user_remote_address';

  /**
   * Update the wpdk extra user meta information. This method was written for the user profile.
   * Updates:
   *     LAST_TIME_SUCCESS_LOGIN
   *     COUNT_SUCCESS_LOGIN
   *     LAST_TIME_WRONG_LOGIN
   *     COUNT_WRONG_LOGIN
   *     LAST_TIME_LOGOUT
   *     STATUS
   *     STATUS_DESCRIPTION
   *
   * @brief Update
   *
   * @param int   $user_id   User id
   * @param array $post_data A key value peirs array with values
   */
  public static function update( $user_id, $post_data )
  {

    /* LAST_TIME_SUCCESS_LOGIN */
    $value = isset( $post_data[self::LAST_TIME_SUCCESS_LOGIN] ) ? $post_data[self::LAST_TIME_SUCCESS_LOGIN] : '';
    if ( !empty( $value ) ) {
      $value = strtotime( $value );
      update_user_meta( $user_id, self::LAST_TIME_SUCCESS_LOGIN, $value );
    }

    /* COUNT_SUCCESS_LOGIN */
    $value = isset( $post_data[self::COUNT_SUCCESS_LOGIN] ) ? $post_data[self::COUNT_SUCCESS_LOGIN] : '';
    update_user_meta( $user_id, self::COUNT_SUCCESS_LOGIN, $value );

    /* LAST_TIME_WRONG_LOGIN */
    $value = isset( $post_data[self::LAST_TIME_WRONG_LOGIN] ) ? $post_data[self::LAST_TIME_WRONG_LOGIN] : '';
    if ( !empty( $value ) ) {
      $value = strtotime( $value );
      update_user_meta( $user_id, self::LAST_TIME_WRONG_LOGIN, $value );
    }

    /* COUNT_WRONG_LOGIN */
    $value = isset( $post_data[self::COUNT_WRONG_LOGIN] ) ? $post_data[self::COUNT_WRONG_LOGIN] : '';
    update_user_meta( $user_id, self::COUNT_WRONG_LOGIN, $value );

    /* LAST_TIME_LOGOUT */
    $value = isset( $post_data[self::LAST_TIME_LOGOUT] ) ? $post_data[self::LAST_TIME_LOGOUT] : '';
    if ( !empty( $value ) ) {
      $value = strtotime( $value );
      update_user_meta( $user_id, self::LAST_TIME_LOGOUT, $value );
    }

    /* STATUS */
    $value = isset( $post_data[self::STATUS] ) ? $post_data[self::STATUS] : '';
    update_user_meta( $user_id, self::STATUS, $value );

    /* STATUS_DESCRIPTION */
    $value = isset( $post_data[self::STATUS_DESCRIPTION] ) ? $post_data[self::STATUS_DESCRIPTION] : '';
    update_user_meta( $user_id, self::STATUS_DESCRIPTION, $value );

  }

}


/**
 * User status model
 *
 * @class           WPDKUserMeta
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-02-26
 * @version         1.0.0
 *
 */
class WPDKUserStatus {

  /**
   * The user is temporary disabled.
   *
   * @brief Disabled
   */
  const DISABLED = 'disabled';

  /**
   * Return the list of supported user statuses
   *
   * @brief User statuses
   *
   * @return mixed
   */
  public static function statuses()
  {
    $statuses = array(
      ''             => __( 'Not set' ),
      self::DISABLED => __( 'Disabled' ),
    );
    return apply_filters( 'wpdk_user_status_statuses', $statuses );
  }
}


/**
 * The WPDKUser class is an extension of WordPress WP_User class.
 *
 * ## Overview
 * In the WPDKUser class you find all method and properties loose in standard WordPress WP_User class.
 *
 * @class              WPDKUser
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-09-17
 * @version            1.0.0
 *
 */
class WPDKUser extends WP_User {

  /**
   * The user first name. Same $user->get( 'first_name' )
   *
   * @brief First name
   *
   * @var string $first_name
   */
  public $first_name;

  /**
   * The user last name. Same $user->get( 'last_name' )
   *
   * @brief Last name
   *
   * @var string $last_name
   */
  public $last_name;

  /**
   * The user nice name
   *
   * @brief Nice name
   *
   * @var string $nice_name
   */
  public $nice_name;

  /**
   * Compose first name and last name
   *
   * @brief Full name
   *
   * @var string $full_name
   */
  public $full_name;

  /**
   * Replcement of $user->data->display_name
   *
   * @brief Display name
   *
   * @var string $display_name
   */
  public $display_name;

  /**
   * Replcement of $user->data->user_email
   *
   * @brief Email user
   *
   * @var string $email
   */
  public $email;

  /**
   * WPDK Extension for status
   *
   * @brief Status
   *
   * @var string $status
   */
  public $status;

  /**
   * WPDK Extension for status description
   *
   * @brief Status description
   *
   * @var string $statusDescription
   */
  public $statusDescription;

  /**
   * Create an instance of WPDKUser class
   *
   * @brief Constructor
   *
   * @param int|object|array|string $user      Optional. User's ID, WP_User object, WPDKUser object, array. If 0 (zero)
   *                                           the current user is get
   * @param string                  $name      Optional. User's username
   * @param int|string              $blog_id   Optional. Blog ID, defaults to current blog.
   *
   * @return WPDKUser
   */
  public function __construct( $user = 0, $name = '', $blog_id = '' )
  {

    $id_user = 0;

    /* Sanitize $id. */
    if ( is_numeric( $user ) ) {
      $id_user = $user;
      /* If zero get the current id user. */
      if ( empty( $id_user ) ) {
        $id_user = get_current_user_id();
      }
    }
    elseif ( is_object( $user ) && isset( $user->ID ) ) {
      $id_user = absint( $user->ID );
    }
    elseif ( is_array( $user ) && isset( $user['ID'] ) ) {
      $id_user = absint( $user['ID'] );
    }
    /* Get by email. */
    elseif ( is_string( $user ) && is_email( $user ) ) {
      $user    = get_user_by( 'email', $user );
      $id_user = $user->ID;
    }

    parent::__construct( $id_user, $name, $blog_id );

    /* Set the extended property when an user is set. */
    if ( !empty( $id_user ) ) {
      $this->first_name        = $this->get( 'first_name' );
      $this->last_name         = $this->get( 'last_name' );
      $this->nice_name         = $this->data->user_nicename;
      $this->full_name         = $this->full_name( $this->first_name, $this->last_name );
      $this->display_name      = $this->data->display_name;
      $this->email             = sanitize_email( $this->data->user_email );
      $this->status            = $this->get( WPDKUserMeta::STATUS );
      $this->statusDescription = $this->get( WPDKUserMeta::STATUS_DESCRIPTION );
      /* Sanitize string->int */
      $this->data->ID = absint( $id_user );
    }
  }

  /**
   * Compose the first letter of first name and append last name, Eg. John Gold -> J.Gold
   *
   * @brief Sanitize a nice name
   *
   * @param string $firstName First name
   * @param string $lastName  Last name
   *
   * @return string
   */
  public static function nice_name( $firstName, $lastName )
  {
    $result = sprintf( '%s.%s', strtoupper( substr( $firstName, 0, 1 ) ), ucfirst( $lastName ) );
    return $result;
  }

  /**
   * Merge first name and last name.
   *
   * @brief Sanitize a full name
   *
   * @param string $firstName First name
   * @param string $lastName  Last name
   * @param bool   $nameFirst Optional. Default to TRUE [firstname lastname]. Set to FALSE to invert order
   *
   * @return string
   */
  public static function full_name( $firstName, $lastName, $nameFirst = true )
  {
    if ( $nameFirst ) {
      $result = sprintf( '%s %s', $firstName, $lastName );
    }
    else {
      $result = sprintf( '%s %s', $lastName, $firstName );
    }
    return $result;
  }

  /**
   * This method is an alias of WPDKUsers::create()
   *
   * @brief Create a WordPress user.
   *
   * @param string      $first_name First name
   * @param string      $last_name  Last name
   * @param string      $email      Email address
   * @param bool|string $password   Optional. Clear password, if set to FALSE a random password is created
   * @param bool        $enabled    Optional. If TRUE the user is enabled, FALSE to set in pending
   * @param string      $role       Optional. User role, default 'subscriber'
   *
   * @return int|WP_Error
   */
  public function create( $first_name, $last_name, $email, $password = false, $enabled = false, $role = 'subscriber' )
  {
    return WPDKUsers::init()->create( $first_name, $last_name, $email, $password, $enabled, $role );
  }

  // -----------------------------------------------------------------------------------------------------------------
  // User transient
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Get the value of a user transient.
   * If the transient does not exist or does not have a value, then the return value will be false.
   *
   * @brief Get
   * @since 1.4.8
   *
   * @uses  apply_filters() Calls 'pre_user_transient_$transient' hook before checking the transient. Any value other than
   *        false will "short-circuit" the retrieval of the transient and return the returned value.
   * @uses  apply_filters() Calls 'user_transient_$transient' hook, after checking the transient, with the transient value.
   *
   * @param string $transient Transient name. Expected to not be SQL-escaped
   * @param int    $user_id   Optional. User ID. If null the current user id is used instead
   *
   * @return mixed Value of transient
   */
  public static function getTransientWithUser( $transient, $user_id = null )
  {
    $user_id = is_null( $user_id ) ? get_current_user_id() : $user_id;

    $pre = apply_filters( 'pre_user_transient_' . $transient, false, $user_id );
    if ( false !== $pre ) {
      return $pre;
    }

    $transient_timeout = '_transient_timeout_' . $transient;
    $transient         = '_transient_' . $transient;
    if ( get_user_meta( $user_id, $transient_timeout, true ) < time() ) {
      delete_user_meta( $user_id, $transient );
      delete_user_meta( $user_id, $transient_timeout );
      return false;
    }

    $value = get_user_meta( $user_id, $transient, true );

    return apply_filters( 'user_transient_' . $transient, $value, $user_id );
  }

  /**
   * Get the value of transient for this WPDKUser object instance.
   * If the transient does not exist or does not have a value, then the return value will be false.
   *
   * @brief Get
   * @since 1.4.8
   *
   * @uses  apply_filters() Calls 'pre_user_transient_$transient' hook before checking the transient. Any value other than
   *        false will "short-circuit" the retrieval of the transient and return the returned value.
   * @uses  apply_filters() Calls 'user_transient_$transient' hook, after checking the transient, with the transient value.
   *
   * @param string $transient Transient name. Expected to not be SQL-escaped
   *
   * @return mixed Value of transient
   */
  public function getTransient( $transient )
  {
    if( !empty( $this->ID ) ) {
      return self::getTransientWithUser( $transient, $this->ID );
    }
  }

  /**
   * Return the transient user time
   *
   * @brief Transient time
   * @since 1.4.8
   *
   * @param string $transient Transient name. Expected to not be SQL-escaped
   * @param int    $user_id   Optional. User ID. If null the current user id is used instead
   *
   * @return int
   */
  public static function getTransientTimeWithUser( $transient, $user_id = null )
  {
    $user_id           = is_null( $user_id ) ? get_current_user_id() : $user_id;
    $transient_timeout = '_transient_timeout_' . $transient;
    return get_user_meta( $user_id, $transient_timeout, true );
  }

  /**
   * Return the transient user time
   *
   * @brief Transient time
   * @since 1.4.8
   *
   * @param string $transient Transient name. Expected to not be SQL-escaped
   *
   * @return int
   */
  public function getTransientTime( $transient )
  {
    if ( !empty( $this->ID ) ) {
      return self::getTransientTimeWithUser( $transient, $this->ID );
    }
  }


  /**
   * Set/update the value of a user transient.
   *
   * You do not need to serialize values. If the value needs to be serialized, then it will be serialized before it is set.
   *
   * @brief Set
   * @since 1.3.0
   *
   * @uses  apply_filters() Calls 'pre_set_user_transient_$transient' hook to allow overwriting the transient value to be
   *        stored.
   * @uses  do_action() Calls 'set_user_transient_$transient' and 'setted_transient' hooks on success.
   *
   * @param string $transient  Transient name. Expected to not be SQL-escaped.
   * @param mixed  $value      Transient value. Expected to not be SQL-escaped.
   * @param int    $expiration Time until expiration in seconds, default 0
   * @param int    $user_id    Optional. User ID. If null the current user id is used instead
   *
   * @return bool False if value was not set and true if value was set.
   */
  public static function setTransientWithUser( $transient, $value, $expiration = 0, $user_id = null )
  {
    $user_id = is_null( $user_id ) ? get_current_user_id() : $user_id;

    $value = apply_filters( 'pre_set_user_transient_' . $transient, $value, $user_id );

    $transient_timeout = '_transient_timeout_' . $transient;
    $transient         = '_transient_' . $transient;
    if ( false === get_user_meta( $user_id, $transient, true ) ) {
      if ( $expiration ) {
        update_user_meta( $user_id, $transient_timeout, time() + $expiration );
      }
      $result = update_user_meta( $user_id, $transient, $value );
    }
    else {
      if ( $expiration ) {
        update_user_meta( $user_id, $transient_timeout, time() + $expiration );
      }
      $result = update_user_meta( $user_id, $transient, $value );
    }

    if ( $result ) {
      do_action( 'set_user_transient_' . $transient );
      do_action( 'setted_user_transient', $transient, $user_id );
    }
    return $result;
  }

  /**
   * Set/update the value of transient for this WPDKUser object instance.
   *
   * You do not need to serialize values. If the value needs to be serialized, then it will be serialized before it is set.
   *
   * @brief Set
   * @since 1.3.0
   *
   * @uses  self::setTransientWithUser()
   *
   * @param string $transient  Transient name. Expected to not be SQL-escaped.
   * @param mixed  $value      Transient value. Expected to not be SQL-escaped.
   * @param int    $expiration Time until expiration in seconds, default 0
   *
   * @return bool False if value was not set and true if value was set.
   */
  public function setTransient( $transient, $value, $expiration = 0 )
  {
    if ( !empty( $this->ID ) ) {
      self::setTransientWithUser( $transient, $value, $expiration, $this->ID );
    }
  }


  // -----------------------------------------------------------------------------------------------------------------
  // User info
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * This method is an alias of WPDKUsers::gravatar(). Return the HTML markup for tag img. False otherwise.
   *
   * @brief Return HTML img of gravatar.
   *
   * @param int    $size    Optional. Gravatar size
   * @param string $alt     Optional. Alternate string for alt attribute
   * @param string $default Optional. Gravatar ID for default (not found) gravatar image
   *
   * @return string
   */
  public function gravatar( $size = 40, $alt = '', $default = "wavatar" )
  {
    return WPDKUsers::init()->gravatar( $this->ID, $size, $alt, $default );
  }

  /**
   * This method is an alias of WPDKUsers::avatar(). Return on instance of WPDKHTMLTagImg class, FALSE otherwise.
   *
   * @brief WPDKHTMLTagImg
   * @since 1.4.8
   *
   * @param int $size Optional. Gravatar size
   *
   * @return WPDKHTMLTagImg|bool
   */
  public function avatar( $size = 40 )
  {
    return WPDKUsers::init()->avatar( $this->ID, $size );
  }



  /**
   * Return the age (in year) from a date in format YYYY-MM-DD or DD/MM/YYYY
   *
   * @brief Get the user age from birth date
   *
   * @todo  To improve in date format
   *
   * @param string $birthday Birth of date. MySQL YYYY-MM-DD o in formato data unico vincolo per adesso è il supporto solo per data italiana, ovvero giorno/meso/anno
   *
   * @return int Age
   */
  public function age( $birthday )
  {
    $year_diff = 0;

    if ( !empty( $birthday ) ) {
      if ( false !== strpos( $birthday, '-' ) ) {
        list( $year, $month, $day ) = explode( '-', $birthday );
      }
      else {
        list( $day, $month, $year ) = explode( '/', $birthday );
      }
      $year_diff  = date( 'Y' ) - $year;
      $month_diff = date( 'm' ) - $month;
      $day_diff   = date( 'd' ) - $day;
      if ( $month_diff < 0 || ( $month_diff == 0 && $day_diff < 0 ) ) {
        $year_diff--;
      }
    }
    return intval( $year_diff );
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Roles
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return TRUE if the current user has one o more roles.
   *
   * @brief Check if the current user has one or more roles
   *
   * @param string|array $roles Single string or array list of roles.
   *
   * @return bool TRUE if user has the role, else FALSE.
   */
  public function hasRoles( $roles )
  {
    global $wp_roles;

    if ( !isset( $wp_roles ) ) {
      $wp_roles = new WP_Roles();
    }

    $current_roles = $this->roles;
    if ( is_array( $roles ) ) {
      foreach ( $roles as $role ) {
        if ( in_array( $role, $current_roles ) ) {
          return true;
        }
      }
      return false;
    }
    else {
      return in_array( $roles, $current_roles );
    }
  }

  /**
   * Return the role name of a user
   *
   * @param int $id_user User ID
   *
   * @return bool|string Ruolo utente o FALSE se errore.
   */
  public static function roleNameForUserID( $id_user )
  {
    global $wp_roles;

    $id_user = absint( $id_user );
    $user    = new WP_User( $id_user );
    if ( !empty( $user ) ) {
      $role_key = $user->roles[key( $user->roles )];
      if ( !empty( $role_key ) ) {
        return $wp_roles->roles[$role_key]['name'];
      }
    }
    return false;
  }

  /**
   * Restutuisce true se l'utente passato negli inputs (o l'utente corrente se non viene passato id utente) possiede
   * un determinato permesso (capability)
   *
   * @brief Check if an user has a specify capability
   *
   * @param string $cap     Capability ID
   * @param int    $id_user Optional. User ID or null for get current user ID
   *
   * @return bool True se l'utente supporta la capability
   */
  public static function hasCap( $cap, $id_user = null )
  {
    if ( is_null( $id_user ) ) {
      $id_user = get_current_user_id();
    }
    $user = new WP_User( $id_user );
    if ( $user ) {
      return $user->has_cap( $cap );
    }
    return false;
  }

  /**
   * Return TRUE if the user has one or more capabilities.
   *
   * @brief Check if an user has one or more capabilities
   *
   * @param string|array $caps Single string capability or array list
   *
   * @return bool Se almeno uno dei permessi è presente restituisce true, altrimenti false
   */
  public function hasCaps( $caps )
  {

    $all_caps = $this->allcaps;
    if ( is_array( $caps ) ) {
      foreach ( $caps as $cap ) {
        if ( isset( $all_caps[$cap] ) ) {
          return true;
        }
      }
    }
    else {
      return in_array( $all_caps, $caps );
    }
    return false;
  }

  /**
   * Restituisce la lista di tutte le capabilities attualmente presenti in WordPress, scorrendo tutti i ruoli
   * presenti ed estraendo le capabilities.
   *
   * @deprecated
   *
   * @return array
   */
  public static function allCapabilities()
  {
    global $wp_roles;
    $merge = array();

    $roles = $wp_roles->get_names();
    foreach ( $roles as $key => $rolename ) {
      $role  = get_role( $key );
      $merge = array_merge( $merge, $role->capabilities );
    }
    $result = array_keys( $merge );
    sort( $result );
    $result = array_combine( $result, $result );
    return $result;
  }

  /**
   * Aggiunge e/o rimuove i permessi (capability) da un utente. L'aggiunta avviene eseguendo una match tra una lista
   * di capability selezionate e una lista di confronto, che corrisponde in pratica alle capabilities che possono
   * essere aggiunte. Senza il parametro $capabilities verrebbero prese in considerazione tutte le capabilities, cosa
   * che ovviamente non va bene. In pratica questo metodo dice; in base a questa lista ($capabilities) quali tra
   * quelle selezionate ($selected_caps) devo attivate/disattivare ?
   *
   *
   * @param int   $id_user       ID dell'utente
   * @param array $selected_caps Lista delle capability da aggiungere
   * @param array $capabilities  Lista di confronto per capire quale capability aggiungere e quale rimuovere
   */
  public static function updateUserCapabilities( $id_user, $selected_caps, $capabilities )
  {
    if ( $id_user && is_array( $selected_caps ) ) {
      $user = new WP_User( $id_user );
      foreach ( $capabilities as $key => $cap ) {
        if ( in_array( $key, $selected_caps ) ) {
          /* Add */
          $user->add_cap( $key );
        }
        else {
          /* Del */
          $user->remove_cap( $key );
        }
      }
    }
  }

}


/**
 * The WPDKUsers allow to manage the WordPress users
 *
 * ## Overview
 * This is a singleton class for manage all WordPress users and adding a several extension for control, checking,
 * login and more.
 *
 * ### User meta
 *
 * See the WPDKUserMeta
 *
 * @class              WPDKUsers
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-02-26
 * @version            1.2.0
 *
 */
class WPDKUsers {

  /**
   * Return a singleton instance of WPDKUsers class
   *
   * @brief Init the Singleton instance
   *
   * @return WPDKUsers
   */
  public static function init()
  {
    static $instance = null;
    if ( is_null( $instance ) ) {
      $instance = new WPDKUsers();
    }
    return $instance;
  }

  /**
   * Return an instance of WPDKUsers class and register the primary hook for manage and enhancer the standard
   * WordPress User.
   *
   * @brief Construct
   *
   * @return WPDKUsers
   */
  private function __construct()
  {

    /* Do a several action/filter to monitoring user action. */

    //$this->logout();
    add_action( 'init', array( $this, 'logout' ) );

    /* Main hook for common check in front end. */
    //add_action( 'wp_head', array( $this, 'wp_head_signin' ) );
    //add_action( 'wp_head', array( $this, 'wp_head_signout' ) );

    /* Hook on Login. */
    add_action( 'wp_login', array( $this, 'wp_login' ) );
    add_action( 'wp_logout', array( $this, 'wp_logout' ) );
    add_action( 'wp_login_failed', array( $this, 'wp_login_failed' ) );

    /* includes/wp_insert_user() Nuovo Utente registrato  */
    //add_action( 'user_register', array( $this, 'user_register' ) );

    /* includes/wp_insert_user() Utente già registrato quindi aggiornamento dati */
    add_action( 'delete_user', array( $this, 'delete_user' ) );
    add_action( 'deleted_user', array( $this, 'deleted_user' ) );

    /* Backend user profile (own) */
    add_action( 'show_user_profile', array( $this, 'show_user_profile' ) );
    add_action( 'personal_options_update', array( $this, 'personal_options_update' ) );
    add_action( 'personal_options', array( $this, 'personal_options' ) );
    add_action( 'profile_personal_options', array( $this, 'profile_personal_options' ) );

    /* Backend user profile (other) */
    add_action( 'edit_user_profile', array( $this, 'edit_user_profile' ) );
    add_action( 'edit_user_profile_update', array( $this, 'edit_user_profile_update' ) );

    /* Extends User edit profile */

    /* Disable and locking featured */
    add_filter( 'wp_authenticate_user', array( $this, 'wp_authenticate_user' ), 1 );

    add_filter( 'user_contactmethods', array( $this, 'user_contactmethods' ) );
  }

  /**
   * Force an user logout when disabled or if in GET you pass wpdk_logout
   *
   * @brief Logout an user
   */
  public function logout()
  {
    /* If a user is logged in. */
    if ( is_user_logged_in() ) {
      $user_id = get_current_user_id();
      $status  = get_user_meta( $user_id, WPDKUserMeta::STATUS, true );
      $logout  = false;

      /* Logout for disabled User. */
      if ( !empty( $status ) && WPDKUserStatus::DISABLED == $status ) {
        $logout = true;
      }

      /* Manual logout. */
      if ( isset( $_REQUEST['wpdk_logout'] ) ) {
        $logout = true;
      }

      if ( true === $logout ) {
        /* Log off the user. */
        wp_logout();
        if ( is_admin() ) {
          wp_safe_redirect( wp_login_url( wp_get_referer() ) );
        }
        else {
          wp_safe_redirect( stripslashes( $_SERVER['REQUEST_URI'] ) );
        }
        exit;
      }
    }
  }


  // -----------------------------------------------------------------------------------------------------------------
  // Signin actions and filters
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Called when an user is authenticate.
   *
   * @brief WP authenticate hook
   *
   * @param WP_User $user WP_User object
   *
   * @return WP_Error|WP_User
   */
  public function wp_authenticate_user( $user )
  {
    if ( is_wp_error( $user ) ) {
      return $user;
    }

    /* Get the user status. */
    $status = $user->get( WPDKUserMeta::STATUS );

    if ( WPDKUserStatus::DISABLED == $status ) {
      /* Ask for continue. */
      $continue = apply_filters( 'wpdk_users_should_denied_signin', true, $user->ID, $status );
      if ( $continue ) {
        $message = $user->get( WPDKUserMeta::STATUS_DESCRIPTION );
        $message = apply_filters( 'wpdk_users_access_denied_status_description', $message, $user->ID, $status );
        return new WP_Error( 'wpdk_users_access_denied', $message, array(
          $user,
          $status
        ) );
      }
    }

    return $user;
  }

  /**
   * This method is called when an user signin with homonymous WordPress action `wp_login`.
   *
   * @brief WP Login hook
   *
   * @param string  $user_login User login
   * @param WP_User $user       Optional.
   */
  public function wp_login( $user_login, $user = null )
  {

    /* Get user by login. */
    if ( is_null( $user ) ) {
      $user = get_user_by( 'login', $user_login );
    }

    /* Get success login count. */
    $count = absint( $user->get( WPDKUserMeta::COUNT_SUCCESS_LOGIN ) );
    if ( empty( $count ) ) {
      $count = 0;
    }
    update_user_meta( $user->ID, WPDKUserMeta::COUNT_SUCCESS_LOGIN, $count + 1 );
    update_user_meta( $user->ID, WPDKUserMeta::COUNT_WRONG_LOGIN, 0 );
    update_user_meta( $user->ID, WPDKUserMeta::LAST_TIME_SUCCESS_LOGIN, time() );
  }

  /**
   * This method is called when an user wrong signin with homonymous WordPress action `wp_login_failed`.
   *
   * @brief WP Login failed hook
   *
   * @param string $user_login User login
   *
   * @return bool
   */
  public function wp_login_failed( $user_login )
  {

    if ( empty( $user_login ) ) {
      return false;
    }

    $user = get_user_by( 'login', $user_login );

    /* Check if the user exists. */
    if ( false === $user ) {
      return false;
    }

    $count = absint( $user->get( WPDKUserMeta::COUNT_WRONG_LOGIN ) );
    if ( empty( $count ) ) {
      $count = 0;
    }
    $count++;

    /* Update the wrong login count. */
    update_user_meta( $user->ID, WPDKUserMeta::COUNT_WRONG_LOGIN, $count );

    /* Notified the wrong user login count. */
    do_action( 'wpdk_user_wrong_login_count', $user->ID, $count );

    return true;

  }

  // -------------------------------------------------------------------------------------------------------------------
  // Signin utility
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Do a WordPress Sign in and call filters and action
   *
   * @brief Signin
   *
   * @param string|int $user     Any user id, user email or user login
   * @param string     $password Password
   * @param bool       $remember Optional. TRUE for set a cookie for next login
   *
   * @return bool TRUE if success, FALSE for access denied
   */
  public function signIn( $user, $password, $remember = false )
  {

    $user_id = false;
    $email   = '';

    /* Check user id */
    if ( is_numeric( $user ) ) {
      $user_id = $user;
    }
    /* Check for email */
    elseif ( is_email( $user ) ) {
      /* Sanitize email */
      $email = sanitize_email( $user );
      /* User exists with email */
      $user_id = email_exists( $email );
    }
    /* Check for user login */
    else {
      $user = get_user_by( 'login', $user );
      if ( $user ) {
        $user_id = $user->ID;
      }
    }

    if ( false !== $user_id ) {
      $user = new WPDKUser( $user_id );
      if ( $user->exists() && WPDKUserStatus::DISABLED !== $user->status ) {
        $result = wp_authenticate( $user->user_login, $password );
        if ( !is_wp_error( $result ) ) {
          /* Set remember cookie */
          wp_set_auth_cookie( $user->ID, $remember );
          do_action( 'wp_login', $user->user_login, $user );

          /* Internal counter */
          $this->wp_login( $user->user_login, $user );

          /* Authenticate! You are */
          wp_set_current_user( $user->ID );
          return true;
        }
      }
    }

    do_action( 'wpdk_singin_wrong', empty( $email ) ? $user_id : $email, $password );

    return false;
  }

  /**
   * Try to authenticate an user without log-in in the system. This method is very different by signIn().
   * Return the user ID on success, FALSE otherwise.
   *
   * @brief User Authenticate
   *
   * @param string $email    The email address of the user
   * @param string $password The user password
   *
   * @return int|bool
   */
  public function authenticate( $email, $password )
  {
    if ( empty( $email ) || empty( $password ) ) {
      return false;
    }

    $id_user = email_exists( $email );
    if ( !empty( $id_user ) ) {
      $user   = new WPDKUser( $id_user );
      $access = wp_authenticate( $user->data->user_login, $password );
      if ( !is_wp_error( $access ) ) {
        return $id_user;
      }
    }
    return false;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Signout actions and filters
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * This method is called when an user signout with homonymous WordPress action `wp_logout`.
   *
   * @brief WP Logout hook
   */
  public function wp_logout()
  {
    $user_id = get_current_user_id();
    if ( !empty( $user_id ) ) {
      update_user_meta( $user_id, WPDKUserMeta::LAST_TIME_LOGOUT, time() );
    }
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Signout utility
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Perform sign out.
   *
   * @brief Do signout
   */
  public function signout()
  {

    /* Esegue il logout da WordPress. */
    wp_logout();

    /* Per default ridirezione sulla home page del sito. */
    $blog_url = get_bloginfo( 'url' );
    wp_safe_redirect( $blog_url );
    exit();
  }

  // ----------------------------------------------------------------------------------------------------------------
  // Create utility
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Create a WordPress user and return the user id on success, WP_Error otherwise.
   *
   * @param string      $first_name First name
   * @param string      $last_name  Last name
   * @param string      $email      Email address
   * @param bool|string $password   Optional. Clear password, if set to FALSE a random password is created
   * @param bool        $enabled    Optional. If FALSE the WPDK user status is set to disable. Default FALSE.
   * @param string      $role       Optional. User role, default 'subscriber'
   *
   * @return int|WP_Error
   */
  public function create( $first_name, $last_name, $email, $password = false, $enabled = false, $role = 'subscriber' )
  {

    /* For security reason an user must have a password. */
    if ( false === $password ) {
      $password = WPDKCrypt::randomAlphaNumber();
      /* @todo Notify to user the password. */
      do_action( 'wpdk_users_random_password', $password );
    }

    $nice_name    = WPDKUser::nice_name( $first_name, $last_name );
    $display_name = WPDKUser::full_name( $first_name, $last_name );

    $user_data = array(
      'user_login'    => $email,
      'user_pass'     => $password,
      'user_email'    => $email,
      'user_nicename' => $nice_name,
      'nickname'      => $nice_name,
      'display_name'  => $display_name,
      'first_name'    => $first_name,
      'last_name'     => $last_name,
      'role'          => $role
    );

    $user_id = wp_insert_user( $user_data );

    if ( is_wp_error( $user_id ) ) {
      return $user_id;
    }

    /* Store IP Address */
    if ( isset( $_SERVER['REMOTE_ADDR'] ) && !empty( $_SERVER['REMOTE_ADDR'] ) ) {
      update_user_meta( $user_id, WPDKUserMeta::REMOTE_ADDR, $_SERVER['REMOTE_ADDR'] );
    }

    /* Disable user if required */
    if ( false === $enabled ) {
      $status = apply_filters( 'wpdk_users_status', WPDKUserStatus::DISABLED, $user_id );
      update_user_meta( $user_id, WPDKUserMeta::STATUS, $status );

      $status_description = apply_filters( 'wpdk_users_status_description', '', $user_id );
      update_user_meta( $user_id, WPDKUserMeta::STATUS_DESCRIPTION, $status_description );
    }

    return $user_id;
  }


  // -----------------------------------------------------------------------------------------------------------------
  // User profile (own)
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * This hook only triggers when a user is viewing their own profile page. If you want to apply your hook to ALL
   * profile pages (not just the current user) then you also need to use the edit_user_profile hook.
   *
   * @brief WP Show user profile hook
   *
   * @param WP_User $user WordPress user object
   */
  public function show_user_profile( $user )
  {

    echo '<br clear="all" /><a name="wpdk"></a>';

    $message              = __( 'This view <strong>is enhanced</strong> by wpXtreme and WPDK framework. Please, have a look below for additional information.', WPDK_TEXTDOMAIN );
    $alert                = new WPDKTwitterBootstrapAlert( 'info', $message, WPDKTwitterBootstrapAlertType::INFORMATION );
    $alert->dismissButton = false;
    $alert->display();

    /* Only the administrator can edit this extra information */
    $disabled = !current_user_can( 'manage_options' );

    /* Sanitize values */
    $last_time_success_login = $user->get( WPDKUserMeta::LAST_TIME_SUCCESS_LOGIN );
    $last_time_wrong_login   = $user->get( WPDKUserMeta::LAST_TIME_WRONG_LOGIN );
    $last_time_logout        = $user->get( WPDKUserMeta::LAST_TIME_LOGOUT );
    $status_description      = $user->get( WPDKUserMeta::STATUS_DESCRIPTION );

    $fields = array(
      __( 'Login information', WPDK_TEXTDOMAIN )  => array(
        array(
          array(
            'type'     => WPDKUIControlType::DATETIME,
            'name'     => WPDKUserMeta::LAST_TIME_SUCCESS_LOGIN,
            'label'    => __( 'Last success login', WPDK_TEXTDOMAIN ),
            'value'    => empty( $last_time_success_login ) ? '' : date( __( 'm/d/Y H:i', WPDK_TEXTDOMAIN ), $last_time_success_login ),
            'disabled' => $disabled
          ),
          array(
            'type'     => WPDKUIControlType::NUMBER,
            'name'     => WPDKUserMeta::COUNT_SUCCESS_LOGIN,
            'label'    => __( '# success login', WPDK_TEXTDOMAIN ),
            'value'    => $user->get( WPDKUserMeta::COUNT_SUCCESS_LOGIN ),
            'disabled' => true
          ),
        ),
        array(
          array(
            'type'     => WPDKUIControlType::DATETIME,
            'name'     => WPDKUserMeta::LAST_TIME_WRONG_LOGIN,
            'label'    => __( 'Last wrong login', WPDK_TEXTDOMAIN ),
            'value'    => empty( $last_time_wrong_login ) ? '' : date( __( 'm/d/Y H:i', WPDK_TEXTDOMAIN ), $last_time_wrong_login ),
            'disabled' => $disabled
          ),
          array(
            'type'     => WPDKUIControlType::NUMBER,
            'name'     => WPDKUserMeta::COUNT_WRONG_LOGIN,
            'label'    => __( '# wrong login', WPDK_TEXTDOMAIN ),
            'value'    => $user->get( WPDKUserMeta::COUNT_WRONG_LOGIN ),
            'disabled' => $disabled
          ),
        ),
      ),

      __( 'Logout information', WPDK_TEXTDOMAIN ) => array(
        array(
          array(
            'type'     => WPDKUIControlType::DATETIME,
            'name'     => WPDKUserMeta::LAST_TIME_LOGOUT,
            'label'    => __( 'Last logout', WPDK_TEXTDOMAIN ),
            'value'    => empty( $last_time_logout ) ? '' : date( __( 'm/d/Y H:i', WPDK_TEXTDOMAIN ), $last_time_logout ),
            'disabled' => $disabled
          ),
        ),
      ),

      __( 'Status', WPDK_TEXTDOMAIN )             => array(
        array(
          array(
            'type'     => WPDKUIControlType::SELECT,
            'name'     => WPDKUserMeta::STATUS,
            'label'    => __( 'Status', WPDK_TEXTDOMAIN ),
            'value'    => $user->get( WPDKUserMeta::STATUS ),
            'options'  => WPDKUserStatus::statuses(),
            'disabled' => $disabled
          ),
        ),
        array(
          array(
            'type'        => WPDKUIControlType::TEXTAREA,
            'name'        => WPDKUserMeta::STATUS_DESCRIPTION,
            'rows'        => 3,
            'cols'        => 40,
            'label'       => __( 'Description', WPDK_TEXTDOMAIN ),
            'placeholder' => __( 'eg: this user is disabled because...', WPDK_TEXTDOMAIN ),
            'value'       => $status_description,
            'disabled'    => $disabled,
          )
        ),
      ),
    );

    $fields = apply_filters( 'wpdk_users_fields_profile', $fields, $user );

    $layout = new WPDKUIControlsLayout( $fields );
    $layout->display();

    do_action( 'wpdk_users_show_user_profile', $user );
  }

  /**
   * This hook only triggers when a user is viewing their own profile page (not others).
   * If you want to apply your hook to ALL profile pages (including users other than the current one) then you also
   * need to use the edit_user_profile_update hook.
   *
   * @brief Personal options update hook
   *
   * @param int $id_user User id
   */
  public function personal_options_update( $id_user )
  {
    /* Same for other users, see below */
    $this->edit_user_profile_update( $id_user );
  }

  /**
   * Hooks immediately after the "Show toolbar..." option on profile page (if current user).
   * Any HTML output should take into account that this hook occurs within the "Personal Options" table element.
   *
   * @brief Personal options
   *
   * @param WP_User $user WordPress user object
   */
  public function personal_options( $user )
  {
    $message = __( 'This view <strong>is enhanced</strong> by wpXtreme and WPDK framework. Please, <strong><a href="#wpdk">have a look below</a></strong> for additional information.', WPDK_TEXTDOMAIN );
    $alert   = new WPDKTwitterBootstrapAlert( 'info', $message, WPDKTwitterBootstrapAlertType::INFORMATION );
    $alert->display();
  }

  /**
   * Hooks above the "Name" section of profile page. This is typically used for adding new fields to WordPress profile
   * pages.
   * This hook only triggers if a user is viewing their own profile page. There is no equivalent hook at this point for
   * injecting content onto the profile pages of non-current users.
   *
   * @brief Profile personal options
   *
   * @param WP_User $user WordPress user object
   */
  public function profile_personal_options( $user )
  {
    /* Nothing to do... for now */
  }

  // -----------------------------------------------------------------------------------------------------------------
  // User profile (other)
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * This hook only triggers when a user is viewing another users profile page (not their own).
   * If you want to apply your hook to ALL profile pages (including the current user) then you also need to use the
   * show_user_profile hook.
   *
   * @brief WP User Profile hook
   *
   * @param WP_User $user WordPress user object
   */
  public function edit_user_profile( $user )
  {
    /* At this moment display the same informations */
    $this->show_user_profile( $user );
  }

  /**
   * This hook only triggers when a user is viewing another user's profile page (not their own).
   * If you want to apply your hook to ALL profile pages (including the current user) then you also need to use
   * the personal_options_update hook.
   *
   * @brief WP Edit user profile update hook
   *
   * @param int $id_user User ID
   *
   * @return bool
   */
  public function edit_user_profile_update( $id_user )
  {
    if ( !current_user_can( 'edit_user' ) ) {
      return false;
    }

    /* Update the WPDK extra information */
    if ( current_user_can( 'manage_options' ) ) {
      WPDKUserMeta::update( $id_user, $_POST );
    }
    return true;
  }


  /**
   * The delete_user action/hook can be used to perform additional actions when a user is deleted.
   * For example, you can delete rows from custom tables created by a plugin.
   *
   * The hook passes one parameter: the user's ID.
   * This hook runs before a user is deleted.
   *
   * The hook deleted_user (notice the "ed") runs after a user is deleted.
   *
   * Choose the appropriate hook for your needs. If you need access to user meta or fields from the user table,
   * use delete_user. User's deleted from Network Site installs may not trigger this hook.
   * Be sure to use the wpmu_delete_user hook for those cases.
   *
   * @brief WP Delete user hook
   *
   * @param int $id_user User ID
   */
  public function delete_user( $id_user )
  {
    /* @todo Do implement */
  }

  /**
   * The deleted_user action/hook can be used to perform additional actions after a user is deleted.
   * For example, you can delete rows from custom tables created by a plugin.
   *
   * The hook passes one parameter: the user's ID.
   *
   * This hook runs after a user is deleted.
   * The hook delete_user (delete vs deleted) runs before a user is deleted.
   *
   * Choose the appropriate hook for your needs. If you need access to user meta or fields from the user table,
   * use delete_user.
   *
   * User's deleted from Network Site installs may not trigger this hook.
   * Be sure to use the wpmu_delete_user hook for those cases.
   *
   * @brief WP Deleted user hook
   *
   * @param int $id_user User ID
   */
  public function deleted_user( $id_user )
  {
    /* @todo Do implement */
  }

  /**
   * Not used yet because can't get a clean list of $contancts. Then is not possible to display a series of checkboxes
   * that indicate the fields not to show. For it, in fact, should I use just this filter or private
   * function / internal _wp_get_user_contactmethods ().
   *
   * @brief WP User contact methods hook
   * @todo  Not used yet
   *
   * @param array $contacts
   *
   * @return mixed
   */
  public function user_contactmethods( $contacts )
  {
    return $contacts;
  }


  /**
   * Return the User id with meta key and meta value.
   *
   * @brief Get user with meta key and value
   *
   * @param string $meta_key   Meta Key
   * @param string $meta_value Meta value
   *
   * @return int User ID or FALSE
   */
  public static function userWithMetaAndValue( $meta_key, $meta_value )
  {
    global $wpdb;

    $sql    = <<< SQL
SELECT user_id
FROM {$wpdb->usermeta}
WHERE meta_key = '{$meta_key}'
AND meta_value = '{$meta_value}'
SQL;
    $result = $wpdb->get_var( $sql );

    return $result;
  }

  /**
   * Return the users list with a capability
   *
   * @param string $find_caps Single capability
   *
   * @return array
   */
  public static function usersWithCaps( $find_caps )
  {
    $users_caps = WPDKCapabilities::getInstance()->usersCapability();

    $users = array();
    foreach ( $users_caps as $user_id => $caps ) {
      $keys = array_keys( $caps );
      if ( in_array( $find_caps, $keys ) ) {
        $users[] = $user_id;
      }
    }
    return $users;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // User info
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return the HTML markup of tag img with the user gravatar. FALSE otherwise.
   *
   * @brief Get html img tag from gravatar.com service
   *
   * @param int    $id_user Optional. User ID or null for current user
   * @param int    $size    Optional. Gravatar size. Default `40`
   * @param string $alt     Optional. Alternate string for alt attribute. Default user display name.
   * @param string $default Optional. Gravatar ID for default (not found) gravatar image, Default `wavatar`
   *
   * @return string|bool
   */
  public function gravatar( $id_user = null, $size = 40, $alt = '', $default = 'wavatar' )
  {
    if ( is_null( $id_user ) ) {
      $id_user = 0;
    }
    $user = new WPDKUser( $id_user );
    if ( $user ) {
      $alt  = empty( $alt ) ? $user->display_name : $alt;
      $src  = sprintf( 'http://www.gravatar.com/avatar/%s?s=%s&d=%s', md5( $user->email ), $size, $default );
      $html = sprintf( '<img src="%s" alt="%s" title="%s" />', $src, $alt, $alt );

      return $html;
    }
    return false;
  }

  /**
   * Return an instance of WPDKHTMLTagImg class
   *
   * @brief WPDKHTMLTagImg
   * @since 1.4.8
   *
   * @param int $id_user Optional. User ID or null for current user
   * @param int $size    Optional. Avatar size. Default `40`
   *
   * @return WPDKHTMLTagImg|bool
   */
  public function avatar( $id_user = null, $size = 40 )
  {
    if ( is_null( $id_user ) ) {
      $id_user = 0;
    }
    $user = new WPDKUser( $id_user );
    if ( $user ) {
      $alt = empty( $alt ) ? $user->display_name : $alt;

      $default = apply_filters( 'wpdk_default_avatar', 'wavatar' );

      $src = sprintf( 'http://www.gravatar.com/avatar/%s?s=%s&d=%s', md5( $user->email ), $size, $default );
      $img = new WPDKHTMLTagImg( $src, $alt, $size, $size );
      return $img;
    }
    return false;
  }



  // -----------------------------------------------------------------------------------------------------------------
  // Users list
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Restituisce un array in formato SDF con la lista degli utenti, formattata con 'display name (email)'
   *
   * @todo       Sicuramente da migliorare in quanto poco flessibile
   *
   * @deprecated Since 1.0.0.b4
   *
   * @return array
   */
  public function arrayUserForSDF()
  {
    $users      = array();
    $users_list = get_users();
    if ( $users_list ) {
      foreach ( $users_list as $user ) {
        $user_id         = '' . $user->ID;
        $users[$user_id] = sprintf( '%s (%s)', $user->display_name, $user->user_email );
      }
    }
    return $users;
  }


  // -----------------------------------------------------------------------------------------------------------------
  // DEPRECATED
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return a key value pairs array with ID => Description of role
   *
   * @brief      Return all WordPress roles
   *
   * @deprecated Use WPDKRoles instead
   *
   * @return array
   */
  public static function arrayRoles()
  {
    global $wp_roles;

    if ( !isset( $wp_roles ) ) {
      $wp_roles = new WP_Roles();
    }

    $result = array();

    $roles = $wp_roles->get_names();
    foreach ( $roles as $key => $role ) {
      $result[$key] = $role;
    }
    return $result;
  }
}


/**
 * An extend WP_Role class for Role model.
 *
 * ## Overview
 *
 * The WPDKRole is a new model for WordPress role object.
 *
 * @class              WPDKRole
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-02-26
 * @version            0.9.0
 *
 */
class WPDKRole extends WP_Role {

  /**
   * The display name of role
   *
   * @brief Display name
   *
   * @var string $displayName
   */
  public $displayName;

  /**
   * Extended description for this role. This extra information is stored in the special array into the wp_options
   * default database table.
   *
   * @brief Description
   *
   * @var string $description
   */
  public $description;

  /**
   * Who create this role
   *
   * @brief Owner
   *
   * @var $string $owner
   */
  public $owner;


  /**
   * Create an instance of WPDKRoles class.
   * If role already exists you get the role object, else you create (add) a new role. In this case you have
   * to set display_name, description and capabilities.
   *
   * @brief Construct
   *
   * @param string $role         Role key
   * @param string $display_name Optional. Role display name. This param is optional because you coul read an exists role
   * @param array  $capabilities Optional. List of any WPDKCapability or name of capability
   * @param string $description  Optional. The extended description of this role
   * @param string $owner        Optional. Owner of this role
   *
   * @return WPDKRole
   */
  public function __construct( $role, $display_name = '', $capabilities = array(), $description = '', $owner = '' )
  {

    /* Sanitize the role name. */
    $role_id = sanitize_title( strtolower( $role ) );

    /* Get Roles */
    $wpdk_roles  = WPDKRoles::getInstance();
    $role_object = $wpdk_roles->get_role( $role_id );

    /* If role not exists then create it. */
    if ( is_null( $role_object ) ) {
      /* If display name is empty and doesn't exists, then the display name is the role name-id */
      if ( empty( $display_name ) ) {
        $display_name = ucfirst( $role );
      }
      $role_object        = $wpdk_roles->add_role( $role_id, $display_name, $capabilities, $description, $owner );
      $this->displayName  = $display_name;
      $this->capabilities = $role_object->capabilities;
      $this->name         = $role_id;

      /* Extends */
      $this->description = $description;
      $this->owner       = $owner;
    }
    else {
      $this->name         = $role;
      $this->displayName  = $wpdk_roles->role_names[$role_id];
      $this->capabilities = $role_object->capabilities;

      /* Extends */
      $extra = get_option( WPDKRoles::OPTION_KEY );

      if ( !empty( $extra ) && isset( $extra[$role_id] ) ) {
        $this->description = $extra[$role][1];
        $this->owner       = $extra[$role][2];
      }
    }
  }

  /**
   * Update the extra role information
   *
   * @brief Update
   *
   * @return bool
   */
  public function update()
  {
    $extra = get_option( WPDKRoles::OPTION_KEY );
    if ( !empty( $extra ) ) {
      $extra[$this->name] = array(
        $this->displayName,
        $this->description,
        $this->owner
      );
    }
    else {
      $extra = WPDKRoles::init()->activeRoles;
    }
    return update_option( WPDKRoles::OPTION_KEY, $extra );
  }

}


/**
 * An extended version of WordPress WP_Roles model.
 *
 * @class              WPDKRoles
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-02-02
 * @version            1.0.0
 *
 */
class WPDKRoles extends WP_Roles {

  /**
   * The extra data are save in option table with this prefix
   *
   * @brief The option key prefix
   *
   */
  const OPTION_KEY = '_wpdk_roles_extends';

  // since 1.4.16 - WordPress has six pre-defined roles:
  const SUPER_ADMIN   = 'super-admin';
  const ADMINISTRATOR = 'administrator';
  const EDITOR        = 'editor';
  const AUTHOR        = 'author';
  const CONTRIBUTOR   = 'contributor';
  const SUBSCRIBER    = 'subscriber';

  /**
   * An array with all active roles
   *
   * @brief Active roles
   *
   * @var array $activeRoles
   */
  public $activeRoles;

  /**
   * An array with all inactive roles
   *
   * @brief Inactive roles
   *
   * @var array $inactiveRoles
   */
  public $inactiveRoles;

  /**
   * Default WordPress roles
   *
   * @brief WordPress
   *
   * @var array $wordPressRoles
   */
  public $wordPressRoles;

  /**
   * Number of roles
   *
   * @brief Counts of roles
   *
   * @var int $count ;
   */
  public $count;

  /**
   * List with count role group by user
   *
   * @brief Array with count for user
   *
   * @var array $arrayCountUsersByRole ;
   */
  public $arrayCountUsersByRole;

  /**
   * An key value pairs array with key = role and value = list of capabilities.
   *
   * @brief List of caps for role
   *
   * @var array $arrayCapabilitiesByRole
   */
  public $arrayCapabilitiesByRole;

  /**
   * A key value pairs array with role id for key and a key value pairs array for value.
   *
   * @brief Extended data for role
   *
   * @var array $_extendedData
   */
  private $_extendedData;

  /**
   * Singleton instance
   *
   * @brief Instance
   *
   * @var WPDKRoles $instance
   */
  private static $instance = null;

  /**
   * Create a singleton instance of WPDKRoles class
   *
   * @brief Get singleton instance
   * @note  This is an alias of getInstance() static method
   *
   * @return WPDKRoles
   */
  public static function init()
  {
    return self::getInstance();
  }

  /**
   * Create a singleton instance of WPDKRoles class
   *
   * @brief Get singleton instance
   *
   * @return WPDKRoles
   */
  public static function getInstance()
  {
    if ( is_null( self::$instance ) ) {
      self::$instance = new WPDKRoles();
    }
    return self::$instance;
  }

  /**
   * Used to invalidate static (internal singleton) and refresh all roles list
   *
   * @brief Invalidate
   *
   * @return WPDKRoles
   */
  public static function invalidate()
  {
    self::$instance = null;
    return self::getInstance();
  }


  /**
   * Create an instance of WPDKRoles class
   *
   * @brief Construct
   *
   * @note  This is a singleton class but for backward compatibility subclass this method can not private
   *
   * @return WPDKRoles
   *
   */
  public function __construct()
  {
    parent::__construct();

    /* Get the extended data. */
    $this->_extendedData = get_option( self::OPTION_KEY );

    if ( !empty( $this->role_names ) ) {
      $this->count = count( $this->role_names );
    }

    /* Init properties. */
    $this->wordPressRoles();
    $this->activeRoles();
    $this->inactiveRoles();
    $this->countUsersByRole();

    /* Create An key value pairs array with key = role and value = list of capabilities. */
    $this->arrayCapabilitiesByRole();

    if ( empty( $this->_extendedData ) ) {
      $this->_extendedData = array_merge( $this->activeRoles, $this->inactiveRoles, $this->wordPressRoles );
      update_option( self::OPTION_KEY, $this->_extendedData );
    }

  }

  // -----------------------------------------------------------------------------------------------------------------
  // Information
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Gets all the roles that have users for the site.
   *
   * @brief Active roles
   *
   * @return array
   */
  public function activeRoles()
  {

    /* Calculate only if the property if note set. */
    if ( !isset( $this->activeRoles ) ) {

      $this->activeRoles = array();
      foreach ( $this->role_names as $role => $name ) {
        $count = $this->countUsersByRole( $role );
        if ( !empty( $count ) ) {
          $this->activeRoles[$role] = isset( $this->_extendedData[$role] ) ? $this->_extendedData[$role] : array( $name, '', '' );
        }
      }
    }
    $this->activeRoles = apply_filters( 'wpdk_roles_active', $this->activeRoles );
    return $this->activeRoles;
  }

  /**
   * Gets all the roles that do not have users for the site.
   *
   * @brief Inactive roles
   *
   * @return array
   */
  public function inactiveRoles()
  {

    /* Calculate only if the property if note set. */
    if ( !isset( $this->inactiveRoles ) ) {

      $this->inactiveRoles = array();
      foreach ( $this->role_names as $role => $name ) {
        $count = $this->countUsersByRole( $role );
        if ( empty( $count ) ) {
          $this->inactiveRoles[$role] = isset( $this->_extendedData[$role] ) ? $this->_extendedData[$role] : array( $name, '', '' );
        }
      }
    }

    $this->inactiveRoles = apply_filters( 'wpdk_roles_inactive', $this->inactiveRoles );
    return $this->inactiveRoles;
  }

  /**
   * Return the global or singular count for role.
   * Counts the number of users for all roles on the site and returns this as an array. If the $user_role is input,
   * the return value will be the count just for that particular role.
   *
   * @brief Counts the number of users for roles
   *
   * @param string $user_role Optional. The role to get the user count for.
   *
   * @return int
   */
  public function countUsersByRole( $user_role = '' )
  {

    /* If the count is not already set for all roles, let's get it. */
    if ( !isset( $this->arrayCountUsersByRole ) ) {

      $this->arrayCountUsersByRole = array();

      /*
       * Count users
       *
       * array(2) {
       *   ["total_users"]=> int(9)
       *   ["avail_roles"]=> array(4) {
       *     ["administrator"]=> int(6)
       *     ["author"]=> int(1)
       *     ["contributor"]=> int(1)
       *     ["subscriber"]=> int(1)
       *   }
       * }
       */
      $user_count = count_users();

      /* Loop through the user count by role to get a count of the users with each role. */
      foreach ( $user_count['avail_roles'] as $role => $count ) {
        $this->arrayCountUsersByRole[$role] = $count;
      }
    }

    /* If the $user_role parameter wasn't passed into this function, return the array of user counts. */
    if ( empty( $user_role ) ) {
      return $this->arrayCountUsersByRole;
    }

    /* If the role has no users, we need to set it to '0'. */
    if ( !isset( $this->arrayCountUsersByRole[$user_role] ) ) {
      $this->arrayCountUsersByRole[$user_role] = 0;
    }

    /* Return the user count for the given role. */
    return $this->arrayCountUsersByRole[$user_role];
  }

  /**
   * Create An key value pairs array with key = role and value = list of capabilities.
   *
   * @brief List of caps for role
   *
   * @return array
   */
  public function arrayCapabilitiesByRole()
  {

    /* If the count is not already set for all roles, let's get it. */
    if ( !isset( $this->arrayCapabilitiesByRole ) ) {

      foreach ( $this->get_names() as $role => $name ) {

        /* Count capabilities for role too. */
        $wp_role = $this->get_role( $role );
        ksort( $wp_role->capabilities );
        $this->arrayCapabilitiesByRole[$role] = $wp_role->capabilities;
      }
    }
    return $this->arrayCapabilitiesByRole;
  }

  /**
   * Return TRUE if the role exists
   *
   * @brief Check if a role exists
   *
   * @param string $role Role key name
   *
   * @return bool
   */
  public function roleExists( $role )
  {
    return ( array_key_exists( $role, $this->role_names ) );
  }

  /**
   * Return a key value pairs array with name of role and extra info.
   *
   * @brief WordPress default roles
   *
   * @return mixed|void
   */
  public function wordPressRoles()
  {
    $this->wordPressRoles = array(
      'administrator' => array( 'Administrator', __( 'Somebody who has access to all the administration features', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'editor'        => array( 'Editor', __( 'Somebody who can publish and manage posts and pages as well as manage other users posts, etc.', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'author'        => array( 'Author', __( 'Somebody who can publish and manage their own posts', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'contributor'   => array( 'Contributor', __( 'Somebody who can write and manage their posts but not publish them', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'subscriber'    => array( 'Subscriber', __( 'Somebody who can only manage their profile', WPDK_TEXTDOMAIN ), 'WordPress' ),
    );

    $this->wordPressRoles = apply_filters( 'wpdk_roles_defaults', $this->wordPressRoles );
    return $this->wordPressRoles;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Override
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Updates the list of roles, if the role doesn't already exist.
   *
   * The capabilities are defined in the following format `array( 'read' => true );`
   * To explicitly deny a role a capability you set the value for that capability to false.
   *
   * In this overide method you can extends the capapilities array with format
   * `array( 'read' => true, 'description' => 'Thsi capability allow access to...' );`
   *
   * @brief Add role name with capabilities to list.
   *
   * @param string $role         Role name.
   * @param string $display_name Role display name.
   * @param array  $capabilities Optional. List of role capabilities in the above format.
   * @param string $description  Optional. An extend description for this role.
   * @param string $owner        Optional. Owner of this role
   *
   * @note  This method override the WP_Roles method to extend
   *
   * @return null|WP_Role
   */
  public function add_role( $role, $display_name, $capabilities = array(), $description = '', $owner = '' )
  {
    /* Normalize caps */
    $caps = array();
    foreach ( $capabilities as $cap ) {
      $caps[$cap] = true;
    }

    $role_object = parent::add_role( $role, $display_name, $caps );
    if ( !is_null( $role_object ) ) {
      if ( !isset( $this->_extendedData[$role] ) ) {
        $this->_extendedData[$role] = array(
          $display_name,
          $description,
          $owner
        );
      }
      update_option( self::OPTION_KEY, $this->_extendedData );
    }
    return $role_object;
  }

  /**
   * Remove a role
   *
   * @brief Role
   *
   * @param string $role
   */
  public function remove_role( $role )
  {
    parent::remove_role( $role );
    unset( $this->_extendedData[$role] );
    update_option( self::OPTION_KEY, $this->_extendedData );
  }


  // -----------------------------------------------------------------------------------------------------------------
  // UI
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return the HTML markup for a combo select
   *
   * @param string|WPDKRole $role Role
   *
   * @return string
   */
  public function selectCapabilitiesWithRole( $role )
  {

    if ( is_object( $role ) && is_a( $role, 'WPDKRole' ) ) {
      $role = $role->name;
    }

    ob_start(); ?>

    <select>
    <?php foreach ( $this->arrayCapabilitiesByRole[$role] as $cap => $enabled ): ?>
      <option><?php echo $cap ?></option>
    <?php endforeach ?>
  </select>

    <?php
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }

}


/**
 * A capabilities class model.
 *
 * ## Overview
 *
 * You have not confuse the property as `roleCapabilities` by the method `roleCapabilities()`. The properties are a
 * linear array such `array( cap1, cap2, ..., capn)`.
 * The method instead return a key values pair array such `array( cap1 => array( cap1, desc, owner), ... )`.
 * The method return the extend information, the property only the name.
 *
 *
 * @class              WPDKCapabilities
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 */
class WPDKCapabilities {

  /**
   * The extra data are save in option table with this prefix
   *
   * @brief The option key prefix
   *
   */
  const OPTION_KEY = '_wpdk_capabilities_extends';

  /**
   * All capabilities in all roles
   *
   * @brief All capabilities
   *
   * @var array $roleCapabilities
   */
  public $roleCapabilities;

  /**
   * The standard WordPress capabilities list
   *
   * @brief Only WordPress capabilities
   *
   * @var array $defaultCapabilities
   */
  public $defaultCapabilities;

  /**
   * This is the capabilities list without WordPress capabilties
   *
   * @brief Only non-wordpress capabilities
   *
   * @var array $capabilities
   */
  public $capabilities;

  /**
   * The list of capabilities added to users
   *
   * @brief User capabiltity
   *
   * @var array $userCapabilities
   */
  public $userCapabilities;

  /**
   * The list of all capabilities
   *
   * @brief All capabilities
   *
   * @var array $allCapabilities
   */
  public $allCapabilities;


  /**
   * A key value pairs array with capability id for key and a key value pairs array for value.
   *
   * @brief Extended data for capability
   *
   * @var array $_extendedData
   */
  private $_extendedData;

  /**
   * Return a singleton instance of WPDKCapabilities class
   *
   * @brief Singleton instance of WPDKCapabilities
   *
   * @return WPDKCapabilities
   */
  public static function getInstance()
  {
    static $instance = null;
    if ( is_null( $instance ) ) {
      $instance = new WPDKCapabilities();
    }
    return $instance;
  }

  /**
   * Create an instance of WPDKCapabilities class
   *
   * @brief Construct
   *
   * @return WPDKCapabilities
   */
  private function __construct()
  {

    /* Get the extended data. */
    $this->_extendedData = get_option( self::OPTION_KEY );

    /* Preset WordPress capabilities list. */
    $this->defaultCapabilities = array_keys( $this->defaultCapabilities() );

    /* Get the role capabilities. */
    $this->roleCapabilities = array_keys( $this->roleCapabilities() );

    /* Get the role users. */
    $this->userCapabilities = array_keys( $this->userCapabilities() );

    /* Setup only plugin capabilities. */
    $this->capabilities = array_unique( array_merge( array_diff( $this->roleCapabilities, $this->defaultCapabilities ), $this->userCapabilities ) );
    sort( $this->capabilities );

    /* All caps */
    $this->allCapabilities = array_unique( array_merge( $this->userCapabilities, $this->roleCapabilities, $this->defaultCapabilities ) );
    sort( $this->allCapabilities );
  }

  /**
   * Return an instance of WPDKCapability class or false if not exists
   *
   * @brief Get a capability
   *
   * @param string $cap_id Capability id
   *
   * @return WPDKCapability
   */
  public function get_cap( $cap_id )
  {
    $cap = false;
    if ( isset( $this->allCapabilities[$cap_id] ) ) {
      $description = '';
      $owner       = '';
      if ( isset( $this->_extendedData[$cap_id] ) ) {
        list( $cap_id, $description, $owner ) = $this->_extendedData[$cap_id];
      }
      $cap = new WPDKCapability( $cap_id, $description, $owner );
    }
    return $cap;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Capabilities list
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return a key value pairs array with unique id key of capability and the description as value.
   * Make sure we keep the default capabilities in case users screw 'em up.  A user could easily remove a
   * useful WordPress capability from all roles.  When this happens, the capability is no longer stored in any of
   * the roles, so it basically doesn't exist.  This function will house all of the default WordPress capabilities in
   * case this scenario comes into play.
   *
   * For those reading this note, yes, I did "accidentally" remove all capabilities from my administrator account
   * when developing this plugin.  And yes, that was fun putting back together.
   *
   * @brief Get all standard WordPress capabilities list
   *
   * The Codex has a list of all the defaults: http://codex.wordpress.org/Roles_and_Capabilities#Capabilities
   *
   * @return array $defaults All the default WordPress capabilities.
   */
  public static function defaultCapabilities()
  {

    /* Create an array of all the default WordPress capabilities so the user doesn't accidentally get rid of them. */
    $defaults = array(
      'activate_plugins'       => array( 'activate_plugins', __( 'Allows access to Administration Panel options: Plugins', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'add_users'              => array( 'add_users', __( 'add_users', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'create_users'           => array( 'create_users', __( 'create_users', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'delete_others_pages'    => array( 'delete_others_pages', __( 'delete_others_pages', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'delete_others_posts'    => array( 'delete_others_posts', __( 'delete_others_posts', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'delete_pages'           => array( 'delete_pages', __( 'delete_pages', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'delete_plugins'         => array( 'delete_plugins', __( 'delete_plugins', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'delete_posts'           => array( 'delete_posts', __( 'delete_posts', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'delete_private_pages'   => array( 'delete_private_pages', __( 'delete_private_pages', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'delete_private_posts'   => array( 'delete_private_posts', __( 'delete_private_posts', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'delete_published_pages' => array( 'delete_published_pages', __( 'delete_published_pages', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'delete_published_posts' => array( 'delete_published_posts', __( 'delete_published_posts', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'delete_users'           => array( 'delete_users', __( 'delete_users', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_dashboard'         => array( 'edit_dashboard', __( 'edit_dashboard', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_files'             => array( 'edit_files', __( 'No longer used.', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_others_pages'      => array( 'edit_others_pages', __( 'edit_others_pages', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_others_posts'      => array( 'edit_others_posts', __( 'edit_others_posts', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_pages'             => array( 'edit_pages', __( 'edit_pages', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_plugins'           => array( 'edit_plugins', __( 'edit_plugins', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_posts'             => array( 'edit_posts', __( 'edit_posts', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_private_pages'     => array( 'edit_private_pages', __( 'edit_private_pages', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_private_posts'     => array( 'edit_private_posts', __( 'edit_private_posts', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_published_pages'   => array( 'edit_published_pages', __( 'edit_published_pages', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_published_posts'   => array( 'edit_published_posts', __( 'edit_published_posts', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_theme_options'     => array( 'edit_theme_options', __( 'Allows access to Administration Panel options: Appearance > Widgets, Appearance > Menus, Appearance > Theme Options if they are supported by the current theme, Appearance > Background, Appearance > Header ', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_themes'            => array( 'edit_themes', __( 'edit_themes', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_users'             => array( 'edit_users', __( 'Allows access to Administration Panel options: Users', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'import'                 => array( 'import', __( 'import', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'install_plugins'        => array( 'install_plugins', __( 'Allows access to Administration Panel options: Plugins > Add New ', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'install_themes'         => array( 'install_themes', __( 'Allows access to Administration Panel options: Appearance > Add New Themes', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'list_users'             => array( 'list_users', __( 'list_users', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'manage_categories'      => array( 'manage_categories', __( 'Allows access to Administration Panel options: Posts > Categories, Links > Categories', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'manage_links'           => array( 'manage_links', __( 'Allows access to Administration Panel options: Links Links > Add New', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'manage_options'         => array( 'manage_options', __( 'Allows access to Administration Panel options: Settings > General, Settings > Writing, Settings > Reading, Settings > Discussion, Settings > Permalinks, Settings > Miscellaneous', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'moderate_comments'      => array( 'moderate_comments', __( 'Allows users to moderate comments from the Comments SubPanel (although a user needs the edit_posts Capability in order to access this)', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'promote_users'          => array( 'promote_users', __( 'promote_users', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'publish_pages'          => array( 'publish_pages', __( 'publish_pages', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'publish_posts'          => array( 'publish_posts', __( 'See and use the "publish" button when editing their post (otherwise they can only save drafts). Can use XML-RPC to publish (otherwise they get a "Sorry, you can not post on this weblog or category.")', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'read'                   => array( 'read', __( 'Allows access to Administration Panel options: Dashboard, Users > Your Profile. Used nowhere in the core code except the menu.php', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'read_private_pages'     => array( 'read_private_pages', __( 'read_private_pages', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'read_private_posts'     => array( 'read_private_posts', __( 'read_private_posts', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'remove_users'           => array( 'remove_users', __( 'remove_users', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'switch_themes'          => array( 'switch_themes', __( 'Allows access to Administration Panel options: Appearance, Appearance > Themes ', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'unfiltered_html'        => array( 'unfiltered_html', __( 'Allows user to post HTML markup or even JavaScript code in pages, posts, and comments. Note: Enabling this option for untrusted users may result in their posting malicious or poorly formatted code. ', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'unfiltered_upload'      => array( 'unfiltered_upload', __( 'unfiltered_upload', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'update_core'            => array( 'update_core', __( 'update_core', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'update_plugins'         => array( 'update_plugins', __( 'update_plugins', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'update_themes'          => array( 'update_themes', __( 'update_themes', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'upload_files'           => array( 'upload_files', __( 'Allows access to Administration Panel options: Media, Media > Add New ', WPDK_TEXTDOMAIN ), 'WordPress' ),
    );

    /* Return the array of default capabilities. */
    return apply_filters( 'wpdk_capabilities_defaults', $defaults );
  }

  /**
   * Return a key value pairs array with unique id key of capability and the description as value.
   * Old WordPress levels system.  This is mostly useful for filtering out the levels when shown in admin
   * screen.  Plugins shouldn't rely on these levels to create permissions for users.  They should move to the
   * newer system of checking for a specific capability instead.
   *
   * @brief The old 'level' caps
   *
   * @return array Old user levels.
   */
  private static function oldLevels()
  {
    $old_levels = array(
      'level_0'  => array( 'level_0', '', 'WordPress' ),
      'level_1'  => array( 'level_1', '', 'WordPress' ),
      'level_2'  => array( 'level_2', '', 'WordPress' ),
      'level_3'  => array( 'level_3', '', 'WordPress' ),
      'level_4'  => array( 'level_4', '', 'WordPress' ),
      'level_5'  => array( 'level_5', '', 'WordPress' ),
      'level_6'  => array( 'level_6', '', 'WordPress' ),
      'level_7'  => array( 'level_7', '', 'WordPress' ),
      'level_8'  => array( 'level_8', '', 'WordPress' ),
      'level_9'  => array( 'level_9', '', 'WordPress' ),
      'level_10' => array( 'level_10', '', 'WordPress' )
    );
    return apply_filters( 'wpdk_capabilities_old_levels', $old_levels );
  }


  /**
   * Gets an array of capabilities according to each user role. Each role will return its caps, which are then
   * added to the overall $capabilities array.
   *
   * Note that if no role has the capability, it technically no longer exists. Since this could be a problem with
   * folks accidentally deleting the default WordPress capabilities, the members_default_capabilities() will
   * return all the defaults.
   *
   *     [cap] = [cap, desc, owner]
   *
   * @brief Get all role capabilities
   *
   * @return array $capabilities All the capabilities of all the user roles.
   */
  public function roleCapabilities()
  {

    /* Get WPDKRoles */
    $wpdk_roles = WPDKRoles::getInstance();

    /* Set up an empty capabilities array. */
    $capabilities = array();

    /* Loop through each role object because we need to get the caps. */
    foreach ( $wpdk_roles->role_objects as $key => $role ) {

      /* Roles without capabilities will cause an error, so we need to check if $role->capabilities is an array. */
      if ( is_array( $role->capabilities ) ) {

        /* Loop through the role's capabilities and add them to the $capabilities array. */
        $exclude = self::oldLevels();
        foreach ( $role->capabilities as $cap => $grant ) {
          if ( !isset( $exclude[$cap] ) ) {
            $capabilities[$cap] = isset( $this->_extendedData[$cap] ) ? $this->_extendedData[$cap] : array(
              $cap,
              '',
              ''
            );
          }
        }
      }
    }

    /* Sort the capabilities by name so they're easier to read when shown on the screen. */
    ksort( $capabilities );

    /* Return the capabilities array */
    return $capabilities;
  }

  /**
   * Return the capability added to user.
   *
   *     [cap] = [cap, desc, owner]
   *
   * @brief Capability users
   * @note  This method is very different by usersCapability()
   *
   * @return array
   */
  public function userCapabilities()
  {
    global $wpdb;

    //$capabilities = get_transient( '_wpdk_users_caps' );
    $capabilities = ''; // cache off for debug
    if ( empty( $capabilities ) ) {
      $sql    = "SELECT user_id, meta_value FROM `{$wpdb->usermeta}` WHERE meta_key = 'wp_capabilities'";
      $result = $wpdb->get_results( $sql, ARRAY_A );

      foreach ( $result as $user_cap ) {
        /* A cap is store with a bolean flah that here is ignored. */
        $temp = array_keys( unserialize( $user_cap['meta_value'] ) );
        foreach ( $temp as $key ) {
          $capabilities[$key] = isset( $this->_extendedData[$key] ) ? $this->_extendedData[$key] : array(
            $key,
            '',
            ''
          );
        }
      }
      //set_transient( '_wpdk_users_caps', $capabilities, 120 );
    }

    /* Sort the capabilities by name so they're easier to read when shown on the screen. */
    ksort( $capabilities );

    return $capabilities;

  }

  /**
   * Return a key value pairs array with all registered capabilities.
   *
   *     [cap] = [cap, desc, owner]
   *
   * @brief All capabilities
   *
   * @return array
   */
  public function allCapabilities()
  {
    $capabilities = $this->userCapabilities();
    $capabilities = array_merge( $capabilities, $this->roleCapabilities() );
    $capabilities = array_merge( $capabilities, $this->defaultCapabilities() );

    /* Sort the capabilities by name so they're easier to read when shown on the screen. */
    ksort( $capabilities );

    return $capabilities;
  }


  // -------------------------------------------------------------------------------------------------------------------
  // Extra
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return a key value pairs array. For each user the list of its capabilties.
   * This return array is used to hash from user id its capabilities.
   *
   *     [user_id] => [list of capabilities]
   *
   * @brief User caps
   * @note  This method is very different by userCapabilities()
   *
   * @return array
   */
  public function usersCapability()
  {
    global $wpdb;

    //$user_caps = get_transient( '_wpdk_users_caps' );
    $user_caps = false; // cache off for debug
    if ( empty( $user_caps ) ) {
      $sql    = "SELECT user_id, meta_value FROM `{$wpdb->usermeta}` WHERE meta_key = 'wp_capabilities'";
      $result = $wpdb->get_results( $sql, ARRAY_A );

      foreach ( $result as $user_cap ) {
        $user_caps[$user_cap['user_id']] = get_userdata( $user_cap['user_id'] )->allcaps;
      }

      //set_transient( '_wpdk_users_caps', $user_caps, 120 );
    }
    return $user_caps;

  }

}


/**
 * Single capability model. This class is useful to map a few instance of capability.
 *
 * @class           WPDKCapability
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-03-04
 * @version         1.0.0
 *
 */
class WPDKCapability {

  /**
   * Capability ID
   *
   * @brief Capability ID
   *
   * @var string $id
   */
  public $id;

  /**
   * Extend description
   *
   * @brief Description
   *
   * @var string $description
   */
  public $description;

  /**
   * Capability owner
   *
   * @brief Owner
   *
   * @var string $owner
   */
  public $owner;

  /**
   * Create an instance of WPDKCapability class
   *
   * @brief Construct
   *
   * @param string $id          Unique id of capability
   * @param string $description Optional. Capability description
   * @param string $owner       Optional. Capability owner, who create this capabilty
   *
   * @return WPDKCapability
   */
  public function __construct( $id, $description = '', $owner = '' )
  {
    $this->id          = $id;
    $this->description = $description;
    $this->owner       = $owner;

  }

  /**
   * Update in options the capability information
   *
   * @brief Update
   */
  public function update()
  {
    $extra = get_option( WPDKCapabilities::OPTION_KEY );
    if ( !empty( $extra ) ) {
      $extra[$this->id] = array(
        $this->id,
        $this->description,
        $this->owner
      );
    }
    else {
      $extra = WPDKCapabilities::getInstance()->capabilities;
    }
    return update_option( WPDKCapabilities::OPTION_KEY, $extra );
  }

}