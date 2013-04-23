<?php
/// @cond private

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
 * @class              WPDKConfigView
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               03/09/12
 * @version            0.85
 *
 * @deprecated         Since 0.6.2 - Use WPDKConfigurationView instead
 *
 */

class WPDKConfigView {

    /**
     *
     * The configuration key
     *
     * @brief Property config key
     *
     * @var string $key
     */
    public $key;

    /**
     * The configuration title
     *
     * @brief Title
     *
     * @var string $title
     */
    public $title;

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
     * This property is usualy override from subclass class. In this way each class can store its branch.
     *
     * @brief Entry point in configuration model
     *
     * @var WPDKConfigBranch $config
     */
    public $config;

    /**
     * Create WPDKConfigView instance object
     *
     * @brief Construct
     *
     * @param WPDKConfig|WPDKConfigBranch $config       Main or branch config pointer
     * @param string                      $key          ID key of config
     * @param string                      $title        Title of config view
     * @param string                      $introduction (Optional) an introduction text message
     */
    function __construct( $config, $key, $title, $introduction = '') {
        $this->config       = $config;
        $this->key          = $key;
        $this->title        = $title;
        $this->introduction = $introduction;
    }

    /**
     * Return the introduction string, if exists, for HTML output
     *
     * @brief Introduction in section
     *
     * @return string
     */
    private function introduction() {
        if ( !empty( $this->introduction ) ) {
            return sprintf( '<p>%s</p>', $this->introduction );
        }
        return $this->introduction;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Display
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Display the content view with form, introduction, fields or custom content. If you set a custom content the
     * default button [Update] and [Reset to default] doesn't not display.
     *
     * @brief Display the content view form
     *
     */
    function display() {

        /* Check if update */
        $this->update();

        if ( !method_exists( $this, 'content' ) ) : ?>
        <form name="wpdk_config_view_form-<?php echo $this->key ?>"
              class="wpdk-settings-view-<?php echo $this->key ?> wpdk-form"
              action=""
              method="post">
        <?php endif; ?>

        <?php WPDKForm::nonceWithKey( $this->key ) ?>

        <?php echo $this->introduction() ?>

        <?php
        /* Se esiste un metodo content() allora usa quello, altrimenti si aspetta dei fields in SDF */
        if ( method_exists( $this, 'content' ) ) {
            $this->content();
        }
        else {
            WPDKForm::htmlForm( $this->fields() );
            echo WPDKUI::buttonsUpdateReset();
        }
        ?>

        <?php if ( !method_exists( $this, 'content' ) ) : ?>
        </form>
        <?php endif; ?>

    <?php
    }

    /**
     * Return the HTML markup
     *
     * @brief Return the output of display() method.
     *
     * @return string The output of display() method
     */
    function html() {
        ob_start();
        $this->display();
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Database/Options
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Update or reset configuration
     *
     * @brief Update sequence
     *
     */
    function update() {

        /* Restrict one update for times */
        static $updated = false;

        if ( false === $updated ) {
            if ( WPDKForm::isNonceVerify( $this->key ) ) {

                if ( isset( $_POST['resetToDefault'] ) ) {

                    if ( !method_exists( $this, 'resetToDefault' ) && !method_exists( $this->config, 'defaults' ) ) {
                        /* @todo Aggiungere filtro */
                        $message = __( 'No Reset to default settings implement yet!', WPDK_TEXTDOMAIN );
                        $alert   = new WPDKTwitterBootstrapAlert( 'no-reset-default', $message, WPDKTwitterBootstrapAlertType::ALERT );
                        $alert->display();
                    }
                    else {
                        if ( method_exists( $this->config, 'defaults' ) ) {
                            $this->config->defaults();
                            WPXtremeConfiguration::init()->update();
                        }
                        else {
                            $this->resetToDefault();
                        }
                        // Whichever the result is, I have called default
                        $updated = true;
                        /* @todo Aggiungere filtro */
                        $message = sprintf( __( 'The <strong>%s</strong> settings were restored to defaults values successfully!', WPDK_TEXTDOMAIN ), $this->title );
                        $alert   = new WPDKTwitterBootstrapAlert( 'success', $message, WPDKTwitterBootstrapAlertType::SUCCESS );
                        $alert->display();
                    }
                }
                else {
                    /* Save */
                    if ( method_exists( $this, 'save' ) ) {
                        $bStatus = $this->save();
                        // Whichever the result is, I have called save
                        $updated = true;
                        if ( $bStatus ) {
                            /* @todo Aggiungere filtro */
                            $message = sprintf( __( 'The <strong>%s</strong> settings values were updated successfully!', WPDK_TEXTDOMAIN ), $this->title );
                            $alert   = new WPDKTwitterBootstrapAlert( 'success', $message, WPDKTwitterBootstrapAlertType::SUCCESS );
                            $alert->display();
                        }
                    }
                    else {
                        $message = __( 'No settings update!', WPDK_TEXTDOMAIN );
                        $alert   = new WPDKTwitterBootstrapAlert( 'no-settings-update', $message, WPDKTwitterBootstrapAlertType::ALERT );
                        $alert->display();
                    }
                }
            }
        }
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Override
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return a SDF array for build the form fields
     *
     * @brief Return a SDF array for build the form fields
     *
     * @note you have to override this method
     *
     */
    function fields() {
        /* To override when the content() method is not implement */
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Override (optional)
    // -----------------------------------------------------------------------------------------------------------------

    // Implement the code below to return a SDF array for build the form fields. Use this method when content() is not
    // implement.
    /*
    function fields() {
        // To override when the content() method is not implement
    }
    */

    // Implement the code below to display a custom content for this config view.
    // If exists overwrite the fields() method.
    /*
    function content() {

    }
     */

    // Implement the code below to save the post data
    /*
    function save() {
        // Return TRUE to display the standard sucessfully message, or FALSE to custom display.
        return true;
    }
    */

}

/// @endcond