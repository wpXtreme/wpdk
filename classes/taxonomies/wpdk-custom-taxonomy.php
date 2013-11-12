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
 * @date            2013-11-08
 * @version         1.0.0
 * @since           1.3.3
 *
 */
class WPDKCustomTaxonomy extends WPDKObject {

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
   * Override version
   *
   * @brief Version
   *
   * @var string $version
   */
  public $version = '1.0.0';

  /**
   * Create an instance of WPDKCustomTaxonomy class
   *
   * @brief Construct
   *
   * @param string       $id          taxonomy ID
   * @param array|string $object_type Name of the object type for the taxonomy object.
   * @apar  array|string $args See optional args in `register_taxonomy()` function.
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

      // do_action('after-' . $taxonomy . '-table', $taxonomy);
      // do_action($taxonomy . '_pre_add_form', $taxonomy);

    }
  }
}

/// @endcond