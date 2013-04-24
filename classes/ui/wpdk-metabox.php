<?php
/**
 * A meta box view
 *
 * @class           WPDKMetaBoxView
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-02-28
 * @version         1.0.0
 *
 */
class WPDKMetaBoxView extends WPDKView {

  /**
   * Create an instance of WPDKMetaBoxView class
   *
   * @brief Construct
   *
   * @param string        $id            String for use in the 'id' attribute of tags.
   * @param string        $title         Title of the meta box.
   * @param string|object $screen        Optional. The screen on which to show the box (post, page, link). Defaults to current screen.
   * @param string        $context       Optional. The context within the page where the boxes should show ('normal', 'advanced').
   * @param string        $priority      Optional. The priority within the context where the boxes should show ('high', 'low').   *
   * @param callable      $callback_args Optional. Callable args.
   *
   * @return WPDKMetaBoxView
   */
  public function __construct( $id, $title, $screen = null, $context = WPDKMetaBoxContext::ADVANCED, $priority = WPDKMetaBoxPriority::NORMAL, $callback_args = null ) {
    parent::__construct( $id );
    add_meta_box( $id, $title, array( $this, 'display' ), $screen, $context, $priority, $callback_args );
  }

}

/**
 * The meta box context standard constant define class
 *
 * @class           WPDKMetaBoxContext
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-02-28
 * @version         1.0.0
 *
 */
class WPDKMetaBoxContext {
  const ADVANCED = 'advanced';
  const NORMAL = 'normal';
  /**
   * Metabox on sidebar
   *
   * @note Doesn't exist before 2.7
   *
   * @brief Side
   */
  const SIDE = 'side';
}

/**
 * The meta box priority standard constant define class
 *
 * @class           WPDKMetaBoxPriority
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-02-28
 * @version         1.0.0
 *
 */
class WPDKMetaBoxPriority {

  const CORE   = 'core';
  /**
   * In PHP DEFAULT is a reserved keywowd, so we must append an underscore
   *
   * @brief Default
   */
  const DEFAULT_ = 'default';
  const HIGH   = 'high';
  const LOW    = 'low';
  /**
   * This is an alias of DEFAULT
   *
   * @brief Default
   */
  const NORMAL = 'default';
  const SORTED = 'sorted';

}