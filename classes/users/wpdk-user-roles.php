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
    // Roles
    if ( isset( WPDKUserRoles::getInstance()->roles[ $this->name ] ) ) {

      // Reset all capabilities
      WPDKUserRoles::getInstance()->roles[ $this->name ]['capabilities'] = array();

      // Set new capabilities
      foreach ( $this->capabilities as $cap ) {
        WPDKUserRoles::getInstance()->roles[ $this->name ]['capabilities'][ $cap ] = true;
      }

      // Updated
      if ( WPDKUserRoles::getInstance()->use_db ) {
        update_option( WPDKUserRoles::getInstance()->role_key, WPDKUserRoles::getInstance()->roles );
        WPDKUserRoles::invalidate();
      }

    }

    $extra = get_option( WPDKUserRoles::OPTION_KEY );
    if ( ! empty( $extra ) ) {
      $extra[ $this->name ] = array(
        $this->displayName,
        $this->description,
        $this->owner
      );
    }
    else {
      $extra = WPDKUserRoles::init()->activeRoles;
    }

    return update_option( WPDKUserRoles::OPTION_KEY, $extra );
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
   * @var array $extend_data
   */
  private $extend_data;

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

    // Get the extended data
    $this->extend_data = get_option( self::OPTION_KEY );

    if ( ! empty( $this->role_names ) ) {
      $this->count = count( $this->role_names );
    }

    // Init properties
    $this->wordPressRoles();
    $this->activeRoles();
    $this->inactiveRoles();
    $this->countUsersByRole();

    // Create An key value pairs array with key = role and value = list of capabilities
    $this->arrayCapabilitiesByRole();

    if ( empty( $this->extend_data ) ) {
      $this->extend_data = array_merge( $this->activeRoles, $this->inactiveRoles, $this->wordPressRoles );
      update_option( self::OPTION_KEY, $this->extend_data );
    }

    // List of all roles
    $this->all_roles = array_merge( $this->activeRoles, $this->inactiveRoles, $this->wordPressRoles );

    /*
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
     *        [0]=>
     *        string(11) "Adv Manager"
     *        [1]=>
     *        string(29) "This role is for adv manager."
     *        [2]=>
     *        string(13) "Roles Manager"
     *      }
     *    }
     */

  }

  // -------------------------------------------------------------------------------------------------------------------
  // Information
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Gets all the roles that have users for the site.
   *
   * @brief Active roles
   *
   * @return array
   */
  public function activeRoles()
  {

    // Calculate only if the property if note set
    if ( ! isset( $this->activeRoles ) ) {

      $this->activeRoles = array();
      foreach ( $this->role_names as $role => $name ) {
        $count = $this->countUsersByRole( $role );
        if ( ! empty( $count ) ) {
          $this->activeRoles[ $role ] = isset( $this->extend_data[ $role ] ) ? $this->extend_data[ $role ] : array(
            $name,
            '',
            ''
          );
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

    // Calculate only if the property if note set
    if ( ! isset( $this->inactiveRoles ) ) {

      $this->inactiveRoles = array();
      foreach ( $this->role_names as $role => $name ) {
        $count = $this->countUsersByRole( $role );
        if ( empty( $count ) ) {
          $this->inactiveRoles[ $role ] = isset( $this->extend_data[ $role ] ) ? $this->extend_data[ $role ] : array(
            $name,
            '',
            ''
          );
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

    // If the count is not already set for all roles, let's get it
    if ( ! isset( $this->arrayCountUsersByRole ) ) {

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

      // Loop through the user count by role to get a count of the users with each role
      foreach ( $user_count['avail_roles'] as $role => $count ) {
        $this->arrayCountUsersByRole[ $role ] = $count;
      }
    }

    // If the $user_role parameter wasn't passed into this function, return the array of user counts
    if ( empty( $user_role ) ) {
      return $this->arrayCountUsersByRole;
    }

    // If the role has no users, we need to set it to '0'
    if ( ! isset( $this->arrayCountUsersByRole[ $user_role ] ) ) {
      $this->arrayCountUsersByRole[ $user_role ] = 0;
    }

    // Return the user count for the given role
    return $this->arrayCountUsersByRole[ $user_role ];
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

    // If the count is not already set for all roles, let's get it
    if ( ! isset( $this->arrayCapabilitiesByRole ) ) {

      foreach ( $this->get_names() as $role => $name ) {

        // Count capabilities for role too
        $wp_role = $this->get_role( $role );
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
   * Return a key value pairs array with name of role and extra info.
   *
   * @brief WordPress default roles
   *
   * @return mixed|void
   */
  public function wordPressRoles()
  {
    $this->wordPressRoles = array(
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

    $this->wordPressRoles = apply_filters( 'wpdk_roles_defaults', $this->wordPressRoles );

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

    $role_object = parent::add_role( $role, $display_name, $caps );
    if ( ! is_null( $role_object ) ) {
      if ( ! isset( $this->extend_data[ $role ] ) ) {
        $this->extend_data[ $role ] = array(
          $display_name,
          $description,
          $owner
        );
      }
      update_option( self::OPTION_KEY, $this->extend_data );
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
  }


  // -------------------------------------------------------------------------------------------------------------------
  // UI
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return the HTML markup for a combo select
   *
   * @param string|WPDKUserRole $role Role
   *
   * @return string
   */
  public function selectCapabilitiesWithRole( $role )
  {

    if ( is_object( $role ) && is_a( $role, 'WPDKUserRole' ) ) {
      $role = $role->name;
    }

    WPDKHTML::startCompress() ?>

    <select>
    <?php foreach ( $this->arrayCapabilitiesByRole[ $role ] as $cap => $enabled ): ?>
      <option><?php echo $cap ?></option>
    <?php endforeach ?>
    </select>

    <?php

    return WPDKHTML::endHTMLCompress();
  }

}

/* @deprecated since 1.5.18 */
class WPDKRoles extends WPDKUserRoles {

}

/* @deprecated since 1.5.18 */
class WPDKRole extends WPDKUserRole {

}
