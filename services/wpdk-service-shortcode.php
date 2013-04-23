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
 * @date               2013-01-09
 * @version            0.9.2
 */
class WPDKServiceShortcode extends WPDKShortcode {

  /**
   * Alias of getInstance();
   *
   * @brief Init the shortcode register
   *
   * @return WPDKServiceShortcode
   */
  public static function init() {
    return self::getInstance();
  }

  /**
   * Create or return a singleton instance of WPDKServiceShortcode
   *
   * @brief Create or return a singleton instance of WPDKServiceShortcode
   *
   * @return WPDKServiceShortcode
   */
  public static function getInstance() {
    static $instance = null;
    if ( is_null( $instance ) ) {
      $instance = new WPDKServiceShortcode();
    }
    return $instance;
  }

  /**
   * Display a content of shortcode only if the user is logged in.
   *
   * @brief Display a content for user logged in
   *
   * @param array       $attrs   Attribute into the shortcode
   * @param null|string $content HTML content
   *
   * @return bool|string
   */
  public function wpdk_is_user_logged_in( $attrs, $content = null ) {
    if ( is_user_logged_in() && !is_null( $content ) ) {
      return $content;
    }
  }

  /**
   * Display a content of shortcode only if the user is NOT logged in.
   *
   * @brief Display a content for NOT user logged in
   *
   * @param array       $attrs   Attribute into the shortcode
   * @param null|string $content HTML content
   *
   * @return bool|string
   */
  public function wpdk_is_user_not_logged_in( $attrs, $content = null ) {
    if ( !is_user_logged_in() && !is_null( $content ) ) {
      return $content;
    }
  }

  /**
   * Return a Key value pairs array with key as shortcode name and value TRUE/FALSE for turn on/off the shortcode.
   *
   * @brief List of allowed shorcode
   *
   * @return array Shortcode array
   */
  protected function shortcodes() {
    $shortcodes = array(
      'wpdk_is_user_logged_in'     => true,
      'wpdk_is_user_not_logged_in' => true,
    );
    return $shortcodes;
  }

} // class WPDKServiceShortcode

/// @endcond