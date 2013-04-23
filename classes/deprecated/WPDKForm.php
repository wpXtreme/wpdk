<?php
/// @cond private

define( 'WPDK_FORM_FIELD_TYPE_',         '' );
define( 'WPDK_FORM_FIELD_TYPE_CUSTOM',   'custom' );
define( 'WPDK_FORM_FIELD_TYPE_HIDDEN',   'hidden' );
define( 'WPDK_FORM_FIELD_TYPE_LABEL',    'label' );
define( 'WPDK_FORM_FIELD_TYPE_TEXT',     'text' );
define( 'WPDK_FORM_FIELD_TYPE_SUBMIT',   'submit' );
define( 'WPDK_FORM_FIELD_TYPE_BUTTON',   'button' );
define( 'WPDK_FORM_FIELD_TYPE_FILE',     'file' );
define( 'WPDK_FORM_FIELD_TYPE_PASSWORD', 'password' );
define( 'WPDK_FORM_FIELD_TYPE_CHECKBOX', 'checkbox' );
define( 'WPDK_FORM_FIELD_TYPE_RADIO',    'radio' );
define( 'WPDK_FORM_FIELD_TYPE_EMAIL',    'email' );
define( 'WPDK_FORM_FIELD_TYPE_NUMBER',   'number' );
define( 'WPDK_FORM_FIELD_TYPE_DATE',     'date' );
define( 'WPDK_FORM_FIELD_TYPE_DATETIME', 'dateTime' );
define( 'WPDK_FORM_FIELD_TYPE_PHONE',    'phone' );
define( 'WPDK_FORM_FIELD_TYPE_SELECT',   'select' );
define( 'WPDK_FORM_FIELD_TYPE_TEXTAREA', 'textarea' );
define( 'WPDK_FORM_FIELD_TYPE_SWIPE',    'swipe' );
define( 'WPDK_FORM_FIELD_TYPE_CHOOSE',   'choose' );
define( 'WPDK_FORM_FIELD_TYPE_PARAGRAPH','paragraph' );
define( 'WPDK_FORM_FIELD_TYPE_ALERT',    'alert' );

define( 'WPDK_FORM_FIELD_SECTION',       'section' );


/*
 * [DRAFT]
 *
 * THE FOLLOWING CODE IS A DRAFT. FEEL FREE TO USE IT TO MAKE SOME EXPERIMENTS, BUT DO NOT USE IT IN ANY CASE IN
 * PRODUCTION ENVIRONMENT. ALL CLASSES AND RELATIVE METHODS BELOW CAN CHNAGE IN THE FUTURE RELEASES.
 *
 */

/**
 *
 * Classe per gestire la costruzione visuale di moduli. Questa nasce come uno standard, sia all'interno di WordPress che
 * con i dovuti adattamenti, anche per altri ambienti. Oltre ad una serie di metodi utilizzabili singolarmente, essa si
 * basa sul concetto di definizione standard di un modulo tramite una serie di array indentati che descrivono l'intero
 * aspetto del modulo stesso.
 * L'array che descrive l'interno del modulo è definito dallo standard SDF, Standard Definition Form.
 *
 *     ### SDF
 *     Questo standard permette di descrivere l'interno di un modulo a partie da una sequenza di array
 *     molto semplice. Essa inizia con la descrizione del fieldset, legend, eventuali messaggi eplicativi e
 *     la serie di item che descrivono i campi del modulo. Ad esempio:
 *
 *     $fields = array(
 *        'Fieldset Legend Group' => array(
 *             'Subtitle',                                 // Optional subtitle
 *                 array( array(...), array(...) ),        // Row of items
 *                 array(...),                             // New row
 *                 array(...)                              // Items
 *         );
 *     );
 *
 * +--------------------------+
 * | form                     |
 * |  +---------------------+ |
 * |  | fieldset            | |
 * |  |  +----------------+ | |
 * |  |  | rows           | | |
 * |  |  +----------------+ | |
 * |  +---------------------+ |
 * +--------------------------+
 *
 *
 * @class        WPDKForm
 * @author       =undo= <info@wpxtre.me>
 * @copyright    Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date         30/11/11
 * @version      1.0
 *
 * @deprecated Sice 0.6.3 - Use WPDKUIControlsLayout instead - Used by several classes
 *
 */
class WPDKForm {

    public $fields;
    public $name;
    public $action;

    public $method = '';
    public $enctype = '';
    public $target = '';
    public $nonce = '';
    public $nonce_token = '';
    public $id = '';
    public $class = '';
    public $style = '';
    public $title = '';


    // -----------------------------------------------------------------------------------------------------------------
    // Construct
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Instanzia un oggetto di tipo WPDKForm
     *
     * @param array  $fields SDF fields
     * @param string $name   Attributo name del form
     * @param string $action Attributo action del form
     *
     */
    function __construct( $fields, $name = '', $action = '' ) {
        $this->fields = $fields;
        $this->name   = $name;
        $this->action = $action;
    }

    /**
     * Restituisce l'html del form con all'interno fieldset e campi, definiti nello standatd SDF.
     *
     * @param string $before         Contenuto HTML custom extra prima del tag form
     * @param string $before_content Contenuto HTML custom extra prima del primo fieldset
     * @param string $after_content  Contenuto HTML custom extra prima della chiusara del tag form
     * @param string $after          Contenuto HTML custom extra dopo la chiusura del tag form
     *
     * @return string
     */
    function html( $before = '', $before_content = '', $after_content = '', $after = '' ) {

        $nonce   = $this->nonce();
        $content = $this->content();

        $html = <<< HTML
{$before}
<form id="{$this->id}"
name="{$this->name}"
method="{$this->method}"
target="{$this->id}"
class="wpdk-form {$this->class}"
enctype="{$this->enctype}"
title="{$this->title}"
style="{$this->style}"
action="{$this->action}">
{$nonce}
{$before_content}
{$content}
{$after_content}
</form>
$after
HTML;
        return $html;
    }

    private function content() {
        /* @todo Da rimuovere quando saranno eliminato gli echo automatici. */
        ob_start();
        $this->htmlForm( $this->fields );
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

    /**
     * @var function
     */
    public static $function;

    /**
     * @var int
     */
    private static $section = 1;

    // -----------------------------------------------------------------------------------------------------------------
    // Static values
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Restituisce l'array per la localizzazione Javascript
     *
     * @return array
     */
    public static function scriptLocalization() {

        $result = array(
            'timeOnlyTitle' => __( 'Choose Time', WPDK_TEXTDOMAIN ),
            'timeText'      => __( 'Time', WPDK_TEXTDOMAIN ),
            'hourText'      => __( 'Hour', WPDK_TEXTDOMAIN ),
            'minuteText'    => __( 'Minute', WPDK_TEXTDOMAIN ),
            'secondText'    => __( 'Seconds', WPDK_TEXTDOMAIN ),
            'currentText'   => __( 'Now', WPDK_TEXTDOMAIN ),
            'dayNamesMin'   => __( 'Su,Mo,Tu,We,Th,Fr,Sa', WPDK_TEXTDOMAIN ),
            'monthNames'    => __( 'January,February,March,April,May,June,July,August,September,October,November,December', WPDK_TEXTDOMAIN ),
            'closeText'     => __( 'Close', WPDK_TEXTDOMAIN ),
            'dateFormat'    => __( 'mm/dd/yy', WPDK_TEXTDOMAIN )
        );
        return $result;
    }

    /**
     * Da implementare in tutte le classe, sono loro stesse ad aggiungere nelle code scripts e styles ciò che gli serve
     *
     *
     * @deprecated
     *
     */
    public static function enqueueStyles() {
        _deprecated_function(  __CLASS__ . __FUNCTION__, '0.5' );

        WPDK::enqueueStyles();
    }

    /**
     * Da implementare in tutte le classe, sono loro stesse ad aggiungere nelle code scripts e styles ciò che gli serve
     *
     *
     * @deprecated
     *
     */
    public static function enqueueScripts() {
        _deprecated_function( __FUNCTION__, '0.5' );

        WPDK::enqueueScripts();
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Commodity
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Restituisce un campo input di tipo hidden con nn nome crittato e un valore di nonce in base al parametro $name
     * passato negli inputs. Se questo è vuoto vengono presi in considerazione il nome del form e la proprietà.
     *
     * @param string $name Nome del nonce
     * @return string HTML dell'input tipo hidden con il campo nonce. Viene anche impostata la proprietà nonce
     */
    function nonce( $name = '' ) {
        $result = '';
        if ( empty( $name ) ) {
            $name = $this->name;
        }
        if ( !empty( $name ) ) {
            $cname       = md5( $name );
            $this->nonce = wp_create_nonce( $cname );
            $result      = sprintf( '<input type="hidden" name="%s" id="%s" value="%s" />', $cname, $cname, $this->nonce );
        }
        return $result;
    }

    /**
     * Genera e visualizza il campo nonce.
     * Visualizza un campo input di tipo hidden con un nonce value generato in base al parametro $key passato negli
     * inputus
     *
     *
     * @uses       wp_create_nonce()
     *
     * @param string $key Chiave per la generazione del nonce
     */
    public static function nonceWithKey( $key ) {
        $name = md5( $key ); ?>
    <input type="hidden"
           name="<?php echo $name ?>"
           id="<?php echo $name ?>"
           value="<?php echo wp_create_nonce( $key );?>"/>
    <?php
    }

    /**
     * Verifica un campo nonce.
     * Restituisce true se il precedente campo nonce creato con nonceWithKey() corrisponde.
     *
     *
     * @uses       wp_verify_nonce()
     *
     * @param string $key Chiave usata in nonceWithKey()
     *
     * @return bool Ritorna true se il campo corrisponde, altrimenti false
     */
    public static function isNonceVerify( $key ) {
        $name = md5( $key );

        if ( isset( $_POST[$name] ) ) {
            if ( wp_verify_nonce( $_POST[$name], $key ) ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Prototipo di gruppi di elementi base
     *
     * @todo       Non utilizzato - prototipo
     *
     *
     * @param array  $item
     * @param array  $groups
     * @param string $type
     *
     * @return array
     */
    public static function group( $item, $groups, $type = WPDK_FORM_FIELD_TYPE_RADIO ) {
        $result = array();
        foreach ( $groups as $key => $group ) {
            $item['type']  = $type;
            $item['value'] = $key;
            $item['label'] = $group;

            $result[] = $item;
        }
        return $result;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Fetching fields
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Percorre ricorsivamente tutto l'array delle definizione dei campi SFD invocando una funzione hook ogni volta che
     * trova un campo
     *
     *
     * @param array    $sfd      Array dei campi comprensivo di fieldset legend e descrizioni, lo stesso usato per htmlForm()
     * @param function $function Una funzione del tipo function($item) {}. Il primo parametro $item è l'array nello standard SFD
     */
    public static function walker( $sfd, $function ) {
        if ( !function_exists( '_walker' ) ) {
            function _walker( $rows, $function ) {
                foreach ( $rows as $item ) {

                    if ( !is_string( $item ) ) {

                        if ( isset( $item['type'] ) &&
                            ( !isset( $item['walker'] ) || ( isset( $item['walker'] ) && !$item['walker'] ) )
                        ) {
                            call_user_func( $function, $item );
                        } else {
                            _walker( $item, $function );
                        }
                    }
                }
            }
        }

        foreach ( $sfd as $rows ) {
            _walker( $rows, $function );
        }
    }

    /**
     * Restituisce una array assocativo con chiave uguale al nome del campo e valore ugaule al POST del campo.
     * Di solito comodo durante la fase di memorizzazione dei dati di una form. Usato soprattutto per serializzare una
     * serie di valori in un unico serialize da memorizzare in tabella.
     *
     *
     * @param array $sfd Array con la descrizione dei campi nello standard SDF
     *
     * @return array Array con chiave => valore; dove è la chiave è il nome del campo nelle specifiche SFD
     */
    public static function arrayKeyItemPostValue( $sfd ) {
        $result = array();
        if ( !function_exists( '_walker_result' ) ) {
            function _walker_result( $rows, &$result ) {
                foreach ( $rows as $item ) {
                    if ( !is_string( $item ) ) {
                        if ( isset( $item['type'] ) && isset( $_POST[$item['name']] ) ) {
                            $result[$item['name']] = $_POST[$item['name']];
                        } else {
                            _walker_result( $item, $result );
                        }
                    }
                }
            }
        }

        foreach ( $sfd as $rows ) {
            _walker_result( $rows, $result );
        }

        return $result;
    }

    /**
     * Percorre ricorsivamente tutto l'array delle definizione dei campi SFD alla ricerca di un match tra il nome del
     * campo e il parametro POST. Se lo trova invoca la funziona data passando elemento e valore.
     *
     *
     * @param array    $sfd      Array dei campi comprensivo di fieldset legend e descrizioni, lo stesso usato per htmlForm()
     * @param function $function Una funzione del tipo function($item, $value) {}. Il primo parametro $item è il nome
     *                           del campo e il secondo il valore
     */
    public static function walkerWithPost( $sfd, $function ) {
        self::$function = $function;
        self::walker( $sfd, '__walkerWithPost' );

        if ( !function_exists( '__walkerWithPost ' ) ) {
            function __walkerWithPost( $item ) {
                if ( isset( $item['name'] ) ) {
                    $name = $item['name'];
                    if ( substr( $item['name'], -2 ) == '[]' ) {
                        $name = substr( $name, 0, strlen( $name ) - 2 );
                    }
                    if ( isset( $_POST[$name] ) ) {
                        call_user_func( WPDKForm::$function, $name, $_POST[$name] );
                    }
                }
            }
        }
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Manipulation
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Aggiunge un insieme di fields (array) all'inizio dell'array $fields con la nuova chiave $key. Utilissimo per
     * aggiungere campi ad un SFD già definito
     *
     *
     * @param array  $fields
     * @param array  $newfield
     * @param string $key String Key in new array
     *
     * @return array
     */
    public static function fieldsetAtBeginningWithLabel( $fields, $newfield, $key ) {
        $fields = array_merge( array( $key => $newfield ), $fields );
        return $fields;
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Output
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Cicla per l'intera struttura di array che descrive i campi (visuali) del form:
     * [section] -> [field list]
     *
     * @filters wpdk_form_section_class
     *
     * @uses       htmlRows()
     *
     * @param array $sfd Array che segue lo standard SFD (Standard Form Definition) ovvero:
     *
     *      $fields = array(
     *         'Fieldset Legend Group' => array(
     *              'Subtitle',                                 // Optional subtitle
     *                  array( array(...), array(...) ),        // Row of items
     *                  array(...),
     *                  array(...)                              // Items
     *          );
     *      );
     *
     */
    public static function htmlForm( $sfd, $accordion = false ) {

        if( false === $accordion ) {
            $open_close = '';
            $accordion_class = '';
        } else {
            $open_close = '<i class="wpdk-openclose-accordion"></i>';
            $accordion_class = 'wpdk-accordion';
        }

        $row_index = 0;

        foreach ( $sfd as $key => $rows ) :

            $accordion_open = ( false === $accordion ) ? '' : ($row_index == 0) ? 'wpdk-accordion-open' : '';

            $class = 'wpdk-form-fieldset wpdk-form-section' . self::$section;
            $class = apply_filters( 'wpdk_form_section_class', $class, self::$section++, $key ); ?>
        <fieldset class="<?php echo $class ?> <?php echo $accordion_open ?>">
            <legend><?php echo $key ?><?php echo $open_close ?></legend>
            <div class="wpdk-fieldset-container <?php echo $accordion_class ?> <?php echo $accordion_open ?>">
                <div>
                <?php self::htmlRows( $rows ) ?>

                </div>
            </div>
        </fieldset>
        <?php $row_index++; endforeach;
    }

    /**
     * Completamento del walker htmlForm() per ogni elemento al di sotto del fieldset
     *
     * @uses       htmlItem()
     *
     * @param array $rows
     */
    public static function htmlRows( $rows ) {
        foreach ( $rows as $item ) {
            if ( is_string( $item ) && $item != '' ) {
                ?><div class="wpdk-form-description"><?php echo $item ?></div<?php
            } else {
                if ( isset( $item['type'] ) ) {
                    self::htmlItem( $item );
                } else if ( isset( $item['group'] ) ) {
                    echo apply_filters( 'wpdk_form_html_group_before', self::wrap( $item ), $item );
                    self::htmlRows( $item['group'] );
                    echo apply_filters( 'wpdk_form_html_group_after', '</div>', $item );
                } else if ( isset( $item['group_inner'] ) ) {
                    echo apply_filters( 'wpdk_form_html_group_before', self::wrapInner( $item ), $item );
                    self::htmlRows( $item['group_inner'] );
                    echo apply_filters( 'wpdk_form_html_group_after', '</span>', $item );
                } else if ( !empty( $item ) ) {
                    echo apply_filters( 'wpdk_form_html_row_before', '<div class="wpdk-form-row">', $item );
                    self::htmlRows( $item );
                    echo apply_filters( 'wpdk_form_html_row_after', '</div>', $item );
                }
            }
        }
    }

    /**
     * Raggruppa, avvolge, una serie di elementi all'interno di un tag div
     *
     *
     * @param array $item SDF element descriptor
     *
     * @return string
     */
    private static function wrap( $item ) {
        $id    = self::_id( $item );
        $class = self::_class( $item );
        $title = self::_title( $item );
        $style = self::_style( $item );

        $wrap = <<< HTML
        <div {$style} id="{$id}" class="wpdk-form-group {$class}" title="{$title}">
HTML;

        return $wrap;
    }

    /**
     * Raggruppa, avvolge, una serie di elementi all'interno di un tag span
     *
     *
     * @param array $item SDF element descriptor
     *
     * @return string
     */
    private static function wrapInner( $item ) {
        $id    = self::_id( $item );
        $class = self::_class( $item );
        $title = self::_title( $item );
        $style = self::_style( $item );

        $wrap = <<< HTML
        <span {$style} id="{$id}" class="wpdk-form-group-inner {$class}" title="{$title}">
HTML;

        return $wrap;
    }

    /**
     * Processa un singolo tipo di campo
     * Qui è introdotta anche una gestione dei paragrafi per andare accapo. In pratica per gestire elementi su una riga
     * e su più righe.
     *
     *
     * @uses       htmlInput()
     * @uses       htmlSelect()
     *
     * @param array $item
     *
     */
    public static function htmlItem( $item ) {
        switch ( $item['type'] ) {
            case WPDK_FORM_FIELD_TYPE_ALERT:
                self::alert( $item );
                break;
            case WPDK_FORM_FIELD_TYPE_PARAGRAPH:
                self::paragraph( $item );
                break;
            case WPDK_FORM_FIELD_TYPE_CUSTOM:
                call_user_func( $item['callback'], $item );
                break;
            case WPDK_FORM_FIELD_TYPE_HIDDEN:
                self::htmlHidden( $item );
                break;
            case WPDK_FORM_FIELD_TYPE_LABEL:
                self::htmlLabel( $item );
                break;
            case WPDK_FORM_FIELD_TYPE_TEXT:
            case WPDK_FORM_FIELD_TYPE_SUBMIT:
            case WPDK_FORM_FIELD_TYPE_BUTTON:
            case WPDK_FORM_FIELD_TYPE_PASSWORD:
            case WPDK_FORM_FIELD_TYPE_CHECKBOX:
            case WPDK_FORM_FIELD_TYPE_RADIO:
            case WPDK_FORM_FIELD_TYPE_EMAIL:
            case WPDK_FORM_FIELD_TYPE_NUMBER:
            case WPDK_FORM_FIELD_TYPE_DATE:
            case WPDK_FORM_FIELD_TYPE_DATETIME:
            case WPDK_FORM_FIELD_TYPE_PHONE:
            case WPDK_FORM_FIELD_TYPE_FILE:
                self::htmlInput( $item );
                break;
            case WPDK_FORM_FIELD_TYPE_SELECT:
                self::htmlSelect( $item );
                break;
            case WPDK_FORM_FIELD_TYPE_TEXTAREA:
                self::htmlTextarea( $item );
                break;
            case WPDK_FORM_FIELD_TYPE_SWIPE:
                self::htmlSwipe( $item );
                break;
            case WPDK_FORM_FIELD_TYPE_CHOOSE:
                self::htmlChoose( $item );
                break;
            default:
                break;
        }
    }

    /// Twitter Bootstrap Alert element
    private static function alert( $item ) {
        $id                    = self::_id( $item );
        $type                  = isset( $item['alert_type'] ) ? $item['alert_type'] : WPDKTwitterBootstrapAlertType::INFORMATION;
        $alert                 = new WPDKTwitterBootstrapAlert( $id, $item['value'], $type );
        $alert->dismiss_button = isset( $item['dismiss_button'] ) ? $item['dismiss_button'] : true;
        $alert->block          = isset( $item['block'] ) ? $item['block'] : false;
        $alert->display();
        self::_append( $item, false );
    }

    /// Paragraph element
    private static function paragraph( $item ) {
        $id    = self::_id( $item );
        $class = self::_class( $item );
        ?>
    <p id="<?php echo $id ?>"
       class="<?php echo $class ?>">
        <?php echo $item['value'] ?>
    </p>
    <?php
        self::_append( $item, false );
    }

    /**
     * Visualizza un campo di tipo hidden
     *
     *
     * @param array $item SDF element descriptor
     */
    private static function htmlHidden( $item ) {
        $id = self::_id( $item ); ?>
    <input type="hidden"
           id="<?php echo $id ?>"
           name="<?php echo $item['name'] ?>"
           value="<?php echo $item['value'] ?>"/>
    <?php
        self::_append( $item, false );
    }

    /**
     * Visualizza l'output per un tag di tipo <input>.
     *
     * @private
     *
     * @param array $item SDF element descriptor
     */
    private static function htmlInput( $item ) {

        $item['id'] = self::_id( $item );
        $before     = self::_before( $item );
        $after      = self::_after( $item );

        echo $before;

        if( $item['type'] == WPDK_FORM_FIELD_TYPE_CHECKBOX ) {
            self::htmlInputWithType( $item );
            self::_label( $item );
        } else {
            self::_label( $item );
            self::htmlInputWithType( $item );
        }

        self::_locked( $item );
        self::_append( $item );
        //self::_help( $item );

        echo $after;
    }

    /**
     * Etichetta semplice. Questo è uno span all'interno di una label con classe wpdk-form-label-inline
     *
     *
     * @param array $item SDF element descriptor
     */
    private static function htmlLabel( $item ) {
        $class       = self::_class( $item );
        $afterlabel  = self::_afterlabel( $item );
        $beforelabel = self::_beforelabel( $item );
        $before      = self::_before( $item );
        $after       = self::_after( $item );
        $title       = self::_title( $item );

        echo $before;

        if ( isset( $item['label'] ) ) {
            ?>
        <label title="<?php echo $title ?>" class="<?php echo $class ?> <?php echo $item['name'] ?> wpdk-form-label-inline">
            <?php echo $beforelabel ?>
            <?php echo $item['label'] ?>
            <?php echo $afterlabel ?> <span><?php echo $item['value'] ?></span>
        </label>
        <?php
            echo $after;
        }
        self::_append( $item );
        //self::_help( $item );
    }

    /**
     * Gestisce l'emissione HTML per i diversi tipi di tag <input>
     *
     * @private
     *
     * @param array $item SDF element descriptor
     */
    private static function htmlInputWithType( $item ) {

        $html = '<input type="%s" class="%s" name="%s" id="%s" value="%s" size="%s" title="%s" %s />';

        // -------------------------------------------------------------------------------------------------------------
        // Comune a tutti gli input
        // -------------------------------------------------------------------------------------------------------------

        /* Per questioni legate a jQuery l'id di un campo input non può essere un array, tipo nome[]. */
        $id          = self::_id( $item );
        $class       = self::_class( $item );
        $title       = self::_title( $item );
        $size        = self::_size( $item );
        $value       = self::_value( $item );
        $placeholder = self::_placeholder( $item );
        $readonly    = self::_readonly( $item );
        $checked     = self::_checked( $item );

        $data        = self::_data( $item );
        $attrs       = self::_attrs( $item );

        $extra = sprintf( '%s %s %s %s %s', $placeholder, $readonly, $checked, $data, $attrs );

        switch ( $item['type'] ) {
            case WPDK_FORM_FIELD_TYPE_TEXT:
            case WPDK_FORM_FIELD_TYPE_SUBMIT:
            case WPDK_FORM_FIELD_TYPE_BUTTON:
            case WPDK_FORM_FIELD_TYPE_FILE:
            case WPDK_FORM_FIELD_TYPE_CHECKBOX:
                $input = sprintf( $html, $item['type'], $class, $item['name'], $id, $value, $size, $title, $extra );
                break;
            case WPDK_FORM_FIELD_TYPE_PASSWORD:
                if ( isset( $item['autocomplete'] ) ) {
                    $extra .= ' autocomplete="' . $item['autocomplete'] . '"';
                }
                else {
                    $extra .= ' autocomplete="off"';
                }
                $input = sprintf( $html, $item['type'], $class, $item['name'], $id, $value, $size, $title, $extra );
                break;

            case WPDK_FORM_FIELD_TYPE_RADIO:
                $input = sprintf( $html, $item['type'], $class, $item['name'], $id, $value, $size, $title, $extra );
                break;
            case WPDK_FORM_FIELD_TYPE_EMAIL:
            case WPDK_FORM_FIELD_TYPE_PHONE:
            case WPDK_FORM_FIELD_TYPE_NUMBER:
                $input = sprintf( $html, 'text', $class, $item['name'], $id, $value, $size, $title, $extra );
                break;
            case WPDK_FORM_FIELD_TYPE_DATE:
            case WPDK_FORM_FIELD_TYPE_DATETIME:
                $input = sprintf( $html, 'text', $class, $item['name'], $id, $value, $size, $title, $extra );
                $input .= self::_clear( $item );
                break;
            default:
                $input = '';
                break;
        }
        echo $input;
    }

    /**
     * Visualizza l'output per un tag di tipo <select>. Supporta le optgroup tramite doppio array:
     *
     *     array( 'value' => 'label', 'value' => 'label', ...);
     *
     *     oppure
     *
     *     array( 'optgroup label' => array( 'value' => 'label', 'value' => 'label', ...),
     *       'optgroup label' => array( 'value' => 'label', 'value' => 'label', ...)
     *     );
     *
     *     oppure
     *
     *     array( 'value' => 'label',
     *       'optgroup label' => array( 'value' => 'label', 'value' => 'label', ...),
     *       'optgroup label' => array( 'value' => 'label', 'value' => 'label', ...)
     *     );
     *
     *
     * @since      1.0
     *
     * @param array $item SDF element descriptor
     */
    private static function htmlSelect( $item ) {
        // -------------------------------------------------------------------------------------------------------------
        // Comune a tutti gli input
        // -------------------------------------------------------------------------------------------------------------
        $id     = self::_id( $item );
        $class  = self::_class( $item );
        $title  = self::_title( $item );
        $before = self::_before( $item );
        $after  = self::_after( $item );

        $size   = isset( $item['size'] ) ? 'size="'.$item['size'].'"' : '';

        /* @todo Da fare */
        //$multiple = self::_size( $item );
        $multiple = isset( $item['multiple'] ) ? 'multiple="multiple"' : '';

        $data  = self::_data( $item );
        $attrs = self::_attrs( $item );

        echo $before;

        self::_label( $item ); ?>

    <select name="<?php echo $item['name'] ?>"
            id="<?php echo $id ?>"
            title="<?php echo $title ?>"
            class="<?php echo $class ?>"
            <?php echo $size ?>
            <?php echo $multiple ?>
            <?php echo $attrs ?>
            <?php echo $data ?>>

        <?php

        /* Controllo callback */
        if ( isset( $item['options'][0] ) &&
            class_exists( $item['options'][0] ) &&
            method_exists( $item['options'][0], $item['options'][1] )
        ) {
            $item['options'] = call_user_func( $item['options'], $item );
        }

        self::htmlOption( $item, $item['options'] ); ?>

    </select>
    <?php
        echo $after;

        self::_locked( $item );
        self::_append( $item );
        //self::_help( $item );
    }

    /**
     * Visualizza gli option e optgroup per un controllo select. Per gli optgroup basta indentare un array in un
     * array.
     *
     * @uses       htmlOption()
     *
     * @param array $item SDF element descriptor
     * @param array $options Options list
     */
    private static function htmlOption( $item, $options ) {
        foreach ( $options as $key => $option ) : ?>
        <?php if ( is_array( $option ) ) : ?>
            <optgroup class="wpdk-form-optiongroup" label="<?php echo $key ?>">
                <?php self::htmlOption( $item, $option ); ?>
            </optgroup>
            <?php else : ?>
            <option class="wpdk-form-option" <?php if ( isset( $item['value'] ) ) selected( $key, $item['value'] ) ?>
                    value="<?php echo $key ?>"><?php echo $option ?></option>
            <?php endif; ?>
        <?php endforeach;
    }

    /**
     * Visualizza una Textarea
     *
     *
     * @param array $item SDF element descriptor
     */
    private static function htmlTextarea( $item ) {
        // -------------------------------------------------------------------------------------------------------------
        // Comune a tutti
        // -------------------------------------------------------------------------------------------------------------
        $id     = self::_id( $item );
        $class  = self::_class( $item );
        $title  = self::_title( $item );
        $value  = self::_value( $item );
        $rows   = isset( $item['rows'] ) ? $item['rows'] : 5;
        $cols   = isset( $item['cols'] ) ? $item['cols'] : 10;
        $before = self::_before( $item );
        $after  = self::_after( $item );

        $data        = self::_data( $item );
        $attrs       = self::_attrs( $item );

        echo $before;

        self::_label( $item ); ?>

    <textarea
              <?php echo $data ?>
              <?php echo $attrs ?>
              name="<?php echo $item['name'] ?>"
              id="<?php echo $id ?>"
              class="wpdk-form-textarea <?php echo $class ?>"
              title="<?php echo $title ?>"
              rows="<?php echo $rows ?>"
              cols="<?php echo $cols ?>"><?php echo $value ?></textarea>
    <?php

        echo $after;
    }

    /**
     * Visuallizza un componente personalizzato di tipo swipe
     *
     *     $item['status'] = 'on' | 'off'
     *
     * @since      1.0
     *
     * @param array $item SDF element descriptor
     */
    public static function htmlSwipe( $item ) {
        // -------------------------------------------------------------------------------------------------------------
        // Comune a tutti gli input
        // -------------------------------------------------------------------------------------------------------------
        $id     = self::_id( $item );
        $class  = self::_class( $item );
        $title  = self::_title( $item );
        $before = self::_before( $item );
        $after  = self::_after( $item );

        $userdata = isset( $item['userdata'] ) ? $item['userdata'] : '';
        $status   = wpdk_is_bool( $item['value'] ) ? 'wpdk-form-swipe-on' : '';

        $data  = self::_data( $item );
        $attrs = self::_attrs( $item );

        echo $before;

        self::_label( $item ); ?>

    <span
        <?php echo $data ?>
        <?php echo $attrs ?>
            wpdk-userdata="<?php echo $userdata ?>"
            id="<?php echo $id ?>"
            title="<?php echo $title ?>"
            class="<?php echo $status ?> <?php echo $class ?>">
                <span></span>
                <input type="hidden"
                       id="wpdk-swipe-<?php echo $id ?>"
                       name="<?php echo $item['name'] ?>"
                       value="<?php echo $item['value'] ?>"/>
            </span>
    <?php
        echo $after;
        //self::_help( $item );
    }


    /**
     * Visualizza un controllo personalizzato per la scelta di elementi personalizzati composti solitamente da
     * id/descrizione. Si appoggia comunque, per questione di standard e sicurezza, ad un campo input di tipo hiddem
     *
     *
     * @uses       htmlHidden()
     *
     * @param array $item SDF element descriptor
     */
    public static function htmlChoose( $item ) {
        // -------------------------------------------------------------------------------------------------------------
        // Comune a tutti gli input
        // -------------------------------------------------------------------------------------------------------------
        $item['id'] = self::_id( $item );
        $class      = self::_class( $item );
        $title      = self::_title( $item );
        $hide       = '';
        if ( empty( $item['label'] ) ) {
            $hide = 'hide';
        }
        ?>
    <span class="wpdk-form-choose">
        <?php self::htmlHidden( $item ) ?>
        <span title="<?php echo $title ?>"
              class="wpdk-form-choose-label <?php echo $class ?> <?php echo $hide ?>"
              id="wpdk-form-choose-label_<?php echo $item['id'] ?>"><?php echo $item['label'] ?></span>
            <input type="button"
                   class="wpdk-form-choose-button"
                   value="..."
                   id="wpdk-form-choose-button_<?php echo $item['id'] ?>"/>
        </span>
    <?php
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Commodity services
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Crea uno o più attributi data da inserire in un tag HTML. Gli attributi data sono molto utilizzati di recente
     * e permettono di memorizzare valori all'interno di tag qualsiasi. Questi attributi hanno la forma: data-[valore],
     * ad esempio:
     *
     *     <div data-my_slot="66"></div>
     *
     * Da jQuery, ad esempio, basta usare - per leggere o scrivere:
     *
     *     $( 'div' ).data( 'my_slot' );
     *
     *
     * @param array $item SDF element descriptor
     *
     *     $item['data'] = array(
     *         'my_slot' => '66'
     *     );
     *
     * @return string
     */
    private static function _data( $item ) {
        $result = '';
        if ( isset( $item['data'] ) ) {
            if ( is_array( $item['data'] ) ) {
                $stack = array();
                foreach ( $item['data'] as $key => $value ) {
                    $stack[] = sprintf( 'data-%s="%s"', $key, $value );
                }
                $result = join( ' ', $stack );
            }
        }
        return $result;
    }

    /**
     * Crea uno o più attributi qualsiasi. Permette di aggiungere un attributo='valore' all'interno di un tag HTML.
     * Questo attrobuto, tutto sommato, potrebbe rendere inutili gli altri come class, id, title, etc... perché
     * gestiti in automatico da questo.
     * Tuttavia si perderebbe le specifiche relazioni che alcuni (come id o title) hanno nei diversi contesti.
     *
     *
     * @param array $item SDF element descriptor
     *
     *     $item['attrs'] = array(
     *         'my_slot' => '66'
     *     );     *
     *
     * @return string
     */
    private static function _attrs( $item ) {
        $result = '';
        if ( isset( $item['attrs'] ) ) {
            if ( is_array( $item['attrs'] ) ) {
                $stack = array();
                foreach ( $item['attrs'] as $key => $value ) {
                    $stack[] = sprintf( '%s="%s"', $key, $value );
                }
                $result = join( ' ', $stack );
            }
        }
        return $result;
    }

    /**
     * Corregge un nome di un elemento quando questo viene usato nella sua forma di array. Nell'attributo name di un
     * campo di una form è possibile avere:
     *
     *     <input name="my_name[]" />
     *
     * In questo caso la $_POST['my_name'] sarà un array. Questo metodo viene usato quando si vuole associare un id ad
     * un campo a cui è stato passato solo il 'name'.
     *
     *
     * @param string $name
     * @param bool   $index Se diverso da false, alla stringa del risultato sarà aggiunto un progressivo
     *
     * @sa _id()
     *
     * @return string
     */
    private static function _sanitize_name_array( $name, $index = false ) {
        $result = $name;
        if ( substr( $result, -2 ) == '[]' ) {
            $result = substr( $result, 0, strlen( $result ) - 2 );
            if ( false !== $index ) {
                $result .= $index;
            }
        }
        return $result;
    }

    /**
     * Elabora l'attriuto before
     *
     *
     * @param array $item SDF element descriptor
     *
     * @return string
     */
    private static function _before( $item ) {
        $before = isset( $item['before'] ) ? $item['before'] : '';
        return $before;
    }

    /**
     * Elabora l'attriuto after
     *
     *
     * @param array $item SDF element descriptor
     *
     * @return string
     */
    private static function _after( $item ) {
        $after = isset( $item['after'] ) ? $item['after'] : '';
        return $after;
    }

    /**
     *
     * @param $item
     *
     * @return string
     */
    private static function _class( $item, $except = false ) {
        /* Queste sono le classi standard applicati per tipologia di campo. */
        $classes = array(
            WPDK_FORM_FIELD_TYPE_BUTTON     => 'wpdk-form-button',
            WPDK_FORM_FIELD_TYPE_CHECKBOX   => 'wpdk-form-checkbox',
            WPDK_FORM_FIELD_TYPE_CHOOSE     => '',
            WPDK_FORM_FIELD_TYPE_CUSTOM     => 'wpdk-form-custom',
            WPDK_FORM_FIELD_TYPE_DATE       => 'wpdk-form-input wpdk-form-date wpdk-form-has-button-clear-left',
            WPDK_FORM_FIELD_TYPE_DATETIME   => 'wpdk-form-input wpdk-form-datetime wpdk-form-has-button-clear-left',
            WPDK_FORM_FIELD_TYPE_EMAIL      => 'wpdk-form-input wpdk-form-email',
            WPDK_FORM_FIELD_TYPE_FILE       => 'wpdk-form-file',
            WPDK_FORM_FIELD_TYPE_HIDDEN     => '',
            WPDK_FORM_FIELD_TYPE_LABEL      => '',
            WPDK_FORM_FIELD_TYPE_NUMBER     => 'wpdk-form-input wpdk-form-number',
            WPDK_FORM_FIELD_TYPE_PASSWORD   => 'wpdk-form-input wpdk-form-password',
            WPDK_FORM_FIELD_TYPE_PHONE      => 'wpdk-form-input wpdk-form-phone',
            WPDK_FORM_FIELD_TYPE_RADIO      => 'wpdk-form-radio',
            WPDK_FORM_FIELD_TYPE_SELECT     => 'wpdk-form-select',
            WPDK_FORM_FIELD_TYPE_SWIPE      => 'wpdk-form-swipe',
            WPDK_FORM_FIELD_TYPE_SUBMIT     => '',
            WPDK_FORM_FIELD_TYPE_TEXT       => 'wpdk-form-input',
            WPDK_FORM_FIELD_TYPE_TEXTAREA   => 'wpdk-form-textarea',

            WPDK_FORM_FIELD_TYPE_ALERT      => 'wpdk-form-alert',
            WPDK_FORM_FIELD_TYPE_PARAGRAPH  => 'wpdk-form-paragraph',
            WPDK_FORM_FIELD_SECTION         => '',
        );

        $stack = array();

        /* Controlli aggiuntivi in base al alcune varianti. */
        if ( isset( $item['class'] ) ) {
            if ( is_array( $item['class'] ) ) {
                $stack = $item['class'];
            } else {
                $stack[] = $item['class'];
            }
        }

        /* Per campi require, controllo da javascript ma è da fare */
        if ( isset( $item['required'] ) ) {
            $stack[] = 'wpdk-form-required';
        }

        /* Per tipo, standard. */
        if ( isset( $item['type'] ) ) {
            $stack[] = $classes[$item['type']];

            if( $item['type'] == WPDK_FORM_FIELD_TYPE_SELECT && isset( $item['size'] ) ) {
                $stack[] = 'wpdk-form-select-size';
            }

        }

        if ( !$except && ( !empty( $item['help'] ) || !empty( $item['title'] ) ) ) {
            $stack[] = 'wpdk-tooltip';
            $stack[] = 'wpdk-has-help';
        }

        return implode( ' ', $stack );
    }

    /**
     *
     * @param $item
     *
     * @return string
     */
    private static function _id( $item ) {
        static $index = 1;

        if ( !isset( $item['id'] ) ) {
            $id = '';
            if ( isset( $item['name'] ) ) {
                $id = isset( $item['id'] ) ? $item['id'] : self::_sanitize_name_array( $item['name'], $index );
                $index++;
            }
            return $id;
        } else {
            return $item['id'];
        }
    }

    /**
     *
     * @param $item
     *
     * @return string
     */
    private static function _title( $item ) {
        $title = '';
        if ( !isset( $item['title'] ) ) {
            if ( isset( $item['label'] ) ) {
                $title = trim( $item['label'] );
            }
        } else {
            $title = trim( $item['title'] );
        }

        if ( !empty( $item['help'] ) ) {
            $title = $item['help'];
        }

        return $title;
    }

    /**
     *
     * @param $item
     *
     * @return int
     */
    private static function _size( $item ) {
        if ( isset( $item['size'] ) ) {
            $size = $item['size'];
        } else {
            $sizesForType = array(
                WPDK_FORM_FIELD_TYPE_EMAIL        => 30,
                WPDK_FORM_FIELD_TYPE_PASSWORD     => 30,
                WPDK_FORM_FIELD_TYPE_TEXT         => 30,
                WPDK_FORM_FIELD_TYPE_DATE         => 10,
                WPDK_FORM_FIELD_TYPE_DATETIME     => 16,
                WPDK_FORM_FIELD_TYPE_NUMBER       => 10,
                WPDK_FORM_FIELD_TYPE_PHONE        => 10,
            );
            $size = isset( $sizesForType[$item['type']] ) ? $sizesForType[$item['type']] : 16;
        }
        return $size;
    }

    /**
     *
     * @param $item
     */
    private static function _locked( $item ) {
        if ( isset( $item['locked'] ) ) : ?>
        <span title="This field is locked for your security. However you can unlock just by click here." class="wpdk-form-locked wpdk-tooltip"></span>
        <?php endif;
    }

    /**
     *
     * @param      $item
     * @param bool $wrap
     */
    private static function _append( $item, $wrap = true ) {
        if ( isset( $item['append'] ) ) : ?>
        <?php if ( $wrap ) : ?>
        <span class="wpdk-form-append <?php echo self::_sanitize_name_array( $item['name'] ) ?>">
            <?php endif; ?>
            <?php echo $item['append'] ?>
            <?php if ( $wrap ) : ?>
        </span>
            <?php endif; ?>
        <?php endif;
    }

    /**
     *
     * @param $item
     *
     * @deprecated
     */
    private static function _help( $item, $echo = true ) {
        _deprecated_function( __CLASS__ . ' ' . __FUNCTION__, '0.5', 'Use tooltip instead' );

        if ( !empty( $item['help'] ) ) {
            $help          = $item['help'];
//            $help_position = !empty( $item['help_position'] ) ? $item['help_position'] : 'top';
//            $help_width    = !empty( $item['help_width'] ) ? $item['help_width'] : '300px';
            $html          = <<< HTML
    <span title="{$help}" class="wpdk-form-icon-help wpdk-tooltip"></span>
HTML;
            if ( $echo ) {
                echo $html;
            } else {
                return $html;
            }
        }
    }

    /**
     *
     * @param $item
     *
     * @return string
     */
    private static function _value( $item ) {
        $value = isset( $item['value'] ) ? $item['value'] : '';
        return $value;
    }

    /**
     *
     * @param $item
     *
     * @return string
     */
    private static function _placeholder( $item ) {
        $placeholder = isset( $item['placeholder'] ) ? 'placeholder="' . $item['placeholder'] . '"' : '';
        return $placeholder;
    }

    /**
     *
     * @param $item
     *
     * @return string
     */
    private static function _readonly( $item ) {
        $readonly = '';
        if ( isset( $item['readonly'] ) || isset( $item['locked'] ) ) { //|| $item['type'] == 'date') {
            $readonly = 'readonly="readonly"';
        }
        return $readonly;
    }

    /**
     *
     * @param      $item
     * @param null $value
     *
     * @return string
     */
    private static function _checked( $item, $value = null ) {
        $checked = '';
        $value   = is_null( $value ) ? isset( $item['value'] ) ? $item['value'] : '' : $value;
        if ( isset( $item['checked'] ) ) {
            if ( $item['checked'] == $value ) {
                $checked = 'checked="checked"';
            }
        }
        return $checked;
    }

    /**
     *
     * @param $item
     *
     * @return string
     */
    private static function _clear( $item ) {
        $result = '<span class="wpdk-form-clear-left"></span>';
        if ( isset( $item['not null'] ) && !$item['not null'] ) {
            $result = '';
        }
        return $result;
    }

    /**
     *
     * @param $item
     *
     * @return string
     */
    private static function _afterlabel( $item ) {
        $default = ( $item['type'] != WPDK_FORM_FIELD_TYPE_CHECKBOX ) ? ':' : '';
        $result  = isset( $item['afterlabel'] ) ? $item['afterlabel'] : $default;
        return $result;
    }

    /**
     *
     * @param $item
     *
     * @return string
     */
    private static function _beforelabel( $item ) {
        $result = isset( $item['beforelabel'] ) ? $item['beforelabel'] : '';
        return $result;
    }

    /**
     *
     * @param $item
     *
     * @return string
     */
    private static function _inline( $item ) {
        $result = '';
        if ( isset( $item['inline'] ) ) {
            $result = wpdk_is_bool( $item['inline'] ) ? 'wpdk-form-label-inline' : '';
        }
        return $result;
    }

    /**
     *
     * @param $item
     *
     * @return string
     */
    private static function _style( $item ) {
        $style = isset( $item['style'] ) ? $item['style'] : '';
        return sprintf( 'style="%s"', $style );
    }

    /**
     *
     * @param $item
     */
    private static function _label( $item ) {
        $class = self::_class( $item, true );
        $id    = self::_id( $item );

        if ( isset( $item['label'] ) ) {
            $afterlabel  = self::_afterlabel( $item );
            $beforelabel = self::_beforelabel( $item ); ?>
        <label class="wpdk-form-label <?php echo $class ?> <?php echo self::_sanitize_name_array( $item['name'] ) ?>"
               for="<?php echo $id ?>"><?php echo $beforelabel ?><?php echo $item['label'] ?><?php echo $afterlabel ?></label>
        <?php
        }
    }
}

/// @endcond