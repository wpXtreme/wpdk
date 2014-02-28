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
 * @deprecated         since 1.4.21
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

    _deprecated_function( __CLASS__ . '::' . __FUNCTION__, '1.5.1', 'WPDKUIAlert, WPDKUIModalDialog, ...' );
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
 * @deprecated         since 1.4.21 - use WPDKUIModalDialog
 *
 */
class WPDKTwitterBootstrapModal extends WPDKUIModalDialog {
  // Keep for backward compatibility
}


/**
 * Twitter Bootstrap buttons types
 *
 * ## Overview
 * This class enum all Twitter Bootstrap buttons types
 *
 * @class              WPDKTwitterBootstrapButtonType
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-02-28
 * @version            1.1.0
 * @deprecated         since 1.4.21
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
 * @class              WPDKTwitterBootstrapButtonSize
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-02-28
 * @version            1.1.0
 * @deprecated         since 1.4.21
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
 * @class              WPDKTwitterBootstrapButton
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-02-28
 * @version            1.1.0
 * @deprecated         since 1.4.21
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