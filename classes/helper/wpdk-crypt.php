<?php
/**
 * Utility class for crypting, password and unique code
 *
 * @class              WPDKCrypt
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2014-01-08
 * @version            0.9.1
 *
 */

class WPDKCrypt extends WPDKObject {

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $__version
   */
  public $__version = '0.9.1';

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
  public static function uniqcode( $prefix = '', $posfix = '', $max_length = 64 )
  {
    $uniqcode = uniqid( $prefix ) . $posfix;
    if ( ( $uniqcode_len = strlen( $uniqcode ) ) > $max_length ) {
      /* Catch from end */
      return substr( $uniqcode, -$max_length );
    }
    return $uniqcode;
  }

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
  static function randomAlphaNumber( $len = 8, $extra = '#,!,.' )
  {
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

  /**
   * Simple custom crypt/decrypt with salt. Return a encrypted or decrypted string.
   *
   * @brief Crypt/Decrypt
   * @since 1.5.6
   *
   * @param string $data    Your data to crypt or decrypt.
   * @param string $salt    Any random salt.
   * @param bool   $encrypt Optional. FALSE to decrypt. Default TRUE.
   *
   * @return string
   */
  public static function crypt_decrypt( $data, $salt, $encrypt = true )
  {
    $key    = array();
    $result = '';
    $state  = array();
    $salt   = md5( str_rot13( $salt ) );
    $len    = strlen( $salt );

    if ( $encrypt ) {
      $data = str_rot13( $data );
    }
    else {
      $data = base64_decode( $data );
    }

    $ii = -1;

    while ( ++$ii < 256 ) {
      $key[ $ii ]   = ord( substr( $salt, ( ( $ii % $len ) + 1 ), 1 ) );
      $state[ $ii ] = $ii;
    }

    $ii = -1;
    $j  = 0;

    while ( ++$ii < 256 ) {
      $j = ( $j + $key[ $ii ] + $state[ $ii ] ) % 255;
      $t = $state[ $j ];

      $state[ $ii ] = $state[ $j ];
      $state[ $j ]  = $t;
    }

    $len = strlen( $data );
    $ii  = -1;
    $j   = 0;
    $k   = 0;

    while ( ++$ii < $len ) {
      $j = ( $j + 1 ) % 256;
      $k = ( $k + $state[ $j ] ) % 255;
      $t = $key[ $j ];

      $state[ $j ] = $state[ $k ];
      $state[ $k ] = $t;

      $x = $state[ ( ( $state[ $j ] + $state[ $k ] ) % 255 ) ];
      $result .= chr( ord( $data[ $ii ] ) ^ $x );
    }

    if ( $encrypt ) {
      $result = base64_encode( $result );
    }
    else {
      $result = str_rot13( $result );
    }

    return $result;
  }
}
