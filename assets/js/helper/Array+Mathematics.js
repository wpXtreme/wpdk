//This JavaScript library is copyright 2002 by Gavin Kistner and Refinery, Inc.
//Reuse or modification permitted provided the previous line is included.
//mailto:!@phrogz.net
//http://www.refinery.com/

/***************************************************************************************************
 * JavaScript Array Set Mathematics Library
 * version 1.2.1, April 26th, 2002  [IEMac5.1-/IEWin5.0-/OldNS .splice() replacement works properly]
 *
 * Methods: array1.union( array2 [,compareFunction] )
 *          array1.subtract( array2 [,compareFunction] )
 *          array1.intersect( array2 [,compareFunction] )
 *          array1.exclusion( array2 [,compareFunction] )
 *          array1.removeDuplicates( [compareFunction] )
 *
 *          array1.unsortedUnion( array2 [,compareFunction] )
 *          array1.unsortedSubtract( array2 [,compareFunction] )
 *          array1.unsortedIntersect( array2 [,compareFunction] )
 *          array1.unsortedExclusion( array2 [,compareFunction] )
 *          array1.unsortedRemoveDuplicates( [compareFunction] )
 *
 *
 * Notes:   All methods return a 'set' Array where duplicates have been removed.
 *
 *          The union(), subtract(), intersect(), and removeDuplicates() methods
 *          are faster than their 'unsorted' counterparts, but return a sorted set:
 *          var a = ['a','e','c'];
 *          var b = ['b','c','d'];
 *          a.unsortedUnion(b)  -->  'a','e','c','b','d'
 *          a.union(b)          -->  'a','b','c','d','e'
 *
 *          Calling any of the methods on an array whose element pairs cannot all be
 *          reliably ordered (objects for which a < b, a > b, and a==b ALL return false)
 *          will produce inaccurate results UNLESS the (usually) optional
 *          'compareFunction' parameter is passed. This should specify a custom
 *          comparison function, as required by the standard Array.sort(myFunc) method
 *          For example:
 *          var siblings = [ {name:'Dain'} , {name:'Chandra'} , {name:'Baird'} , {name:'Linden'} ];
 *          var brothers = [ {name:'Dain'} , {name:'Baird'} ];
 *          function compareNames(a,b){ return (a.name < b.name)?-1:(a.name > b.name)?1:0 }
 *          var sisters=siblings.unsortedSubtract(brothers, compareNames);
 *
 ***************************************************************************************************/


if (Array.prototype.splice && typeof([0].splice(0)) == "number") Array.prototype.splice = null;
if (!Array.prototype.splice) Array.prototype.splice = function ( ind, cnt ) {
    var len = this.length;
    var arglen = arguments.length;
    if ( arglen == 0 ) return ind;
    if ( typeof(ind) != "number" ) ind = 0;
    else if ( ind < 0 ) ind = Math.max( 0, len + ind );
    if ( ind > len ) {
        if ( arglen > 2 ) ind = len;
        else return [];
    }
    if ( arglen < 2 ) cnt = len - ind;
    cnt = (typeof(cnt) == "number") ? Math.max( 0, cnt ) : 0;
    var removeArray = this.slice( ind, ind + cnt );
    var endArray = this.slice( ind + cnt );
    len = this.length = ind;
    for ( var i = 2; i < arglen; i++ ) this[len++] = arguments[i];
    for ( var i = 0, endlen = endArray.length; i < endlen; i++ ) this[len++] = endArray[i];
    return removeArray;
};

//*** SORTED IMPLEMENTATIONS ***************************************************
Array.prototype.union = function ( a2, compareFunction ) {
    return this.concat( a2 ? a2 : null ).removeDuplicates( compareFunction );
};
Array.prototype.subtract = function ( a2, compareFunction ) {
    if ( !compareFunction ) compareFunction = null;
    var a1 = this.removeDuplicates( compareFunction );
    if ( !a2 ) return a1;
    var a2 = a2.removeDuplicates( compareFunction );
    var len2 = a2.length;
    if ( compareFunction ) {
        for ( var i = 0; i < a1.length; i++ ) {
            var src = a1[i], found = false, src;
            for ( var j = 0; j < len2 && compareFunction( src2 = a2[j], src ) != 1; j++ ) if ( compareFunction( src, src2 ) == 0 ) {
                found = true;
                break;
            }
            if ( found ) a1.splice( i--, 1 );
        }
    }
    else {
        for ( var i = 0; i < a1.length; i++ ) {
            var src = a1[i], found = false, src;
            for ( var j = 0; (j < len2) && (src >= (src2 = a2[j])); j++ ) if ( src2 == src ) {
                found = true;
                break;
            }
            if ( found ) a1.splice( i--, 1 );
        }
    }
    return a1;
};
Array.prototype.intersect = function ( a2, compareFunction ) {
    if ( !compareFunction ) compareFunction = null;
    var a1 = this.removeDuplicates( compareFunction );
    if ( !a2 ) return a1;
    var a2 = a2.removeDuplicates( compareFunction );
    var len2 = a2.length;
    if ( len2 < a1.length ) {
        var c = a2;
        a2 = a1;
        a1 = c;
        c = null;
        len2 = a2.length;
    }
    if ( compareFunction ) {
        for ( var i = 0; i < a1.length; i++ ) {
            var src = a1[i], found = false, src;
            for ( var j = 0; j < len2 && compareFunction( src2 = a2[j], src ) != 1; j++ ) if ( compareFunction( src, src2 ) == 0 ) {
                found = true;
                break;
            }
            if ( !found ) a1.splice( i--, 1 );
        }
    }
    else {
        for ( var i = 0; i < a1.length; i++ ) {
            var src = a1[i], found = false, src;
            for ( var j = 0; (j < len2) && (src >= (src2 = a2[j])); j++ ) if ( src2 == src ) {
                found = true;
                break;
            }
            if ( !found ) a1.splice( i--, 1 );
        }
    }
    return a1;
};
Array.prototype.removeDuplicates = function ( compareFunction ) {
    if ( !compareFunction ) compareFunction = null;
    var a1 = this.concat().sort( compareFunction );
    if ( compareFunction ) {
        for ( var i = 0; i < a1.length; i++ ) {
            var src = a1[i];
            for ( var j = i + 1; j < a1.length && compareFunction( a1[j], src ) == 0; j++ ) {
            }
            if ( j - 1 > i ) a1.splice( i + 1, j - i - 1 );
        }
    }
    else {
        for ( var i = 0; i < a1.length; i++ ) {
            var src = a1[i];
            for ( var j = i + 1; j < a1.length && a1[j] == src; j++ ) {
            }
            if ( j - 1 > i ) a1.splice( i + 1, j - i - 1 );
        }
    }
    return a1;
};
Array.prototype.exclusion = function ( a2, compareFunction ) {
    if ( !compareFunction ) compareFunction = null;
    var a1 = this.removeDuplicates( compareFunction );
    if ( !a2 ) return a1;
    return a1.subtract( a2, compareFunction ).concat( a2.subtract( a1, compareFunction ) ).sort( compareFunction );
};

//*** UNSORTED IMPLEMENTATIONS *************************************************
Array.prototype.unsortedUnion = function ( a2, compareFunction ) {
    if ( !compareFunction ) compareFunction = null;
    return this.concat( a2 ? a2 : null ).unsortedRemoveDuplicates( compareFunction );
};
Array.prototype.unsortedSubtract = function ( a2, compareFunction ) {
    if ( !compareFunction ) compareFunction = null;
    var a1 = this.unsortedRemoveDuplicates( compareFunction );
    if ( !a2 ) return a1;
    var subtrahend = a2.unsortedRemoveDuplicates( compareFunction );
    if ( compareFunction ) {
        for ( var i = 0; i < a1.length; i++ ) {
            var src = a1[i];
            for ( var j = 0, len = subtrahend.length; j < len; j++ ) if ( compareFunction( subtrahend[j], src ) == 0 ) {
                a1.splice( i--, 1 );
                break;
            }
        }
    }
    else {
        for ( var i = 0; i < a1.length; i++ ) {
            var src = a1[i];
            for ( var j = 0, len = subtrahend.length; j < len; j++ ) if ( subtrahend[j] == src ) {
                a1.splice( i--, 1 );
                break;
            }
        }
    }
    return a1;
};
Array.prototype.unsortedIntersect = function ( a2, compareFunction ) {
    if ( !compareFunction ) compareFunction = null;
    if ( !a2 ) return this.unsortedRemoveDuplicates( compareFunction );
    var a1 = this;
    var len2 = a2.length;
    a1 = a1.unsortedRemoveDuplicates( compareFunction );
    if ( compareFunction ) {
        for ( var i = 0; i < a1.length; i++ ) {
            var src = a1[i];
            for ( var j = 0; j < len2; j++ ) if ( compareFunction( a2[j], src ) == 0 ) break;
            if ( j == len2 ) a1.splice( i--, 1 );
        }
    }
    else {
        for ( var i = 0; i < a1.length; i++ ) {
            var src = a1[i];
            for ( var j = 0; j < len2; j++ ) if ( a2[j] == src ) break;
            if ( j == len2 ) a1.splice( i--, 1 );
        }
    }
    return a1;
};
Array.prototype.unsortedRemoveDuplicates = function ( compareFunction ) {
    var a1 = this.concat();
    if ( compareFunction ) {
        for ( var i = 0; i < a1.length; i++ ) {
            var src = a1[i];
            for ( var j = i + 1; j < a1.length; j++ ) if ( compareFunction( a1[j], src ) == 0 ) a1.splice( j, 1 );
        }
    }
    else {
        for ( var i = 0; i < a1.length; i++ ) {
            var src = a1[i];
            for ( var j = i + 1; j < a1.length; j++ ) if ( a1[j] == src ) a1.splice( j--, 1 );
        }
    }
    return a1;
};
Array.prototype.unsortedExclusion = function ( a2, compareFunction ) {
    if ( !compareFunction ) compareFunction = null;
    var a1 = this.unsortedRemoveDuplicates( compareFunction );
    if ( !a2 ) return a1;
    return a1.unsortedSubtract( a2, compareFunction ).concat( a2.unsortedSubtract( a1, compareFunction ) ).sort( compareFunction );
};