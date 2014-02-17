<?php

/**
 * Utility for Twitter Bootstrap Popover
 *
 * ## Overview
 * The WPDKTwitterBootstrapPopover class is a Twitter Bootstrap alert wrap.
 *
 * ### Create an Alert
 * To create and display an alert just coding:
 *
 *     $alert = new WPDKTwitterBootstrapPopover( 'my-alert', 'Hello World!' );
 *     $alert->display();
 *
 * OR
 *
 *     class myAlert extends WPDKTwitterBootstrapPopover {
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
 * @class              WPDKTwitterBootstrapPopover
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2014-02-08
 * @version            1.0.0
 * @since              1.4.21
 *
 */
class WPDKTwitterBootstrapPopover extends WPDKTwitterBootstrap {

  /**
   * Apply a CSS fade transition to the popover
   *
   * @brief Animation
   *
   * @var bool $animation
   */
  public $animation = true;

  /**
   * Insert HTML into the popover. If false, jQuery's text method will be used to insert content into the DOM.
   * Use text if you're worried about XSS attacks.
   *
   * @brief HTML
   *
   * @var bool $html
   */
  public $html =  false;

  /**
   * How to position the popover - top | bottom | left | right | auto.
   * When "auto" is specified, it will dynamically reorient the popover. For example, if placement is "auto left",
   * the popover will display to the left when possible, otherwise it will display right.
   *
   * @brief Placement
   *
   * @var string $placement
   */
  public $placement = 'right';

  /**
   * If a selector is provided, popover objects will be delegated to the specified targets. In practice, this is used to
   * enable dynamic HTML content to have popovers added. See http://jsfiddle.net/fScua/.
   *
   * @brief Selector
   *
   * @var string $selector
   */
  public $selector = '';

  /**
   * How popover is triggered - click | hover | focus | manual
   *
   * @brief Trigger
   *
   * @var string $trigger
   */
  public $trigger = 'click';

  /**
   * Default title value if title attribute isn't present
   *
   * @brief Title
   *
   * @var string $title
   */
  public $title = '';

  /**
   * Default content value if data-content attribute isn't present
   *
   * @brief Content
   *
   * @var string $content
   */
  public $content = '';

  /**
   * Delay showing and hiding the popover (ms) - does not apply to manual trigger type.
   * If a number is supplied, delay is applied to both hide/show
   *
   * Object structure is a json: delay: { show: 500, hide: 100 }
   *
   * You can use array( 'show' => 500, 'hide' => 100 )
   *
   * @brief Delay
   *
   * @var int $delay
   */
  public $delay = 0;

  /**
   * Appends the popover to a specific element. Example: container: 'body'. This option is particularly useful in that it
   * allows you to position the popover in the flow of the document near the triggering element - which will prevent the
   * popover from floating away from the triggering element during a window resize.
   *
   * @brief Container
   *
   * @var string $container
   */
  public $container = '';

  /**
   * Create an instance of WPDKTwitterBootstrapPopover class
   *
   * @brief Construct
   *
   * @param string $id        Popover id
   * @param string $content   Optional. Content
   * @param string $placement Optional. top | bottom | left | right | auto. Default 'right'
   * @param string $title     Optional. Title
   *
   * @return WPDKTwitterBootstrapPopover
   */
  public function __construct( $id, $content = '', $placement = 'right', $title = '' )
  {
    parent::__construct( $id );

    $this->content   = $content;
    $this->placement = $placement;
    $this->title     = $title;
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
    WPDKHTML::startCompress() ?>
    <div
      <?php echo empty( $this->id ) ? '' : 'id="' . $this->id . '"' ?>
      <?php echo empty( $this->data ) ? '' : self::dataInline( $this->data ) ?>
      class="<?php echo self::classInline( $this->class, array(
        'wpdk-popover',
        $this->placement
      ) ) ?>">
      <div class="popover-arrow"></div>
      <h3 class="popover-title">
        <?php echo $this->_title() ?>
      </h3>
      <div class="popover-content">
        <?php echo $this->_content() ?>
      </div>
    </div>
    <?php
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }

  /**
   * Return an array item description in Control Layout Array format
   *
   * @brief To control
   * @todo Implement after WPDKUIControlType::POPOVER
   *
   * @return array
   */
  public function toControl()
  {
//    $item = array(
//      'type'           => WPDKUIControlType::ALERT,
//      'id'             => $this->id,
//      'alert_type'     => $this->type,
//      'dismiss_button' => $this->dismissButton,
//      'value'          => $this->content,
//      'title'          => $this->title,
//    );
//    return array( $item );
  }
}