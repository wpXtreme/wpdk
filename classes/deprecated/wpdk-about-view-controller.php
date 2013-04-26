<?php
/**
 * Standard view controller to manage the info of plugin. This view controller is used in a sub classes and make easy
 * display the credits/info of a plugin.
 *
 * ## Overview
 * This class make easy to display you plugin information and credits.
 *
 * ### Subclassing notes
 * To display your credits info plugin, just create your view controller and extends this class.
 * You have to provide a simple method `credits()` that return a key value pairs array like:
 *
 *     $credits = array(
 *        // Header title
 *        __( 'Developers & UI Designers', WPXCLEANFIX_TEXTDOMAIN ) => array(
 *            array(
 *                'name'  => 'John Agima (Design & Develop)',
 *                'mail'  => 'a.j.agima@wpxtre.me',
 *                'site'  => 'https://wpxtre.me',
 *            ),
 *        ),
 *
 *        // Header title
 *        __( 'Translations', WPXCLEANFIX_TEXTDOMAIN ) => array(
 *            array(
 *                'name'  => 'User Name (Turkish)',
 *                'mail'  => 'u.name@wpxtre.me',
 *                'site'  => 'https://wpxtre.me,
 *            ),
 *            array(
 *                'name'  => 'User Name (Turkish)',
 *                'mail'  => 'u.name@wpxtre.me',
 *                'site'  => 'https://wpxtre.me,
 *            ),
 *            array(
 *                'name'  => 'User Name (Turkish)',
 *                'mail'  => 'u.name@wpxtre.me',
 *                'site'  => 'https://wpxtre.me,
 *                'title' => 'Custom toolrip'
 *            ),
 *        ),
 *     );
 *
 *
 * @class              WPDKAboutViewController
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-01-23
 * @version            0.8.3
 *
 * @deprecated         Since 1.0.0.b4 - Used WPXtremeAboutViewController instead
 *
 */
class WPDKAboutViewController extends WPDKViewController {

  /**
   * An array in Contro Layout Array format with the about list.
   *
   * @brief Fields list
   *
   * @var array $fields
   */
  private $fields;

  /**
   * Create an instance of WPDKAboutViewController class
   *
   * @brief Construct
   *
   * @param WPDKWordPressPlugin $plugin
   */
  public function __construct( WPDKWordPressPlugin $plugin ) {
    $id    = sanitize_key( $plugin->name );
    $title = sprintf( '%s ver. %s ', $plugin->name, $plugin->version );
    parent::__construct( $id, $title );

    if ( method_exists( $this, 'about' ) ) {
      $this->fields = $this->fields();
    }

    if ( !empty( $this->fields ) ) {
      $content    = $this->about();
      $view_about = WPDKView::initWithContent( $id . '-view-about', 'wpdk-border-container', $content );
      $this->view->addSubview( $view_about );
    }
  }

  /**
   * Return a key value pairs array (Control Layout Array) with the list of section and people.
   *
   * @brief Fields list
   * @note You have to override this method in you subclass
   *
   * @return array
   */
  public function fields() {
    die( 'Method ' . __METHOD__ . ' must be over-ridden in a sub-class.' );
  }

  /**
   * Return the HTML markup from the credits list array
   *
   * @brief HTML markup form credits list
   *
   * @return string
   */
  private function about() {
    $html = '';
    if ( !empty( $this->fields ) ) {
      foreach ( $this->fields as $key => $value ) {
        $html .= sprintf( '<div class="wpdk-credits wpdk-credits-%s clearfix">', sanitize_title( $key ) );
        $html .= sprintf( '<h3>%s</h3>', $key );
        $html .= '<ul class="clearfix">';
        foreach ( $value as $info ) {
          $site  = isset( $info['site'] ) ? $info['site'] : '';
          $title = isset( $info['title'] ) ? $info['title'] : $site;
          $html .= sprintf( '<li class="wpdk-tooltip clearfix" title="%s" data-placement="top"><img src="http://www.gravatar.com/avatar/%s?s=32&d=wavatar" /><a target="_blank" href="%s">%s</a></li>', $title, md5( $info['mail'] ), $site, $info['name'] );
        }
        $html .= '</ul></div>';
      }
    }
    return $html;
  }

  /**
   * Return a key value pairs array with the list of section and people.
   *
   * @brief Credits list
   * @note You have to override this method in you subclass
   * @deprecated Since 1.0.0.b4
   *
   * @return array
   */
  public function credits() {
    _deprecated_function( __METHOD__, '1.0.0.b4', 'fields()' );
    return $this->fields();
  }

}