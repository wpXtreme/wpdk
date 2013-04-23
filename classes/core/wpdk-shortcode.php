<?php
/// @cond private

/**
 * Shortcode class for extends a shortcode parent class.
 * You will use this class to extends a your own shortcode class.
 *
 *     class YouClass extends WPDKShortcode {}
 *
 * In this way you can access to `registerShortcodes` method
 *
 * @class              WPDKShortcode
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.1
 * @since              0.9
 *
 */

class WPDKShortcode {

    /**
     * Create an instance of WPDKShortcode class
     *
     * @brief Construct
     *
     * @return WPDKShortcode
     */
    public function __construct() {
        $this->registerShortcodes();
    }

    /**
     * Register all shorcode in shortcodes() array
     *
     * @brief Register shortcode
     */
    public function registerShortcodes() {
        $shortcodes = $this->shortcodes();
        foreach ( $shortcodes as $shortcode => $to_register ) {
            if ( $to_register ) {
                add_shortcode( $shortcode, array( $this, $shortcode ) );
            }
        }
    }

    /**
     * Return a Key value pairs array with key as shortcode name and value TRUE/FALSE for turn on/off the shortcode.
     *
     * @brief List of allowed shorcode
     *
     * @return array Shortcode array
     */
    protected function shortcodes() {
        /* To override. */
        return array();
    }

} // class WPDKShortcode

/// @endcond