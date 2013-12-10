<?php
/**
 * The base view class
 *
 * ## Overview
 * To create a view use
 *
 *     $view = new WPDKView( 'my-view' );
 *     $view->content = 'Hello World";
 *     $view->display();
 *
 * Or
 *
 *     $view = WPDKView::initWithContent( 'my-view', '', 'Hello World' );
 *     $view->display();
 *
 * Or, suggested method
 *
 *     class MyView extends WPDKView {
 *       public function __construct() {
 *         parent::__construct( 'my-id', 'additional class' );
 *       }
 *
 *       // Override
 *       public function draw() {
 *         echo 'Hello World';
 *       }
 *     }
 *
 * ### draw() VS content
 * If you like use a `WPDKView` directly, you will use the `content` property. Otherwise, you can sub class the `WPDKView`
 * and then use the method `draw()` to diplay the content of the view.
 *
 * ### Observing
 * You can observe some event via filters and actions. For example you can catch when a content has been drawed.
 * For fo this you can use the filter `wpdk_view_did_draw_content`:
 *
 *     add_action( 'wpdk_view_did_draw_content', array( $this, 'wpdk_view_did_draw_content') );
 *
 *     public function wpdk_view_did_draw_content( $view ) {
 *       if ( is_a( $view, 'WPDKHeaderView' ) ) {
 *           // Do something
 *       }
 *     }
 *
 * In this case we had used the "is_a" function to understand which type of view was passed. Alternatively you can check
 * the view id, for example.
 *
 *     public function wpdk_view_did_draw_content( $view ) {
 *       if ( 'my_id' == $view->id ) {
 *           // Do something
 *       }
 *     }
 *
 * @class              WPDKView
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-10-17
 * @version            1.1.0
 *
 */

class WPDKView extends WPDKObject {

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $version
   */
  public $version = '1.0.1';

  /**
   * The CSS class or list of classes
   *
   * @brief Class
   *
   * @var array $class
   */
  public $class;

  /**
   * The HTML markup content of this view
   *
   * @brief The content
   *
   * @var string $content
   */
  public $content;

  /**
   * Key value pairs array Attribute data: data-attribute = value
   *
   * @var array $data
   */
  public $data;

  /**
   * The unique id for this view
   *
   * @brief ID
   *
   * @var string $id
   */
  public $id;

  /**
   * An array list with the subviews of this view
   *
   * @brief List of sub views
   *
   * @var array $subviews
   */
  public $subviews;

  /**
   * The parent root WPDKView
   *
   * @brief The superview
   *
   * @var WPDKView $superview
   */
  public $superview;

  /**
   * An array list with views of this view controller
   *
   * @brief      List of views
   * @deprecated Since 1.0.0.b4 - Use subviews instead
   *
   * @var array $views;
   */
  protected $views;

  /**
   * Create an instance of WPDKView class
   *
   * @brief Construct
   *
   * @param string       $id    The unique id for this view
   * @param array|string $class Optional. The CSS classes for this view
   *
   * @return WPDKView
   */
  public function __construct( $id, $class = '' )
  {
    $this->id        = sanitize_title( $id );
    $this->class     = WPDKHtmlTag::sanitizeClasses( $class );
    $this->content   = '';
    $this->superview = null;
    $this->subviews  = array();
  }

  /**
   * Create an instance of WPDKView class with a content
   *
   * @brief Init a WPDKView
   *
   * @param string       $id      The unique id for this view
   * @param array|string $class   Optional. The CSS classes for this view
   * @param string       $content Optional. An HTML markup content
   *
   * @return bool|WPDKView Return an instance of WPDKView or FALSE if error
   */
  public static function initWithContent( $id, $class = '', $content = '' )
  {
    if ( !empty( $content ) ) {
      $instance          = new WPDKView( $id, $class );
      $instance->content = $content;
      return $instance;
    }
    return false;
  }

  /* @todo Experimental */
  public static function initWithFrame( $id, $rect )
  {
  }

  /**
   * Return the HTML markup content of this view
   *
   * @brief Get HTML markup content
   *
   * @return string
   */
  public function html()
  {
    ob_start();
    $this->display();
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }

  /**
   * Return the HTML markup content for this view
   *
   * @brief    The view content
   *
   * @internal WPDKView $view
   *
   * @return string
   */
  public function display()
  {
    $classes = WPDKHTMLTag::classInline( $this->class );
    $data    = WPDKHTMLTag::dataInline( $this->data );
    ?>
    <div data-type="wpdk-view"
         id="<?php echo $this->id ?>"
         class="<?php echo $classes ?>" <?php echo $data ?> >

    <?php do_action( 'wpdk_view_' . $this->id . '_before_draw', $this ) ?>

    <?php do_action( 'wpdk_view_will_draw_content', $this ) ?>
    <?php $this->draw() ?>
    <?php do_action( 'wpdk_view_did_draw_content', $this ) ?>

    <?php do_action( 'wpdk_view_' . $this->id . '_after_draw', $this ) ?>

    <?php if ( is_array( $this->subviews ) ) : ?>
      <?php
      /**
       * @var WPDKView $view
       */
      foreach ( $this->subviews as $view ) : ?>
        <?php $view->display() ?>
      <?php endforeach ?>
    <?php endif ?>

    <?php do_action( 'wpdk_view_' . $this->id . '_before_close', $this ) ?>

  </div>

  <?php
  }

  /**
   * Draw the view content
   *
   * @brief Draw
   * @note  This method can be over-ridden in a sub-class.
   */
  public function draw()
  {
    /* This method can be over-ridden in a sub-class. */
    echo $this->content;
  }


  /**
   * Add a view in queue views
   *
   * @brief Add a subview
   *
   * @param WPDKView $view
   *
   * @return WPDKView
   */
  public function addSubview( $view )
  {
    $continue = apply_filters( 'wpdk_view_should_add_subview', true, $view );
    if ( $continue ) {
      $view->superview           = $this;
      $this->subviews[$view->id] = $view;
      do_action( 'wpdk_view_did_add_subview', $view );
    }
    return $view;
  }

  /**
   * Remove this view from its superview
   *
   * @brief Remove this view
   */
  public function removeFromSuperview()
  {
    if ( !empty( $this->superview ) ) {
      unset( $this->superview->subviews[$this->id] );
    }
  }
}