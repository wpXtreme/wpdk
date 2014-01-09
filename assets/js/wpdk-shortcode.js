/**
 * WPDK Shortcode TinyMCE buttons
 *
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-08
 * @version         1.0.0
 * @since           1.4.8
 *
 */
(function ()
{
  tinymce.create( 'tinymce.plugins.WPDKShortcodes', {
    /**
     * Initializes the plugin, this will be executed after the plugin has been created.
     * This call is done before the editor instance has finished it's initialization so use the onInit event
     * of the editor instance to intercept that event.
     *
     * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
     * @param {string} url Absolute URL to where the plugin is located.
     */
    init : function ( ed, url )
    {
      if ( !empty( _WPDKShortcodes.buttons ) ) {

        // Loop in buttons
        for ( var i in  _WPDKShortcodes.buttons ) {
          var id = _WPDKShortcodes.buttons[i].id;

          // Remove object property for addButton() method
          delete( _WPDKShortcodes.buttons[i].id );
          ed.addButton( id, _WPDKShortcodes.buttons[i] );
        }
      }

      ed.addButton( 'example', {
        text    : 'My button',
        icon    : false,
        onclick : function ()
        {
          // Open window
          ed.windowManager.open( {
            title    : 'Example plugin',
            body     : [
              {type : 'textbox', name : 'title', label : 'Title'}
            ],
            onsubmit : function ( e )
            {
              // Insert content when the window form is submitted
              ed.insertContent( 'Title: ' + e.data.title );
            }
          } );
        }
      });
    },

    /**
     * Creates control instances based in the incomming name. This method is normally not
     * needed since the addButton method of the tinymce.Editor class is a more easy way of adding buttons
     * but you sometimes need to create more complex controls like listboxes, split buttons etc then this
     * method can be used to create those.
     *
     * @param {String} n Name of the control to create.
     * @param {tinymce.ControlManager} cm Control manager to use inorder to create new control.
     * @return {tinymce.ui.Control} New control instance or null if no control was created.
     */
    createControl : function ( n, cm )
    {
      return null;
    },

    /**
     * Returns information about the plugin as a name/value array.
     * The current keys are longname, author, authorurl, infourl and version.
     *
     * @return {Object} Name/value array containing information about the plugin.
     */
    getInfo : function ()
    {
      return {
        longname  : 'WPDK Buttons',
        author    : 'wpXtreme, Inc.',
        authorurl : 'https://wpxtre.me',
        infourl   : 'https://wpxtre.me',
        version   : '1.0.1'
      };
    }
  } );

  // Register plugin
  tinymce.PluginManager.add( 'WPDKShortcodes', tinymce.plugins.WPDKShortcodes );
})();