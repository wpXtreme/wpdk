<?php
/**
 * Constant class for alert type
 *
 * @class              WPDKTwitterBootstrapAlertType
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-01-07
 * @version            1.1.1
 * @note               Updated to Bootstrap v3.0.0
 *
 */
class WPDKTwitterBootstrapAlertType {
  /* @deprecated const since 1.3.1 and Bootstrap v3.0.0 */
  const ALERT       = 'alert-error';

  const SUCCESS     = 'alert-success';
  const INFORMATION = 'alert-info';
  const WARNING     = 'alert-warning';
  const DANGER      = 'alert-danger';

  /* Since 1.4.8 */
  const WHITE       = 'alert-white';
}

/**
 * Utility for Twitter Bootstrap Alert
 *
 * ## Overview
 * The WPDKTwitterBootstrapAlert class is a Twitter Bootstrap alert wrap.
 *
 * ### Create an Alert
 * To create and display an alert just coding:
 *
 *     $alert = new WPDKTwitterBootstrapAlert( 'my-alert', 'Hello World!' );
 *     $alert->display();
 *
 * OR
 *
 *     class myAlert extends WPDKTwitterBootstrapAlert {
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
 * @class              WPDKTwitterBootstrapAlert
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2014-01-16
 * @version            1.6.1
 * @note               Updated HTML markup and CSS to Bootstrap v3.0.0
 *
 */
class WPDKTwitterBootstrapAlert extends WPDKTwitterBootstrap {

  /**
   * @deprecated Since 1.0.0.b4 - Use dismissButton instead
   */
  public $dismiss_button;

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
   * HTML content
   *
   * @brief Content
   *
   * @var string $content
   */
  public $content = '';

  /**
   * Use const in WPDKTwitterBootstrapAlertType
   *
   * @brief Alert type
   *
   * @var string $type
   */
  public $type;

  /**
   * Set TRUE for alert-block class style
   *
   * @brief Block layout
   * @deprecated Since WPDK 1.3.1 and Bootstrap 3.0.0
   *
   * @var bool $block
   */
  public $block;

  /**
   * Create an instance of WPDKTwitterBootstrapAlert class
   *
   * @brief Construct
   *
   * @param string $id      This alert id
   * @param string $content Optional. An HTML content for this alert
   * @param string $type    Optional. See WPDKTwitterBootstrapAlertType. Default WPDKTwitterBootstrapAlertType::INFORMATION
   * @param string $title   Optional. Title of alert
   *
   * @return WPDKTwitterBootstrapAlert
   */
  public function __construct( $id, $content = '', $type = WPDKTwitterBootstrapAlertType::INFORMATION, $title = '' )
  {
    parent::__construct( $id );

    $this->content = $content;
    $this->type    = $type;
    $this->title   = $title;
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
    if ( $this->dismissButton ) {
      WPDKHTML::startCompress(); ?>
      <button
        type="button"
        class="close <?php echo empty( $this->dismissToolTip ) ? '' : 'wpdk-tooltip' ?>"
        <?php echo empty( $this->dismissToolTip ) ? '' : 'title="' . $this->dismissToolTip . '"' ?>
        data-dismiss="alert">×</button>
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
    return empty( $this->title ) ? sprintf( '<h4>%s</h4>', $this->title() ) : sprintf( '<h4>%s</h4>', $this->title );
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
    WPDKHTML::startCompress() ?>
    <div
      <?php echo empty( $this->id ) ? '' : 'id="' . $this->id . '"' ?>
      <?php echo empty( $this->data ) ? '' : self::dataInline( $this->data )  ?>
      class="<?php echo self::classInline( $this->class, array( $this->type, 'wpdk-alert', 'fade', 'in', 'clearfix' ) )  ?>">
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
  function alert( $echo = true ) {
    if ( $echo ) {
      $this->display();
    }
    else {
      return $this->html();
    }
  }

}