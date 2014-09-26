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
   * TELIZE end point api http://www.telize.com/
   * Used to get geocoding information by IP address
   */
  const TELIZE_END_POINT = 'http://www.telize.com/geoip/';

  /**
   * Google Maps - used for reverse geocoding
   */
  const GOOGLE_REVERSE_GEOCODIND = 'http://maps.googleapis.com/maps/api/geocode/json?latlng=';

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

    // Build endpoint API
    $endpoint = self::TELIZE_END_POINT . $ip;

    $response = wp_remote_get( $endpoint );

    // Dead connection
    if ( 200 != wp_remote_retrieve_response_code( $response ) ) {
      return false;
    }

    $body = wp_remote_retrieve_body( $response );

    return (array) json_decode( $body );

  }

  /**
   * Return an array with reverse geocoding information.
   *
   * @param float $lat Latitude value.
   * @param float $lng Longitude value.
   *
   * @return array
   */
  public function reverseGeocodingWithLatLng( $lat, $lng )
  {

    // Build the endpoit
    $endpoint = sprintf( '%s%s,%s', self::GOOGLE_REVERSE_GEOCODIND, $lat, $lng );

    $response = wp_remote_get( $endpoint );

    // Dead connection
    if ( 200 != wp_remote_retrieve_response_code( $response ) ) {
      return false;
    }

    $body = wp_remote_retrieve_body( $response );

    $result = (array) json_decode( $body );

    return $result['results'];
  }

  /**
   * Return a single property/type.
   *
   * @param array  $reverse_geocoding The array with reverse geocoding information retuned by
   *                                  `reverseGeocodingWithLatLng()`
   * @param string $type              The type.
   * @param string $property          Optional. Default 'long_name'
   *
   * @return mixed
   */
  private function getWithType( $reverse_geocoding, $type, $property = 'long_name' )
  {
    foreach ( $reverse_geocoding as $object ) {

      //WPXtreme::log( $object );

      foreach ( $object->address_components as $address_components ) {
        if ( in_array( $type, $address_components->types ) ) {
          return $address_components->$property;
        }
      }
    }
  }

  /**
   * Return the route.
   *
   * @param array $reverse_geocoding The array with reverse geocoding information retuned by
   *                                 `reverseGeocodingWithLatLng()`
   *
   * @return string
   */
  public function route( $reverse_geocoding )
  {
    return $this->getWithType( $reverse_geocoding, 'route' );
  }

  /**
   * Return the street number.
   *
   * @param array $reverse_geocoding The array with reverse geocoding information retuned by
   *                                 `reverseGeocodingWithLatLng()`
   *
   * @return string
   */
  public function street_number( $reverse_geocoding )
  {
    return $this->getWithType( $reverse_geocoding, 'street_number' );
  }

}