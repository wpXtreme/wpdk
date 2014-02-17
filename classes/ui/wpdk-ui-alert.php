<?php

/**
 * Constant class for alert type
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
   * Add `alert-dismissable` class
   *
   * @brief Dismissable
   * @since 1.3.1
   *
   * @var bool $dismissable
   */
  public $dismissable = true;

  /**
   * Tooltip on dismiss button
   *
   * @brief Tooltip
   * @since 1.4.8
   *
   * @var string $dismissToolTip
   */
  public $dismissToolTip = '';

  /**
   * Title
   *
   * @brief Title
   *
   * @var string $title
   */
  public $title = '';

  /**
   * True to display dismiss [x] button. Default true
   *
   * @brief Dismiss button
   *
   * @var bool $dismissButton
   */
  public $dismissButton = true;

  /**
   * Glyph used like dismiss button
   *
   * @brief Dismiss button glyph icone
   *
   * @var string $dismiss_button_glyph
   */
  public $dismiss_button_glyph = '×';

  /**
   * HTML content
   *
   * @brief Content
   *
   * @var string $content
   */
  public $content = '';

  /**
   * Use const in WPDKUIAlertType
   *
   * @brief Alert type
   *
   * @var string $type
   */
  public $type;

  /**
   * If TRUE this alert is permanet dismiss by a logged in user
   *
   * @brief Permanent dismiss
   * @since 1.4.21
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
  public $block;

  /**
   * List of permanent dismissed alert id
   *
   * @brief Permanent dismissed
   *
   * @var array $dismissed
   */
  protected $dismissed = array();

  /**
   * Create an instance of WPDKUIAlert class
   *
   * @brief Construct
   *
   * @param string $id      This alert id
   * @param string $content Optional. An HTML content for this alert
   * @param string $type    Optional. See WPDKUIAlertType. Default WPDKUIAlertType::INFORMATION
   * @param string $title   Optional. Title of alert
   *
   * @return WPDKUIAlert
   */
  public function __construct( $id, $content = '', $type = WPDKUIAlertType::INFORMATION, $title = '' )
  {
    $this->id      = sanitize_title( $id );
    $this->content = $content;
    $this->type    = $type;
    $this->title   = $title;

    // @since 1.4.21 permanent dismissed
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
  private function dismissButton()
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
    if( true === $this->permanent_dismiss ) {
      $classes[] = 'wpdk-alert-permanent-dismiss';
    }

    if ( $this->dismissButton ) {
      WPDKHTML::startCompress(); ?>
      <button
        type="button"
        class="<?php echo WPDKHTMLTag::classInline( $classes ) ?>"
        <?php echo $title ?>
        data-dismiss="alert"><?php echo $this->dismiss_button_glyph ?></button>
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
   * Return the HTML markup for  a Twitter bootstrap alert
   *
   * @brief Get HTML
   *
   * @return string
   *
   */
  public function html()
  {
    // Permanent dismiss
    if ( !empty( $this->dismissed ) && in_array( $this->id, $this->dismissed ) ) {
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
      <?php echo $this->dismissButton() ?>
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