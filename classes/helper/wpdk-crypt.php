<?php
/**
 * Utility class for crypting, password and unique code
 *
 * @class              WPDKCrypt
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */

class WPDKCrypt {

    // -----------------------------------------------------------------------------------------------------------------
    // Unique code generator
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return a $max_length char unique code (in hexdecimal) with optional prefix and postfix, keep the length at $max_length.
     *
     * @brief Generatean unique code
     *
     * @param string $prefix     Optional
     * @param string $posfix     Optional
     * @param int    $max_length Length of result, default 64
     *
     * @return string
     */
    static function uniqcode( $prefix = '', $posfix = '', $max_length = 64 ) {
        $uniqcode = uniqid( $prefix ) . $posfix;
        if ( ( $uniqcode_len = strlen( $uniqcode ) ) > $max_length ) {
            /* Catch from end */
            return substr( $uniqcode, -$max_length );
        }
        return $uniqcode;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Random generator
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Return a random string with alfa number characters.
     *
     * @brief Generate a random code
     *
     * @param int    $len   Length of result , default 8
     * @param string $extra Extra characters, default = '#,!,.'
     *
     * @return string
     */
    static function randomAlphaNumber( $len = 8, $extra = '#,!,.' ) {
        $alfa = 'a,b,c,d,e,f,g,h,i,j,k,l,m,n,o,p,q,r,s,t,u,v,w,x,y,z,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z';
        $num  = '0,1,2,3,4,5,6,7,8,9';
        if ( $extra != '' ) {
            $num .= ',' . $extra;
        }
        $alfa = explode( ',', $alfa );
        $num  = explode( ',', $num );
        shuffle( $alfa );
        shuffle( $num );
        $misc = array_merge( $alfa, $num );
        shuffle( $misc );
        $result = substr( implode( '', $misc ), 0, $len );

        return $result;
    }
}
