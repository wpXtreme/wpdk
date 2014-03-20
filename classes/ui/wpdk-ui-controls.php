<?php

/**
 * This class has the list of the constrols allowed
 *
 * @class              WPDKUIControlType
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-11-11
 * @version            0.8.3
 *
 */
class WPDKUIControlType {

  const ALERT       = 'WPDKUIControlAlert';
  const BUTTON      = 'WPDKUIControlButton';
  const CHECKBOX    = 'WPDKUIControlCheckbox';
  const CHECKBOXES  = 'WPDKUIControlCheckboxes';
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
  const SWITCHBOX   = 'WPDKUIControlSwitch';
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
 * @date               2013-10-29
 * @version            1.0.0
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
   * Useful constant to append a [Remove] button on select list control
   *
   * @since 1.4.8
   */
  const APPEND_SELECT_LIST_REMOVE = 'append_select_list_remove';

  /**
   * Useful constant to append a [Add] button on select list control
   *
   * @since 1.4.8
   */
  const APPEND_SELECT_LIST_ADD = 'append_select_list_add';

  /**
   * A string with list of attributes
   *
   * @brief Attributes
   *
   * @var array|string $attrs
   */
  protected $attrs = array();

  /**
   * A string or an array with CSS classes
   *
   * @brief CSS classes
   *
   * @var string|array $class
   */
  protected $class = array();
  /**
   * A string or an array with list of data attributes
   *
   * @brief Data attributes
   *
   * @var string|array $data
   */
  protected $data = array();
  /**
   * A sanitize attribute id
   *
   * @brief Attribute ID
   *
   * @var string $id
   */
  protected $id = '';
  /**
   * A key value pairs array with the WPDK ui control description
   *
   * @brief WPDK UI Control array descriptor
   *
   * @var array $item
   */
  protected $item = '';
  /**
   * The name attribute
   *
   * @brief Name attribute
   *
   * @var string $name
   */
  protected $name = '';
  /**
   * A string with inlibe CSS styles
   *
   * @brief CSS inline style
   *
   * @var string $style
   */
  protected $style = '';
  /**
   * Keep an array with input size attribute for specific type
   *
   * @brief Get size
   * @since 1.0.0.b4
   *
   * @var array $_sizeForType
   */
  private $_sizeForType = array();

  /**
   * Create an instance of WPDKUIControl class
   *
   * @brief Construct
   *
   * @param array $item_control Control array
   */
  public function __construct( $item_control )
  {
    $this->item = $item_control;

    // Sanitize the common array key

    $this->attrs = WPDKHTMLTag::sanitizeAttributes( isset( $this->item['attrs'] ) ? $this->item['attrs'] : array() );
    $this->data  = WPDKHTMLTag::sanitizeData( isset( $this->item['data'] ) ? $this->item['data'] : array() );
    $this->class = WPDKHTMLTag::sanitizeClasses( isset( $this->item['class'] ) ? $this->item['class'] : array() );

    if ( isset( $this->item['id'] ) ) {
      $this->id = sanitize_key( $this->item['id'] );
    }
    elseif ( isset( $this->item['name'] ) ) {
      $this->id = sanitize_key( $this->item['name'] );
    }

    $this->name = isset( $this->item['name'] ) ? $this->item['name'] : '';

    // Input size attribute for specific type
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
  protected function data()
  {
    $result = '';
    if ( isset( $this->item['data'] ) && !empty( $this->item['data'] ) ) {
      $result = WPDKHTMLTag::dataInline( $this->item['data'] );
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
  protected function classes()
  {
    $result = '';
    if ( isset( $this->item['class'] ) && !empty( $this->item['class'] ) ) {
      $result = WPDKHTMLTag::classInline( $this->item['class'] );
    }
    return $result;
  }

  /**
   * Display the control
   *
   * @brief Display
   */
  public function display()
  {
    echo $this->html();
  }

  /**
   * Drawing
   *
   * @brief Draw
   */
  public function draw()
  {
    die( __METHOD__ . ' must be override in your subclass' );
  }

  /**
   * Return the HTML markup for control
   *
   * @brief Get HTML
   *
   * @return string
   */
  public function html()
  {
    WPDKHTML::startCompress();

    echo $this->contentWithKey( 'before' );

    $this->draw();

    echo $this->contentWithKey( 'after' );

    return WPDKHTML::endCompress();
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
  protected function contentWithKey( $key )
  {

    $result = '';

    /*
     * Append predefined content
     *
     *     'append'  => WPDKUIControl::APPEND_SELECT_LIST_REMOVE
     * OR
     *     'append'  => array( WPDKUIControl::APPEND_SELECT_LIST_REMOVE, 'Remove' )
     * OR
     *     'append'  => array( WPDKUIControl::APPEND_SELECT_LIST_ADD, 'destination_select' )
     * OR
     *     'append'  => array( WPDKUIControl::APPEND_SELECT_LIST_ADD, 'destination_select', 'Add' )
     *
     */
    if ( 'append' == $key && !empty( $this->item['append'] ) ) {
      $append = array_merge( (array)$this->item['append'], array(0,0,0) );

      list( $code, $destination_select, $label ) = $append;

      switch ( $code ) {

        case WPDKUIControl::APPEND_SELECT_LIST_REMOVE:
          $label                = empty( $destination_select ) ? __( 'Remove', WPDK_TEXTDOMAIN ) : $destination_select;
          $this->item['append'] = '<input data-remove_from="' . $this->item['id'] .
            '" class="wpdk-form-button wpdk-form-button-remove button-secondary" style="vertical-align:top" type="button" value="' .
            $label . '" />';
          break;

        case WPDKUIControl::APPEND_SELECT_LIST_ADD:
          if ( !empty( $destination_select ) ) {
            $label                = empty( $label ) ? __( 'Add', WPDK_TEXTDOMAIN ) : $label;
            $this->item['append'] =
              '<input type="button" data-copy="' . $this->item['name'] . '" data-paste="' . $destination_select .
              '" class="wpdk-form-button wpdk-form-button-copy-paste button-secondary" value="' . $label . '" />';
          }
          break;
      }
    }

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
   * @param string|WPDKHTMLTagInputType $type  Optional. Type of input
   * @param string                       $class Optional. CSS additional class
   */
  protected function inputType( $type = WPDKHTMLTagInputType::TEXT, $class = '' )
  {

    echo $this->contentWithKey( 'prepend' );

    // Create the label
    $label = $this->label();

    // Display right label
    echo is_null( $label ) ? '' : $label->html();

    $input               = new WPDKHTMLTagInput( '', $this->name, $this->id );
    $input->type         = $type;
    $input->class        = WPDKHTMLTag::mergeClasses( $this->class, $class, 'wpdk-form-input wpdk-ui-control' );
    $input->style        = WPDKHTMLTag::styleInline( isset( $this->item['style'] ) ? $this->item['style'] : '' );
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

    // Add a clear field button only
    if ( in_array( $this->item['type'], array( WPDKUIControlType::DATE, WPDKUIControlType::DATETIME ) ) ) {
      $span_clear = new WPDKHTMLTagSpan();
      $span_clear->class[] = 'wpdk-form-clear-left';
      $span_clear->content = $input->html() . WPDKGlyphIcons::html( WPDKGlyphIcons::CANCEL_CIRCLED );
      $span_clear->display();
    } else {
      $input->display();
    }

    if ( isset( $this->item['locked'] ) && true == $this->item['locked'] ) {
      printf( '<span title="%s" class="wpdk-form-locked wpdk-has-tooltip"></span>', __( 'This field is locked for your security. However you can unlock just by click here.', WPDK_TEXTDOMAIN ) );
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
  protected function label()
  {

    if ( !isset( $this->item['label'] ) || empty( $this->item['label'] ) ) {
      return null;
    }

    $content = '';

    if ( is_string( $this->item['label'] ) ) {
      $content = trim( $this->item['label'] );
    }
    elseif ( is_array( $this->item['label'] ) ) {
      $content = trim( $this->item['label']['value'] );
    }

    $before_label = isset( $this->item['beforelabel'] ) ? $this->item['beforelabel'] : '';
    $after_label  = isset( $this->item['afterlabel'] ) ? $this->item['afterlabel'] : '';

    // Special behavior (before) for these controls
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

    // Create the lable
    $label      = new WPDKHTMLTagLabel( $before_label . $content . $after_label );
    $label->for = $this->id;
    $label->class[] = 'wpdk-has-tooltip';
    $label->class[] = 'wpdk-form-label';
    $label->class[] = 'wpdk-ui-control';

    if ( is_array( $this->item['label'] ) ) {
      $label->data  = isset( $this->item['label']['data'] ) ? $this->item['label']['data'] : '';
      $label->style = isset( $this->item['label']['style'] ) ? $this->item['label']['style'] : '';
      $label->setPropertiesByArray( isset( $this->item['label']['attrs'] ) ? $this->item['label']['attrs'] : '' );
    }

    // Special behavior (after) for these controls
    switch ( $this->item['type'] ) {
      case WPDKUIControlType::CHECKBOX:
        $label->class[] = 'wpdk-form-checkbox';
        $label->class[] = 'wpdk-ui-control';
        break;

      case WPDKUIControlType::SWITCHBOX:
        if ( !isset( $this->item['label_placement'] ) || 'left' == $this->item['label_placement'] ) {
          $label->class[] = 'wpdk-form-switch-left';
        }
        $label->data = $this->item['data'];
        break;

      case WPDKUIControlType::SWIPE:
        if ( isset( $this->item['label_placement'] ) && 'right' == $this->item['label_placement'] ) {
          $label->class = str_replace( 'wpdk-has-tooltip', 'wpdk-form-label-inline', $label->class );
        }
        break;

      case WPDKUIControlType::TEXTAREA:
      case WPDKUIControlType::SELECT_LIST:
        $label->class[] = 'wpdk-form-label-top';
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
        return sprintf( '<a href="%s" data-title="%s" class="wpdk-guide wpdk-has-tooltip" title="%s">%s</a>', $guide, $title, $tooltip, $button_title );
      }

      /* Array args guide: array( 'Title of modal', 'unique uri' ) */
      if ( is_array( $guide ) && is_numeric( key( $guide ) ) ) {
        $title = $guide[0];
        $uri   = $guide[1];
        return sprintf( '<a href="%s" data-title="%s" class="wpdk-guide wpdk-has-tooltip" title="%s">%s</a>', $uri, $title, $tooltip, $button_title );
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

        return sprintf( '<a href="%s" data-title="%s" class="wpdk-guide wpdk-has-tooltip %s" title="%s">%s</a>', $uri, $title, $classes, $tooltip, $button_title );
      }

      $result = '<span>[#internal error - wrong guide]</span>';
    }

    return $result;
  }

}

/**
 * Simple Alert control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::ALERT,
 *         'alert_type'     => WPDKUIAlertType::INFORMATION,
 *         'id'             => 'id',
 *         'value'          => 'Alert content',
 *         'title'          => 'Alert title',
 *         'class'          => '' | array(),
 *         'dismiss_button' => true,
 *         'prepend'        => '',
 *         'append'         => ''
 *     );
 *
 * @class              WPDKUIControlAlert
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-02-28
 * @version            1.0.0
 *
 */
class WPDKUIControlAlert extends WPDKUIControl {

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw()
  {
    $value      = isset( $this->item['value'] ) ? $this->item['value'] : '';
    $title      = isset( $this->item['title'] ) ? $this->item['title'] : '';
    $alert_type = isset( $this->item['alert_type'] ) ? $this->item['alert_type'] : WPDKUIAlertType::INFORMATION;

    $alert                = new WPDKUIAlert( $this->id, $value, $alert_type, $title );
    $alert->dismissButton = isset( $this->item['dismiss_button'] ) ? $this->item['dismiss_button'] : true;
    $alert->class         = isset( $this->item['classes'] ) ? $this->item['classes'] : isset( $this->item['class'] ) ? $this->item['class'] : '';

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
 *         'title'          => 'This title is a tooltip',
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
  public function __construct( $item )
  {
    $item['type'] = WPDKUIControlType::BUTTON;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw()
  {
    echo $this->contentWithKey( 'prepend' );

    // Since 1.4.21
    if ( isset( $this->item['content'] ) ) {
      $button          = new WPDKHTMLTagButton( $this->item['content'] );
      $button->class   = $this->class;
      $button->class[] = 'wpdk-form-button';
      $button->class[] = 'wpdk-ui-control';
      $button->name    = isset( $this->item['name'] ) ? $this->item['name'] : '';
      $button->id      = isset( $this->item['id'] ) ? $this->item['id'] : $button->name;
      $button->data    = isset( $this->item['data'] ) ? $this->item['data'] : '';
      $button->value   = isset( $this->item['value'] ) ? $this->item['value'] : '';
      $button->setPropertiesByArray( isset( $this->item['attrs'] ) ? $this->item['attrs'] : '' );
    }

    // Backward compatibility - deprecated - will be remove asap
    else {
      $button        = new WPDKHTMLTagInput( '', $this->name, $this->id );
      $button->type  = WPDKHTMLTagInputType::BUTTON;
      $button->class = $this->class;
      $button->data  = isset( $this->item['data'] ) ? $this->item['data'] : '';
      $button->value = isset( $this->item['value'] ) ? $this->item['value'] : '';
      $button->setPropertiesByArray( isset( $this->item['attrs'] ) ? $this->item['attrs'] : '' );
    }
    $button->display();

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
 *         'title'          => 'This title is a tooltip',
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

    $input          = new WPDKHTMLTagInput( '', $this->name, $this->id );
    $input->type    = WPDKHTMLTagInputType::CHECKBOX;
    $input->class   = $this->class;
    $input->class[] = 'wpdk-form-checkbox';
    $input->class[] = 'wpdk-ui-control';
    $input->data    = isset( $this->item['data'] ) ? $this->item['data'] : '';
    $input->value   = isset( $this->item['value'] ) ? $this->item['value'] : '';
    $input->title   = isset( $this->item['title'] ) ? $this->item['title'] : '';
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
 * Checkboxes control.
 *
 *     $item = array(
 *         'type'           => WPDKUIControlType::CHECKBOXES,
 *         'legend'         => 'field set (legend)',
 *         'beforelabel'    => '',
 *         'afterlabel'     => ':',
 *         'list'           => array( [item checkbox without typekey], ... ),
 *     );
 *
 * @class              WPDKUIControlCheckbox
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-11-11
 * @version            0.1.0
 *
 */
class WPDKUIControlCheckboxes extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlCheckbox class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlCheckboxes
   */
  public function __construct( $item )
  {
    $item['type'] = WPDKUIControlType::CHECKBOXES;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw()
  {
    echo $this->contentWithKey( 'prepend' );

    if ( isset( $this->item['list'] ) && !empty( $this->item['list'] ) && is_array( $this->item['list'] ) ) {
      $content = '';
      foreach ( $this->item['list'] as $checkbox ) {
        /*
         * @todo Introducing the indent by checcking $checkbox['list']
         */
        $checkbox['type'] = WPDKUIControlType::CHECKBOX;
        $cb = new WPDKUIControlCheckbox( $checkbox );
        $content .= $cb->html();
      }

      if ( isset( $this->item['label'] ) && !empty( $this->item['label'] ) ) {
        $field_set = new WPDKHTMLTagFieldset( $content, $this->item['label'] );
        $field_set->display();
      }
      else {
        echo $content;
      }

      echo $this->contentWithKey( 'append' );

      echo ' ' . $this->guide();
    }
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
 *         'title'          => 'This title is a tooltip',
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

    $input_button          = new WPDKHTMLTagInput( '', '', 'wpdk-form-choose-button_' . $this->id );
    $input_button->type    = WPDKHTMLTagInputType::BUTTON;
    $input_button->class[] = 'wpdk-form-choose-button';
    $input_button->value   = '...';

    /* Create the span container. */
    $span_inner          = new WPDKHTMLTagSpan();
    $span_inner->title   = isset( $this->item['title'] ) ? $this->item['title'] : '';
    $hide_class          = isset( $this->item['label'] ) ? $this->item['label'] : '';
    $span_inner->class   = $this->class;
    $span_inner->class[] = 'wpdk-form-choose-label';
    $span_inner->class[] = $hide_class;

    $content = $input_hidden->html() . $span_inner->html() . $input_button->html();

    /* Create the span container. */
    $span          = new WPDKHTMLTagSpan( $content );
    $span->class[] = 'wpdk-form-choose wpdk-control-choose';

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
 *         'title'          => 'This title is a tooltip',
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
  public function draw()
  {
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
 *         'title'          => 'This title is a tooltip',
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
  public function draw()
  {
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
 *         'title'          => 'This title is a tooltip',
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
 *         'title'          => 'This title is a tooltip',
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
 *         'type'           => WPDKUIControlType::LABEL,
 *         'id'             => 'id',
 *         'for'            => '',
 *         'value'          => 'Label Text',
 *         'attrs'          => array(),
 *         'data'           => array(),
 *         'class'          => array(),
 *         'style'          => '',
 *         'title'          => 'This title is a tooltip',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlLabel
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-03-20
 * @version            1.0.0
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
  public function __construct( $item )
  {
    $item['type'] = WPDKUIControlType::LABEL;
    parent::__construct( $item );
  }

  /**
   * Dawing alert control
   *
   * @brief Display
   */
  public function draw()
  {
    echo $this->contentWithKey( 'prepend' );

    $value = isset( $this->item['value'] ) ? $this->item['value'] : '';

    $label          = new WPDKHTMLTagLabel( $value, $this->name, $this->id );
    $label->class   = $this->class;
    $label->class[] = 'wpdk-form-label-inline';
    $label->data    = isset( $this->item['data'] ) ? $this->item['data'] : '';
    $label->style   = isset( $this->item['style'] ) ? $this->item['style'] : '';
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
 *         'title'          => 'This title is a tooltip',
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
 *         'title'          => 'This title is a tooltip',
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
 *         'title'          => 'This title is a tooltip',
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
 *         'title'          => 'This title is a tooltip',
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
 *         'title'          => 'This title is a tooltip',
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
  public function __construct( $item )
  {
    $item['type'] = WPDKUIControlType::SELECT;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw()
  {

    echo $this->contentWithKey( 'prepend' );

    // Create the label
    $label = $this->label();

    // Display right label
    echo is_null( $label ) ? '' : $label->html();

    $input              = new WPDKHTMLTagSelect( $this->item['options'], $this->name, $this->id );
    $input->class       = $this->class;
    $input->class[]     = 'wpdk-form-select';
    $input->class[]     = 'wpdk-ui-control';
    $input->_first_item = isset( $this->item['first_item'] ) ? $this->item['first_item'] : '';
    $input->data        = isset( $this->item['data'] ) ? $this->item['data'] : array();
    $input->style       = isset( $this->item['style'] ) ? $this->item['style'] : null;
    $input->multiple    = isset( $this->item['multiple'] ) ? $this->item['multiple'] : null;
    $input->size        = isset( $this->item['size'] ) ? $this->item['size'] : null;
    $input->disabled    = isset( $this->item['disabled'] ) ? $this->item['disabled'] ? 'disabled' : null : null;
    $input->value       = isset( $this->item['value'] ) ? $this->item['value'] : array();
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
 *         'title'          => 'This title is a tooltip',
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
  public function __construct( $item )
  {
    $item['type'] = WPDKUIControlType::SELECT_LIST;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw()
  {
    echo $this->contentWithKey( 'prepend' );

    // Create the label
    $label = $this->label();

    // Display right label
    echo is_null( $label ) ? '' : $label->html();

    $input           = new WPDKHTMLTagSelect( $this->item['options'], $this->name, $this->id );
    $input->class    = $this->class;
    $input->class[]  = 'wpdk-form-select';
    $input->class[]  = 'wpdk-form-select-size';
    $input->class[]  = 'wpdk-ui-control';
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
 *         'label'          => 'Optional label',
 *         'attrs'          => '',
 *         'data'           => '',
 *         'class'          => '',
 *         'style'          => '',
 *         'title'          => 'This title is a tooltip',
 *         'prepend'        => '',
 *         'append'         => '',
 *     );
 *
 * @class              WPDKUIControlSubmit
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-08-14
 * @version            0.8.2
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

    $label = $this->label();

    /* Display right label. */
    echo is_null( $label ) ? '' : $label->html();

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
 *         'title'           => 'This title is Tooltip',
 *         'prepend'         => '',
 *         'append'          => '',
 *         'popover'         => instance of WPDKUIPopover
 *     );
 *
 * @class              WPDKUIControlSwipe
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2014-02-23
 * @version            0.9.0
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
  public function __construct( $item )
  {
    $item['type'] = WPDKUIControlType::SWIPE;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw()
  {
    // Prepare popover

    /**
      * @var WPDKUIPopover $popover
     */
    $popover = null;

    // since 1.5.0 - check for popover
    if ( isset( $this->item['popover'] ) && is_a( $this->item['popover'], 'WPDKUIPopover' ) ) :

      $popover = $this->item['popover']; ?>

      <div class='wpdk-has-popover wpdk-popover-container'
      data-title='<?php echo $popover->title() ?>'
      data-content='<?php echo esc_attr( $popover->content() ) ?>'
      data-placement='<?php echo $popover->placement ?>'
      data-trigger='<?php echo $popover->trigger ?>'
      data-html='<?php echo $popover->html ? 'true' : 'false' ?>'
      data-animation='<?php echo $popover->animation ? 'true' : 'false' ?>'
      data-container='<?php echo $popover->container ?>'
      data-delay='<?php echo empty( $popover->delay ) ? 0 : json_encode( $popover->delay ) ?>'>
    <?php endif;

    echo $this->contentWithKey( 'prepend' );

    // Create the label
    $label = $this->label();

    // Display left label
    if ( !isset( $this->item['label_placement'] ) || 'left' == $this->item['label_placement'] ) {
      echo is_null( $label ) ? '' : $label->html();
    }

    $input_hidden        = new WPDKHTMLTagInput( '', $this->name, 'wpdk-swipe-' . $this->id );
    $input_hidden->type  = WPDKHTMLTagInputType::HIDDEN;
    $input_hidden->value = isset( $this->item['value'] ) ? $this->item['value'] : '';

    $status = wpdk_is_bool( $this->item['value'] ) ? 'wpdk-form-swipe-on' : '';

    $swipe          = new WPDKHTMLTagSpan( '<span></span>' . $input_hidden->html() );
    $class          = isset( $this->item['class'] ) ?  $this->item['class'] : '';
    $swipe->class   = WPDKHTMLTag::mergeClasses( $class,  'wpdk-form-swipe ' . $status );
    $swipe->id      = $this->id;
    $swipe->data    = isset( $this->item['data'] ) ? $this->item['data'] : array();

    if ( isset( $this->item['userdata'] ) ) {
      $swipe->data['userdata'] = esc_attr( $this->item['userdata'] );
    }

    // Title and tooltip
    $swipe->title = isset( $this->item['title'] ) ? $this->item['title'] : '';
    if ( !empty( $swipe->title ) ) {
      $swipe->class[] = 'wpdk-has-tooltip';
    }

    $swipe->setPropertiesByArray( isset( $this->item['attrs'] ) ? $this->item['attrs'] : '' );

    $swipe->display();

    // Display right label
    if ( isset( $this->item['label_placement'] ) && 'right' == $this->item['label_placement'] ) {
      echo is_null( $label ) ? '' : $label->html();
    }

    echo $this->contentWithKey( 'append' );

    if( ! empty( $popover ) ) {
      echo '</div>';
    }
  }
}

/**
 * SwitchBox control.
 * Experimental for override a standard checkbox
 *
 *     $item = array(
 *         'type'            => WPDKUIControlType::SWITCHBOX,
 *         'label'           => 'label',
 *         'label_placement' => 'left|right',
 *         'id'              => 'id',
 *         'name'            => 'name',
 *         'value'           => 'on',
 *         'checked'         => 'on',
 *         'attrs'           => '',
 *         'data'            => '',
 *         'class'           => '',
 *         'style'           => '',
 *         'title'           => 'This title is a tooltip',
 *         'prepend'         => '',
 *         'append'          => '',
 *     );
 *
 * @class              WPDKUIControlSwitch
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-10-28
 * @version            1.0.0
 * @since              1.3.1
 *
 */
class WPDKUIControlSwitch extends WPDKUIControl {

  /**
   * Create an instance of WPDKUIControlSwipe class
   *
   * @brief Construct
   * @since 1.0.0.b3
   *
   * @param array $item Key value pairs with control info
   *
   * @return WPDKUIControlSwitch
   */
  public function __construct( $item )
  {
    $item['type'] = WPDKUIControlType::SWITCHBOX;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw()
  {
    echo $this->contentWithKey( 'prepend' );

    $input          = new WPDKHTMLTagInput( '', $this->name, $this->id );
    $input->type    = WPDKHTMLTagInputType::CHECKBOX;
    $input->class   = $this->class;
    $input->class[] = 'wpdk-form-switch';
    $input->class[] = 'wpdk-ui-control';
    $input->data    = isset( $this->item['data'] ) ? $this->item['data'] : '';
    $input->value   = isset( $this->item['value'] ) ? $this->item['value'] : '';
    $input->title   = isset( $this->item['title'] ) ? $this->item['title'] : '';
    $input->setPropertiesByArray( isset( $this->item['attrs'] ) ? $this->item['attrs'] : '' );

    if ( isset( $this->item['checked'] ) ) {
      if ( $input->value === $this->item['checked'] ) {
        $input->checked = 'checked';
      }
    }

    $input->display();

    /* Create the label. */
    $label = $this->label();
    $label->display();

    echo $this->contentWithKey( 'append' );

    echo ' ' . $this->guide();
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
 *         'title'          => 'This title is a tooltip',
 *         'prepend'        => '',
 *         'append'         => '',
 *         'guide'          => 'slug-of-guide'
 *     );
 *
 * ### guide
 * The new key `guide` is used to open a Modal Dialog with an iframe on the Developer Center. You can display
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
  public function __construct( $item )
  {
    $item['type'] = WPDKUIControlType::TEXT;
    parent::__construct( $item );
  }

  /**
   * Drawing control
   *
   * @brief Draw
   */
  public function draw()
  {
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
 *         'title'          => 'This title is a tooltip',
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

    /* Create the label. @todo usually you like set this label on the top. */
    $label = $this->label();

    /* Display right label. */
    echo is_null( $label ) ? '' : $label->html();

    $content = isset( $this->item['value'] ) ? $this->item['value'] : '';

    $input              = new WPDKHTMLTagTextarea( $content, $this->name, $this->id );
    $input->class       = $this->class;
    $input->class[]     = 'wpdk-form-textarea';
    $input->class[]     = 'wpdk-ui-control';
    $input->data        = isset( $this->item['data'] ) ? $this->item['data'] : '';
    $input->content     = $content;
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
 * @date               2013-12-29
 * @version            0.9.0
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
  public function __construct( $cla )
  {
    $this->_cla = $cla;
  }

  /**
   * Return an instance of WPDKUIControlsLayout class
   *
   * @brief Init instance
   * @since 1.4.8
   *
   * @param array $cla Controls Layout array
   */
  public static function init( $cla )
  {
    return new self( $cla );
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
  public static function item( $item )
  {
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
  private static function _processItem( $item )
  {
    $class_name = isset( $item['type'] ) ? $item['type'] : '';
    if ( !empty( $class_name ) && class_exists( $class_name ) ) {
      $control = new $class_name( $item );
      $control->display();
    }
  }

  /**
   * Display the controls layout
   *
   * @brief Display
   */
  public function display()
  {
    echo $this->html();
  }

  /**
   * Return the HTML markup pf controls layout array
   *
   * @brief Get the HTML
   *
   * @return string
   */
  public function html()
  {
    // Buffering...
    WPDKHTML::startCompress();

    foreach ( $this->_cla as $key => $value ) : ?>

      <fieldset class="wpdk-form-fieldset wpdk-ui-control">
        <legend><?php echo $key ?></legend>
        <div class="wpdk-fieldset-container">
          <?php $this->_processRows( $value ) ?>
        </div>
      </fieldset>

    <?php endforeach;

    return WPDKHTML::endCompress();
  }

  /**
   * Processing a single row within one or more items (controls array)
   *
   * @brief Processing row
   *
   * @param array $rows
   */
  private function _processRows( $rows )
  {
    foreach ( $rows as $item ) {
      if ( is_string( $item ) && !empty( $item ) ) {
        ?>
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
      elseif ( !empty( $item ) ) {
        echo apply_filters( 'wpdk_form_html_row_before', '<div class="wpdk-form-row">', $item );
        $this->_processRows( $item );
        echo apply_filters( 'wpdk_form_html_row_after', '</div>', $item );
      }
    }
  }

  /**
   * Return a container
   *
   * @brief Container
   *
   * @param array $item Control item
   *
   * @return string
   */
  private function container( $item )
  {
    $class = isset( $item['class'] ) ? $item['class'] : '';
    return sprintf( '<div class="%s">', $class );
  }

}