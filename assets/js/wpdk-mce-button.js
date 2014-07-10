/**
 * Manage the TinyMCE buttons.
 *
 * @class           WPDKDynamicTable
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-05-20
 * @version         1.0.0
 */

( function ()
{
  "use strict";

  tinymce.PluginManager.add( 'wpdk_shortcodes_button', function ( editor, url )
  {
    editor.addButton( 'wpdk_shortcodes_button', {
      icon    : 'wpdk-mce-button',
      onclick : function ()
      {
        editor.insertContent( 'WPExplorer.com is awesome!' );

        WPDKMCE.openDialog();
      }
    } );
  } );

} )();