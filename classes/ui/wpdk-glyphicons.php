<?php
/**
 * Manage the glyph icons
 *
 * @image html glyphs-icons.png "Available Glyph icons"
 *
 * @class           WPDKGlyphIcons
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-15
 * @version         1.0.3
 *
 */
final class WPDKGlyphIcons extends WPDKObject {

  const ANGLE_DOWN      = 'wpdk-icon-angle-down';
  const UPDOWN_CIRCLE   = 'wpdk-icon-updown-circle';
  const ANGLE_LEFT      = 'wpdk-icon-angle-left';
  const ANGLE_RIGHT     = 'wpdk-icon-angle-right';
  const ANGLE_UP        = 'wpdk-icon-angle-up';
  const ARROWS_CW       = 'wpdk-icon-arrows-cw';
  const ATTENTION       = 'wpdk-icon-attention';
  const BUG             = 'wpdk-icon-bug';
  const CANCEL_CIRCLED2 = 'wpdk-icon-cancel-circled2';
  const CCW             = 'wpdk-icon-ccw';
  const CHAT            = 'wpdk-icon-chat';
  const CLOCK           = 'wpdk-icon-clock-1';
  const COMMENT_EMPTY   = 'wpdk-icon-comment-empty';
  const CW              = 'wpdk-icon-cw';
  const DOWN_BIG        = 'wpdk-icon-down-big';
  const DOWN_OPEN       = 'wpdk-icon-down-open';
  const EMO_COFFEE      = 'wpdk-icon-emo-coffee';
  const EXPORT          = 'wpdk-icon-export';
  const GITHUB          = 'wpdk-icon-github';
  const HEART           = 'wpdk-icon-heart';
  const HEART_EMPTY     = 'wpdk-icon-heart-empty';
  const LEFT_OPEN       = 'wpdk-icon-left-open';
  const LOCK            = 'wpdk-icon-lock';
  const LOCK_OPEN       = 'wpdk-icon-lock-open';
  const LOCK_OPEN_ALT   = 'wpdk-icon-lock-open-alt';
  const MAIL            = 'wpdk-icon-mail';
  const MINUS_SQUARED   = 'wpdk-icon-minus-squared';
  const OK              = 'wpdk-icon-ok';
  const OK_CIRCLED      = 'wpdk-icon-ok-circled';
  const PENCIL          = 'wpdk-icon-pencil';
  const PLUS_SQUARED    = 'wpdk-icon-plus-squared';
  const RIGHT_OPEN      = 'wpdk-icon-right-open';
  const SEARCH          = 'wpdk-icon-search';
  const SPIN1           = 'wpdk-icon-spin1 animate-spin';
  const SPIN2           = 'wpdk-icon-spin2 animate-spin';
  const SPIN3           = 'wpdk-icon-spin3 animate-spin';
  const SPIN4           = 'wpdk-icon-spin4 animate-spin';
  const SPIN5           = 'wpdk-icon-spin5 animate-spin';
  const SPIN6           = 'wpdk-icon-spin6 animate-spin';
  const STAR            = 'wpdk-icon-star';
  const STAR_EMPTY      = 'wpdk-icon-star-empty';
  const STAR_HALF       = 'wpdk-icon-star-half';
  const STAR_HALF_ALT   = 'wpdk-icon-star-half-alt';
  const TRASH           = 'wpdk-icon-trash';
  const UP_OPEN         = 'wpdk-icon-up-open';

  // since 1.4.5
  const EMO_HAPPY       = 'wpdk-icon-emo-happy';
  const EMO_UNHAPPY     = 'wpdk-icon-emo-unhappy';
  const CANCEL_CIRCLED  = 'wpdk-icon-cancel-circled';
  const THUMBS_UP_ALT   = 'wpdk-icon-thumbs-up-alt';
  const THUMBS_DOWN_ALT = 'wpdk-icon-thumbs-down-alt';
  const THUMBS_UP       = 'wpdk-icon-thumbs-up';
  const THUMBS_DOWN     = 'wpdk-icon-thumbs-down';
  const COG             = 'wpdk-icon-cog';
  const UP_BIG          = 'wpdk-icon-up-big';
  const LEFT_BIG        = 'wpdk-icon-left-big';
  const RIGHT_BIG       = 'wpdk-icon-right-big';
  const OFF             = 'wpdk-icon-off';
  const FACEBOOK        = 'wpdk-icon-facebook';
  const APPLE           = 'wpdk-icon-apple';
  const TWITTER         = 'wpdk-icon-twitter';

  // since 1.4.7
  const GOOGLE_PLUS     = 'wpdk-icon-gplus';

  // since 1.4.21
  const FIREFOX         = 'wpdk-icon-firefox';
  const CHROME          = 'wpdk-icon-chrome';
  const OPERA           = 'wpdk-icon-opera';
  const IE              = 'wpdk-icon-ie';
  const TAG             = 'wpdk-icon-tag';
  const TAGS            = 'wpdk-icon-tags';
  const DOC_INV         = 'wpdk-icon-doc-inv';

  // since 1.5.0
  const HELP_CIRCLED    = 'wpdk-icon-help-circled';
  const INFO_CIRCLED    = 'wpdk-icon-info-circled';

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $__version
   */
  public $__version = '1.0.3';
    
  /**
   * Create an instance of WPDKGlyphIcons class
   *
   * @brief Construct
   *
   * @return WPDKGlyphIcons
   */
  private function __construct()
  {

  }

  /**
   * Return a singleton instance of WPDKGlyphIcons class
   *
   * @brief Singleton
   *
   * @return WPDKGlyphIcons
   */
  public static function init()
  {
    static $instance = null;
    if ( is_null( $instance ) ) {
      $instance = new WPDKGlyphIcons();
    }
    return $instance;
  }

  /**
   * Return the HTML markup for glyph icon
   *
   * @brief Glypho
   *
   * @param string $glypho Glyph icon code
   * @param string $size   Optional. Size styles, eg: 16px
   * @param string $color  Optional. Color style, eg: #c00
   * @param string $tag    Optional. HTML tag, default 'i'.
   *
   * @return string
   */
  public static function html( $glypho, $size = '', $color = '', $tag = 'i' )
  {
    $stack = array();
    $style = '';

    if ( !empty( $size ) ) {
      $stack[] = sprintf( 'font-size:%s', $size );
    }

    if ( !empty( $color ) ) {
      $stack[] = sprintf( 'color:%s', $color );
    }

    if( !empty( $stack ) ) {
      $style = sprintf( 'style="%s"', implode(';', $stack ) );
    }

    $result = sprintf( '<%s %s class="%s"></%s>', $tag, $style, $glypho, $tag );
    return $result;
  }

  /**
   * Display the HTML markup for glyph icon
   *
   * @brief Glypho
   *
   * @param string $glypho Glyph icon code
   * @param string $size   Optional. Size styles, eg: 16px
   * @param string $color  Optional. Color style, eg: #c00
   * @param string $tag    Optional. HTML tag, default 'i'.
   *
   * @return string
   */
  public static function display( $glypho, $size = '', $color = '', $tag = 'i' )
  {
    echo self::html( $glypho, $size, $color, $tag );
  }

}