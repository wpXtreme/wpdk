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
 * This class has the list of the constrols allowed
 *
 * @class              WPDKUIControlType
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-01-29
 * @version            0.8.2
 *
 */
class WPDKUIControlType {

  const ALERT       = 'WPDKUIControlAlert';
  const BUTTON      = 'WPDKUIControlButton';
  const CHECKBOX    = 'WPDKUIControlCheckbox';
  const CHOOSE      = 'WPDKUIControlChoose';
  const CUSTOM      = 'WPDKUIControlCustom';
  const DATE        = 'WPDKUIControlDate';
  const DATETIME    = 'WPDKUIControlDateTime';
  const EMAIL       = 'WPDKUIControlEmail';
  const FILE        = 'WPDKUIControlFile';
  const HIDDEN      = 'WPDKUIControlHidden';
  const LABEL       = 'WPDKUIControlLabel';
  const NUMBER      = 'WPDKUIControlNumber';
  const PASSWORD    = 'WPDKUIControlPassword';
  const PARAGRAPH   = 'paragraph';
  const PHONE       = 'WPDKUIControlPhone';
  const RADIO       = 'WPDKUIControlRadio';
  const SECTION     = 'WPDKUIControlSection';
  const SELECT      = 'WPDKUIControlSelect';
  const SELECT_LIST = 'WPDKUIControlSelectList';
  const SUBMIT      = 'WPDKUIControlSubmit';
  const SWIPE       = 'WPDKUIControlSwipe';
  const TEXT        = 'WPDKUIControlText';
  const TEXTAREA    = 'WPDKUIControlTextarea';
}

/**
 * This class describe a WPDK UI Control. A WPDK UI Control is as a set of simple HTML tag. For example a WPDK UI
 * Control of type input is compose by:
 *
 *     [control label][control input][extra]
 *
 * In detail a WPDK ui control has the follow structure:
 *
 *     [before]
 *       [before_label]
 *         [label control]
 *       [after_label]
 *         [prepend]
 *          [main control][extra]
 *       [append]
 *     [after]
 *
 * However every control class has its drawing method to manage different output.
 *
 * ## Overview
 * This class allow to make simple display button and common UI.
 *
 * @class              WPDKUI
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-01-31
 * @version            0.8.4
 *
 */
class WPDKUIControl {

  /**
   * Default attribute size for input tag
   *
   * @brief Attribute size
   * @since 1.0.0.b4
   */
  const DEFAULT_SIZE_ATTRIBUTE = 30;

  /**
   * A string with list of attributes
   *
   * @brief Attributes
   *
   * @var string $attrs
   */
  protected $attrs;

  /**
   * A string with CSS classes
   *
   * @brief CSS classes
   *
   * @var string $class
   */
  protected $class;
  /**
   * A string with list of data attributes
   *
   * @brief Data attributes
   *
   * @var string $data
   */
  protected $data;
  /**
   * A sanitize attribute id
   *
   * @brief Attribute ID
   *
   * @var string $id
   */
  protected $id;
  /**
   * A key value pairs array with the WPDK ui control description
   *
   * @brief WPDK UI Control array descriptor
   *
   * @var array $item
   */
  protected $item;
  /**
   * The name attribute
   *
   * @brief Name attribute
   *
   * @var string $name
   */
  protected $name;
  /**
   * A string with inlibe CSS styles
   *
   * @brief CSS inline style
   *
   * @var string $style
   */
  protected $style;
  /**
   * Keep an array with input size attribute for specific type
   *
   * @brief Get size
   * @since 1.0.0.b4
   *
   * @var array $_sizeForType
   */
  private $_sizeForType;

  /**
   * Create an instance of WPDKUIControl class
   *
   * @brief Construct
   *
   * @param array $item_control Control array
   */
  public function __construct( $item_control ) {
    $this->item = $item_control;

    /* Sanitize the common array key. */

    $this->attrs = $this->attrs();
    $this->data  = $this->data();
    $this->class = $this->classes();

    if ( isset( $this->item['id'] ) ) {
      $this->id = sanitize_key( $this->item['id'] );
    }
    elseif ( isset( $this->item['name'] ) ) {
      $this->id = sanitize_key( $this->item['name'] );
    }

    $this->name = isset( $this->item['name'] ) ? $this->item['name'] : '';

    /* Input size attribute for specific type. */
    $this->_sizeForType = array(
      WPDKUIControlType::DATE     => 10,
      WPDKUIControlType::DATETIME => 16,
      WPDKUIControlType::EMAIL    => 30,
      WPDKUIControlType::NUMBER   => 10,
      WPDKUIControlType::PHONE    => 10,
      WPDKUIControlType::TEXT     => 30,
      WPDKUIControlType::PASSWORD => 30,
    );
  }

  /**
   * Return a string with complete list of generic attributes as `title`, `id`, etc...
   *
   * @brief Attributes
   *
   * @return string
   */
  protected function attrs() {
    $result = '';
    if ( isset( $this->item['attrs'] ) ) {
      if ( is_array( $this->item['attrs'] ) ) {
        $stack = array();
        foreach ( $this->item['attrs'] as $attr => $value ) {
          $stack[] = sprintf( ' %s="%s"', $attr, $value );
        }
        if ( !empty( $stack ) ) {
          $result = join( ' ', $stack );
        }
      }
      elseif ( is_string( $this->item['attrs'] ) ) {
        $result = $this->item['attrs'];
      }
    }
    return $result;
  }

  /**
   * Return a string with complete list of data attributes as `data-name = "value"`. For example:
   *
   *     <div data-my_attribute="my_value"></div>
   *
   * The data asttributes are readable and writeable from jQuery with:
   *
   *     $( 'div' ).data( 'my_attribute' );
   *
   * @brief Data attributes
   *
   * @return string
   */
  protected function data() {
    $result = '';
    if ( isset( $this->item['data'] ) ) {
      if ( is_array( $this->item['data'] ) ) {
        $stack = array();
        foreach ( $this->item['data'] as $attr => $value ) {
          $stack[] = sprintf( 'data-%s="%s"', $attr, htmlspecialchars( stripslashes( $value ) ) );
        }
        if ( !empty( $stack ) ) {
          $result = join( ' ', $stack );
        }
      }
      elseif ( is_string( $this->item['data'] ) ) {
        $result = $this->item['data'];
      }
    }
    return $result;
  }

  /**
   * Return a string with complete list of CSS class
   *
   * @brief CSS Class
   *
   * @return string
   */
  protected function classes() {
    $result = '';
    if ( isset( $this->item['class'] ) ) {
      if ( is_array( $this->item['class'] ) ) {
        $stack = array();
        foreach ( $this->item['class'] as $value ) {
          $stack[] = $value;
        }
        if ( !empty( $stack ) ) {
          $result = join( ' ', $stack );
        }
      }
      elseif ( is_string( $this->item['class'] ) ) {
        $result = $this->item['class'];
      }
    }
    return $result;
  }

  /**
   * Display the control
   *
   * @brief Display
   */
  public function display() {
    echo $this->html();
  }

  /**
   * Return the HTML markup for control
   *
   * @brief Get HTML
   *
   * @return string
   */
  public function html() {
    ob_start();

    echo $this->contentWithKey( 'before' );

    $this->draw();

    echo $this->contentWithKey( 'after' );

    $content = ob_get_contents();
    ob_end_clean();

    return $content;
  }

  /**
   * Return the content by specific key. If the content is an instance of class WPDKUIControl then get the HTML.
   * If the content is an array (Controls Layout array) porcess it as sub-control.
   *
   * @brief Get content form array
   *
   * @param string $key String key in array
   *
   * @return string
   */
  protected function contentWithKey( $key ) {
    $result = '';
    if ( isset( $this->item[$key] ) ) {
      $content = $this->item[$key];
      if ( is_object( $content ) && is_a( $content, 'WPDKUIControl' ) ) {
        $result = $content->html();
      }
      elseif ( is_array( $content ) && isset( $content['type'] ) ) {
        $class_name = $content['type'];
        $control    = new $class_name( $content );
        $result     = $control->html();
      }
      elseif ( is_string( $content ) ) {
        $result = $content;
      }
      elseif( is_callable( $content ) ) {
        $result = call_user_func( $content, $this->item );
      }
    }
    return $result;
  }

  /**
   * This is a utility method to display a common input type
   *
   * @brief Common input control
   *
   * @param WPDKHTMLTagInputType $type  Optional. Type of input
   * @param string               $class Optional. CSS additional class
   */
  protected function inputType( $type = WPDKHTMLTagInputType::TEXT, $class = '' ) {
    echo $this->contentWithKey( 'prepend' );

    /* Create the label. */
    $label = $this->label();

    /* Display right label. */
    echo is_null( $label ) ? '' : $label->html();

    $input               = new WPDKHTMLTagInput( '', $this->name, $this->id );
    $input->type         = $type;
    $input->class        = implode( ' ', array( trim( $class), trim( $this->class ) , 'wpdk-form-input' ) );
    $input->data         = isset( $this->item['data'] ) ? $this->item['data'] : '';
    $input->value        = isset( $this->item['value'] ) ? $this->item['value'] : '';
    $input->autocomplete = isset( $this->item['autocomplete'] ) ? $this->item['autocomplete'] : null;
    $input->disabled     = isset( $this->item['disabled'] ) ? $this->item['disabled'] ? 'disabled' : null : null;
    $input->readonly     = isset( $this->item['readonly'] ) ? $this->item['readonly'] ? 'readonly' : null : null;
    $input->required     = isset( $this->item['required'] ) ? $this->item['required'] ? 'required' : null : null;

    if ( WPDKHTMLTagInputType::HIDDEN != $type ) {
      $input->size        = isset( $this->item['size'] ) ? $this->item['size'] : $this->sizeForType( $this->item['type'] );
      $input->title       = isset( $this->item['title'] ) ? $this->item['title'] : '';
      $input->placeholder = isset( $this->item['placeholder'] ) ? $this->item['placeholder'] : '';
    }

    $input->setPropertiesByArray( isset( $this->item['attrs'] ) ? $this->item['attrs'] : '' );

    if ( isset( $this->item['locked'] ) && true == $this->item['locked'] ) {
      $input->readonly = 'readonly';
    }

    $input->display();

    /* Add a clear field button only. */
    if ( in_array( $this->item['type'], array( WPDKUIControlType::DATE, WPDKUIControlType::DATETIME ) ) ) {
      $span_clear = new WPDKHTMLTagSpan();
      $span_clear->class = 'wpdk-form-clear-left';
      $span_clear->display();
    }

    if ( isset( $this->item['locked'] ) && true == $this->item['locked'] ) {
      printf( '<span title="%s" class="wpdk-form-locked wpdk-tooltip"></span>', __( 'This field is locked for your security. However you can unlock just by click here.', WPDK_TEXTDOMAIN ) );
    }

    echo $this->contentWithKey( 'append' );

    echo ' ' . $this->guide();
  }

  /**
   * Return an instance of WPDKHTMLTagLabel class or null if no label provided.
   *
   * @brief Standard label for controls
   *
   * @return null|WPDKHTMLTagLabel
   */
  protected function label() {
    if ( !isset( $this->item['label'] ) || empty( $this->item['label'] ) ) {
      return null;
    }

    if ( is_string( $this->item['label'] ) ) {
      $content = trim( $this->item['label'] );
    }
    elseif ( is_array( $this->item['label'] ) ) {
      $content = trim( $this->item['label']['value'] );
    }

    $before_label = isset( $this->item['beforelabel'] ) ? $this->item['beforelabel'] : '';
    $after_label  = isset( $this->item['afterlabel'] ) ? $this->item['afterlabel'] : ':';

    /* Special behavior (before) for these controls. */
    switch ( $this->item['type'] ) {
      case WPDKUIControlType::CHECKBOX:
        $after_label = isset( $this->item['afterlabel'] ) ? $this->item['afterlabel'] : '';
        break;
      case WPDKUIControlType::SWIPE:
        if ( isset( $this->item['label_placement'] ) && 'right' == $this->item['label_placement'] ) {
          $after_label = isset( $this->item['afterlabel'] ) ? $this->item['afterlabel'] : '';
        }
        break;
      default:
        if ( empty( $content ) ) {
          $after_label = isset( $this->item['afterlabel'] ) ? $this->item['afterlabel'] : '';
        }
        break;
    }

    /* Create the lable. */
    $label      = new WPDKHTMLTagLabel( $before_label . $content . $after_label );
    $label->for = $this->id;
    $label->class .= ' wpdk-form-label wpdk-tooltip';
    if ( is_array( $this->item['label'] ) ) {
      $label->data  = isset( $this->item['label']['data'] ) ? $this->item['label']['data'] : '';
      $label->style = isset( $this->item['label']['style'] ) ? $this->item['label']['style'] : '';
      $label->setPropertiesByArray( isset( $this->item['label']['attrs'] ) ? $this->item['label']['attrs'] : '' );
    }

    /* Special behavior (after) for these controls. */
    switch ( $this->item['type'] ) {
      case WPDKUIControlType::CHECKBOX:
        $label->class .= ' wpdk-form-checkbox';
        break;

      case WPDKUIControlType::SWIPE:
        if ( isset( $this->item['label_placement'] ) && 'right' == $this->item['label_placement'] ) {
          $label->class = str_replace( 'wpdk-tooltip', 'wpdk-form-label-inline', $label->class );
        }
        break;

      case WPDKUIControlType::TEXTAREA:
      case WPDKUIControlType::SELECT_LIST:
        $label->class .= ' wpdk-form-label-top';
        break;
    }

    $label->title = isset( $this->item['title'] ) ? $this->item['title'] : '';

    return $label;
  }

  /**
   * Return the specific attribute size for control type
   *
   * @brief Attribute size
   *
   * @param string $type Control type as defined in WPDKUIControlType
   *
   * @return int
   */
  private function sizeForType( $type ) {
    if ( !empty( $type ) && isset( $this->_sizeForType[$type] ) && !empty( $this->_sizeForType[$type] ) ) {
      return $this->_sizeForType[$type];
    }
    return self::DEFAULT_SIZE_ATTRIBUTE;
  }

  /**
   * Return the HTML markup for the guide engine
   *
   * @brief Guide
   * @since 1.0.0.b4
   *
   * @return string
   */
  protected function guide() {
    $result = '';
    if ( isset( $this->item['guide'] ) && !empty( $this->item['guide'] ) ) {
      $guide = $this->item['guide'];

      /* Standard assets. */
      $button_title = __( 'Guide', WPDK_TEXTDOMAIN );

      /* Title of modal window. */
      $title = __( 'Guide', WPDK_TEXTDOMAIN );

      /* Tooltip. */
      $tooltip = __( 'Open the guide', WPDK_TEXTDOMAIN );

      /* Simple guide link: 'unique uri' */
      if ( is_string( $guide ) ) {
        return sprintf( '<a href="%s" data-title="%s" class="wpdk-guide wpdk-tooltip" title="%s">%s</a>', $guide, $title, $tooltip, $button_title );
      }

      /* Array args guide: array( 'Title of modal', 'unique uri' ) */
      if ( is_array( $guide ) && is_numeric( key( $guide ) ) ) {
        $title = $guide[0];
        $uri   = $guide[1];
        return sprintf( '<a href="%s" data-title="%s" class="wpdk-guide wpdk-tooltip" title="%s">%s</a>', $uri, $title, $tooltip, $button_title );
      }

      /*
       * Array args guide:
       *
       * array(
       *      'title'   => 'Title of modal',
       *      'uri'     => 'unique uri',
       *      'button'  => 'Button label',
       *      'tooltip' => 'Title tooltips',
       *      'classes' => 'Additional css classes'
       * );
       *
       */
      if ( is_array( $guide ) && !empty( $guide['uri'] ) ) {
        $uri          = $guide['uri'];
        $title        = isset( $guide['title'] ) ? $guide['title'] : $title;
        $button_title = isset( $guide['button'] ) ? $guide['button'] : $button_title;
        $tooltip      = isset( $guide['tooltip'] ) ? $guide['tooltip'] : $tooltip;
        $classes      = isset( $guide['classes'] ) ? $guide['classes'] : '';

        return sprintf( '<a href="%s" data-title="%s" class="wpdk-guide wpdk-tooltip %s" title="%s">%s</a>', $uri, $title, $classes, $tooltip, $button_title );
      }

      $result = '<span>[#internal error - wrong guide]</span>';
    }

    return $result;
  }

}

/**
 * Simple Twitter Alert control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::ALERT,
 *         'alert_type'     => WPDKTwitterBootstrapAlertType::INFORMATION,
 *         'id'             => 'id',
 *         'value'          => 'Alert content',
 *         'dismiss_button' => true,
 *         'block'          => false,
 *         'prepend'        => '',
 *         'append'         => ''
 *     );
 *
 * @class              WPDKUIControlAlert
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKUIControlAlert extends WPDKUIControl {

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    $value      = isset( $this->item['value'] ) ? $this->item['value'] : '';
    $alert_type = isset( $this->item['alert_type'] ) ? $this->item['alert_type'] : WPDKTwitterBootstrapAlertType::INFORMATION;

    $alert                = new WPDKTwitterBootstrapAlert( $this->id, $value, $alert_type );
    $alert->dismissButton = isset( $this->item['dismiss_button'] ) ? $this->item['dismiss_button'] : true;
    $alert->block         = isset( $this->item['block'] ) ? $this->item['block'] : false;
    $alert->classes       = isset( $this->item['classes'] ) ? $this->item['classes'] : '';

    echo $this->contentWithKey( 'prepend' );

    $alert->display();

    echo $this->contentWithKey( 'append' );
  }

}

/**
 * Simple Button control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::BUTTON,
 *         'id'             => 'id',
 *         'name'           => 'name',
 *         'value'          => 'Button Text',
 *         'attrs'          => '',
 *         'data'           => '',
 *         'class'          => '',
 *         'style'          => '',
 *         'title'          => 'This title is Twitter Bootstrap Tooltips',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlButton
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKUIControlButton extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlButton class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlButton
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::BUTTON;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    echo $this->contentWithKey( 'prepend' );

    $input        = new WPDKHTMLTagInput( '', $this->name, $this->id );
    $input->type  = WPDKHTMLTagInputType::BUTTON;
    $input->class = $this->class;
    $input->data  = isset( $this->item['data'] ) ? $this->item['data'] : '';
    $input->value = isset( $this->item['value'] ) ? $this->item['value'] : '';
    $input->setPropertiesByArray( isset( $this->item['attrs'] ) ? $this->item['attrs'] : '' );

    $input->display();

    echo $this->contentWithKey( 'append' );
  }

}

/**
 * Checkbox control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::CHECKBOX,
 *         'label'          => 'Right label',
 *         'id'             => 'id',
 *         'name'           => 'name',
 *         'beforelabel'    => '',
 *         'afterlabel'     => ':',
 *         'value'          => 'Checkbox value',
 *         'checked'        => 'Checkbox value',
 *         'attrs'          => array(),
 *         'data'           => array(),
 *         'class'          => array(),
 *         'style'          => '',
 *         'title'          => 'This title is Twitter Bootstrap Tooltips',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlCheckbox
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKUIControlCheckbox extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlCheckbox class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlCheckbox
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::CHECKBOX;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    echo $this->contentWithKey( 'prepend' );

    /* Create the label. */
    $label = $this->label();

    $input        = new WPDKHTMLTagInput( '', $this->name, $this->id );
    $input->type  = WPDKHTMLTagInputType::CHECKBOX;
    $input->class = 'wpdk-form-checkbox ' . $this->class;
    $input->data  = isset( $this->item['data'] ) ? $this->item['data'] : '';
    $input->value = isset( $this->item['value'] ) ? $this->item['value'] : '';
    $input->title = isset( $this->item['title'] ) ? $this->item['title'] : '';
    $input->setPropertiesByArray( isset( $this->item['attrs'] ) ? $this->item['attrs'] : '' );

    if ( isset( $this->item['checked'] ) ) {
      if ( $input->value === $this->item['checked'] ) {
        $input->checked = 'checked';
      }
    }

    $input->display();

    /* Display right label. */
    echo is_null( $label ) ? '' : $label->html();

    echo $this->contentWithKey( 'append' );

    echo ' ' . $this->guide();
  }

}

/**
 * Choose control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::CHOOSE,
 *         'label'          => 'Left label',
 *         'id'             => 'id',
 *         'name'           => 'name',
 *         'beforelabel'    => '',
 *         'afterlabel'     => ':',
 *         'value'          => 'Button Text',
 *         'attrs'          => array(),
 *         'data'           => array(),
 *         'class'          => array(),
 *         'style'          => '',
 *         'title'          => 'This title is Twitter Bootstrap Tooltips',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlChoose
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKUIControlChoose extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlChoose class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlChoose
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::CHOOSE;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    echo $this->contentWithKey( 'prepend' );

    $input_hidden        = new WPDKHTMLTagInput( '', $this->name, $this->id );
    $input_hidden->type  = WPDKHTMLTagInputType::HIDDEN;
    $input_hidden->data  = isset( $this->item['data'] ) ? $this->item['data'] : '';
    $input_hidden->value = isset( $this->item['value'] ) ? $this->item['value'] : '';
    $input_hidden->setPropertiesByArray( isset( $this->item['attrs'] ) ? $this->item['attrs'] : '' );

    $input_button        = new WPDKHTMLTagInput( '', '', 'wpdk-form-choose-button_' . $this->id );
    $input_button->type  = WPDKHTMLTagInputType::BUTTON;
    $input_button->class = 'wpdk-form-choose-button';
    $input_button->value = '...';

    /* Create the span container. */
    $span_inner        = new WPDKHTMLTagSpan();
    $span_inner->title = isset( $this->item['title'] ) ? $this->item['title'] : '';
    $hide_class        = isset( $this->item['label'] ) ? $this->item['label'] : '';
    $span_inner->class = 'wpdk-form-choose-label ' . $this->class . ' ' . $hide_class;

    $content = $input_hidden->html() . $span_inner->html() . $input_button->html();

    /* Create the span container. */
    $span        = new WPDKHTMLTagSpan( $content );
    $span->class = 'wpdk-form-choose wpdk-control-choose';

    $span->display();

    echo $this->contentWithKey( 'append' );
  }

}

/**
 * Custom control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::CUSTOM,
 *         'content'        => 'Your HTML markup',
 *     );
 *
 * @class              WPDKUIControlCustom
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-01-29
 * @version            0.1.0
 * @since              1.0.0.b4
 *
 */
class WPDKUIControlCustom extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlCustom class
   *
   * @brief Construct
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlCustom
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::CUSTOM;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    $content = '';
    if ( isset( $this->item['content'] ) ) {
      if ( is_callable( $this->item['content'] ) ) {
        if ( isset( $this->item['param'] ) ) {
          $content = call_user_func( $this->item['content'], $this->item['param'] );
        }
        else {
          $content = call_user_func( $this->item['content'] );
        }
      }
      elseif ( is_string( $this->item['content'] ) ) {
        $content = $this->item['content'];
      }
    }
    echo $content;
  }
}

/**
 * Date control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::DATE,
 *         'label'          => 'Left label',
 *         'id'             => 'id',
 *         'name'           => 'name',
 *         'beforelabel'    => '',
 *         'afterlabel'     => ':',
 *         'value'          => 'Button Text',
 *         'attrs'          => array(),
 *         'data'           => array(),
 *         'class'          => array(),
 *         'style'          => '',
 *         'title'          => 'This title is Twitter Bootstrap Tooltips',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlDate
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKUIControlDate extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlDate class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlDate
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::DATE;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    $this->inputType( WPDKHTMLTagInputType::TEXT, 'wpdk-form-date wpdk-form-has-button-clear-left ' );
  }

}

/**
 * Date time control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::DATETIME,
 *         'label'          => 'Left label',
 *         'id'             => 'id',
 *         'name'           => 'name',
 *         'beforelabel'    => '',
 *         'afterlabel'     => ':',
 *         'value'          => 'Button Text',
 *         'attrs'          => array(),
 *         'data'           => array(),
 *         'class'          => array(),
 *         'style'          => '',
 *         'title'          => 'This title is Twitter Bootstrap Tooltips',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlDateTime
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKUIControlDateTime extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlDateTime class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlDateTime
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::DATETIME;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    $this->inputType( WPDKHTMLTagInputType::TEXT, 'wpdk-form-datetime wpdk-form-has-button-clear-left ' );
  }

}

/**
 * Email control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::EMAIL,
 *         'label'          => 'Left label',
 *         'id'             => 'id',
 *         'name'           => 'name',
 *         'beforelabel'    => '',
 *         'afterlabel'     => ':',
 *         'value'          => 'Button Text',
 *         'attrs'          => array(),
 *         'data'           => array(),
 *         'class'          => array(),
 *         'style'          => '',
 *         'title'          => 'This title is Twitter Bootstrap Tooltips',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlEmail
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKUIControlEmail extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlEmail class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlEmail
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::EMAIL;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    $this->inputType( WPDKHTMLTagInputType::TEXT, 'wpdk-form-email' );
  }

}

/**
 * File control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::FILE,
 *         'label'          => 'Left label',
 *         'id'             => 'id',
 *         'name'           => 'name',
 *         'beforelabel'    => '',
 *         'afterlabel'     => ':',
 *         'value'          => 'Button Text',
 *         'attrs'          => array(),
 *         'data'           => array(),
 *         'class'          => array(),
 *         'style'          => '',
 *         'title'          => 'This title is Twitter Bootstrap Tooltips',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlFile
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKUIControlFile extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlFile class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlFile
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::FILE;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    $this->inputType( WPDKHTMLTagInputType::FILE, 'wpdk-form-file' );
  }

}

/**
 * Input type hidden control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::HIDDEN,
 *         'id'             => 'id',
 *         'name'           => 'name',
 *         'value'          => 'Button Text',
 *
 *     );
 *
 * @class              WPDKUIControlHidden
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKUIControlHidden extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlHidden class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlHidden
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::HIDDEN;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    $this->inputType( WPDKHTMLTagInputType::HIDDEN );
  }

}

/**
 * Label control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::FILE,
 *         'id'             => 'id',
 *         'for'            => '',
 *         'value'          => 'Label Text',
 *         'attrs'          => array(),
 *         'data'           => array(),
 *         'class'          => array(),
 *         'style'          => '',
 *         'title'          => 'This title is Twitter Bootstrap Tooltips',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlLabel
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKUIControlLabel extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlLabel class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlLabel
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::LABEL;
    parent::__construct( $item );
  }

  /**
   * Dawing alert control
   *
   * @brief Display
   */
  public function draw() {
    echo $this->contentWithKey( 'prepend' );

    $value = isset( $this->item['value'] ) ? $this->item['value'] : '';

    $label        = new WPDKHTMLTagLabel( $value, $this->name, $this->id );
    $label->class = 'wpdk-form-label-inline ' . $this->class;
    $label->data  = isset( $this->item['data'] ) ? $this->item['data'] : '';
    $label->style = isset( $this->item['style'] ) ? $this->item['style'] : '';
    $label->setPropertiesByArray( isset( $this->item['attrs'] ) ? $this->item['attrs'] : '' );

    $label->display();

    echo $this->contentWithKey( 'append' );
  }

}

/**
 * Number control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::NUMBER,
 *         'label'          => 'Left label' | array(),
 *         'id'             => 'id',
 *         'value'          => '',
 *         'attrs'          => array(),
 *         'data'           => array(),
 *         'class'          => array(),
 *         'style'          => '',
 *         'title'          => 'This title is Twitter Bootstrap Tooltips',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlNumber
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 * @todo               Add filter for accept only number
 * @todo               Add min and max value
 * @todo               Add spinner and step
 *
 */
class WPDKUIControlNumber extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlNumber class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlNumber
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::NUMBER;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    $this->inputType( WPDKHTMLTagInputType::NUMBER, 'wpdk-form-number' );
  }

}

/**
 * Password control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::PASSWORD,
 *         'label'          => 'Left label',
 *         'id'             => 'id',
 *         'value'          => '',
 *         'attrs'          => array(),
 *         'data'           => array(),
 *         'class'          => array(),
 *         'style'          => '',
 *         'title'          => 'This title is Twitter Bootstrap Tooltips',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlPassword
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 * @todo               Add a jQuery trigger with a value to how to stronger the password
 *
 */
class WPDKUIControlPassword extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlPassword class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlPassword
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::PASSWORD;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    $this->inputType( WPDKHTMLTagInputType::PASSWORD, 'wpdk-form-password' );
  }

}

// TODO Paragraph

/**
 * Phone control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::PHONE,
 *         'label'          => 'Left label',
 *         'id'             => 'id',
 *         'value'          => '',
 *         'attrs'          => array(),
 *         'data'           => array(),
 *         'class'          => array(),
 *         'style'          => '',
 *         'title'          => 'This title is Twitter Bootstrap Tooltips',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlPassword
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 * @todo               Add a jQuery trigger with a value to how to stronger the password
 *
 */
class WPDKUIControlPhone extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlPhone class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlPhone
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::PHONE;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    $this->inputType( WPDKHTMLTagInputType::TEL, 'wpdk-form-phone' );
  }

}

/**
 * Radio control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::RADIO,
 *         'label'          => 'Left label',
 *         'id'             => 'id',
 *         'name'           => 'name',
 *         'beforelabel'    => '',
 *         'afterlabel'     => ':',
 *         'value'          => 'Radio button value',
 *         'checked'        => 'Radio button value',
 *         'attrs'          => array(),
 *         'data'           => array(),
 *         'class'          => array(),
 *         'style'          => '',
 *         'title'          => 'This title is Twitter Bootstrap Tooltips',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlRadio
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKUIControlRadio extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlRadio class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlRadio
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::RADIO;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    $this->inputType( WPDKHTMLTagInputType::RADIO, 'wpdk-form-radio' );
  }

}

/**
 * Section (foo) control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::SECTION,
 *         'label'          => 'Left label',
 *         'id'             => 'id',
 *     );
 *
 * @class              WPDKUIControlSection
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-01-31
 * @version            0.8.1
 *
 */
class WPDKUIControlSection extends WPDKUIControl {
  /**
   * Create an instance of WPDKUIControlSection class
   *
   * @brief Construct
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlSection
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::SELECT;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    /* Nothing to display. */
  }
}

/**
 * Select control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::SELECT,
 *         'label'          => 'Left label',
 *         'id'             => 'id',
 *         'value'          => '',
 *         'attrs'          => array(),
 *         'data'           => array(),
 *         'class'          => array(),
 *         'style'          => '',
 *         'title'          => 'This title is Twitter Bootstrap Tooltips',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlSelect
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 * @note If you want set the multiple attribute, use WPDKUIControlSelectList UI control instead
 *
 */
class WPDKUIControlSelect extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlSelect class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlSelect
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::SELECT;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    echo $this->contentWithKey( 'prepend' );

    /* Create the label. */
    $label = $this->label();

    /* Display right label. */
    echo is_null( $label ) ? '' : $label->html();

    $input           = new WPDKHTMLTagSelect( $this->item['options'], $this->name, $this->id );
    $input->class    = 'wpdk-form-select ' . $this->class;
    $input->data     = isset( $this->item['data'] ) ? $this->item['data'] : array();
    $input->style    = isset( $this->item['style'] ) ? $this->item['style'] : null;
    $input->multiple = isset( $this->item['multiple'] ) ? $this->item['multiple'] : null;
    $input->size     = isset( $this->item['size'] ) ? $this->item['size'] : null;
    $input->disabled = isset( $this->item['disabled'] ) ? $this->item['disabled'] ? 'disabled' : null : null;
    $input->value    = isset( $this->item['value'] ) ? $this->item['value'] : array();
    $input->setPropertiesByArray( isset( $this->item['attrs'] ) ? $this->item['attrs'] : '' );

    $input->display();

    echo $this->contentWithKey( 'append' );
  }

}

/**
 * Select control in multiple mode.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::SELECT_LIST,
 *         'label'          => 'Left label',
 *         'id'             => 'id',
 *         'value'          => '',
 *         'attrs'          => array(),
 *         'data'           => array(),
 *         'class'          => array(),
 *         'style'          => '',
 *         'title'          => 'This title is Twitter Bootstrap Tooltips',
 *         'size'           => '5',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlSelectList
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 */
class WPDKUIControlSelectList extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlSelectList class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlSelectList
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::SELECT_LIST;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    echo $this->contentWithKey( 'prepend' );

    /* Create the label. */
    $label = $this->label();

    /* Display right label. */
    echo is_null( $label ) ? '' : $label->html();

    $input           = new WPDKHTMLTagSelect( $this->item['options'], $this->name, $this->id );
    $input->class    = 'wpdk-form-select wpdk-form-select-size ' . $this->class;
    $input->data     = isset( $this->item['data'] ) ? $this->item['data'] : array();
    $input->style    = isset( $this->item['style'] ) ? $this->item['style'] : null;
    $input->multiple = 'multiple';
    $input->size     = isset( $this->item['size'] ) ? $this->item['size'] : 5;
    $input->value    = isset( $this->item['value'] ) ? $this->item['value'] : array();
    $input->setPropertiesByArray( isset( $this->item['attrs'] ) ? $this->item['attrs'] : '' );

    $input->display();

    echo $this->contentWithKey( 'append' );
  }

}

/**
 * Input type submit control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::SUBMIT,
 *         'id'             => 'id',
 *         'name'           => 'name',
 *         'value'          => 'Button Text',
 *         'attrs'          => '',
 *         'data'           => '',
 *         'class'          => '',
 *         'style'          => '',
 *         'title'          => 'This title is Twitter Bootstrap Tooltips',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlSubmit
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKUIControlSubmit extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlSubmit class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlSubmit
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::SUBMIT;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    echo $this->contentWithKey( 'prepend' );

    $input        = new WPDKHTMLTagInput( '', $this->name, $this->id );
    $input->type  = WPDKHTMLTagInputType::SUBMIT;
    $input->class = $this->class;
    $input->data  = isset( $this->item['data'] ) ? $this->item['data'] : '';
    $input->value = isset( $this->item['value'] ) ? $this->item['value'] : '';
    $input->setPropertiesByArray( isset( $this->item['attrs'] ) ? $this->item['attrs'] : '' );

    $input->display();

    echo $this->contentWithKey( 'append' );
  }
}

/**
 * Swipe control.
 *
 *     $item = array(
 *         'type'            => WPDKUIControlType::SWIPE,
 *         'label'           => 'label',
 *         'label_placement' => 'left|right',
 *         'id'              => 'id',
 *         'name'            => 'name',
 *         'value'           => 'on',
 *         'attrs'           => '',
 *         'data'            => '',
 *         'class'           => '',
 *         'style'           => '',
 *         'title'           => 'This title is Twitter Bootstrap Tooltips',
 *         'prepend'         => '',
 *         'append'          => '',
 *     );
 *
 * @class              WPDKUIControlSwipe
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-01-30
 * @version            0.8.2
 *
 */
class WPDKUIControlSwipe extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlSwipe class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlSwipe
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::SWIPE;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    echo $this->contentWithKey( 'prepend' );

    /* Create the label. */
    $label = $this->label();

    /* Display left label. */
    if ( !isset( $this->item['label_placement'] ) || 'left' == $this->item['label_placement'] ) {
      echo is_null( $label ) ? '' : $label->html();
    }

    $input_hidden        = new WPDKHTMLTagInput( '', $this->name, 'wpdk-swipe-' . $this->id );
    $input_hidden->type  = WPDKHTMLTagInputType::HIDDEN;
    $input_hidden->value = isset( $this->item['value'] ) ? $this->item['value'] : '';

    $status = wpdk_is_bool( $this->item['value'] ) ? 'wpdk-form-swipe-on' : '';

    $swipe        = new WPDKHTMLTagSpan( '<span></span>' . $input_hidden->html() );
    $swipe->class = 'wpdk-form-swipe wpdk-tooltip ' . $status;
    $swipe->id    = $this->id;
    $swipe->data  = isset( $this->item['data'] ) ? $this->item['data'] : array();
    if ( isset( $this->item['userdata'] ) ) {
      $swipe->data['userdata'] = esc_attr( $this->item['userdata'] );
    }
    $swipe->title = isset( $this->item['title'] ) ? $this->item['title'] : '';
    $swipe->setPropertiesByArray( isset( $this->item['attrs'] ) ? $this->item['attrs'] : '' );

    $swipe->display();

    /* Display right label. */
    if ( isset( $this->item['label_placement'] ) && 'right' == $this->item['label_placement'] ) {
      echo is_null( $label ) ? '' : $label->html();
    }

    echo $this->contentWithKey( 'append' );
  }
}

/**
 * Input type text control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::TEXT,
 *         'label'          => 'Left label' | array(),
 *         'id'             => 'id',
 *         'name'           => 'name',
 *         'value'          => 'Inner text',
 *         'placeholder'    => 'Placeholder',
 *         'attrs'          => '',
 *         'data'           => '',
 *         'class'          => '',
 *         'style'          => '',
 *         'title'          => 'This title is Twitter Bootstrap Tooltips',
 *         'prepend'        => '',
 *         'append'         => '',
 *         'guide'          => 'slug-of-guide'
 *     );
 *
 * ### guide
 * The new key `guide` is used to open a Twitter Bootstrap Modal with an iframe on the Developer Center. You can display
 * any Developer Center public guide, just by slug name page. The key `guide` can be an array too:
 *
 *     'guide'   => array(
 *       'Say hello to guide',
 *       'how-to-update-your-bootstrap-php'
 *      )
 *
 * Or
 *     'guide'  => array(
 *      'uri'     => 'how-to-update-your-bootstrap-php',
 *      'tooltip' => 'This is a custom tooltip for this guide',
 *      'title'   => 'This is the custom modal title',
 *      'button'  => 'Need help?'
 *      )
 *
 * @class              WPDKUIControlText
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKUIControlText extends WPDKUIControl {
  /**
   * Create an instance of WPDKUIControlText class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlText
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::TEXT;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    $this->inputType( WPDKHTMLTagInputType::TEXT );
  }
}

/**
 * Input type text control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::TEXTAREA,
 *         'label'          => 'Left label',
 *         'id'             => 'id',
 *         'name'           => 'name',
 *         'value'          => 'Inner text',
 *         'attrs'          => '',
 *         'data'           => '',
 *         'class'          => '',
 *         'style'          => '',
 *         'cols'           => '10',
 *         'rows'           => '4',
 *         'title'          => 'This title is Twitter Bootstrap Tooltips',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlTextarea
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKUIControlTextarea extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlTextarea class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlTextarea
   */
  public function __construct( $item ) {
    $item['type'] = WPDKUIControlType::TEXTAREA;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw() {
    echo $this->contentWithKey( 'prepend' );

    /* Create the label. @todo usually you li ke set this label n the top. */
    $label = $this->label();

    /* Display right label. */
    echo is_null( $label ) ? '' : $label->html();

    $content = isset( $this->item['value'] ) ? $this->item['value'] : '';

    $input              = new WPDKHTMLTagTextarea( $content, $this->name, $this->id );
    $input->class       = $this->class . ' wpdk-form-textarea';
    $input->data        = isset( $this->item['data'] ) ? $this->item['data'] : '';
    $input->value       = $content;
    $input->cols        = isset( $this->item['cols'] ) ? $this->item['cols'] : '10';
    $input->rows        = isset( $this->item['rows'] ) ? $this->item['rows'] : '4';
    $input->disabled    = isset( $this->item['disabled'] ) ? $this->item['disabled'] ? 'disabled' : null : null;
    $input->placeholder = isset( $this->item['placeholder'] ) ? $this->item['placeholder'] : null;
    $input->setPropertiesByArray( isset( $this->item['attrs'] ) ? $this->item['attrs'] : '' );

    $input->display();

    echo $this->contentWithKey( 'append' );
  }
}

/**
 * This class manage and display a Controls Layout Array.
 *
 * ## Getting started
 *
 * In order to display a controls layout you need to define a simple Controls Layout Array (CLA) and to instance the
 * WPDKUIControlsLayout class:
 *
 *     $cla = array(
 *        'Fieldset Legend Group' => array(
 *             'Subtitle',                                 // Optional subtitle
 *                 array( array(...), array(...) ),        // Row of items
 *                 array(...),                             // New row
 *                 array(...)                              // Items
 *         );
 *     );
 *
 *     $layout = new WPDKUIControlsLayout( $cla );
 *     $layout->display();
 *
 *
 * @class              WPDKUIControlType
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKUIControlsLayout {

  /**
   * Controls Layout array
   *
   * @var array $_cla;
   */
  private $_cla;

  /**
   * Create an instance of WPDKUIControlsLayout class
   *
   * @brief Construct
   *
   * @param array $cla Controls Layout array
   */
  public function __construct( $cla ) {
    $this->_cla = $cla;
  }

  /**
   * Return the HTML markup for a single item array
   *
   * @brief Single item
   * @since 1.0.0.b4
   *
   * @param array $item Single item to process
   *
   * @return string
   */
  public static function item( $item ) {
    ob_start();
    self::_processItem( $item );
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }

  /**
   * Processing a single item in CLA format
   *
   * @brief Processing a single item
   *
   * @param array $item Control description in CLA format
   */
  private function _processItem( $item ) {
    $class_name = isset( $item['type'] ) ? $item['type'] : '';
    if ( !empty( $class_name ) ) {
      $control = new $class_name( $item );
      $control->display();
    }
  }

  /**
   * Display the controls layout
   *
   * @brief Display
   */
  public function display() {
    echo $this->html();
  }

  /**
   * Return the HTML markup pf controls layout array
   *
   * @brief Get the HTML
   *
   * @return string
   */
  public function html() {
    /* Buffering... */
    ob_start();

    foreach ( $this->_cla as $key => $value ) : ?>

    <fieldset class="wpdk-form-fieldset">
      <legend><?php echo $key ?></legend>
      <div class="wpdk-fieldset-container">
        <?php $this->_processRows( $value ) ?>
      </div>
    </fieldset>

    <?php endforeach;

    $content = ob_get_contents();
    ob_end_clean();

    return $content;
  }

  /**
   * Processing a single row within one or more items (controls array)
   *
   * @brief Processing row
   *
   * @param array $rows
   */
  private function _processRows( $rows ) {

    foreach ( $rows as $item ) {
      if ( is_string( $item ) && !empty( $item ) ) { ?>
      <div class="wpdk-form-description"><?php echo $item ?></div><?php
      }
      elseif ( isset( $item['type'] ) ) {
        $this->_processItem( $item );
      }
      elseif ( isset( $item['container'] ) ) {
        echo apply_filters( 'wpdk_form_html_group_before', $this->container( $item ), $item );
        $this->_processRows( $item['container'] );
        echo apply_filters( 'wpdk_form_html_group_after', '</div>', $item );
      }
      elseif ( isset( $item['group_inner'] ) ) {
        echo apply_filters( 'wpdk_form_html_group_before', self::wrapInner( $item ), $item );
        $this->_processRows( $item['group_inner'] );
        echo apply_filters( 'wpdk_form_html_group_after', '</span>', $item );
      }
      elseif ( !empty( $item ) ) {
        echo apply_filters( 'wpdk_form_html_row_before', '<div class="wpdk-form-row">', $item );
        $this->_processRows( $item );
        echo apply_filters( 'wpdk_form_html_row_after', '</div>', $item );
      }
    }
  }

  private function container( $item ) {
    $class = isset( $item['class'] ) ? $item['class'] : '';
    return sprintf( '<div class="%s">', $class );
  }

}


/// @endcond

/**
 * UI Helper for general purpose
 *
 * ## Overview
 * This class allow to make simple display button and common UI.
 *
 * @class              WPDKUI
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKUI {

  // -----------------------------------------------------------------------------------------------------------------
  // Buttons
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return the HTML markup for standard [Update] and [Reset to default] button
   *
   * @brief Button Update and Reset
   */
  public static function buttonsUpdateReset() {
    $button_update = self::button();
    $button_reset  = self::button( __( 'Reset to default', WPDK_TEXTDOMAIN ), array(
                                                                                   'name'    => 'resetToDefault',
                                                                                   'classes' => 'button-secondary'
                                                                              ) );
    return sprintf( '<p>%s%s</p>', $button_reset, $button_update );
  }

  /**
   * Utility that return the HTML markup for a submit button
   *
   * @brief Button submit
   *
   * @param array $args Optional. Item description
   *
   * @return string
   */
  public function submit( $args = array() ) {
    $default_args = array(
      'name'  => 'button-submit',
      'class' => 'button button-primary alignright',
      'value' => __( 'Submit', WPDK_TEXTDOMAIN )
    );

    $item = wp_parse_args( $args, $default_args );

    $item['type']  = WPDKUIControlType::SUBMIT;

    $submit = new WPDKUIControlSubmit( $item );

    return $submit->html();
  }


  /**
   * Return HTML markup for a input type
   *
   * @brief Simple button
   *
   * @param string $label Button label
   * @param string $args  Array key value
   *     'type'                  => 'submit',
   *     'name'                  => 'button-update',
   *     'classes'               => ' btn btn-primary button-primary',
   *     'additional_classes'    => '',
   *     'data'                  => ''
   *
   * @return string HTML input type submit
   */
  public static function button( $label = '', $args = array() ) {

    $default_args = array(
      'type'               => 'submit',
      'name'               => 'button-update',
      'classes'            => ' btn btn-primary button button-primary alignright',
      'additional_classes' => '',
      'data'               => array(),
    );

    $args = wp_parse_args( $args, $default_args );

    /* Label */
    if ( empty( $label ) ) {
      $label = __( 'Update', WPDK_TEXTDOMAIN );
    }

    /* Name attribute */
    if ( empty( $args['name'] ) ) {
      $name = '';
    }
    else {
      $name = sprintf( 'name="%s"', $args['name'] );
    }

    /* Create data attributes. */
    $data = '';
    if( !empty( $args['data'] ) ) {
      $stack = array();
      foreach( $args['data'] as $key => $value ) {
        $stack[] = sprintf( 'data-%s="%s"', $key, htmlspecialchars( stripslashes( $value ) ) );
      }
      $data = implode( ',', $stack );
    }

    /* Additional classes */
    $classes = '';
    if ( !empty( $args['additional_classes'] ) ) {
      if ( !is_array( $args['additional_classes'] ) ) {
        $new = explode( ' ', $args['additional_classes'] );
        if( empty( $new ) ) {
          $new = array( $args['additional_classes'] );
        }
      }

      /* Split default class to remove duplicate. */
      $default = explode( ' ', $args['classes'] );
      $classes = implode( ' ',  array_merge( $default, $new ) );
    }

    $classes .= $args['classes'];

    ob_start(); ?>

  <input type="<?php echo $args['type'] ?>" <?php echo $name ?>
         <?php echo $data ?>
         class="<?php echo $classes ?>"
         value="<?php echo $label ?>"/>

  <?php
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Badges
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * @deprecated Since 1.0.0.b3 use badge() instead
   */
  public static function badged( $count = 0, $classes = '', $tooltip = '', $placement = '' ) {
    _deprecated_function( __METHOD__, '1.0.0.b3', 'badge()' );
    self::badge( $count, $classes, $tooltip, $placement );
  }

  /**
   * Return the HTML markup for a simple badge.
   *
   * @brief Badge
   *
   * @param int    $count     Optional. Number to display in the badge
   * @param string $classes   Optional. Additional class for this badge
   * @param string $tooltip   Optional. Tooltip to display when mouse over
   * @param string $placement Optional. Tooltip placement, default `bottom`
   *
   * @return string
   */
  public static function badge( $count = 0, $classes = '', $tooltip = '', $placement = '' ) {
    $classes = !empty( $classes ) ? ' ' . $classes : '';

    if ( !empty( $tooltip ) ) {
      $classes .= ' wpdk-tooltip';
      $placement = sprintf( 'data-placement="%s"', empty( $placement ) ? 'bottom' : $placement );
    }

    if ( !empty( $count ) ) {
      $result = sprintf( '<span title="%s" %s class="wpdk-badge update-plugins count-%s%s"><span class="plugin-count">%s</span></span>', $tooltip, $placement, $count, $classes, number_format_i18n( $count ) );
    }
    else {
      /* Restituisco comunque un placeholder comodo per poter inserire onfly via javascript un badge. */
      $result = sprintf( '<span class="%s"></span>', $classes );
    }
    return $result;
  }


  // -----------------------------------------------------------------------------------------------------------------
  // View
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return a standard HTML markup view for WordPress backend, with wrap, icon, title and main container
   *
   * @brief
   *
   * @param string $id         Class to add to main content container
   * @param string $title      Head title
   * @param string $icon_class Icon class
   * @param string $content    HTML content
   *
   * @deprecated Use WPDKView and WPDKViewController
   *
   * @return string
   */
  public static function view( $id, $title, $icon_class, $content ) {
    $html = <<< HTML
<div class="wrap">
    <div class="{$icon_class}"></div>
    <h2>{$title}</h2>
    <div class="wpdk-border-container {$id}">
        {$content}
    </div>
</div>
HTML;
    return $html;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Form
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return the HTML markup for an input type hidden with a nonce value.
   *
   * @brief Input nonce
   * @since 1.0.0.b4
   *
   * @param string $id ID used for nonce code
   *
   * @return string
   */
  public static function inputNonce( $id ) {
    $nonce                     = md5( $id );
    $input_hidden_nonce        = new WPDKHTMLTagInput( '', $nonce, $nonce );
    $input_hidden_nonce->type  = WPDKHTMLTagInputType::HIDDEN;
    $input_hidden_nonce->value = wp_create_nonce( $id );
    return $input_hidden_nonce->html();
  }


  /// @cond private

  /*
   * [DRAFT]
   *
   * THE FOLLOWING CODE IS A DRAFT. FEEL FREE TO USE IT TO MAKE SOME EXPERIMENTS, BUT DO NOT USE IT IN ANY CASE IN
   * PRODUCTION ENVIRONMENT. ALL CLASSES AND RELATIVE METHODS BELOW CAN CHNAGE IN THE FUTURE RELEASES.
   *
   */

  // -----------------------------------------------------------------------------------------------------------------
  // Prototype
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return a new truncate well string. The result string is wrap into a special HTML markup.
   *
   * @brief Enhancer string truncate
   *
   * @note Prototype
   *
   * @param string $value String to truncate
   * @param string $size  Number of character
   *
   * @return string
   */
  public static function labelTruncate( $value, $size = 'small' ) {
    $html = <<< HTML
    <div class="wpdk-ui-truncate wpdk-ui-truncate-size_{$size}" title="{$value}">
        <span>{$value}</span>
    </div>
HTML;
    return $html;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Information
  // @todo This is the old Credits engine. These methods have to renamed and move into a custom class.
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return a format list from array
   *
   *
   * @param array $credits Array key pair for credits
   *
   *     $credits = array(
   *        __( 'Developers & UI Designers', WPXCLEANFIX_TEXTDOMAIN ) => array(
   *            array(
   *                'name'  => 'Giovambattista Fazioli (Design & Develop)',
   *                'mail'  => 'g.fazioli@wpxtre.me',
   *                'site'  => 'http://www.undolog.com',
   *            ),
   *        ),
   *
   *        __( 'Translations', WPXCLEANFIX_TEXTDOMAIN ) => array(
   *            array(
   *                'name'  => 'Baris Unver (Turkish)',
   *                'mail'  => 'baris.unver@beyn.orge',
   *                'site'  => 'http://beyn.org/',
   *            ),
   *            array(
   *                'name'  => 'Valentin B (French)',
   *                'mail'  => '',
   *                'site'  => 'http://geekeries.fr/',
   *            ),
   *            array(
   *                'name'  => 'rauchmelder (German)',
   *                'mail'  => 'team@fakten-fiktionen.de',
   *                'site'  => '#',
   *            ),
   *            array(
   *                'name'  => 'Győző Farkas alias FYGureout (Hungarian)',
   *                'mail'  => 'webmester@wordpress2you.com',
   *                'site'  => 'http://www.wordpress2you.com',
   *            ),
   *        ),
   *     );
   *
   * @return string
   *
   * @todo Rename (info)
   *
   */
  public static function credits( array $credits ) {
    $html = '';
    foreach ( $credits as $key => $value ) {
      $html .= sprintf( '<div class="wpdk-credits wpdk-credits-%s clearfix">', sanitize_title( $key ) );
      $html .= sprintf( '<h3>%s</h3>', $key );
      $html .= '<ul class="clearfix">';
      foreach ( $value as $info ) {
        $html .= sprintf( '<li class="wpdk-tooltip clearfix" title="%s" data-placement="top"><img src="http://www.gravatar.com/avatar/%s?s=32&d=wavatar" /><a target="_blank" href="%s">%s</a></li>', $info['site'], md5( $info['mail'] ), $info['site'], $info['name'] );
      }
      $html .= '</ul></div>';
    }
    return $html;
  }

  /// Utility for credits title

  /* @todo Rename */
  public static function creditsTitle( $class ) {
    if ( is_object( $class ) && is_subclass_of( $class, 'WPDKWordPressPlugin' ) ) {
      return sprintf( '%s ver.%s', $class->name, $class->version );
    }
    return false;
  }

  /// Utility for credits copyright

  /* @todo Rename */
  public static function creditsCopy( $copy ) {
    return sprintf( '<p class="wpdk-credits-copy alignright">%s</p>', $copy );
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Deprecated
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Helper per la creazione di un messaggio di risposta updated
   *
   * @deprecated
   *
   * @param string       $message
   * @param bool         $highlight
   * @param string       $class
   * @param bool         $echo
   *
   * @return string
   */
  public static function message( $message, $highlight = false, $class = '', $echo = true, $dismiss = false ) {
    _deprecated_function( __METHOD__, '0.5', 'WPDKTwitterBootstrapAlert' );

    $highlight = $highlight ? 'highlight' : '';

    $dismiss = $dismiss ? '<a class="close" data-dismiss="alert" href="#">&times;</a>' : '';
    /* @todo Questa riga produce lo scatenarsi di un javascript WP che posiziona il div sempre nello stesso posto */
    $updated = empty( $dismiss ) ? 'updated' : '';
    //$updated = '';

    $html = <<< HTML
<div class="{$class} fade in {$highlight} {$updated}">
    {$dismiss}
    <p>{$message}</p>
</div>
HTML;
    if ( $echo ) {
      echo $html;
    }
    else {
      return $html;
    }
  }

  /**
   * Helper per la creazione di un messaggio di errore
   *
   * @deprecated Use WPDKResult and its classes
   *
   * @param string  $message
   * @param bool    $highlight
   * @param string  $class
   * @param bool    $echo
   *
   * @return string
   */
  public static function error( $message, $highlight = false, $class = '', $echo = true ) {
    _deprecated_function( __METHOD__, '0.5', 'WPDKTwitterBootstrapAlert' );

    $highlight = $highlight ? 'highlight' : '';

    $html = <<< HTML
<div class="error fade {$class} {$highlight}">
    <p>{$message}</p>
</div>
HTML;
    if ( $echo ) {
      echo $html;
    }
    else {
      return $html;
    }
  }

  /// @endcond

}
