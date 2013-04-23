/**
 * Estende la classse Boolean
 *
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            09/12/11
 * @version         1.0
 *
 */

Boolean.prototype.XOR = function ( bool2 ) {
    var bool1 = this.valueOf();
    return (bool1 == true && bool2 == false) || (bool2 == true && bool1 == false);
    //return (bool1 && !bool2) || (bool2 && !bool1);
};