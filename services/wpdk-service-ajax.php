<?php
/// @cond private

if ( wpdk_is_ajax() ) {

  /**
   * Ajax Services Gateway
   *
   * @class              WPDKServiceAjax
   * @author             =undo= <info@wpxtre.me>
   * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
   * @date               2012-11-28
   * @version            0.8.1
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
    public static function getInstance() {
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
    public static function init() {
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
    protected function actions() {

      $actionsMethods = array(
        'wpdk_action_user_by'            => true,
        'wpdk_action_autocomplete'       => true,
        'wpdk_action_autocomplete_posts' => true,
        'wpdk_action_dismiss_wp_pointer' => true,
      );
      return $actionsMethods;
    }

    // -------------------------------------------------------------------------------------------------------------
    // Actions methods
    // -------------------------------------------------------------------------------------------------------------

    /**
     * Display the autocomplete for input tag. List all user by term.
     *
     * @brief Do autocomplete
     *
     * @internal string $_POST['term'] Term to searching for
     *
     * @return string JSON encode with search results
     */
    public function wpdk_action_user_by() {

      /* ID, name, email... */
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
     * @brief Custom autocomplete
     * @deprecated Since 1.0.0.b4
     *
     * @internal   string $_POST['term'] Term to searching for
     *
     * @return string JSON encode with search results
     */
    public function wpdk_action_autocomplete() {
      $pattern         = esc_attr( $_POST['term'] );
      $autocomplete_id = esc_attr( $_POST['autocomplete_id'] );

      /* @todo Do docs */
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
    public function wpdk_action_autocomplete_posts() {
      global $wpdb;

      /* Get params. */
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
SELECT post_name, post_title
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
            'value' => $post->post_name,
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
    public function wpdk_action_dismiss_wp_pointer() {
      $id_user             = get_current_user_id();
      $pointer             = esc_attr( $_POST['pointer'] );
      $dismissed           = unserialize( get_user_meta( $id_user, 'wpdk_dismissed_wp_pointer', true ) );
      $dismissed[$pointer] = true;
      update_user_meta( $id_user, 'wpdk_dismissed_wp_pointer', serialize( $dismissed ) );
      die();
    }

  } // class WPDKServiceAjax

} // if( wpdk_is_ajax() )

/// @endcond