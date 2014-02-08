<?php

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
 * @date               2014-02-07
 * @version            1.0.3
 *
 */
class WPDKTwitterBootstrap extends WPDKHTMLTag {

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $version
   */
  public $__version = '1.0.3';

  /**
   * Create an instance of WPDKTwitterBootstrap class
   *
   * @brief Construct
   *
   * @param string $id The id attribute of main container
   *
   * @return WPDKTwitterBootstrap
   */
  public function __construct( $id )
  {
    $this->id = $id;
  }

  /**
   * Return the HTML markup for Twitter boostrap modal
   *
   * @brief Get HTML
   *
   * @return string
   */
  public function html()
  {
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
 *     $modal->addButton( 'close', 'Close dialog' );
 *     $modal->modal();
 *
 * That's all, or since 1.4.9 you can extends this class
 *
 *     class MyDialog extends WPDKTwitterBootstrapModal {
 *
 *       // Internal construct
 *       public function __construct()
 *       {
 *          parent::__construct( $id, $title );
 *       }
 *
 *       // Override buttons
 *       public function buttons()
 *       {
 *          $buttons = array(
 *             'button_id' = array(
 *                'label'   => __( 'No, Thanks' ),
 *                'dismiss' => true,
 *              );
 *          );
 *          return $buttons; // Filtrable?
 *       }
 *
 *       // Override content
 *       public function content()
 *       {
 *          echo 'Hello...';
 *       }
 *     }
 *
 * @class              WPDKTwitterBootstrapModal
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2014-02-07
 * @version            1.1.1
 * @note               Updated HTML markup and CSS to Bootstrap v3.0.0
 *
 */
class WPDKTwitterBootstrapModal extends WPDKTwitterBootstrap {

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $version
   */
  public $__version = '1.1.0';

  /**
   * Title of modal (window)
   *
   * @brief Title
   *
   * @var string $title
   */
  public $title = '';

  /**
   * HTML content
   *
   * @brief Content
   *
   * @var string $content
   */
  public $content = '';

  /**
   * Width
   *
   * @brief Width
   *
   * @var int $width
   */
  public $width = '';

  /**
   * Height
   *
   * @brief Height
   *
   * @var int $height
   */
  public $height = '';

  /**
   * True for dismiss button [x] on top right
   *
   * @brief      Dismiss button
   *
   * @var bool $close_button
   * @deprecated Since 1.0.0.b4 - Use dismissButton instead
   */
  public $close_button = false;

  /**
   * True for dismiss button [x] on top right
   *
   * @brief Dismiss button
   *
   * @var bool $dismissButton
   */
  public $dismissButton = true;

  /**
   * List of buttons to display in footer
   *
   * @brief Footer buttons list
   *
   * @var array $buttons
   */
  public $buttons = array();

  /**
   * Closes the modal when escape key is pressed
   *
   * @brief Keyboard
   *
   * @var bool $keyboard
   */
  public $keyboard = 'true';

  /**
   * Includes a modal-backdrop element. Alternatively, specify `static` for a backdrop which doesn't close the modal on
   * click.
   *
   * @brief Backdrop
   *
   * @var bool $backdrop
   */
  public $backdrop = 'true';

  /**
   * Render a modal as static. Defaul is FALSE
   *
   * @var bool $static
   */
  public $static = false;

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
  public function __construct( $id, $title, $content = '' )
  {
    parent::__construct( $id );

    $this->title   = $title;
    $this->content = $content;
  }

  /**
   * Return the HTML aria title format
   *
   * @brief Return the aria title
   *
   * @return string
   */
  private function aria_title()
  {
    return sprintf( '%s-title', $this->id );
  }

  /**
   * Return the HTML markup for top right dismiss button [x]
   *
   * @brief Dismiss button
   *
   * @return string
   */
  private function dismissButton()
  {
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
  private function width()
  {
    $result = '';
    if ( !empty( $this->width ) ) {
      $result = sprintf( 'width:%spx', rtrim( $this->width, 'px' ) );
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
  private function height()
  {
    $result = '';
    if ( !empty( $this->height ) ) {
      $result = sprintf( 'style="height:%spx"', rtrim( $this->height, 'px' ) );
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
  private function _buttons()
  {
    $result  = '';
    $buttons = $this->buttons();

    if ( !empty( $buttons ) ) {
      $stack = '';
      foreach ( $buttons as $key => $value ) {
        $class = isset( $value['class'] ) ? $value['class'] : '';
        $label = isset( $value['label'] ) ? $value['label'] : '';
        $title = isset( $value['title'] ) ? 'title="' . $value['title'] . '"' : '';
        if ( !empty( $title ) ) {
          $class .= ' wpdk-has-tooltip';
        }
        $data_dismiss = ( isset( $value['dismiss'] ) && true == $value['dismiss'] ) ? 'data-dismiss="modal"' : '';
        $stack .= sprintf( '<button type="button" %s id="%s" class="btn button %s" %s aria-hidden="true">%s</button>', $title, $key, $class, $data_dismiss, $label );
      }
    }

    if ( !empty( $stack ) ) {
      $result = sprintf( '<div class="modal-footer">%s</div>', $stack );
    }

    return $result;
  }

  /**
   * Return a list of button
   *
   * @brief Buttons
   *
   * @return array
   */
  public function buttons()
  {
    // You can override this method
    return $this->buttons;
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
   * Display and open dialog
   *
   * @brief Open
   *
   * @since 1.4.9
   */
  public function open()
  {
    $this->display();
    $this->show();
  }

  /**
   * Return the HTML markup for Twitter boostrap modal
   *
   * @brief Get HTML
   *
   * @return string
   */
  public function html()
  {

    /* Get default data as properties. */
    $this->data['keyboard'] = $this->keyboard;
    $this->data['backdrop'] = $this->backdrop;

    ob_start(); ?>
    <div style="<?php echo $this->static ? 'position:relative;top:auto;left:auto;right:auto;margin:0 auto 20px;z-index:1;max-width:100%' : 'display:none;' ?>"
         class="wpdk-modal <?php echo $this->static ? '' : 'hide fade' ?>"
      <?php echo self::dataInline( $this->data ) ?>
         id="<?php echo $this->id ?>"
         tabindex="-1"
         role="dialog"
         aria-labelledby="<?php echo $this->aria_title() ?>"
         aria-hidden="true">
      <div style="<?php echo $this->width() ?>" class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <?php echo $this->dismissButton() ?>
            <h4 class="modal-title" id="<?php echo $this->aria_title() ?>"><?php echo $this->title ?></h4>
          </div>
          <div class="modal-body" <?php echo $this->height() ?>>
            <?php echo $this->content() ?>
          </div>
          <?php echo $this->_buttons() ?>
        </div>
      </div>
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
  public function show()
  {
    WPDKHTML::startCompress(); ?>
    <script type="text/javascript">
      jQuery( function ( $ )
      {
        "use strict";
        $( '#<?php echo $this->id ?>' ).wpdkModal( 'show' );
        <?php do_action( 'wpdk_tbs_dialog_javascript_show' ) ?>
        <?php do_action( 'wpdk_tbs_dialog_javascript_show-' . $this->id ) ?>
      } );
    </script>
    <?php echo WPDKHTML::endJavascriptCompress();
  }

  /**
   * Toggle modal
   *
   * @brief Toggle
   */
  public function toggle()
  {
    WPDKHTML::startCompress(); ?>
    <script type="text/javascript">
      jQuery( function ( $ )
      {
        $( '#<?php echo $this->id ?>' ).wpdkModal( 'toggle' );
      } );
    </script>
    <?php echo WPDKHTML::endJavascriptCompress();
  }

  /**
   * Hide immediately
   *
   * @brief Hide
   */
  public function hide()
  {
    WPDKHTML::startCompress(); ?>
    <script type="text/javascript">
      jQuery( function ( $ )
      {
        $( '#<?php echo $this->id ?>' ).wpdkModal( 'hide' );
      } );
    </script>
    <?php echo WPDKHTML::endJavascriptCompress();
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
  public function buttonOpenModal( $label, $class = '' )
  {
    $id     = sprintf( '#%s', $this->id );
    $result = sprintf( '<button class="button %s" type="button" data-toggle="modal" data-target="%s">%s</button>', $class, $id, $label );
    return $result;
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
  public function addButton( $id, $label, $dismiss = true, $class = '' )
  {
    $this->buttons[$id] = array(
      'label'   => $label,
      'class'   => $class,
      'dismiss' => $dismiss
    );
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Deprecated
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * @deprecated Use display() or html() instead
   */
  public function modal( $echo = true )
  {
    if ( $echo ) {
      $this->display();
    }
    else {
      return $this->html();
    }
  }

  /**
   * @deprecated Sice 1.0.0.b4 - Use addButton() instead
   */
  public function add_buttons( $id, $label, $dismiss = true, $class = '' )
  {
    _deprecated_function( __METHOD__, '1.0.0.b4', 'addButton()' );
    $this->addButton( $id, $label, $dismiss, $class );
  }


  /**
   * @deprecated Sice 1.0.0.b4 - Use addData() instead
   */
  public function add_data( $key, $value )
  {
    _deprecated_function( __METHOD__, '1.0.0.b4', 'addData()' );
    //$this->data[] = array( $key => $value );
    $this->addData( $key, $value );
  }

  /**
   * @deprecated Since 1.0.0.b4 - Use buttonOpenModal() instead
   */
  public function button_open_modal( $label, $class = '' )
  {
    _deprecated_function( __METHOD__, '1.0.0.b4', 'buttonOpenModal()' );
    return $this->buttonOpenModal( $label, $class );
  }
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
   * @var string $label
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
  public function __construct( $id, $label, $type = WPDKTwitterBootstrapButtonType::PRIMARY,
                               $tag = WPDKHTMLTagName::BUTTON )
  {
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
  public function html()
  {

    $block    = $this->block ? 'btn-block' : '';
    $disabled = $this->disabled ? 'disabled' : '';
    $class    = trim( join( ' ', array(
      'btn',
      $this->type,
      $this->size,
      $block,
      $disabled
    ) ) );

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

/**
 * @class WPDKTwitterBoostrapPopover
 * @note  Not implement yet
 */
class WPDKTwitterBoostrapPopover {
}