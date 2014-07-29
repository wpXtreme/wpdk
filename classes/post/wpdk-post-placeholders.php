<?php

/**
 * Manage the registered placeholders used to compose a mail from a post, page or any custom post type.
 * See thirth part extensions like Users Manager.
 *
 * @class           WPDKPostPlaceholders
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-07-22
 * @version         1.0.1
 * @since           1.5.6
 *
 */
class WPDKPostPlaceholders {

  const DATE              = '${DATE}';
  const DATE_TIME         = '${DATE_TIME}';
  const USER_DISPLAY_NAME = '${USER_DISPLAY_NAME}';
  const USER_EMAIL        = '${USER_EMAIL}';
  const USER_FIRST_NAME   = '${USER_FIRST_NAME}';
  const USER_LAST_NAME    = '${USER_LAST_NAME}';

  /**
   * Return a singleton instance of WPDKPostPlaceholders class
   *
   * @brief Singleton
   *
   * @return WPDKPostPlaceholders
   */
  public static function init()
  {
    static $instance = null;
    if ( is_null( $instance ) ) {
      $instance = new self();
    }

    return $instance;
  }

  /**
   * Create an instance of WPDKPostPlaceholders class
   *
   * @brief Construct
   *
   * @return WPDKPostPlaceholders
   */
  public function __construct()
  {
    // Fires after all built-in meta boxes have been added.
    add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ), 10, 2 );

    // Filter the list of registered placeholder.
    add_filter( 'wpdk_post_placeholders', array( $this, 'wpdk_post_placeholders' ) );

    // Filter the content with standard placeholder
    add_filter( 'wpdk_post_placeholders_content', array( $this, 'wpdk_post_placeholders_content' ), 10, 4 );

    // Filter the standard WPDK (Core) array
    add_filter( 'wpdk_post_placeholders_array', array( $this, 'wpdk_post_placeholders_array' ), 10, 2 );
  }

  /**
   * Return a standard array with user placeholder and values.
   *
   * @since 1.5.8
   *
   * @param array $array   Optional. A custom key value array.
   * @param bool  $user_id Optional. User id or FALSE for current user logged in. This param could be set to -1, in this
   *                       case the original to email is an external address: no WordPress user.
   *
   * @return array
   */
  public function wpdk_post_placeholders_array( $array = array(), $user_id = false )
  {
    // If user id is empty but nobody is logged in exit
    if ( ( empty( $user_id ) && ! is_user_logged_in() ) || $user_id < 0 ) {
      return $array;
    }

    $user = new WPDKUser( $user_id );

    $defaults = array(
      // TODO Think to add a filter for placeholder
      self::DATE              => date( 'j M, Y' ),
      self::DATE_TIME         => date( 'j M, Y H:i:s' ),
      self::USER_DISPLAY_NAME => $user->display_name,
      self::USER_FIRST_NAME   => $user->first_name,
      self::USER_LAST_NAME    => $user->last_name,
      self::USER_EMAIL        => $user->email
    );

    //WPXtreme::log( $defaults );

    return array_merge( $array, $defaults );

  }

  /**
   * Filter the content with standard placeholder.
   *
   * @brief Placeholder content
   *
   * @param string $content       The content.
   * @param int    $user_id       Optional. User id or null for current user.
   * @param array  $replace_pairs Optional. It's an array in the form array( 'from' => 'to', ...).
   * @param array  $args          Optional. Mixed extra params.
   */
  public function wpdk_post_placeholders_content( $content, $user_id = false, $replace_pairs = array(), $args = array() )
  {
    // Merge
    $replaces = apply_filters( 'wpdk_post_placeholders_array', $replace_pairs, $user_id );

    return strtr( $content, $replaces );
  }

  /**
   * Fires after all built-in meta boxes have been added.
   *
   * @since 3.0.0
   *
   * @param string  $post_type Post type.
   * @param WP_Post $post      Post object.
   */
  public function add_meta_boxes( $post_type, $post )
  {
    /**
     * Filter used to display the WPDK Post Placeholders metabox.
     * Usually the placeholders maetabox is display only on post and page post type. If your custom post type would
     * display the placeholders metabox you have add this filter in your register custom post type init.
     *
     * @param bool    $display Set to TRUE to display placeholers metabox. Defaul FALSE.
     * @param WP_Post $post    Post object.
     */
    $display = apply_filters( 'wpdk_post_placeholders_metabox_will_display-' . $post_type, false, $post );

    if ( true === $display || in_array( $post_type, array( 'post', 'page' ) ) ) {

      // Add wpdk post placeholders metabox
      WPDKPostPlaceholdersMetaBoxView::init();

      // Welcome tour in all edit form
      add_action( 'edit_form_top', array( WPDKPostPlaceholdersTourModalDialog::init(), 'open' ) );
    }
  }

  /**
   * Filter the list of registered placeholder.
   *
   * @param array $placeholders An array key value pairs with the list of registered placeholders.
   */
  public function wpdk_post_placeholders( $placeholders )
  {

    $wpdk_mail_placeholders = array(
      self::DATE              => array( __( 'Date', WPDK_TEXTDOMAIN ), 'Core' ),
      self::DATE_TIME         => array( __( 'Date & Time', WPDK_TEXTDOMAIN ), 'Core' ),
      self::USER_FIRST_NAME   => array( __( 'User First name', WPDK_TEXTDOMAIN ), 'Core' ),
      self::USER_LAST_NAME    => array( __( 'User Last name', WPDK_TEXTDOMAIN ), 'Core' ),
      self::USER_DISPLAY_NAME => array( __( 'User Display name', WPDK_TEXTDOMAIN ), 'Core' ),
      self::USER_EMAIL        => array( __( 'User email', WPDK_TEXTDOMAIN ), 'Core' ),
    );

    return array_merge( $placeholders, $wpdk_mail_placeholders );
  }

}

/**
 * WPDK Post Placeholders Metabox View
 *
 * @class           WPDKPostPlaceholdersMetaBoxView
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-06-04
 * @version         1.0.0
 *
 */
class WPDKPostPlaceholdersMetaBoxView extends WPDKMetaBoxView {
  
  const ID = 'wpdk-post-placeholder-metabox-view';

  /**
   * Return a singleton instance of WPDKPostPlaceholdersMetaBoxView class
   *
   * @brief Singleton
   *
   * @return WPDKPostPlaceholdersMetaBoxView
   */
  public static function init()
  {
    static $instance = null;
    if ( is_null( $instance ) ) {
      $instance = new self();
    }

    return $instance;
  }

  /**
   * Create an instance of WPDKPostPlaceholdersMetaBoxView class
   *
   * @brief Construct
   *
   * @return WPDKPostPlaceholdersMetaBoxView
   */
  public function __construct()
  {
    parent::__construct( self::ID, __( 'Placeholders' ), null, WPDKMetaBoxContext::SIDE, WPDKMetaBoxPriority::HIGH );
  }

  /**
   * Display the HTML markup content for this view.
   *
   * @brief Display view content
   *
   * @return string
   */
  public function display()
  {
    /**
     * Filter the list of registered placeholder.
     *
     * @param array $placeholders An array key value pairs with the list of registered placeholders.
     */
    $placeholders = apply_filters( 'wpdk_post_placeholders', array() );

    // Reverse :)
    $placeholders = array_reverse( $placeholders, true );

    // This is impossible
    if( empty( $placeholders ) ) {
      _e( 'No PLaceholders registered/found' );
    }

    // Owner array
    $owners = array();

    // Build the owner select combo
    foreach ( $placeholders as $placeholder_key => $info ) {
      $key           = sanitize_title( $info[1] );
      $owners[ $key ] = $info[1];
    }
    ?>

    <select id="wpdk-post-placeholder-select" class="wpdk-ui-control wpdk-form-select">
      <option selected="selected" style="display:none" disabled="disabled"><?php _e( 'Filter by Owner' ) ?></option>
      <option value=""><?php _e( 'All' ) ?></option>
      <?php foreach( $owners as $key => $owner ) : ?>
        <option value="<?php echo $key ?>"><?php echo $owner ?></option>
      <?php endforeach ?>
    </select>

    <div class="wpdk-post-placeholders"><?php

    // Group by owner
    $owner = '';

    // Loop into the placeholders
    foreach( $placeholders as $placeholder_key => $info ) : ?>

      <?php echo ( $owner != $info[1] ) ? sprintf( '<small>%s</small>', $info[1] ) : '' ?>
      <?php $owner = $info[1] ?>

      <a onclick="window.parent.send_to_editor('<?php echo $placeholder_key ?>')"
            data-owner="<?php echo sanitize_title( $info[1] ) ?>"
            title="<?php echo $placeholder_key ?>"
            href="#"><?php printf( '%s %s', WPDKGlyphIcons::html( WPDKGlyphIcons::ANGLE_LEFT ), $info[0] ) ?></a>

    <?php endforeach; ?>

    </div>

    <script type="text/javascript">
      (function ( $ )
      {
        // Select
        var $select = $( '#wpdk-post-placeholder-select' );

        // Display by owner
        $select.on( 'change', function ()
        {
          if( empty( $( this ).val() ) ) {
            $( '.wpdk-post-placeholders' ).find( 'a,small' ).show();
          }
          else {
            $( '.wpdk-post-placeholders' ).find( 'a,small' ).hide();
            $( '.wpdk-post-placeholders' ).find( 'a[data-owner="'+ $( this ).val() +'"]' ).show();
          }
        } );

      })( jQuery );
    </script>

  <?php
  }

}

/**
 * WPDK Post Placeholder tour.
 * This tour will open only when the placeholder new dialog is visible on the screen.
 *
 * @class           WPDKPostPlaceholdersTourModalDialog
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-06-05
 * @version         1.0.0
 *
 */
class WPDKPostPlaceholdersTourModalDialog extends WPDKUIModalDialog {

  /**
   * An instance of WPDKUIPageView class
   *
   * @brief Page view
   *
   * @var WPDKUIPageView $page_view
   */
  private $page_view;

  /**
   * Return a singleton instance of WPDKPostPlaceholdersTourModalDialog class
   *
   * @brief Singleton
   *
   * @return WPDKPostPlaceholdersTourModalDialog
   */
  public static function init()
  {
    static $instance = null;
    if ( is_null( $instance ) ) {
      $instance = new self();
    }

    return $instance;
  }

  /**
   * Create an instance of WPDKPostPlaceholdersTourModalDialog class
   *
   * @brief Construct
   *
   * @return WPDKPostPlaceholdersTourModalDialog
   */
  public function __construct()
  {
    // Remember, change the id to reopen this dialog tour
    parent::__construct( 'wpdk-post-placeholder-welcome-tour', __( 'New Placeholders Metabox' ) );

    // Check if dismissed
    if( false === $this->is_dismissed() ) {

      // Permanent dismiss
      $this->permanent_dismiss = true;

      // Enqueue page view
      WPDKUIComponents::init()->enqueue( WPDKUIComponents::PAGE );

      // Display the page view
      $this->page_view = WPDKUIPageView::initWithHTML( $this->pages() );
    }
  }

  /**
   * Return the complete HTML mark with the pages to display.
   *
   * @brief Pages
   *
   * @return string
   */
  private function pages()
  {
    WPDKHTML::startCompress();
    ?>

    <div>
      <img style="margin-right:16px;height:380px" class="alignleft" src="data:image/png;base64,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" />
      <h3>News in WPDK v<?php echo WPDK_VERSION ?>,</h3>
      <p>from today you have a new Placeholder panel available when edit a post or a page. This new version is much more easy to use.</p>
      <p>You can see all registered Placeholders and just click on it to insert them into the editor. Also you can filter the registered Placeholders by selecting the menu group.</p>
      <p>Now you must not remember the Placeholder meaning, they are ever available on the screen and new one can coming soon with other plugins or extensions.</p>
    </div>

    <?php echo WPDKUIPageView::HTML_PAGE_SEPARATOR ?>

    <div>
      <p style="margin-top:48px" class="text-center">
        <img class="aligncenter" src="data:image/png;base64,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" />
      </p>
      <h1 style="font-size:60px;color:#666" class="text-center">Thank You</h1>
      <p class="text-center">By clicking on the close button this dialog will no longer open in future.</p>
    </div>

    <?php

    return WPDKHTML::endCompress();
  }

  /**
   * Content
   *
   * @brief Content
   * @return string
   */
  public function content()
  {
    return $this->page_view->html();
  }

  /**
   * Footer
   *
   * @brief Footer
   * @return string
   */
  public function footer()
  {
    return $this->page_view->navigator();
  }

}