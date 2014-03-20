<?php

/**
 * Useful Modal dialog.
 *
 * ## Overview
 * You can create in a simple way a standard dialog.
 *
 * ### Create a simple modal dialog
 * For do this just instance this class like:
 *
 *     $modal = new WPDKUIModalDialog( 'myDialog', 'Dialog title', 'The content' );
 *     $modal->addButton( 'close', 'Close dialog' );
 *     $modal->modal();
 *
 * That's all, or since 1.4.9 you can extends this class
 *
 *     class MyDialog extends WPDKUIModalDialog {
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
 * @class              WPDKUIModalDialog
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-02-13
 * @version            1.0.0
 * @since              1.4.21
 * @note               Updated HTML markup and CSS to Bootstrap v3.1.0
 *
 */
class WPDKUIModalDialog extends WPDKHTMLTag {

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $version
   */
  public $__version = '1.0.0';

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
   * Render as static. Default is FALSE
   *
   * @brief Static
   *
   * @var bool $static
   */
  public $static = false;

  /**
   * Glyph used like dismiss button
   *
   * @brief Dismiss button glyph icon
   * @since 1.4.22
   *
   * @var string $dismiss_button_glyph
   */
  public $dismiss_button_glyph = '×';

  /**
   * Create an instance of WPDKUIModalDialog class
   *
   * @brief Construct
   *
   * @param string $id      ID for this modal
   * @param string $title   The modal title
   * @param string $content Optional. An HTML content for dialog
   *
   * @return WPDKUIModalDialog
   */
  public function __construct( $id, $title, $content = '' )
  {

    $this->id      = sanitize_title( $id );
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
    if ( $this->dismissButton  ) {
      $result = '<button type="button" class="close" data-dismiss="wpdkModal" aria-hidden="true">' . $this->dismiss_button_glyph . '</button>';
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
        $data_dismiss = ( isset( $value['dismiss'] ) && true == $value['dismiss'] ) ? 'data-dismiss="wpdkModal"' : '';
        $stack .= sprintf( '<button type="button" %s id="%s" class="button %s" %s aria-hidden="true">%s</button>', $title, $key, $class, $data_dismiss, $label );
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
   * Return the HTML markup for modal dialog
   *
   * @brief Get HTML
   *
   * @return string
   */
  public function html()
  {

    // Get default data as properties
    $this->data['keyboard'] = $this->keyboard;
    $this->data['backdrop'] = $this->backdrop;

    WPDKHTML::startCompress(); ?>

    <div style="<?php echo $this->static ? 'position:relative;top:auto;left:auto;right:auto;margin:0 auto 20px;z-index:1;max-width:100%' : 'display:none;' ?>"
         class="wpdk-modal <?php echo $this->static ? '' : 'fade' ?>"
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

    return WPDKHTML::endCompress();
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