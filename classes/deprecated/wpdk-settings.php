<?php
/// @cond private

/**
 * @class              WPDKSettings
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               15/05/12
 * @version            0
 * @deprecated         Since 0.6.2 - Use WPDKConfiguration instead - Used yet by SmartShop
 *
 */

class WPDKSettings {

    public $entry;
    public $option_name;

    // -----------------------------------------------------------------------------------------------------------------
    // Init
    // -----------------------------------------------------------------------------------------------------------------

    function __construct( $option_name, $entry ) {
        $this->option_name = $option_name;
        $this->entry       = $entry;
    }
    
    // -----------------------------------------------------------------------------------------------------------------
    // General: get/Set Shorthand
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Legge o imposta una serie di impostazioni di primo livello
     *
     * @param string $key    ID delle impostazioni
     * @param array  $values Se impostato le impostazioni sono scritte
     *
     * @return array Impostazioni o true se impostate, false se errore
     */
    public function settings( $key, $values = null ) {
        $settings = $this->options();
        if ( !is_null( $values ) ) {
            $settings[$this->entry][$key] = $values;
            return update_option( $this->option_name, $settings );
        } else {
            return $settings[$this->entry][$key];
        }
    }

    /**
     * Legge o imposta un valore (hash) nelle impostazioni di secondo livello
     *
     * @param string $section ID della sezione: general, wp_integration, ...
     * @param string $key     ID della impostazione
     * @param mixed  $values  Se impostato cambia i settings
     *
     * @return mixed|null Restituisce il valore dell'impostazione o null se essa non esiste. In caso di impostazione del
     *             valore viene restituito il rotorno della funzione update_option()
     */
    public function setting( $section, $key, $values = null ) {
        $settings = $this->options();
        if ( !is_null( $values ) ) {
            $settings[$this->entry][$section][$key] = $values;
            return update_option( $this->option_name, $settings );
        } else {
            if ( isset( $settings[$this->entry][$section][$key] ) ) {
                return $settings[$this->entry][$section][$key];
            }
            return null;
        }
    }

    /*
    Con PHP 5+ è possibile intercettare dinamicamente chiamate ai metodi, così da emulare metodi dinamici runtime.
    Decommentanto il codice qui sotto si ottengono in automatico dei metodi virtuali chiamati con il nome della
    impostazione in settings.
    Non usato in quanto c'è un notevole loop (foreach()) da eseguire. Per ragioni di velocità è meglio l'accesso
    hash del metodo di sopra ::setting()

    public static function  __callStatic( $method, $args ) {
        $options  = $this->options();
        $settings = $options[ $this->entry ];
        foreach ( $settings as $section => $methods ) {
            foreach ( $methods as $setting => $value ) {
                if ( $method == $setting ) {
                    return $settings[$section][$method];
                }
            }
        }
    }

    */

    /**
     * Imposta una sotto sezione delle impostazioni con i valori di dafault
     *
     * @param string $key Identificativo della sotto sezione delle impostazioni
     *
     * @return bool
     */
    public function resetDefault( $key ) {
        $defaults = $this->defaultOptions();
        return $this->settings( $key, $defaults[$this->entry][$key] );
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Methods
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Get/Set an options plugin.
     *
     * @param string $setting Id/Key dell'impostazione da leggero o impostare ( in base al parametro $value). Se null vengono restituite
     *                        tutte le option sotto forma di array
     * @param null   $value   Valore da impostare
     *
     * @return mixed|bool|null Se si cercava un'impostazione e questa non viene trovata, viene restisuito il valore di
     *             default, se anche questo non viene trovato restituisce null. Se si vuole registrare un impostazione restituisce
     *             true
     */
    private function options( $setting = null, $value = null ) {
        $options = get_option( $this->option_name );

        if ( is_null( $setting ) ) {
            $optionsDefault = $this->defaultOptions();
            $result         = wp_parse_args( $options, $optionsDefault );
            return $result;
        }

        if ( is_null( $value ) ) {
            if ( isset( $options[$setting] ) ) {
                return $options[$setting];
            } else {
                $optionsDefault = $this->defaultOptions();
                if ( isset( $optionsDefault[$setting] ) ) {
                    return $optionsDefault[$setting];
                } else {
                    return null;
                }
            }
        } else {
            $options[$setting] = $value;
            update_option( $this->option_name, $options );
            return true;
        }
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Actions
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Aggiunge le opzioni alla wp_options di WordPress, se non ci sono. Usata anche durante attivazione e riattivazione
     * se vengono introdotte nuove opzioni di default, vedi aggiornamenti.
     *
     * @return void
     */
    public function init() {
        add_option( $this->option_name, $this->defaultOptions() );
        $options        = get_option( $this->option_name );
        $optionsDefault = $this->defaultOptions();
        $currentOptions = wp_parse_args( $options, $optionsDefault );
        update_option( $this->option_name, $currentOptions );
    }

    /**
     * Brute reset of the options plugin. Warning, use careful.
     *
     * @return bool False if option was not added and true if option was added.
     */
    public function optionReset() {
        delete_option( $this->option_name );
        return add_option( $this->option_name, $this->defaultOptions() );
    }

    /**
     * Elimina definitivamente un'impostazione
     *
     * @param $setting
     *
     * @return bool False if option was not added and true if option was added.
     */
    public function optionDelete( $setting ) {
        $options = get_option( $this->option_name );
        unset( $options[$setting] );
        return update_option( $this->option_name, $options );
    }

}


/**
 * Classe base ereditata dalle view di settings
 *
 * @class              WPDKSettingsView
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               14/05/12
 * @version            1.0.0
 *
 * @deprecated         Since 0.6.2 - Use WPDKConfigurationView instead - Used yet by SmartShop
 *
 */

class WPDKSettingsView {

    public $key;
    public $title;
    public $introduction;
    public $settings;

    // -----------------------------------------------------------------------------------------------------------------
    // Init
    // -----------------------------------------------------------------------------------------------------------------

    /// Construct
    function __construct( $key, $title, $settings, $introduction = '') {
        $this->key          = $key;
        $this->title        = $title;
        $this->settings     = $settings;
        $this->introduction = ( $introduction === false ) ? __( 'Please, write an introduction', WPDK_TEXTDOMAIN ) : '';
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Display
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Visualizza il form
     *
     */
    function display() {
        if ( WPDKForm::isNonceVerify( $this->key ) ) {
            $this->update();
        } ?>
    <form class="wpdk-settings-view-<?php echo $this->key ?> wpdk-form" action="" method="post">
        <?php WPDKForm::nonceWithKey( $this->key ) ?>

        <p><?php echo $this->introduction ?></p>

        <?php
            if( method_exists( $this, 'content' ) ) {
                $this->content();
            } else {
                WPDKForm::htmlForm( $this->fields() );
            }
        ?>

        <?php echo WPDKUI::buttonsUpdateReset() ?>
    </form><?php

    }

    /**
     * Restituisce il contenuto di ::display();
     *
     * @return string
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
     * Verifica se è necessario fare un aggiornamento o reset dei dati
     *
     */
    function update() {

        if ( isset( $_POST['resetToDefault'] ) ) {
            /* Reset to default */
            $result = $this->settings->resetDefault( $this->key );

            /* @todo Aggiungere filtro */
            WPDKUI::message( sprintf( __( 'The <strong>%s</strong> settings were restored to defaults values successfully!', WPDK_TEXTDOMAIN ), $this->title ), true );
        } else {
            /* Save */
            if( method_exists( $this, 'save') ) {
                $this->save();
            } else {
                /* Autosave for key. */
                if ( method_exists( $this, 'valuesForSave' ) ) {
                    $values = $this->valuesForSave();
                    $key    = $this->key;
                    $this->settings->$key( $values );
                } else {
                    WPDKUI::error( __( 'No settings update!', WPDK_TEXTDOMAIN ) );
                    return;
                }
            }

            /* @todo Aggiungere filtro */
            WPDKUI::message( sprintf( __( 'The <strong>%s</strong> settings values were updated successfully!', WPDK_TEXTDOMAIN ), $this->title ) );
        }
    }


}

/// @endcond