<?php
/**
 * Useful class to manage mail as post
 *
 * ## Overview
 * Extends _WPDKPost with useful method and property to mail
 *
 * @class           WPDKMail
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-02-18
 * @version         1.0.0
 *
 */
class WPDKMail extends _WPDKPost {

  /**
   * Create an instance of WPDKMail class
   *
   * @brief Construct
   *
   * @param string|int|object|null $mail      Optional. Post ID, post object, post slug or null
   * @param string                 $post_type Optional. If $record is a string (slug) then this is the post type where search.
   *                                          Default is 'page'
   *
   * @return WPDKMail
   */
  public function __construct( $mail = null, $post_type = 'page' ) {
    parent::__construct( $mail, $post_type );
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Public methods
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Perform a send mail. Return FALSE if an error occour.
   *
   * @brief Send mail
   *
   * @param string|int      $to                     String target 'name <email>' or user id
   * @param bool|string     $subject                Optional. String subject for this mail, if FALSE will bw used the title of
   *                                                post-mail
   * @param bool|int|string $from                   Optional. String from 'name <email>' or user id, set to empty to use default
   *                                                blog name and admin email
   * @param array           $placeholders           Optional. A Key value pairs with placeholders substitution.
   *
   * @return bool
   */
  public function send( $to, $subject = false, $from = '', $placeholders = array() ) {

    if ( is_numeric( $from ) ) {
      $user = new WP_User( $from );
      $from = sprintf( '%s <%s>', $user->data->display_name, $user->get( 'user_email' ) );
    }

    /* $from è nel formato 'NOME <email>', ad esempio: 'wpXtreme <info@wpxtre.me>' */
    if ( empty( $from ) ) {
      /* Get the defaul WordPress email. */
      $from = sprintf( '%s <%s>', get_option( 'blogname' ), get_option( 'admin_email' ) );
    }

    $headers = array(
      'From: ' . $from . "\r\n",
      'Content-Type: text/html' . "\r\n"
    );

    /* Se $to è un numero corriponde ad un id_user */
    $user = false;
    if ( is_numeric( $to ) ) {
      $user = new WP_User( $to );
      $to   = sprintf( '  %s <%s>', $user->data->display_name, $user->get( 'user_email' ) );
    }

    if ( $subject === false ) {
      $subject = apply_filters( 'the_title', $this->post_title );
    }

    //$body = apply_filters( 'the_content', $post->post_content );
    $body = $this->post_content;
    $body = $this->replacePlaceholder( $body, $user, $placeholders );

    return wp_mail( $to, $subject, $body, $headers );
  }

  /**
   * Replace every placeholder in content body mail with true data value. However some placeholder must be as extra
   * parameter. For example the password placeholder (the password is not decryptable) must be as extra param.
   *
   * @brief Replace placeholder with value
   *
   * @param string          $content Content to filter
   * @param bool|int|object $id_user Optional. User ID or FALSE to get the current user id. You can set as object WP_User
   * @param array           $extra   Optional. Extra placeholder to replace, for custom use.
   *
   * @return string
   */
  private function replacePlaceholder( $content, $id_user = false, $extra = array() ) {

    if ( false === $id_user ) {
      $id_user = get_current_user_id();
      $user    = new WP_User( $id_user );
    }
    elseif ( is_object( $id_user ) && is_a( $id_user, 'WP_User' ) ) {
      $user = $id_user;
    }
    elseif ( is_numeric( $id_user ) ) {
      $user = new WP_User( $id_user );
    }
    else {
      return $content;
    }

    $str_replaces = array(
      WPDKMailPlaceholder::USER_FIRST_NAME   => $user->get( 'first_name' ),
      WPDKMailPlaceholder::USER_LAST_NAME    => $user->get( 'last_name' ),
      WPDKMailPlaceholder::USER_DISPLAY_NAME => $user->data->display_name,
      WPDKMailPlaceholder::USER_EMAIL        => $user->data->user_email,
    );

    if ( !empty( $extra ) ) {
      $str_replaces = array_merge( $str_replaces, $extra );
    }

    $str_replaces = apply_filters( 'wpdk_mail_replace_placeholders', $str_replaces, $id_user );

    $content = strtr( $content, $str_replaces );

    return $content;
  }

}

/**
 * This class contains the definition of mail placeholder
 *
 * @class              WPDKMailPlaceholder
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-02-18
 * @version            0.8.1
 */
class WPDKMailPlaceholder {

  const USER_DISPLAY_NAME = '${USER_DISPLAY_NAME}';
  const USER_EMAIL        = '${USER_EMAIL}';
  const USER_FIRST_NAME   = '${USER_FIRST_NAME}';
  const USER_LAST_NAME    = '${USER_LAST_NAME}';

  /**
   * Return a key values pais array with the list of placehodlers. This array has a key with the placeholder string
   * and an array( description, plugin name )
   *
   * @brief Placeholders list
   *
   * @return array
   */
  public static function placeholders() {
    $placeholders = array(
      self::USER_FIRST_NAME   => array(
        __( 'User First name', WPDK_TEXTDOMAIN ),
        'Core'
      ),
      self::USER_LAST_NAME    => array(
        __( 'User Last name', WPDK_TEXTDOMAIN ),
        'Core'
      ),
      self::USER_DISPLAY_NAME => array(
        __( 'User Display name', WPDK_TEXTDOMAIN ),
        'Core'
      ),
      self::USER_EMAIL        => array(
        __( 'User email', WPDK_TEXTDOMAIN ),
        'Core'
      ),
    );

    return apply_filters( 'wpdk_mail_placeholders', $placeholders );
  }

}