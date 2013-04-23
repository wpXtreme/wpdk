<?php
/// @cond private

/* WPDKDateTime Test Cases */
class WPDKDateTimeTest extends PHPUnit_Framework_TestCase {

  function setUp() {
    global $wpdb;
  }

  //
  // CONTEXT: FormatFromFormat
  //
  function test_formatFromFormat_basic() {

    $result = WPDKDateTime::formatFromFormat( "201112241412" );
    $this->assertEquals( $result, '12/24/2011 14:12' );
  }

  function test_formatFromFormat_with_informat() {

    $result = WPDKDateTime::formatFromFormat( "121424122011", 'iHdmY' );
    $this->assertEquals( $result, '12/24/2011 14:12' );
  }

  function test_format_from_format_with_inoutformat() {

    $result = WPDKDateTime::formatFromFormat( "121424122011", 'iHdmY', 'd-m-Y H::i' );
    $this->assertEquals( $result, '24-12-2011 14::12' );
  }


  //
  // CONTEXT: date2MySql
  //
  function test_date2mysql_basic() {
    $result = WPDKDateTime::date2MySql( "12/24/2011" );
    $this->assertEquals( $result, '2011-12-24' );
  }

  function test_date2mysql_with_informat() {
    $result = WPDKDateTime::date2MySql( "12-24-2011", 'm-d-Y' );
    $this->assertEquals( $result, '2011-12-24' );
  }

  //
  // CONTEXT: dateTime2MySql
  //
  function test_dateTime2MySql_basic() {
    $result = WPDKDateTime::dateTime2MySql( "12/24/2011 10:20:15" );
    $this->assertEquals( $result, '2011-12-24 10:20:15' );
  }

  function test_dateTime2MySql_with_informat() {
    $result = WPDKDateTime::dateTime2MySql( "12-24-2011/10:20:15", 'm-d-Y/H:i:s' );
    $this->assertEquals( $result, '2011-12-24 10:20:15' );
  }


  //
  // CONTEXT: stripSecondsFromTime
  //
  function test_stripSecondsFromTime_basic() {
    $result = WPDKDateTime::stripSecondsFromTime( '20:15:12' );
    $this->assertEquals( $result, '20:15' );
  }

}

/// @endcond