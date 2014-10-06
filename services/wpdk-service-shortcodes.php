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
 * TODO To complete
 *
 * @class              WPDKServiceShortcodes
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2014-01-09
 * @version            1.0.1
 */
final class WPDKServiceShortcodes extends WPDKShortcodes {

  /**
   * Alias of getInstance();
   *
   * @return WPDKServiceShortcodes
   */
  public static function init()
  {
    return self::getInstance();
  }

  /**
   * Create or return a singleton instance of WPDKServiceShortcodes
   *
   * @return WPDKServiceShortcodes
   */
  public static function getInstance()
  {
    static $instance = null;
    if ( is_null( $instance ) ) {
      $instance = new self();

      // TODO Uncomment to complete the shortcodes manager view

      // Print the scripts for rich editor
      //add_action( 'admin_print_scripts', array( $instance, 'admin_print_scripts' ) );

      // Add action for mce buttons
      //add_action( 'admin_head', array( $instance, 'admin_head' ) );

      // Print the script for no rich editor
      //add_action( 'admin_print_footer_scripts', array( $instance, 'admin_print_footer_scripts' ) );

    }
    return $instance;
  }

  /**
   * Fires when scripts are printed for all admin pages.
   */
  public function admin_print_scripts()
  {
    // Load the WPDK MCE engine
    wp_enqueue_script( 'wpdk-mce', WPDK_URI_JAVASCRIPT . 'wpdk-mce.js', array(), WPDK_VERSION );
  }

  /**
   * Fires in <head> for all admin pages.
   */
  public function admin_head()
  {
    // check user permissions
    if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
      return;
    }

    add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );
    add_filter( 'mce_buttons', array( $this, 'mce_buttons' ) );
  }

  /**
   * Prints any scripts and data queued for the footer.
   * Right way to add a editor button in HTML view.
   */
  public function admin_print_footer_scripts()
  {
    WPDKHTML::startCompress(); ?>
    <script type="text/javascript">

      (function ()
      {
        QTags.addButton( 'wpdk_shortcodes_button', '[]', function ()
        {
          QTags.insertContent( 'Insert by No rich!!' );

          WPDKMCE.openDialog();
        } );
      })();

    </script>
    <?php
    echo WPDKHTML::endJavascriptCompress();
  }

  /**
   * Filter the list of TinyMCE external plugins.
   *
   * The filter takes an associative array of external plugins for
   * TinyMCE in the form 'plugin_name' => 'url'.
   *
   * The url should be absolute, and should include the js filename
   * to be loaded. For example:
   * 'myplugin' => 'http://mysite.com/wp-content/plugins/myfolder/mce_plugin.js'.
   *
   * If the external plugin adds a button, it should be added with
   * one of the 'mce_buttons' filters.
   *
   * @param array $external_plugins An array of external TinyMCE plugins.
   */
  public function mce_external_plugins( $plugin_array )
  {
    $plugin_array['wpdk_shortcodes_button'] = WPDK_URI_JAVASCRIPT . 'wpdk-mce-button.js';

    return $plugin_array;
  }

  /**
   * Filter the first-row list of TinyMCE buttons (Visual tab).
   *
   * @param array  $buttons   First-row list of buttons.
   * @param string $editor_id Unique editor identifier, e.g. 'content'.
   */
  public function mce_buttons( $buttons )
  {
    array_push( $buttons, 'wpdk_shortcodes_button' );

    return $buttons;
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
   * @param array  $atts     Attribute into the shortcode
   * @param string $content  Optional. $content HTML content
   *
   * @return bool|string
   */
  public function wpdk_is_user_logged_in( $atts, $content = null )
  {
    if ( is_user_logged_in() && !is_null( $content ) ) {

      // Set default attributes. 
      $defaults = array(
        'roles'  => '',
        'caps'   => '',
        'emails' => '',
        'ids'    => ''
      );

      // Merge with shortcode. 
      $args = shortcode_atts( $defaults, $atts, 'wpdk_is_user_logged_in' );

      // Check for role. 
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

      // Check for caps. 
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

      // Check for emails. 
      if ( !empty( $args['emails'] ) ) {
        $emails  = explode( ',', $args['emails'] );
        $user_id = get_current_user_id();
        $user    = new WPDKUser( $user_id );
        if ( !in_array( $user->email, $emails ) ) {
          return;
        }
      }

      // Check for ids. 
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

} // class WPDKServiceShortcodes

/// @endcond