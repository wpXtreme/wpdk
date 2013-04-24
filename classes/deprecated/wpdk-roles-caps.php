<?php

/// @cond private

/**
 * WPDKRoles class extends features of WordPress class WP_Roles.
 *
 * @class              WPDKRoles
 * @author             yuma - <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 * @deprecated Since 1.0.0.b4 - Never used
 */

// Include functions for handling extended metadata
//require_once( 'wpdk-ext-meta-functions.php' );

class __WPDKRoles extends WP_Roles {

  //-------------------------------------------------------------------------------------------
  // Properties
  //-------------------------------------------------------------------------------------------

  /**
   * The total number of roles
   *
   * @brief The number of roles
   *
   * @var int $countRoles;
   */
  public $countRoles;

  /**
   * The single instance of this class that can exist in every HTTP request
   *
   * @brief The single instance of this class
   *
   * @var WPDKRoles $_firstInstance
   *
   * @since 0.0.1
   */
  //  private static $_firstInstance = NULL;


  //-------------------------------------------------------------------------------------------
  // Methods
  //-------------------------------------------------------------------------------------------

  /**
   * The class constructor, that extends in some part the default constructor of parent WP_Roles
   *
   * @brief The constructor
   *
   * @since 0.0.1
   *
   * @note Here is a different, cleaner approach to the singleton concept.
   *
   */
  function __construct() {

    // If this is first New about this object
    //    if( ! isset( self::$_firstInstance )) {

    // Parent
    parent::__construct();

    // Get the # of users for every role
    $this->_countUsersByRole();

    // # of roles
    $this->countRoles = count( $this->roles );

    // At last, save this instance in private static environment: AT LAST!!!!!
    //    self::$_firstInstance = $this;

    //    }
    //    else {
    //
    //      // Get previous instance data and create a mirror
    //      foreach( get_object_vars( self::$_firstInstance ) as $sProp => $sValue ) {
    //        $this->$sProp = $sValue;
    //      }
    //
    //    }

  }


  /**
   * Counts the number of users for every role on the site and set this value into the array of roles created by WP_Roles.
   * This function is invoked just one time, in the constructor, and save the count
   *
   * @brief Counts the number of users for every role
   *
   * @since 0.0.1
   *
   */
  private function _countUsersByRole() {

    // First of all, set every counter to zero for any role
    foreach ( $this->roles as $sRole => $aData ) {
      $this->roles[$sRole]['count_users'] = 0;
    }

    // Count users with a WP function
    $aUserData = count_users();

    /* Loop through the user count by role to get a count of the users with each role. */
    foreach ( $aUserData['avail_roles'] as $role => $count ) {
      $this->roles[$role]['count_users'] = $count;
    }

  }


  /**
   * Create a brand new role, with its capabilities. The add_role from parent is simply made more secure.
   *
   * @brief Create a brand new role
   *
   * @since 0.0.1
   *
   * @param string $sRoleName     - role name/key in DB and in the whole system
   * @param string $sRoleLabel    - label of role in WordPress environment ( i.e. in user settings ).
   * @param array  $aCapabilities (optional) Array of capabilities for this role. Default to array().
   *
   * @return mixed WP_Role|WPDKError : the new role, or the error occurred.
   *
   */
  function add_role( $sRoleName, $sRoleLabel, $aCapabilities = array() ) {

    // check about role syntax
    if ( empty( $sRoleName ) || empty( $sRoleLabel ) ) {
      return new WPDKError( 'WPDKRoles', __( 'Role data cannot be empty.', WPDK_TEXTDOMAIN ) );
    }

    // check about role data length
    if (
      strlen( $sRoleName ) >= self::MAX_LENGTH_OF_ROLE_DATA || strlen( $sRoleLabel ) >= self::MAX_LENGTH_OF_ROLE_DATA
    ) {
      return new WPDKError( 'WPDKRoles', sprintf( __( 'Role data cannot be more than %s chars.', WPDK_TEXTDOMAIN ), self::MAX_LENGTH_OF_ROLE_DATA ) );
    }


    // role name/key can contain only letters, digits and some other chars
    if ( 1 == preg_match( '/[^a-zA-Z0-9_\-]+/', $sRoleName ) ) {
      return new WPDKError( 'WPDKRoles', __( 'Role name can contain only letters, digits, \'_\' and \'-\' chars.', WPDK_TEXTDOMAIN ) );
    }

    // this role must be UNIQ in the whole WP system
    if ( TRUE == $this->is_role( $sRoleName ) ) {
      return new WPDKError( 'WPDKRoles', sprintf( __( "Can't create two roles with the same name '%s'.", WPDK_TEXTDOMAIN ), $sRoleName ) );
    }

    // create the role
    $cRole = parent::add_role( $sRoleName, $sRoleLabel, $aCapabilities );

    // update options with extended data about role and return - role has a prefix to distinguish from cap
    //    return self::update_extended_data( self::ROLE_KEY_PREFIX . $sRoleName, $aNewData );

    return $cRole;

  }


  /**
   * Create a brand new capability for a role, with some new params that extends normal WordPress handling.
   *
   * @brief Create a brand new capability
   *
   * @since 1.0.0
   *
   * @param string $sCapName  - capability name/key in DB and in the whole system
   * @param string $sRole     - role the capability belongs to.
   * @param bool   $bCapValue (optional) boolean value of capability. Default to TRUE.
   * @param array  $aNewData  (optional) array of new extended data related to this cap. Default to array().
   *
   * @return mixed TRUE|WPDKError
   *
   */
  static function create_cap( $sCapName, $sRole, $bCapValue = TRUE, $aNewData = array() ) {

    // check about capability name syntax
    if ( empty( $sCapName ) ) {
      return new WPDKError( 'wpdk_roles_caps', __( 'Capability name cannot be empty.', WPDK_TEXTDOMAIN ) );
    }

    // capability name/key can contain only letters, digits and some other chars
    if ( 1 == preg_match( '/[^a-zA-Z0-9_\-]+/', $sCapName ) ) {
      return new WPDKError( 'wpdk_roles_caps', __( 'Capability name can contain only letters, digits, _ and - chars.', WPDK_TEXTDOMAIN ) );
    }

    // if role is unexistent, can't create a brand new cap
    $aRoles = self::$cWpRoles->get_names(); // Get all roles
    if ( FALSE == array_key_exists( $sRole, $aRoles ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( "Can't add a capability to the unexistent role %s.", WPDK_TEXTDOMAIN ), $sRole ) );
    }

    // this cap must be UNIQ in the WP system, regardless to role
    if ( TRUE == self::cap_exists( $sCapName ) ) {
      return new WPDKError( 'wpdk_roles_caps', __( "Can't add a capability with the same name of an already existent one.", WPDK_TEXTDOMAIN ) );
    }

    // create the cap
    self::$cWpRoles->add_cap( $sRole, $sCapName, $bCapValue );

    // update options with extended data about cap and return
    return self::update_extended_data( $sCapName, $aNewData );

  }


  /**
   * Set the main role of a WordPress user.
   *
   * @brief Set the main role of a user
   *
   * @since 1.0.0
   *
   * @param int    $iIDUser   - user ID in WordPress environment
   * @param string $sRoleName - role name/key
   *
   * @return mixed TRUE|WPDKError
   *
   */
  static function set_main_role_of_user( $iIDUser, $sRoleName ) {

    // Does the user exist?
    $rCheck = WP_User::get_data_by( 'id', $iIDUser );
    if ( FALSE == $rCheck ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'User %d does not exist in system.', WPDK_TEXTDOMAIN ), $iIDUser ) );
    }

    // Does the role exist?
    if ( FALSE == array_key_exists( $sRoleName, self::get_all_roles() ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Role %s does not exist in system.', WPDK_TEXTDOMAIN ), $sRoleName ) );
    }

    // Does the user have already the role as a main role?
    $aRole = self::get_user_role( $iIDUser );
    if ( is_array( $aRole ) ) {
      if ( $sRoleName == key( $aRole ) ) {
        return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'The main role of user "%s" is already "%s".', WPDK_TEXTDOMAIN ), $rCheck->user_login, $sRoleName ) );
      }
    }

    // Set the main role of the user
    $rUser = new WP_User( $iIDUser );
    $rUser->set_role( $sRoleName );

    // set_role method of WP_User returns void. So return TRUE
    return TRUE;

  }


  /**
   * Add an existing capability to a specific WordPress user
   *
   * @brief Add a cap to a user
   *
   * @since 1.0.0
   *
   * @param int    $iIDUser   - user ID in WordPress environment
   * @param string $sCapName  - capability name/key
   * @param bool   $bCapValue (optional) boolean value of capability. Default to TRUE.
   *
   * @return mixed TRUE|WPDKError
   *
   */
  static function add_cap_to_user( $iIDUser, $sCapName, $bCapValue = TRUE ) {

    // Get data about user
    $rCheck = WP_User::get_data_by( 'id', $iIDUser );
    if ( FALSE == $rCheck ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'User %d does not exist in system.', WPDK_TEXTDOMAIN ), $iIDUser ) );
    }

    // Does the cap exist?
    if ( FALSE == self::cap_exists( $sCapName ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Unable to add unexistent capability "%s" to user "%s".', WPDK_TEXTDOMAIN ), $sCapName, $rCheck->user_login ) );

    }

    $rUser = new WP_User( $iIDUser );

    // Does the user have already this cap?
    if ( TRUE == array_key_exists( $sCapName, $rUser->allcaps ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'User "%s" has already the capability "%s".', WPDK_TEXTDOMAIN ), $rCheck->user_login, $sCapName ) );

    }

    // add cap to user
    $rUser->add_cap( $sCapName, $bCapValue );

    // add_cap method of WP_User returns void. So return TRUE
    return TRUE;

  }


  /**
   * Delete a capability from a specific WordPress user
   * WARNING: with this method, I can ALSO DELETE A CAPABILITY FROM DEFAULT USERS, LIKE admin. Please use VERY
   * CAREFULLY this facility.
   *
   * @brief Delete a cap from user
   *
   * @since 1.0.0
   *
   * @param int    $iIDUser  - user ID in WordPress environment
   * @param string $sCapName - capability name/key
   *
   * @return mixed TRUE|WPDKError
   *
   */
  static function delete_cap_from_user( $iIDUser, $sCapName ) {

    // Get data about user
    $rCheck = WP_User::get_data_by( 'id', $iIDUser );
    if ( FALSE == $rCheck ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'User %d does not exist in system.', WPDK_TEXTDOMAIN ), $iIDUser ) );
    }

    $rUser = new WP_User( $iIDUser );

    // Does the user have this cap? If not, what do I want to delete?
    if ( FALSE == array_key_exists( $sCapName, $rUser->allcaps ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'User "%s" does not have the capability "%s".', WPDK_TEXTDOMAIN ), $rCheck->user_login, $sCapName ) );

    }

    // delete cap from user
    $rUser->remove_cap( $sCapName );

    // remove_cap method of WP_User returns void. So return TRUE
    return TRUE;

  }


  /**
   * Add an existing capability to a specific WordPress role
   *
   * @brief Add a cap to a role
   *
   * @since 1.0.0
   *
   * @param string $sRoleName - role name/key that receive the cap
   * @param string $sCapName  - capability name/key
   * @param bool   $bCapValue (optional) boolean value of capability. Default to TRUE.
   *
   * @return mixed TRUE|WPDKError
   *
   */
  static function add_cap_to_role( $sRoleName, $sCapName, $bCapValue = TRUE ) {

    // Does the role exist?
    if ( FALSE == array_key_exists( $sRoleName, self::get_all_roles() ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Role %s does not exist in system.', WPDK_TEXTDOMAIN ), $sRoleName ) );
    }

    // Does the cap exist?
    if ( FALSE == self::cap_exists( $sCapName ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Unable to add unexistent capability "%s" to role "%s".', WPDK_TEXTDOMAIN ), $sCapName, $sRoleName ) );

    }

    // Does the role have already this cap?
    if ( TRUE == array_key_exists( $sCapName, self::get_caps_of_role( $sRoleName ) ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Role "%s" has already the capability "%s".', WPDK_TEXTDOMAIN ), $sRoleName, $sCapName ) );

    }

    // add cap to role
    self::$cWpRoles->add_cap( $sRoleName, $sCapName, $bCapValue );

    // add_cap method of WP_Roles returns void. So return TRUE
    return TRUE;

  }


  /**
   * Delete an existing capability from a specific WordPress role.
   * WARNING: with this method, I can ALSO DELETE A CAPABILITY FROM DEFAULT ROLES, LIKE administrator. Please use VERY
   * CAREFULLY this facility.
   *
   * @brief Delete a cap from a role
   *
   * @since 1.0.0
   *
   * @param string $sRoleName - role name/key that will drop the cap
   * @param string $sCapName  - capability name/key
   *
   * @return mixed TRUE|WPDKError
   *
   */
  static function delete_cap_from_role( $sRoleName, $sCapName ) {

    // Does the role exist?
    if ( FALSE == array_key_exists( $sRoleName, self::get_all_roles() ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Role %s does not exist in system.', WPDK_TEXTDOMAIN ), $sRoleName ) );
    }

    // Does the cap exist?
    if ( FALSE == self::cap_exists( $sCapName ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Unable to delete unexistent capability "%s" to role "%s".', WPDK_TEXTDOMAIN ), $sCapName, $sRoleName ) );

    }

    // Does the role have already this cap? If not, what do I want to delete?
    if ( FALSE == array_key_exists( $sCapName, self::get_caps_of_role( $sRoleName ) ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Role "%s" has not the capability "%s".', WPDK_TEXTDOMAIN ), $sRoleName, $sCapName ) );

    }

    // delete cap from role
    self::$cWpRoles->remove_cap( $sRoleName, $sCapName );

    // remove_cap method of WP_Roles returns void. So return TRUE
    return TRUE;

  }


  /**
   * Delete an existing WordPress role.
   * WARNING: with this method, I can ALSO COMPLETELY DELETE DEFAULT ROLES, LIKE administrator. Please use VERY
   * CAREFULLY this facility.
   *
   * @brief Delete a role
   *
   * @since 1.0.0
   *
   * @param string $sRoleName - role name/key to delete
   *
   * @return mixed TRUE|WPDKError
   *
   */
  static function delete_role( $sRoleName ) {

    // Does the role exist?
    if ( FALSE == array_key_exists( $sRoleName, self::get_all_roles() ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Role %s does not exist in system.', WPDK_TEXTDOMAIN ), $sRoleName ) );
    }

    // delete role from system, with all its associated capabilities
    self::$cWpRoles->remove_role( $sRoleName );

    // delete also all extended data about role
    return self::delete_extended_data( self::ROLE_KEY_PREFIX . $sRoleName, self::ALL_EXTENDED_DATA );

  }


  /**
   * Set extended data about a specific role, or update extended data for the same key.
   *
   * @since 1.0.0
   *
   * @brief Set extended data about a role
   *
   * @param string $sRoleName - role name/key to get data of.
   * @param array  $aNewData  array of new extended data related to this role.
   *
   * @return TRUE|FALSE : if the role is in, return TRUE if extended data has been stored, FALSE otherwise.
   *
   */
  static function set_extended_role_data( $sRoleName, $aNewData ) {

    $aAllRoles = self::get_all_roles();

    if ( array_key_exists( $sRoleName, $aAllRoles ) ) {
      return self::update_extended_data( self::ROLE_KEY_PREFIX . $sRoleName, $aNewData );
    }
    else {
      return FALSE;
    }

  }


  /**
   * Delete extended data about a specific role.
   *
   * @brief Delete extended data of a role
   *
   * @since 1.0.0
   *
   * @param string    $sRoleName    - role name/key to delete extended data of.
   * @param int|array $aExtendedKey - ALL_EXTENDED_DATA, or an array of extended keys to delete. If this param is equal
   *                                to ALL_EXTENDED_DATA, then all extended data related to the role will be deleted.
   *
   * @return TRUE|FALSE : if the role is in, return TRUE if extended data has been delete, FALSE otherwise.
   *
   */
  static function delete_extended_role_data( $sRoleName, $aExtendedKey ) {

    $aAllRoles = self::get_all_roles();

    if ( array_key_exists( $sRoleName, $aAllRoles ) ) {
      return self::delete_extended_data( self::ROLE_KEY_PREFIX . $sRoleName, $aExtendedKey );
    }
    else {
      return FALSE;
    }

  }


  /**
   * Set extended data about a specific capability, or update extended data for the same key.
   *
   * @brief Set extended data of a cap
   *
   * @since 1.0.0
   *
   * @param string $sCapName - capability name/key to get data of.
   * @param array  $aNewData array of new extended data related to this cap.
   *
   * @return TRUE|FALSE : if the capability is in, return TRUE if extended data has been stored, FALSE otherwise.
   *
   */
  static function set_extended_cap_data( $sCapName, $aNewData ) {

    if ( self::cap_exists( $sCapName ) ) {
      return self::update_extended_data( $sCapName, $aNewData );
    }
    else {
      return FALSE;
    }

  }


  /**
   * Delete extended data about a specific capability.
   *
   * @brief Delete extended data of a cap
   *
   * @since 1.0.0
   *
   * @param string    $sCapName     - capability name/key to delete extended data of.
   * @param int|array $aExtendedKey - ALL_EXTENDED_DATA, or an array of extended keys to delete. If this param is equal
   *                                to ALL_EXTENDED_DATA, then all extended data related to the capability will be deleted.
   *
   * @return TRUE|FALSE : if the capability is in, return TRUE if its extended data has been deleted, FALSE otherwise.
   *
   */
  static function delete_extended_cap_data( $sCapName, $aExtendedKey ) {

    if ( self::cap_exists( $sCapName ) ) {
      return self::delete_extended_data( $sCapName, $aExtendedKey );
    }
    else {
      return FALSE;
    }

  }


  /**
   * Get all capabilities data about a specific role, including extended one.
   *
   * @brief Get all caps of a role
   *
   * @since 1.0.0
   *
   * @param string $sRoleName - role to get data of.
   *
   * @return mixed array|FALSE : if the role is in, return an array with all data about its capabilities
   *
   */
  static function get_caps_of_role( $sRoleName ) {

    $aAllCaps = self::get_all_caps( self::CAPS_GROUPED_BY_ROLE );

    if ( array_key_exists( $sRoleName, $aAllCaps ) ) {
      return $aAllCaps[$sRoleName];
    }
    else {
      return FALSE;
    }

  }


  /**
   * Get all capabilities data about a specific user, including extended one.
   *
   * @brief Get all caps of a user
   *
   * @since 1.0.0
   *
   * @param int $iIDUser - user ID in WordPress environment
   *
   * @return mixed array|FALSE : if the user exists, return an array with all data about its capabilities
   *
   */
  static function get_caps_of_user( $iIDUser ) {

    // Check if user exists
    $rCheck = WP_User::get_data_by( 'id', $iIDUser );
    if ( FALSE == $rCheck ) {
      return FALSE;
    }

    $rUser = new WP_User( $iIDUser );

    // Get all caps of user and merge them with related extended data
    $aAllCaps   = self::get_all_caps();
    $aFinalCaps = array();
    foreach ( $rUser->allcaps as $sCapKey => $sCapValue ) {
      if ( isset( $aAllCaps[$sCapKey] ) ) {
        $aFinalCaps[$sCapKey] = $aAllCaps[$sCapKey];
      }
      // Overwrite generic capability grant with specific capability grant about this user
      $aFinalCaps[$sCapKey]['grant'] = $sCapValue;
    }

    return $aFinalCaps;

  }


  /**
   * Get all data about a specific role, including extended one.
   *
   * @brief Get all data about a role
   *
   * @since 1.0.0
   *
   * @param string $sRoleName - role name/key to get data of.
   *
   * @return mixed array|FALSE : if the role is in, return an array with all data about it
   *
   */
  static function get_role_data( $sRoleName ) {

    $aAllRoles = self::get_all_roles();

    if ( array_key_exists( $sRoleName, $aAllRoles ) ) {
      return $aAllRoles[$sRoleName];
    }
    else {
      return FALSE;
    }

  }


  /**
   * Get all data about a specific capability, including extended one.
   *
   * @brief Get all data about a cap
   *
   * @since 1.0.0
   *
   * @param string $sCapName - capability name/key to get data of.
   *
   * @return mixed array|FALSE : if the capability is in, return an array with all data about it
   *
   */
  static function get_cap_data( $sCapName ) {

    $aAllCaps = self::get_all_caps( self::CAPS_NOT_GROUPED_BY_ROLE );

    if ( array_key_exists( $sCapName, $aAllCaps ) ) {
      return $aAllCaps[$sCapName];
    }
    else {
      return FALSE;
    }

  }


  /**
   * Update options about extended data, with checks.
   *
   * @brief Update extended data options
   *
   * @since 1.0.0
   *
   * @param string $sKey     - capability/role key
   * @param array  $aNewData - array of new data about cap/role to store
   *
   * @return TRUE|FALSE : return TRUE if the extended data are valid and update is OK, FALSE otherwise.
   *
   */
  private static function update_extended_data( $sKey, $aNewData ) {

    // if I don't have extended data to update, return
    if ( empty( $aNewData ) ) {
      return TRUE;
    }

    // get all extended data previously stored in DB
    $aExtendedData = get_option( self::OPTION_KEY );

    foreach ( $aNewData as $sEnKey => $sEnValue ) {

      // Check before update
      if ( is_numeric( $sEnKey ) ) {
        continue;
      }

      // update or add new data to key
      $aExtendedData[$sKey][$sEnKey] = $sEnValue;

    }

    // save extended data to DB
    update_option( self::OPTION_KEY, $aExtendedData );

    return TRUE;

  }


  /**
   * Delete options about extended data, with checks.
   *
   * @brief Delete extended data options
   *
   * @since 1.0.0
   *
   * @param string    $sKey         - capability/role key
   * @param int|array $aExtendedKey - ALL_EXTENDED_DATA, or an array of keys of extended data about cap/role to delete. If
   *                                this param is equal to ALL_EXTENDED_DATA, then all extended data related to the key will be deleted.
   *
   * @return TRUE|FALSE : return TRUE if the extended data has been deleted, FALSE otherwise.
   *
   */
  private static function delete_extended_data( $sKey, $aExtendedKey ) {

    // if I don't have extended data to delete, return
    if ( empty( $aExtendedKey ) ) {
      return TRUE;
    }

    // get all extended data previously stored in DB
    $aExtendedData = get_option( self::OPTION_KEY );

    // if I received an array, delete specific extended keys
    if ( is_array( $aExtendedKey ) ) {
      foreach ( $aExtendedKey as $sKeyToDelete ) {

        // Delete key
        if ( isset( $aExtendedData[$sKey][$sKeyToDelete] ) ) {
          unset( $aExtendedData[$sKey][$sKeyToDelete] );
        }

      }
    }
    // if I received the 'delete all' command, delete all extended keys
    elseif ( $aExtendedKey == self::ALL_EXTENDED_DATA ) {
      unset ( $aExtendedData[$sKey] );
    }

    // save extended data to DB
    update_option( self::OPTION_KEY, $aExtendedData );

    return TRUE;

  }


  /**
   * Return the main role of a user in the whole WordPress system, with all extended data eventually associated to it.
   * For some reason, a user can have more roles, but for WordPress the main role is the first element in (WP_User)->roles.
   * This function respects this behaviour.
   *
   * @brief Return the role of a user
   *
   * @since 1.0.0
   *
   * @param int $iIDUser - user ID in WordPress environment
   *
   * @return array|WPDKError The main role of the user, or an instance of WPDKError object in case of an error.
   *
   */
  static function get_user_role( $iIDUser ) {

    // Check user
    $rCheck = WP_User::get_data_by( 'id', $iIDUser );
    if ( FALSE == $rCheck ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'User %d does not exist in system.', WPDK_TEXTDOMAIN ), $iIDUser ) );
    }

    // Get main user role
    $rUser      = new WP_User( $iIDUser );
    $aUserRoles = $rUser->roles;
    $sMainRole  = reset( $aUserRoles );

    // Get extended data about role
    $aRoles        = self::$cWpRoles->get_names();
    $aExtendedData = get_option( self::OPTION_KEY );
    $aFinalRole    = array();
    foreach ( $aRoles as $sRoleKey => $sValue ) {
      if ( $sRoleKey == $sMainRole ) {
        $aFinalRole[$sRoleKey]['display_label'] = $sValue;
        if ( is_array( $aExtendedData ) ) {
          if ( isset( $aExtendedData[self::ROLE_KEY_PREFIX . $sRoleKey] ) ) {
            foreach ( $aExtendedData[self::ROLE_KEY_PREFIX . $sRoleKey] as $sExKey => $sExValue ) {
              $aFinalRole[$sRoleKey][$sExKey] = $sExValue;
            }
          }
        }
        break; // return info only about this role
      }
    }

    return $aFinalRole;

  }


  /**
   * If $iMode param is equal to self::WP_STANDARD, directly returns output of get_names() method of WP_Roles object.
   * Else, return a multidimensional array with all actual roles in the whole WordPress system, and all extended data
   * eventually associated to them.
   * The basic format of this array is:
   *
   *     array(n) {
   *       ........
   *       ["administrator"]=>
   *       array(1) {
   *         ["display_label"]=>
   *         string(13) "Administrator"
   *       }
   *       .......
   *     }
   *
   * Every single main key is the role key/name, and indexes a further array, in which all extended data about
   * role are stored. This array has only one fixed key, 'display_label', equal to the label string shown by WordPress
   * related to the role. All others key are extended and dynamically handled data.
   *
   * @brief Return all roles
   *
   * @since 1.0.0
   *
   * @param int $iMode - (optional) return mode. If it is equal to self::WP_STANDARD, the method returns the standard
   *                   data from WP environment.
   *
   * @return array All roles in WordPress system, standard or extended, according to $iMode.
   *
   */
  static function get_all_roles( $iMode = 0 ) {

    // Get all roles
    $aRoles = self::$cWpRoles->get_names();

    // Return standard data, if requested
    if ( self::WP_STANDARD == $iMode ) {
      return $aRoles;
    }

    // Get extended data
    $aExtendedData = get_option( self::OPTION_KEY );

    // Build final array of roles merged with extended params
    foreach ( $aRoles as $sRoleKey => $sValue ) {
      $aFinalRoles[$sRoleKey]['display_label'] = $sValue;
      if ( is_array( $aExtendedData ) ) {
        if ( isset( $aExtendedData[self::ROLE_KEY_PREFIX . $sRoleKey] ) ) {
          foreach ( $aExtendedData[self::ROLE_KEY_PREFIX . $sRoleKey] as $sExKey => $sExValue ) {
            $aFinalRoles[$sRoleKey][$sExKey] = $sExValue;
          }
        }
      }
    }

    return $aFinalRoles;

  }

  /**
   * Return a multidimensional array with all actual caps in the whole WordPress system, and all extended data
   * eventually associated to them.
   * The basic format of this array is like this example (if caps ARE NOT grouped by role):
   *
   *    array(n) {
   *       .....
   *       ["switch_themes"]=>
   *       array(1) {
   *          ["grant"]=>
   *          bool(true)
   *       }
   *      .....
   *     ["writiness"]=>
   *      array(4) {
   *          ["grant"]=>
   *          bool(true)
   *          ["description"]=>
   *          string(24) "Descrizione di writiness"
   *          ["label"]=>
   *          string(18) "label di writiness"
   *          ["input"]=>
   *          string(31) "questa e una nuova cap"
   *      }
   *    }
   *
   * Every single main key is the cap key/name, and indexes a further array, in which all extended data about
   * cap are stored. This array has only one fixed key, 'grant', equal to the default grant related to the cap. All others
   * key are extended and dynamically handled data.
   * If the array of caps is grouped by role, the behaviour is equivalent; the only difference is that the first main key
   * is not the single cap, but the role the cap belongs to.
   *
   * @brief Return all caps
   *
   * @since 1.0.0
   *
   * @param int $iGroupedBy - whether or not return the array grouped by role. self::CAPS_NOT_GROUPED_BY_ROLE means
   *                        the array will be unidimensional; self::CAPS_GROUPED_BY_ROLE means the array will be grouped by single role.
   *
   * @return array All capabilities in WordPress system.
   *
   */
  static function get_all_caps( $iGroupedBy = self::CAPS_NOT_GROUPED_BY_ROLE ) {

    // Get all roles
    $aRoles = self::$cWpRoles->get_names();

    // foreach role, merge capabilities in a global array as requested
    $aAllCaps = array();
    foreach ( $aRoles as $sRoleKey => $sRoleLabel ) {
      $rRoleData = self::$cWpRoles->get_role( $sRoleKey );
      if ( self::CAPS_NOT_GROUPED_BY_ROLE == $iGroupedBy ) {
        $aAllCaps = array_merge( $aAllCaps, $rRoleData->capabilities );
      }
      else {
        $aAllCaps[$rRoleData->name] = $rRoleData->capabilities;
      }
    }

    // Get extended caps data
    $aExtendedCapsData = get_option( self::OPTION_KEY );

    // Build final array of capabilities merged with extended params
    $aFinalCaps = array();
    if ( self::CAPS_NOT_GROUPED_BY_ROLE == $iGroupedBy ) {
      foreach ( $aAllCaps as $sCapKey => $sCapValue ) {
        $aFinalCaps[$sCapKey]['grant'] = $sCapValue;
        if ( is_array( $aExtendedCapsData ) ) {
          if ( isset( $aExtendedCapsData[$sCapKey] ) ) {
            foreach ( $aExtendedCapsData[$sCapKey] as $sExKey => $sExValue ) {
              $aFinalCaps[$sCapKey][$sExKey] = $sExValue;
            }
          }
        }
      }
    }
    else {
      foreach ( $aAllCaps as $sRoleKey => $sValue ) {
        foreach ( $sValue as $sCapKey => $sCapValue ) {
          $aFinalCaps[$sRoleKey][$sCapKey]['grant'] = $sCapValue;
          if ( is_array( $aExtendedCapsData ) ) {
            if ( isset( $aExtendedCapsData[$sCapKey] ) ) {
              foreach ( $aExtendedCapsData[$sCapKey] as $sExKey => $sExValue ) {
                $aFinalCaps[$sRoleKey][$sCapKey][$sExKey] = $sExValue;
              }
            }
          }
        }
      }
    }

    return $aFinalCaps;

  }

}

/**
 * @class              WPDKRolesCaps
 * @copyright          Copyright (c) wpXtreme, Inc
 * @author             yuma - <g.achilli@wpxtre.me>
 * @date               2012-11-28
 * @version            0.8.1
 *
 * @deprecated Since 1.0.0.b4 - Nveere used
 *
 * WPDKRolesCaps class extends features of roles and capabilities in a WordPress environment.
 *
 * ## Overview
 * [draft]
 *
 */
class WPDKRolesCaps {

  //-------------------------------------------------------------------------------------------
  // Internal properties
  //-------------------------------------------------------------------------------------------

  /**
   * @brief WP_Roles private instance
   *
   * (private - static) The private instance of WP_Roles object used by this class. It is properly initialized
   * only through ::init method.
   *
   * @var object $cWpRoles
   *
   * @since 1.0.0
   */
  private static $cWpRoles;

  //-------------------------------------------------------------------------------------------
  // Internal constants
  //-------------------------------------------------------------------------------------------

  /**
   * @brief Caps in monodimensional array
   *
   * int - Used to return all capabilities in an unidimensional array
   *
   * @since 1.0.0
   */
  const CAPS_NOT_GROUPED_BY_ROLE = 1;

  /**
   * @brief Caps in multidimensional array
   *
   * int - Used to return all capabilities grouped by role
   *
   * @since 1.0.0
   */
  const CAPS_GROUPED_BY_ROLE = 2;

  /**
   * @brief Max length of role data
   *
   * int - Max length of role data ( don't confuse it with role description!!!, that can be an extension )
   *
   * @since 1.0.0
   */
  const MAX_LENGTH_OF_ROLE_DATA = 36;

  /**
   * @brief Option key for extended params
   *
   * string - Option key for storing extended params into db.
   *
   * @since 1.0.0
   */
  const OPTION_KEY = 'wpdk_roles_caps';

  /**
   * @brief Option prefix for role extended params
   *
   * string - Option prefix added to role key for distinguish it to cap with the same name.
   *
   * @since 1.0.0
   */
  const ROLE_KEY_PREFIX = 'wpdk_role-';

  /**
   * @brief For deleting all extended data about a key
   *
   * int - Used when I want to delete all extended data about a specific key.
   *
   * @since 1.0.0
   */
  const ALL_EXTENDED_DATA = 1;

  /**
   * @brief Return WP standard output
   *
   * int - Used for directly returning the WP standard whenever it needs.
   *
   * @since 1.0.0
   */
  const WP_STANDARD = 1;

  //-------------------------------------------------------------------------------------------
  // Methods
  //-------------------------------------------------------------------------------------------

  /**
   * Initialize static instance of this class.
   *
   * @brief Init instance
   *
   * @since 1.0.0
   *
   * @return bool TRUE if init operations are OK, FALSE otherwise
   *
   */
  static function init() {

    global $wp_roles;

    // Init of WP_Roles instance
    if ( !isset( $wp_roles ) ) {
      self::$cWpRoles = new WP_Roles;
    }
    else {
      self::$cWpRoles = $wp_roles;
    }

  }

  /**
   * Check if a capability exists in the whole WordPress system.
   *
   * @brief Does the cap exist?
   *
   * @since 1.0.0
   *
   * @param string $sCapName The capability name.
   *
   * @return bool
   *
   */
  static function cap_exists( $sCapName ) {

    return array_key_exists( $sCapName, self::get_all_caps() );

  }


  /**
   * Check if a role exists in the whole WordPress system.
   *
   * @brief Does the role exist?
   *
   * @since 1.0.0
   *
   * @param string $sRoleName The role name.
   *
   * @return bool
   *
   */
  static function role_exists( $sRoleName ) {

    return ( array_key_exists( $sRoleName, self::$cWpRoles->get_names() ) );

  }


  /**
   * Create a brand new role, with some new params that extends normal WordPress handling.
   *
   * @brief Create a brand new role
   *
   * @since 1.0.0
   *
   * @param string $sRoleName  - role name/key in DB and in the whole system
   * @param string $sRoleLabel - label of role in WordPress environment ( i.e. in user settings ).
   * @param array  $aNewData   (optional) array of new extended data related to this role. Default to array().
   *
   * @return mixed TRUE|WPDKError
   *
   */
  static function create_role( $sRoleName, $sRoleLabel, $aNewData = array() ) {

    // check about role syntax
    if ( empty( $sRoleName ) || empty( $sRoleLabel ) ) {
      return new WPDKError( 'wpdk_roles_caps', __( 'Role data cannot be empty.', WPDK_TEXTDOMAIN ) );
    }

    // check about role data length
    if (
      strlen( $sRoleName ) >= self::MAX_LENGTH_OF_ROLE_DATA || strlen( $sRoleLabel ) >= self::MAX_LENGTH_OF_ROLE_DATA
    ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Role data cannot be more than %s chars.', WPDK_TEXTDOMAIN ), self::MAX_LENGTH_OF_ROLE_DATA ) );
    }

    // role name/key can contain only letters, digits and some other chars
    if ( 1 == preg_match( '/[^a-zA-Z0-9_\-]+/', $sRoleName ) ) {
      return new WPDKError( 'wpdk_roles_caps', __( 'Role name can contain only letters, digits, _ and - chars.', WPDK_TEXTDOMAIN ) );
    }

    // this role must be UNIQ in the whole WP system
    $aRoles = self::$cWpRoles->get_names(); // Get all roles
    if ( TRUE == array_key_exists( $sRoleName, $aRoles ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( "Can't create two roles with the same name '%s'.", WPDK_TEXTDOMAIN ), $sRoleName ) );
    }

    // create the role
    self::$cWpRoles->add_role( $sRoleName, $sRoleLabel );

    // update options with extended data about role and return - role has a prefix to distinguish from cap
    return self::update_extended_data( self::ROLE_KEY_PREFIX . $sRoleName, $aNewData );

  }


  /**
   * Create a brand new capability for a role, with some new params that extends normal WordPress handling.
   *
   * @brief Create a brand new capability
   *
   * @since 1.0.0
   *
   * @param string $sCapName  - capability name/key in DB and in the whole system
   * @param string $sRole     - role the capability belongs to.
   * @param bool   $bCapValue (optional) boolean value of capability. Default to TRUE.
   * @param array  $aNewData  (optional) array of new extended data related to this cap. Default to array().
   *
   * @return mixed TRUE|WPDKError
   *
   */
  static function create_cap( $sCapName, $sRole, $bCapValue = TRUE, $aNewData = array() ) {

    // check about capability name syntax
    if ( empty( $sCapName ) ) {
      return new WPDKError( 'wpdk_roles_caps', __( 'Capability name cannot be empty.', WPDK_TEXTDOMAIN ) );
    }

    // capability name/key can contain only letters, digits and some other chars
    if ( 1 == preg_match( '/[^a-zA-Z0-9_\-]+/', $sCapName ) ) {
      return new WPDKError( 'wpdk_roles_caps', __( 'Capability name can contain only letters, digits, _ and - chars.', WPDK_TEXTDOMAIN ) );
    }

    // if role is unexistent, can't create a brand new cap
    $aRoles = self::$cWpRoles->get_names(); // Get all roles
    if ( FALSE == array_key_exists( $sRole, $aRoles ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( "Can't add a capability to the unexistent role %s.", WPDK_TEXTDOMAIN ), $sRole ) );
    }

    // this cap must be UNIQ in the WP system, regardless to role
    if ( TRUE == self::cap_exists( $sCapName ) ) {
      return new WPDKError( 'wpdk_roles_caps', __( "Can't add a capability with the same name of an already existent one.", WPDK_TEXTDOMAIN ) );
    }

    // create the cap
    self::$cWpRoles->add_cap( $sRole, $sCapName, $bCapValue );

    // update options with extended data about cap and return
    return self::update_extended_data( $sCapName, $aNewData );

  }


  /**
   * Set the main role of a WordPress user.
   *
   * @brief Set the main role of a user
   *
   * @since 1.0.0
   *
   * @param int    $iIDUser   - user ID in WordPress environment
   * @param string $sRoleName - role name/key
   *
   * @return mixed TRUE|WPDKError
   *
   */
  static function set_main_role_of_user( $iIDUser, $sRoleName ) {

    // Does the user exist?
    $rCheck = WP_User::get_data_by( 'id', $iIDUser );
    if ( FALSE == $rCheck ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'User %d does not exist in system.', WPDK_TEXTDOMAIN ), $iIDUser ) );
    }

    // Does the role exist?
    if ( FALSE == array_key_exists( $sRoleName, self::get_all_roles() ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Role %s does not exist in system.', WPDK_TEXTDOMAIN ), $sRoleName ) );
    }

    // Does the user have already the role as a main role?
    $aRole = self::get_user_role( $iIDUser );
    if ( is_array( $aRole ) ) {
      if ( $sRoleName == key( $aRole ) ) {
        return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'The main role of user "%s" is already "%s".', WPDK_TEXTDOMAIN ), $rCheck->user_login, $sRoleName ) );
      }
    }

    // Set the main role of the user
    $rUser = new WP_User( $iIDUser );
    $rUser->set_role( $sRoleName );

    // set_role method of WP_User returns void. So return TRUE
    return TRUE;

  }


  /**
   * Add an existing capability to a specific WordPress user
   *
   * @brief Add a cap to a user
   *
   * @since 1.0.0
   *
   * @param int    $iIDUser   - user ID in WordPress environment
   * @param string $sCapName  - capability name/key
   * @param bool   $bCapValue (optional) boolean value of capability. Default to TRUE.
   *
   * @return mixed TRUE|WPDKError
   *
   */
  static function add_cap_to_user( $iIDUser, $sCapName, $bCapValue = TRUE ) {

    // Get data about user
    $rCheck = WP_User::get_data_by( 'id', $iIDUser );
    if ( FALSE == $rCheck ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'User %d does not exist in system.', WPDK_TEXTDOMAIN ), $iIDUser ) );
    }

    // Does the cap exist?
    if ( FALSE == self::cap_exists( $sCapName ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Unable to add unexistent capability "%s" to user "%s".', WPDK_TEXTDOMAIN ), $sCapName, $rCheck->user_login ) );

    }

    $rUser = new WP_User( $iIDUser );

    // Does the user have already this cap?
    if ( TRUE == array_key_exists( $sCapName, $rUser->allcaps ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'User "%s" has already the capability "%s".', WPDK_TEXTDOMAIN ), $rCheck->user_login, $sCapName ) );

    }

    // add cap to user
    $rUser->add_cap( $sCapName, $bCapValue );

    // add_cap method of WP_User returns void. So return TRUE
    return TRUE;

  }


  /**
   * Delete a capability from a specific WordPress user
   * WARNING: with this method, I can ALSO DELETE A CAPABILITY FROM DEFAULT USERS, LIKE admin. Please use VERY
   * CAREFULLY this facility.
   *
   * @brief Delete a cap from user
   *
   * @since 1.0.0
   *
   * @param int    $iIDUser  - user ID in WordPress environment
   * @param string $sCapName - capability name/key
   *
   * @return mixed TRUE|WPDKError
   *
   */
  static function delete_cap_from_user( $iIDUser, $sCapName ) {

    // Get data about user
    $rCheck = WP_User::get_data_by( 'id', $iIDUser );
    if ( FALSE == $rCheck ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'User %d does not exist in system.', WPDK_TEXTDOMAIN ), $iIDUser ) );
    }

    $rUser = new WP_User( $iIDUser );

    // Does the user have this cap? If not, what do I want to delete?
    if ( FALSE == array_key_exists( $sCapName, $rUser->allcaps ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'User "%s" does not have the capability "%s".', WPDK_TEXTDOMAIN ), $rCheck->user_login, $sCapName ) );

    }

    // delete cap from user
    $rUser->remove_cap( $sCapName );

    // remove_cap method of WP_User returns void. So return TRUE
    return TRUE;

  }


  /**
   * Add an existing capability to a specific WordPress role
   *
   * @brief Add a cap to a role
   *
   * @since 1.0.0
   *
   * @param string $sRoleName - role name/key that receive the cap
   * @param string $sCapName  - capability name/key
   * @param bool   $bCapValue (optional) boolean value of capability. Default to TRUE.
   *
   * @return mixed TRUE|WPDKError
   *
   */
  static function add_cap_to_role( $sRoleName, $sCapName, $bCapValue = TRUE ) {

    // Does the role exist?
    if ( FALSE == array_key_exists( $sRoleName, self::get_all_roles() ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Role %s does not exist in system.', WPDK_TEXTDOMAIN ), $sRoleName ) );
    }

    // Does the cap exist?
    if ( FALSE == self::cap_exists( $sCapName ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Unable to add unexistent capability "%s" to role "%s".', WPDK_TEXTDOMAIN ), $sCapName, $sRoleName ) );

    }

    // Does the role have already this cap?
    if ( TRUE == array_key_exists( $sCapName, self::get_caps_of_role( $sRoleName ) ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Role "%s" has already the capability "%s".', WPDK_TEXTDOMAIN ), $sRoleName, $sCapName ) );

    }

    // add cap to role
    self::$cWpRoles->add_cap( $sRoleName, $sCapName, $bCapValue );

    // add_cap method of WP_Roles returns void. So return TRUE
    return TRUE;

  }


  /**
   * Delete an existing capability from a specific WordPress role.
   * WARNING: with this method, I can ALSO DELETE A CAPABILITY FROM DEFAULT ROLES, LIKE administrator. Please use VERY
   * CAREFULLY this facility.
   *
   * @brief Delete a cap from a role
   *
   * @since 1.0.0
   *
   * @param string $sRoleName - role name/key that will drop the cap
   * @param string $sCapName  - capability name/key
   *
   * @return mixed TRUE|WPDKError
   *
   */
  static function delete_cap_from_role( $sRoleName, $sCapName ) {

    // Does the role exist?
    if ( FALSE == array_key_exists( $sRoleName, self::get_all_roles() ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Role %s does not exist in system.', WPDK_TEXTDOMAIN ), $sRoleName ) );
    }

    // Does the cap exist?
    if ( FALSE == self::cap_exists( $sCapName ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Unable to delete unexistent capability "%s" to role "%s".', WPDK_TEXTDOMAIN ), $sCapName, $sRoleName ) );

    }

    // Does the role have already this cap? If not, what do I want to delete?
    if ( FALSE == array_key_exists( $sCapName, self::get_caps_of_role( $sRoleName ) ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Role "%s" has not the capability "%s".', WPDK_TEXTDOMAIN ), $sRoleName, $sCapName ) );

    }

    // delete cap from role
    self::$cWpRoles->remove_cap( $sRoleName, $sCapName );

    // remove_cap method of WP_Roles returns void. So return TRUE
    return TRUE;

  }


  /**
   * Delete an existing WordPress role.
   * WARNING: with this method, I can ALSO COMPLETELY DELETE DEFAULT ROLES, LIKE administrator. Please use VERY
   * CAREFULLY this facility.
   *
   * @brief Delete a role
   *
   * @since 1.0.0
   *
   * @param string $sRoleName - role name/key to delete
   *
   * @return mixed TRUE|WPDKError
   *
   */
  static function delete_role( $sRoleName ) {

    // Does the role exist?
    if ( FALSE == array_key_exists( $sRoleName, self::get_all_roles() ) ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'Role %s does not exist in system.', WPDK_TEXTDOMAIN ), $sRoleName ) );
    }

    // delete role from system, with all its associated capabilities
    self::$cWpRoles->remove_role( $sRoleName );

    // delete also all extended data about role
    return self::delete_extended_data( self::ROLE_KEY_PREFIX . $sRoleName, self::ALL_EXTENDED_DATA );

  }


  /**
   * Set extended data about a specific role, or update extended data for the same key.
   *
   * @since 1.0.0
   *
   * @brief Set extended data about a role
   *
   * @param string $sRoleName - role name/key to get data of.
   * @param array  $aNewData  array of new extended data related to this role.
   *
   * @return TRUE|FALSE : if the role is in, return TRUE if extended data has been stored, FALSE otherwise.
   *
   */
  static function set_extended_role_data( $sRoleName, $aNewData ) {

    $aAllRoles = self::get_all_roles();

    if ( array_key_exists( $sRoleName, $aAllRoles ) ) {
      return self::update_extended_data( self::ROLE_KEY_PREFIX . $sRoleName, $aNewData );
    }
    else {
      return FALSE;
    }

  }


  /**
   * Delete extended data about a specific role.
   *
   * @brief Delete extended data of a role
   *
   * @since 1.0.0
   *
   * @param string    $sRoleName    - role name/key to delete extended data of.
   * @param int|array $aExtendedKey - ALL_EXTENDED_DATA, or an array of extended keys to delete. If this param is equal
   *                                to ALL_EXTENDED_DATA, then all extended data related to the role will be deleted.
   *
   * @return TRUE|FALSE : if the role is in, return TRUE if extended data has been delete, FALSE otherwise.
   *
   */
  static function delete_extended_role_data( $sRoleName, $aExtendedKey ) {

    $aAllRoles = self::get_all_roles();

    if ( array_key_exists( $sRoleName, $aAllRoles ) ) {
      return self::delete_extended_data( self::ROLE_KEY_PREFIX . $sRoleName, $aExtendedKey );
    }
    else {
      return FALSE;
    }

  }


  /**
   * Set extended data about a specific capability, or update extended data for the same key.
   *
   * @brief Set extended data of a cap
   *
   * @since 1.0.0
   *
   * @param string $sCapName - capability name/key to get data of.
   * @param array  $aNewData array of new extended data related to this cap.
   *
   * @return TRUE|FALSE : if the capability is in, return TRUE if extended data has been stored, FALSE otherwise.
   *
   */
  static function set_extended_cap_data( $sCapName, $aNewData ) {

    if ( self::cap_exists( $sCapName ) ) {
      return self::update_extended_data( $sCapName, $aNewData );
    }
    else {
      return FALSE;
    }

  }


  /**
   * Delete extended data about a specific capability.
   *
   * @brief Delete extended data of a cap
   *
   * @since 1.0.0
   *
   * @param string    $sCapName     - capability name/key to delete extended data of.
   * @param int|array $aExtendedKey - ALL_EXTENDED_DATA, or an array of extended keys to delete. If this param is equal
   *                                to ALL_EXTENDED_DATA, then all extended data related to the capability will be deleted.
   *
   * @return TRUE|FALSE : if the capability is in, return TRUE if its extended data has been deleted, FALSE otherwise.
   *
   */
  static function delete_extended_cap_data( $sCapName, $aExtendedKey ) {

    if ( self::cap_exists( $sCapName ) ) {
      return self::delete_extended_data( $sCapName, $aExtendedKey );
    }
    else {
      return FALSE;
    }

  }


  /**
   * Get all capabilities data about a specific role, including extended one.
   *
   * @brief Get all caps of a role
   *
   * @since 1.0.0
   *
   * @param string $sRoleName - role to get data of.
   *
   * @return mixed array|FALSE : if the role is in, return an array with all data about its capabilities
   *
   */
  static function get_caps_of_role( $sRoleName ) {

    $aAllCaps = self::get_all_caps( self::CAPS_GROUPED_BY_ROLE );

    if ( array_key_exists( $sRoleName, $aAllCaps ) ) {
      return $aAllCaps[$sRoleName];
    }
    else {
      return FALSE;
    }

  }


  /**
   * Get all capabilities data about a specific user, including extended one.
   *
   * @brief Get all caps of a user
   *
   * @since 1.0.0
   *
   * @param int $iIDUser - user ID in WordPress environment
   *
   * @return mixed array|FALSE : if the user exists, return an array with all data about its capabilities
   *
   */
  static function get_caps_of_user( $iIDUser ) {

    // Check if user exists
    $rCheck = WP_User::get_data_by( 'id', $iIDUser );
    if ( FALSE == $rCheck ) {
      return FALSE;
    }

    $rUser = new WP_User( $iIDUser );

    // Get all caps of user and merge them with related extended data
    $aAllCaps   = self::get_all_caps();
    $aFinalCaps = array();
    foreach ( $rUser->allcaps as $sCapKey => $sCapValue ) {
      if ( isset( $aAllCaps[$sCapKey] ) ) {
        $aFinalCaps[$sCapKey] = $aAllCaps[$sCapKey];
      }
      // Overwrite generic capability grant with specific capability grant about this user
      $aFinalCaps[$sCapKey]['grant'] = $sCapValue;
    }

    return $aFinalCaps;

  }


  /**
   * Get all data about a specific role, including extended one.
   *
   * @brief Get all data about a role
   *
   * @since 1.0.0
   *
   * @param string $sRoleName - role name/key to get data of.
   *
   * @return mixed array|FALSE : if the role is in, return an array with all data about it
   *
   */
  static function get_role_data( $sRoleName ) {

    $aAllRoles = self::get_all_roles();

    if ( array_key_exists( $sRoleName, $aAllRoles ) ) {
      return $aAllRoles[$sRoleName];
    }
    else {
      return FALSE;
    }

  }


  /**
   * Get all data about a specific capability, including extended one.
   *
   * @brief Get all data about a cap
   *
   * @since 1.0.0
   *
   * @param string $sCapName - capability name/key to get data of.
   *
   * @return mixed array|FALSE : if the capability is in, return an array with all data about it
   *
   */
  static function get_cap_data( $sCapName ) {

    $aAllCaps = self::get_all_caps( self::CAPS_NOT_GROUPED_BY_ROLE );

    if ( array_key_exists( $sCapName, $aAllCaps ) ) {
      return $aAllCaps[$sCapName];
    }
    else {
      return FALSE;
    }

  }


  /**
   * Update options about extended data, with checks.
   *
   * @brief Update extended data options
   *
   * @since 1.0.0
   *
   * @param string $sKey     - capability/role key
   * @param array  $aNewData - array of new data about cap/role to store
   *
   * @return TRUE|FALSE : return TRUE if the extended data are valid and update is OK, FALSE otherwise.
   *
   */
  private static function update_extended_data( $sKey, $aNewData ) {

    // if I don't have extended data to update, return
    if ( empty( $aNewData ) ) {
      return TRUE;
    }

    // get all extended data previously stored in DB
    $aExtendedData = get_option( self::OPTION_KEY );

    foreach ( $aNewData as $sEnKey => $sEnValue ) {

      // Check before update
      if ( is_numeric( $sEnKey ) ) {
        continue;
      }

      // update or add new data to key
      $aExtendedData[$sKey][$sEnKey] = $sEnValue;

    }

    // save extended data to DB
    update_option( self::OPTION_KEY, $aExtendedData );

    return TRUE;

  }


  /**
   * Delete options about extended data, with checks.
   *
   * @brief Delete extended data options
   *
   * @since 1.0.0
   *
   * @param string    $sKey         - capability/role key
   * @param int|array $aExtendedKey - ALL_EXTENDED_DATA, or an array of keys of extended data about cap/role to delete. If
   *                                this param is equal to ALL_EXTENDED_DATA, then all extended data related to the key will be deleted.
   *
   * @return TRUE|FALSE : return TRUE if the extended data has been deleted, FALSE otherwise.
   *
   */
  private static function delete_extended_data( $sKey, $aExtendedKey ) {

    // if I don't have extended data to delete, return
    if ( empty( $aExtendedKey ) ) {
      return TRUE;
    }

    // get all extended data previously stored in DB
    $aExtendedData = get_option( self::OPTION_KEY );

    // if I received an array, delete specific extended keys
    if ( is_array( $aExtendedKey ) ) {
      foreach ( $aExtendedKey as $sKeyToDelete ) {

        // Delete key
        if ( isset( $aExtendedData[$sKey][$sKeyToDelete] ) ) {
          unset( $aExtendedData[$sKey][$sKeyToDelete] );
        }

      }
    }
    // if I received the 'delete all' command, delete all extended keys
    elseif ( $aExtendedKey == self::ALL_EXTENDED_DATA ) {
      unset ( $aExtendedData[$sKey] );
    }

    // save extended data to DB
    update_option( self::OPTION_KEY, $aExtendedData );

    return TRUE;

  }


  /**
   * Return the main role of a user in the whole WordPress system, with all extended data eventually associated to it.
   * For some reason, a user can have more roles, but for WordPress the main role is the first element in (WP_User)->roles.
   * This function respects this behaviour.
   *
   * @brief Return the role of a user
   *
   * @since 1.0.0
   *
   * @param int $iIDUser - user ID in WordPress environment
   *
   * @return array|WPDKError The main role of the user, or an instance of WPDKError object in case of an error.
   *
   */
  static function get_user_role( $iIDUser ) {

    // Check user
    $rCheck = WP_User::get_data_by( 'id', $iIDUser );
    if ( FALSE == $rCheck ) {
      return new WPDKError( 'wpdk_roles_caps', sprintf( __( 'User %d does not exist in system.', WPDK_TEXTDOMAIN ), $iIDUser ) );
    }

    // Get main user role
    $rUser      = new WP_User( $iIDUser );
    $aUserRoles = $rUser->roles;
    $sMainRole  = reset( $aUserRoles );

    // Get extended data about role
    $aRoles        = self::$cWpRoles->get_names();
    $aExtendedData = get_option( self::OPTION_KEY );
    $aFinalRole    = array();
    foreach ( $aRoles as $sRoleKey => $sValue ) {
      if ( $sRoleKey == $sMainRole ) {
        $aFinalRole[$sRoleKey]['display_label'] = $sValue;
        if ( is_array( $aExtendedData ) ) {
          if ( isset( $aExtendedData[self::ROLE_KEY_PREFIX . $sRoleKey] ) ) {
            foreach ( $aExtendedData[self::ROLE_KEY_PREFIX . $sRoleKey] as $sExKey => $sExValue ) {
              $aFinalRole[$sRoleKey][$sExKey] = $sExValue;
            }
          }
        }
        break; // return info only about this role
      }
    }

    return $aFinalRole;

  }


  /**
   * If $iMode param is equal to self::WP_STANDARD, directly returns output of get_names() method of WP_Roles object.
   * Else, return a multidimensional array with all actual roles in the whole WordPress system, and all extended data
   * eventually associated to them.
   * The basic format of this array is:
   *
   *     array(n) {
   *       ........
   *       ["administrator"]=>
   *       array(1) {
   *         ["display_label"]=>
   *         string(13) "Administrator"
   *       }
   *       .......
   *     }
   *
   * Every single main key is the role key/name, and indexes a further array, in which all extended data about
   * role are stored. This array has only one fixed key, 'display_label', equal to the label string shown by WordPress
   * related to the role. All others key are extended and dynamically handled data.
   *
   * @brief Return all roles
   *
   * @since 1.0.0
   *
   * @param int $iMode - (optional) return mode. If it is equal to self::WP_STANDARD, the method returns the standard
   *                   data from WP environment.
   *
   * @return array All roles in WordPress system, standard or extended, according to $iMode.
   *
   */
  static function get_all_roles( $iMode = 0 ) {

    // Get all roles
    $aRoles = self::$cWpRoles->get_names();

    // Return standard data, if requested
    if ( self::WP_STANDARD == $iMode ) {
      return $aRoles;
    }

    // Get extended data
    $aExtendedData = get_option( self::OPTION_KEY );

    // Build final array of roles merged with extended params
    foreach ( $aRoles as $sRoleKey => $sValue ) {
      $aFinalRoles[$sRoleKey]['display_label'] = $sValue;
      if ( is_array( $aExtendedData ) ) {
        if ( isset( $aExtendedData[self::ROLE_KEY_PREFIX . $sRoleKey] ) ) {
          foreach ( $aExtendedData[self::ROLE_KEY_PREFIX . $sRoleKey] as $sExKey => $sExValue ) {
            $aFinalRoles[$sRoleKey][$sExKey] = $sExValue;
          }
        }
      }
    }

    return $aFinalRoles;

  }

  /**
   * Return a multidimensional array with all actual caps in the whole WordPress system, and all extended data
   * eventually associated to them.
   * The basic format of this array is like this example (if caps ARE NOT grouped by role):
   *
   *    array(n) {
   *       .....
   *       ["switch_themes"]=>
   *       array(1) {
   *          ["grant"]=>
   *          bool(true)
   *       }
   *      .....
   *     ["writiness"]=>
   *      array(4) {
   *          ["grant"]=>
   *          bool(true)
   *          ["description"]=>
   *          string(24) "Descrizione di writiness"
   *          ["label"]=>
   *          string(18) "label di writiness"
   *          ["input"]=>
   *          string(31) "questa e una nuova cap"
   *      }
   *    }
   *
   * Every single main key is the cap key/name, and indexes a further array, in which all extended data about
   * cap are stored. This array has only one fixed key, 'grant', equal to the default grant related to the cap. All others
   * key are extended and dynamically handled data.
   * If the array of caps is grouped by role, the behaviour is equivalent; the only difference is that the first main key
   * is not the single cap, but the role the cap belongs to.
   *
   * @brief Return all caps
   *
   * @since 1.0.0
   *
   * @param int $iGroupedBy - whether or not return the array grouped by role. self::CAPS_NOT_GROUPED_BY_ROLE means
   *                        the array will be unidimensional; self::CAPS_GROUPED_BY_ROLE means the array will be grouped by single role.
   *
   * @return array All capabilities in WordPress system.
   *
   */
  static function get_all_caps( $iGroupedBy = self::CAPS_NOT_GROUPED_BY_ROLE ) {

    // Get all roles
    $aRoles = self::$cWpRoles->get_names();

    // foreach role, merge capabilities in a global array as requested
    $aAllCaps = array();
    foreach ( $aRoles as $sRoleKey => $sRoleLabel ) {
      $rRoleData = self::$cWpRoles->get_role( $sRoleKey );
      if ( self::CAPS_NOT_GROUPED_BY_ROLE == $iGroupedBy ) {
        $aAllCaps = array_merge( $aAllCaps, $rRoleData->capabilities );
      }
      else {
        $aAllCaps[$rRoleData->name] = $rRoleData->capabilities;
      }
    }

    // Get extended caps data
    $aExtendedCapsData = get_option( self::OPTION_KEY );

    // Build final array of capabilities merged with extended params
    $aFinalCaps = array();
    if ( self::CAPS_NOT_GROUPED_BY_ROLE == $iGroupedBy ) {
      foreach ( $aAllCaps as $sCapKey => $sCapValue ) {
        $aFinalCaps[$sCapKey]['grant'] = $sCapValue;
        if ( is_array( $aExtendedCapsData ) ) {
          if ( isset( $aExtendedCapsData[$sCapKey] ) ) {
            foreach ( $aExtendedCapsData[$sCapKey] as $sExKey => $sExValue ) {
              $aFinalCaps[$sCapKey][$sExKey] = $sExValue;
            }
          }
        }
      }
    }
    else {
      foreach ( $aAllCaps as $sRoleKey => $sValue ) {
        foreach ( $sValue as $sCapKey => $sCapValue ) {
          $aFinalCaps[$sRoleKey][$sCapKey]['grant'] = $sCapValue;
          if ( is_array( $aExtendedCapsData ) ) {
            if ( isset( $aExtendedCapsData[$sCapKey] ) ) {
              foreach ( $aExtendedCapsData[$sCapKey] as $sExKey => $sExValue ) {
                $aFinalCaps[$sRoleKey][$sCapKey][$sExKey] = $sExValue;
              }
            }
          }
        }
      }
    }

    return $aFinalCaps;

  }

}

/// @endcond