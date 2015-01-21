<?php

/**
 * Useful view for preferences
 *
 * @class           WPDKPreferencesView
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-12-02
 * @version         1.2.0
 *
 * @history         1.1.0 - Added utility method `buttonPage()`
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
   * @param WPDKPreferences $preferences An instance of WPDKPreferences clas
   * @param string          $property    Preferences branch property name
   *
   * @return WPDKPreferencesView
   */
  public function __construct( $preferences, $property )
  {
    parent::__construct( 'wpdk_preferences_view-' . $property );

    // Strore preferences
    $this->preferences = $preferences;

    // Save the branch if exists
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
    // Create a nonce key
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

    $layout        = new WPDKUIControlsLayout( $this->fields( $this->branch ) );
    $form          = new WPDKHTMLTagForm( $input_hidden_nonce->html() . $input_hidden_class->html() . $input_hidden_branch->html() . $layout->html() . $this->buttonsUpdateReset() );
    $form->name    = 'wpdk_preferences_view_form-' . $this->branch_property;
    $form->id      = $form->name;
    $form->class[] = 'wpdk-form wpdk-preferences-view-' . $this->branch_property;
    $form->method  = 'post';
    $form->action  = '';

    /**
     * Filter the form object for this branch view.
     *
     * @param WPDKHTMLTagForm $form An instance of WPDKHTMLTagForm class.
     */
    $form = apply_filters( 'wpdk_preferences_branch_form', $form );

    /**
     * Fires before display the view. You can add your custome feedback message.
     */
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
    $args          = array(
      'name' => 'update-preferences',
    );
    $button_update = WPDKUI::button( __( 'Update', WPDK_TEXTDOMAIN ), $args );

    /**
     * Filter the HTML markup for update button.
     *
     * The dynamic portion of the hook name, $branch, refers to the branch property name.
     *
     * @param string $branch The HTML markup for button update.
     */
    $button_update = apply_filters( 'wpdk_preferences_button_update-' . $this->branch_property, $button_update );

    // Confirm message for reset to defaul
    $confirm = __( "Are you sure to reset this preferences to default values?\n\nThis operation is not reversible!", WPDK_TEXTDOMAIN );

    /**
     * Filter the reset to default confirm message.
     *
     * @param string $confirm The reset to default confirm message.
     */
    $confirm = apply_filters( 'wpdk_preferences_reset_to_default_confirm_message', $confirm );

    $args         = array(
      'name'    => 'reset-to-default-preferences',
      'classes' => 'button-secondary',
      'data'    => array( 'confirm' => $confirm )
    );
    $button_reset = WPDKUI::button( __( 'Reset to default', WPDK_TEXTDOMAIN ), $args );

    /**
     * Filter the HTML markup for reset button.
     *
     * The dynamic portion of the hook name, $branch, refers to the branch property name.
     *
     * @param string $branch The HTML markup for button reset.
     */
    $button_reset = apply_filters( 'wpdk_preferences_button_reset-' . $this->branch_property, $button_reset );

    /**
     * Filter the array with bottom buttons.
     *
     * @param array $buttons List of bottom buttons.
     */
    $buttons = apply_filters( 'wpdk_preferences_buttons-' . $this->branch_property, array( $button_reset, $button_update ) );

    // Avoid the paragraph
    if( empty( $buttons ) ) {
      return;
    }

    return sprintf( '<p>%s</p>', implode( '', $buttons ) );
  }

  // -------------------------------------------------------------------------------------------------------------------
  // UTILITIES
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return the HTML markup for a button Create/Edit for a post of type page.
   *
   * @param string $slug         The page slug from preference. May be empty.
   * @param string $referrer     Optional. A string used as get params whe edit post in order to display a button
   *                             "Back to...". If youo set this params you have to manage the relative hook in edit
   *                             post.
   * @param string $post_type    Optional. Post type. Default 'page'.
   * @param string $combo_select Optional. ID of select combo from which get the selected post type. Default empty.
   *
   * @return string
   */
  public function buttonPage( $slug, $referrer = '', $post_type = 'page', $combo_select = '' )
  {
    // Create or Edit?
    if( empty( $slug ) ) {
      $url        = admin_url( 'post-new.php' );
      $label      = __( 'Create new' );
      $query_args = array( 'post_type' => $post_type );
    }
    else {
      $page = get_page_by_path( $slug, OBJECT, $post_type );

      // Stability
      if( is_null( $page ) ) {
        return;
      }
      $url        = admin_url( 'post.php' );
      $label      = __( 'Edit' );
      $query_args = array( 'post' => $page->ID, 'action' => 'edit' );
    }

    // Added referrer
    if( !empty( $referrer ) && !empty( $_REQUEST[ 'page' ] ) ) {
      $query_args[ $referrer ] = $_REQUEST[ 'page' ];
    }

    // Complete url
    $url = add_query_arg( $query_args, $url );

    // Defaul result
    $result = sprintf( '<a class="button" href="%s">%s</a>', $url, $label );

    // Combo select for dynamic post type
    if( !empty( $combo_select ) && empty( $slug ) ) {
      $url = remove_query_arg( 'post_type', $url );
      $result = sprintf( '<a data-post_type="#%s" data-url="%s" class="button wpdk-preferences-create-edit-post-button" href="#">%s</a>', ltrim( $combo_select, '#' ), $url, $label );
    }

    return $result;

  }

}