<?php

/**
 * UI Helper for general purpose
 *
 * ## Overview
 * This class allow to make simple display button and common UI.
 *
 * @class              WPDKUI
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2014-02-08
 * @version            1.0.0
 *
 */
final class WPDKUI {

  // -------------------------------------------------------------------------------------------------------------------
  // Buttons
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return the HTML markup for standard [Update] and [Reset to default] button
   *
   * @brief Button Update and Reset
   */
  public static function buttonsUpdateReset()
  {
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
  public static function submit( $args = array() )
  {
    $default_args = array(
      'name'  => 'button-submit',
      'class' => 'button button-primary alignright',
      'value' => __( 'Submit', WPDK_TEXTDOMAIN )
    );

    $item = wp_parse_args( $args, $default_args );

    $item['type'] = WPDKUIControlType::SUBMIT;

    $submit = new WPDKUIControlSubmit( $item );

    return $submit->html();
  }


  /**
   * Return HTML markup for a input type
   *
   * @brief Simple button
   *
   * @param string $label Optional. Button label. If empty default is 'Update'
   * @param array  $args  Optional. A keys value array for additional settings
   *
   *     'type'                  => 'submit',
   *     'name'                  => 'button-update',
   *     'classes'               => ' button-primary',
   *     'additional_classes'    => '',
   *     'data'                  => ''
   *
   * @return string HTML input type submit
   */
  public static function button( $label = '', $args = array() )
  {

    $default_args = array(
      'type'               => 'submit',
      'name'               => 'button-update',
      'classes'            => ' button button-primary alignright',
      'additional_classes' => '',
      'data'               => array(),
    );

    $args = wp_parse_args( $args, $default_args );

    // Label
    if ( empty( $label ) ) {
      $label = __( 'Update', WPDK_TEXTDOMAIN );
    }

    // Name attribute
    if ( empty( $args['name'] ) ) {
      $name = '';
    }
    else {
      $name = sprintf( 'name="%s"', $args['name'] );
    }

    // Build data
    $data = WPDKHTMLTag::dataInline(  $args['data'] );

    // Build classes
    $classes = WPDKHTMLTag::classInline( $args['classes'], $args['additional_classes'] );

    WPDKHTML::startCompress() ?>

    <input type="<?php echo $args['type'] ?>" <?php echo $name ?>
      <?php echo $data ?>
           class="<?php echo $classes ?>"
           value="<?php echo $label ?>" />

    <?php

    return WPDKHTML::endHTMLCompress();
  }

  // -------------------------------------------------------------------------------------------------------------------
  // Badges
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * @deprecated Since 1.0.0.b3 use badge() instead
   */
  public static function badged( $count = 0, $classes = '', $tooltip = '', $placement = '' )
  {
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
  public static function badge( $count = 0, $classes = '', $tooltip = '', $placement = '' )
  {
    $classes = !empty( $classes ) ? ' ' . $classes : '';

    if ( !empty( $tooltip ) ) {
      $classes .= ' wpdk-has-tooltip';
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


  // -------------------------------------------------------------------------------------------------------------------
  // View
  // -------------------------------------------------------------------------------------------------------------------

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
  public static function view( $id, $title, $icon_class, $content )
  {
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

  // -------------------------------------------------------------------------------------------------------------------
  // Form
  // -------------------------------------------------------------------------------------------------------------------

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
  public static function inputNonce( $id )
  {
    $nonce                     = md5( $id );
    $input_hidden_nonce        = new WPDKHTMLTagInput( '', $nonce, $nonce );
    $input_hidden_nonce->type  = WPDKHTMLTagInputType::HIDDEN;
    $input_hidden_nonce->value = wp_create_nonce( $id );
    return $input_hidden_nonce->html();
  }

  /**
   * Return a new truncate well string. The result string is wrap into a special HTML markup.
   *
   * @brief Enhancer string truncate
   *
   * @note  Prototype
   *
   * @param string $value String to truncate
   * @param string $size  Number of character
   *
   * @return string
   */
  public static function labelTruncate( $value, $size = 'small' )
  {
    $html = <<< HTML
    <div class="wpdk-ui-truncate wpdk-ui-truncate-size_{$size}" title="{$value}">
        <span>{$value}</span>
    </div>
HTML;
    return $html;
  }
}