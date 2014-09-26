<?php

/**
 * Geo-localization class utility helper.
 *
 * @class           WPDKGeo
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-09-25
 * @version         1.0.0
 * @since           1.5.18
 *
 */
class WPDKGeo {

  /**
   * TELIZE end point api
   * http://www.telize.com/
   */
  const TELIZE_END_POINT = 'http://www.telize.com/geoip/';

  /**
   * Return a singleton instance of WPDKGeo class
   *
   * @brief Singleton
   *
   * @return WPDKGeo
   */
  public static function init()
  {
    static $instance = null;
    if ( is_null( $instance ) ) {
      $instance = new self();
    }

    return $instance;
  }

  /**
   * Create an instance of WPDKGeo class
   *
   * @brief Construct
   *
   * @return WPDKGeo
   */
  public function __construct()
  {
  }

  /**
   * Return the geo IP information by ip address.
   *
   *    array(17) {
   *      ["longitude"]=> float(12.4833)
   *      ["latitude"]=> float(41.9)
   *      ["asn"]=> string(6) "AS3269"
   *      ["offset"]=> string(1) "2"
   *      ["ip"]=> string(12) "87.3.222.157"
   *      ["area_code"]=> string(1) "0"
   *      ["continent_code"]=> string(2) "EU"
   *      ["dma_code"]=> string(1) "0"
   *      ["city"]=> string(4) "Rome"
   *      ["timezone"]=> string(11) "Europe/Rome"
   *      ["region"]=> string(5) "Lazio"
   *      ["country_code"]=> string(2) "IT"
   *      ["isp"]=> string(21) "Telecom Italia S.p.a."
   *      ["postal_code"]=> string(5) "00141"
   *      ["country"]=> string(5) "Italy"
   *      ["country_code3"]=> string(3) "ITA"
   *      ["region_code"]=> string(2) "07"
   *    }
   *
   * @brief GeoIP
   *
   * @param string $ip Optional. The ip address or empty for `$_SERVER['REMOTE_ADDR']`,
   *
   * @return array|bool
   */
  public function geoIP( $ip = '' )
  {
    // Get current ip
    $ip = empty( $ip ) ? $_SERVER['REMOTE_ADDR'] : $ip;

    $response = wp_remote_get( self::TELIZE_END_POINT . $ip );

    // Dead connection
    if ( 200 != wp_remote_retrieve_response_code( $response ) ) {
      return false;
    }

    $body = wp_remote_retrieve_body( $response );

    return json_decode( $body );

  }

}