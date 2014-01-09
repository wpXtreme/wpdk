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
 * @class           WPDKCustomTaxonomy
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2014-01-08
 * @version         1.0.1
 * @since           1.4.0
 *
 */
class WPDKCustomTaxonomy extends WPDKObject {

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $__version
   */
  public $__version = '1.0.1';

  /**
   * Custom Taxonomy ID
   *
   * @brief taxonomy ID
   *
   * @var string $id
   */
  public $id = '';

  /**
   * Name of the object type for the taxonomy object.
   *
   * @brief CPT ID list
   *
   * @var array|string $object_type
   */
  public $object_type;

  /**
   * Create an instance of WPDKCustomTaxonomy class
   *
   * @brief Construct
   *
   * @param string        $id          taxonomy ID
   * @param array|string  $object_type Name of the object type for the taxonomy object.
   * @param  array|string $args        See optional args in `register_taxonomy()` function.
   *
   * @return WPDKCustomTaxonomy
   */
  public function __construct( $id, $object_type, $args )
  {
    /* Save useful properties */
    $this->id = $id;
    $this->object_type = $object_type;

    /* @todo Do a several control check in the input $args array */

    /* Register the taxonomy. */
    register_taxonomy( $id, $object_type, $args );

    /* Init admin hook */
    $this->initAdminHook();
  }

  /**
   * Init useful (common) admon hook
   *
   * @brief Init admin hook
   */
  private function initAdminHook()
  {
    if ( is_admin() ) {
      /* Admin backend head area */
      add_action( 'admin_print_styles-edit-tags.php', array( $this, 'admin_print_styles' ) );

      /* Current screen */
      add_action( 'current_screen', array( $this, '_current_screen' ) );

      // do_action('after-' . $taxonomy . '-table', $taxonomy);
      // do_action($taxonomy . '_pre_add_form', $taxonomy);

    }
  }

  /**
   * Fire when edit view is loaded
   *
   * @brief Edit view
   */
  public function admin_print_styles()
  {
    /* Override */
  }

  /**
   * Fire when current screen is set
   *
   * @brief Current Screen
   *
   * @param WP_Screen $screen
   */
  public function _current_screen( $screen )
  {
    if ( !empty( $screen->taxonomy ) && $screen->taxonomy == $this->id ) {
      $this->admin_print_styles();
    }
  }

}

/// @endcond