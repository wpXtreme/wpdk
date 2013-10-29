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
    ?>
    <div data-type="wpdk-view"
         id="<?php echo $this->id ?>"
         class="<?php echo $this->classes() ?>" <?php echo $this->data() ?> >

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
  private function classes()
  {
    if ( !empty( $this->class ) ) {
      if ( is_string( $this->class ) ) {
        $this->class = explode( ' ', $this->class );
      }
      $this->class[] = 'wpdk-view';
      $this->class[] = 'clearfix';
      $this->class   = array_unique( $this->class );
      return join( ' ', $this->class );
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
  private function data()
  {
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
 * @date               2013-10-10
 * @version            0.9.0
 *
 */

class WPDKViewController extends WPDKObject {

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $version
   */
  public $version = '0.9.0';

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
  public function __construct( $id, $title )
  {
    $this->id       = sanitize_title( $id );
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
  public static function initWithView( $id, $title, $view )
  {
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
  public static function didHeadLoad()
  {
    /* To override */
  }

  /**
   * This static method is called when the head of this view controller is loaded by WordPress.
   * It is used by WPDKMenu for example, as 'load-' action.
   *
   * @brief Head
   */
  public static function willLoad()
  {
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
  public function html()
  {
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
  public function display()
  {
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
  public function __construct( $id, $title = '' )
  {
    parent::__construct( $id, 'clearfix wpdk-header-view' );

    /* WPDKHeaderView property. */
    $this->title = $title;
  }

  /**
   * Draw the content of this view
   *
   * @brief Draw content
   */
  public function draw()
  {
    ?>
    <div data-type="wpdk-header-view" id="<?php echo $this->id ?>" class="wpdk-vc-header-icon"></div>
    <h2><?php echo $this->title ?>
      <?php
      /* @todo Add action docs. */
      do_action( 'wpdk_header_view_' . $this->id . '_title_did_appear', $this );
      // @deprecated
      do_action( 'wpdk_header_view_title_did_appear', $this );
      ?></h2>
    <?php
    do_action( 'wpdk_header_view_' . $this->id . '_after_title', $this );
    // @deprecated
    do_action( 'wpdk_header_view_after_title', $this );
    ?>
    <?php
    parent::draw();
  }

}


/**
 * Useful view controller (tabs) for preferences
 *
 * @class           WPDKPreferencesViewController
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-08-20
 * @version         1.0.0
 *
 */
class WPDKPreferencesViewController extends WPDKjQueryTabsViewController {

  /**
   * Preferences
   *
   * @brief Preferences
   *
   * @var WPDKPreferences $preferences
   */
  private $preferences;

  /**
   * Create an instance of WPDKPreferencesViewController class
   *
   * @brief Construct
   *
   * @param WPDKPreferences $preferences    An instance of WPDKPreferences class
   * @param string          $title          The title of view controller
   * @param array           $tabs           Tabs array
   *
   * @return WPDKPreferencesViewController
   */
  public function __construct( $preferences, $title, $tabs )
  {
    $this->preferences = $preferences;
    $view = new WPDKjQueryTabsView( $preferences->name, $tabs );
    parent::__construct( $preferences->name, $title, $view );

    /* Provide a reset all button. */
    add_action( 'wpdk_header_view_' . $this->id . '-header-view_after_title', array( $this, 'display_toolbar' ) );
  }

  /**
   * Hook used to display a form toolbar preferences
   *
   * @brief Tolbar
   */
  public function display_toolbar()
  {
    $confirm = __( "Are you sure to reset All preferences to default value?\n\nThis operation is not reversible!", WPDK_TEXTDOMAIN );
    $confirm = apply_filters( 'wpdk_preferences_reset_all_confirm_message', $confirm );
    ?>
    <div class="tablenav top">
      <form id="wpdk-preferences"
            enctype="multipart/form-data"
            method="post">

        <input type="hidden"
               name="wpdk_preferences_class"
               value="<?php echo get_class( $this->preferences ) ?>" />
        <input type="file" name="file" />
        <input type="submit"
                 name="wpdk_preferences_import"
                 class="button button-primary"
                 value="<?php _e( 'Import', WPDK_TEXTDOMAIN ) ?>" />

        <input type="submit"
               name="wpdk_preferences_export"
               class="button button-secondary right"
               value="<?php _e( 'Export', WPDK_TEXTDOMAIN ) ?>" />

        <input type="submit"
               name="wpdk_preferences_reset_all"
               class="button button-primary right"
               data-confirm="<?php echo $confirm ?>"
               value="<?php _e( 'Reset All', WPDK_TEXTDOMAIN ) ?>" />

        <?php do_action( 'wpdk_preferences_view_controller-' . $this->id . '-tablenav-top', $this ) ?>
      </form>
    </div>
  <?php
  }

}

/**
 * Useful view for preferences
 *
 * @class           WPDKPreferencesView
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-08-20
 * @version         1.0.0
 *
 */
class WPDKPreferencesView extends WPDKView {

  /**
   * An instance of WPDKPreferences class
   *
   * @brief Preferences
   *
   * @var WPDKPreferences $preferences
   */
  public $preferences;

  /**
   * An instance of WPDKPreferencesBranch class
   *
   * @brief Branch
   *
   * @var WPDKPreferencesBranch $preferences
   */
  public $branch;

  /**
   * Branch property name
   *
   * @brief Branch property name
   *
   * @var string $branch_property
   */
  private $branch_property;

  /**
   * Create an instance of WPDKPreferencesView class
   *
   * @brief Construct
   *
   * @param WPDKPreferences $preferences   An instance of WPDKPreferences clas
   * @param string          $property      Preferences branch property name
   *
   * @return WPDKPreferencesView
   */
  public function __construct( $preferences, $property )
  {
    parent::__construct( 'wpdk_preferences_view-' . $property );
    $this->preferences = $preferences;
    if ( !empty( $property ) && isset( $this->preferences->$property ) ) {
      $this->branch_property = $property;
      $this->branch          = $this->preferences->$property;
    }
  }

  /**
   * Display
   *
   * @brief Display
   */
  public function draw()
  {
    /* Create a nonce key. */
    $nonce                     = md5( $this->id );
    $input_hidden_nonce        = new WPDKHTMLTagInput( '', $nonce, $nonce );
    $input_hidden_nonce->type  = WPDKHTMLTagInputType::HIDDEN;
    $input_hidden_nonce->value = wp_create_nonce( $this->id );

    $input_hidden_class        = new WPDKHTMLTagInput( '', 'wpdk_preferences_class' );
    $input_hidden_class->type  = WPDKHTMLTagInputType::HIDDEN;
    $input_hidden_class->value = get_class( $this->preferences );

    $input_hidden_branch        = new WPDKHTMLTagInput( '', 'wpdk_preferences_branch' );
    $input_hidden_branch->type  = WPDKHTMLTagInputType::HIDDEN;
    $input_hidden_branch->value = $this->branch_property;

    $layout       = new WPDKUIControlsLayout( $this->fields( $this->branch ) );
    $form         = new WPDKHTMLTagForm( $input_hidden_nonce->html() . $input_hidden_class->html() . $input_hidden_branch->html() . $layout->html() . $this->buttonsUpdateReset() );
    $form->name   = 'wpdk_preferences_view_form-' . $this->branch_property;
    $form->id     = $form->name;
    $form->class  = 'wpdk-form wpdk-preferences-view-' . $this->branch_property;
    $form->method = 'post';
    $form->action = '';

    do_action( 'wpdk_preferences_feedback-' . $this->branch_property );

    $form->display();
  }

  /**
   * Override to return the array fields
   *
   * @brief Fields
   *
   * @param WPDKPreferencesBranch $branch An instance of preferences branch
   *
   * @return array
   */
  public function fields( $branch )
  {
    die( __METHOD__ . ' must be override in your subclass' );
  }

  /**
   * Return the HTML markup for standard [Reset to default] and [Update] buttons. You can override this method to hide
   * or change the default buttons on bottom form.
   *
   * @brief Buttons Reset and Update
   *
   * @return string
   */
  public function buttonsUpdateReset()
  {
    $args         = array(
      'name'    => 'update-preferences',
    );
    $button_update = WPDKUI::button( __( 'Update', WPDK_TEXTDOMAIN ), $args );

    $confirm = __( "Are you sure to reset this preferences to default values?\n\nThis operation is not reversible!", WPDK_TEXTDOMAIN );
    $confirm = apply_filters( 'wpdk_preferences_reset_to_default_confirm_message', $confirm );

    $args         = array(
      'name'    => 'reset-to-default-preferences',
      'classes' => 'button-secondary',
      'data'    => array( 'confirm' => $confirm )
    );
    $button_reset = WPDKUI::button( __( 'Reset to default', WPDK_TEXTDOMAIN ), $args );

    return sprintf( '<p>%s%s</p>', $button_reset, $button_update );
  }

}