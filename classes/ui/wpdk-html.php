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
 * This class is a list of constant for HTML 4.1 tag supported
 *
 * @class              WPDKHTMLTagName
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 */
class WPDKHTMLTagName {

  /* HTML 4.1 tags */
  const A        = 'a';
  const BUTTON   = 'button';
  const FIELDSET = 'fieldset';
  const FORM     = 'form';
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

  /* Input types */
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

    /* Create an WPDKHTMLTag instance. */
    parent::__construct( WPDKHTMLTagName::A );

    /* The content. */
    $this->content = $content;

    /* The href. Late binding */
    $this->href = $href;

    /* Setting. */
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
   * @var string Set to 'disabled'. Specifies that a button should be disabled.
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
  public function __construct( $content = '' ) {

    /* Create an WPDKHTMLTag instance. */
    parent::__construct( WPDKHTMLTagName::BUTTON );

    /* The content. */
    $this->content = $content;

    /* Setting. */
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
   * @var WPDKHTMLTagLegend
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

  // -----------------------------------------------------------------------------------------------------------------
  // Over-riding
  // -----------------------------------------------------------------------------------------------------------------

  public function beforeContent() {
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
  public function __construct( $content = '' ) {

    /* Create an WPDKHTMLTag instance. */
    parent::__construct( WPDKHTMLTagName::FORM );

    /* The content. */
    $this->content = $content;

    /* Setting. */
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
 *
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

    /* Create an WPDKHTMLTag instance. */
    parent::__construct( WPDKHTMLTagName::INPUT );

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
 * @date               2012-11-28
 * @version            0.8.1
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
  private $_options;

  /**
   * Create an instance of WPDKHTMLTagSelect class
   *
   * @brief Construct
   *
   * @param array|callback $options  A key value pairs array with value/text or a callback function
   * @param string         $name     Attribute name
   * @param string         $id       Attribute id
   *
   * @todo The internal method _options doesn't check for associative array
   *
   */
  public function __construct( $options = array(), $name = '', $id = '' ) {

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
  public function draw() {
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
   * @param array $options  A key value pairs array. If the value is an array then an optio group is created.
   *
   * @return string
   */
  public function options( $options ) {
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
  private function _options( $options ) {
    foreach ( $options as $key => $option ) : ?>
    <?php if ( is_array( $option ) ) : ?>
      <optgroup class="wpdk-form-optiongroup" label="<?php echo $key ?>">
        <?php $this->_options( $option ); ?>
      </optgroup>
      <?php else : ?>
      <option class="wpdk-form-option" <?php if ( !empty( $this->value ) ) wpdk_selected( $this->value, $key ) ?>
              value="<?php echo $key ?>"><?php echo $option ?></option>
      <?php endif; ?>
    <?php endforeach;
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

/**
 * Generic HTML model. This class is sub class from above class.
 * Thanks to http://www.w3schools.com/tags/default.asp for definitions
 *
 * @class              WPDKHTMLTag
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKHTMLTag {

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

  /* Global attributes. */

  public $accesskey;
  public $class;
  /**
   * HTML inner content of tag.
   *
   * @brief Content
   *
   * @var string $content
   */
  public $content;
  public $contenteditable;
  public $contextmenu;
  /**
   * Key value pairs array Attribute data: data-attribute = value
   *
   * @brief Data attribute
   *
   * @var array $data
   */
  public $data;
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

  /* Global events */
  public $title;
  /**
   * Override. List of tag attributes that can be used on any HTML element.
   *
   * @brief Attributes list
   *
   * @var array $attributes
   */
  protected $attributes;
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
  public function __construct( $tag_name ) {

    /* Store this tag name. */
    $this->tagName = $tag_name;

    /* Init the data attribute array. */
    $this->data = array();

    /* The global attributes below can be used on any HTML element. */
    $this->_globalAttributes = array(
      'accesskey',
      'class',
      'contenteditable',
      // HTML 5
      'contextmenu',
      // HTML 5
      'dir',
      'draggable',
      // HTML 5
      'dropzone',
      // HTML 5
      'hidden',
      // HTML 5
      'id',
      'lang',
      'spellcheck',
      // HTML 5
      'style',
      'tabindex',
      'title',

      'onclick'
    );
  }


  // -----------------------------------------------------------------------------------------------------------------
  // Override
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Add a data attribute
   *
   * @brief Add data attribute
   *
   * @param string $name  Data attribute name
   * @param string $value Data attribute value
   */
  public function addData( $name, $value ) {
    $this->data[$name] = $value;
  }

  /**
   * Display HTML markup for this tag
   *
   * @brief Display
   */
  public function display() {
    echo $this->html();
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Public
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return the HTML markup for this tag
   *
   * @brief Return the HTML markup for this tag
   *
   * @return string
   */
  public function html() {
    ob_start();

    /* Open. */
    echo $this->open;

    /* Cycle for tag specify attributes. */
    foreach ( $this->attributes as $attr ) {
      if ( isset( $this->$attr ) && !is_null( $this->$attr ) ) {
        printf( ' %s="%s"', $attr, htmlspecialchars( stripslashes( $this->$attr ) ) );
      }
    }

    /* Cycle for global common attributes. */
    foreach ( $this->_globalAttributes as $attr ) {
      if ( isset( $this->$attr ) && !is_null( $this->$attr ) ) {
        printf( ' %s="%s"', $attr, htmlspecialchars( stripslashes( $this->$attr ) ) );
      }
    }

    /* Generic data attribute. */
    if ( is_array( $this->data ) ) {
      foreach ( $this->data as $attr => $value ) {
        printf( ' data-%s="%s"', $attr, htmlspecialchars( stripslashes( $value ) ) );
      }
    }

    /* Content, only for enclousure TAG. */
    if ( '/>' !== $this->close ) {

      /* Close the first part tag. */
      echo '>';

      /* Before content. */
      $this->beforeContent();

      /* Content. */
      $this->draw();

      /* After content. */
      $this->afterContent();

      /* Close. */
      echo $this->close;
    }
    else {
      /* Close. */
      echo $this->close;
      echo $this->content;
    }

    $content = ob_get_contents();
    ob_end_clean();

    return $content;
  }

  /**
   * Override this method to display anything before the content output
   *
   * @brief Before content
   */
  protected function beforeContent() {
    /* to override if needed */
  }

  /**
   * Draw the content of tag
   *
   * @note You can override this method in order to customize it
   *
   * @brief Draw
   */
  public function draw() {
    echo $this->content;
  }

  /**
   * Override this method to display anything after the content output
   *
   * @brief After content
   */
  protected function afterContent() {
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
}

/// @endcond