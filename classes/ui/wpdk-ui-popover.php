<?php

/**
 * @class              WPDKUIPopoverPlacement
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-02-23
 * @version            1.0.0
 * @since              1.5.0
 */
class WPDKUIPopoverPlacement {

  const AUTO   = 'auto';
  const BOTTOM = 'bottom';
  const LEFT   = 'left';
  const RIGHT  = 'right';
  const TOP    = 'top';
}

/**
 * @class              WPDKUIPopover
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-02-23
 * @version            1.0.0
 * @since              1.5.0
 */
class WPDKUIPopover extends WPDKHTMLTag {

  /**
   * Apply a CSS fade transition to the popover
   *
   * @brief Animation
   *
   * @var bool $animation
   */
  public $animation = true;

  /**
   * Appends the popover to a specific element. Example: container: 'body'.
   * This option is particularly useful in that it allows you to position the popover in the flow of the document near
   * the triggering element - which will prevent the popover from floating away from the triggering element during a
   * window resize.
   *
   * @brief Container
   *
   * @var bool $container
   */
  public $container = false;

  /**
   * Content
   *
   * @brief Content
   *
   * @var string $content
   */
  public $content = '';

  /**
   * Delay showing and hiding the popover (ms) - does not apply to manual trigger type If a number is supplied, delay is
   * applied to both hide/show Object structure is: delay: { show: 500, hide: 100 }
   *
   * @brief Deleay
   *
   * @var int|string $delay
   */
  public $delay = 0;

  /**
   * Insert HTML into the popover. If false, jQuery's text method will be used to insert content into the DOM.
   * Use text if you're worried about XSS attacks.
   *
   * @brief HTML content
   *
   * @var bool $html
   */
  public $html = false;

  /**
   * ID attribute of main content
   *
   * @brief ID
   *
   * @var string $id
   */
  public $id = '';

  /**
   * How to position the popover - top | bottom | left | right | auto.
   * When "auto" is specified, it will dynamically reorient the popover.
   * For example, if placement is "auto left", the popover will display to the left when possible, otherwise it will
   * display right.
   *
   * @brief Placement
   *
   * @var string
   */
  public $placement = WPDKUIPopoverPlacement::RIGHT;

  /**
   * if a selector is provided, popover objects will be delegated to the specified targets.
   * In practice, this is used to enable dynamic HTML content to have popovers added.
   * See http://jsfiddle.net/fScua/
   *
   * @brief Selector
   *
   * @var string $selector
   */
  public $selector = '';

  /**
   * Render as static. Default is FALSE
   *
   * @brief Static
   *
   * @var bool $static
   */
  public $static = false;

  /**
   * Title
   *
   * @brief Title
   *
   * @var string $title
   */
  public $title = '';

  /**
   * How popover is triggered - click | hover | focus | manual
   *
   * @brief Trigger
   *
   * @var string $trigger
   */
  public $trigger = 'click';

  /**
   * Create an instance of WPDKUIPopover class
   *
   * @param string $id        ID attribute
   * @param string $title     Optional. Title of popover
   * @param string $content   Optional. Content of popover
   * @param string $placement Optional. Default WPDKUIPopoverPlacement::RIGHT
   *
   * @return WPDKUIPopover
   */
  public function __construct( $id, $title = '', $content = '', $placement = WPDKUIPopoverPlacement::RIGHT )
  {
    $this->id        = sanitize_title( $id );
    $this->title     = $title;
    $this->content   = $content;
    $this->placement = $placement;
  }

  /**
   * Return the HTML markup of Popover
   *
   * @brief Popover
   *
   * @return string
   */
  public function html()
  {

    // Get title
    $title = $this->title();
    $title = empty( $title ) ? '' : sprintf( '<h3 class="popover-title">%s</h3>', $title );

    WPDKHTML::startCompress(); ?>

    <div id="<?php echo $this->id ?>"
      class="<?php echo WPDKHTMLTag::classInline( array( 'wpdk-popover', $this->placement, $this->class, empty( $this->static ) ? '' : 'fade' ) ) ?>">
      <?php echo self::dataInline( $this->data ) ?>
      <div class="arrow"></div>
      <?php echo $title ?>
      <div class="popover-content">
        <?php echo $this->content() ?>
      </div>
    </div>

   <?php
    return WPDKHTML::endCompress();
  }

  /**
   * Title of popober
   *
   * @brief Title
   *
   * @return string
   */
  public function title()
  {
    // Override in subclass
    return $this->title;
  }

  /**
   * Return the content of popover
   *
   * @brief Content
   *
   * @return string
   */
  public function content()
  {
    // Override in subclass
    return $this->content;
  }




}