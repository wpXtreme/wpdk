<?php
/// @cond private

if ( wpdk_is_ajax() ) {

  /**
   * Ajax Services Gateway
   *
   * @class              WPDKServiceAjax
   * @author             =undo= <info@wpxtre.me>
   * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
   * @date               2014-10-03
   * @version            1.0.0
   *
   */
  final class WPDKServiceAjax extends WPDKAjax {

    /**
     * Create or return a singleton instance of WPDKServiceAjax
     *
     * @return WPDKServiceAjax
     */
    public static function getInstance()
    {
      static $instance = null;
      if ( is_null( $instance ) ) {
        $instance = new self();
      }
      return $instance;
    }

    /**
     * Alias of getInstance();
     *
     * @return WPDKServiceAjax
     */
    public static function init()
    {
      return self::getInstance();
    }

    /**
     * Return the array list with allowed method. This is a Key value pairs array with value for not signin user ajax
     * method allowed.
     *
     * @return array
     */
    protected function actions()
    {

      $actionsMethods = array(
        'wpdk_action_user_by'            => true,
        'wpdk_action_autocomplete'       => true,
        'wpdk_action_autocomplete_posts' => true,
        'wpdk_action_autocomplete_users' => true,
        'wpdk_action_dismiss_wp_pointer' => true,

        // since 1.7.0
        'wpdk_action_on_swipe'           => true,

        // since 1.9.0
        'wpdk_action_on_switch'          => true,

        // since 1.4.21
        'wpdk_action_alert_dismiss'      => false,
        'wpdk_action_modal_dismiss'      => false,
      );

      return $actionsMethods;
    }

    /**
     * This action will fires if 'on_swipe' data attribute is set on the swipe control.
     *
     * @since 1.7.0
     * @deprecated since 1.9.0
     */
    public function wpdk_action_on_swipe()
    {
      $response = new WPDKAjaxResponse();

      // Status
      $enabled = isset( $_POST[ 'enabled' ] ) ? $_POST[ 'enabled' ] : false;

      // Get action
      $on_swipe = isset( $_POST[ 'on_swipe' ] ) ? $_POST[ 'on_swipe' ] : false;

      // Stability
      if( empty( $on_swipe ) ) {
        $response->error = __( 'Wrong parameter' );
        $response->json();
      }

      //WPXtreme::log( $enabled, 'before do' );

      /**
       * Fires the ajax action.
       *
       * The dynamic portion of the hook name, $on_swipe, refers to the custom ajax action.
       *
       * @param bool $enabled The swipe status enabled.
       */
      do_action( 'wp_ajax_' . $on_swipe, $enabled );

      $response->json();
    }

    /**
     * This action will fires if 'on_switch' data attribute is set on the switch ui button control.
     *
     * @since 1.9.0
     *
     */
    public function wpdk_action_on_switch()
    {
      $response = new WPDKAjaxResponse();

      // $state
      $state = isset( $_POST[ 'state' ] ) ? $_POST[ 'state' ] : false;

      // Get action
      $on_switch = isset( $_POST[ 'on_switch' ] ) ? $_POST[ 'on_switch' ] : false;

      // Stability
      if( empty( $on_switch ) ) {
        $response->error = __( 'Wrong parameter' );
        $response->json();
      }

      /**
       * Fires the ajax action.
       *
       * The dynamic portion of the hook name, $on_switch, refers to the custom ajax action.
       *
       * @param bool $state The switch state on/off (true/false).
       */
      do_action( 'wp_ajax_' . $on_switch, $state );

      $response->json();
    }

    /**
     * Return the autocomplete for users.
     *
     * @since 1.5.1
     */
    public function wpdk_action_autocomplete_users()
    {
      // Get the term
      $term = isset( $_POST['term'] ) ? $_POST['term'] : '';

      if( empty( $term ) ) {
        wp_die( -1 );
      }

      // Get the site id
      $site_id = isset( $_POST['site_id'] ) ? absint( $_POST['site_id'] ) : get_current_blog_id();

      // Get avatar
      $avatar      = isset( $_POST['avatar'] ) ? (bool)( $_POST['avatar'] ) : true;
      $avatar_size = isset( $_POST['avatar_size'] ) ? absint( $_POST['avatar_size'] ) : 32;

      // Get query
      $query = isset( $_POST['query'] ) ? (array)$_POST['query'] : array( 'user_login', 'user_nicename', 'user_email' );

      // Include users
      $include_blog_users = get_users( array( 'blog_id' => $site_id, 'fields'  => 'ID' ) );
      $exclude_blog_users = get_users( array( 'blog_id' => $site_id, 'fields'  => 'ID' ) );

      $args = array(
        'blog_id'        => false,
        'search'         => '*' . $term . '*',
        'include'        => $include_blog_users,
        'exclude'        => $exclude_blog_users,
        'search_columns' => $query,

        // @since 1.5.17 - avoid disabled users
        'meta_key'       => WPDKUserMeta::STATUS,
        'meta_compare'   => 'NOT EXISTS'
      );

      $users = get_users( $args );

      // Prepare array response
      $return = array();

      /**
       * @var WP_User $user
       */
      $user = null;

      // Loop in users WP_User
      foreach ( $users as $user ) {

        // Get avatar
        $img_avatar = $avatar ? get_avatar( $user->ID, $avatar_size, 'wavatar', $user->data->display_name ) : '';

        // Return
        $return[] = array(
          'value'        => $user->user_email,
          'label'        => sprintf( '%s %s %s (%s)', $img_avatar, $user->user_firstname, $user->user_lastname, $user->user_email ),
          'id'           => $user->ID,
          'email'        => $user->data->user_email,
          'display_name' => $user->data->display_name
        );
      }

      wp_send_json( $return );

    }

    /**
     * Display the autocomplete for input tag. List all user by term.
     *
     * @use string $_POST['term'] Term to searching for
     *
     * @return string JSON encode with search results
     */
    public function wpdk_action_user_by()
    {

      // ID, name, email...
      $pattern = esc_attr( $_POST['term'] );

      if ( !empty( $pattern ) ) {
        $pattern = '*' . $pattern . '*';
        $users   = get_users( array( 'search' => $pattern ) );
        if ( !empty( $users ) ) {
          $result = array();
          foreach ( $users as $user ) {
            $result[] = array(
              'id'    => $user->ID,
              'value' => sprintf( '%s (%s)', $user->display_name, $user->user_email )
            );
          }
          wp_die( json_encode( $result ) );
        }
      }
      wp_die();
    }

    /**
     * This method is such `wpdk_action_user_by` but used for custom autocomplete.
     *
     * @deprecated Since 1.0.0.b4
     *
     * @use   string $_POST['term'] Term to searching for
     *
     * @return string JSON encode with search results
     */
    public function wpdk_action_autocomplete()
    {
      $pattern         = esc_attr( $_POST['term'] );
      $autocomplete_id = esc_attr( $_POST['autocomplete_id'] );

      $result = apply_filters( 'wpdk_autocomplete', array(), $pattern, $autocomplete_id );

      wp_die( json_encode( $result ) );
    }

    /**
     * Return a posts list for type and status
     *
     * @return string
     */
    public function wpdk_action_autocomplete_posts()
    {
      /**
       * @var wpdb $wpdb
       */
      global $wpdb;

      // Get params
      $post_type   = esc_attr( isset( $_POST['post_type'] ) ? $_POST['post_type'] : WPDKPostType::PAGE );
      $post_status = esc_attr( isset( $_POST['post_status'] ) ? $_POST['post_status'] : WPDKPostStatus::PUBLISH );
      $limit       = esc_attr( isset( $_POST['limit'] ) ? sprintf( 'LIMIT 0,%s', $_POST['limit'] ) : '' );
      $order       = esc_attr( isset( $_POST['order'] ) ? $_POST['order'] : 'ASC' );
      $orderby     = esc_attr( isset( $_POST['orderby'] ) ? $_POST['orderby'] : 'post_title' );
      $term        = esc_attr( isset( $_POST['term'] ) ? $_POST['term'] : '' );

      // Prepare response
      $response = array();

      $table_posts = $wpdb->posts;

      $where_post_name = '';
      if ( !empty( $term ) ) {
        $where_post_name = sprintf( ' AND ( post_name LIKE "%%%s%%" OR post_title LIKE "%%%s%%" )', $term, $term );
      }

      $sql    = <<< SQL
SELECT
 ID,
 post_title

FROM {$table_posts}

WHERE 1

{$where_post_name}

AND post_type = '{$post_type}'
AND post_status = '{$post_status}'

ORDER BY {$orderby} {$order}

{$limit}
SQL;
      $result = $wpdb->get_results( $sql );
      if ( !is_wp_error( $result ) ) {
        foreach ( $result as $post ) {
          $response[] = array(
            'value' => get_page_uri( $post->ID ),
            'label' => apply_filters( 'the_title', $post->post_title ),
          );
        }
      }

      wp_send_json( $response );
    }


    /**
     * Dismiss a WPDK pointer for an user
     */
    public function wpdk_action_dismiss_wp_pointer()
    {
      $id_user             = get_current_user_id();
      $pointer             = esc_attr( $_POST['pointer'] );
      $dismissed           = unserialize( get_user_meta( $id_user, 'wpdk_dismissed_wp_pointer', true ) );
      $dismissed[$pointer] = true;
      update_user_meta( $id_user, 'wpdk_dismissed_wp_pointer', serialize( $dismissed ) );
      wp_die();
    }

    /**
     * Permanent alert dismiss by user logged in
     *
     * @since 1.4.21
     */
    public function wpdk_action_alert_dismiss()
    {
      $response = new WPDKAjaxResponse();

      // Get the alert id via post
      $alert_id = isset( $_POST['alert_id'] ) ? empty( $_POST['alert_id'] ) ? '' : $_POST['alert_id'] : '';

      // Stability
      if( empty( $alert_id ) ) {
        $response->error = __( 'Malformed data sending: no alert id found!', WPDK_TEXTDOMAIN );
        $response->json();
      }

      // Stability
      if( !is_user_logged_in() ) {
        $response->error = __( 'Severe error: no user logged in!', WPDK_TEXTDOMAIN );
        $response->json();
      }

      // Get the logged in user
      $user_id = get_current_user_id();

      // Get the dismissed list
      $dismissed = get_user_meta( $user_id, WPDKUIAlert::USER_META_KEY_PERMANENT_DISMISS, true );
      $dismissed = empty( $dismissed ) ? array() : $dismissed;

      // Add this alert id and make array unique - avoid duplicate
      $dismissed[ md5( $alert_id ) ] = time();
      update_user_meta( $user_id, WPDKUIAlert::USER_META_KEY_PERMANENT_DISMISS, $dismissed );

      /**
       * Fires when an alert is permanent dismiss by a user.
       *
       * @param int    $user_id  User that dismiss this alert.
       * @param string $alert_id The alert id.
       */
      do_action( 'wpdk_ui_alert_dismissed', $user_id, $alert_id );

      $response->json();
    }


    /**
     * Permanent modal dismiss by user logged in.
     *
     * @since 1.5.6
     */
    public function wpdk_action_modal_dismiss()
    {
      $response = new WPDKAjaxResponse();

      // Get the alert id via post
      $modal_id = isset( $_POST['modal_id'] ) ? $_POST['modal_id'] : '';

      // Stability
      if( empty( $modal_id ) ) {
        $response->error = __( 'Malformed data sedning: no alert id found!', WPDK_TEXTDOMAIN );
        $response->json();
      }

      // Stability
      if( !is_user_logged_in() ) {
        $response->error = __( 'Severe error: no user logged in!', WPDK_TEXTDOMAIN );
        $response->json();
      }

      // Get the logged in user
      $user_id = get_current_user_id();

      // Get the dismissed list
      $dismissed = get_user_meta( $user_id, WPDKUIModalDialog::USER_META_KEY_PERMANENT_DISMISS, true );
      $dismissed = empty( $dismissed ) ? array() : $dismissed;

      // Add this alert id and make array unique - avoid duplicate
      $dismissed[ md5( $modal_id ) ] = time();
      update_user_meta( $user_id, WPDKUIModalDialog::USER_META_KEY_PERMANENT_DISMISS, $dismissed );

      /**
       * Fires when an alert is permanent dismiss by a user.
       *
       * @param int    $user_id  User that dismiss this alert.
       * @param string $modal_id The alert id.
       */
      do_action( 'wpdk_ui_modal_dismissed', $user_id, $modal_id );

      $response->json();
    }

  } // class WPDKServiceAjax

} // if( wpdk_is_ajax() )

/// @endcond