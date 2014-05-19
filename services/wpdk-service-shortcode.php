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
 * Register the WPDK base shortcode for WordPress
 *
 * ## Overview
 *
 * These are the low-level shortcode for WordPress with prefix `wpdk_`.
 *
 * @class              WPDKServiceShortcode
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2014-01-09
 * @version            1.0.1
 */
class WPDKServiceShortcode extends WPDKShortcodes {

  /**
   * Alias of getInstance();
   *
   * @brief Init the shortcode register
   *
   * @return WPDKServiceShortcode
   */
  public static function init()
  {

    $button = new WPDKEditorButton( 'wpdk-shortcode', 'WPDK Shortcodes', '_WPDKShortcodes.open_dialog()', WPDK_URI_CSS . 'images/wpdk-shortcodes.png' );
    $mce_plugin = new WPDKTinyMCEPlugin( 'WPDKShortcodes', 'WPDK Shortcodes', WPDK_URI_JAVASCRIPT . 'wpdk-shortcode.js', array( $button ), '1.0.0' );

    return self::getInstance();
  }

  /**
   * Create or return a singleton instance of WPDKServiceShortcode
   *
   * @brief Create or return a singleton instance of WPDKServiceShortcode
   *
   * @return WPDKServiceShortcode
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
   * Display a content of shortcode only if the user is logged in. In addition you can set below attributes:
   *
   *     roles  - A list of roles string, comma separated. Eg: administrator, subscriber
   *     caps   - A list of capabilities string, comma separated. Eg. read, level_0
   *     emails - A list of emails string, comma separated. Eg. a.agima@commodore.com, c.sf@gmail.com
   *     ids    - A list of user id, comma separated. Eg. 12,13,14
   *
   * For instance:
   *
   *     [wpdk_is_user_logged_in roles='subscriber']
   *     [wpdk_is_user_logged_in roles='subscriber' caps="adv_perm, adv_read"]
   *     [wpdk_is_user_logged_in emails='a.agima@commodore.com' caps="adv_perm, adv_read"]
   *     [wpdk_is_user_logged_in ids='134']
   *
   * @brief Display a content for user logged in
   *
   * @param array  $atts     Attribute into the shortcode
   * @param string $content  Optional. $content HTML content
   *
   * @return bool|string
   */
  public function wpdk_is_user_logged_in( $atts, $content = null )
  {
    if ( is_user_logged_in() && !is_null( $content ) ) {

      /* Set default attributes. */
      $defaults = array(
        'roles'  => '',
        'caps'   => '',
        'emails' => '',
        'ids'    => ''
      );

      /* Merge with shortcode. */
      $args = shortcode_atts( $defaults, $atts, 'wpdk_is_user_logged_in' );

      /* Check for role. */
      if ( !empty( $args['roles'] ) ) {
        $found = false;
        $roles = explode( ',', $args['roles'] );
        $user_id = get_current_user_id();
        $user    = new WPDKUser( $user_id );
        foreach ( $roles as $role ) {
          if ( in_array( $role, $user->roles ) ) {
            $found = true;
            break;
          }
        }
        if( false === $found ) {
          return;
        }
      }

      /* Check for caps. */
      if ( !empty( $args['caps'] ) ) {
        $found = false;
        $caps = explode( ',', $args['caps'] );
        $user_id = get_current_user_id();
        $user    = new WPDKUser( $user_id );
        foreach ( $caps as $cap ) {
          if ( array_key_exists( $cap, $user->allcaps ) ) {
            $found = true;
            break;
          }
        }
        if( false === $found ) {
          return;
        }
      }

      /* Check for emails. */
      if ( !empty( $args['emails'] ) ) {
        $emails  = explode( ',', $args['emails'] );
        $user_id = get_current_user_id();
        $user    = new WPDKUser( $user_id );
        if ( !in_array( $user->email, $emails ) ) {
          return;
        }
      }

      /* Check for ids. */
      if ( !empty( $args['ids'] ) ) {
        $ids     = explode( ',', $args['ids'] );
        $user_id = get_current_user_id();
        if ( !in_array( $user_id, $ids ) ) {
          return;
        }
      }

      return $content;
    }
  }

  /**
   * Display a content of shortcode only if the user is NOT logged in.
   *
   * @brief Display a content for NOT user logged in
   *
   * @param array  $attrs     Attribute into the shortcode
   * @param string $content   Optional. $content HTML content
   *
   * @return bool|string
   */
  public function wpdk_is_user_not_logged_in( $attrs, $content = null )
  {
    if ( !is_user_logged_in() && !is_null( $content ) ) {
      return $content;
    }
  }

  /**
   * Return the GitHub include script Gist. Use [wpdk_gist id="762771662"]
   *
   * @brief GitHub Gist
   *
   * @param array  $atts      Attribute into the shortcode
   * @param string $content   Optional. $content HTML content
   *
   * @return string
   */
  public function wpdk_gist( $atts, $content = null )
  {
    return sprintf( '<script src="https://gist.github.com/%s.js%s"></script>', $atts['id'], isset( $atts['file'] ) ? '?file=' . $atts['file'] : '' );
  }

  /**
   * Return a Key value pairs array with key as shortcode name and value TRUE/FALSE for turn on/off the shortcode.
   *
   * @brief List of allowed shorcode
   *
   * @return array Shortcode array
   */
  protected function shortcodes()
  {
    $shortcodes = array(
      'wpdk_is_user_logged_in'     => true,
      'wpdk_is_user_not_logged_in' => true,
      'wpdk_gist'                  => true,
    );
    return $shortcodes;
  }

} // class WPDKServiceShortcode

/// @endcond