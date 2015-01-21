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
 * @date               2014-06-09
 * @version            1.0.2
 * @since              1.4.21
 * @note               Updated HTML markup and CSS to Bootstrap v3.1.0
 *
 */
class WPDKUIModalDialog extends WPDKHTMLTag {

  // Used to store for each user the dismiss dialog
  const USER_META_KEY_PERMANENT_DISMISS = '_wpdk_modal_dismiss';

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
   * If TRUE this modal is permanet dismiss by a logged in user
   *
   * @brief Permanent dismiss
   * @since 1.5.6
   *
   * @var bool $permanent_dismiss
   */
  public $permanent_dismiss = false;

  /**
   * List of permanent dismissed dialog id
   *
   * @brief Permanent dismissed
   *
   * @var array $dismissed
   */
  protected $dismissed = array();

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

    // @since 1.5.6 permanent dismissed
    if ( is_user_logged_in() ) {
      $user_id         = get_current_user_id();
      $this->dismissed = get_user_meta( $user_id, self::USER_META_KEY_PERMANENT_DISMISS, true );
    }
  }

  /**
   * Return TRUE if this modal dialog is dismissed. FALSE otherwise.
   *
   * @brief Is dismissed
   * @return bool
   */
  public function is_dismissed()
  {
    if ( !empty( $this->dismissed ) ) {
      return in_array( md5( $this->id ), array_keys( $this->dismissed ) );
    }

    return false;
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
    // Prepare classes
    $classes = array( 'close' );

    // Title
    $title = '';

    // Permanent dismiss by user logged in
    if( true === $this->permanent_dismiss ) {
      $classes[] = 'wpdk-modal-permanent-dismiss';
      $title     = sprintf( 'title="%s"', __( 'By clicking on the X button, this dialog won\'t appear' ) );
    }

    $result = '';
    if ( $this->dismissButton  ) {
      $result = sprintf( '<button %s type="button" class="%s" data-dismiss="wpdkModal" aria-hidden="true">%s</button>', $title, WPDKHTMLTag::classInline( $classes ), $this->dismiss_button_glyph );
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
      $result = sprintf( 'width:%s', $this->width );
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
      $result = sprintf( 'style="height:%s"', $this->height );
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

      // Loop in buttons
      foreach ( $buttons as $key => $value ) {
        $class = isset( $value['class'] ) ? $value['class'] : '';
        $label = isset( $value['label'] ) ? $value['label'] : '';
        $title = isset( $value['title'] ) ? 'title="' . $value['title'] . '"' : '';
        $href  = isset( $value['href'] ) ? $value['href'] : '';

        // No tooltip
        if ( !empty( $title ) ) {
          $class .= ' wpdk-has-tooltip';
        }

        $data_dismiss = ( isset( $value['dismiss'] ) && true == $value['dismiss'] ) ? 'data-dismiss="wpdkModal"' : '';

        // Switch between button | a
        if( empty( $href ) ) {
          $result .= sprintf( '<button type="button" %s id="%s" class="button %s" %s aria-hidden="true">%s</button>', $title, $key, $class, $data_dismiss, $label );
        }
        // Use tag a
        else {
          $result .= sprintf( '<a href="%s" %s id="%s" class="button %s" %s aria-hidden="true">%s</a>', $href, $title, $key, $class, $data_dismiss, $label );
        }
      }
    }

    return $result;
  }

  /**
   * Return the footer content.
   *
   * @brief Footer
   *
   * @return string
   */
  private function _footer()
  {
    $footer = $this->footer();

    if( empty( $footer ) ) {
      $footer = $this->_buttons();
    }

    if( empty( $footer ) ) {
      return $footer;
    }

    return sprintf( '<div class="modal-footer">%s</div>', $footer );

  }

  /**
   * Content of the footer of dialog. You can override this method.
   *
   * @brief Footer
   *
   * @return string
   */
  public function footer()
  {
    return '';
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
    // Permanent dismiss
    if ( !empty( $this->dismissed ) && in_array( md5( $this->id ), array_keys( $this->dismissed ) ) ) {
      return;
    }

    // Get default data as properties
    $this->data['keyboard'] = $this->keyboard;
    $this->data['backdrop'] = $this->backdrop;

    WPDKHTML::startCompress(); ?>

    <div style="<?php echo $this->static ? 'position:relative;top:auto;left:auto;right:auto;margin:0 auto 20px;z-index:1;max-width:100%' : 'display:none;' ?>"
         class="wpdk-modal <?php echo $this->static ? '' : 'fade' ?>"
      <?php echo self::dataInline( $this->data ) ?>
         id="<?php echo $this->id ?>"
         tabindex="-1"
         role="wpdk-dialog"
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
          <?php echo $this->_footer() ?>
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
    if( is_admin() ) {

      // Prints any scripts and data queued for the footer.
      add_action( 'admin_print_footer_scripts', array( $this, 'print_footer_scripts' ), 999 );
    }
    else {
      // Fires when footer scripts are printed.
      add_action( 'wp_print_footer_scripts', array( $this, 'print_footer_scripts' ), 999 );
    }
  }

  /**
   * Prints any scripts and data queued for the footer.
   */
  public function print_footer_scripts()
  {
    WPDKHTML::startCompress(); ?>
    <script type="text/javascript">
      jQuery( function ( $ )
      {
        "use strict";
        $( '#<?php echo $this->id ?>' ).wpdkModal( 'show' );

        <?php

        /**
         * Fires when a modal dialog is show.
         *
         * The dynamic portion of the hook name, $id, refers to the modal dialod id.
         *
         * @since 1.7.0
         */
        do_action( 'wpdk_ui_modal_dialog_javascript_show-' . $this->id );

        ?>
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

        <?php

        /**
         * Fires when a modal dialog is toggle.
         *
         * The dynamic portion of the hook name, $id, refers to the modal dialod id.
         *
         * @since 1.7.0
         */
        do_action( 'wpdk_ui_modal_dialog_javascript_toogle-' . $this->id );

        ?>
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
    $result = sprintf( '<button class="button %s" type="button" data-toggle="wpdkModal" data-target="%s">%s</button>', $class, $id, $label );
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
   * @param string $href    Optional. If set use a TAG A instead BUTTON.
   */
  public function addButton( $id, $label, $dismiss = true, $class = '', $href = '' )
  {
    $this->buttons[ $id ] = array(
      'label'   => $label,
      'dismiss' => $dismiss,
      'class'   => $class,
      'href'    => $href,
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