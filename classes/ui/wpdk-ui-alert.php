<?php

/**
 * Constant class for alert type.
 * This constants provides to change the aspect layout of alert (the left border color).
 *
 * @class              WPDKUIAlertType
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-02-13
 * @version            1.0.0
 * @since              1.4.21
 * @note               Updated to Bootstrap v3.1.0
 *
 */
class WPDKUIAlertType {
  const SUCCESS     = 'alert-success';
  const INFORMATION = 'alert-info';
  const WARNING     = 'alert-warning';
  const DANGER      = 'alert-danger';
  const WHITE       = 'alert-white';
}

/**
 * Useful Alert
 *
 * ### Create an Alert
 * To create and display an alert just coding:
 *
 *     $alert = new WPDKUIAlert( 'my-alert', 'Hello World!' );
 *     $alert->display();
 *
 * OR
 *
 *     class myAlert extends WPDKUIAlert {
 *
 *       // Internal construct
 *       public function __construct()
 *       {
 *          parent::__construct( $id, false, $type, $title );
 *       }
 *
 *       // Override content
 *       public function content()
 *       {
 *          echo 'Hello...';
 *       }
 *     }
 *
 * @class              WPDKUIAlert
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-02-13
 * @version            1.0.0
 * @since              1.4.21
 * @note               Updated HTML markup and CSS to Bootstrap v3.1.0
 *
 */
class WPDKUIAlert extends WPDKHTMLTag {

  // Used to store for each user the dismiss alert
  const USER_META_KEY_PERMANENT_DISMISS = '_wpdk_alert_dismiss';

  /**
   * Add `alert-dismissable` class in HTML markup.
   *
   * @brief Dismissable
   * @since 1.3.1
   *
   * @var bool $dismissable
   */
  public $dismissable = true;

  /**
   * This is the tooltip (for more detail) display on dismiss button.
   *
   * @brief Tooltip on dismiss button
   * @since 1.4.8
   *
   * @var string $dismissToolTip
   */
  public $dismissToolTip = '';

  /**
   * Title of the alter.
   *
   * @brief Title
   *
   * @var string $title
   */
  public $title = '';

  /**
   * Set TRUE to display dismiss [x] button. Default TRUE.
   *
   * @brief Display dismiss button
   *
   * @var bool $dismissButton
   */
  public $dismissButton = true;
  
  /**
   * If TRUE this alert is permanent dismiss by a logged in user
   *
   * @brief Permanent dismiss
   * @since 1.5.6
   *
   * @var bool $dismissPermanent
   */
  public $dismissPermanent = false;

  /**
   * Used to get/set the glyph icon used like dismiss button.
   *
   * @brief Dismiss button glyph icon
   * @since 1.5.6
   *
   * @var string $dismissButtonGlyph
   */
  public $dismissButtonGlyph = '×';

  /**
   * Glyph used like dismiss button
   *
   * @brief      Dismiss button glyph icon
   * @deprecated since 1.5.6 Use $dismissButtonGlyph instead
   *
   * @var string $dismiss_button_glyph
   */
  public $dismiss_button_glyph = '×';

  /**
   * The HTML content of alert.
   *
   * @brief Content
   *
   * @var string $content
   */
  public $content = '';

  /**
   * The alert type change the left border color. Use the constants in WPDKUIAlertType class.
   *
   * @brief Alert type
   *
   * @var string $type
   */
  public $type;

  /**
   * If TRUE this alert is permanet dismiss by a logged in user
   *
   * @brief      Permanent dismiss
   * @since      1.4.21
   * @deprecated 1.5.6 Use $dismissPermanent
   *
   * @var bool $permanent_dismiss
   */
  public $permanent_dismiss = false;

  /**
   * Set TRUE for alert-block class style
   *
   * @brief      Block layout
   * @deprecated Since WPDK 1.3.1 and Bootstrap 3.0.0
   *
   * @var bool $block
   */
  public $block = false;

  /**
   * List of permanent dismissed alert id.
   * Internal use only.
   *
   * @brief Permanent dismissed
   *
   * @var array $dismissed
   */
  protected $dismissed = array();

  /**
   * Create an instance of WPDKUIAlert class.
   *
   * @brief Construct
   *
   * @param string $id      Optional. This alert id. If FALSE a unique id is create in order to avoid potential alert
   *                        display missing.
   * @param string $content Optional. An HTML content for this alert
   * @param string $type    Optional. See WPDKUIAlertType. Default WPDKUIAlertType::INFORMATION
   * @param string $title   Optional. Title of alert
   *
   * @return WPDKUIAlert
   */
  public function __construct( $id = false, $content = '', $type = WPDKUIAlertType::INFORMATION, $title = '' )
  {
    // since 1.6.0 - create a unique id
    if ( false === $id ) {
      $id = md5( uniqid() . time() );
    }
    $this->id      = sanitize_title( $id );
    $this->content = $content;
    $this->type    = $type;
    $this->title   = $title;

    // Used for permanent dismiss
    if ( is_user_logged_in() ) {
      $user_id         = get_current_user_id();
      $this->dismissed = get_user_meta( $user_id, self::USER_META_KEY_PERMANENT_DISMISS, true );
    }
  }

  /**
   * Return the HTML markup button for dismiss
   *
   * @brief Dismiss button
   *
   * @return string
   */
  private function _dismissButton()
  {
    $result = '';

    // Title
    $title = '';

    // Classes
    $classes = array( 'close' );

    // Custom title/tooltip in button close
    if ( !empty( $this->dismissToolTip ) ) {
      $classes[] = 'wpdk-has-tooltip';
      $title     = sprintf( 'title="%s"', $this->dismissToolTip );
    }

    // Permanent dismiss by user logged in
    if( true === $this->dismissPermanent ) {
      $classes[] = 'wpdk-alert-permanent-dismiss';
      $title     = sprintf( 'title="%s"', __( 'By clicking on the X button, this alert won\'t appear' ) );
    }

    if ( $this->dismissButton ) {
      WPDKHTML::startCompress(); ?>
      <button
        type="button"
        class="<?php echo WPDKHTMLTag::classInline( $classes ) ?>"
        <?php echo $title ?>
        data-dismiss="wpdkAlert"><?php echo $this->dismissButtonGlyph ?></button>
      <?php
      $result = WPDKHTML::endHTMLCompress();
    }

    return $result;
  }

  /**
   * Return a formatted content
   *
   * @brief Content
   *
   * @return string
   */
  private function _content()
  {
    return empty( $this->content ) ? wpautop( $this->content() ) : wpautop( $this->content );
  }

  /**
   * Content
   *
   * @brief Content
   *
   * @return string
   */
  public function content()
  {
    // You can override this method
    return $this->content;
  }

  /**
   * Return the title
   *
   * @brief Title
   * @return string
   */
  private function _title()
  {
    if ( empty( $this->title ) ) {
      $this->title = $this->title();
    }

    return empty( $this->title ) ? '' : sprintf( '<h4>%s</h4>', $this->title );
  }

  /**
   * Title
   *
   * @brief Title
   * @return string
   */
  public function title()
  {
    // You can override this method
    return $this->title;
  }

  /**
   * Return the HTML markup for alert
   *
   * @brief Get HTML
   *
   * @return string
   *
   */
  public function html()
  {
    // Permanent dismiss
    if ( !empty( $this->dismissed ) && !empty( $this->id ) && in_array( md5( $this->id ), array_keys( $this->dismissed ) ) ) {
      return;
    }

    WPDKHTML::startCompress() ?>
    <div
      <?php echo empty( $this->id ) ? '' : 'id="' . $this->id . '"' ?>
      <?php echo empty( $this->data ) ? '' : self::dataInline( $this->data ) ?>
      class="<?php echo self::classInline( $this->class, array(
        $this->type,
        'wpdk-alert',
        $this->dismissable ? 'alert-dismissable' : '',
        'fade',
        'in',
        'clearfix'
      ) ) ?>">
      <?php echo $this->_dismissButton() ?>
      <?php echo $this->_title() ?>
      <?php echo $this->_content() ?>
    </div>

    <?php
    return WPDKHTML::endHTMLCompress();
  }

  /**
   * Return an array item description in Control Layout Array format
   *
   * @brief Brief
   * @since 1.0.0
   *
   * @return array
   */
  public function toControl()
  {
    $item = array(
      'type'           => WPDKUIControlType::ALERT,
      'id'             => $this->id,
      'alert_type'     => $this->type,
      'dismiss_button' => $this->dismissButton,
      'value'          => $this->content,
      'title'          => $this->title,
    );
    return array( $item );
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Deprecated
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return alert-block class style
   *
   * @brief Block layout
   *
   * @return string
   */
  private function alert_block()
  {
    _deprecated_function( __CLASS__ . '::' . __FUNCTION__, '1.3.1 and Bootstrap 3.0.0', '' );
    return $this->block ? 'alert-block' : '';
  }

  /**
   * @deprecated Use display() or html() instead
   */
  function alert( $echo = true )
  {
    if ( $echo ) {
      $this->display();
    }
    else {
      return $this->html();
    }
  }

}