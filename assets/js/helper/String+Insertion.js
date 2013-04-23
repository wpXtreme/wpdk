/**
 * Estende la classe String
 *
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            09/12/11
 * @version         1.0
 *
 */

String.prototype.insertAt = function ( loc, strChunk ) {
    return (this.valueOf().substr( 0, loc )) + strChunk + (this.valueOf().substr( loc ))
};
