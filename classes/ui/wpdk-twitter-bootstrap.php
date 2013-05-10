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
 * IUnnknow for Twitter Bootstrap
 *
 * ## Overview
 * The WPDKTwitterBootstrap class is an abstract iunknow class for subclass Twitter Bootstrap. At this moment this
 * class is not used yet.
 *
 * @class              WPDKTwitterBootstrap
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-02-28
 * @version            1.0.1
 *
 */

class WPDKTwitterBootstrap {

  /**
   * The id attribute of main container
   *
   * @brief ID
   *
   * @var strinf $id
   */
  public $id;

  /**
   * HTML data attributes list. A key value pair array with key as data attribute name and value as value of data
   * attribute.
   *
   * @brief Data
   *
   * @var array $data
   */
  public $data;

  /**
   * Additional classes list
   *
   * @brief Classes
   *
   * @var array|string $classes
   */
  public $classes;

  /**
   * Create an instance of WPDKTwitterBootstrap class
   *
   * @brief Construct
   *
   * @param string $id The id attribute of main container
   *
   * @return WPDKTwitterBootstrap
   */
  public function __construct( $id ) {
    $this->id   = $id;
    $this->data = array();
  }

  /**
   * Return the HTML markup for 'data-' attributes list.
   *
   * @brief Build data attribute
   *
   * @return string
   */
  protected function data() {
    $result = '';
    $stack  = array();
    if ( !empty( $this->data ) ) {
      foreach ( $this->data as $key => $value ) {
        $stack[] = sprintf( 'data-%s="%s"', $key, htmlspecialchars( stripslashes( $value ) ) );
      }
      $result = join( ' ', $stack );
    }
    return $result;
  }

  /**
   * Return the additional classes list
   *
   * @brief Classes
   *
   * @return string
   */
  protected function classes() {
    $result = '';
    if ( !empty( $this->classes ) ) {
      $result = $this->classes;
      if ( is_array( $this->classes ) ) {
        $result = join( ' ', $this->classes );
      }
    }
    return $result;
  }

  /**
   * Add an attribute data
   *
   * @brief Add data
   */
  public function addData( $key, $value ) {
    $this->data[$key] = $value;
  }

  /**
   * Display the Twitter boostrap modal
   *
   * @brief Display
   */
  public function display() {
    echo $this->html();
  }

  /**
   * Return the HTML markup for Twitter boostrap modal
   *
   * @brief Get HTML
   *
   * @return string
   */
  public function html() {
    die( __METHOD__ . ' must be override in your subclass' );
  }

}


/**
 * Utility for Twitter Bootstrap Modal dialog.
 *
 * ## Overview
 * You can create in a simple way a standard twitter bootstrap dialog.
 *
 * ### Create a sinple Twitter Bootstrap modal dialog
 * For do this just instance this class like:
 *
 *     $modal = new WPDKTwitterBootstrapModal( 'myDialog', 'Dialog title', 'The content' );
 *     $modal->add_buttons( 'close', 'Close dialog' );
 *     $modal->modal();
 *
 * That's all
 *
 * @class              WPDKTwitterBootstrapModal
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-02-28
 * @version            0.8.5
 *
 */
class WPDKTwitterBootstrapModal extends WPDKTwitterBootstrap {

  /**
   * Title of modal (window)
   *
   * @brief Title
   *
   * @var string $title
   */
  public $title;

  /**
   * HTML content
   *
   * @brief Content
   *
   * @var string $content
   */
  public $content;

  /**
   * Width
   *
   * @brief Width
   *
   * @var int $width
   */
  public $width;

  /**
   * Height
   *
   * @brief Height
   *
   * @var int $height
   */
  public $height;

  /**
   * True for dismiss button [x] on top right
   *
   * @brief      Dismiss button
   *
   * @var bool $close_button
   * @deprecated Since 1.0.0.b4 - Use dismissButton instead
   */
  public $close_button;

  /**
   * True for dismiss button [x] on top right
   *
   * @brief Dismiss button
   *
   * @var bool $dismissButton
   */
  public $dismissButton;

  /**
   * List of buttons to display in footer
   *
   * @brief Footer buttons list
   *
   * @var array $buttons
   */
  public $buttons;

  /**
   * Closes the modal when escape key is pressed
   *
   * @brief Keyboard
   *
   * @var bool $keyboard
   */
  public $keyboard;

  /**
   * Includes a modal-backdrop element. Alternatively, specify `static` for a backdrop which doesn't close the modal on
   * click.
   *
   * @brief Backdrop
   *
   * @var bool $backdrop
   */
  public $backdrop;

  /**
   * Render a modal as static. Defaul is FALSE
   *
   * @var bool $static
   */
  public $static;

  /**
   * Create an instance of WPDKTwitterBootstrapModal class
   *
   * @brief Construct
   *
   * @param string $id      ID for this modal
   * @param string $title   The modal title
   * @param string $content Optional. An HTML content for dialog
   *
   * @return WPDKTwitterBootstrapModal
   */
  public function __construct( $id, $title, $content = '' ) {
    parent::__construct( $id );

    $this->title         = $title;
    $this->content       = $content;
    $this->width         = '';
    $this->height        = '';
    $this->static        = false;
    $this->keyboard      = 'true';
    $this->backdrop      = 'true';
    $this->close_button  = false;
    $this->dismissButton = true;

    $this->buttons = array();
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Private methods
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return the HTML aria title format
   *
   * @brief Return the aria title
   *
   * @return string
   */
  private function aria_title() {
    return sprintf( '%s-title', $this->id );
  }

  /**
   * Return the HTML markup for top right dismiss button [x]
   *
   * @brief Dismiss button
   *
   * @return string
   */
  private function dismissButton() {
    $result = '';
    if ( $this->dismissButton || $this->close_button ) {
      $result = '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
    }
    return $result;
  }

  /**
   * Return the computate style for width to use in main div of dialog.
   *
   * @brief Inline style for width
   * @since 1.0.0.b3
   *
   * @return string
   */
  private function width() {
    $result = '';
    if ( !empty( $this->width ) ) {
      $result = sprintf( 'width:%spx', $this->width );
    }
    return $result;
  }

  /**
   * Return the computate style for height to use in main content div of dialog.
   *
   * @brief Inline style for height
   * @since 1.0.0.b3
   *
   * @return string
   */
  private function height() {
    $result = '';
    if ( !empty( $this->height ) ) {
      $result = sprintf( 'style="height:%spx"', $this->height );
    }
    return $result;
  }

  /**
   * Return the HTML markup for footer buttons
   *
   * @brief Footer buttons
   *
   * @return string
   */
  private function buttons() {
    $result = '';
    if ( !empty( $this->buttons ) ) {
      $buttons = '';
      foreach ( $this->buttons as $key => $value ) {
        $class        = isset( $value['class'] ) ? $value['class'] : '';
        $label        = isset( $value['label'] ) ? $value['label'] : '';
        $data_dismiss = ( isset( $value['dismiss'] ) && true == $value['dismiss'] ) ? 'data-dismiss="modal"' : '';
        $buttons .= sprintf( '<button id="%s" class="btn button %s" %s aria-hidden="true">%s</button>', $key, $class, $data_dismiss, $label );
      }
    }

    if ( !empty( $buttons ) ) {
      $result = sprintf( '<div class="modal-footer">%s</div>', $buttons );
    }

    return $result;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Public methods
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * @deprecated Use display() or html() instead
   */
  public function modal( $echo = true ) {
    if ( $echo ) {
      $this->display();
    }
    else {
      return $this->html();
    }
  }

  /**
   * Return the HTML markup for Twitter boostrap modal
   *
   * @brief Get HTML
   *
   * @return string
   */
  public function html() {

    /* Get default data as properties. */
    $this->data['keyboard'] = $this->keyboard;
    $this->data['backdrop'] = $this->backdrop;

    ob_start(); ?>
    <div style="<?php echo $this->static ? 'position:relative;top:auto;left:auto;right:auto;margin:0 auto 20px;z-index:1;max-width:100%' : 'display:none;' ?><?php echo $this->width() ?>"
         class="modal <?php echo $this->static ? '' : 'hide fade' ?>" <?php echo $this->data() ?>
         id="<?php echo $this->id ?>"
         tabindex="-1"
         role="dialog"
         aria-labelledby="<?php echo $this->aria_title() ?>"
         aria-hidden="true">
      <div class="modal-header">
        <?php echo $this->dismissButton() ?>
        <h3 id="<?php echo $this->aria_title() ?>"><?php echo $this->title ?></h3>
      </div>
      <div <?php echo $this->height() ?> class="modal-body">
        <?php echo $this->content ?>
      </div>
      <?php echo $this->buttons() ?>
    </div>

    <?php
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }

  /**
   * Display immediately
   *
   * @brief Show
   */
  public function show() {
    ?>
    <script type="text/javascript">
      jQuery( document ).ready( function () {
        jQuery( '#<?php echo $this->id ?>' ).modal( 'show' );
      } );
    </script>
  <?php
  }

  /**
   * Toggle modal
   *
   * @brief Toggle
   */
  public function toggle() {
    ?>
    <script type="text/javascript">
      jQuery( document ).ready( function () {
        jQuery( '#<?php echo $this->id ?>' ).modal( 'toggle' );
      } );
    </script>
  <?php
  }

  /**
   * Hide immediately
   *
   * @brief Hide
   */
  public function hide() {
    ?>
    <script type="text/javascript">
      jQuery( document ).ready( function () {
        jQuery( '#<?php echo $this->id ?>' ).modal( 'hide' );
      } );
    </script>
  <?php
  }


  /**
   * @deprecated Sice 1.0.0.b4 - Use addButton() instead
   */
  public function add_buttons( $id, $label, $dismiss = true, $class = '' ) {
    _deprecated_function( __METHOD__, '1.0.0.b4', 'addButton()' );
    $this->addButton( $id, $label, $dismiss, $class );
  }

  /**
   * Add a footer button
   *
   * @brief Add button
   *
   * @param string $id      Unique id for button
   * @param string $label   Text label
   * @param bool   $dismiss Optional. True for data-dismiss
   * @param string $class   Optional. Addition CSS class
   */
  public function addButton( $id, $label, $dismiss = true, $class = '' ) {
    $this->buttons[$id] = array(
      'label'   => $label,
      'class'   => $class,
      'dismiss' => $dismiss
    );
  }


  /**
   * @deprecated Sice 1.0.0.b4 - Use addData() instead
   */
  public function add_data( $key, $value ) {
    _deprecated_function( __METHOD__, '1.0.0.b4', 'addData()' );
    //$this->data[] = array( $key => $value );
    $this->addData( $key, $value );
  }

  /**
   * Add an attribute data
   *
   * @brief Add data
   */
  public function addData( $key, $value ) {
    $this->data[$key] = $value;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Public utility methods
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * @deprecated Since 1.0.0.b4 - Use buttonOpenModal() instead
   */
  public function button_open_modal( $label, $class = '' ) {
    _deprecated_function( __METHOD__, '1.0.0.b4', 'buttonOpenModal()' );
    return $this->buttonOpenModal( $label, $class );
  }

  /**
   * Return the HTML markup for button tag to open this modal dialog
   *
   * @brief Return a button for open this dialog
   *
   * @param string $label Text button label
   * @param string $class Optional. Additional class
   *
   * @return string
   */
  public function buttonOpenModal( $label, $class = '' ) {
    $id     = sprintf( '#%s', $this->id );
    $result = sprintf( '<button class="button %s" type="button" data-toggle="modal" data-target="%s">%s</button>', $class, $id, $label );
    return $result;
  }

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
 * @class              WPDKTwitterBootstrapAlert
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-02-28
 * @version            1.1.1
 */
class WPDKTwitterBootstrapAlert extends WPDKTwitterBootstrap {

  /**
   * @deprecated Since 1.0.0.b4 - Use dismissButton instead
   */
  public $dismiss_button;

  /**
   * True to display dismiss [x] button. Default true
   *
   * @brief Dismiss button
   *
   * @var bool $dismissButton
   */
  public $dismissButton;

  /**
   * HTML content
   *
   * @brief Content
   *
   * @var string $content
   */
  public $content;

  /**
   * Use const in WPDKTwitterBootstrapAlertType
   *
   * @brief Alert type
   *
   * @var string
   */
  public $type;

  /**
   * Set TRUE for alert-block class style
   *
   * @brief Block layout
   *
   * @var bool
   */
  public $block;

  /**
   * Create an instance of WPDKTwitterBootstrapAlert class
   *
   * @brief Construct
   *
   * @param string $id      This alert id
   * @param string $content An HTML content for this alert
   * @param string $type    See WPDKTwitterBootstrapAlertType
   *
   * @return WPDKTwitterBootstrapAlert
   */
  public function __construct( $id, $content, $type = WPDKTwitterBootstrapAlertType::INFORMATION ) {
    parent::__construct( $id );

    $this->content = $content;
    $this->type    = $type;

    $this->dismissButton = true;
    $this->block         = false;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Private methods
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return alert-block class style
   *
   * @brief Block layout
   *
   * @return string
   */
  private function alert_block() {
    return $this->block ? 'alert-block' : '';
  }

  /**
   * Return the HTML markup button for dismiss
   *
   * @brief Dismiss button
   *
   * @return string
   */
  private function dismissButton() {
    $result = '';
    if ( $this->dismissButton ) {
      $result = '<button type="button" class="close" data-dismiss="alert">×</button>';
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
  private function content() {
    if ( '<' !== substr( $this->content, 0, 1 ) ) {
      return sprintf( '<p>%s</p>', $this->content );
    }
    return $this->content;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Public methods
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return the HTML markup for  a Twitter bootstrap alert
   *
   * @brief Get HTML
   *
   * @return string
   *
   */
  public function html() {
    ob_start(); ?>

    <div class="alert <?php echo $this->type ?> <?php echo $this->classes() ?> <?php echo $this->alert_block() ?>">
      <?php echo $this->dismissButton() ?>
      <?php echo $this->content() ?>
    </div>

    <?php
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }

  /* @deprecated Use display() or html() instead */
  function alert( $echo = true ) {
    if ( $echo ) {
      $this->display();
    }
    else {
      return $this->html();
    }
  }
}


/**
 * Constant class for alert type
 *
 * @class              WPDKTwitterBootstrapAlertType
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-02-28
 * @version            1.0.1
 */
class WPDKTwitterBootstrapAlertType {
  const ALERT       = 'alert-error';
  const SUCCESS     = 'alert-success';
  const INFORMATION = 'alert-info';
}


/**
 * Twitter Bootstrap buttons types
 *
 * ## Overview
 * This class enum all Twitter Bootstrap buttons types
 *
 * @class           WPDKTwitterBootstrapButtonType
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-02-28
 * @version         1.1.0
 *
 */
class WPDKTwitterBootstrapButtonType {
  const NONE    = '';
  const PRIMARY = 'btn-primary';
  const INFO    = 'btn-info';
  const SUCCESS = 'btn-success';
  const WARNING = 'btn-warning';
  const DANGER  = 'btn-danger';
  const LINK    = 'btn-link';
}


/**
 * Twitter Bootstrap buttons sizes
 *
 * ## Overview
 * This class enum all Twitter Bootstrap buttons sizes
 *
 * @class           WPDKTwitterBootstrapButtonSize
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-02-28
 * @version         1.1.0
 *
 */
class WPDKTwitterBootstrapButtonSize {
  const NONE  = '';
  const MINI  = 'btn-mini';
  const SMALL = 'btn-small';
  const LARGE = 'btn-large';
}


/**
 * Twitter Bootstrap Button
 *
 * ## Overview
 * This class is a wrap of Twitter Bootstrap Button
 *
 * @class           WPDKTwitterBootstrapButton
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-02-28
 * @version         1.1.0
 *
 */
class WPDKTwitterBootstrapButton extends WPDKTwitterBootstrap {

  /**
   * The button label
   *
   * @brief Label
   *
   * @var string $lable
   */
  public $label;

  /**
   * Any of WPDKTwitterBootstrapButtonType enum
   *
   * @brief Type
   *
   * @var string $type
   */
  public $type;

  /**
   * Any of WPDKTwitterBootstrapButtonSize enums
   *
   * @brief Size
   *
   * @var string $size
   */
  public $size;

  /**
   * Display the button as block
   *
   * @brief Block mode
   *
   * @var bool $block
   */
  public $block;

  /**
   * TRUE to disabled the button
   *
   * @brief Disabled
   *
   * @var bool $disabled
   */
  public $disabled;

  /**
   * Usage only for tag anchor
   *
   * @brief HREF
   *
   * @var string $href
   */
  public $href;

  /**
   * You can choose A, BUTTON or INPUT tag type. Usage WPDKHTMLTagName enums.
   *
   * @brief Tag type
   *
   * @var string $tag
   */
  public $tag;

  /**
   * Create and instance of WPDKTwitterBootstrapButton class
   *
   * @brief Construct
   *
   * @param string $id    ID and name of tag
   * @param string $label Label of button
   * @param string $type  Optional. Any WPDKTwitterBootstrapButtonTyped enum
   * @param string $tag   Optional. Type of tag
   *
   * @return WPDKTwitterBootstrapButton
   */
  public function __construct( $id, $label, $type = WPDKTwitterBootstrapButtonType::PRIMARY, $tag = WPDKHTMLTagName::BUTTON ) {
    parent::__construct( $id );

    $this->label = $label;
    $this->type  = $type;
    $this->tag   = $tag;
  }


  /**
   * Return the HTML markup for the button
   *
   * @brief HTML markup
   *
   * @return string
   */
  public function html() {

    $block    = $this->block ? 'btn-block' : '';
    $disabled = $this->disabled ? 'disabled' : '';
    $class    = trim( join( ' ', array( 'btn', $this->type, $this->size, $block, $disabled ) ) );

    ob_start();

    switch ( $this->tag ) {
      case WPDKHTMLTagName::A:
        $href = empty( $this->href ) ? '#' : $this->href;
        printf( '<a id="%s" name="%s" href="%s" class="%s">%s</a>', $this->id, $this->id, $href, $class, $this->label );
        break;
      case WPDKHTMLTagName::BUTTON:
        printf( '<input id="%s" name="%s" type="button" class="%s" value="%s" />', $this->id, $this->id, $class, $this->label );
        break;
      case WPDKHTMLTagName::INPUT:
        printf( '<input id="%s" name="%s" type="submit" class="%s" value="%s" />', $this->id, $this->id, $class, $this->label );
        break;
    }

    $html = ob_get_contents();
    ob_end_clean();

    return $html;
  }
}

// TODO
class WPDKTwitterBoostrapPopover {
}

/// @endcond