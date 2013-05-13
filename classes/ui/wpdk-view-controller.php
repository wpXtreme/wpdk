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
 * Or
 *
 *     class MyView extends WPDKView {
 *         function __construct( $id, $class = '' ) {
 *         }
 *
 *         // Override
 *         function draw() {
 *             echo 'Hello World';
 *         }
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
 *     function wpdk_view_did_draw_content( $view ) {
 *         if ( is_a( $view, 'WPDKHeaderView' ) ) {
 *             // Do something
 *         }
 *     }
 *
 * In this case we had used the "is_a" function to understand which type of view was passed. Alternatively you can check
 * the view id, for example.
 *
 *     function wpdk_view_did_draw_content( $view ) {
 *         if ( 'my_id' == $view->id ) {
 *             // Do something
 *         }
 *     }
 *
 *
 * @class              WPDKView
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */

class WPDKView {

  /**
   * The CSS class or list of classes
   *
   * @brief Class
   *
   * @var string|array $class
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
   * @brief List of views
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
   * @param array|string $class The CSS classes for this view
   *
   * @return WPDKView
   */
  public function __construct( $id, $class = '' ) {
    $this->id        = sanitize_key( $id );
    $this->class     = $class;
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
  public static function initWithContent( $id, $class = '', $content = '' ) {
    if ( !empty( $content ) ) {
      $instance          = new WPDKView( $id, $class );
      $instance->content = $content;
      return $instance;
    }
    return false;
  }

  /* @todo Experimental */
  public static function initWithFrame( $id, $rect ) {}

  /**
   * Return the HTML markup content of this view
   *
   * @brief Get HTML markup content
   *
   * @return string
   */
  public function html() {
    ob_start();
    $this->display();
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }

  /**
   * Return the HTML markup content for this view
   *
   * @brief The view content
   *
   * @internal WPDKView $view
   *
   * @return string
   */
  public function display() {
    ?>
  <div data-type="wpdk-view"
       id="<?php echo $this->id ?>"
       class="wpdk-view <?php echo $this->classes() ?> clearfix" <?php echo $this->data() ?> >

    <?php do_action( 'wpdk_view_' . $this->id . '_before_draw', $this ) ?>

    <?php do_action( 'wpdk_view_will_draw_content', $this ) ?>
    <?php $this->draw() ?>
    <?php do_action( 'wpdk_view_did_draw_content', $this ) ?>

    <?php do_action( 'wpdk_view_' . $this->id . '_after_draw', $this ) ?>

    <?php if ( is_array( $this->subviews ) ) : ?>
    <?php foreach ( $this->subviews as $view ) : ?>
      <?php $view->display() ?>
      <?php endforeach ?>
    <?php endif ?>

    <?php do_action( 'wpdk_view_' . $this->id . '_before_close', $this ) ?>

  </div>

  <?php
  }

  /**
   * Return the computed CSS class or classes space separated
   *
   * @brief Return the computed CSS class
   *
   * @return string
   */
  private function classes() {
    if ( !empty( $this->class ) ) {
      if ( is_string( $this->class ) ) {
        return $this->class;
      }
      elseif ( is_array( $this->class ) ) {
        return join( ' ', $this->class );
      }
    }
    return '';
  }

  /**
   * Return the computed data attribute
   *
   * @brief Return the computed data attribute
   *
   * @return string
   */
  private function data() {
    $result = '';
    if ( !empty( $this->data ) ) {
      foreach ( $this->data as $attr => $value ) {
        $result .= sprintf( ' data-%s="%s"', $attr, htmlspecialchars( stripslashes( $value ) ) );
      }
    }
    return $result;
  }

  /**
   * Draw the view content
   *
   * @brief Draw
   * @note This method can be over-ridden in a sub-class.
   */
  public function draw() {
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
  public function addSubview( $view ) {
    $continue = apply_filters( 'wpdk_view_should_add_subview', true, $view );
    if ( $continue ) {
      $view->superview        = $this;
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
  public function removeFromSuperview() {
    if ( !empty( $this->superview ) ) {
      unset( $this->superview->subviews[$this->id] );
    }
  }
}

/**
 * The WPDKViewCntroller is the main view likely WordPress
 *
 * ## Overview
 * A view controller to allow to manage a standard WordPress view. A standard view is:
 *
 * [ header with icon and title - optional button add]
 * [single or more view]
 *
 * ### Subclassing notes
 *
 *
 * @class              WPDKViewController
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */

class WPDKViewController {

  /**
   * The unique id for this view controller
   *
   * @brief ID
   *
   * @var string $id
   */
  public $id;
  /**
   * The title of this view controller. This is displayed on top header
   *
   * @brief Title
   *
   * @var string $title
   */
  public $title;
  /**
   * The view stored in this property represents the root view for the view controller hierarchy.
   *
   * @brief The root view
   *
   * @var WPDKView $view
   */
  public $view;
  /**
   * An instance of WPDKHeaderView
   *
   * @brief The header view
   *
   * @var WPDKHeaderView $viewHead
   */
  public $viewHead;

  /**
   * Create an instance of WPDKViewController class
   *
   * @brief Construct
   *
   * @param string $id         The unique id for this view controller
   * @param string $title      The title of this view controller. This is displayed on top header
   *
   * @return WPDKViewController
   */
  public function __construct( $id, $title ) {
    $this->id       = sanitize_key( $id );
    $this->title    = $title;
    $this->view     = new WPDKView( $id . '-view-root', array( 'wrap' ) );
    $this->viewHead = new WPDKHeaderView( $id . '-header-view', $this->title );

    $this->view->addSubview( $this->viewHead );
  }

  /**
   * Return an instance of WPDKViewController class. This static method create a view controller with a view.
   *
   * @brief Init a view controller with a view
   *
   * @param string   $id         The unique id for this view controller
   * @param string   $title      The title of this view controller. This is displayed on top header
   * @param WPDKView $view       A instance of WPDKView class. This will be a subview.
   *
   * @return bool|WPDKViewController The view controller or FALSE if error
   */
  public static function initWithView( $id, $title, $view ) {
    if ( !is_object( $view ) || !is_a( $view, 'WPDKView' ) ) {
      return false;
    }
    else {
      $instance = new WPDKViewController( $id, $title );
      $instance->view->addSubview( $view );
    }
    return $instance;
  }

  /**
   * This static method is called when the head of this view controller is loaded by WordPress.
   * It is used by WPDKMenu for example, as 'admin_head-' action.
   *
   * @brief Head
   */
  public static function didHeadLoad() {
    /* To override */
  }

  /**
   * This static method is called when the head of this view controller is loaded by WordPress.
   * It is used by WPDKMenu for example, as 'load-' action.
   *
   * @brief Head
   */
  public static function willLoad() {
    /* To override */
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Display
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return the HTML markup content of this view
   *
   * @brief Get HTML markup content
   *
   * @return string
   */
  public function html() {
    ob_start();
    $this->display();
    $content = ob_get_contents();
    ob_end_clean();
    return $content;
  }

  /**
   * Display the content of this view controller
   *
   * @brief Display the view controller
   */
  public function display() {
    ?>

  <?php do_action( $this->id . '_will_view_appear', $this ) ?>

  <?php do_action( 'wpdk_view_controller_will_view_appear', $this->view, $this ); // @deprecated ?>

  <?php $this->view->display() ?>

  <?php do_action( 'wpdk_view_controller_did_view_appear', $this->view, $this ); // @deprecated ?>

  <?php do_action( $this->id . '_did_view_appear', $this ) ?>

  <?php
  }
}

/**
 * Standard header view
 *
 * @class              WPDKHeaderView
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-02-21
 * @version            0.8.2
 *
 */
class WPDKHeaderView extends WPDKView {

  /**
   * The title of this view.
   *
   * @brief Title
   *
   * @var string $title
   */
  public $title;

  /**
   * Create an instance of WPDKHeaderView class
   *
   * @brief Construct
   *
   * @param string $id    The unique id for this view
   * @param string $title The title of this view controller. This is displayed on top header
   *
   * @return WPDKHeaderView
   */
  public function __construct( $id, $title = '' ) {
    parent::__construct( $id, 'clearfix wpdk-header-view' );

    /* WPDKHeaderView property. */
    $this->title = $title;
  }

  /**
   * Draw the content of this view
   *
   * @brief Draw content
   */
  public function draw() {
    ?>
  <div data-type="wpdk-header-view" id="<?php echo $this->id ?>" class="wpdk-vc-header-icon"></div>
  <h2><?php echo $this->title ?>
    <?php
    /* @todo Add action docs. */
    do_action( 'wpdk_header_view_' . $this->id . '_title_did_appear', $this );
    // @deprecated
    do_action( 'wpdk_header_view_title_did_appear', $this );
    ?></h2>
  <div class="wpdk-vc-header-after-title">
    <?php
    do_action( 'wpdk_header_view_' . $this->id . '_after_title', $this );
    // @deprecated
    do_action( 'wpdk_header_view_after_title', $this );
    ?>
  </div>
  <?php
    parent::draw();
  }

}


/**
 * Configuration view
 *
 * ## Overview
 *
 * You can subclass this class to build an easy view config. This class help you to render a standard view for the
 * configuration panel. It create for you the wrap HTML content, dispplay the form fields and update or reset the
 * data. In addition display the message for feedback.
 *
 * ### Implement method
 *
 * You can implement some standard method for manage this view
 *
 * * fields() This method return an SDF array to display the form fields
 * * content() This method is used for custom view control. Overwrite the fields()
 * * save() Your own custom save data. If this method id not implement your data aren't saved.
 *
 * @class              WPDKConfigurationView
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 */

class WPDKConfigurationView extends WPDKView {

  /**
   *
   * This is an optional string information
   *
   * @brief Top most introduction
   *
   * @var string $introduction
   */
  public $introduction;

  /**
   * The configuration title
   *
   * @brief Title
   *
   * @var string $title
   */
  public $title;

  /**
   * This property is usualy override from subclass class. In this way each class can store its branch.
   *
   * @brief Entry point in configuration model
   *
   * @var WPDKConfiguration $_configuration
   */
  private $_configuration;

  /**
   * This is a pointer to your own sun configuration branch. This can be a object, array or anything else.
   * Will be your job manage this pointer.
   *
   * @brief Sub configuration brach
   *
   * @var array|null|object $_subConfiguration
   */
  private $_subConfiguration;

  /**
   * Create WPDKConfigurationView instance object
   *
   * @brief Construct
   *
   * @param string                      $id                ID key of configuration
   * @param string                      $title             Title of configuration view
   * @param WPDKConfiguration           $configuration     Optional. Main configuration pointer
   * @param object|array                $sub_configuration Optional. Sub configuration
   * @param string                      $introduction      Optional. An introduction text message
   *
   * @return WPDKConfigurationView
   */
  public function __construct( $id, $title, $configuration = null, $sub_configuration = null, $introduction = '' ) {
    $this->id                = sanitize_key( $id );
    $this->title             = $title;
    $this->_configuration    = $configuration;
    $this->_subConfiguration = $sub_configuration;
    $this->introduction      = $introduction;

    /* Check if update */
    $this->_processPost();
  }

  /**
   * Update or reset configuration
   *
   * @brief Update sequence
   *
   */
  private function _processPost() {

    $nonce = md5( $this->id );

    if ( isset( $_POST[$nonce] ) && wp_verify_nonce( $_POST[$nonce], $this->id ) ) {

      /* Reset to default? */
      if ( isset( $_POST['resetToDefault'] ) ) {
        $this->resetToDefault();
      }

      /* Update */
      else {
        $bStatus = $this->updatePostData();
        if ( $bStatus ) {
          if ( !is_null( $this->_configuration ) ) {
            $this->_configuration->update();
          }
          add_action( 'wpdk_header_view_after_title', array( $this, 'wpdk_header_view_after_title' ) );
        }
      }
    }
  }

  /**
   * Return the introduction string, if exists, for HTML output
   *
   * @brief Introduction in section
   *
   * @return string
   */
  private function _introduction() {
    if ( !empty( $this->introduction ) ) {
      $alert                = new WPDKTwitterBootstrapAlert( 'introduction', $this->introduction, WPDKTwitterBootstrapAlertType::INFORMATION );
      $alert->dismissButton = false;
      $alert->block         = true;
      return $alert->html();
    }
    return $this->introduction;
  }

  /**
   * Reset to default values of sub configuration branch. You can override this method if your sub configuration
   * branch is not an object or not implements a `defaults()` method.
   *
   * @note You can override this method
   *
   * @brief Reset to default
   */
  public function resetToDefault() {
    /* Check if sub configuration is set and if it implements the standard defaults() method. */
    if ( is_object( $this->_subConfiguration ) && method_exists( $this->_subConfiguration, 'resetToDefault' ) ) {
      $this->_subConfiguration->resetToDefault();
      if ( !is_null( $this->_configuration ) ) {
        $this->_configuration->update();
      }

      /* Display message. */
      $message = sprintf( __( '<strong>%s</strong> settings were successfully restored to defaults values!', WPDK_TEXTDOMAIN ), $this->title );
      $alert   = new WPDKTwitterBootstrapAlert( 'success', $message, WPDKTwitterBootstrapAlertType::SUCCESS );
      $alert->display();
    }

    /* @todo In extreme case we could use: */
    /*
    elseif( is_object( $this->_subConfiguration ) ) {
        $class_name = get_class( $this->_subConfiguration );
        $this->_subConfiguration = new $class_name;
        $this->_configuration->update();
    }
    */

    else {
      $message = __( 'No Reset to default settings implement yet!', WPDK_TEXTDOMAIN );
      $alert   = new WPDKTwitterBootstrapAlert( 'no-reset-default', $message, WPDKTwitterBootstrapAlertType::ALERT );
      $alert->display();
    }
  }

  /**
   * Display the content view with form, introduction, fields or custom content. You can override this method with your
   * own drawing.
   *
   * @brief Display the content view form
   *
   */
  public function draw() {

    /* Create a nonce key. */
    $nonce                     = md5( $this->id );
    $input_hidden_nonce        = new WPDKHTMLTagInput( '', $nonce, $nonce );
    $input_hidden_nonce->type  = WPDKHTMLTagInputType::HIDDEN;
    $input_hidden_nonce->value = wp_create_nonce( $this->id );

    $layout       = new WPDKUIControlsLayout( $this->fields() );
    $form         = new WPDKHTMLTagForm(
      $input_hidden_nonce->html() . $this->_introduction() . $layout->html() . $this->buttonsUpdateReset() );
    $form->name   = 'wpdk_configuration_view_form-' . $this->id;
    $form->id     = $form->name;
    $form->class  = 'wpdk-form wpdk-configuration-view-' . $this->id;
    $form->method = 'post';
    $form->action = '';
    $form->display();
  }

  /**
   * Process and set you own post data
   *
   * @brief Update configuration
   *
   * @note You can override this method
   *
   * @return bool TRUE to update the configuration and display the standard sucessfully message, or FALSE to avoid
   * the update configuration and do a custom display.
   */
  public function updatePostData() {
    return true;
  }

  /**
   * Display succefully configuration updated message
   *
   * @brief Action close to after title
   *
   * @param WPDKHeaderView $header_view
   */
  public function wpdk_header_view_after_title( $header_view ) {
    $message = sprintf( __( '<strong>%s</strong> settings values were successfully updated!', WPDK_TEXTDOMAIN ), $this->title );
    $alert   = new WPDKTwitterBootstrapAlert( 'success', $message, WPDKTwitterBootstrapAlertType::SUCCESS );
    $alert->display();
  }

  /**
   * Return the HTML markup for standard [Reset to default] and [Update] buttons. You can override this method to hide
   * or change the default buttons on bottom form.
   *
   * @brief Buttons Reset and Update
   * @since 1.0.0.b3
   * @note You can overide this method
   *
   * @return string
   */
  public function buttonsUpdateReset() {
    return WPDKUI::buttonsUpdateReset();
  }

  /**
   * Return a SDF array for build the form fields
   *
   * @brief Return a SDF array for build the form fields
   *
   * @note You can override this method
   *
   */
  public function fields() {
    /* To override */
    return array();
  }
}