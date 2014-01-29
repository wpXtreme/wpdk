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
 * Prototype
 *
 * @class           WPDKScripts
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-29
 * @version         1.0.0
 * @since           1.4.13
 */
class WPDKScripts {

  const JQUERY = 'jquery';

  protected $scripts = array();

  /**
   * Create an instance of WPDKScripts class
   *
   * @brief Construct
   *
   * @return WPDKScripts
   */
  public function __construct()
  {
    global $wp_scripts;
  }

  public function register( $plugin, $scripts, $path, $version, $all_in_footer = true )
  {

    return $this->scripts;
  }

}

/// @endcond