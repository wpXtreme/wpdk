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
   * Any image URL, WPDKHTMLTagImg or WPDKGlyphIcon constant
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
  public function __construct( $id, $title, $image = '' )
  {
    // @todo sanitize
    $this->id    = $id;
    $this->title = $title;
    $this->image = $image;
  }

  /**
   * Return the part of script for button
   *
   * @brief Brief
   * @return string
   */
  public function html()
  {
    WPDKHTML::startCompress();
    ?>
    ed.addButton( '<?php echo $this->id ?>', {
    title : '<?php echo $this->title ?>',
    <?php echo $this->image() ?>
    });
    <?php
    return WPDKHTML::endJavascriptCompress();
  }

  /**
   * Return the right image format
   *
   * @brief Brief
   *
   * @return string
   */
  private function image()
  {
    if ( empty( $this->image ) ) {
      return;
    }

    if ( is_string( $this->image ) ) {
      $url = $this->image;
    }
    elseif ( is_a( $this->image, 'WPDKHTMLTagImg' ) ) {
      $url = $this->image->src;
    }

    return sprintf( 'image : "%s"', $url );
  }
  
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

  /**
   * A lowercase id
   *
   * @brief Plugin ID
   *
   * @var string $id
   */
  public $id = '';

  /**
   * A capitalize class extension name
   *
   * @brief Plugin name
   *
   * @var string $name
   */
  public $name = '';

  /**
   * Description
   *
   * @brief
   *
   * @var string $description
   */
  public $description = '';

  /**
   * Description
   *
   * @brief Author name
   *
   * @var string $author
   */
  public $author = '';

  /**
   * Description
   *
   * @brief Author URL
   *
   * @var string $author_url
   */
  public $author_url = '';

  /**
   * Description
   *
   * @brief Plugin url
   *
   * @var string $url
   */
  public $url = '';

  /**
   * Description
   *
   * @brief Version
   *
   * @var string $version
   */
  public $version = '';

  /**
   * Description
   *
   * @brief Buttons
   *
   * @var array $buttons
   */
  public $buttons = array();

  /**
   * Create an instance of WPDKTinyMCEPlugin class
   *
   * @brief Construct
   *
   * @return WPDKTinyMCEPlugin
   */
  public function __construct( $name, $description, $buttons, $version = '1.0.0' )
  {
    // @todo sanitize
    $this->id   = $name;
    $this->name = $name;

    $this->description = $description;
    $this->version     = $version;

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
  public function mce_buttons( $buttons )
  {
    if ( empty( $this->buttons ) ) {
      return $buttons;
    }

    /**
     * @var WPDKEditorButton $button
     */
    foreach ( $this->buttons as $button ) {
      array_push( $buttons, $button );
    }

    return $buttons;
  }

  public function mce_external_plugins( $plugin_array )
  {
    $plugin_array[$this->id] = '';
  }

  /**
   * Return the HTML (script) markup
   *
   * @brief Brief
   *
   * @return string
   */
  public function html()
  {
    WPDKHTML::startCompress(); ?>
    <script type="text/javascript">
(function ()
{
  tinymce.create( 'tinymce.plugins.<?php echo $this->name ?>', {
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
      <?php ?>
      ed.addButton( 'dropcap', {
        title : 'DropCap',
        cmd   : 'dropcap',
        image : url + '/dropcap.jpg'
      } );
      <?php ?>

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
        longname  : '<?php echo $this->description ?>',
        author    : '<?php echo $this->author ?>',
        authorurl : '<?php echo $this->author_url ?>',
        infourl   : '<?php echo $this->url ?>',
        version   : '<?php echo $this->version ?>'
      };
    }
  } );

  // Register plugin
  tinymce.PluginManager.add( '<?php echo $this->id ?>', tinymce.plugins.<?php echo $this->name ?> );
})();
</script>
<?php

    return WPDKHTML::endJavascriptCompress();

  }

}