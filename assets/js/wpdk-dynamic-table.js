/**
 * This class manage a WPDKDynamicTable
 *
 * @class           WPDKDynamicTable
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-22
 * @version         1.0.2
 */

jQuery( function ( $ )
{
  "use strict";

  if ( typeof( window.WPDKDynamicTable ) === 'undefined' ) {
    window.WPDKDynamicTable = (function ()
    {

      /**
       * This object
       *
       * @type {{version: string, init: _init}}
       * @private
       */
      var _WPDKDynamicTable = {
        version : '1.0.2',
        init    : _init
      };

      /**
       * Init
       *
       * @returns {{version: string, init: _init}}
       * @private
       */
      function _init()
      {
        var table = $( 'table.wpdk-dynamic-table' );
        if ( table.length ) {
          table.on( 'click', '.wpdk-dt-add-row', false, _addRow );
          table.on( 'click', '.wpdk-dt-delete-row', false, _deleteRow );

          // Sortable
          $( 'table.wpdk-dynamic-table-sortable tbody' ).sortable( {
            axis   : "y",
            cursor : "n-resize",
            start  : function ( e, ui ) {},
            stop   : function () {}
          } );
        }

        return _WPDKDynamicTable;
      }
      
      /**
       * Add a row to the dynamic table
       *
       * @private
       */
      function _addRow()
      {
        var table = $( this ).parents( 'table.wpdk-dynamic-table' );
        var clone = $( this ).parents( 'tr' ).prevAll( '.wpdk-dt-clone' ).clone();
        clone.removeClass( 'wpdk-dt-clone' ).appendTo( table );
        $( this ).hide().siblings( '.wpdk-dt-clone' ).removeClass( 'wpdk-dt-clone' ).show( function ()
        {
          /**
           * Fires to request a jQuery date picker refresh.
           */
          wpdk_do_action( WPDKUIComponents.REFRESH_JQUERY_DATEPICKER );

        } );
        return false;
      }

      /**
       * Delete a row from dynamic table
       *
       * @private
       */
      function _deleteRow()
      {
        $( this ).wpdkTooltip( 'hide' );
        $( this ).parents( 'tr' ).fadeOut( 300, function () { $( this ).remove(); } );
        return false;
      }

      return _WPDKDynamicTable.init();

    })();
  }

} );