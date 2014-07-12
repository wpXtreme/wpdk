<?php

/**
 * Useful class to manage mail as post.
 *
 * ## Overview
 * Extends WPDKPost with useful method and property to mail.
 *
 * @class           WPDKMail
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-05-15
 * @version         1.0.1
 *
 */
class WPDKMail extends WPDKPost {

  /**
   * Header for carbon copy
   *
   * @brief Carbon copy
   * @since 1.4.9
   *
   * @var string $cc
   */
  public $cc = '';

  /**
   * Header for carbon copy
   *
   * @brief Carbon copy
   * @since 1.4.9
   *
   * @var string $bcc
   */
  public $bcc = '';

  /**
   * From for header
   *
   * @brief From
   *
   * @var string $from
   */
  private $from = '';

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
  public function __construct( $mail = null, $post_type = 'page' )
  {
    parent::__construct( $mail, $post_type );
  }

  /**
   * Return the array with the available post type used to send a mail.
   *
   * @brief Post types for mail
   *
   * @return array
   */
  public static function postTypes()
  {
    // Register the post type for mail
    $post_types = array(
      'post' => 'Post',
      'page' => 'Page'
    );

    /**
     * Filter the available post types used to send a mail.
     *
     * @param array $post_types An array with post type id and label.
     */

    return apply_filters( 'wpdk_mail_post_types', $post_types );
  }

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
   * @return bool|WPDKError
   */
  public function send( $to, $subject = false, $from = '', $placeholders = array() )
  {
    // Set user id for replace placeholder - see classes/post/wpdk-post-placeholders.php
    $user_id = - 1;

    // Check for to
    if ( is_numeric( $to ) ) {
      $user_id = $to;
      $user_to = get_user_by( 'id', $user_id );
      $to      = sprintf( '%s <%s>', $user_to->data->display_name, $user_to->data->user_email );
    }
    // TODO recover id from email address

    // Use shared private property
    $this->from = $from;

    // $from is as 'NOME <email>', eg: 'wpXtreme <info@wpxtre.me>'
    if ( empty( $this->from ) ) {

      // Get the default WordPress email
      $this->from = sprintf( '%s <%s>', get_option( 'blogname' ), get_option( 'admin_email' ) );
    }

    // Check for from
    elseif ( is_numeric( $this->from ) ) {
      $user_from  = get_user_by( 'id', $from );
      $this->from = sprintf( '%s <%s>', $user_from->data->display_name, $user_from->data->user_email );
    }

    // If subject is empty get the post title
    if ( empty( $subject ) ) {

      /**
       * Filter the title of mail.
       *
       * @param string $title The title of mail.
       */
      $subject = apply_filters( 'the_title', $this->post_title );
    }

    /**
     * Filter the content of mail.
     *
     * @param string $content      The content of mail.
     * @param int    $user_id      The user ID.
     * @param array  $placeholders The array of placeholders.
     */
    $body = apply_filters( 'wpdk_post_placeholders_content', $this->post_content, $user_id, $placeholders );

    try {

      /**
       * Filter the to.
       *
       * @param string $to The to string in 'John Red <jred@gmail.com>' format.
       */
      $to = apply_filters( 'wpdk_mail_to', $to );

      WPXtreme::log( $to, 'filter to email' );

      $result = wp_mail( $to, $subject, $body, $this->headers() );
    }
    catch ( phpmailerException $e ) {
      return new WPDKError( 'wpdk-mail-send', $e->getMessage(), $e );
    }

    return $result;
  }

  /**
   * Return the computated header for mail
   *
   * @brief Headers
   *
   * @return string
   */
  private function headers()
  {
    // Build the header
    $headers = array(
      'From: ' . $this->from,
      'Content-Type: text/html'
    );

    // Added cc and bcc
    if ( !empty( $this->cc ) ) {
      $this->cc = explode( ',', $this->cc );
      foreach ( $this->cc as $email ) {
        $headers[] = sprintf( 'Cc: %s', $email );
      }
    }

    if ( !empty( $this->bcc ) ) {
      $this->bcc = explode( ',', $this->bcc );
      foreach ( $this->bcc as $email ) {
        $headers[] = sprintf( 'Bcc: %s', $email );
      }
    }

    /**
     * Filter the headers array.
     *
     * @param array $headers The headers array.
     */
    $headers = apply_filters( 'wpdk_mail_headers', $headers );

    return implode( "\r\n", $headers );
  }

}