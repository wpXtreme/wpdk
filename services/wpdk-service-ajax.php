<?php
/// @cond private

if ( wpdk_is_ajax() ) {

  /**
   * Ajax Services Gateway
   *
   * @class              WPDKServiceAjax
   * @author             =undo= <info@wpxtre.me>
   * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
   * @date               2014-03-03
   * @version            1.0.0
   *
   */
  class WPDKServiceAjax extends WPDKAjax {

    /**
     * Create or return a singleton instance of WPDKServiceAjax
     *
     * @brief Create or return a singleton instance of WPDKServiceAjax
     *
     * @return WPDKServiceAjax
     */
    public static function getInstance()
    {
      static $instance = null;
      if ( is_null( $instance ) ) {
        $instance = new WPDKServiceAjax();
      }
      return $instance;
    }

    /**
     * Alias of getInstance();
     *
     * @brief Init the ajax register
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
     * @brief Ajax actions list
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

        // since 1.4.21
        'wpdk_action_alert_dismiss'      => false,
      );

      return $actionsMethods;
    }

    /**
     * Return the autocomplete for users
     *
     * @brief Autocomplete for users
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

      $users = get_users( array(
          'blog_id'        => false,
          'search'         => '*' . $term . '*',
          'include'        => $include_blog_users,
          'exclude'        => $exclude_blog_users,
          'search_columns' => $query,
      ) );

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
          'value' => $user->user_email,
          'label' => sprintf( '%s %s %s (%s)', $img_avatar, $user->user_firstname, $user->user_lastname, $user->user_email ),
          'id'    => $user->ID
        );
      }

     	wp_die( json_encode( $return ) );

    }

    /**
     * Display the autocomplete for input tag. List all user by term.
     *
     * @brief    Do autocomplete
     *
     * @internal string $_POST['term'] Term to searching for
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
          echo json_encode( $result );
        }
      }
      die();
    }

    /**
     * This method is such `wpdk_action_user_by` but used for custom autocomplete.
     *
     * @brief      Custom autocomplete
     * @deprecated Since 1.0.0.b4
     *
     * @internal   string $_POST['term'] Term to searching for
     *
     * @return string JSON encode with search results
     */
    public function wpdk_action_autocomplete()
    {
      $pattern         = esc_attr( $_POST['term'] );
      $autocomplete_id = esc_attr( $_POST['autocomplete_id'] );

      $result = apply_filters( 'wpdk_autocomplete', array(), $pattern, $autocomplete_id );
      echo json_encode( $result );
      die();
    }

    /**
     * Return a posts list for type and status
     *
     * @brief Autocomplete posts
     *
     * @return string
     */
    public function wpdk_action_autocomplete_posts()
    {
      global $wpdb;

      // Get params
      $post_type   = esc_attr( isset( $_POST['post_type'] ) ? $_POST['post_type'] : WPDKPostType::PAGE );
      $post_status = esc_attr( isset( $_POST['post_status'] ) ? $_POST['post_status'] : WPDKPostStatus::PUBLISH );
      $limit       = esc_attr( isset( $_POST['limit'] ) ? sprintf( 'LIMIT 0,%s', $_POST['limit'] ) : '' );
      $order       = esc_attr( isset( $_POST['order'] ) ? $_POST['order'] : 'ASC' );
      $orderby     = esc_attr( isset( $_POST['orderby'] ) ? $_POST['orderby'] : 'post_title' );
      $term        = esc_attr( isset( $_POST['term'] ) ? $_POST['term'] : '' );

      /* Prepare response. */
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

      echo json_encode( $response );
      die();
    }


    /**
     * Dismiss a WPDK pointer for an user
     *
     * @brief Dismiss pointer
     */
    public function wpdk_action_dismiss_wp_pointer()
    {
      $id_user             = get_current_user_id();
      $pointer             = esc_attr( $_POST['pointer'] );
      $dismissed           = unserialize( get_user_meta( $id_user, 'wpdk_dismissed_wp_pointer', true ) );
      $dismissed[$pointer] = true;
      update_user_meta( $id_user, 'wpdk_dismissed_wp_pointer', serialize( $dismissed ) );
      die();
    }

    /**
     * Permanent alert dismiss by user logged in
     *
     * @brief Permanent alert dismiss
     * @since 1.4.21
     */
    public function wpdk_action_alert_dismiss()
    {
      $response = new WPDKAjaxResponse();

      // Get the alert id via post
      $alert_id = isset( $_POST['alert_id'] ) ? $_POST['alert_id'] : '';

      // Stability
      if( empty( $alert_id ) ) {
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
      $dismissed = get_user_meta( $user_id, WPDKTwitterBootstrapAlert::USER_META_KEY_PERMANENT_DISMISS, true );
      $dismissed = empty( $dismissed ) ? array() : $dismissed;

      // Add this alert id and make array unique - avoid duplicate
      $dismissed = array_unique( array_merge( $dismissed, (array)$alert_id ) );
      update_user_meta( $user_id, WPDKTwitterBootstrapAlert::USER_META_KEY_PERMANENT_DISMISS, $dismissed );

      $response->json();
    }



  } // class WPDKServiceAjax

} // if( wpdk_is_ajax() )

/// @endcond