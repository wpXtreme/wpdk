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

  /**
   * Create an instance of WPDKTinyMCEPlugin class
   *
   * @brief Construct
   *
   * @return WPDKTinyMCEPlugin
   */
  public function __construct()
  {
  }

}