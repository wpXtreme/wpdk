<?php

/**
 * Useful view for preferences
 *
 * @class           WPDKPreferencesView
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-05-21
 * @version         1.1.0
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
     * @param string $button_update The HTML markup for button update.
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
     * @param string $button_update The HTML markup for button reset.
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

}