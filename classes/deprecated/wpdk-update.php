<?php
/// @cond private

/*
 * [DRAFT]
 *
 * THE FOLLOWING CODE IS A DRAFT. FEEL FREE TO USE IT TO MAKE SOME EXPERIMENTS, BUT DO NOT USE IT IN ANY CASE IN
 * PRODUCTION ENVIRONMENT. ALL CLASSES AND RELATIVE METHODS BELOW CAN CHNAGE IN THE FUTURE RELEASES.
 *
 */

/**
 * Manage update from wpXtreme plugin repository: WPX Store
 *
 * ## Overview
 *
 * @class              WPDKUpdate
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 * @deprecated         Since 1.0.0.b4 - Use WPX version
 *
 */

class WPDKUpdate {

    /**
     * The plugin basename from main plugin filename, ie. wpx-cleanfix/wpx-cleanfix.php
     *
     * @brief The plugin basename
     *
     * @var string $_plugin_slug
     */
    private $_plugin_slug;


    /**
     * Instance of base class WPDKAPI
     *
     * @brief API
     *
     * @var WPDKAPI $api
     */
    private $api;

    /**
     * Create an instance of WPDKUpdate class
     *
     * @brief Construct
     *
     * @param string $file Plugin filename
     *
     * @return WPDKUpdate
     */
    public function __construct( $file ) {

        $this->_plugin_slug = plugin_basename( $file );

        /* Instance of own API. */
        $this->api = new WPDKAPI( 'wpdk' );

        /* Alternate checking repository */
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'pre_set_site_transient_update_plugins' ) );

        /* Check For Plugin Information */
        add_filter( 'plugins_api', array( $this, 'plugins_api' ), 10, 3 );
        add_action( 'in_plugin_update_message-' . $this->_plugin_slug, array( $this, 'in_plugin_update_message'), 10, 2 );

    }

    // -----------------------------------------------------------------------------------------------------------------
    // WordPress hooks: own checking repositiry
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Questo hook filter non fa parte propriamente dell'engine di aggiornamento dei plugin. I filtri di questo tipo
     * fanno parte delle transient. Questo in particolare è costruito dal filtro 'pre_set_transient_' . $transient e
     * chiamato dalla set_transient(). In definitiva serve per alterare la lista dei plugin da aggiornare che WordPress
     * memorizza nelle option ( tramite appunto transient ) una volta al giorno.
     *
     * @note Per questo motivo è stato introdotto un delete_option( '_site_transient_update_plugins' ); in
     *       WPXtremeAPI::plugstore()
     *
     * @note Questa viene utilizzata singolarmente da ogni plugin installato. Vedi infatti il parametro 'plugin_name'
     *       negli $args che viene valorizzato con $this->_plugin_slug. Ricordo che la classe WPDKUpdate viene
     *       utilizzata come istanza in ogni bootstrap dei nostri plugin, che altrimenti non verrebbero mai aggiornati
     *       dallo store.
     *
     * @brief WordPress filter when fetch the plugin list
     *
     * @uses WPXtremeAPI::updatePlugins()
     *
     * @param object $transient Elenco dei plugin da aggiornare:
     *
     *     object(stdClass)#272 (3) {
     *       ["last_checked"]=>     int(1342125406)
     *       ["checked"]=>          array(7) {
     *         ["akismet/akismet.php"]=>        string(5) "2.5.6"
     *         ["members/members.php"]=>        string(3) "0.2"
     *         ["wpx-cleanfix/main.php"]=>      string(3) "1.0"
     *         ["wpx-sample/main.php"]=>        string(3) "1.0"
     *         ["wpx-smartshop/main.php"]=>     string(3) "1.0"
     *         ["wpxtreme/main.php"]=>          string(3) "1.0"
     *         ["wpxtreme-server/main.php"]=>   string(3) "1.0"
     *       }
     *       ["response"]=> array(0) { }
     *     }
     *
     *
     * @return object
     *
     */
    public function pre_set_site_transient_update_plugins( $transient ) {

        /* Only backend administration */
        if( !is_admin() ) {
            return $transient;
        }

        /* Check if the transient contains the 'checked' information If no, just return its value without hacking it */
        if ( empty( $transient->checked ) ) {
            return $transient;
        }

        /* The transient contains the 'checked' information Now append to it information form your own API */
        $args = array(
            'action'      => 'update-check',
            'plugin_name' => $this->_plugin_slug,
            'version'     => $transient->checked[$this->_plugin_slug]
        );

        $response = $this->api->updatePlugins( $args );

        /* If response is false, don't alter the transient */
        if ( false !== $response ) {
            $transient->response[$this->_plugin_slug] = $response;
        }

        return $transient;
    }

    /**
     * Hook filter dell'omonima funzione WordPress.
     *
     * @brief WordPress filters
     *
     * @param bool   $false
     * @param string $action Identificativo del comando da eseguire, Eg. 'plugin_information'
     * @param array  $args   Arguments to serialize for the Plugin Info API.
     *
     * @uses WPXtremeAPI::plugin_information()
     *
     * @return bool|mixed
     */
    public function plugins_api( $false, $action, $args ) {

        /* Check if $args is valid. */
        if ( !isset( $args->slug ) ) {
            return false;
        }

        $transient = get_site_transient( 'update_plugins' );

        /* Check if this plugins API is about this plugin */
        if ( $args->slug != $this->_plugin_slug ) {
            return $false;
        }

        /* POST data to send to your API */
        $args = array(
            'action'      => 'plugin_information',
            'plugin_name' => $this->_plugin_slug,
            'version'     => $transient->checked[$this->_plugin_slug]
        );

        /* Send request for detailed information */
        $response = $this->api->pluginInformation( $args );

        return $response;
    }

    /**
     * Questa action viene chiamata quando WordPress costruisce la riga sulla tabella della lista dei plugin e segnala
     * un aggiornamento. Vedi cmq la action orginale nella forma:
     *
     *     do_action( "in_plugin_update_message-$file", $plugin_data, $r );
     *
     * Questa contiene tutte le informazioni utili relative all'aggiornamento, compreso l'url dal quale scaricare lo
     * zip. Dove è $file è tipo "wpxtreme/main.php", questo è sempre lo slug del plugin; cartella/file pricipale.
     *
     * @param array  $plugin_data Queste sono le informazioni sul plugin che bisogna aggiornare. Sono del tutto simili
     *                            alle informazioni inserite come commento nel file principlae del plugin.
     *
     *     array(12) {
     *       ["Name"]=>         string(9) "wpxSample"
     *       ["PluginURI"]=>    string(17) "https://wpxtre.me/"
     *       ["Version"]=>      string(3) "1.0.0"
     *       ["Description"]=>  string(13) "Sample Plugin"
     *       ["Author"]=>       string(8) "wpXtreme"
     *       ["AuthorURI"]=>    string(16) "https://wpxtre.me"
     *       ["TextDomain"]=>   string(0) ""
     *       ["DomainPath"]=>   string(0) ""
     *       ["Network"]=>      bool(false)
     *       ["Title"]=>        string(9) "wpxSample"
     *       ["AuthorName"]=>   string(8) "wpXtreme"
     *       ["update"]=>       bool(true)
     *     }
     *
     * @param object $r Oggetto con le informazioni sulla versione e l'url del package da scaricare
     *
     *     object(stdClass)#325 (4) {
     *       ["slug"]=>         string(19) "wpx-sample/main.php"
     *       ["new_version"]=>  string(3) "1.3.0"
     *       ["url"]=>          string(16) "https://wpxtre.me"
     *       ["package"]=>      string(111) "http://dev.wpxtre.me/api/download/wpxm-4fff289a3a478eccbc87e4b5ce2fe28308fd9f2a7baf3/?wpxpn=wpx-sample/main.php"
     *     }
     *
     */
    public function in_plugin_update_message( $plugin_data, $r ) {

        $token = $this->api->getToken();

        if ( empty( $r->wxp_package ) || empty( $token ) ) {
            $query_args = array(
                'page' => 'wpxm_menu_wpx_store_plugins',
            );
            $url            = add_query_arg( $query_args, self_admin_url( 'admin.php' ) );
            $you_have_login = __( 'You have to login in order to download this update.', WPDK_TEXTDOMAIN );
            printf( '<div style="vertical-align: middle;margin: 16px"><a class="button button-hero" href="%s">%s</a></div>', $url, $you_have_login );
        }
        elseif ( !empty( $token ) ) {
            // http://dev.wpxtre.me/wp-admin/admin.php?page=wpxm_menu_wpx_store_plugins&action=upgrade-plugin&plugin=wpx-sample/wpx-__WPXGENESI_SHORT_PLUGIN_NAME_LOWERCASE__.php
            $query_args = array(
                'page'   => 'wpxm_menu_wpx_store_plugins',
                'action' => 'upgrade-plugin',
                'plugin' => $r->slug
            );
            $url        = add_query_arg( $query_args, self_admin_url( 'admin.php' ) );
            printf( '<div style="vertical-align: middle;margin: 16px"><a class="button button-primary button-hero" href="%s">%s</a> %s</div>', $url, __( 'Update from WPX Store', WPDK_TEXTDOMAIN ), __( 'Remember that you can do this update from WPX Store too.', WPDK_TEXTDOMAIN ) );
        }
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Own checking repository
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Get the transient object with the list of plugins and the list pf to updated plugins.
     * The `transient` has a form like:
     *
     *     object(stdClass)#324 (3) {
     *       ["last_checked"] => int(1355306798)
     *       ["checked"] => array(11) {
     *         ["akismet/akismet.php"]                                          => string(5) "2.5.6"
     *         ["wpx-followgram/wpx-followgram.php"]                            => string(5) "1.0.0"
     *         ["followgram/wpfollowgram.php"]                                  => string(5) "1.0.0"
     *         ["gd-bbpress-attachments/gd-bbpress-attachments.php"]            => string(5) "1.8.4"
     *         ["members/members.php"]                                          => string(5) "0.2.2"
     *         ["wordpress-importer/wordpress-importer.php"]                    => string(3) "0.6"
     *         ["wpx-cleanfix/wpx-cleanfix.php"]                                => string(3) "1.0"
     *         ["wpx-smartshop/main.php"]                                       => string(3) "0.5"
     *         ["wpxtreme/main.php"]                                            => string(3) "0.9"
     *         ["wpxtreme-server/wpxserver.php"]                                => string(3) "1.5"
     *         ["wpx-sample/wpx-__WPXGENESI_SHORT_PLUGIN_NAME_LOWERCASE__.php"] => string(5) "0.1.0"
     *       }
     *       ["response"]=> array(1) {
     *         ["wpx-sample/wpx-__WPXGENESI_SHORT_PLUGIN_NAME_LOWERCASE__.php"] => object(stdClass)#328 (4) {
     *           ["slug"]        => string(60) "wpx-sample/wpx-__WPXGENESI_SHORT_PLUGIN_NAME_LOWERCASE__.php"
     *           ["new_version"] => string(3) "1.3"
     *           ["url"]         => string(17) "https://wpxtre.me"
     *           ["package"]     => string(0) ""
     *         }
     *      }
     *    }
     *
     * We will `url` key in `response` array to check if a plugin is of type wpXtreme. In `response` you found all
     * updated plugin; both wordpress.org, wpxtreme and others
     *
     *
     * @return int Return the count of only wpXtreme plugin to update
     */
    public static function countUpdatingPlugins() {
        $count = 0;
        $transient = get_site_transient( 'update_plugins' );

        if ( !empty( $transient->response ) ) {
            foreach ( $transient->response as $plugin ) {
                if ( isset( $plugin->url ) && 'https://wpxtre.me' == $plugin->url ) {
                    $count++;
                }
            }
        }
        return $count;
    }
}

/* You have to include. */
if ( !class_exists( 'Plugin_Upgrader' ) ) {
    if ( isset( $_GET['action'] ) && 'do-core-upgrade' == $_GET['action'] ) {
        return;
    }
    require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
}

/**
 * Subclassing WordPress Plugin_Upgrader class
 *
 * @class              WPDKPluginUpgrader
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 * @deprecated         Since 1.0.0.b4 - Use WPX version
 *
 */
class WPDKPluginUpgrader extends Plugin_Upgrader {

    /**
     * Override for replace updagrade string
     *
     * @brief Updagrade string
     */
    public function upgrade_strings() {
        $this->strings['up_to_date']          = __( '<i class="wpdk-icon-warning"></i>The plugin is at the latest version.', WPDK_TEXTDOMAIN );
        $this->strings['no_package']          = __( '<i class="wpdk-icon-warning"></i>Update package not available.', WPDK_TEXTDOMAIN );
        $this->strings['downloading_package'] = __( '<i class="wpdk-icon-success"></i>Downloading update from WPX Store&#8230;', WPDK_TEXTDOMAIN );
        $this->strings['installing_package']  = __( '<i class="wpdk-icon-success"></i>Installing the latest version&#8230;', WPDK_TEXTDOMAIN );
        $this->strings['unpack_package']      = __( '<i class="wpdk-icon-success"></i>Unpacking the update&#8230;', WPDK_TEXTDOMAIN );
        $this->strings['deactivate_plugin']   = __( '<i class="wpdk-icon-success"></i>Deactivating the plugin&#8230;', WPDK_TEXTDOMAIN );
        $this->strings['remove_old']          = __( '<i class="wpdk-icon-success"></i>Removing the old version of the wpx plugin&#8230;', WPDK_TEXTDOMAIN );
        $this->strings['remove_old_failed']   = __( '<i class="wpdk-icon-warning"></i>Could not remove the old wpx plugin.', WPDK_TEXTDOMAIN );
        $this->strings['process_failed']      = __( '<i class="wpdk-icon-warning"></i>wpx Plugin update failed.', WPDK_TEXTDOMAIN );
        $this->strings['process_success']     = __( '<i class="wpdk-icon-success"></i>wpx Plugin updated successfully.', WPDK_TEXTDOMAIN );
    }

    /**
     * Override for replace install strings
     *
     * @brief Install strings
     */
    public function install_strings() {
        $this->strings['no_package']          = __( '<i class="wpdk-icon-warning"></i>Install package not available.', WPDK_TEXTDOMAIN );
        $this->strings['downloading_package'] = __( '<i class="wpdk-icon-success"></i>Downloading install package&#8230;', WPDK_TEXTDOMAIN );
        $this->strings['unpack_package']      = __( '<i class="wpdk-icon-success"></i>Unpacking the package&#8230;', WPDK_TEXTDOMAIN );
        $this->strings['installing_package']  = __( '<i class="wpdk-icon-success"></i>Installing the wpx plugin&#8230;', WPDK_TEXTDOMAIN );
        $this->strings['process_failed']      = __( '<i class="wpdk-icon-warning"></i>wpx Plugin install failed.', WPDK_TEXTDOMAIN );
        $this->strings['process_success']     = __( '<i class="wpdk-icon-success"></i>wpx Plugin installed successfully.', WPDK_TEXTDOMAIN );
    }

    /**
     * @brief Generic strings
     *
     * Override for replace generic strings
     *
     */
    public function generic_strings() {
        $this->strings['bad_request']       = __( '<i class="wpdk-icon-warning"></i>Invalid Data provided.' );
        $this->strings['fs_unavailable']    = __( '<i class="wpdk-icon-warning"></i>Could not access filesystem.' );
        $this->strings['fs_error']          = __( '<i class="wpdk-icon-warning"></i>Filesystem error.' );
        $this->strings['fs_no_root_dir']    = __( '<i class="wpdk-icon-warning"></i>Unable to locate WordPress Root directory.' );
        $this->strings['fs_no_content_dir'] = __( '<i class="wpdk-icon-warning"></i>Unable to locate WordPress Content directory (wp-content).' );
        $this->strings['fs_no_plugins_dir'] = __( '<i class="wpdk-icon-warning"></i>Unable to locate WordPress Plugin directory.' );
        $this->strings['fs_no_themes_dir']  = __( '<i class="wpdk-icon-warning"></i>Unable to locate WordPress Theme directory.' );
        /* translators: %s: directory name */
        $this->strings['fs_no_folder']         = __( '<i class="wpdk-icon-warning"></i>Unable to locate needed folder (%s).' );
        $this->strings['download_failed']      = __( '<i class="wpdk-icon-warning"></i>Download failed.' );
        $this->strings['installing_package']   = __( '<i class="wpdk-icon-success"></i>Installing the latest version&#8230;' );
        $this->strings['folder_exists']        = __( '<i class="wpdk-icon-warning"></i>Destination folder already exists.' );
        $this->strings['mkdir_failed']         = __( '<i class="wpdk-icon-warning"></i>Could not create directory.' );
        $this->strings['incompatible_archive'] = __( '<i class="wpdk-icon-warning"></i>The package could not be installed.' );
        $this->strings['maintenance_start']    = __( '<i class="wpdk-icon-success"></i>Enabling Maintenance mode&#8230;' );
        $this->strings['maintenance_end']      = __( '<i class="wpdk-icon-success"></i>Disabling Maintenance mode&#8230;' );
    }


    /**
     * A ben guardare questa potrebbe essere inutile in quanto riproduce quello che già fa il metodo upgrade()
     * della classe ereditata, unica differenza è il parametro di ingresso che dev'essere un oggetto e non l'indirizzo
     * del package di scaricamento
     *
     * @brief Extract and update
     *
     * @param $package
     *
     * @return bool
     */
    public function upgrade( $package ) {

        wp_cache_flush();

        $this->init();
        $this->upgrade_strings();

        add_filter( 'upgrader_pre_install', array( &$this, 'deactivate_plugin_before_upgrade' ), 10, 2 );
        add_filter( 'upgrader_clear_destination', array( &$this, 'delete_old_plugin' ), 10, 4 );

        $this->run( array(
                         'package'           => $package,
                         'destination'       => WP_PLUGIN_DIR,
                         'clear_destination' => true,
                         'clear_working'     => true,
                         'hook_extra'        => array( 'plugin' => $package )
                    ) );

        // Cleanup our hooks, incase something else does a upgrade on this connection.
        remove_filter( 'upgrader_pre_install', array( &$this, 'deactivate_plugin_before_upgrade' ) );
        remove_filter( 'upgrader_clear_destination', array( &$this, 'delete_old_plugin' ) );

        if ( !$this->result || is_wp_error( $this->result ) ) {
            return $this->result;
        }

        return false;
    }
}


/**
 * Subclassing WordPress Plugin_Installer_Skin class
 *
 * ### Debug
 *
 *     $this->upgrader->skin->options
 *
 *     array(7) {
 *       ["url"]=>
 *       string(154) "http://dev.wpxtre.me/wp-admin/admin.php?page=wpxm_menu_wpx_store&action=upgrade-plugin&plugin=wpx-sample/wpx-__WPXGENESI_SHORT_PLUGIN_NAME_LOWERCASE__.php"
 *       ["nonce"]=>
 *       string(0) ""
 *       ["title"]=>
 *       string(27) "Update Plugin from wpxStore"
 *       ["context"]=>
 *       bool(false)
 *       ["type"]=>
 *       string(3) "web"
 *       ["plugin"]=>
 *       string(60) "wpx-sample/wpx-__WPXGENESI_SHORT_PLUGIN_NAME_LOWERCASE__.php"
 *       ["api"]=>
 *       array(0) {
 *       }
 *     }
 *
 * @class              WPDKPluginUpgraderSkin
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 * @deprecated         Since 1.0.0.b4 - Use WPX version
 *
 */
class WPDKPluginUpgraderSkin extends Plugin_Installer_Skin {

    /**
     * @brief Open the header
     */
    public function header() {
        if ( $this->done_header ) {
            return;
        }
        $this->done_header = true;

        $options = $this->upgrader->skin->options;

        ?>
        <div class="wpdk-install-report-feedback">
            <fieldset class="wpdk-form-fieldset">
                <legend><?php echo $options['title'] ?></legend>
                <?php

        wp_ob_end_flush_all();
        flush();

    }

    /**
     * @brief Close the footer
     */
    public function footer() {
        ?>
            </fieldset>
        <?php

        /* Get Plugin info. */
        $plugin_file = $this->upgrader->plugin_info();

        /* Array delle azioni alla fine della schermata */
        $install_actions = array();

        /* Back to WPX Store button. */
        $url                                = remove_query_arg( array('action', 'plugin') );
        $title                              = __( 'Back to WPX store', WPDK_TEXTDOMAIN );
        $label                              = __( 'Back to WPX Store', WPDK_TEXTDOMAIN );
        $install_actions['wpx_store'] = sprintf( '<a class="button button-secondary" href="%s" title="%s">%s</a>', $url, $title, $label );

        $from = isset( $_GET['from'] ) ? stripslashes( $_GET['from'] ) : 'plugins';

        /* @todo Da testare e capire se nel contesto wpx Store abbia senso */
        if ( 'import' == $from ) {
            $url                                = wp_nonce_url( 'plugins.php?action=activate&amp;from=import&amp;plugin=' . $plugin_file, 'activate-plugin_' . $plugin_file );
            $title                              = __( 'Activate this plugin', WPDK_TEXTDOMAIN );
            $label                              = __( 'Activate Plugin &amp; Run Importer', WPDK_TEXTDOMAIN );
            $install_actions['activate_plugin'] = sprintf( '<a class="button button-primary alignright" href="%s" title="%s">%s</a>', $url, $title, $label );
        }
        /* This is the default. */
        else {

            // TODO to complete
            //$url = add_query_arg( array( 'action' => 'activate', '_wpnonce' => wp_create_nonce( 'activate-plugin_' . $plugin_file ) ) );

            $url                                = wp_nonce_url( 'plugins.php?action=activate&amp;plugin=' . $plugin_file, 'activate-plugin_' . $plugin_file );
            $title = __( 'Activate this plugin', WPDK_TEXTDOMAIN );
            $label                              = __( 'Activate Plugin', WPDK_TEXTDOMAIN );
            $install_actions['activate_plugin'] = sprintf( '<a class="button button-primary alignright" href="%s" title="%s">%s</a>', $url, $title, $label );
        }

        /* Network activate. */
        if ( is_multisite() && current_user_can( 'manage_network_plugins' ) ) {
            $url                                = wp_nonce_url( 'plugins.php?action=activate&amp;networkwide=1&amp;plugin=' . $plugin_file, 'activate-plugin_' . $plugin_file );
            $title                              = __( 'Activate this plugin for all sites in this network', WPDK_TEXTDOMAIN );
            $label                              = __( 'Network Activate', WPDK_TEXTDOMAIN );
            $install_actions['network_activate'] = sprintf( '<a class="button button-primary alignright" href="%s" title="%s">%s</a>', $url, $title, $label );

            unset( $install_actions['activate_plugin'] );
        }

        /* If any error occour not active. */
        if ( !$this->result || is_wp_error( $this->result ) ) {
            unset( $install_actions['activate_plugin'] );
            unset( $install_actions['network_activate'] );
        }

        /* Check if the plugin is already active. */
        if ( is_plugin_active( $plugin_file ) ) {
            unset( $install_actions['activate_plugin'] );
        }

        $install_actions = apply_filters( 'install_plugin_complete_actions', $install_actions, $this->api, $plugin_file );
        if ( !empty( $install_actions ) ) {
            $this->feedback( sprintf( '<span class="clearfix">%s</span>', implode( ' ', $install_actions ) ) );
        }
        ?>

        </div><!-- wpdk-install-report-feedback -->
    <?php

        wp_ob_end_flush_all();
        flush();

    }

    /**
     * @brief Update/Install after footer
     */
    public function after() {
        wp_ob_end_flush_all();
        flush();
    }
}

/// @endcond