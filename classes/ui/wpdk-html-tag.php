<?php

/**
 * Generic HTML model. This class is sub class from above class.
 * Thanks to http://www.w3schools.com/tags/default.asp for definitions
 *
 * @class              WPDKHTMLTag
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2014-01-08
 * @version            1.1.1
 *
 */
class WPDKHTMLTag extends WPDKObject {

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $__version
   */
  public $__version = '1.1.1';

  /**
   * Here there are all late binding tag attributes. Image...
   *
   * charset
   * coords
   * href
   * classs
   * ...
   *
   */

  // Global attributes

  public $accesskey = '';
  public $class = array();

  /**
   * HTML inner content of tag.
   *
   * @brief Content
   *
   * @var string $content
   */
  public $content = '';
  public $contenteditable;
  public $contextmenu;

  /**
   * Key value pairs array Attribute data: data-attribute = value
   *
   * @brief Data attribute
   *
   * @var array $data
   */
  public $data = array();
  public $dir;
  public $draggable;
  public $dropzone;
  public $hidden;
  public $id;
  public $lang;
  public $onclick;
  public $spellcheck;
  public $style;
  public $tabindex;

  /**
   * The TAG name
   *
   * @brief Tag name
   *
   * @var WPDKHTMLTagName $tagName
   */
  public $tagName;

  /**
   * Title
   *
   * @brief Title
   *
   * @var string $title
   */
  public $title;

  /**
   * Override. List of tag attributes that can be used on any HTML element.
   *
   * @brief Attributes list
   *
   * @var array $attributes
   */
  protected $attributes = array();

  /**
   * Override. Close format Eg. '</a>' or '/>'
   *
   * @brief Close tag
   *
   * @var string $close
   */
  protected $close;

  /**
   * Override. Open format Eg. '<a'
   *
   * @brief Open tag
   *
   * @var string $open
   */
  protected $open;

  /**
   * List of global common attributes for all HTML tags
   *
   * @brif Global attributes
   *
   * @var array $_globalAttributes
   */
  private $_globalAttributes;

  /**
   * Create an instance of WPDKHTMLTag class
   *
   * @param string $tag_name
   *
   * @return WPDKHTMLTag
   */
  public function __construct( $tag_name )
  {
    // Store this tag name
    $this->tagName = $tag_name;

    // The global attributes below can be used on any HTML element
    $this->_globalAttributes = array(
      'accesskey',
      'class',
      'contenteditable', // HTML 5
      'contextmenu', // HTML 5
      'dir',
      'draggable', // HTML 5
      'dropzone', // HTML 5
      'hidden', // HTML 5
      'id',
      'lang',
      'spellcheck', // HTML 5
      'style',
      'tabindex',
      'title',
      'onclick'
    );
  }

  /**
   * Utility to add a data attribute. You can access directly to `data` array property instead
   *
   * @brief Add data attribute
   *
   * @param string $name  Data attribute name
   * @param string $value Data attribute value
   */
  public function addData( $name, $value )
  {
    if ( !empty( $name ) ) {
      $this->data[$name] = $value;
    }
  }

  /**
   * Utility to add a class. You can access directly to `class` array property instead
   *
   * @brief Add CSS Class
   * @since 1.3.1
   *
   * @param string $class
   */
  public function addClass( $class )
  {
    if ( !empty( $class ) ) {
      $this->class[$class] = $class;
    }
  }

  /**
   * Display HTML markup for this tag
   *
   * @brief Display
   */
  public function display()
  {
    echo $this->html();
  }

  /**
   * Return the HTML markup for this tag
   *
   * @brief Return the HTML markup for this tag
   *
   * @return string
   */
  public function html()
  {

    // Start buffering
    WPDKHTML::startCompress();

    // Open the tag
    echo $this->open;

    // Cycle for tag specify attributes
    foreach ( $this->attributes as $attr ) {
      if ( isset( $this->$attr ) && !is_null( $this->$attr ) ) {
        printf( ' %s="%s"', $attr, stripslashes( $this->$attr ) );
      }
    }

    // Cycle for global common attributes
    foreach ( $this->_globalAttributes as $attr ) {
      if ( isset( $this->$attr ) && !is_null( $this->$attr ) ) {
        if ( 'class' == $attr ) {
          $classes = self::classInline( $this->$attr );
          if( !empty( $classes ) ) {
            printf( ' class="%s"', $classes );
          }
        }
        else {
          printf( ' %s="%s"', $attr, htmlspecialchars( stripslashes( $this->$attr ) ) );
        }
      }
    }

    // Generic data attribute
    $data = self::dataInline( $this->data );
    if ( !empty( $data ) ) {
      printf( ' %s', $data );
    }

    // Content, only for enclousure TAG
    if ( '/>' !== $this->close ) {

      // Close the first part tag
      echo '>';

      // Before content
      $this->beforeContent();

      // Content
      $this->draw();

      // After content
      $this->afterContent();

      // Close
      echo $this->close;
    }

    // Close
    else {
      echo $this->close;
      echo $this->content;
    }

    return WPDKHTML::endCompress();
  }

  /**
   * Override this method to display anything before the content output
   *
   * @brief Before content
   */
  protected function beforeContent()
  {
    /* to override if needed */
  }

  /**
   * Draw the content of tag
   *
   * @note You can override this method in order to customize it
   *
   * @brief Draw
   */
  public function draw()
  {
    echo $this->content;
  }

  /**
   * Override this method to display anything after the content output
   *
   * @brief After content
   */
  protected function afterContent()
  {
    /* to override if needed */
  }

  /**
   * Set one or more WPDKHTMLTag properties by an array.
   *
   * @brief Set properties by array
   *
   * @param array $properties A key value pairs array with the property (key) and its value
   */
  public function setPropertiesByArray( $properties = array() ) {
    if ( !empty( $properties ) ) {
      foreach ( $properties as $key => $value ) {
        if ( in_array( $key, $this->attributes ) || in_array( $key, $this->_globalAttributes ) ) {
          $this->$key = $value;
        }
      }
    }
  }

  // -------------------------------------------------------------------------------------------------------------------
  // SANITIZE
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return a key value pairs array with generic attribute list
   *
   *     self::sanitizeAttributes( 'modal="false" modal="true" color=red' );
   *     // array(2) { ["modal"]=> string(5) "true" ["color"]=> string(3) "red" }
   *
   * @brief Sanitize attributes
   *
   * @param string $attributes Attribute inline
   *
   * @return array
   */
  public static function sanitizeAttributes( $attributes )
  {
    $stack = array();
    if ( is_string( $attributes ) && !empty( $attributes ) ) {
      $single_attrobutes = explode( ' ', $attributes );
      foreach ( $single_attrobutes as $attribute ) {
        $parts            = explode( '=', $attribute );
        $stack[$parts[0]] = trim( $parts[1], "\"''" );
      }
    }

    if ( is_array( $attributes ) ) {
      return $attributes;
    }

    return $stack;
  }

  /**
   * Return a key value pairs array with data attribute list
   *
   *     self::sanitizeAttributes( 'data-modal="false" modal="true" data-color=red' );
   *     // array(2) { ["modal"]=> string(5) "true" ["color"]=> string(3) "red" }
   *
   * @brief Sanitize data attributes
   *
   * @param string $attributes Data attribute inline
   *
   * @return array
   */
  public static function sanitizeData( $attributes )
  {
    $stack = array();
    if ( is_string( $attributes ) && !empty( $attributes ) ) {
      $single_attrobutes = explode( ' ', $attributes );
      foreach ( $single_attrobutes as $attribute ) {
        $parts = explode( '=', $attribute );
        $key   = $parts[0];
        if ( 'data-' == substr( $key, 0, 5 ) ) {
          $key = substr( $key, 5 );
        }
        $stack[$key] = trim( $parts[1], "\"''" );
      }
    }

    if ( is_array( $attributes ) ) {
      foreach ( $attributes as $key => $value ) {
        if ( 'data-' == substr( $key, 0, 5 ) ) {
          $key = substr( $key, 5 );
        }
        $stack[$key] = trim( $value, "\"''" );
      }
    }

    return $stack;
  }

  /**
   * Return key value pairs array unique with css class list. In this way you can unset a secified class.
   * If the input is a string (space separate strings) will return an array with css classes.
   *
   *     $class = 'a b c c';
   *     echo self::sanitizeClasses( $class );
   *
   *     array(
   *       'a' => 'a',
   *       'b' => 'b',
   *       'c' => 'c',
   *      )
   *
   * @brief Sanitize CSS Classes list
   *
   * @param string|array $classes Any string or array with classes
   *
   * @return array
   */
  public static function sanitizeClasses( $classes )
  {
    if( empty( $classes) || is_null( $classes ) ) {
      return array();
    }

    // Convert the string classes in array
    if ( is_string( $classes ) ) {
      $classes = explode( ' ', $classes );
    }

    $classes = array_filter( array_unique( $classes, SORT_STRING ) );

    return array_combine( $classes, $classes );
  }

  /**
   * Return key value pairs array unique with css styke list. In this way you can unset a secified class.
   * If the input is a string (space separate strings) will return an array with css classes.
   *
   *     $style = 'display:block;position:absolute';
   *     echo self::sanitizeStyles( $style );
   *
   *     array(
   *       'display'  => 'block',
   *       'position' => 'absolute',
   *      )
   *
   * @brief Sanitize CSS Classes list
   * @since 1.4.7
   *
   * @param string|array $styles Any string or array with styles
   *
   * @return array
   */
  public static function sanitizeStyles( $styles )
  {
    if ( empty( $styles ) || is_null( $styles ) ) {
      return array();
    }

    // Convert the string styles in array
    if ( is_string( $styles ) ) {
      $entries = explode( ';', $styles );
      $styles  = array();
      foreach ( $entries as $entry ) {
        list( $key, $value ) = explode( ':', $entry, 2 );
        $styles[$key] = trim( $value );
      }
    }

    return array_unique( $styles, SORT_STRING );
  }

  // -------------------------------------------------------------------------------------------------------------------
  // INLINE
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return a inline generic attribute
   *
   *     $data = array(
   *       'color' => 'red',
   *       'size'  => 12
   *     );
   *     echo self::attributeInline( $data, array( 'modal' => "true" ) );
   *     // 'color="red" size="12" modal="true"'
   *
   * @brief Inline Data attribute
   *
   * @param array      $attributes            Key value pairs array with data attribute list
   * @param array|bool $additional_attributes Optional. Additional data
   *
   * @return string
   */
  public static function attributeInline( $attributes, $additional_attributes = false )
  {

    $attributes = self::sanitizeAttributes( $attributes );

    if ( !empty( $additional_attributes ) ) {
      $additional_attributes = self::sanitizeAttributes( $additional_attributes );
      $attributes = array_merge( $attributes, $additional_attributes );
    }
    $stack = array();
    foreach ( $attributes as $key => $value ) {
      $stack[] = sprintf( '%s="%s"', $key, htmlspecialchars( stripslashes( $value ) ) );
    }
    return join( ' ', $stack );
  }

  /**
   * Return a inline data attribute
   *
   *     $data = array(
   *       'color' => 'red',
   *       'size'  => 12
   *     );
   *     echo self::dataInline( $data, array( 'modal' => "true" ) );
   *     // 'data-color="red" data-size="12" data-modal="true"'
   *
   * @brief Inline Data attribute
   *
   * @param array      $data            Key value pairs array with data attribute list
   * @param array|bool $additional_data Optional. Additional data
   *
   * @return string
   */
  public static function dataInline( $data, $additional_data = false )
  {
    $data = self::sanitizeData( $data );

    if ( !empty( $additional_data ) ) {
      $additional_data = self::sanitizeData( $additional_data );
      $data = array_merge( $data, $additional_data );
    }
    $stack = array();
    foreach ( $data as $key => $value ) {
      $stack[] = sprintf( 'data-%s="%s"', $key, htmlspecialchars( stripslashes( $value ) ) );
    }
    return join( ' ', $stack );
  }

  /**
   * Return a sanitize and inline list of css classes
   *
   *     $classes = array(
   *       'color',
   *       'modal',
   *     );
   *     echo self::classInline( $classes, array( 'modal', 'hide' ) );
   *     // 'color modal hide'
   *
   *     echo self::classInline( $classes, 'delta modal' );
   *     // 'color delta modal'
   *
   * @brief Inline CSS class
   *
   * @param array|string $classes            List of css classes
   * @param array|bool   $additional_classes Optional. Additional classes
   *
   * @return string
   */
  public static function classInline( $classes, $additional_classes = false )
  {
    $classes = self::sanitizeClasses( $classes );

    if ( !empty( $additional_classes ) ) {
      $additional_classes = self::sanitizeClasses( $additional_classes );
      if ( !empty( $additional_classes ) ) {
        $classes = array_merge( $classes, $additional_classes );
      }
    }

    $keys = array_keys( $classes );

    return join( ' ', $keys );
  }

  /**
   * Return a sanitize and inline list of css styles
   *
   *     $styles = array(
   *       'position' => 'absolute',
   *       'top' => 0,
   *     );
   *     echo self::styleInline( $styles, array( 'left' => 0, 'display' => 'block' ) );
   *     // 'position:absolute;top:0;left:0;display:block'
   *
   *     echo self::styleInline( $styles, 'display:block' );
   *     // 'position:absolute;top:0;display:block'
   *
   * @brief Inline CSS class
   * @since 1.4.7
   *
   * @param array|string $styles            List of css styles
   * @param array|bool   $additional_styles Optional. Additional styles
   *
   * @return string
   */
  public static function styleInline( $styles, $additional_styles = false )
  {
    $styles = self::sanitizeStyles( $styles );

    if ( !empty( $additional_styles ) ) {
      $additional_styles = self::sanitizeStyles( $additional_styles );
      if ( !empty( $additional_styles ) ) {
        $styles = array_merge( $styles, $additional_styles );
      }
    }

    $result = array();
    foreach ( $styles as $key => $value ) {
      $result[] = $key . ':' . $value;
    }

    return rtrim( implode( ';', $result ), ';' );

  }

  // -------------------------------------------------------------------------------------------------------------------
  // UTILITIES
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Merge one or more class
   *
   * @brief Merge
   * @since 1.4.0
   *
   * @param array|string $class  Initial string or array class to merge
   * @param array|string $class2 Optional.
   * @param array|string $_      Optional.
   *
   * @return array
   */
  public static function mergeClasses( $class, $class2 = null, $_ = null )
  {
    $class = self::sanitizeClasses( $class );

    if ( func_num_args() < 2 ) {
      return $class;
    }

    for ( $i = 1; $i < func_num_args(); $i++ ) {
      $arg = func_get_arg( $i );
      if ( !is_null( $arg ) ) {
        $s     = self::sanitizeClasses( $arg );
        $class = array_merge( $class, $s );
      }
    }
    return self::sanitizeClasses( $class );
  }

}

/**
 * This class is a list of constant for HTML 4.1 tag supported
 *
 * @class              WPDKHTMLTagName
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-10-16
 * @version            0.8.2
 */
class WPDKHTMLTagName {

  // HTML 4.1 tags
  const A        = 'a';
  const BUTTON   = 'button';
  const FIELDSET = 'fieldset';
  const FORM     = 'form';
  const IMG      = 'img';
  const INPUT    = 'input';
  const LABEL    = 'label';
  const LEGEND   = 'legend';
  const SELECT   = 'select';
  const SPAN     = 'span';
  const TEXTAREA = 'textarea';
}

/**
 * This class is a list of constants for HTML type input tag.
 *
 * @class              WPDKHTMLTagInputType
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 */
class WPDKHTMLTagInputType {

  // Input types
  const BUTTON         = 'button';
  const CHECKBOX       = 'checkbox';
  const COLOR          = 'color';
  const DATE           = 'date';
  const DATETIME       = 'datetime';
  const DATETIME_LOCAL = 'datetime-local';
  const EMAIL          = 'email';
  const FILE           = 'file';
  const HIDDEN         = 'hidden';
  const IMAGE          = 'image';
  const MONTH          = 'month';
  const NUMBER         = 'number';
  const PASSWORD       = 'password';
  const RADIO          = 'radio';
  const RANGE          = 'range';
  const RESET          = 'reset';
  const SEARCH         = 'search';
  const SUBMIT         = 'submit';
  const TEL            = 'tel';
  const TEXT           = 'text';
  const TIME           = 'time';
  const URL            = 'url';
  const WEEK           = 'week';
}

/**
 * Wrapper model for tag A.
 * Remeber to add this tag in WPDKHTMLTagName
 *
 * @class              WPDKHTMLTagA
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKHTMLTagA extends WPDKHTMLTag {

  /**
   * Not supported in HTML5. Specifies the character-set of a linked document.
   *
   * @brief Charset
   *
   * @var string $charset
   */
  public $charset;
  /**
   * Not supported in HTML5. Specifies the coordinates of a link
   *
   * @brief Coordinates
   *
   * @var string $coords
   */
  public $coords;
  /**
   * Specifies the URL of the page the link goes to.
   *
   * @brief URI
   *
   * @var string $href
   */
  public $href;
  /**
   * Specifies the language of the linked document. Value language_code
   *
   * @brief Language
   *
   * @var string $hreflang
   */
  public $hreflang;
  /**
   * New in HTML5. Specifies what media/device the linked document is optimized for. Value media_query
   *
   * @brief Media type
   *
   * @var string $media
   */
  public $media;
  /**
   * Not supported in HTML5. Specifies the name of an anchor
   *
   * @brief Anchor name
   *
   * @var string $name
   */
  public $name;
  /**
   * Specifies the relationship between the current document and the linked document.
   * Available values are: alternate, author, bookmark, help, license, next, nofollow, noreferrer, prefetch,
   * prev, search, tag
   *
   * @brief Relationship
   *
   * @var string $rel
   */
  public $rel;
  /**
   * Not supported in HTML5. Specifies the relationship between the linked document and the current document
   *
   * @brief Rev
   *
   * @var string $rev
   */
  public $rev;
  /**
   * Not supported in HTML5. Specifies the shape of a link. Values: default, rect, circl,e poly
   *
   * @brief Shape
   *
   * @var string $shape
   */
  public $shape;
  /**
   * Specifies where to open the linked document. Values: _blank, _self, _parent, _top or framename
   *
   * @brief Target
   *
   * @var string $target
   */
  public $target;
  /**
   * New in HTML 5. Specifies the MIME type of the linked document. Value MIME_type
   *
   * @brief Type
   *
   * @var string $type
   */
  public $type;

  /**
   * Create an instance of WPDKHTMLTagA class
   *
   * @brief Construct
   *
   * @param string $content HTML inner (or adjacent) content
   * @param string $href    The href attribute
   */
  public function __construct( $content = '', $href = '' ) {

    // Create an WPDKHTMLTag instance
    parent::__construct( WPDKHTMLTagName::A );

    // The content
    $this->content = $content;

    // The href. Late binding
    $this->href = $href;

    // Setting
    $this->open       = '<a';
    $this->close      = '</a>';
    $this->attributes = array(
      'charset',
      'coords',
      'href',
      'hreflang',
      'media',
      // HTML 5
      'name',
      'rel',
      'rev',
      'shape',
      'target',
      'type'
      // HTML 5
    );
  }

}

/**
 * Wrapper model for tag BUTTON.
 * Remeber to add this tag in WPDKHTMLTagName
 *
 * @class              WPDKHTMLTagButton
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKHTMLTagButton extends WPDKHTMLTag {

  /* Interface. */

  /**
   * New in HTML 5. Specifies that an <input> element should automatically get focus when the page loads.
   * Values `autofocus`.
   *
   * @brief Autofocus
   *
   * @var string $autofocus
   */
  public $autofocus;

  /**
   * Set to 'disabled'. Specifies that a button should be disabled.
   *
   * @var string $disabled
   */
  public $disabled;
  /**
   * New in HTML 5. Specifies one or more forms the <input> element belongs to
   *
   * @brief Form ID
   *
   * @var string $form
   */
  public $form;
  /**
   * New in HTML 5. Specifies the URL of the file that will process the input control when the form is submitted
   * (for type="submit" and type="image"). Values URL
   *
   * @brief URL action
   *
   * @var string $formaction
   */
  public $formaction;
  /**
   * New in HTML 5. Specifies how the form-data should be encoded when submitting it to the server (for type="submit"
   * and type="image"). Values `application/x-www-form-urlencoded`, `multipart/form-data`, `text/plain`
   *
   * @brief Encryption type
   *
   * @var string $formenctype
   */
  public $formenctype;
  /**
   * New in HTML 5. Defines the HTTP method for sending data to the action URL (for type="submit" and type="image").
   * Values `get`, `post`
   *
   * @brief Method
   *
   * @var string $formmethod
   */
  public $formmethod;
  /**
   * New in HTML 5. Defines that form elements should not be validated when submitted. Values `formnovalidate`
   *
   * @brief Validate
   *
   * @var string $formnovalidate
   */
  public $formnovalidate;
  /**
   * New in HTML 5. Specifies where to display the response after submitting the form. Only for type="submit".
   * Values `_blank`, `_self`, `_parent`, `_top` or framename
   *
   * @brief Target
   *
   * @var string $formtarget
   */
  public $formtarget;

  /**
   * Specifies a name for the button.
   *
   * @brief Name
   *
   * @var string $name
   */
  public $name;

  /**
   * Specifies the type of button: button, reset, submit
   *
   * @brief Type
   *
   * @var string $type
   */
  public $type;

  /**
   * Specifies an initial value for the button
   *
   * @brief Value
   *
   * @var string $value
   */
  public $value;

  /**
   * Create an instance of WPDKHTMLTagButton class
   *
   * @brief Construct
   *
   * @param string $content HTML inner (or adjacent) content
   */
  public function __construct( $content = '' )
  {

    // Create an WPDKHTMLTag instance
    parent::__construct( WPDKHTMLTagName::BUTTON );

    // The content
    $this->content = $content;

    // Setting
    $this->open       = '<button';
    $this->close      = '</button>';
    $this->attributes = array(
      'autofocus',
      'disabled',
      'form',
      'formaction',
      'formenctype',
      'formmethod',
      'formnovalidate',
      'formtarget',
      'name',
      'type',
      'value'
    );
  }

}

/**
 *
 * Wrapper model for tag FIELDSET.
 * Remeber to add this tag in WPDKHTMLTagName
 *
 * @class              WPDKHTMLTagFieldset
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKHTMLTagFieldset extends WPDKHTMLTag {

  /* Interface. */

  /**
   * New in HTML 5. Specifies that a group of related form elements should be disabled
   *
   * @brief Disabled
   *
   * @var string $disabled
   */
  public $disabled;
  /**
   * New in HTML 5. Specifies one or more forms the fieldset belongs to. Value: form_id
   *
   * @brief Form id
   *
   * @var string $form
   */
  public $form;
  /**
   * Pointer to WPDKHTMLTagLegend object. This is an utility. This class create a WPDKHTMLTagLegend object for you
   * when the params `legend` in constructor is not empty.
   *
   * @brief Legend
   *
   * @var WPDKHTMLTagLegend $legend
   */
  public $legend;
  /**
   * New in HTML 5. Specifies a name for the fieldset.
   *
   * @brief Name
   *
   * @var string $name
   */
  public $name;

  /**
   * Create an instance WPDKHTMLTagFieldset class
   *
   * @brief Construct
   *
   * @param string $content HTML inner (or adjacent) content
   * @param string $legend  Content of WPDKHTMLTaglegend object
   */
  public function __construct( $content = '', $legend = '' ) {

    /* Create an WPDKHTMLTag instance. */
    parent::__construct( WPDKHTMLTagName::FIELDSET );

    /* The content. */
    $this->content = $content;

    /* Legend. */
    if ( !empty( $legend ) ) {
      $this->legend = new WPDKHTMLTagLegend( $legend );
    }

    /* Setting. */
    $this->open       = '<fieldset';
    $this->close      = '</fieldset>';
    $this->attributes = array(
      'disabled',
      'form',
      'name'
    );
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Over-riding
  // -------------------------------------------------------------------------------------------------------------------

  public function beforeContent()
  {
    if ( !empty( $this->legend ) ) {
      $this->legend->display();
    }
  }

}

/**
 * Wrapper model for tag FORM.
 * Remeber to add this tag in WPDKHTMLTagName
 *
 * @class              WPDKHTMLTagForm
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKHTMLTagForm extends WPDKHTMLTag {

  /**
   * Not supported in HTML5. Specifies the types of files that the server accepts (that can be submitted
   * through a file upload). Value: MIME_type
   *
   * @brief Accept
   *
   * @var string $accept
   */
  public $accept;

  /**
   * Specifies the character encodings that are to be used for the form submission. Value: character_set
   *
   * @brief Accept charset
   *
   * @var string $accept_charset
   */
  public $accept_charset;

  /**
   * Specifies where to send the form-data when a form is submitted.
   *
   * @brief Action
   *
   * @var string $action
   */
  public $action;

  /**
   * New in HTML 5. Specifies whether a form should have autocomplete on or off. Values: on, off
   *
   * @brief Autocomplete
   *
   * @var string $autocomplete
   */
  public $autocomplete;

  /**
   * Specifies how the form-data should be encoded when submitting it to the server (only for method="post").
   * Values: application/x-www-form-urlencoded, multipart/form-data, text/plain
   *
   * @brief Encription type
   *
   * @var string $enctype
   */
  public $enctype;

  /**
   * Specifies the HTTP method to use when sending form-data. Values: get, post
   *
   * @brief Method
   *
   * @var string $method
   */
  public $method;

  /**
   * Specifies the name of a form
   *
   * @brief Form name
   *
   * @var string $name
   */
  public $name;

  /**
   * New in HTML 5. Specifies that the form should not be validated when submitted. Value: novalidate
   *
   * @brief No validate
   *
   * @var string $novalidate
   */
  public $novalidate;

  /**
   * Specifies where to display the response that is received after submitting the form.
   * Values: _blank, _self, _parent, _top
   *
   * @brief Target
   *
   * @var string $target
   */
  public $target;

  /**
   * Create an instance of WPDKHTMLTagForm class
   *
   * @brief Construct
   *
   * @param string $content HTML inner (or adjacent) content
   *
   * @return WPDKHTMLTagForm
   */
  public function __construct( $content = '' )
  {
    // Create an WPDKHTMLTag instance
    parent::__construct( WPDKHTMLTagName::FORM );

    // The content
    $this->content = $content;

    // Setting
    $this->open       = '<form';
    $this->close      = '</form>';
    $this->attributes = array(
      'accept',
      'accept-charset',
      'action',
      'autocomplete',
      'enctype',
      'method',
      'name',
      'novalidate',
      'target'
    );
  }
}


/**
 * Wrapper model for tag Img.
 * Remeber to add this tag in WPDKHTMLTagName
 *
 * @class              WPDKHTMLTagImg
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-10-18
 * @version            1.0.0
 * @since              1.3.1
 *
 */
class WPDKHTMLTagImg extends WPDKHTMLTag {

  /**
   * Specifies an alternate text for an image
   *
   * @var string $alt
   */
  public $alt;

  /**
   * Allow images from third-party sites that allow cross-origin access to be used with canvas:
   * anonymous, use-credentials
   *
   * @var string $crossorigin
   */
  public $crossorigin;

  /**
   * Specifies the height of an image
   *
   * @var string $height
   */
  public $height;

  /**
   * Specifies an image as a server-side image-map: ismap
   *
   * @var string $ismap
   */
  public $ismap;

  /**
   * Specifies the URL of an image
   *
   * @var string $src
   */
  public $src;

  /**
   * Specifies an image as a client-side image-map. Values #mapnam
   *
   * @var string $usemap
   */
  public $usemap;

  /**
   * Specifies the width of an image
   *
   * @var string $width
   */
  public $width;

  /**
   * Create an instance of WPDKHTMLTagImg class
   *
   * @brief Construct
   *
   * @param string $src    Optional. Specifies the URL of an image
   * @param string $alt    Optional. Specifies an alternate text for an image
   * @param string $width  Optional. Specifies the width of an image
   * @param string $height Optional. Specifies the height of an image
   *
   * @return WPDKHTMLTagImg
   */
  public function __construct( $src = '', $alt = '', $width = '', $height = '' )
  {
    // Create an WPDKHTMLTag instance
    parent::__construct( WPDKHTMLTagName::IMG );

    $this->src    = $src;
    $this->alt    = $alt;
    $this->width  = $width;
    $this->height = $height;

    /* Setting. */
    $this->open       = '<img';
    $this->close      = '/>';
    $this->attributes = array(
      'alt',
      'crossorigin',
      'height',
      'ismap',
      'src',
      'usemap',
      'width',
    );
  }

}


/**
 * Wrapper model for tag INPUT.
 * Remeber to add this tag in WPDKHTMLTagName
 *
 * @class              WPDKHTMLTagInput
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKHTMLTagInput extends WPDKHTMLTag {

  /* Interface. */

  /**
   * Not supported in HTML5. Specifies the character-set of a linked document.
   *
   * @brief Char set accapt
   *
   * @var string $accept
   */
  public $accept;
  /**
   * Not supported in HTML5. Deprecated in HTML 4.01. Specifies the alignment of an image input (only for
   * type="image"). Values: left, right, top, middle, bottom
   *
   * @brief Align
   *
   * @deprecated in HTML 4.01
   *
   * @var string $align
   *
   */
  public $align;
  /**
   * Specifies an alternate text for images (only for type="image")
   *
   * @brief Alternative text
   *
   * @var string $alt
   */
  public $alt;
  /**
   * New in HTML 5. Specifies whether an <input> element should have autocomplete enabled. Values `on`, `off`
   *
   * @brief Autocomplete
   *
   * @var string $autocomplete
   */
  public $autocomplete;
  /**
   * New in HTML 5. Specifies that an <input> element should automatically get focus when the page loads.
   * Values `autofocus`.
   *
   * @brief Autofocus
   *
   * @var string $autofocus
   */
  public $autofocus;
  /**
   * Specifies that an <input> element should be pre-selected when the page loads (for type="checkbox" or
   * type="radio"). Values `checked`
   *
   * @brief Checked
   *
   * @var string $checked
   */
  public $checked;
  /**
   * Specifies that an <input> element should be disabled. Values `disabled`
   *
   * @brief Disabled
   *
   * @var string $disabled
   */
  public $disabled;
  /**
   * New in HTML 5. Specifies one or more forms the <input> element belongs to
   *
   * @brief Form ID
   *
   * @var string $form
   */
  public $form;
  /**
   * New in HTML 5. Specifies the URL of the file that will process the input control when the form is submitted
   * (for type="submit" and type="image"). Values URL
   *
   * @brief URL action
   *
   * @var string $formaction
   */
  public $formaction;
  /**
   * New in HTML 5. Specifies how the form-data should be encoded when submitting it to the server (for type="submit"
   * and type="image"). Values `application/x-www-form-urlencoded`, `multipart/form-data`, `text/plain`
   *
   * @brief Encryption type
   *
   * @var string $formenctype
   */
  public $formenctype;
  /**
   * New in HTML 5. Defines the HTTP method for sending data to the action URL (for type="submit" and type="image").
   * Values `get`, `post`
   *
   * @brief Method
   *
   * @var string $formmethod
   */
  public $formmethod;
  /**
   * New in HTML 5. Defines that form elements should not be validated when submitted. Values `formnovalidate`
   *
   * @brief Validate
   *
   * @var string $formnovalidate
   */
  public $formnovalidate;
  /**
   * New in HTML 5. Specifies where to display the response that is received after submitting the form (for
   * type="submit" and type="image"). Values `_blank`, `_self`, `_parent`, `_top` or framename
   *
   * @var string $formtarget
   */
  public $formtarget;
  /**
   * New in HTML 5. Specifies the height of an <input> element (only for type="image"). Values in pixels
   *
   * @brief Height
   *
   * @var string $height
   */
  public $height;
  /**
   * New in HTML 5. Refers to a <datalist> element that contains pre-defined options for an <input> element
   *
   * @brief Detail id
   * @var string $list
   */
  public $list;
  /**
   * New in HTML 5. Specifies the maximum value for an <input> element. Values `number`, `date`
   *
   * @brief Max value
   *
   * @var string $max
   */
  public $max;
  /**
   * Specifies the maximum number of characters allowed in an <input> element
   *
   * @brief Max length
   *
   * @var int $maxlength
   */
  public $maxlength;
  /**
   * New in HTML 5. Specifies a minimum value for an <input> element. Values: number, date
   *
   * @brief Min value
   *
   * @var int $min
   */
  public $min;
  /**
   * New in HTML 5. Specifies that a user can enter more than one value in an <input> element, Values `multiple`
   *
   * @brief Multiple
   *
   * @var string $multiple
   */
  public $multiple;
  /**
   * Specifies the name of an <input> element
   *
   * @brief Name
   *
   * @var string $name
   */
  public $name;
  /**
   * New in HTML 5. Specifies a regular expression that an <input> element's value is checked against. Value: regexp
   *
   * @brief Regular expression
   *
   * @var string $pattern
   */
  public $pattern;
  /**
   * New in HTML 5. Specifies a short hint that describes the expected value of an <input> element
   *
   * @brief Placeholder
   *
   * @var string $placeholder
   */
  public $placeholder;
  /**
   * Specifies that an input field is read-only. Values `readonly`
   *
   * @brief Read only
   *
   * @var string $readonly
   */
  public $readonly;
  /**
   * New in HTML 5. Specifies that an input field must be filled out before submitting the form. Values `required`
   *
   * @brief Required
   *
   * @var string $required
   */
  public $required;
  /**
   * Specifies the width, in characters, of an <input> element
   *
   * @brief Size
   *
   * @var int $size
   */
  public $size;
  /**
   * Specifies the URL of the image to use as a submit button (only for type="image")
   *
   * @brief URL
   *
   * @var string $src
   */
  public $src;
  /**
   * New in HTML 5. Specifies the legal number intervals for an input field
   *
   * @brief Step
   *
   * @var int $step
   */
  public $step;
  /**
   * Specifies the type <input> element to display. For values use the WPDKHTMLTagInputType
   *
   * @brief Type
   *
   * @var string $type
   */
  public $type;
  /**
   * Specifies the value of an <input> element
   *
   * @brief Value
   *
   * @var string $value
   */
  public $value;
  /**
   * New in HTML 5. Specifies the width of an <input> element (only for type="image")
   *
   * @brief Width
   *
   * @var int $width
   */
  public $width;

  /**
   * Create an instance of WPDKHTMLTagInput class
   *
   * @brief Construct
   *
   * @param string $content Optional. HTML inner (or adjacent) content
   * @param string $name    Optional. Attribute name
   * @param string $id      Optional. Attribute id
   */
  public function __construct( $content = '', $name = '', $id = '' ) {

    // Create an WPDKHTMLTag instance
    parent::__construct( WPDKHTMLTagName::INPUT );

    // The content
    $this->content = $content;

    // Set name
    if ( !empty( $name ) ) {
      $this->name = $name;
    }

    // To default, if $name exists and $id not exists, then $id = $name
    if ( !empty( $name ) && empty( $id ) ) {

      // Check if field is an array
      if ( '[]' == substr( $name, -2 ) ) {

        // Sanitize id
        $this->id = substr( $name, 0, strlen( $name ) - 2 );
      }

      else {
        $this->id = $name;
      }
    }

    // This is the case where $name = '' and $id != ''
    elseif ( !empty( $id ) ) {
      $this->id = $id;
    }

    // Setting
    $this->open       = '<input';
    $this->close      = '/>';
    $this->attributes = array(
      'accept',
      'align',
      'alt',
      'autocomplete',
      'autofocus',
      'checked',
      'disabled',
      'form',
      'formaction',
      'formenctype',
      'formmethod',
      'formnovalidate',
      'formtarget',
      'height',
      'list',
      'max',
      'maxlength',
      'min',
      'multiple',
      'name',
      'pattern',
      'placeholder',
      'readonly',
      'required',
      'size',
      'src',
      'step',
      'type',
      'value',
      'width'
    );
  }

}

/**
 *
 * Wrapper model for tag LABEL.
 * Remeber to add this tag in WPDKHTMLTagName
 *
 * @class              WPDKHTMLTagLabel
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-01-30
 * @version            0.8.2
 *
 */
class WPDKHTMLTagLabel extends WPDKHTMLTag {

  /* Interface. */

  /**
   * Specifies which form element a label is bound to. Value: element_id
   *
   * @brief For
   *
   * @var string $for
   */
  public $for;
  /**
   * New in HTML 5. Specifies one or more forms the label belongs to. Value: form_id
   *
   * @brief Form id
   *
   * @var string $form
   */
  public $form;

  /**
   * Create an instance of WPDKHTMLTagLabel class
   *
   * @brief Construct
   *
   * @param string $content HTML inner (or adjacent) content
   */
  public function __construct( $content = '' ) {

    /* Create an WPDKHTMLTag instance. */
    parent::__construct( WPDKHTMLTagName::LABEL );

    /* The content. */
    $this->content = $content;

    /* Setting. */
    $this->open       = '<label';
    $this->close      = '</label>';
    $this->attributes = array(
      'for',
      'form'
    );
  }

}

/**
 * Wrapper model for tag LEGEND.
 * Remeber to add this tag in WPDKHTMLTagName
 *
 * @class              WPDKHTMLTagLegend
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKHTMLTagLegend extends WPDKHTMLTag {

  /* Interface. */

  /**
   * Deprecated. Use styles instead. Specifies the alignment of the caption. Values: top, bottom, left, right
   *
   * @brief Align
   *
   * @deprecated Use styles instead.
   *
   * @var string $align
   */
  public $align;

  /**
   * Create an instance of WPDKHTMLTagLegend class
   *
   * @brief Construct
   *
   * @param string $content HTML inner (or adjacent) content
   */
  function __construct( $content = '' ) {

    /* Create an WPDKHTMLTag instance. */
    parent::__construct( WPDKHTMLTagName::LEGEND );

    /* The content. */
    $this->content = $content;

    /* Setting. */
    $this->open       = '<legend';
    $this->close      = '</legend>';
    $this->attributes = array(
      'align',
    );
  }

}

/**
 * Wrapper model for tag SELECT.
 * Remeber to add this tag in WPDKHTMLTagName
 *
 * @class              WPDKHTMLTagSelect
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-12-29
 * @version            0.9.0
 *
 */
class WPDKHTMLTagSelect extends WPDKHTMLTag {

  /* Interface. */

  public $autofocus;
  public $disabled;
  public $form;
  public $multiple;
  public $name;
  public $size;
  public $value;

  /**
   * Used to display a special first item (disable and display none and non selectable) to use instead label
   *
   * @brief First item disabled
   * @since 1.4.8
   *
   * @var string $_first_item
   */
  public $_first_item = '';

  private $_options;

  /**
   * Create an instance of WPDKHTMLTagSelect class
   *
   * @brief Construct
   *
   * @param array|callback $options A key value pairs array with value/text or a callback function
   * @param string         $name    Attribute name
   * @param string         $id      Attribute id
   *
   * @todo  The internal method _options doesn't check for associative array
   *
   */
  public function __construct( $options = array(), $name = '', $id = '' )
  {

    /* Create an WPDKHTMLTag instance. */
    parent::__construct( WPDKHTMLTagName::SELECT );

    /* Store the options for draw later. */
    $this->_options = $options;

    /* Set name. */
    if ( !empty( $name ) ) {
      $this->name = $name;
    }

    /* To default, if $name exists and $id not exists, then $id = $name. */
    if ( !empty( $name ) && empty( $id ) ) {
      /* Check if field is an array. */
      if ( '[]' == substr( $name, -2 ) ) {
        /* Sanitize id. */
        $this->id = substr( $name, 0, strlen( $name ) - 2 );
      }

      else {
        $this->id = $name;
      }
    }
    /* This is the case where $name = '' and $id != '' */
    elseif ( !empty( $id ) ) {
      $this->id = $id;
    }

    /* Setting. */
    $this->open       = '<select';
    $this->close      = '</select>';
    $this->attributes = array(
      'autofocus',
      'disabled',
      'form',
      'multiple',
      'name',
      'size'
    );
  }

  /**
   * Override parent method for draw the options with selectd values.
   *
   * @brief Draw
   */
  public function draw()
  {
    $options = array();
    if ( is_callable( $this->_options ) ) {
      $options = call_user_func( $this->_options, $this );
    }
    elseif ( is_array( $this->_options ) ) {
      $options = $this->_options;
    }
    $this->content = $this->options( $options );
    parent::draw();
  }

  /**
   * Return the HTML markup for the option and optgroup tag.
   *
   * @brief Build the options
   *
   * @param array $options A key value pairs array. If the value is an array then an optio group is created.
   *
   * @return string
   */
  public function options( $options )
  {
    ob_start();
    $this->_options( $options );
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }

  /**
   * Recursive method to build the options and option group for select tag.
   * Display the HTML markup for the option and optgroup tag.
   *
   * @brief Build the options
   *
   * @param array $options A key value pairs array. If the value is an array then an optio group is created.
   */
  private function _options( $options )
  {
    if ( !empty( $this->_first_item ) ) : ?>
      <option value=""
              disabled="disabled"
              selected="selected"
              style="display:none"><?php echo $this->_first_item ?></option>
    <?php endif;

    foreach ( $options as $key => $option ) : ?>
      <?php if ( is_array( $option ) ) : ?>
        <optgroup class="wpdk-form-optiongroup" label="<?php echo $key ?>">
        <?php $this->_options( $option ); ?>
      </optgroup>
      <?php else : ?>
        <option class="wpdk-form-option" <?php if ( !empty( $this->value ) ) echo WPDKHTMLTagSelect::selected( $this->value, $key ) ?>
                value="<?php echo $key ?>"><?php echo $option ?></option>
      <?php endif; ?>
    <?php endforeach;
  }

  /**
   * Return HTML `selected` attribute or empty string.
   * Commodity to extends selected() WordPress function with array check.
   *
   * @brief WordPress selected() replacement
   * @since 1.2.0
   *
   * @param string|array $haystack Single value or array
   * @param mixed        $current  (true) The other value to compare if not just true
   *
   *     <select name="test">
   *       <option <?php echo WPDKHTMLTagSelect::selected( 'value', $value ) ?>>Value</option>
   *       ...
   *     </select>
   *
   *     <select name="test">
   *       <option <?php echo WPDKHTMLTagSelect::selected( array( '1', '14', '16'), $value ) ?>>Value</option>
   *       ...
   *     </select>
   *
   * @return string HTML attribute or empty string
   */
  public static function selected( $haystack, $current )
  {
    if ( is_array( $haystack ) ) {
      if ( in_array( $current, $haystack ) ) {
        $current = $haystack = 1;

        return selected( $haystack, $current, false );
      }

      return false;
    }

    return selected( $haystack, $current, false );
  }

}

/**
 * Wrapper model for tag SPAN.
 * Remeber to add this tag in WPDKHTMLTagName
 *
 * @class              WPDKHTMLTagSpan
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKHTMLTagSpan extends WPDKHTMLTag {

  /* Interface. */

  /**
   * Create an instance of WPDKHTMLTagSpan class
   *
   * @brief Construct
   *
   * @param string $content HTML inner (or adjacent) content
   */
  public function __construct( $content = '' ) {

    /* Create an WPDKHTMLTag instance. */
    parent::__construct( WPDKHTMLTagName::SPAN );

    /* The content. */
    $this->content = $content;

    /* Setting. */
    $this->open       = '<span';
    $this->close      = '</span>';
    $this->attributes = array();
  }

}

/**
 * Wrapper model for tag TEXTAREA.
 * Remeber to add this tag in WPDKHTMLTagName
 *
 * @class              WPDKHTMLTagTextarea
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-01-22
 * @version            0.9.0
 *
 */
class WPDKHTMLTagTextarea extends WPDKHTMLTag {

  /* Interface. */

  /**
   * New in HTML5. Specifies that a text area should automatically get focus when the page loads. Default 'autofocus'
   *
   * @brief Autofocus
   *
   * @var string $autofocus
   */
  public $autofocus;
  /**
   * Specifies the visible width of a text area.
   *
   * @brief Column
   *
   * @var int $cols
   */
  public $cols;
  /**
   * Specifies that a text area should be disabled. Value: `disabled`
   *
   * @brief Disabled
   *
   * @var string $disabled
   */
  public $disabled;
  /**
   * New in HTML5. Specifies one or more forms the text area belongs to
   *
   * @brief Form id
   *
   * @var string $form
   */
  public $form;
  /**
   * New in HTML5. Specifies the maximum number of characters allowed in the text area
   *
   * @brief Maxlength
   *
   * @var int $maxlength
   */
  public $maxlength;
  /**
   * Specifies the name for a text area.
   *
   * @brief Name
   *
   * @var string $name
   */
  public $name;
  /**
   * New in HTML5. Specifies a short hint that describes the expected value of a text area
   *
   * @brief Placeholder
   *
   * @var string $placeholder
   */
  public $placeholder;
  /**
   * Specifies that a text area should be read-only. Value: `readonly`
   *
   * @brief Read only
   *
   * @var string $readonly
   */
  public $readonly;
  /**
   * New in HTML5. Specifies that a text area is required/must be filled out, Default 'required'
   *
   * @brief Required
   *
   * @var string $required
   */
  public $required;
  /**
   * Specifies the visible number of lines in a text area
   *
   * @brief Rows
   *
   * @var int $rows
   */
  public $rows;
  /**
   * New in HTML5. Specifies how the text in a text area is to be wrapped when submitted in a form.
   * Default 'hard', 'soft'
   *
   * @brief Wrap
   *
   * @var string $wrap
   */
  public $wrap;

  /**
   * Create an instance of WPDKHTMLTagTextarea class
   *
   * @brief Construct
   *
   * @param string         $content  HTML inner (or adjacent) content
   * @param string         $name     Attribute name
   * @param string         $id       Attribute id
   *
   * @return WPDKHTMLTagTextarea
   */
  public function __construct( $content = '', $name = '', $id = '' ) {

    /* Create an WPDKHTMLTag instance. */
    parent::__construct( WPDKHTMLTagName::TEXTAREA );

    /* The content. */
    $this->content = $content;

    /* Set name. */
    if ( !empty( $name ) ) {
      $this->name = $name;
    }

    /* To default, if $name exists and $id not exists, then $id = $name. */
    if ( !empty( $name ) && empty( $id ) ) {
      /* Check if field is an array. */
      if ( '[]' == substr( $name, -2 ) ) {
        /* Sanitize id. */
        $this->id = substr( $name, 0, strlen( $name ) - 2 );
      }

      else {
        $this->id = $name;
      }
    }
    /* This is the case where $name = '' and $id != '' */
    elseif ( !empty( $id ) ) {
      $this->id = $id;
    }

    /* Setting. */
    $this->open       = '<textarea';
    $this->close      = '</textarea>';
    $this->attributes = array(
      'autofocus',
      'cols',
      'disabled',
      'form',
      'maxlength',
      'name',
      'placeholder',
      'readonly',
      'required',
      'rows',
      'wrap'
    );
  }

}