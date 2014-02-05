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
 * @date            2014-02-04
 * @version         1.0.2
 * @since           1.4.0
 *
 */
class WPDKCustomPostType extends WPDKObject {

  /**
   * Override version
   *
   * @brief Version
   *
   * @var string $__version
   */
  public $__version = '1.0.2';

  /**
   * Custom Post type ID
   *
   * @brief CPT ID
   *
   * @var string $id
   */
  public $id = '';

  /**
   * Path images
   *
   * @brief URL images
   * @since 1.4.8
   *
   * @var string $url_images
   */
  public $url_images = '';

  /**
   * Create an instance of WPDKCustomPostType class
   *
   * @brief Construct
   *
   * @return WPDKCustomPostType
   */
  public function __construct( $id, $args )
  {
    /* Save useful properties */
    $this->id = $id;

    /* Do a several control check in the input $args array */

    /* Register MetaBox */
    if ( !isset( $args['register_meta_box_cb'] ) ) {
      $args['register_meta_box_cb'] = array( $this, 'register_meta_box' );
    }

    /* Get icons path */
    if ( isset( $args['menu_icon'] ) ) {
      $this->url_images = trailingslashit( dirname( $args['menu_icon'] ) );
    }

    /* Register custom post type. */
    register_post_type( $id, $args );

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

      /* Body header class */
      add_filter( 'admin_body_class', array( $this, 'admin_body_class' ) );

      /* Feedback */
      add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );

      /* Default Enter title */
      add_filter( 'enter_title_here', array( $this, '_enter_title_here' ) );

      /* Hook save post */
      add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );

      /* Manage column */
      add_action( 'manage_' . $this->id . '_posts_custom_column', array( $this, 'manage_posts_custom_column' ) );
      add_filter( 'manage_edit-' . $this->id . '_columns', array( $this, 'manage_edit_columns') );
      add_filter( 'manage_edit-' . $this->id . '_sortable_columns', array( $this, 'manage_edit_sortable_columns' ) );

      /* Will loaded... */
      /* Action when cpt list o cpt edit are invokes */
      add_action( 'admin_head-post.php', array( $this, '_will_load_post_list_edit' ) );
      add_action( 'admin_head-edit.php', array( $this, '_will_load_post_list_edit' ) );
      add_action( 'admin_head-post-new.php', array( $this, '_will_load_post_new' ) );
      add_action( 'current_screen', array( $this, '_current_screen' ) );

      /* Footer page. */
      add_action( 'admin_footer-post.php', array( $this, 'admin_footer') );

      /* Display right icon on the title */
      if( !empty( $this->url_images ) ) {
        add_action( 'admin_head', array( $this, 'admin_head' ) );
      }
    }
  }

  /**
   * Description
   *
   * @brief Brief
   */
  public function admin_head()
  {
    global $post_type;

    if ( $post_type != $this->id ) {
      return;
    }

    WPDKHTML::startCompress();
    ?>
    <style type="text/css">
      body.post-type-<?php echo $this->id ?> .wrap > h2
      {
        background-image  : url(<?php echo $this->url_images ?>logo-64x64.png);
        background-repeat : no-repeat;
        height            : 64px;
        line-height       : 64px;
        padding           : 0 0 0 80px;
      }
    </style>
    <?php
    echo WPDKHTML::endCSSCompress();
  }

  /**
   * Added this custom post type ID to tag body classes.
   *
   * @param string $classes The class string
   *
   * @todo Check if WordPress already add this information
   *
   * @return string
   */
  public function admin_body_class( $classes )
  {
    //$classes .= ' wpdk-header-view ' . self::ID;
    return $classes;
  }

  /**
   * Return an array with new CPT update messages.
   * Banner update messages.
   *
   * @brief Message feedback
   *
   * @param array $messages The array of post update messages.
   *
   * @see   /wp-admin/edit-form-advanced.php
   *
   * @return array
   */
  public function post_updated_messages()
  {
    /* You can override this hook method */
  }

  /**
   * This filter allow to change the pseudo-placeholder into the text input for title in edit/new post type form.
   *
   * @brief The placeholder in text input of post
   *
   * @param string $title Default placeholder
   *
   * @return string
   */
  public function _enter_title_here( $title )
  {
    global $post_type;

    if ( $post_type == $this->id ) {
      $title = $this->shouldEnterTitleHere( $title );
    }
    return $title;
  }

  /**
   * Override this delegate method to change the pseudo-placeholder into the text input for title in edit/new post type form.
   *
   * @brief The placeholder in text input of post
   *
   * @param string $title Default placeholder
   *
   * @return string
   */
  public function shouldEnterTitleHere( $title )
  {
    /* You can override this delegate method */
    return $title;
  }

  /**
   * This action is called when a post is save or updated.
   *
   * @brief Save/update post
   * @note  You DO NOT override this method, use `update()` instead
   *
   * @param int|string $post_id Post ID
   * @param object     $post    Optional. Post object
   *
   * @return void
   */
  public function save_post( $post_id, $post = '' )
  {

    WPXtreme::log( 'save_post' );

    // Do not save...
    if ( ( defined( 'DOING_AUTOSAVE' ) && true === DOING_AUTOSAVE ) ||
         ( defined( 'DOING_AJAX' ) && true === DOING_AJAX ) ||
         ( defined( 'DOING_CRON' ) && true === DOING_CRON )
       ) {
      return;
    }

    // Get post type information
    $post_type        = get_post_type();
    $post_type_object = get_post_type_object( $post_type );

    // Exit
    if ( false == $post_type || is_null( $post_type_object ) ) {
      return;
    }


    // This function only applies to the following post_types
    if ( !in_array( $post_type, array( $this->id ) ) ) {
      return;
    }

    // Find correct capability from post_type arguments
    $capability       = '';
    if ( isset( $post_type_object->cap->edit_posts ) ) {
      $capability = $post_type_object->cap->edit_posts;
    }

    // Return if current user cannot edit this post
    if ( !current_user_can( $capability ) ) {
      return;
    }

    // If all ok then update()
    $this->update( $post_id, $post );

  }

  /**
   * Override this method to save/update your custom data.
   * This method is called by hook action 'save_post'
   *
   * @brief Update data
   *
   * @param int|string $post_id Post ID
   * @param object     $post    Optional. Post object
   *
   */
  public function update( $post_id, $post )
  {
    /* You can override this method to save your own data */
  }

  /**
   * This filter allow to display the content of column
   *
   * @brief Manage content columns
   *
   * @param array $column The column
   *
   * @return array
   */
  public function manage_posts_custom_column( $column )
  {
    /* You can override this hook method */
  }

  /**
   * This filter allow to change the columns of list table for this custom post type.
   *
   * @brief Manage columns
   *
   * @param array $columns The list table columns list array
   *
   * @return array
   */
  public function manage_edit_columns( $columns )
  {
    /* You can override this hook method */
    return $columns;
  }

  /**
   * List of sortable columns
   *
   * @brief Sortable columns
   *
   * @param array $columns Array Default sortable columns
   *
   * @return array
   */
  public function manage_edit_sortable_columns( $columns )
  {
    /* You can override this hook method */
    return $columns;
  }

  /**
   * This hook is called when a post list or edit did loaded
   *
   * @brief List or edit
   */
  public function _will_load_post_list_edit()
  {
    global $post_type;

    if ( $post_type == $this->id ) {
      $this->willLoadAdminPost();
      if ( isset( $_REQUEST['action'] ) && 'edit' == $_REQUEST['action'] ) {
        $this->willLoadEditPost();
      } else {
        $this->willLoadListPost();
      }
    }
  }

  /**
   * This hook is called when your CPT edit view is loaded
   *
   * @brief Edit
   */
  public function willLoadEditPost()
  {
    /* You can override this delegate method */
  }

  /**
   * This hook is called when your CPT list view is loaded
   *
   * @brief List
   */
  public function willLoadListPost()
  {
    /* You can override this delegate method */
  }

  /**
   * This hook is called when a new CPT is loaded
   *
   * @brief New
   */
  public function _will_load_post_new()
  {
    global $post_type;

    if ( $this->id == $post_type ) {
      $this->willLoadAdminPost();
      $this->willLoadPostNew();
    }
  }

  /**
   * This hook is called when your CPT new view is loaded
   *
   * @brief New
   */
  public function willLoadPostNew()
  {
    /* You can override this delegate method */
  }

  /**
   * This hook is called when your CPT views are loaded in admin
   *
   * @brief Admin head
   */
  public function willLoadAdminPost()
  {
    /* You can override this delegate method */
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
    if ( !empty( $screen->post_type ) && $screen->post_type == $this->id ) {
      $this->willLoadAdminPost();
    }
  }


  /**
   * Used this hook to display footer content
   *
   * @note Not used Yet
   * @brief Admin footer
   */
  public function admin_footer()
  {

  }

  /**
   * Ovveride this hook to register your custom meta box
   *
   * @brief Meta box
   */
  public function register_meta_box()
  {
    /* You can override this hook method */
  }

}

/// @endcond