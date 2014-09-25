<?php

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
 * @class              WPDKUserCapabilities
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-09-24
 * @version            1.0.0
 *
 * @since              1.5.18
 */
class WPDKUserCapabilities {

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
  private $_extendedData = array();

  /**
   * Updated the capabilties extended data
   *
   * @brief Updated
   * @since 1.5.4
   *
   * @param WPDKUserCapability $capability An instance of WPDKUserCapability class
   */
  public function update_extended_data( $capability )
  {
    $this->_extendedData[ $capability->id ] = array(
      $capability->id,
      $capability->description,
      $capability->owner
    );

    update_option( WPDKUserCapabilities::OPTION_KEY, $this->_extendedData );
  }

  /**
   * Return the capabilties extended data or FALSE if not found.
   *
   * @brief Get
   * @since 1.5.4
   *
   * @param string|WPDKUserCapability $capability Any cap id or WPDKUserCapability istance
   *
   * @return bool
   */
  public function get_extended_data( $capability )
  {
    // String or Object ?
    $id = is_object( $capability ) ? $capability->id : $capability;

    return isset( $this->_extendedData[ $id ] ) ? $this->_extendedData[ $id ] : false;
  }

  /**
   * Delete the capabilties extended data
   *
   * @brief Get
   * @since 1.5.4
   *
   * @param string|WPDKUserCapability $capability Any cap id or WPDKUserCapability istance
   */
  public function delete_extended_data( $capability )
  {
    // String or Object ?
    $id = is_object( $capability ) ? $capability->id : $capability;

    // Destroy
    unset( $this->_extendedData[ $id ] );

    // Update
    update_option( WPDKUserCapabilities::OPTION_KEY, $this->_extendedData );
  }

  /**
   * Return a singleton instance of WPDKUserCapabilities class
   *
   * @brief      Singleton instance of WPDKUserCapabilities
   * @deprecated since 1.5.4 use get_instance() or init() instead
   *
   * @return WPDKUserCapabilities
   */
  public static function getInstance()
  {
    _deprecated_function( __METHOD__, '1.5.4', 'init()' );

    return self::init();
  }

  /**
   * Return a singleton instance of WPDKUserCapabilities class
   *
   * @brief Singleton instance of WPDKUserCapabilities
   *
   * @return WPDKUserCapabilities
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
   * Create an instance of WPDKUserCapabilities class
   *
   * @brief Construct
   *
   * @return WPDKUserCapabilities
   */
  private function __construct()
  {

    // Get the extended data
    $this->_extendedData = get_option( self::OPTION_KEY );

    // Preset WordPress capabilities list
    $this->defaultCapabilities = array_keys( $this->defaultCapabilities() );

    // Get the role capabilities
    $this->roleCapabilities = array_keys( $this->roleCapabilities() );

    // Get the role users
    $this->userCapabilities = array_keys( $this->userCapabilities() );

    // Setup only plugin capabilities
    $this->capabilities = array_unique( array_merge( array_diff( $this->roleCapabilities, $this->defaultCapabilities ), $this->userCapabilities ) );
    sort( $this->capabilities );

    // All caps
    $this->allCapabilities = array_unique( array_merge( $this->userCapabilities, $this->roleCapabilities, $this->defaultCapabilities ) );

    // Sort
    sort( $this->allCapabilities );
  }

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

    // Create an array of all the default WordPress capabilities so the user doesn't accidentally get rid of them
    $defaults = array(
      'activate_plugins'       => array(
        'activate_plugins',
        __( 'Allows access to Administration Panel options: Plugins', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'add_users'              => array( 'add_users', __( 'add_users', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'create_users'           => array( 'create_users', __( 'create_users', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'delete_others_pages'    => array(
        'delete_others_pages',
        __( 'delete_others_pages', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'delete_others_posts'    => array(
        'delete_others_posts',
        __( 'delete_others_posts', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'delete_pages'           => array( 'delete_pages', __( 'delete_pages', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'delete_plugins'         => array( 'delete_plugins', __( 'delete_plugins', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'delete_posts'           => array( 'delete_posts', __( 'delete_posts', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'delete_private_pages'   => array(
        'delete_private_pages',
        __( 'delete_private_pages', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'delete_private_posts'   => array(
        'delete_private_posts',
        __( 'delete_private_posts', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'delete_published_pages' => array(
        'delete_published_pages',
        __( 'delete_published_pages', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'delete_published_posts' => array(
        'delete_published_posts',
        __( 'delete_published_posts', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'delete_users'           => array( 'delete_users', __( 'delete_users', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_dashboard'         => array( 'edit_dashboard', __( 'edit_dashboard', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_files'             => array( 'edit_files', __( 'No longer used.', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_others_pages'      => array( 'edit_others_pages', __( 'edit_others_pages', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_others_posts'      => array( 'edit_others_posts', __( 'edit_others_posts', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_pages'             => array( 'edit_pages', __( 'edit_pages', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_plugins'           => array( 'edit_plugins', __( 'edit_plugins', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_posts'             => array(
        'edit_posts',
        __( 'Allows access to Administration Panel options: Posts, Posts > Add New, Comments, Comments > Awaiting Moderation', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'edit_private_pages'     => array(
        'edit_private_pages',
        __( 'edit_private_pages', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'edit_private_posts'     => array(
        'edit_private_posts',
        __( 'edit_private_posts', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'edit_published_pages'   => array(
        'edit_published_pages',
        __( 'edit_published_pages', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'edit_published_posts'   => array(
        'edit_published_posts',
        __( 'User can edit their published posts. This capability is off by default. The core checks the capability edit_posts, but on demand this check is changed to edit_published_posts. If you don\'t want a user to be able edit his published posts, remove this capability.', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'edit_theme_options'     => array(
        'edit_theme_options',
        __( 'Allows access to Administration Panel options: Appearance > Widgets, Appearance > Menus, Appearance > Theme Options if they are supported by the current theme, Appearance > Background, Appearance > Header ', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'edit_themes'            => array( 'edit_themes', __( 'edit_themes', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'edit_users'             => array(
        'edit_users',
        __( 'Allows access to Administration Panel options: Users', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'import'                 => array( 'import', __( 'import', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'install_plugins'        => array(
        'install_plugins',
        __( 'Allows access to Administration Panel options: Plugins > Add New ', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'install_themes'         => array(
        'install_themes',
        __( 'Allows access to Administration Panel options: Appearance > Add New Themes', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'list_users'             => array( 'list_users', __( 'list_users', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'manage_categories'      => array(
        'manage_categories',
        __( 'Allows access to Administration Panel options: Posts > Categories, Links > Categories', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'manage_links'           => array(
        'manage_links',
        __( 'Allows access to Administration Panel options: Links Links > Add New', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'manage_options'         => array(
        'manage_options',
        __( 'Allows access to Administration Panel options: Settings > General, Settings > Writing, Settings > Reading, Settings > Discussion, Settings > Permalinks, Settings > Miscellaneous', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'moderate_comments'      => array(
        'moderate_comments',
        __( 'Allows users to moderate comments from the Comments SubPanel (although a user needs the edit_posts Capability in order to access this)', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'promote_users'          => array( 'promote_users', __( 'promote_users', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'publish_pages'          => array( 'publish_pages', __( 'publish_pages', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'publish_posts'          => array(
        'publish_posts',
        __( 'See and use the "publish" button when editing their post (otherwise they can only save drafts). Can use XML-RPC to publish (otherwise they get a "Sorry, you can not post on this weblog or category.")', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'read'                   => array(
        'read',
        __( 'Allows access to Administration Panel options: Dashboard, Users > Your Profile. Used nowhere in the core code except the menu.php', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'read_private_pages'     => array(
        'read_private_pages',
        __( 'read_private_pages', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'read_private_posts'     => array(
        'read_private_posts',
        __( 'read_private_posts', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'remove_users'           => array( 'remove_users', __( 'remove_users', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'switch_themes'          => array(
        'switch_themes',
        __( 'Allows access to Administration Panel options: Appearance, Appearance > Themes ', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'unfiltered_html'        => array(
        'unfiltered_html',
        __( 'Allows user to post HTML markup or even JavaScript code in pages, posts, and comments. Note: Enabling this option for untrusted users may result in their posting malicious or poorly formatted code. ', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
      'unfiltered_upload'      => array( 'unfiltered_upload', __( 'unfiltered_upload', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'update_core'            => array( 'update_core', __( 'update_core', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'update_plugins'         => array( 'update_plugins', __( 'update_plugins', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'update_themes'          => array( 'update_themes', __( 'update_themes', WPDK_TEXTDOMAIN ), 'WordPress' ),
      'upload_files'           => array(
        'upload_files',
        __( 'Allows access to Administration Panel options: Media, Media > Add New ', WPDK_TEXTDOMAIN ),
        'WordPress'
      ),
    );

    // Sorting
    ksort( $defaults );

    // Return the array of default capabilities
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
    // Get WPDKUserRoles
    $wpdk_roles = WPDKUserRoles::getInstance();

    // Set up an empty capabilities array
    $capabilities = array();

    // Loop through each role object because we need to get the caps
    foreach ( $wpdk_roles->role_objects as $key => $role ) {

      // Roles without capabilities will cause an error, so we need to check if $role->capabilities is an array
      if ( is_array( $role->capabilities ) ) {

        // Loop through the role's capabilities and add them to the $capabilities array
        $exclude = self::oldLevels();
        foreach ( $role->capabilities as $cap => $grant ) {
          if ( ! isset( $exclude[ $cap ] ) ) {
            $capabilities[ $cap ] = isset( $this->_extendedData[ $cap ] ) ? $this->_extendedData[ $cap ] : array(
              $cap,
              '',
              ''
            );
          }
        }
      }
    }

    // Sort the capabilities by name so they're easier to read when shown on the screen
    ksort( $capabilities );

    // Return the capabilities array
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
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    //$capabilities = get_transient( '_wpdk_users_caps' );
    $capabilities = ''; // cache off for debug

    if ( empty( $capabilities ) ) {
      $sql    = "SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'wp_capabilities'";
      $result = $wpdb->get_results( $sql, ARRAY_A );

      foreach ( $result as $user_cap ) {

        // A cap is store with a bolean flag that here is ignored
        $temp = array_keys( unserialize( $user_cap['meta_value'] ) );
        foreach ( $temp as $key ) {
          $capabilities[ $key ] = isset( $this->_extendedData[ $key ] ) ? $this->_extendedData[ $key ] : array(
            $key,
            '',
            ''
          );
        }
      }
      //set_transient( '_wpdk_users_caps', $capabilities, 120 );
    }

    // Sort the capabilities by name so they're easier to read when shown on the screen
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

    // Sort the capabilities by name so they're easier to read when shown on the screen
    ksort( $capabilities );

    return $capabilities;
  }

  /**
   * Return TRUE if a capability exists
   *
   * @brief Capability exists
   * @since 1.5.4
   *
   * @param string $cap Capability ID
   *
   * @return bool
   */
  public function capabilityExists( $cap )
  {
    return in_array( $cap, $this->allCapabilities );
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Extra
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Add a capability to roles and/or users
   *
   * @brief Add a cap
   * @since 1.5.4
   *
   * @param WPDKUserCapability $cap   An instance of WPDKUserCapability class
   * @param array          $roles Optional. List of roles where add this cap
   * @param array          $users Optional. List of user id where add this cap
   */
  public function add_cap( $cap, $roles = array(), $users = array() )
  {
    // Add this cap to roles?
    if ( ! empty( $roles ) ) {
      foreach ( $roles as $role ) {
        WPDKUserRoles::init()->add_cap( $role, $cap->id );
      }
    }

    // Add this cap to users?
    if ( ! empty( $users ) ) {
      foreach ( $users as $user_id ) {
        $user = new WP_User( $user_id );
        $user->add_cap( $cap->id );
      }
    }

    // Store the extra info
    $this->update_extended_data( $cap );

  }

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
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    //$user_caps = get_transient( '_wpdk_users_caps' );
    $user_caps = false; // cache off for debug
    if ( empty( $user_caps ) ) {
      $sql    = "SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'wp_capabilities'";
      $result = $wpdb->get_results( $sql, ARRAY_A );

      foreach ( $result as $user_cap ) {
        $user_caps[ $user_cap['user_id'] ] = get_userdata( $user_cap['user_id'] )->allcaps;
      }

      //set_transient( '_wpdk_users_caps', $user_caps, 120 );
    }

    ksort( $user_caps );

    return $user_caps;

  }

  /**
   * Delete capabilities
   *
   * @brief Delete
   *
   * @param string|array $id Any single or array of caps
   */
  public function delete( $id )
  {
    // Makes array
    $id = (array) $id;

    // Get extra info
    $extra = get_option( self::OPTION_KEY );

    // Loop
    foreach ( $id as $cap ) {

      // Destroy extra info
      unset( $extra[ $cap ] );

      /*
       * Remove this cap from users
       */

      // Gets users
      $users = $this->usersWithCaps( $cap );

      foreach ( $users as $user_id ) {
        $user = new WP_User( $user_id );
        $user->remove_cap( $cap );
      }

      /*
       * Remove cap from roles
       */

      // Loop in roles
      foreach ( WPDKUserRoles::init()->arrayCapabilitiesByRole as $role => $caps ) {
        if ( in_array( $cap, array_keys( $caps ) ) ) {
          WPDKUserRoles::init()->remove_cap( $role, $cap );
        }
      }

    }

    // Update extra info
    update_option( self::OPTION_KEY, $extra );
  }

  /**
   * Return a list of users id with any caps
   *
   * @brief Users with caps
   * @since 1.5.4
   *
   * @param string|array $caps Any single or array of caps
   */
  public function usersWithCaps( $caps )
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    // Makes array
    $caps = (array) $caps;

    // Likes
    $likes = array();

    // Loop
    foreach ( $caps as $cap ) {
      $likes[] = "meta_value LIKE '%\"$cap\"%'";
    }

    // Build `meta_value LIKE "" OR meta_value LIKE "" OR ...`
    $likes_str = implode( ' OR ', $likes );

    // Prepare select
    $sql = "SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'wp_capabilities' AND ( $likes_str )";

    // Gets users
    return array_keys( $wpdb->get_results( $sql, OBJECT_K ) );

  }

}


/**
 * Single capability model. This class is useful to map a few instance of capability.
 *
 * @class           WPDKUserCapability
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-09-24
 * @version         1.0.0
 *
 * @since           1.5.18
 *
 */
class WPDKUserCapability {

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
   * List of users id with this cap
   *
   * @brief Users
   *
   * @var array $users
   */
  public $users = array();

  /**
   * List of role id with this cap
   *
   * @brief Roles
   *
   * @var array $roles
   */
  public $roles = array();

  /**
   * Create an instance of WPDKUserCapability class
   *
   * @brief Construct
   *
   * @param string $id          Unique id of capability
   * @param string $description Optional. Capability description
   * @param string $owner       Optional. Capability owner, who create this capabilty
   *
   * @return WPDKUserCapability
   */
  public function __construct( $id, $description = '', $owner = '' )
  {
    $this->id          = $id;
    $this->description = $description;
    $this->owner       = $owner;

    // Cap already exists ? Then get the data
    if ( WPDKUserCapabilities::init()->capabilityExists( $id ) ) {

      // Get extends data
      $extend_data = WPDKUserCapabilities::init()->get_extended_data( $id );

      if ( ! empty( $extend_data ) ) {

        list( $id, $description, $owner ) = $extend_data;

        // Population
        $this->description = $description;
        $this->owner       = $owner;
      }

      // Get the users id list with this cap
      $this->users = WPDKUserCapabilities::init()->usersWithCaps( $id );

      // Get the roles id list with this cap
      $this->roles = WPDKUserRoles::init()->rolesWithCaps( $id );
    }

  }

  /**
   * Update in options the capability information
   *
   * @brief Update
   */
  public function update()
  {
    // Update extra data
    WPDKUserCapabilities::init()->update_extended_data( $this );
  }

}


/* @deprecated since 1.5.18 */
class WPDKCapabilities extends WPDKUserCapabilities {}

/* @deprecated since 1.5.18 */
class WPDKCapability extends WPDKUserCapability {

}