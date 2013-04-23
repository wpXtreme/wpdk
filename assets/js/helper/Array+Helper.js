/**
 * Array extends
 *
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            09/12/11
 * @version         1.0
 *
 */

// Only add this implementation if one does not already exist.
if (Array.prototype.slice == null) Array.prototype.slice = function ( start, end ) {
    if ( start < 0 ) start = this.length + start; //'this' refers to the object to which the prototype is applied
    if ( end == null ) end = this.length;
    else if ( end < 0 ) end = this.length + end;
    var newArray = [];
    for ( var ct = 0, i = start; i < end; i++ ) newArray[ct++] = this[i];
    return newArray;
};