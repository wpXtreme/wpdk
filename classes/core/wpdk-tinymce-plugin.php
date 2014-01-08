<?php
/**
 * Description
 *
 * ## Overview
 *
 * Description
 *
 * @class           WPDKEditorButton
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-08
 * @version         1.0.0
 *
 */
class WPDKEditorButton {

  /**
   * Unique button id
   *
   * @brief Button ID
   *
   * @var string $id
   */
  public $id = '';

  /**
   * Short description of button
   *
   * @brief Description
   *
   * @var string $title
   */
  public $title = '';

  /**
   * Any image URL, WPDKTagImage or WPDKGlyphIcon constant
   *
   * @brief Image
   *
   * @var string|WPDKTagImage $image
   */
  public $image = '';

  /**
   * Create an instance of WPDKEditorButton class
   *
   * @brief Construct
   *
   * @return WPDKEditorButton
   */
  public function __construct()
  {

  }
  
}

/**
 * Description
 *
 * ## Overview
 *
 * Description
 *
 * @class           WPDKEditorButtons
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-08
 * @version         1.0.0
 *
 */
class WPDKEditorButtons {

  /**
   * Create an instance of WPDKEditorButtons class
   *
   * @brief Construct
   *
   * @return WPDKEditorButtons
   */
  public function __construct()
  {
    if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
      add_filter( 'mce_buttons', array( $this, 'mce_buttons' ) );
      add_filter( 'mce_external_plugins', array( $this, 'mce_external_plugins' ) );
    }
  }
  
  /**
   * Description
   *
   * @brief Brief
   *
   * @param array $buttons
   * 
   * @return array
   */
  public function mce_buttons( $buttons) {}
  public function mce_external_plugins() {}

}

/**
 * Description
 *
 * ## Overview
 *
 * Description
 *
 * @class           WPDKTinyMCEPlugin
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-08
 * @version         1.0.0
 *
 */
class WPDKTinyMCEPlugin {

  public $name = '';
  public $description = '';
  public $author = '';
  public $author_url = '';
  public $url = '';
  public $version = '';

  /**
   * Create an instance of WPDKTinyMCEPlugin class
   *
   * @brief Construct
   *
   * @return WPDKTinyMCEPlugin
   */
  public function __construct( $name, $description, $version = '1.0.0' )
  {
    // @todo sanitize
    $this->name = $name;

    $this->description = $description;
    $this->version = $version;
  }

  public function html()
  {
    WPDKHTML::startCompress(); ?>
(function() {
    tinymce.create('tinymce.plugins.Wptuts', {
        /**
         * Initializes the plugin, this will be executed after the plugin has been created.
         * This call is done before the editor instance has finished it's initialization so use the onInit event
         * of the editor instance to intercept that event.
         *
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {

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
        createControl : function(n, cm) {
            return null;
        },

        /**
         * Returns information about the plugin as a name/value array.
         * The current keys are longname, author, authorurl, infourl and version.
         *
         * @return {Object} Name/value array containing information about the plugin.
         */
        getInfo : function() {
            return {
                longname : 'Wptuts Buttons',
                author : 'Lee',
                authorurl : 'http://wp.tutsplus.com/author/leepham',
                infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/example',
                version : "0.1"
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add( 'wptuts', tinymce.plugins.Wptuts );
})();
<?php

    return WPDKHTML::endJavascriptCompress();

  }

}