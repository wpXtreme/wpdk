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
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-09-30
 * @version         1.0.2
 * @since           1.4.0
 *
 * @history         1.0.2 - Renamed `admin_print_styles_edit_tags_php`
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
  public $__version = '1.0.2';

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
  public $object_type = '';

  /**
   * Create an instance of WPDKCustomTaxonomy class
   *
   * @param string       $id          taxonomy ID
   * @param array|string $object_type Name of the object type for the taxonomy object.
   * @param array|string $args        See optional args in `register_taxonomy()` function.
   *
   * @return WPDKCustomTaxonomy
   */
  public function __construct( $id, $object_type, $args )
  {

    // Save useful properties
    $this->id          = $id;
    $this->object_type = $object_type;

    // @todo Do a several control check in the input $args array

    // Register the taxonomy.
    register_taxonomy( $id, $object_type, $args );

    // Init admin hook
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

      // Fires when styles are printed for a specific admin page based on $hook_suffix.
      add_action( 'admin_print_styles-edit-tags.php', array( $this, 'admin_print_styles_edit_tags_php' ) );

      // Current screen
      add_action( 'current_screen', array( $this, '_current_screen' ) );

      // do_action('after-' . $taxonomy . '-table', $taxonomy);
      // do_action($taxonomy . '_pre_add_form', $taxonomy);

    }
  }

  /**
   * Fires when styles are printed for a specific admin page based on $hook_suffix.
   *
   * @since WP 2.6.0
   */
  public function admin_print_styles_edit_tags_php()
  {
    // To override
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
    if ( ! empty( $screen->taxonomy ) && $screen->taxonomy == $this->id ) {
      $this->admin_print_styles_edit_tags_php();
    }
  }

}

/// @endcond