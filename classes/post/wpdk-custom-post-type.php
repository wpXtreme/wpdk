<?php
/// @cond private

/*
 * [DRAFT]
 *
 * THE FOLLOWING CODE IS A DRAFT. FEEL FREE TO USE IT TO MAKE SOME EXPERIMENTS, BUT DO NOT USE IT IN ANY CASE IN
 * PRODUCTION ENVIRONMENT. ALL CLASSES AND RELATIVE METHODS BELOW CAN CHNAGE IN THE FUTURE RELEASES.
 *
 */

/**
 * Custom Post Type model class
 *
 * @class           WPDKCustomPostType
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-11-08
 * @version         1.0.0
 *
 */
class WPDKCustomPostType extends WPDKObject {

  const ID = '';

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $version
   */
  public $version = '1.0.0';

  /**
   * Create an instance of WPDKCustomPostType class
   *
   * @brief Construct
   *
   * @return WPDKCustomPostType
   */
  public function __construct( $id, $args )
  {
    /* Register custom post type. */
    register_post_type( $id, $args );
  }

}


/// @endcond
