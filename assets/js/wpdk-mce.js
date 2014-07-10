/**
 * Manage the TinyMCE buttons.
 *
 * @class           WPDKDynamicTable
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-05-20
 * @version         1.0.0
 */

jQuery( function ( $ )
{
  "use strict";

  if ( typeof( window.WPDKMCE ) === 'undefined' ) {

    window.WPDKMCE = (function ()
    {

      console.log( 'WPDKMCE' );

      var _WPDKMCE = {
        openDialog     : _openDialog
      };

      /**
       * General init
       *
       * @private
       */
      function _init()
      {

        return _WPDKMCE;
      }

      /**
       * Open dialog to choose the registered shortcode
       *
       * @private
       */
      function _openDialog()
      {
        var dialog = new WPDKUIModalDialog( 'wpdk-shortcodes-dialog',
          'WPDK Shortcode Manager',
          'Content' );
        dialog.display();
      }

      return _init();

    })();

  }

} );