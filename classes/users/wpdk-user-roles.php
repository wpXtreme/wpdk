<?php

/**
 * An extend WP_Role class for Role model.
 *
 * ## Overview
 *
 * The WPDKUserRole is a new model for WordPress role object.
 *
 * @class              WPDKUserRole
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-09-24
 * @version            1.0.0
 *
 * @since              1.5.18
 *
 */
class WPDKUserRole extends WP_Role {

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
   * Create an instance of WPDKUserRoles class.
   * If role already exists you get the role object, else you create (add) a new role. In this case you have
   * to set display_name, description and capabilities.
   *
   * @brief Construct
   *
   * @param string $role         Role key
   * @param string $display_name Optional. Role display name. This param is optional because you coul read an exists
   *                             role
   * @param array  $capabilities Optional. List of any WPDKCapability or name of capability
   * @param string $description  Optional. The extended description of this role
   * @param string $owner        Optional. Owner of this role
   *
   * @return WPDKUserRole
   */
  public function __construct( $role, $display_name = '', $capabilities = array(), $description = '', $owner = '' )
  {

    // Sanitize the role name
    $role_id = str_replace( '-', '_', sanitize_title( strtolower( $role ) ) );

    // Get Roles
    $wpdk_roles  = WPDKUserRoles::getInstance();
    $role_object = $wpdk_roles->get_role( $role_id );

    // If role not exists then create it
    if ( is_null( $role_object ) ) {

      // If display name is empty and doesn't exists, then the display name is the role name-id
      if ( empty( $display_name ) ) {
        $display_name = ucfirst( $role );
      }

      // Sanitize
      $display_name = str_replace( '-', ' ', $display_name );
      $display_name = str_replace( '_', ' ', $display_name );

      $role_object        = $wpdk_roles->add_role( $role_id, $display_name, $capabilities, $description, $owner );
      $this->displayName  = $display_name;
      $this->capabilities = $role_object->capabilities;
      $this->name         = $role_id;

      // Extends
      $this->description = $description;
      $this->owner       = $owner;
    }
    // Get the object (for return or update)
    else {
      $this->name         = $role_id;
      $this->displayName  = $wpdk_roles->role_names[ $role_id ];
      $this->capabilities = $role_object->capabilities;

      // Extends
      $extra = get_option( WPDKUserRoles::OPTION_KEY );

      if ( ! empty( $extra ) && isset( $extra[ $role_id ] ) ) {
        $this->description = $extra[ $role ][1];
        $this->owner       = $extra[ $role ][2];
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
    // WPDKUserRoles
    $wpdk_roles = WPDKUserRoles::getInstance();

    // Roles
    if ( isset( $wpdk_roles->roles[ $this->name ] ) ) {

      // Reset all capabilities
      $wpdk_roles->roles[ $this->name ]['capabilities'] = array();

      // Set new capabilities
      foreach ( $this->capabilities as $cap ) {
        $wpdk_roles->roles[ $this->name ]['capabilities'][ $cap ] = true;
      }

      // Updated
      if ( $wpdk_roles->use_db ) {

        update_option( $wpdk_roles->role_key, $wpdk_roles->roles );

        /**
         * Fires when the role is updated.
         *
         * @param string $role_key The role key.
         * @param array  $roles    The role capabilities array.
         */
        do_action( 'wpdk_user_role_update', $wpdk_roles->role_key, $wpdk_roles->roles );
      }

    }

    $extend = get_option( WPDKUserRoles::OPTION_KEY );

    // Stability - however $extend is never empty - see WPDKUserRoles constructor in /classes/users/wpdk-user-roles.php
    $extend = empty( $extend ) ? array() : $extend;

    $extend[ $this->name ] = array(
      $this->displayName,
      $this->description,
      $this->owner
    );

    $result = update_option( WPDKUserRoles::OPTION_KEY, $extend );

    /**
     * Fires when the role is updated.
     *
     * @param string $role_key The role key.
     * @param array  $roles    The role capabilities array.
     * @param array  $extend    The array extra (extends) data.
     */
    do_action( 'wpdk_user_role_extend_update', $wpdk_roles->role_key, $wpdk_roles->roles, $extend );

    return $result;
  }

}


/**
 * An extended version of WordPress WP_Roles model.
 *
 * @class              WPDKUserRoles
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-09-24
 * @version            1.0.0
 *
 * @since              1.5.18
 *
 */
class WPDKUserRoles extends WP_Roles {

  // The extra data are save in option table with this prefix
  const OPTION_KEY = '_wpdk_roles_extends';

  // since 1.4.16 - WordPress has six pre-defined roles:
  const SUPER_ADMIN   = 'super-admin';
  const ADMINISTRATOR = 'administrator';
  const EDITOR        = 'editor';
  const AUTHOR        = 'author';
  const CONTRIBUTOR   = 'contributor';
  const SUBSCRIBER    = 'subscriber';

  /**
   * Array with the list of all roles.
   *
   * @var array $all_roles
   */
  public $all_roles = array();

  /**
   * An array with all active roles
   *
   * @brief Active roles
   *
   * @var array $activeRoles
   */
  public $activeRoles = array();

  /**
   * An array with all inactive roles
   *
   * @brief Inactive roles
   *
   * @var array $inactiveRoles
   */
  public $inactiveRoles = array();

  /**
   * Default WordPress roles
   *
   * @brief WordPress
   *
   * @var array $wordPressRoles
   */
  public $wordPressRoles = array();

  /**
   * Number of roles
   *
   * @brief Counts of roles
   *
   * @var int $count
   */
  public $count = 0;

  /**
   * List with count role group by user
   *
   * @brief Array with count for user
   *
   * @var array $arrayCountUsersByRole ;
   */
  public $arrayCountUsersByRole = array();

  /**
   * An key value pairs array with key = role and value = list of capabilities.
   *
   * @brief List of caps for role
   *
   * @var array $arrayCapabilitiesByRole
   */
  public $arrayCapabilitiesByRole = array();

  /**
   * A key value pairs array with role id for key and a key value pairs array for value.
   *
   * @brief Extended data for role
   *
   * @var array $extend_data
   */
  private $extend_data = array();

  /**
   * Singleton instance
   *
   * @brief Instance
   *
   * @var WPDKUserRoles $instance
   */
  private static $instance = null;

  /**
   * Create a singleton instance of WPDKUserRoles class
   *
   * @brief Get singleton instance
   * @note  This is an alias of getInstance() static method
   *
   * @return WPDKUserRoles
   */
  public static function init()
  {
    if ( is_null( self::$instance ) ) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  /**
   * Create a singleton instance of WPDKUserRoles class
   *
   * @brief Get singleton instance
   *
   * @return WPDKUserRoles
   */
  public static function getInstance()
  {
    return self::init();
  }

  /**
   * Create a singleton instance of WPDKUserRoles class
   *
   * @brief Get singleton instance
   *
   * @return WPDKUserRoles
   */
  public static function get_instance()
  {
    return self::init();
  }

  /**
   * Used to invalidate static (internal singleton) and refresh all roles list
   *
   * @brief Invalidate
   *
   * @return WPDKUserRoles
   */
  public static function invalidate()
  {

    self::$instance = null;

    return self::get_instance();
  }


  /**
   * Create an instance of WPDKUserRoles class
   *
   * @brief Construct
   *
   * @note  This is a singleton class but for backward compatibility subclass this method can not private
   *
   * @return WPDKUserRoles
   *
   */
  public function __construct()
  {
    parent::__construct();

    // WPXtreme::caller();

    // Get the extended data
    $this->extend_data = get_option( self::OPTION_KEY );

    if ( ! empty( $this->role_names ) ) {
      $this->count = count( $this->role_names );
    }

    // Init the `wordPressRoles` property with the list of WordPress default roles
    $this->wordPressRoles();

    // Init the `arrayCountUsersByRole` property with the count user by role
    $this->countUsersByRole();

    // Init the `activeRoles` and `inactiveRoles` properties with the list of used and unused role
    $this->statusRoles();

    // Init the `arrayCapabilitiesByRole` property with key = role and value = list of capabilities
    $this->arrayCapabilitiesByRole();

    // Init `all_roles` property with the list of all roles
    $this->all_roles = array_merge( $this->activeRoles, $this->inactiveRoles, $this->wordPressRoles );

    /*
     * $this->all_roles
     *
     *     array(13) {
     *      ["administrator"]=> array(3) {
     *        [0]=> string(13) "Administrator"
     *        [1]=> string(58) "Somebody who has access to all the administration features"
     *        [2]=> string(9) "WordPress"
     *      }
     *      ["subscriber"]=> array(3) {
     *        [0]=> string(10) "Subscriber"
     *        [1]=> string(42) "Somebody who can only manage their profile"
     *        [2]=> string(9) "WordPress"
     *      }
     *      ...
     *      ["adv-manager"]=>
     *      array(3) {
     *        [0]=> string(11) "Adv Manager"
     *        [1]=> string(29) "This role is for adv manager."
     *        [2]=> string(13) "Roles Manager"
     *      }
     *    }
     */

    if ( empty( $this->extend_data ) ) {
      $this->extend_data = $this->all_roles;

      update_option( self::OPTION_KEY, $this->extend_data );

      /**
       * Fires when the role is updated.
       *
       * @param array $extend The array extra (extends) data.
       */
      do_action( 'wpdk_user_roles_extend_update', $this->extend_data );
    }

  }

  // -------------------------------------------------------------------------------------------------------------------
  // Information
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Set the `activeRoles` and `inactiveRoles` properties array.
   *
   * @brief Get active and inactive roles list
   * @since 1.5.18
   */
  public function statusRoles()
  {
    // Reset properties
    $this->activeRoles   = array();
    $this->inactiveRoles = array();

    // Loop into the roles
    foreach ( $this->role_names as $role => $name ) {

      // Get count
      $count = $this->arrayCountUsersByRole[ $role ];

      // Default empty extends
      $extend = array( $name, '', '' );

      // Get the extend data
      $extend = isset( $this->extend_data[ $role ] ) ? $this->extend_data[ $role ] : $extend;

      if ( empty( $count ) ) {
        $this->inactiveRoles[ $role ] = $extend;
      }
      else {
        $this->activeRoles[ $role ] = $extend;
      }
    }
  }

  /**
   * @brief      Active roles
   * @deprecated since 1.5.18 - Use statusRoles() instead
   *
   * @return array
   */
  public function activeRoles()
  {

    _deprecated_function( __CLASS__ . '::' . __FUNCTION__, '1.5.18', 'statusRoles()' );

    // Calculate only if the property if note set
    if ( empty( $this->activeRoles ) ) {
      $this->statusRoles();
    }

    return $this->activeRoles;
  }

  /**
   * @brief      Inactive roles
   * @deprecated since 1.5.18 - Use statusRoles() instead
   *
   * @return array
   */
  public function inactiveRoles()
  {
    _deprecated_function( __CLASS__ . '::' . __FUNCTION__, '1.5.18', 'statusRoles()' );

    // Calculate only if the property if note set
    if ( empty( $this->inactiveRoles ) ) {

      $this->statusRoles();
    }

    return $this->inactiveRoles;
  }

  /**
   * Set the `arrayCountUsersByRole` property with the list of user count by role.
   *
   * Counts the number of users for all roles on the site and returns this as an array.
   *
   * @brief Counts the number of users for roles
   *
   * @return array
   */
  public function countUsersByRole()
  {
    // Reset property
    $this->arrayCountUsersByRole = array();

    $user_count = count_users();

    /*
     * $user_count
     *
     *   array(2) {
     *      ["total_users"]=> int(6833)
     *      ["avail_roles"]=> array(9) {
     *        ["administrator"]=> int(6)
     *        ["subscriber"]=> int(6604)
     *        ["bbp_keymaster"]=> int(3)
     *        ["bbp_participant"]=> int(177)
     *        ["pending"]=> int(2)
     *        ["member"]=> int(181)
     *        ["trial"]=> int(36)
     *        ["plan_user_1_month"]=> int(2)
     *        ["ex_plan_user_1_month"]=> int(2)
     *      }
     *    }
     *
     */

    // Loop into all role
    foreach ( $this->role_names as $role => $name ) {
      $this->arrayCountUsersByRole[ $role ] = isset( $user_count['avail_roles'][ $role ] ) ? absint( $user_count['avail_roles'][ $role ] ) : 0;
    }

    return $this->arrayCountUsersByRole;
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
    // Reset property
    $this->arrayCapabilitiesByRole = array();

    // Loop into the
    foreach ( $this->get_names() as $role => $name ) {

      // Count capabilities for role too
      $wp_role = $this->get_role( $role );

      // Stability
      if ( ! is_null( $wp_role ) ) {
        ksort( $wp_role->capabilities );
        $this->arrayCapabilitiesByRole[ $role ] = $wp_role->capabilities;
      }
    }

    return $this->arrayCapabilitiesByRole;
  }

  /**
   * Return the roles id list with one or more caps
   *
   * @brief  Roles with cap
   * @since  1.5.4
   *
   * @param WPDKCapability|string|array $caps Any instance of WPDKCapability class, capability name or array of
   *                                          capability name/instance of WPDKCapability class
   *
   * @return array
   */
  public function rolesWithCaps( $caps )
  {
    // Makes array
    $caps = (array) $caps;

    // Normalize caps
    $normalize_caps = array();
    foreach ( $caps as $cap ) {
      $normalize_caps[] = is_string( $cap ) ? $cap : $cap->id;
    }

    // Prepare results
    $results = array();

    // Roles with capabilities
    foreach ( $this->arrayCapabilitiesByRole as $role => $capabilities ) {
      foreach ( $normalize_caps as $normalize_cap ) {
        if ( in_array( $normalize_cap, $capabilities ) ) {
          $results[] = $role;
          break;
        }
      }
    }

    return $results;
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
   * Return a key value pairs array with name of role and extends info.
   *
   * @brief WordPress default roles
   *
   * @return mixed|void
   */
  public function wordPressRoles()
  {
    // Default WordPress roles
    $roles = array(
      'administrator' => array(
        'Administrator',
        __( 'Somebody who has access to all the administration features', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'editor'        => array(
        'Editor',
        __( 'Somebody who can publish and manage posts and pages as well as manage other users posts, etc.', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'author'        => array(
        'Author',
        __( 'Somebody who can publish and manage their own posts', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'contributor'   => array(
        'Contributor',
        __( 'Somebody who can write and manage their posts but not publish them', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'subscriber'    => array(
        'Subscriber',
        __( 'Somebody who can only manage their profile', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
    );

    /**
     * Filter the default WordPress roles array.
     *
     * @param array $wordpress_roles The default WordPress roles array.
     */
    $this->wordPressRoles = apply_filters( 'wpdk_user_roles_wordpress_defaults', $roles );

    return $this->wordPressRoles;
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Override
  // -------------------------------------------------------------------------------------------------------------------

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
    // Normalize caps
    $caps = array();
    foreach ( $capabilities as $cap ) {
      $caps[ $cap ] = true;
    }

    // Ask to parent
    $role_object = parent::add_role( $role, $display_name, $caps );

    // Stability
    if ( ! is_null( $role_object ) ) {
      if ( ! isset( $this->extend_data[ $role ] ) ) {
        $this->extend_data[ $role ] = array(
          $display_name,
          $description,
          $owner
        );
      }
      update_option( self::OPTION_KEY, $this->extend_data );

      /**
       * Fires when a role is added.
       *
       * @since 1.5.18
       *
       * @param string $role   The role key.
       * @param array  $extend The array with extend data for this role
       */
      do_action( 'wpdk_user_roles_added_role', $role, $this->extend_data[ $role ] );

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
    unset( $this->extend_data[ $role ] );
    update_option( self::OPTION_KEY, $this->extend_data );

    /**
     * Fires when a role is removed.
     *
     * @since 1.5.18
     *
     * @param string $role The role key.
     */
    do_action( 'wpdk_user_roles_removed_role', $role );
  }

}

/* @deprecated since 1.5.18 */
class WPDKRoles extends WPDKUserRoles {

}

/* @deprecated since 1.5.18 */
class WPDKRole extends WPDKUserRole {

}
