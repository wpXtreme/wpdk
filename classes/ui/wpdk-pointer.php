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
 * Manage a single WordPress Pointer
 *
 * @class              WPDKPointer
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */

class WPDKPointer {

  /**
   * The box model alignment of pointer, default is `top`
   *
   * @brief Alignment
   *
   * @var string $align
   */
  public $align;

  /**
   * This is the dismiss/close button. For default this button perform an Ajax call and write in option that this
   * pointer is no longer to display.
   *
   * @brief Button close
   *
   * @var array $buttonClose
   */
  public $buttonClose;

  /**
   * This is a custom primary button shows on the left of dismiss button
   *
   * @brief Custom primary button
   *
   * @var array $buttonPrimary
   */
  public $buttonPrimary;
  /**
   * The HTML markup of pointer content. This can be a simple string without markup. In this case a p tag is added.
   *
   * @brief Content
   *
   * @var string $content
   */
  public $content;
  /**
   * The pointer edge arrow, default is `left`
   *
   * @brief Pointer edge arrow
   *
   * @var string $edge
   */
  public $edge;
  /**
   * Pointer ID
   *
   * @brief ID
   *
   * @var string $id
   */
  public $id;
  /**
   * This is the next page slug for automatic tour
   *
   * @brief Next page slug
   *
   * @var string $nextPage
   */
  public $nextPage;
  /**
   * A jQuery selector HTML tag.
   *
   * @brief jQuery selector
   *
   * @var string $selector
   */
  public $selector;
  /**
   * The pointer title
   *
   * @brief Title
   *
   * @var string $title
   */
  public $title;

  /**
   * Create an instance of WPDKPointer class
   *
   * @brief Construct
   *
   * @param string $id       Uniq id for this Pointer
   * @param string $selector jQuery selector
   * @param string $title    Title
   * @param string $content  HTML markup content
   * @param string $edge     Edge, default `left`
   * @param string $align    Alignment, default `top`
   *
   * @return WPDKPointer
   */
  public function __construct( $id, $selector, $title, $content, $edge = 'left', $align = 'top' ) {
    $this->id       = sanitize_key( $id );
    $this->selector = $selector;
    $this->title    = $title;
    $this->content  = $content;
    $this->edge     = $edge;
    $this->align    = $align;

    $this->nextPage = '';

    $this->buttonClose = array(
      'id'       => 'wpdk-pointer-button-close',
      'label'    => __( 'Dismiss', WPDK_TEXTDOMAIN ),
      'dismiss'  => true,
      'function' => '',
      'page'     => ''
    );

    $this->buttonPrimary = array(
      'id'    => 'wpdk-button-pointer-next',
      'label' => __( 'Next', WPDK_TEXTDOMAIN ),
      'page'  => ''
    );

    add_action( 'admin_print_footer_scripts', array( $this, 'display' ) );

  }

  /**
   * Print the javascript for WP Pointer
   *
   * @brief Print the javascript for WP Pointer
   */
  public function display() {

    /* Get info pointer. */
    $pointer_id = $this->id;
    $selector   = $this->selector;

    /* Build the pointer content. */
    $args = array(
      'content'  => sprintf( '%s%s', $this->_title(), $this->_content() ),
      'position' => array(
        'edge'  => $this->edge,
        'align' => $this->align
      )
    );

    /* If empty exit. */
    if ( empty( $pointer_id ) || empty( $selector ) || empty( $args ) || empty( $args['content'] ) ) {
      return;
    }

    /* Controllo se l'utente a deciso di dismettere questo pointer, in tal caso non lo visualizzo */
    $id_user = get_current_user_id();

    /* Check if dismiss, see 'action_wpdk_dismiss_wp_pointer' in Ajax for detail. */
    $dismissed = unserialize( get_user_meta( $id_user, 'wpdk_dismissed_wp_pointer', true ) );
    if ( isset( $dismissed[$pointer_id] ) ) {
      return;
    }

    /* Build a standard next button when the property nextPage is not empty. */
    if ( !empty( $this->nextPage ) ) {
      $this->buttonPrimary['page'] = $this->nextPage;
    }

    ?>
    <script type="text/javascript">
      //<![CDATA[
      jQuery( document ).ready( function ( $ ) {
        var options = <?php echo json_encode( $args ); ?>, setup;

        if ( !options ) {
          return;
        }

        options = $.extend( options, {

          buttons : function ( event, t ) {
            var button = jQuery( '<?php echo $this->_buttonClose() ?>' );
            button.bind( 'click.pointer', function () {
              t.element.pointer( 'close' );
            } );
            return button;
          },

          close : function () {
            var button = jQuery( '#<?php echo $this->buttonClose['id'] ?>' );
            <?php if ( empty( $this->buttonClose['function'] ) ) : ?>
            if( button.data('dismiss') ) {
              $.post( ajaxurl, {
                pointer : '<?php echo $pointer_id; ?>',
                action  : 'wpdk_action_dismiss_wp_pointer'
              } );
            }
            <?php else: ?>
            <?php echo $this->buttonClose['function'] ?>
            <?php endif; ?>
          }
        } );

        $( '<?php echo $selector; ?>' ).pointer( options ).pointer( 'open' );

        <?php if ( !empty( $this->buttonPrimary ) && !empty( $this->buttonPrimary['page']) ) : ?>
        $( '#<?php echo $this->buttonClose['id'] ?>' ).after( '<?php echo $this->_buttonPrimary() ?>' );
        $( '#<?php echo $this->buttonPrimary['id'] ?>' ).click( function () {
          <?php
          if ( empty( $this->buttonPrimary['function'] ) && !empty( $this->buttonPrimary['page'] ) ) {
              echo 'window.location="' . admin_url( 'admin.php?page=' . $this->buttonPrimary['page'] ) . '";';
          }
          elseif ( !empty( $this->buttonPrimary['function'] ) ) {
              echo $this->buttonPrimary['function'];
          }
          ?>
        } );
        <?php endif; ?>

      } );
      //]]>
    </script>
  <?php
  }

  /**
   * Return the right HTML markup for the pointer title. If the title has not a markup then a tag h3 is added.
   *
   * @brief Get title
   *
   * @return string
   */
  private function _title() {
    if ( '<' != substr( $this->title, 0, 1 ) ) {
      return sprintf( '<h3>%s</h3>', $this->title );
    }
    return $this->title;
  }

  /**
   * Return the right HTML markup for the pointer content. If the content has not a markup then a tag p is added.
   *
   * @brief Get content
   *
   * @return string
   */
  private function _content() {
    if ( '<' != substr( $this->content, 0, 1 ) ) {
      return sprintf( '<p>%s</p>', $this->content );
    }
    return $this->content;
  }

  /**
   * Return the HTML markup for close button
   *
   * @brief Get close button
   *
   * @return string
   */
  private function _buttonClose() {
    $dismiss = '';
    if( isset( $this->buttonClose['dismiss']) && true ===  $this->buttonClose['dismiss'] ) {
      $dismiss = 'data-dismiss="true"';
    }
    $result = sprintf( '<a id="%s" %s class="button-secondary">%s</a>', $this->buttonClose['id'], $dismiss, $this->buttonClose['label'] );
    return $result;
  }

  /**
   * Return the HTML markup for secondary button
   *
   * @brief Get secondary button
   *
   * @return string
   */
  private function _buttonPrimary() {
    $result = sprintf( '<a id="%s" style="margin-right:5px" class="button-primary">%s</a>', $this->buttonPrimary['id'], $this->buttonPrimary['label'] );
    return $result;
  }

}

/**
 * @prototype
 *
 * @class           WPDKPointerButton
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-06-19
 * @version         1.0.0
 *
 */
class WPDKPointerButton {

  public $id;
  public $page;
  public $dismiss;

}


/// @endcond
