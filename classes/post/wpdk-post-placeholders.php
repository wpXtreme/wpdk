<?php

/**
 * Description
 *
 * @class           WPDKPostPlaceholders
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-06-04
 * @version         1.0.0
 *
 */
class WPDKPostPlaceholders {

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
    add_action( 'add_meta_boxes', array( 'WPDKPostPlaceholdersMetaBoxView', 'init' ) );

    // Filter the list of registered placeholder.
    add_filter( 'wpdk_post_placeholders', array( $this, 'wpdk_post_placeholders' ) );
  }

  /**
   * Filter the list of registered placeholder.
   *
   * @param array $placeholders An array key value pairs with the list of registered placeholders.
   */
  public function wpdk_post_placeholders( $placeholders )
  {

    $wpdk_mail_placeholders = array(
      self::USER_FIRST_NAME   => array( __( 'User First name', WPDK_TEXTDOMAIN ), 'Core' ),
      self::USER_LAST_NAME    => array( __( 'User Last name', WPDK_TEXTDOMAIN ), 'Core' ),
      self::USER_DISPLAY_NAME => array( __( 'User Display name', WPDK_TEXTDOMAIN ), 'Core' ),
      self::USER_EMAIL        => array( __( 'User email', WPDK_TEXTDOMAIN ), 'Core' ),
    );

    return array_merge( $placeholders, $wpdk_mail_placeholders );
  }

}

/**
 * Description
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