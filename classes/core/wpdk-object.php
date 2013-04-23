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
 * ## Overview
 *
 * Questa classe astratta - se ereditata - permette di aggiungere una migliore gestione delle proprietà, che su PHP non
 * è propriamente rigorosa. Grazie ai metodi magici del PHP, come __get() e __set(), permette di simulare la gestione
 * delle proprietà di Objective-C. Data quindi una variabile pubblica, nel momento che si prova ad impostarla, verrà
 * cercato il metodo accessorio set[Nome proprietà]; stessa cosa per la lettura.
 *
 * @class              WPDKObject
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.1.1
 * @note               NOT USED YET
 *
 */
class WPDKObject {

    /**
     * @brief Get accessor
     */
    public function __get( $name ) {
        //NSLog("%s::%s - %s", __CLASS__, __FUNCTION__, $name);

        if ( method_exists( $this, ( $method = 'get' . ucfirst( $name ) ) ) ) {
            return $this->$method();
        } else {
            return $this->$name;
        }
    }

    /// Utility property
    public function __isset( $name ) {
        if ( method_exists( $this, ( $method = 'isset' . ucfirst( $name ) ) ) ) {
            return $this->$method();
        } else {
            return;
        }
    }

    /**
     * @brief Set accessor
     */
    public function __set( $name, $value ) {
        if ( method_exists( $this, ( $method = 'set' . ucfirst( $name ) ) ) ) {
            $this->$method( $value );
        }
    }

    /**
     * @brief Utility property
     */
    public function __unset( $name ) {
        if ( method_exists( $this, ( $method = 'unset' . ucfirst( $name ) ) ) ) {
            $this->$method();
        }
    }

}

/// @endcond
