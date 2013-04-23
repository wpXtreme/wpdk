<?php
/// @cond private

/*
 * [DRAFT]
 *
 * THE FOLLOWING CODE IS A DRAFT. FEEL FREE TO USE IT TO MAKE SOME EXPERIMENTS, BUT DO NOT USE IT IN ANY CASE IN
 * PRODUCTION ENVIRONMENT. ALL CLASSES AND RELATIVE METHODS BELOW CAN CHNAGE IN THE FUTURE RELEASES.
 *
 */

/**
 * @class              __WPDKUser__
 *
 * Gestisce un estensione di un utente WordPress, dalla registrazione al login fine all'aggiunti di campi extra come
 * indirizzo, città, etc...
 *
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               07/12/11
 * @version            1.0
 *
 * @deprecated         Use New WPDKUser instead
 *
 * @todo               NOTA BENE:
 *                     alcuni metodi sono stati inseriti qui per errore. Essi sono specifici di wpXtreme Plugin e non
 *                     dovrebbero trovarsi in questa che è una libreria general purpose
 *
 * @todo               Terminare gli hook principali di login registrazione utente, cancellazione
 * @todo               creare metodo per la security key
 * @todo               Gestire in visualizzazione e nel codice il 'wpdk_user_internal-status_message'
 *
 * USER META
 * =========
 *
 * Questi hanno un prefisso per distinguere quelli interni da quelli custom estesi
 *
 * wpdk_user_internal-
 * wpdk_user_custom-
 *
 * @internal
 *
 * wpdk_user_internal-count_success_login
 * wpdk_user_internal-count_wrong_login
 * wpdk_user_internal-time_last_login
 * wpdk_user_internal-time_last_logout
 * wpdk_user_internal-status [confirmed | disabled | locked ]
 * wpdk_user_internal-status_message Es. [ Locked because 5 login wrong ]
 *
 *
 */

/**
 * @addtogroup filters Filters
 *    Documentazione di tutti i filtri disponibili
 * @{
 * @defgroup user_helper_filters Nel file wpdk-user-helper.php
 * @ingroup filters
 *    Filters in file wpdk-user-helper.php
 * @}
 */

/**
 * @addtogroup actions Actions
 *    Documentazione di tutte le azioni disponibili
 * @{
 * @defgroup user_helper_actions Nel file wpdk-user-helper.php
 * @ingroup actions
 *    Actions in file wpdk-user-helper.php
 * @}
 */

class __WPDKUser__ {

  const kInternalPrefix = 'wpdk_user_internal-';
  const kCustomPrefix   = 'wpdk_user_custom-';

  const STATUS_CONFIRMED = 'confirmed';
  const STATUS_DISABLED  = 'disabled';
  const STATUS_LOCKED    = 'locked';

  // -----------------------------------------------------------------------------------------------------------------
  // Static values
  // -----------------------------------------------------------------------------------------------------------------

  // -----------------------------------------------------------------------------------------------------------------
  // Database
  // -----------------------------------------------------------------------------------------------------------------


  // -----------------------------------------------------------------------------------------------------------------
  // Init
  // -----------------------------------------------------------------------------------------------------------------

  public static function init() {
    /* Main hook for common check in front end. */
    add_action( 'wp_head', array(
                                __CLASS__,
                                'wp_head'
                           ) );

    /* Hook on Login. */
    add_action( 'wp_login', array(
                                 __CLASS__,
                                 'wp_login'
                            ) );
    add_action( 'wp_logout', array(
                                  __CLASS__,
                                  'wp_logout'
                             ) );
    add_action( 'wp_login_failed', array(
                                        __CLASS__,
                                        'wp_login_failed'
                                   ) );

    /* includes/wp_insert_user() Nuovo Utente registrato  */
    add_action( 'user_register', array(
                                      __CLASS__,
                                      'user_register'
                                 ) );

    /* includes/wp_insert_user() Utente già registrato quindi aggiornamento dati */
    add_action( 'profile_update', array(
                                       __CLASS__,
                                       'profile_update'
                                  ), 10, 2 );
    add_action( 'delete_user', array(
                                    __CLASS__,
                                    'delete_user'
                               ) );
    add_action( 'deleted_user', array(
                                     __CLASS__,
                                     'deleted_user'
                                ) );

    /* Backend edit user update */
    add_action( 'personal_options_update', array(
                                                __CLASS__,
                                                'personal_options_update'
                                           ) );
    add_action( 'edit_user_profile_update', array(
                                                 __CLASS__,
                                                 'edit_user_profile_update'
                                            ) );

    /* Extends Users List Table */
    add_filter( 'manage_users_columns', array(
                                             __CLASS__,
                                             'manage_users_columns'
                                        ) );
    add_action( 'manage_users_custom_column', array(
                                                   __CLASS__,
                                                   'manage_users_custom_column'
                                              ), 10, 3 );

    /* Extends User edit profile */
    add_action( 'edit_user_profile', array(
                                          __CLASS__,
                                          'edit_user_profile'
                                     ) );
    add_action( 'show_user_profile', array(
                                          __CLASS__,
                                          'show_user_profile'
                                     ) );

    /* Disable and locking featured */
    add_filter( 'wp_authenticate_user', array(
                                             __CLASS__,
                                             'wp_authenticate_user'
                                        ), 1 );

    add_filter( 'user_contactmethods', array(
                                            __CLASS__,
                                            'user_contactmethods'
                                       ) );
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Internal filters
  // -----------------------------------------------------------------------------------------------------------------

  /// Filter for Wrong Sign In
  public static function wpdk_signin_error( $feedback ) {
    $message  = __( '<strong>Warning!</strong> Wrong username or password. Please check careful your data or Reset your Password if you do not remember it!', WPDK_TEXTDOMAIN );
    $alert    = new WPDKTwitterBootstrapAlert( 'wrong-login', $message, WPDKTwitterBootstrapAlertType::ALERT );
    $feedback = $alert->alert( false );
    return $feedback;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // WordPress hook
  // -----------------------------------------------------------------------------------------------------------------

  public static function wp_head() {

    /**
     * @defgroup wpdk_will_did_signin wpdk_will_did_signin
     * @{
     *
     * @ingroup  user_helper_filters
     *           Used for bypass wpdk sign in
     *
     * @param bool $continue Default TRUE
     *
     * @return bool Default TRUE, FALSE for bypass sign in
     *
     * @}
     */
    $continue = apply_filters( 'wpdk_will_did_signin', true );

    /* Check for Sign In. */
    if ( $continue && isset( $_POST['wpdk_action_login'] ) && 'login' == $_POST['wpdk_action_login'] ) {
      $remember = isset( $_POST['wpdk_login_remember'] );
      $result   = WPDKUser::signIn( $_POST['wpdk_user_email'], $_POST['wpdk_user_password'], $remember );
      if ( false === $result ) {
        add_filter( 'wpdk_signin_error', array(
                                              __CLASS__,
                                              'wpdk_signin_error'
                                         ) );
        return false;
      }
      else {
        /* Per default ridirezione sulla home page del sito. */
        $blog_url = get_bloginfo( 'url' );
        wp_safe_redirect( $blog_url );
        exit();
      }
    }

    /**
     * @defgroup wpdk_will_did_signout wpdk_will_did_signout
     * @{
     *
     * @ingroup  user_helper_filters
     *           Used for bypass wpdk sign out
     *
     * @param bool $continue Default TRUE
     *
     * @return bool Default TRUE, FALSE for bypass sign out
     *
     * @}
     */
    $continue = apply_filters( 'wpdk_will_did_signout', true );

    /* Check for Sign out. */
    if ( $continue && isset( $_GET['wpdk_logout'] ) ) {
      self::signout();
    }
  }

  /**
   * Non utilizzato per adesso in quanto non avendo la lista "pulita" contenuta in $contacts non è possibile fare una
   * serie di checkbox che indicano i campi da non mostrare. Per averla, infatti, dovrei usare proprio questo filtro
   * oppure la funzione privata/interna _wp_get_user_contactmethods() che, tuttavia, a sua volta richiama appunto
   * questo filtro; ergo, non sono gestibili.
   *
   * @todo Not used yet
   *
   * @param array $contacts
   *
   * @return mixed
   */
  public static function user_contactmethods( $contacts ) {
    return $contacts;
  }

  /**
   * Eseguito quando un utente viene autenticato. Viene usato per gestire i lock sugli utenti
   *
   *
   * @param WP_User $user Oggeto WP_User
   *
   * @return WP_Error|WP_User Restituisce l'oggetto WP_User o un WP_Error in caso di blocco o errore
   */
  public static function wp_authenticate_user( $user ) {
    if ( is_wp_error( $user ) ) {
      return $user;
    }

    if ( $user->get( 'wpdk_user_internal-status' ) == self::STATUS_DISABLED ) {
      return new WP_Error( 'wpdk_error-login_user_disabled', __( 'Login not allowed because this user is disabled.', WPDK_TEXTDOMAIN ) );
    }

    if ( $user->get( 'wpdk_user_internal-status' ) == self::STATUS_LOCKED ) {
      return new WP_Error( 'wpdk_error-login_user_locked', __( 'Login not allowed because this user is locked.', WPDK_TEXTDOMAIN ) );
    }

    return $user;
  }

  /**
   * Login utente
   *
   *
   * @param string  $user_login user login
   * @param WP_User $user
   */
  public static function wp_login( $user_login, $user = null ) {
    if ( is_null( $user ) ) {
      $user = get_user_by( 'login', $user_login );
    }
    $count = absint( $user->get( 'wpdk_user_internal-count_success_login' ) );
    if ( empty( $count ) ) {
      $count = 0;
    }
    update_user_meta( $user->ID, 'wpdk_user_internal-count_success_login', $count + 1 );

    /* @todo Se l'utente si autentica correttamente, azzero il contatore dei login sbagliati: aggiungere filtro e/o impostazioni */
    update_user_meta( $user->ID, 'wpdk_user_internal-count_wrong_login', 0 );
    update_user_meta( $user->ID, 'wpdk_user_internal-time_last_login', time() );
  }

  /**
   * Logout
   *
   */
  public static function wp_logout() {
    $user_id = get_current_user_id();
    update_user_meta( $user_id, 'wpdk_user_internal-time_last_logout', time() );
  }

  /**
   * Chiamata da WordPress quando un utente sbaglia il login
   *
   * @param $user_login
   *
   * @return mixed
   */
  public static function wp_login_failed( $user_login ) {

//    if ( empty( $user_login ) || !WPXtremeConfiguration::init()->enhancers->security->users->bCountLoginAttempts ) {
//      return;
//    }

    if ( empty( $user_login ) ) {
      return;
    }

    $user  = get_user_by( 'login', $user_login );
    $count = absint( $user->get( 'wpdk_user_internal-count_wrong_login' ) );
    if ( empty( $count ) ) {
      $count = 0;
    }

    /**
     * @defgroup wpdk_user_count_wrong_login wpdk_user_count_wrong_login
     * @{
     *
     * @ingroup  user_helper_filters
     *           Called when a login user was wrong and your count is increase
     *
     * @param int $count   Wrong login count + 1
     * @param int $id_user User ID
     *
     * @return   int Wrong login count
     *
     * @}
     */
    $count = apply_filters( 'wpdk_user_count_wrong_login', ( $count + 1 ), $user->ID );

    update_user_meta( $user->ID, 'wpdk_user_internal-count_wrong_login', $count );

    /* Recupero dalle impostazioni quanti login sbagliati l'utente può fare */
    //$attempts = absint( WPXtremeConfiguration::init()->enhancers->security->users->iMaxNumberOfWrongLoginAttempts );
    $attempts = 10;
    if ( $count >= $attempts ) {

      update_user_meta( $user->ID, 'wpdk_user_internal-status', self::STATUS_LOCKED );

      /**
       * @defgroup wpdk_user_status wpdk_user_status
       * @{
       *
       * @ingroup  user_helper_actions
       *           Called when a user status is change
       *
       * @param int    $id_user  User ID
       * @param string $status   Status
       *
       * @}
       */
      do_action( 'wpdk_user_status', $user->ID, self::STATUS_LOCKED );
    }
  }

  /**
   * Called when an user is created
   *
   * @todo Not used yet
   *
   * @param int $id_user User ID
   */
  public static function user_register( $id_user ) {

  }

  /**
   * Called when updating user data after an insert
   *
   * @todo Not used yet
   *
   * @param int   $id_user       User ID
   * @param array $old_user_data User data
   */
  public static function profile_update( $id_user, $old_user_data ) {

  }

  /**
   * Called when your owner profile is updated
   *
   * @param int $id_user User iD
   */
  public static function personal_options_update( $id_user ) {
    /* Same for other users, see below */
    self::edit_user_profile_update( $id_user );
  }

  /**
   * Called when updating user data
   *
   * @param int $id_user User ID
   */
  public static function edit_user_profile_update( $id_user ) {
    if ( !current_user_can( 'edit_user', $id_user ) ) {
      return false;
    }

    /* Questi sono i campi registrati nelle impostazioni, devo però impostare i valori nella user meta. */
    /* @todo Non è proprio corretto che il wpdk indirizzi il plugin wpXtreme, dando anche per scontato che sia stato attivato */
    $items = WPXtremeConfiguration::init()->enhancers->users->extra_fields->fields;
    if ( !empty( $items ) ) {
      foreach ( $items as $key => $item ) {
        if ( isset( $item['name'] ) ) {
          /* @todo Qui andrebbero sanitizzati i valori in base al tipo del campo. */
          $value = esc_attr( $_POST[$item['name']] );
          update_user_meta( $id_user, $item['name'], $value );
        }
      }
    }

    update_user_meta( $id_user, 'bill_town', $_POST['bill_town'] );

  }

  /**
   * L'utente WordPress sta per essere eliminato. Analizzando il codice è comunque non possibile impedire tramite
   * questa action che l'utente venga eliminato.
   *
   * @param int $id_user User ID
   */
  public static function delete_user( $id_user ) {

  }

  /**
   * L'utente WordPress è stato eliminato
   *
   * @param int $id_user User ID
   */
  public static function deleted_user( $id_user ) {

  }

  /**
   * Altera le colonne della List table degli utenti di WordPress
   *
   * @param array $columns Elenco Key value pairs delle colonne
   *
   * @return array
   */
  public static function manage_users_columns( $columns ) {

    $columns['wpdk_user_internal-time_last_login']     = __( 'Last login', WPDK_TEXTDOMAIN );
    $columns['wpdk_user_internal-time_last_logout']    = __( 'Last logout', WPDK_TEXTDOMAIN );
    $columns['wpdk_user_internal-count_success_login'] = __( '# Login', WPDK_TEXTDOMAIN );
    $columns['wpdk_user_internal-count_wrong_login']   = __( '# Wrong', WPDK_TEXTDOMAIN );
    $columns['wpdk_user_internal-status']              = __( 'Enabled', WPDK_TEXTDOMAIN );

    return $columns;
  }

  /**
   * Contenuto (render) di una colonna
   *
   *
   * @param mixed  $value
   * @param string $column_name Column name
   * @param int    $user_id     User ID
   */
  public static function manage_users_custom_column( $value, $column_name, $user_id ) {
    $result = new WP_User( $user_id );
    $value  = $result->get( $column_name );

    if ( $column_name == 'wpdk_user_internal-time_last_login' || $column_name == 'wpdk_user_internal-time_last_logout'
    ) {
      if ( !empty( $value ) ) {
        $value = WPDKDateTime::timeNewLine( date( __( 'm/d/Y H:i:s', WPDK_TEXTDOMAIN ), $value ) );
      }
    }
    elseif ( 'wpdk_user_internal-status' == $column_name ) {
      $item = array(
        'type'       => WPDK_FORM_FIELD_TYPE_SWIPE,
        'name'       => 'wpdk-user-enabled',
        'userdata'   => $user_id,
        'afterlabel' => '',
        'value'      => ( empty( $value ) || $value == self::STATUS_CONFIRMED ) ? 'on' : 'off'
      );
      ob_start();
      WPDKForm::htmlSwipe( $item );
      $value = ob_get_contents();
      ob_end_clean();
    }
    elseif ( 'wpdk_user_internal-count_wrong_login' == $column_name ) {
      if ( empty( $value ) ) {
        $value = '0';
      }
      //$value .= '/' . absint( WPXtremeConfiguration::init()->enhancers->security->users->iMaxNumberOfWrongLoginAttempts );
      $value .= '/10';
    }

    return $value;
  }

  /**
   * Pagina di modifica nel backend
   *
   * @param $user
   */
  public static function edit_user_profile( $user ) {
    /* Per adesso mostro il profilo di un altro utente come se visualizzassi il mio personale */
    self::show_user_profile( $user );
  }

  /**
   * Called when the user edit view is displayed
   *
   * @todo Add more fields
   *
   * @param object $user User object
   */
  public static function show_user_profile( $user ) {

    $fields = array(
      __( 'Extra fields', WPDK_TEXTDOMAIN ) => array(
        __( 'See WPX Enhancer Users to manage this extra fields', WPDK_TEXTDOMAIN ),
      )
    );

    $extra_fields = WPDKUser::arrayExtraFieldsForSDF( $user, $fields );

    WPDKForm::htmlForm( $fields );

    /* @todo To doc */
    do_action( 'wpdk_user_show_user_profile', $user );
  }

  // -----------------------------------------------------------------------------------------------------------------
  // WordPress Roles list
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Restituisce la lista di tutti i ruoli attualmente presenti in WordPress.
   *
   * @static
   * @return array
   */
  public static function allRoles() {
    global $wp_roles;
    $result = array();

    $roles = $wp_roles->get_names();
    foreach ( $roles as $key => $role ) {
      $result[$key] = $role;
    }
    return $result;
  }

  /**
   * Restituisce il 'nome' del ruolo di un utente
   *
   *
   * @param int $id_user User ID
   *
   * @return bool|string Ruolo utente o FALSE se errore.
   */
  public static function roleNameForUserID( $id_user ) {
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

  // -----------------------------------------------------------------------------------------------------------------
  // WordPress Capabilities List
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Restituisce la lista di tutte le capabilities attualmente presenti in WordPress, scorrendo tutti i ruoli
   * presenti ed estraendo le capabilities.
   *
   * @static
   * @return array
   */
  public static function allCapabilities() {
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
   * quelle selexionate ($selected_caps) devo attivate/disattivare ?
   *
   *
   * @param int   $id_user       ID dell'utente
   * @param array $selected_caps Lista delle capability da aggiungere
   * @param array $capabilities  Lista di confronto per capire quale capability aggiungere e quale rimuovere
   */
  public static function updateUserCapabilities( $id_user, $selected_caps, $capabilities ) {
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

  /**
   * Legge o imposta lo user meta per visualizzare la toolbar di WordPress in front end
   *
   *
   * @param int  $id_user Se NULL prende l'utente attualmente loggato
   * @param null $show    Se NULL restituisce il valore corrente per l'utente $id_user
   *
   * @return bool|mixed True se l'impostazione è avvenuta con successo oppure una stringa che indica lo stato attuale della toolbar
   */
  public static function showAdminBarFront( $id_user = null, $show = null ) {
    if ( is_null( $id_user ) ) {
      $id_user = get_current_user_id();
    }
    if ( $id_user ) {
      $show_admin_bar_front = get_user_meta( $id_user, 'show_admin_bar_front', true );
      if ( is_null( $show ) ) {
        return $show_admin_bar_front;
      }
      else {
        $value = ( $show === true ) ? 'true' : 'false';
        update_user_meta( $id_user, 'show_admin_bar_front', $value );
        return true;
      }
    }
    return false;
  }


  // -----------------------------------------------------------------------------------------------------------------
  // WordPress integration
  // -----------------------------------------------------------------------------------------------------------------

  // -----------------------------------------------------------------------------------------------------------------
  // WordPress integration - Login
  // -----------------------------------------------------------------------------------------------------------------

  /// Sign In
  /**
   * Do a WordPress Sign in and call filters and action
   *
   * @param string $email    A valid email address
   * @param string $password Password
   * @param bool   $remember TRUE for set a cookie for next login
   *
   * @return bool TRUE if success, FALSE for access denied
   */
  public static function signIn( $email, $password, $remember = false ) {

    /* Sanitizzo parametri in input. */
    $email    = sanitize_email( $email );
    $password = esc_attr( $password );

    /* Controlla che esista un utente con quella email. */
    $id_user = email_exists( $email );

    if ( false !== $id_user ) {
      $user = new WP_User( $id_user );
      if ( $user ) {
        $result = wp_authenticate( $user->user_login, $password );
        if ( !is_wp_error( $result ) ) {
          /* Set remember cookie */
          wp_set_auth_cookie( $user->ID, $remember );
          do_action( 'wp_login', $user->user_login, $user );

          /* Internal counter */
          self::wp_login( $user->user_login, $user );

          /* Authenticate! You are */
          wp_set_current_user( $user->ID );
          return true;
        }
      }
    }

    /**
     * @defgroup wpdk_singin_wrong wpdk_singin_wrong
     * @{
     *
     * @ingroup  user_helper_actions
     *           Called when a sign in is failed
     *
     * @param string $email    Username/email used to sign in
     * @param string $password Password used to sign in
     *
     * @}
     */
    do_action( 'wpdk_singin_wrong', $email, $password );
    return false;
  }

  /// Sign out
  public static function signout() {

    /* Esegue il logout da WordPress. */
    wp_logout();

    /* Per default ridirezione sulla home page del sito. */
    $blog_url = get_bloginfo( 'url' );
    wp_safe_redirect( $blog_url );
    exit();
  }

  /**
   * Esegue il login in WordPress
   *
   *
   * @param string $field Indica il campo da utilizzare come username
   *
   * @return bool Restituisce true se il login ha avuto successo, altrime false per errore
   *
   * @deprecated Use WPDKUser::signIn() instead
   */
  public static function doLogin( $field = 'user_email' ) {

    _deprecated_function( __METHOD__, '0.5', 'WPDKUser::signIn()' );

    return self::signIn( $_POST['username'], $_POST['password'], isset( $_POST['remember'] ) );

    /* ## DEPRECATED OLD CODE

    global $wpdb;

    if ( isset( $_POST['action'] ) && $_POST['action'] == 'do_login' ) {
        $email    = sanitize_email( $_POST['username'] );
        $password = esc_attr( $_POST['password'] );
        if ( $email && $password ) {
            $sql = <<< SQL
SELECT ID, user_login
FROM `{$wpdb->users}`
WHERE {$field} = '{$email}'
SQL;
            $row = $wpdb->get_row( $sql );
            if ( $row ) {
                $result = wp_authenticate( $row->user_login, $password );
                if ( !is_wp_error( $result ) ) {
                    wp_set_auth_cookie( $row->ID, isset( $_POST['remember'] ) );
                    $user = get_user_by( 'login', $row->user_login );
                    do_action( 'wp_login', $row->user_login, $user );
                    self::wp_login( $row->user_login, $user );
                    wp_set_current_user( $row->ID );
                    return true;
                }
            }
        }
        do_action( 'wpdk_login_wrong' );
    }
    return false;

    ## END DEPRECATED */
  }

  /**
   * Autentica uno user per email e password
   *
   * @param string $email    User email address
   * @param string $password Clear password
   *
   * @return int|bool Restituisce l'ID dell'utente o false se non autenticato
   */
  public static function authenticate( $email, $password ) {
    global $wpdb;

    if ( empty( $email ) || empty( $password ) ) {
      return false;
    }

    $email    = sanitize_email( $email );
    $password = esc_attr( $password );

    $sql = <<< SQL
    SELECT ID, user_login
    FROM `{$wpdb->users}`
    WHERE user_email = '{$email}'
SQL;
    $row = $wpdb->get_row( $sql );

    if ( $row ) {
      $result = wp_authenticate( $row->user_login, $password );
      if ( !is_wp_error( $result ) ) {
        self::wp_login( $row->user_login );
        return $row->ID;
      }
    }
    return false;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Commodity
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Unisce nome e cognome prendendo la prima lettere del nome, Eg. Mario Rossi > M.Rossi
   *
   *
   * @param string $firstName Nome
   * @param string $lastName  Cognome
   *
   * @return string
   */
  public static function formatNiceName( $firstName, $lastName ) {
    $result = sprintf( '%s.%s', strtoupper( substr( $firstName, 0, 1 ) ), ucfirst( $lastName ) );
    return $result;
  }

  /**
   * Unisce nome e cognome o cognome e nome
   *
   *
   * @param string $firstName Nome
   * @param string $lastName  Cognome
   * @param bool   $nameFirst
   *
   * @return string
   */
  public static function formatFullName( $firstName, $lastName, $nameFirst = true ) {
    if ( $nameFirst ) {
      $result = sprintf( '%s %s', $firstName, $lastName );
    }
    else {
      $result = sprintf( '%s %s', $lastName, $firstName );
    }
    return $result;
  }


  // -----------------------------------------------------------------------------------------------------------------
  // User info
  // -----------------------------------------------------------------------------------------------------------------

  /// Save extra fields user
  /**
   * @param int|object|array $user      User mixed
   * @param array            $post_data Array key/value data, usually $_POST
   *
   * @return bool
   */
  public static function updateExtraFields( $user, $post_data ) {
    /* Check for user */
    $id_user = self::id( $user );
    if ( empty( $id_user ) ) {
      return false;
    }

    if ( !empty( $post_data ) ) {
      /* Recupero l'elenco dei campi extra */
      $items = WPXtremeConfiguration::init()->enhancers->users->extra_fields->fields;
      /* Ciclo per i campi extra e verifico che siano presenti in $post_data */
      foreach ( $items as $item ) {
        if ( isset( $item['name'] ) ) {
          $name = $item['name'];
          if ( isset( $post_data[$name] ) ) {

            /**
             * @defgroup wpdk_user_should_update_extra_field wpdk_user_should_update_extra_field
             * @{
             *
             * @ingroup  user_helper_filters
             *           Called before an extra field is update
             *
             * @param bool  $update    Default true
             * @param array $item      SDF item
             * @param array $post_data Usually $_POST or any array data
             *
             * @return bool True for update, false to skip
             *
             * @}
             */
            $update = apply_filters( 'wpdk_user_should_update_extra_field', true, $item, $post_data );
            if ( true === $update ) {
              update_user_meta( $id_user, $name, esc_attr( $post_data[$name] ) );
            }
          }
        }
      }
    }
  }

  /// Return the extra fields
  /**
   * Return and set the user extra fields.
   *
   * @param int|object|array $user   User
   * @param array            $fields SDF fields where append these extra fields
   *
   * @return array|bool False if error or array extra fields
   */
  public static function arrayExtraFieldsForSDF( $user, &$fields = array() ) {

    /* Get first section of exists sdf fields */
    $key_field = key( $fields );

    /* Check for user */
    if ( !empty( $user ) ) {
      $id_user = self::id( $user );
      if ( empty( $id_user ) ) {
        return false;
      }
    }
    else {
      $id_user = false;
    }

    $value    = null;
    $items    = WPXtremeConfiguration::init()->enhancers->users->extra_fields->fields;
    $sections = & $fields[$key_field];
    if ( !empty( $items ) ) {
      foreach ( $items as $item ) {
        if ( WPDKUIControlType::SECTION == $item['type'] ) {
          $sections                 = & $fields;
          $sections[$item['label']] = array();
          $sections                 = & $sections[$item['label']];
        }
        else {
          if ( isset( $item['name'] ) ) {
            if ( $id_user ) {
              $value = get_user_meta( $id_user, $item['name'], true );
            }

            if ( $value ) {
              /* @todo Qui andrebbero sanitizzati i valori in base al tipo del campo. */
              $item['value'] = $value;
            }

            /**
             * @defgroup wpdk_user_extra_field_before_ wpdk_user_extra_field_before_
             * @{
             *
             * @ingroup  user_helper_filters
             *           Called before an extra field
             *
             * @param bool  $return Default false for nothing to pre-append
             * @param array $item   SDF item
             * @param mixed $value  Value
             *
             * @return array $item SDF item or false
             *
             * @}
             */
            $before = apply_filters( 'wpdk_user_extra_field_before_' . $item['name'], false, $item, $value );
            if ( false !== $before ) {
              $sections[] = $before;
            }

            /**
             * @defgroup wpdk_user_extra_field_ wpdk_user_extra_field_
             * @{
             *
             * @ingroup  user_helper_filters
             *           Called when an extra field is found
             *
             * @param array    $item     SDF item
             * @param mixed    $value    Value
             * @param int|bool $id_user  User ID or false
             *
             * @return array $item SDF item
             *
             * @}
             */
            $sections[] = apply_filters( 'wpdk_user_extra_field_' . $item['name'], array( $item ), $value, $id_user );

            /**
             * @defgroup wpdk_user_extra_field_after_ wpdk_user_extra_field_after_
             * @{
             *
             * @ingroup  user_helper_filters
             *           Called after an extra field
             *
             * @param bool  $return Default false for nothing to pre-append
             * @param array $item   SDF item
             * @param mixed $value  Value
             *
             * @return array $item SDF item or false
             *
             * @}
             */
            $after = apply_filters( 'wpdk_user_extra_field_after_' . $item['name'], false, $item, $value );
            if ( false !== $after ) {
              $sections[] = $after;
            }

          }
          else {
            /**
             * @defgroup wpdk_user_extra_field wpdk_user_extra_field
             * @{
             *
             * @ingroup  user_helper_filters
             *           Called when an extra field is found
             *
             * @param array $item   SDF item
             * @param mixed $value  Value
             *
             * @return array $item SDF item
             *
             * @}
             */
            $sections[] = apply_filters( 'wpdk_user_extra_field', array( $item ), $value );
          }
        }
      }
    }
    return $items;
  }

  /**
   * Restituisce un oggetto utente con anche le informazioni in user meta
   *
   * @deprecated Questa procedura non è necessaria in quanto l'oggetto WP_User già carica tutti gli user meta e li rende accessibili tramite il metodo get()
   *
   * @param null $user
   *
   * @return null|WP_User
   */
  public static function user( $user = null ) {

    _deprecated_function( __FUNCTION__, '0.5', 'WP_User' );

    if ( is_null( $user ) ) {
      $id_user = get_current_user_id();
    }
    elseif ( is_numeric( $user ) ) {
      $id_user = $user;
    }

    if ( !is_object( $user ) ) {
      $user = new WP_User( $id_user );
    }
    $user->user_meta = get_user_meta( $id_user );

    return $user;
  }

  /**
   * Restituisce l'id dell'utente con una determinata meta_key e meta_value
   *
   *
   * @param string $meta_key   Identificativo della meta_key
   * @param string $meta_value Valore
   *
   * @return int ID utente o false se non trovato
   */
  public static function userWithMetaAndValue( $meta_key, $meta_value ) {
    global $wpdb;

    $sql    = <<< SQL
SELECT user_id
FROM $wpdb->usermeta
WHERE meta_key = '$meta_key'
AND meta_value = '$meta_value'
SQL;
    $result = $wpdb->get_var( $sql );

    return $result;
  }

  /// Get users by meta key and value
  /**
   * Restituisce l'elenco degli ID utente che possiedono un determinata meta_key
   *
   * @todo Not implements yet
   *
   *
   * @param string $meta_key
   * @param string $meta_value
   *
   * @return array Users object or null
   */
  public static function usersWithMeta( $meta_key, $meta_value ) {
    /* ... */
  }

  /// Get the user display name
  /**
   * Restituisce il Display Name come impostato nel backend user
   *
   *
   * @param int $id_user User ID or null for current user ID
   *
   * @return string|bool Display name string or false if error
   */
  public static function displayName( $id_user = null ) {
    if ( is_null( $id_user ) ) {
      $id_user = get_current_user_id();
    }
    $user = new WP_User( $id_user );
    if ( $user ) {
      return $user->display_name;
    }
    return false;
  }

  /// Get the user age from birth date
  /**
   * Calcola l'eta (in anni) a partire da una data nel formato YYYY-MM-DD o DD/MM/YYYY
   *
   * @todo To improve in date format
   *
   *
   * @param string $birthday Data di nascita. Questo può essere sia in formato MySQL YYYY-MM-DD o in formato data
   *                         unico vincolo per adesso è il supporto solo per data italiana, ovvero giorno/meso/anno
   *
   * @return int Age
   */
  public static function ageFromDate( $birthday ) {
    $year_diff = 0;

    if ( !empty( $birthday ) ) {
      if ( strpos( $birthday, '-' ) !== false ) {
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

  /**
   * Converte la data di nascita visuale in quella MySQL
   *
   *
   * @param string $birthdate
   *
   * @return string
   */
  private static function birthDateToMySQL( $birthdate ) {
    return WPDKDateTime::formatFromFormat( $birthdate, '', 'Y-m-d' );
  }

  /// Convert a birth date for display
  /**
   * Converte la data di nascita di MySQL in quella visuale
   *
   * @param string $birthdate Borth date in mySQL format (Y-m-d)
   *
   * @return string Date for display
   */
  private static function birthDateToInput( $birthdate ) {
    return WPDKDateTime::formatFromFormat( $birthdate, '', __( 'm/d/Y', WPDK_TEXTDOMAIN ) );
  }


  // -----------------------------------------------------------------------------------------------------------------
  // Sanitize
  // -----------------------------------------------------------------------------------------------------------------

  /// Get a id user from mixed input
  /**
   * Metodo polimorfico in grado di restituire l'id di un utente in base al parametro di input
   *
   *
   * @param int|string|object|array $user User (id, object o array)
   *
   * @return int ID user
   */
  public static function id( $user ) {
    if ( is_numeric( $user ) ) {
      $result = $user;
    }
    elseif ( is_object( $user ) && isset( $user->ID ) ) {
      $result = $user->ID;
    }
    elseif ( is_array( $user ) && isset( $user['ID'] ) ) {
      $result = $user['ID'];
    }
    else {
      $message = __( 'Wrong user parameter', WPXSMARTSHOP_TEXTDOMAIN );
      $error   = new WP_Error( 'wpdk_error-wrong_user_parameter', $message, $user );
      return $error;
    }
    return absint( $result );
  }

  /**
   * Esegue dei controlli per sanitizzare lo uniqID - può essere sovrascritta
   *
   *
   * @param string $id
   *
   * @return string
   */
  public static function sanitizeUserUniqID( $id ) {
    if ( substr( $id, 0, 1 ) != 'u' ) {
      return '';
    }

    //4ee5e4ab78c38
    $result = substr( $id, 0, 14 );
    return $result;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Gravatar
  // -----------------------------------------------------------------------------------------------------------------

  /// Get html img tag from gravatar.com service
  /**
   * Restituisce l'html dell'immagine sul servizio Gravatar
   *
   *
   * @param int    $id_user User ID or null for current user ID
   * @param int    $size    Gravatar size
   * @param string $alt     Alternate string for alt attribute
   * @param string $default Gravatar ID for default (not found) gravatar image
   *
   * @return string L'HTML del tag <img> dell'avatar, altrimenti false per errore
   */
  public static function gravatar( $id_user = null, $size = 40, $alt = '', $default = "wavatar" ) {
    if ( is_null( $id_user ) ) {
      $id_user = get_current_user_id();
    }
    $user = new WP_User( $id_user );
    if ( $user ) {
      $alt = empty( $alt ) ? $user->display_name : $alt;
      $src = sprintf( 'http://www.gravatar.com/avatar/%s?s=%s&d=%s', md5( $user->user_email ), $size, $default );

      $html = <<< HTML
            <img src="{$src}" alt="{$alt}" title="{$alt}" />
HTML;
      return $html;
    }
    return false;
  }

  /**
   * Restituisce un array in formato SDF con la lista degli utenti, formattata con 'display name (email)'
   *
   * @todo Sicuramente da migliorare in quanto poco flessibile
   *
   * @return array
   */
  public static function arrayUserForSDF() {
    $users      = array();
    $users_list = get_users();
    if ( $users_list ) {
      foreach ( $users_list as $user ) {
        $users[$user->ID] = sprintf( '%s (%s)', $user->display_name, $user->user_email );
      }
    }
    return $users;
  }

  /* @todo Alias allRoles() - quest'ultimo da eliminare ? */
  public static function arrayRolesForSDF() {
    global $wp_roles;
    $result = array();

    $roles = $wp_roles->get_names();
    foreach ( $roles as $key => $role ) {
      $result[$key] = $role;
    }
    return $result;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Registration/Profile/Double-optin
  // -----------------------------------------------------------------------------------------------------------------

  /// Create a WordPress user
  /**
   * Crea una utenza in WordPress.
   * Crea un utente nella tabella di WordPress seguendo i parametri impostati negli inputs.
   *
   *
   * @param string        $first_name First name
   * @param string        $last_name  Last name
   * @param string        $email      Email address
   * @param bool|string   $password   Clear password, if set to false a random password is created
   * @param bool          $enabled    Se true l'utente viene creato e immediatamente abilitato, lasciare false per porlo in
   *                                  uno stato di pending
   *
   * @return int|WP_Error
   */
  public static function create( $first_name, $last_name, $email, $password = false, $enabled = false ) {

    /* @note Per ragioni di sicurezza sarebbe bene creare sempre utenti con password. Evitare quindi di creare utenti
     *       con password nulla, ovvero vutoa.
     */
    if ( false === $password ) {
      $password = WPDKCrypt::randomAlphaNumber();
    }

    $niceName = self::formatNiceName( $first_name, $last_name );

    $userInfo = array(
      "user_login"    => $email,
      'user_pass'     => $password,
      'user_email'    => $email,
      "user_nicename" => $niceName,
      "nickname"      => $niceName,
      "display_name"  => self::formatFullName( $first_name, $last_name ),
      "first_name"    => $first_name,
      "last_name"     => $last_name,
      "role"          => WPXtremeConfiguration::init()->enhancers->users->registration->default_user_role
    );

    $result = wp_insert_user( $userInfo );

    /* Se l'utente è stato inserito lo disabilito come da parametro. */
    if ( !is_wp_error( $result ) && false === $enabled ) {
      update_user_meta( $result, 'wpdk_user_internal-status', self::STATUS_DISABLED );
      /* @todo Aggiungere filtro. */
      update_user_meta( $result, 'wpdk_user_internal-status-message', __( 'Waiting for enabling', WPDK_TEXTDOMAIN ) );
    }

    return $result;
  }


  // -----------------------------------------------------------------------------------------------------------------
  // LOGIN
  // -----------------------------------------------------------------------------------------------------------------

  /// Get SDF fields for user login
  public static function fieldsLogin() {
    $fields = array(
      __( 'Access data', WPDK_TEXTDOMAIN ) => array(

        array(
          'type'  => WPDK_FORM_FIELD_TYPE_HIDDEN,
          'name'  => 'wpdk_action_login',
          'value' => 'login'
        ),

        array(
          array(
            'type'  => WPDK_FORM_FIELD_TYPE_TEXT,
            'name'  => 'wpdk_user_email',
            'label' => __( 'Email', WPDK_TEXTDOMAIN )
          ),
        ),
        array(
          array(
            'type'  => WPDK_FORM_FIELD_TYPE_PASSWORD,
            'name'  => 'wpdk_user_password',
            'label' => __( 'Password', WPDK_TEXTDOMAIN )
          )
        ),

        /* Remeber */
        array(
          array(
            'type'  => WPDK_FORM_FIELD_TYPE_CHECKBOX,
            'name'  => 'wpdk_login_remember',
            'label' => __( 'Remember', WPDK_TEXTDOMAIN )
          )
        ),
      ),
    );

    /**
     * @defgroup wpdk_user_login_fields wpdk_user_login_fields
     * @{
     *
     * @ingroup  user_helper_filters
     *           Called when the SDF dields are ready. You can change the default SDF array
     *
     * @param array $fields SDF array for login
     *
     * @return array $fields SDF array for login
     *
     * @}
     */
    $fields = apply_filters( 'wpdk_user_login_fields', $fields );

    return $fields;
  }

  /// Get user login form view
  public static function formLogin( $content = null ) {

    /* Check the content */
    $content = is_null( $content ) ? '' : $content;

    ob_start();
    WPDKForm::htmlForm( self::fieldsLogin() );
    $contents = ob_get_contents();
    ob_end_clean();

    $button_login = WPDKUI::button( __( 'Login', WPDK_TEXTDOMAIN ), array( 'classes' => 'btn btn-primary pull-right' ) );

    /**
     * @defgroup wpdk_login_button_signup wpdk_login_button_signup
     * @{
     *
     * @ingroup  user_helper_filters
     *           Called for build the label of signup button
     *
     * @param string $label Label link to signup
     *
     * @return string $label Label link to signup
     *
     * @}
     */
    $button_signup_label = apply_filters( 'wpdk_login_button_signup', __( 'Signup', WPDK_TEXTDOMAIN ) );

    /**
     * @defgroup wpdk_login_button_signup_url wpdk_login_button_signup_url
     * @{
     *
     * @ingroup  user_helper_filters
     *           Called for the url of signup
     *
     * @param string $url Url for signup page
     *
     * @return string $url Url for signup page or FALSE to hide signup button
     *
     * @}
     */
    $button_signup_url = apply_filters( 'wpdk_login_button_signup_url', false );
    $button_signup     = '';
    if ( false !== $button_signup_url ) {
      $button_signup = sprintf( '<a class="btn btn-warning pull-left" href="%s">%s</a>', $button_signup_url, $button_signup_label );
    }

    $html = <<< HTML
<form class="wpdk-form wpdk-user-login" name="wpdk-user-login" method="post" action="">
{$contents}
<div class="wpdk-form-row">
<p>{$button_signup} {$button_login}</p>
{$content}
</div>
</form>
HTML;
    return $html;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // RESET PASSWORD
  // -----------------------------------------------------------------------------------------------------------------

  /// Get SDF fields for reset password
  public static function fieldsResetPassword() {
    $fields = array(
      __( 'Forgot Password?', WPDK_TEXTDOMAIN ) => array(

        array(
          'type'  => WPDK_FORM_FIELD_TYPE_HIDDEN,
          'name'  => 'wpdk_action_reset_password',
          'value' => 'reset'
        ),

        __( 'Forgot your Password?! No worry, enter your email address into username field here on the left then you receive a mail with a new temporary password', WPDK_TEXTDOMAIN ),

        array(
          array(
            'type'  => WPDK_FORM_FIELD_TYPE_TEXT,
            'name'  => 'wpdk_user_email',
            'label' => __( 'Email', WPDK_TEXTDOMAIN )
          ),
          array(
            'type'  => WPDK_FORM_FIELD_TYPE_SUBMIT,
            'name'  => 'wpdk_button_login_recovery',
            'class' => 'btn btn-success',
            'value' => __( 'Recovry Password', WPDK_TEXTDOMAIN )
          )
        ),
      ),
    );

    return $fields;
  }

  /// Get reset password for view
  public static function formResetPassword() {
    ob_start();
    WPDKForm::htmlForm( self::fieldsResetPassword() );
    $contents = ob_get_contents();
    ob_end_clean();

    $html = <<< HTML
<form class="wpdk-form wpdk-user-reset-password" name="wpdk-user-reset-password" method="post" action="">
{$contents}
</div>
</form>
HTML;
    return $html;
  }


  // -----------------------------------------------------------------------------------------------------------------
  // EDIT PROFILE
  // -----------------------------------------------------------------------------------------------------------------

  /// Get SDF fields for edit profile
  /**
   * Restituisce l'array dei campi in formato SDF per la visualizzazione del profilo
   *
   * @todo Estendere con i campi extra gestibili da backend
   *
   * @return array
   */
  public static function fieldsProfile( $user, $args ) {

    $fields = array(
      __( 'Your profile', WPDK_TEXTDOMAIN ) => array(

        array(
          'type'  => WPDK_FORM_FIELD_TYPE_HIDDEN,
          'name'  => 'wpdk_action_profile',
          'value' => 'update'
        ),

        /* Dato che un amministratore potrebbe editare anche profili di altri, riposto anche lo user ID che
        potrebbe essere diverso dall'utente attualmente loggato. Questo serve per aggiornare il profilo.
        */
        array(
          'type'  => WPDK_FORM_FIELD_TYPE_HIDDEN,
          'name'  => 'wpdk_action_profile_id',
          'value' => $user->ID
        ),

        sprintf( __( '<strong>%s</strong> active from <strong>%s</strong><br/>Last login <strong>%s</strong>, last logout <strong>%s</strong>', WPDK_TEXTDOMAIN ), $user->data->display_name, WPDKDateTime::formatFromFormat( $user->data->user_registered, '', __( 'm/d/Y H:i', WPDK_TEXTDOMAIN ) ), date( __( 'm/d/Y H:i', WPDK_TEXTDOMAIN ), $user->get( 'wpdk_user_internal-time_last_login' ) ), date( __( 'm/d/Y H:i', WPDK_TEXTDOMAIN ), $user->get( 'wpdk_user_internal-time_last_logout' ) ) ),

        /* Name, last name and email */
        array(
          array(
            'type'  => WPDK_FORM_FIELD_TYPE_TEXT,
            'name'  => 'wpdk_first_name',
            'value' => $user->get( 'first_name' ),
            'label' => __( 'First name', WPDK_TEXTDOMAIN ),
            'attrs' => empty( $args['bypass_reset_password'] ) ? false : array( 'disabled' => 'disabled' ),
          )
        ),
        array(
          array(
            'type'  => WPDK_FORM_FIELD_TYPE_TEXT,
            'name'  => 'wpdk_last_name',
            'value' => $user->get( 'last_name' ),
            'label' => __( 'Last name', WPDK_TEXTDOMAIN ),
            'attrs' => empty( $args['bypass_reset_password'] ) ? false : array( 'disabled' => 'disabled' ),
          )
        ),
        array(
          array(
            'type'  => WPDK_FORM_FIELD_TYPE_TEXT,
            'name'  => 'wpdk_email',
            'value' => $user->data->user_email,
            'data'  => array( 'placement' => 'right' ),
            'title' => '',
            'label' => __( 'Email', WPDK_TEXTDOMAIN ),
            'attrs' => empty( $args['bypass_reset_password'] ) ? false : array( 'disabled' => 'disabled' ),
          )
        ),

        /* Password */
        array(
          array(
            'type'   => WPDK_FORM_FIELD_TYPE_PASSWORD,
            'label'  => __( 'Password', WPDK_TEXTDOMAIN ),
            'name'   => 'wpdk_password',
            'append' => __( 'Leave blank to unchange your password', WPDK_TEXTDOMAIN )
          )
        ),
        array(
          array(
            'type'  => WPDK_FORM_FIELD_TYPE_PASSWORD,
            'label' => __( 'Repeat Password', WPDK_TEXTDOMAIN ),
            'name'  => 'wpdk_password_repeat'
          )
        ),
      ),
    );

    if ( 'true' == $args['extra_fields'] ) {
      $extra_fields = WPDKUser::arrayExtraFieldsForSDF( $user, $fields );
    }

    /**
     * @defgroup wpdk_user_profile_fields wpdk_user_profile_fields
     * @{
     *
     * @ingroup  user_helper_filters
     *           Called when the SDF fields are ready. You can change the default SDF array
     *
     * @param array $fields SDF array for profile
     *
     * @return array $fields SDF array for profile
     *
     * @}
     */
    if ( empty( $args['bypass_reset_password'] ) ) {
      $fields = apply_filters( 'wpdk_user_profile_fields', $fields );
    }

    return $fields;
  }

  /// Get user profile form view
  /**
   * Restituisce la form del profilo utente
   *
   *
   * @param WP_User $user Oggetto WP_User
   *
   * @return string
   */
  public static function formProfile( $user, $args ) {
    ob_start();
    WPDKForm::htmlForm( self::fieldsProfile( $user, $args ) );
    $content = ob_get_contents();
    ob_end_clean();

    $button_update = WPDKUI::button();

    $html = <<< HTML
<form class="wpdk-form wpxm-user-profile" name="wpxm-user-profile" method="post" action="">
{$content}
<div class="wpdk-form-row">
<p>{$button_update}</p>
</div>
</form>
HTML;

    /**
     * @defgroup wpdk_user_profile_after_form wpdk_user_profile_after_form
     * @{
     *
     * @ingroup  user_helper_filters
     *           Called after HTML user profile form
     *
     * @param string $html HTML user profile form
     *
     * @return string $html HTML user profile form
     *
     * @}
     */
    $html = apply_filters( 'wpdk_user_profile_after_form', $html );

    return $html;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // REGISTRATION
  // -----------------------------------------------------------------------------------------------------------------

  /// Get SDF fields for user registration
  /**
   * Campi in SFD per la form di registrazione
   *
   * @todo Estendere con i campi extra gestibili da backend
   *
   * @return array
   */
  public static function fieldsRegistration( $args ) {

    if ( true === WPXtremeConfiguration::init()->enhancers->users->registration->double_optin ) {
      $title_email  = __( 'This email address will be used for sending an email to you. You have to confirm url address in the email.', WPDK_TEXTDOMAIN );
      $double_optin = 'double-optin';
    }
    else {
      $double_optin = 'no-double-optin';
      $title_email  = __( 'This email address will be the your username for login.', WPDK_TEXTDOMAIN );
    }

    $fields = array(
      __( 'Account information', WPDK_TEXTDOMAIN ) => array(

        array(
          'type'  => WPDK_FORM_FIELD_TYPE_HIDDEN,
          'name'  => 'wpdk_action_registration',
          'value' => $double_optin
        ),

        __( 'Fill out all <strong>required</strong> fields', WPDK_TEXTDOMAIN ),

        array(
          array(
            'type'   => WPDK_FORM_FIELD_TYPE_TEXT,
            'name'   => 'wpdk_first_name',
            'label'  => __( 'First name', WPDK_TEXTDOMAIN ),
            'append' => __( '<strong>(required)</strong>', WPDK_TEXTDOMAIN )
          )
        ),
        array(
          array(
            'type'   => WPDK_FORM_FIELD_TYPE_TEXT,
            'name'   => 'wpdk_last_name',
            'label'  => __( 'Last name', WPDK_TEXTDOMAIN ),
            'append' => __( '<strong>(required)</strong>', WPDK_TEXTDOMAIN )
          )
        ),
        array(
          array(
            'type'   => WPDK_FORM_FIELD_TYPE_TEXT,
            'name'   => 'wpdk_email',
            'data'   => array( 'placement' => 'right' ),
            'title'  => $title_email,
            'label'  => __( 'Email', WPDK_TEXTDOMAIN ),
            'append' => __( '<strong>(required)</strong> to this address will be send an email confirm', WPDK_TEXTDOMAIN )
          )
        ),
      )
    );

    if ( 'true' == $args['extra_fields'] ) {
      WPDKUser::arrayExtraFieldsForSDF( null, $fields );
    }

    return $fields;
  }

  /**
   * Restituisce la form di registrazione
   *
   * @todo Documentare parametri, sia qui che nello shortcode
   *
   *
   * @param array $args Parametri provenienti dallo shortcode, ma utilizzabli anche direttamente
   *
   * @return string
   */
  public static function formRegistration( $args ) {

    ob_start();
    WPDKForm::htmlForm( self::fieldsRegistration( $args ) );
    $content = ob_get_contents();
    ob_end_clean();

    $button_registration = WPDKUI::button( __( 'Register', WPDK_TEXTDOMAIN ) );

    $html = <<< HTML
<form class="wpdk-form wpxm-user-registration" name="wpxm-user-registration" method="post" action="">
{$content}
<div class="wpdk-form-row">
<p>{$button_registration}</p>
</div>
</form>
HTML;
    return $html;
  }

  /// Return HTML message for double optin
  /**
   * Procedura di Double optin. Controllo e registrazione temporanea.
   *
   *
   * @param string $first_name   First name
   * @param string $last_name    Last name
   * @param string $email        Email address
   * @param bool   $extra_fields Indica se devono essere memorizzati anche gli extra fields
   *
   * @return string
   */
  public static function registerUserForDoubleOptin( $first_name, $last_name, $email, $extra_fields = false ) {

    /* Creo utente in WordPress disabilitato. */
    /* @todo Se questo utente non viene confermato entro un certo lasso di tempo andrebbe eliminato. */

    /* Controllo che non esista già un utente con questa email. */
    if ( email_exists( $email ) ) {
      $message = __( 'Warning! Your email is not valid.', WPDK_TEXTDOMAIN );
    }
    else {
      /* Creo utente. */
      $result = self::create( $first_name, $last_name, $email );

      /* $result contiene l'id dell'utente creato o un oggetto WP_Error in caso di errore. */

      if ( is_wp_error( $result ) ) {
        $message = sprintf( __( 'Error: %s', WPDK_TEXTDOMAIN ), $result->get_error_message() );
      }
      else {
        /* Memorizzo anche gli extra fields */
        if ( $extra_fields ) {
          WPDKUser::updateExtraFields( $result, $_POST );
        }
        /* Invio mail con il template definito nei setting all'utente $result appena inserito */
        WPXtremeMailCustomPostType::mail( WPXtremeConfiguration::init()->enhancers->users->registration->mail_slug_confirm, $result );
        $message = __( 'Thanks, please check your email to confirm registration.', WPDK_TEXTDOMAIN );
      }
    }

    $html = <<< HTML
<h2>{$message}</h2>
HTML;
    return $html;
  }

  /// Enable an user by unlock code
  /**
   * Procedura di abilitazione utente.
   * La password qui viene rigenerata e aggiornata all'utente. Questo perché in prima istanza le utenze devono sempre
   * avere una password e secondo perché il crypt di WordPress non è reversibili (in tempi brevi).
   *
   *
   * @param string $unlock_code Unlock code for enable an user
   *
   * @return string|bool HTML message or false if error
   */
  public static function enableUserAfterDoubleOptin( $unlock_code ) {

    /* Verifico codice di sblocco. Questo è l'md5 della mail dell'utente */
    /* Cerco un utente 'lockato' */
    $id_user = self::userWithMetaAndValue( 'wpdk_unlock_code', $unlock_code );

    /* Se esiste questa utenza... */
    if ( $id_user ) {
      /* Genero nuova password, aggiorno utente e abilito. */
      $new_password = WPDKCrypt::randomAlphaNumber();
      $userdata     = array(
        'ID'        => $id_user,
        'user_pass' => $new_password
      );
      $result       = wp_update_user( $userdata );

      if ( $result == $id_user ) {

        /* Rimuovo lo user meta con il codice di sblocco */
        delete_user_meta( $id_user, 'wpdk_unlock_code' );

        /* Abilito/riabilito questa utenza */
        update_user_meta( $id_user, 'wpdk_user_internal-status', self::STATUS_CONFIRMED );

        /* Invio mail con il template definito nei setting all'utente $result appena inserito */
        $extra = array(
          WPXtremeMailPlaceholder::USER_PASSWORD => $new_password
        );
        WPXtremeMailCustomPostType::mail( WPXtremeConfiguration::init()->enhancers->users->registration->mail_slug_confirmed, $id_user, false, false, $extra );

        /* @todo Aggiungere filtro e pagina (slug) di redirect */
        $message = __( 'Thanks, please check your email for retrive username and password', WPDK_TEXTDOMAIN );

        $html = <<< HTML
<h2>{$message}</h2>
HTML;
        return $html;
      }
    }
    return false;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // is/has zone
  // -----------------------------------------------------------------------------------------------------------------

  /// Check if an user has a specify capability
  /**
   * Restutuisce true se l'utente passato negli inputs (o l'utente corrente se non viene passato id utente) possiede
   * un determinato permesso (capability)
   *
   *
   * @param string $cap     Capability ID
   * @param int    $id_user User ID or null for get current user ID
   *
   * @return bool True se l'utente supporta la capability
   */
  public static function hasCap( $cap, $id_user = null ) {
    if ( is_null( $id_user ) ) {
      $id_user = get_current_user_id();
    }
    $user = new WP_User( $id_user );
    if ( $user ) {
      return $user->has_cap( $cap );
    }
    return false;
  }

  /// Check if an user has one or more capabilities
  /**
   * Restituisce true se almeno uno dei permessi passati negli inputs è presente nella lista permessi utente.
   *
   *
   * @param array $caps    Capabilities array
   * @param int   $id_user User ID or null for get current user ID
   *
   * @return bool Se almeno uno dei permessi è presente restituisce true, altrimenti false
   */
  public static function hasCaps( $caps, $id_user = null ) {
    if ( is_null( $id_user ) ) {
      $id_user = get_current_user_id();
    }
    $user = new WP_User( $id_user );
    if ( $user ) {
      $all_caps = $user->allcaps;
      foreach ( $caps as $cap ) {
        if ( isset( $all_caps[$cap] ) ) {
          return true;
        }
      }
    }
    return false;
  }

  /**
   * Restituisce true se l'utente corrente ha uno o più Ruoli nel suo profilo
   *
   * @todo Da verificare
   *
   * @param string | array $a Regola o array di rogle da verificare. Per ritornare true, in caso di array, basta che
   *                          una sola regola sia supportata dall'utente. Queste sono da considerarsi in OR non is AND
   *
   * @return bool Se almeno una delle regole passate negli inputs è supportata ritirna True. Altrimenti False.
   */
  public static function hasCurrentUserRoles( $a ) {

    if ( !function_exists( 'wp_get_current_user' ) ) {
      require_once( ABSPATH . '/wp-includes/pluggable.php' );
    }
    global $wp_roles;

    if ( !isset( $wp_roles ) ) {
      $wp_roles = new WP_Roles();
    }

    $current_user = wp_get_current_user();
    $roles        = $current_user->roles;
    if ( is_array( $a ) ) {
      foreach ( $a as $i ) {
        if ( in_array( $i, $roles ) ) {
          return true;
        }
      }
      return false;
    }
    else {
      return in_array( $a, $roles );
    }
  }

  /// This is a joke
  public static function isUserAdministrator() {
    $id_user = get_current_user_id();
    return ( $id_user == 1 );
  }

}

/// @endcond