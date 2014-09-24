<?php

/**
 * This class describe the WPDK user meta extra fields
 *
 * @class           WPDKUserMeta
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-08-25
 * @version         1.0.1
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

    // LAST_TIME_SUCCESS_LOGIN
    $value = isset( $post_data[ self::LAST_TIME_SUCCESS_LOGIN ] ) ? $post_data[ self::LAST_TIME_SUCCESS_LOGIN ] : '';
    if ( ! empty( $value ) ) {
      update_user_meta( $user_id, self::LAST_TIME_SUCCESS_LOGIN, $value );
    }

    // COUNT_SUCCESS_LOGIN
    $value = isset( $post_data[ self::COUNT_SUCCESS_LOGIN ] ) ? $post_data[ self::COUNT_SUCCESS_LOGIN ] : '';
    update_user_meta( $user_id, self::COUNT_SUCCESS_LOGIN, $value );

    // LAST_TIME_WRONG_LOGIN
    $value = isset( $post_data[ self::LAST_TIME_WRONG_LOGIN ] ) ? $post_data[ self::LAST_TIME_WRONG_LOGIN ] : '';
    if ( ! empty( $value ) ) {
      update_user_meta( $user_id, self::LAST_TIME_WRONG_LOGIN, $value );
    }

    // COUNT_WRONG_LOGIN
    $value = isset( $post_data[ self::COUNT_WRONG_LOGIN ] ) ? $post_data[ self::COUNT_WRONG_LOGIN ] : '';
    update_user_meta( $user_id, self::COUNT_WRONG_LOGIN, $value );

    // LAST_TIME_LOGOUT
    $value = isset( $post_data[ self::LAST_TIME_LOGOUT ] ) ? $post_data[ self::LAST_TIME_LOGOUT ] : '';
    if ( ! empty( $value ) ) {
      update_user_meta( $user_id, self::LAST_TIME_LOGOUT, $value );
    }

    // STATUS
    $value = isset( $post_data[ self::STATUS ] ) ? $post_data[ self::STATUS ] : '';

    // @since 1.5.17 - fixed status empty with delete
    if ( empty( $value ) ) {
      delete_user_meta( $user_id, self::STATUS );
    }
    else {
      update_user_meta( $user_id, self::STATUS, $value );
    }

    // STATUS_DESCRIPTION
    $value = isset( $post_data[ self::STATUS_DESCRIPTION ] ) ? $post_data[ self::STATUS_DESCRIPTION ] : '';
    update_user_meta( $user_id, self::STATUS_DESCRIPTION, $value );

  }

}


/**
 * User status model
 *
 * @class           WPDKUserMeta
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-04-29
 * @version         1.1.0
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
   * The user is cancelled
   *
   * @brief Cancelled
   * @since 1.5.5
   */
  const CANCELED = 'canceled';

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
      self::DISABLED => __( 'Disabled', WPDK_TEXTDOMAIN ),
      self::CANCELED => __( 'Canceled', WPDK_TEXTDOMAIN ),
    );

    /**
     * Filter the default WPDK user status list.
     *
     * @param array $statuses The key value pairs with status id => label.
     */
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
 * @date               2014-02-11
 * @version            1.0.1
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
   * Return an instance of WPDKUser class for the current user logged in or null if no user found/logged in
   *
   * @brief Return the current user
   *
   * @since 1.4.21
   *
   * @return WPDKUser
   */
  public static function current_user()
  {
    // Check for user logged in
    if ( ! is_user_logged_in() ) {
      return null;
    }

    return new self;
  }

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

    // Sanitize $id
    if ( is_numeric( $user ) ) {
      $id_user = $user;

      // If zero get the current id user
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

    // Get by email
    elseif ( is_string( $user ) && is_email( $user ) ) {
      $user    = get_user_by( 'email', $user );
      $id_user = $user->ID;
    }

    parent::__construct( $id_user, $name, $blog_id );

    // Set the extended property when an user is set
    if ( ! empty( $id_user ) ) {
      $this->first_name        = $this->get( 'first_name' );
      $this->last_name         = $this->get( 'last_name' );
      $this->nice_name         = $this->data->user_nicename;
      $this->full_name         = $this->full_name( $this->first_name, $this->last_name );
      $this->display_name      = $this->data->display_name;
      $this->email             = sanitize_email( $this->data->user_email );
      $this->status            = $this->get( WPDKUserMeta::STATUS );
      $this->statusDescription = $this->get( WPDKUserMeta::STATUS_DESCRIPTION );

      // Sanitize string->int
      $this->data->ID = absint( $id_user );
      $this->ID       = absint( $id_user );
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

  // -------------------------------------------------------------------------------------------------------------------
  // User transient
  // -------------------------------------------------------------------------------------------------------------------

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
    if ( ! empty( $this->ID ) ) {
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
    if ( ! empty( $this->ID ) ) {
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
    if ( ! empty( $this->ID ) ) {
      self::setTransientWithUser( $transient, $value, $expiration, $this->ID );
    }
  }

  /**
   * Delete a user transient. Return TRUE if successful, FALSE otherwise
   *
   * @brief Delete
   * @since 1.5.1
   *
   * @uses  do_action() Calls 'delete_user_transient_$transient' hook before transient is deleted.
   * @uses  do_action() Calls 'deleted_user_transient' hook on success.
   *
   * @param string $transient Transient name. Expected to not be SQL-escaped.
   * @param int    $user_id   Optional. User ID. If null the current user id is used instead
   *
   * @return bool
   */
  public static function deleteTransientWithUser( $transient, $user_id = null )
  {
    $user_id = is_null( $user_id ) ? get_current_user_id() : $user_id;

    do_action( 'delete_user_transient_' . $transient, $transient, $user_id );

    $transient_timeout = '_transient_timeout_' . $transient;
    $transient         = '_transient_' . $transient;
    $result            = delete_user_meta( $user_id, $transient );
    if ( $result ) {
      delete_user_meta( $user_id, $transient_timeout );
      do_action( 'deleted_user_transient', $transient, $user_id );
    }

    return $result;
  }

  /**
   * Delete a user transient for this WPDKUser object instance. Return TRUE if successful, FALSE otherwise
   *
   * @brief Delete
   * @since 1.5.1
   *
   * @uses  do_action() Calls 'delete_user_transient_$transient' hook before transient is deleted.
   * @uses  do_action() Calls 'deleted_user_transient' hook on success.
   *
   * @param string $transient Transient name. Expected to not be SQL-escaped.
   * @param int    $user_id   Optional. User ID. If null the current user id is used instead
   *
   * @return bool
   */
  public function deleteTransient( $transient )
  {
    if ( ! empty( $this->ID ) ) {
      self::deleteTransientWithUser( $transient, $this->ID );
    }
  }



  // -------------------------------------------------------------------------------------------------------------------
  // User info
  // -------------------------------------------------------------------------------------------------------------------

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

    if ( ! empty( $birthday ) ) {
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

  // -------------------------------------------------------------------------------------------------------------------
  // Roles
  // -------------------------------------------------------------------------------------------------------------------

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

    if ( ! isset( $wp_roles ) ) {
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
    if ( ! empty( $user ) ) {
      $role_key = $user->roles[ key( $user->roles ) ];
      if ( ! empty( $role_key ) ) {
        return $wp_roles->roles[ $role_key ]['name'];
      }
    }

    return false;
  }

  /**
   * Set the role of the user. Perform a cache clear for this user.
   *
   * This will remove the previous roles of the user and assign the user the
   * new one. You can set the role to an empty string and it will remove all
   * of the roles from the user.
   *
   * @since 1.5.6
   *
   * @param string $role Role name.
   */
  public function set_role( $role )
  {
    // Before remove all previous
    $this->remove_all_caps();

    parent::set_role( $role );

    // Flush user cache
    wp_cache_delete( $this->ID, 'users' );

    // Destroy the global
    global $current_user;
    unset( $current_user );
    unset( $GLOBALS['current_user'] );

    // Reset as current
    wp_set_current_user( $this->ID );
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
        if ( isset( $all_caps[ $cap ] ) ) {
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
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-09-16
 * @version            1.2.1
 *
 * @history            1.2.1 - Added `deleteUsersMetaWithKey()` static method
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
      $instance = new self();
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

    // Force an user logout when disabled or if in GET you pass wpdk_logout
    add_action( 'init', array( $this, 'logout' ) );

    // Main hook for common check in front end
    //add_action( 'wp_head', array( $this, 'wp_head_signin' ) );
    //add_action( 'wp_head', array( $this, 'wp_head_signout' ) );

    // Fires after the user has successfully logged in.
    add_action( 'wp_login', array( $this, 'wp_login' ) );

    // Fires after a user is logged-out.
    add_action( 'wp_logout', array( $this, 'wp_logout' ) );

    // Fires after a user login has failed.
    add_action( 'wp_login_failed', array( $this, 'wp_login_failed' ) );

    // Fires immediately after a new user is registered.
    //add_action( 'user_register', array( $this, 'user_register' ) );

    // Fires immediately before a user is deleted from the database.
    //add_action( 'delete_user', array( $this, 'delete_user' ) );

    // Fires immediately after a user is deleted from the database.
    //add_action( 'deleted_user', array( $this, 'deleted_user' ) );

    // Fires after the 'About Yourself' settings table on the 'Your Profile' editing screen.
    add_action( 'show_user_profile', array( $this, 'show_user_profile' ) );

    // Fires before the page loads on the 'Your Profile' editing screen.
    add_action( 'personal_options_update', array( $this, 'personal_options_update' ) );

    // Fires at the end of the 'Personal Options' settings table on the user editing screen.
    add_action( 'personal_options', array( $this, 'personal_options' ) );

    // Fires after the 'Personal Options' settings table on the 'Your Profile' editing screen.
    //add_action( 'profile_personal_options', array( $this, 'profile_personal_options' ) );

    // Fires after the 'About the User' settings table on the 'Edit User' screen.
    add_action( 'edit_user_profile', array( $this, 'edit_user_profile' ) );

    // Fires before the page loads on the 'Edit User' screen.
    add_action( 'edit_user_profile_update', array( $this, 'edit_user_profile_update' ) );

    // Extends User edit profile

    // Filter whether the given user can be authenticated with the provided $password.
    add_filter( 'wp_authenticate_user', array( $this, 'wp_authenticate_user' ), 1 );

    // Filter the user contact methods.
    //add_filter( 'user_contactmethods', array( $this, 'user_contactmethods' ) );
  }

  /**
   * Force an user logout when disabled or if in GET you pass wpdk_logout.
   *
   * @brief Logout an user
   */
  public function logout()
  {
    // If a user is logged in
    if ( is_user_logged_in() ) {
      $user_id = get_current_user_id();
      $status  = get_user_meta( $user_id, WPDKUserMeta::STATUS, true );
      $logout  = false;

      // Logout for disabled User
      if ( ! empty( $status ) && in_array( $status, array( WPDKUserStatus::DISABLED, WPDKUserStatus::CANCELED ) ) ) {
        $logout = true;
      }

      // Manual logout
      if ( isset( $_REQUEST['wpdk_logout'] ) ) {
        $logout = true;
      }

      if ( true === $logout ) {

        // Log off the user
        wp_logout();
      }
    }
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Signin actions and filters
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Filter whether the given user can be authenticated with the provided $password.
   *
   * @since WP 2.5.0
   *
   * @param WP_User|WP_Error $user     WP_User or WP_Error object if a previous
   *                                   callback failed authentication.
   * @param string           $password Password to check against the user.
   */
  public function wp_authenticate_user( $user )
  {
    if ( is_wp_error( $user ) ) {
      return $user;
    }

    // Get the user status
    $status = $user->get( WPDKUserMeta::STATUS );

    if ( in_array( $status, array( WPDKUserStatus::DISABLED, WPDKUserStatus::CANCELED ) ) ) {

      /**
       * Filter the DENIED procedure when a user can't login.
       *
       * @param bool   $continue Default TRUE. Set to FALSE to bypass the ACCESS DENIED.
       * @param int    $user_id  The user id.
       * @param string $status   The current user status.
       */
      $continue = apply_filters( 'wpdk_users_should_denied_signin', true, $user->ID, $status );

      if ( $continue ) {
        // Get the status description
        $message = $user->get( WPDKUserMeta::STATUS_DESCRIPTION );

        /**
         * Filter the status description; because the user can't login.
         *
         * @param string $message The description of ACCESS DENIED.
         * @param int    $user_id The user id.
         * @param string $status  The current user status.
         */
        $message = apply_filters( 'wpdk_users_access_denied_status_description', $message, $user->ID, $status );

        return new WP_Error( 'wpdk_users_access_denied', $message, array( $user, $status ) );
      }
    }

    return $user;
  }

  /**
 	 * Fires after the user has successfully logged in.
 	 *
 	 * @since WP 1.5.0
 	 *
 	 * @param string  $user_login Username.
 	 * @param WP_User $user       WP_User object of the logged-in user.
 	 */
  public function wp_login( $user_login, $user = null )
  {

    // Get user by login
    if ( is_null( $user ) ) {
      $user = get_user_by( 'login', $user_login );
    }

    // Get success login count
    $count = absint( $user->get( WPDKUserMeta::COUNT_SUCCESS_LOGIN ) );

    if ( empty( $count ) ) {
      $count = 0;
    }

    update_user_meta( $user->ID, WPDKUserMeta::COUNT_SUCCESS_LOGIN, $count + 1 );
    update_user_meta( $user->ID, WPDKUserMeta::COUNT_WRONG_LOGIN, 0 );
    update_user_meta( $user->ID, WPDKUserMeta::LAST_TIME_SUCCESS_LOGIN, time() );
  }

  /**
   * Fires after a user login has failed.
   *
   * @since WP 2.5.0
   *
   * @param string $username User login.
   */
  public function wp_login_failed( $username )
  {

    if ( empty( $username ) ) {
      return false;
    }

    $user = get_user_by( 'login', $username );

    // Check if the user exists.
    if ( false === $user ) {
      return false;
    }

    $count = absint( $user->get( WPDKUserMeta::COUNT_WRONG_LOGIN ) );
    if ( empty( $count ) ) {
      $count = 0;
    }
    $count++;

    // Update the wrong login count
    update_user_meta( $user->ID, WPDKUserMeta::COUNT_WRONG_LOGIN, $count );

    /**
     * Fires when an user wrong login. This action is used to increment wrong login count.
     *
     * @param int $user_id The user id.
     * @param int $count   Current count of wrong login.
     */
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

    // Check user id
    if ( is_numeric( $user ) ) {
      $user_id = $user;
    }

    // Check for email
    elseif ( is_email( $user ) ) {

      // Sanitize email
      $email = sanitize_email( $user );

      // User exists with email
      $user_id = email_exists( $email );
    }

    // Check for user login
    else {
      $user = get_user_by( 'login', $user );
      if ( $user ) {
        $user_id = $user->ID;
      }
    }

    if ( false !== $user_id ) {
      $user = new WPDKUser( $user_id );
      if ( $user->exists() && ! in_array( $user->status, array( WPDKUserStatus::DISABLED, WPDKUserStatus::CANCELED ) )
      ) {

        // Check access
        $result = wp_authenticate( $user->user_login, $password );

        if ( ! is_wp_error( $result ) ) {

          // Clear the cookie
          wp_clear_auth_cookie();

          // Set remember cookie
          wp_set_auth_cookie( $user->ID, $remember );
          do_action( 'wp_login', $user->user_login, $user );

          // Internal counter
          $this->wp_login( $user->user_login, $user );

          // Authenticate! You are
          wp_set_current_user( $user->ID );

          return true;
        }
      }
    }

    /**
     * Fires when a signin failure.
     *
     * @param int|string User      email OR user id.
     * @param string     $password Password.
     */
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
    if ( ! empty( $id_user ) ) {
      $user   = new WPDKUser( $id_user );
      $access = wp_authenticate( $user->data->user_login, $password );
      if ( ! is_wp_error( $access ) ) {
        return $id_user;
      }
    }

    return false;
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Signout actions and filters
  // -------------------------------------------------------------------------------------------------------------------

  /**
 	 * Fires after a user is logged-out.
 	 *
 	 * @since WP 1.5.0
 	 */
  public function wp_logout()
  {
    $user_id = get_current_user_id();
    if ( ! empty( $user_id ) ) {
      update_user_meta( $user_id, WPDKUserMeta::LAST_TIME_LOGOUT, time() );
    }
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Signout utility
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Perform signout. This method is an alias of `wp_logout()`
   *
   * @brief Do signout
   */
  public function signout()
  {
    // Do WordPress logout
    wp_logout();
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Create utility
  // -------------------------------------------------------------------------------------------------------------------

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

    // For security reason an user must have a password
    if ( false === $password ) {
      $password = WPDKCrypt::randomAlphaNumber();

      /**
       * Fires when a random password is generated.
       *
       * @param string $password Random Password.
       */
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

    // Store IP Address
    if ( isset( $_SERVER['REMOTE_ADDR'] ) && ! empty( $_SERVER['REMOTE_ADDR'] ) ) {
      update_user_meta( $user_id, WPDKUserMeta::REMOTE_ADDR, $_SERVER['REMOTE_ADDR'] );
    }

    // Disable user if required
    if ( false === $enabled ) {

      /**
       * Filter the user status before update.
       *
       * @param string $status  The status id. Default `WPDKUserStatus::DISABLED`.
       * @param int    $user_id The user id.
       */
      $status = apply_filters( 'wpdk_users_status', WPDKUserStatus::DISABLED, $user_id );
      update_user_meta( $user_id, WPDKUserMeta::STATUS, $status );

      $status_description = apply_filters( 'wpdk_users_status_description', '', $user_id );
      update_user_meta( $user_id, WPDKUserMeta::STATUS_DESCRIPTION, $status_description );
    }

    return $user_id;
  }


  // -------------------------------------------------------------------------------------------------------------------
  // User profile (own)
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Fires after the 'About Yourself' settings table on the 'Your Profile' editing screen.
   *
   * The action only fires if the current user is editing their own profile.
   *
   * @since WP 2.0.0
   *
   * @param WP_User $profileuser The current WP_User object.
   */
  public function show_user_profile( $profileuser )
  {

    echo '<br clear="all" /><a name="wpdk"></a>';

    $message                  = __( 'This view <strong>is enhanced</strong> by wpXtreme and WPDK framework. Please, have a look below for additional information.', WPDK_TEXTDOMAIN );
    $alert                    = new WPDKUIAlert( 'wpdk-alert-show_user_profile', $message, WPDKUIAlertType::INFORMATION );
    $alert->dismissPermanent  = true;
    $alert->display();

    // Only the administrator can edit this extra information
    $disabled = ! current_user_can( 'manage_options' );

    // Sanitize values
    $last_time_success_login = $profileuser->get( WPDKUserMeta::LAST_TIME_SUCCESS_LOGIN );
    $last_time_wrong_login   = $profileuser->get( WPDKUserMeta::LAST_TIME_WRONG_LOGIN );
    $last_time_logout        = $profileuser->get( WPDKUserMeta::LAST_TIME_LOGOUT );
    $status_description      = $profileuser->get( WPDKUserMeta::STATUS_DESCRIPTION );

    $fields = array(
      __( 'Login information', WPDK_TEXTDOMAIN )  => array(
        array(
          array(
            'type'     => WPDKUIControlType::DATETIME,
            'name'     => WPDKUserMeta::LAST_TIME_SUCCESS_LOGIN,
            'label'    => __( 'Last success login', WPDK_TEXTDOMAIN ),
            'value'    => empty( $last_time_success_login ) ? '' : WPDKDateTime::format( $last_time_success_login, WPDKDateTime::DATETIME_SECONDS_LESS_FORMAT_PHP ),
            'disabled' => $disabled
          ),
          array(
            'type'     => WPDKUIControlType::NUMBER,
            'name'     => WPDKUserMeta::COUNT_SUCCESS_LOGIN,
            'label'    => __( '# success login', WPDK_TEXTDOMAIN ),
            'value'    => $profileuser->get( WPDKUserMeta::COUNT_SUCCESS_LOGIN ),
            'disabled' => true
          ),
        ),
        array(
          array(
            'type'     => WPDKUIControlType::DATETIME,
            'name'     => WPDKUserMeta::LAST_TIME_WRONG_LOGIN,
            'label'    => __( 'Last wrong login', WPDK_TEXTDOMAIN ),
            'value'    => empty( $last_time_wrong_login ) ? '' : WPDKDateTime::format( $last_time_wrong_login, WPDKDateTime::DATETIME_SECONDS_LESS_FORMAT_PHP ),
            'disabled' => $disabled
          ),
          array(
            'type'     => WPDKUIControlType::NUMBER,
            'name'     => WPDKUserMeta::COUNT_WRONG_LOGIN,
            'label'    => __( '# wrong login', WPDK_TEXTDOMAIN ),
            'value'    => $profileuser->get( WPDKUserMeta::COUNT_WRONG_LOGIN ),
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
            'value'    => empty( $last_time_logout ) ? '' : WPDKDateTime::format( $last_time_logout, WPDKDateTime::DATETIME_SECONDS_LESS_FORMAT_PHP ),
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
            'value'    => $profileuser->get( WPDKUserMeta::STATUS ),
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

    /**
     * Filter the layout control array with the extra WPDK fields.
     *
     * You can use this filter or `wpdk_users_show_user_profile` action to modify the default layout control array.
     *
     * @param array   $fields      Layout array fields.
     * @param WP_User $profileuser The current WP_User object.
     */
    $fields = apply_filters( 'wpdk_users_fields_profile', $fields, $profileuser );

    $layout = new WPDKUIControlsLayout( $fields );
    $layout->display();

    /**
     * Fires after display the layout controls.
     *
     * You can use this action or `wpdk_users_fields_profile` filter to modify the default layout control array.
     *
     * @param WP_User $profileuser The current WP_User object.
     */
    do_action( 'wpdk_users_show_user_profile', $profileuser );
  }

  /**
 	 * Fires before the page loads on the 'Your Profile' editing screen.
 	 *
 	 * The action only fires if the current user is editing their own profile.
 	 *
 	 * @since WP 2.0.0
 	 *
 	 * @param int $user_id The user ID.
 	 */
  public function personal_options_update( $user_id )
  {
    // Same for other users, see below
    $this->edit_user_profile_update( $user_id );
  }

  /**
   * Fires at the end of the 'Personal Options' settings table on the user editing screen.
   *
   * @since WP 2.7.0
   *
   * @param WP_User $profileuser The current WP_User object.
   */
  public function personal_options( $profileuser )
  {
    $message                 = __( 'This view <strong>is enhanced</strong> by wpXtreme and WPDK framework. Please, <strong><a href="#wpdk">have a look below</a></strong> for additional information.', WPDK_TEXTDOMAIN );
    $alert                   = new WPDKUIAlert( 'wpdk-alert-personal_options', $message, WPDKUIAlertType::INFORMATION );
    $alert->dismissPermanent = true;
    $alert->display();
  }

  /**
   * Fires after the 'Personal Options' settings table on the 'Your Profile' editing screen.
   *
   * The action only fires if the current user is editing their own profile.
   *
   * @since 2.0.0
   *
   * @param WP_User $profileuser The current WP_User object.
   */
  public function profile_personal_options( $profileuser )
  {
    // Nothing to do... for now
  }

  // -------------------------------------------------------------------------------------------------------------------
  // User profile (other)
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Fires after the 'About the User' settings table on the 'Edit User' screen.
   *
   * @since WP 2.0.0
   *
   * @param WP_User $profileuser The current WP_User object.
   */
  public function edit_user_profile( $profileuser )
  {
    // At this moment display the same informations
    $this->show_user_profile( $profileuser );
  }

  /**
 	 * Fires before the page loads on the 'Edit User' screen.
 	 *
 	 * @since WP 2.7.0
 	 *
 	 * @param int $user_id The user ID.
 	 */
  public function edit_user_profile_update( $user_id )
  {
    if ( ! current_user_can( 'edit_user' ) ) {
      return false;
    }

    // Update the WPDK extra information
    if ( current_user_can( 'manage_options' ) ) {
      WPDKUserMeta::update( $user_id, $_POST );
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
    // @todo Do implement
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
    // @todo Do implement
  }

  /**
   * Filter the user contact methods.
   *
   * @since WP 2.9.0
   *
   * @param array   $methods Array of contact methods and their labels.
   * @param WP_User $user    WP_User object.
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
    /**
     * @var wpdb $wpdb
     */
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
    $users_caps = WPDKCapabilities::init()->usersCapability();

    $users = array();
    foreach ( $users_caps as $user_id => $caps ) {
      $keys = array_keys( $caps );
      if ( in_array( $find_caps, $keys ) ) {
        $users[] = $user_id;
      }
    }

    return $users;
  }

  // -------------------------------------------------------------------------------------------------------------------
  // User info
  // -------------------------------------------------------------------------------------------------------------------

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

      // Alt image
      $alt = empty( $alt ) ? $user->display_name : $alt;

      return get_avatar( $user->ID, $size, $default, $alt );
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

      /**
       * Filter the default gravatar string.
       *
       * @param string $default Gravater default id string.
       */
      $default = apply_filters( 'wpdk_default_avatar', 'wavatar' );

      // Use SSL
      $gravatar = is_ssl() ? 'https://secure.gravatar.com/avatar/' : 'http://0.gravatar.com/avatar/';

      $src = sprintf( '%s%s?s=%s&d=%s', $gravatar, md5( $user->email ), $size, $default );
      $img = new WPDKHTMLTagImg( $src, $alt, $size, $size );

      return $img;
    }

    return false;
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Users Meta
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Delete the user meta with $key from all users.
   *
   * @brief Delete meta users
   * @since 1.5.16
   *
   * @param string $key Meta key to delete.
   */
  public static function deleteUsersMetaWithKey( $key )
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    // Stability
    if ( empty( $key ) || ! is_string( $key ) ) {
      return false;
    }

    $sql    = <<<SQL
DELETE FROM {$wpdb->usermeta}
WHERE meta_key = '{$key}'
SQL;
    $result = $wpdb->query( $sql );

  }

  // -------------------------------------------------------------------------------------------------------------------
  // Users list
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return an array layout format with user id => user display name. This method is useful when you use the WPDK
   * control layout array.
   *
   * @todo       To improve
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
        $user_id           = '' . $user->ID;
        $users[ $user_id ] = sprintf( '%s (%s)', $user->display_name, $user->user_email );
      }
    }

    return $users;
  }


  // -------------------------------------------------------------------------------------------------------------------
  // DEPRECATED
  // -------------------------------------------------------------------------------------------------------------------

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

    if ( ! isset( $wp_roles ) ) {
      $wp_roles = new WP_Roles();
    }

    $result = array();

    $roles = $wp_roles->get_names();
    foreach ( $roles as $key => $role ) {
      $result[ $key ] = $role;
    }

    return $result;
  }
}