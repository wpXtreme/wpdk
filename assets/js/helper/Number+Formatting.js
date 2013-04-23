/**
 * Estende la classe Number
 *
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            09/12/11
 * @version         1.0
 *
 * @uses            String+Insertion.js
 *
 */

Number.prototype.toCurrency = function ( noFractions, currencySymbol, decimalSeparator, thousandsSeparator ) {
    var n, startAt, intLen;
    if ( currencySymbol == null ) currencySymbol = "$";
    if ( decimalSeparator == null ) decimalSeparator = ".";
    if ( thousandsSeparator == null ) thousandsSeparator = ",";
    n = this.round( noFractions ? 0 : 2, true, decimalSeparator );
    intLen = n.length - (noFractions ? 0 : 3);
    if ( (startAt = intLen % 3) == 0 ) startAt = 3;
    for ( var i = 0, len = Math.ceil( intLen / 3 ) - 1; i < len; i++ )n = n.insertAt( i * 4 + startAt, thousandsSeparator );
    return currencySymbol + n;
};
Number.prototype.toInteger = function ( thousandsSeparator ) {
    var n, startAt, intLen;
    if ( thousandsSeparator == null ) thousandsSeparator = ",";
    n = this.round( 0, true );
    intLen = n.length;
    if ( (startAt = intLen % 3) == 0 ) startAt = 3;
    for ( var i = 0, len = Math.ceil( intLen / 3 ) - 1; i < len; i++ )n = n.insertAt( i * 4 + startAt, thousandsSeparator );
    return n;
};
Number.prototype.round = function ( decimals, returnAsString, decimalSeparator ) {
    //Supports 'negative' decimals, e.g. myNumber.round(-3) rounds to the nearest thousand
    var n, factor, breakPoint, whole, frac;
    if ( !decimals ) decimals = 0;
    factor = Math.pow( 10, decimals );
    n = (this.valueOf() + "");         //To get the internal value of an Object, use the valueOf() method
    if ( !returnAsString ) return Math.round( n * factor ) / factor;
    if ( !decimalSeparator ) decimalSeparator = ".";
    if ( n == 0 ) return "0." + ((factor + "").substr( 1 ));
    breakPoint = (n = Math.round( n * factor ) + "").length - decimals;
    whole = n.substr( 0, breakPoint );
    if ( decimals > 0 ) {
        frac = n.substr( breakPoint );
        if ( frac.length < decimals ) frac = (Math.pow( 10, decimals - frac.length ) + "").substr( 1 ) + frac;
        return whole + decimalSeparator + frac;
    }
    else return whole + ((Math.pow( 10, -decimals ) + "").substr( 1 ));
};