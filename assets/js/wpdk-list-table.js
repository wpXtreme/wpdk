/**
 * This class manage a WPDKListTbaleViewController
 *
 * @class           WPDKListTbaleViewController
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-04-24
 * @version         1.0.0
 */

jQuery( function ( $ )
{
  "use strict";

  if ( typeof( window.WPDKListTbaleViewController ) === 'undefined' ) {
    window.WPDKListTbaleViewController = (function ()
    {

      // Internal object
      var $t = {
        version : '1.0.0',
        init    : _init
      };

      /**
       * Return a singleton instance of WPDKDynamicTable class
       *
       * @returns {{}}
       */
      function _init()
      {

        // Ask confirm for delete
        _askConfirm();

        return $t;
      }

      /**
       * Ask user confirm before delete a row
       *
       * @param form_name
       * @private
       */
      function _askConfirm()
      {
        // Get the main form
        var $form = $( 'form' );

        // Prepare a message
        var message = "Warning!!\n\nAre you sure to delete permately the rows selected?\n\nThis operation is not reversible!!";

        // Ask a confirm if bulk action is delete
        $form.submit( function() {
          var action = $form.find( 'select[name="action"] option:selected' ).val();
          var action2 = $form.find( 'select[name="action2"] option:selected' ).val();

          if( 'action_delete' == action || 'action_delete' == action2 ) {
            return confirm( message );
          }
        });

        // Ask confirm on action link
        $( document ).on( 'click', '.row-actions span.action_delete a', function() {
          return confirm( message );
        } );
      }

      return _init();

    })();
  }

} );