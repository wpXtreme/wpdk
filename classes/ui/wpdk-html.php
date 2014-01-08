<?php
/**
 * Helper class to manage HTML
 *
 * @class           WPDKHTML
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-08
 * @version         1.0.1
 * @since           1.4.0
 *
 */
class WPDKHTML extends WPDKObject {

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $__version
   */
  public $__version = '1.0.1';

  /**
   * Utility to start buffering
   *
   * @brief Start compress buffering
   */
  public static function startCompress()
  {
    ob_start();
  }

  /**
   * Display compressed output
   *
   * @brief End compressed
   */
  public static function endCSSCompress()
  {
    $css = ob_get_contents();
    ob_end_clean();

    /* Remove comments */
    $css = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css );

    /* Replace with none */
    $none = array(
      "\r\n",
      "\n",
      "\r",
      "\t",
      '  ',
      '   ',
      '    ',
    );
    $css  = str_replace( $none, '', $css );

    /* Optimized */
    $css = str_replace( array( '; ', ' ;', ';;' ) , ';', $css );
    $css = str_replace( array( ': ', ' :' ), ':', $css );
    $css = str_replace( array( '{ ', ' {' ), '{', $css );
    $css = str_replace( array( '} ', ' }', ';}' ) , '}', $css );
    $css = str_replace( array( ', ', ' ,  ' ) , ',', $css );
    $css = str_replace( array(' 0px', ':0px' ), '0', $css );
    $css = str_replace( '#000000', '#000', $css );
    $css = str_replace( array( '#ffffff', '#FFFFFF' ) , '#fff', $css );

    return trim( $css );
  }

  /**
   * Display compressed output
   *
   * @brief End compressed
   * @since 1.4.5
   */
  public static function endJavascriptCompress()
  {
    $js = ob_get_contents();
    ob_end_clean();

    /* Remove comments */
    //$content = preg_replace( '!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content );

    /* Replace with none */
    $none = array(
      "\t",
      '  ',
      '   ',
      '    ',
    );
    $js   = str_replace( $none, '', $js );

    /* Optimized */
    $js = str_replace( array( '= ', ' =' ) , '=', $js );

    /* Remove tabs, spaces, newlines, etc. */
    $content = trim( $js );

    return $content;
  }

  /**
   * Display compressed output
   *
   * @brief    End compressed
   * @note     The following params are not used yet
   *
   * @param bool $comments    Optional. Remove comments: <!-- -->
   * @param bool $conditional Optional. Removed conditional comments: <!--[! ]-->
   *
   * @return string
   */
  public static function endHTMLCompress( $comments = false, $conditional = false )
  {
    $html = ob_get_contents();
    ob_end_clean();

    /* Replace with none */
    $none = array(
      "\r\n",
      "\n",
      "\r",
      "\t"
    );
    $html = str_replace( $none, '', $html );

    /* Optimized */
    $html = str_replace( array( '  ', '   ', '    ', '     ' ) , ' ', $html );

    return trim( $html );
  }

}