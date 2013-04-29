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
 * The WPDKPost class is a WordPress Post as object.
 *
 * ## Overview
 * The WPDKPost class is a wrap of WordPress Post record. In addition this class provides a lot of methods and
 * properties.
 *
 * ### Properties naming
 * You'll see that a lot of properties class are written in lowercase and underscore mode as `$post_date`. This beacouse
 * they are a map of database record.
 *
 * ### Post onfly
 *
 * ### Create a virtual post
 *
 * @class              _WPDKPost
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-04-29
 * @version            0.9.1
 *
 */

class _WPDKPost {

  // -----------------------------------------------------------------------------------------------------------------
  // These properties are a one on one map of database table field name.
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * The post ID
   *
   * @brief ID Post
   *
   * @var int $ID
   */
  public $ID;
  /**
   * Number of comments, pings, and trackbacks combined
   *
   * @brief Comment count
   *
   * @var int $comment_count
   */
  public $comment_count;
  /**
   * The comment status, ie. open. Max 20 char
   *
   * @brief Comment status
   *
   * @var string $comment_status
   */
  public $comment_status;
  /**
   * The guid. Global Unique Identifier. The “real” URL to the post, not the permalink version. For pages, this is the
   * actual URL. In the case of files (attachments), this holds the URL to the file.
   *
   * @brief GUID
   *
   * @var string $guid
   */
  public $guid;
  /**
   * Holds values for display order of pages. Only works with pages, not posts.
   *
   * @brief Menu order
   *
   * @var int $menu_order
   */
  public $menu_order;
  /**
   * The ping status, ie. open. Max 20 char
   *
   * @brief Ping status
   *
   * @var string $ping_status
   */
  public $ping_status;
  /**
   * List of urls that have been pinged (for published posts)
   *
   * @brief List of pinged urls
   *
   * @var array $pinged
   */
  public $pinged;
  /**
   * The post author ID
   *
   * @brief ID author
   *
   * @var int $post_author
   */
  public $post_author;
  /**
   * Number representing post category ID#. This property is not present on databse record.
   *
   * @brief Category ID
   *
   * @var int $post_category
   */
  public $post_category;
  /**
   * The post content
   *
   * @brief Content
   *
   * @var string $post_content
   */
  public $post_content;
  /**
   * Exists to store a cached version of post content (most likely with all the the_content filters already applied).
   * If you’ve got a plugin that runs a very resource heavy filter on content, you might consider caching the results
   * with post_content_filtered, and calling that from the front end instead.
   *
   * @brief Content filtered
   *
   * @var string $post_content_filtered
   */
  public $post_content_filtered;
  /**
   * The Post date
   *
   * @brief Date Post
   *
   * @var string $post_date
   */
  public $post_date;
  /**
   * The post date in GMT
   *
   * @brief Date post in GMT
   *
   * @var string $post_date_gmt
   */
  public $post_date_gmt;
  /**
   * The post excerpt
   *
   * @brief Excerpt
   *
   * @var string $post_excerpt
   */
  public $post_excerpt;
  /**
   * The post mime type. Only used for files (attachments). Contains the MIME type of the uploaded file.
   * Typical values are: text/html, image/png, image/jpg
   *
   * @brief Mime type
   *
   * @var string $post_mime_type
   *
   */
  public $post_mime_type;
  /**
   * Modified date
   *
   * @brief Modified date
   *
   * @var string $post_modified
   */
  public $post_modified;
  /**
   * Modifed date in GMT
   *
   * @brief Modifed date in GMT
   *
   * @var string $post_modified_gmt
   */
  public $post_modified_gmt;
  /**
   * The post name. Same as post slug. Max 200 char
   *
   * @brief Name
   *
   * @var string $post_name
   */
  public $post_name;
  /**
   * Parent Post ID
   *
   * @brief Parent post
   *
   * @var int $post_parent
   */
  public $post_parent;
  /**
   * Protect post password. Will be empty if no password. Max 20 char
   *
   * @brief Password
   *
   * @var string $post_password
   */
  public $post_password;
  /**
   * Post status, ie. publish, draft. Max 20 char
   *
   * @brief Status
   *
   * @var string $post_status
   */
  public $post_status;
  /**
   * The post title
   *
   * @brief Title
   *
   * @var string $post_title
   */
  public $post_title;
  /**
   * The post type. Used by Custom Post. Default 'post'. Self-explanatory for pages and posts. Any files uploaded are
   * attachments and post revisions saved as revision
   *
   * @brief Type
   *
   * @var string $post_type
   */
  public $post_type;
  /**
   * List of urls to ping when post is published (for unpublished posts)
   *
   * @brief List ping urls
   *
   * @var array $to_ping
   */
  public $to_ping;

  /**
   * Create an instance of WPDKPost class
   *
   * @brief Construct
   *
   * @param string|int|object|null $record    Optional. Post ID, post object, post slug or null
   * @param string                 $post_type Optional. If $record is a string (slug) then this is the post type where search.
   *                                          Default is 'page'
   *
   * @return WPDKPost
   */
  public function __construct( $record = null, $post_type = 'page' ) {

    /* Get post by id. */
    if ( !is_null( $record ) && is_numeric( $record ) ) {
      $this->initPostByID( absint( $record ) );
    }

    /* Get post from database record. */
    elseif ( !is_null( $record ) && is_object( $record ) && isset( $record->ID ) ) {
      $this->initPostByPost( $record );
    }

    /* Get post by name. */
    elseif ( !is_null( $record ) && is_string( $record ) ) {
      /* @todo Use get by name */
      $object = get_page_by_path( $record, OBJECT, $post_type );
      $this->initPostByPost( $object );
    }

    /* Create an empty post. */
    elseif ( is_null( $record ) ) {
      /* Create a new onfly post */
      $defaults = $this->postEmpty();
      $this->initPostByArgs( $defaults );
    }
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Create/Get Post
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Init this instance of WPDKPost as post from Post ID
   *
   * @brief Init by Post ID
   *
   * @param int $id_post Post ID
   */
  private function initPostByID( $id_post ) {
    if ( isset( $GLOBALS[__CLASS__][$id_post] ) ) {
      $post = $GLOBALS[__CLASS__][$id_post];
    }
    else {
      $GLOBALS[__CLASS__][$id_post] = $post = get_post( $id_post );
    }
    $this->initPostByPost( $post );
  }

  /**
   * Init this instance of WPDKPost as post from Post database object
   *
   * @brief Init by Post object
   *
   * @param object $post The post database record
   */
  private function initPostByPost( $post ) {
    if ( is_object( $post ) ) {
      /* Get properties. */
      foreach ( $post as $property => $value ) {
        $this->$property = $value;
      }
    }
  }

  /**
   * Return a Key value pairs array with property and value for an empty post. The properties are set to a default
   * values.
   *
   * @brief Return an empty post key value pairs
   *
   * @return array
   */
  private function postEmpty() {
    $args = array(
      'ID'                    => 0,
      'post_author'           => 0,
      'post_date'             => '0000-00-00 00:00:00',
      'post_date_gmt'         => '0000-00-00 00:00:00',
      'post_content'          => '',
      'post_title'            => '',
      'post_excerpt'          => '',
      'post_status'           => WPDKPostStatus::PUBLISH,
      'comment_status'        => 'open',
      'ping_status'           => 'open',
      'post_password'         => '',
      'post_name'             => '',
      'to_ping'               => '',
      'pinged'                => '',
      'post_modified'         => '0000-00-00 00:00:00',
      'post_modified_gmt'     => '0000-00-00 00:00:00',
      'post_content_filtered' => '',
      'post_parent'           => 0,
      'guid'                  => '',
      'menu_order'            => 0,
      'post_type'             => WPDKPostType::POST,
      'post_mime_type'        => '',
      'comment_count'         => 0
    );
    return $args;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Empty Post
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Init this instance of WPDKPost as a empty Post
   *
   * @brief Init by array arguments
   */
  private function initPostByArgs( $args ) {
    foreach ( $args as $property => $value ) {
      $this->$property = $value;
    }
  }

  // -----------------------------------------------------------------------------------------------------------------
  // CRUD
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Update this post on database. Also this method check if you are in admin backend area for this custom post.
   * In this case the post update if turn off and save the post meta only.
   *
   * @brief Update
   *
   * @since 0.9
   * @uses  wp_update_post()
   * @uses  self::updatePostMeta()
   *
   * @return int|WP_Error The value 0 or WP_Error on failure. The post ID on success.
   */
  public function update() {
    /* Avoid update when we are in admin backend area. */
    global $pagenow;

    if ( 'post.php' != $pagenow ) {
      return wp_update_post( $this, true );
    }
  }

  /**
   * Delete permately this post from database
   *
   * @brief Delete
   *
   * @since 0.9
   * @uses  wp_delete_post() with second parameter to TRUE.
   *
   * @return mixed False on failure
   */
  public function delete() {
    return wp_delete_post( $this->ID, true );
  }

  /**
   * Moves a post or page to the Trash
   * If trash is disabled, the post or page is permanently deleted.
   *
   * @brief Set in trash
   *
   * @since 0.9
   * @uses  wp_trash_post()
   *
   * @return mixed False on failure
   */
  public function trash() {
    return wp_trash_post( $this->ID );
  }

  /**
   * Restores a post or page from the Trash
   *
   * @brief Set in trash
   *
   * @since 0.9
   * @uses  wp_untrash_post()
   *
   * @return mixed False on failure
   */
  public function untrash() {
    return wp_untrash_post( $this->ID );
  }
}

/**
 * WordPress standard Post Status at 3.4 release
 *
 * @class              WPDKPostStatus
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKPostStatus {
  const AUTO_DRAFT = 'auto-draft';
  const DRAFT      = 'draft';
  const INHERIT    = 'inherit';
  const PENDING    = 'pending';
  /**
   * @note Sorry by PRIVATE is a php keyword so I have used PRIVATE_ instead
   */
  const PRIVATE_ = 'private';
  const PUBLISH  = 'publish';
  const TRASH    = 'trash';
}

/**
 * WordPress standard Post Type at 3.4 release
 *
 * @class              WPDKPostType
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 *
 */
class WPDKPostType {
  const ATTACHMENT    = 'attachment';
  const NAV_MENU_ITEM = 'nav_menu_item';
  const PAGE          = 'page';
  const POST          = 'post';
  const REVISION      = 'revision';
}


/**
 * WordPress Posts model
 *
 * ## Overview
 *
 * Manage posts model
 *
 * @class           WPDKPosts
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-03-15
 * @version         1.0.0
 *
 */
class WPDKPosts {

  /**
   * Create an instance of WPDKPosts class
   *
   * @brief Construct
   *
   * @return WPDKPosts
   */
  public function __construct() {
  }

}




/**
 * Utility for post meta
 *
 * @class              WPDKPostMeta
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 * @deprecated         Since 0.9 - Not useful - Used by wpx-smartshop-product-metabox.php (WPXSmartShopProductMetaBox)
 *
 */
class WPDKPostMeta {

  /**
   * Pointer to _WPDKPost object
   *
   * @brief An instance of _WPDKPost class
   *
   * @var _WPDKPost $_post
   */
  private $_post;

  /**
   * Create an instance of WPDKPostMeta class
   *
   * @brief Construct
   *
   * @param int|object $post Post ID, Post object
   *
   * @return WPDKPostMeta
   */
  public function __construct( $post ) {
    $this->_post = new _WPDKPost( $post );
  }

  /**
   * @brief Update or delete a post meta
   *
   * Update a post meta with key `$meta_key` for post `$id_post`. If value is NULL the post meta is deleted.
   *
   * @param int         $id_post    Post ID
   * @param string      $meta_key   Meta key
   * @param string|null $meta_value Meta value. If NULL the post meta is deleted.
   */
  public static function updatePostMetaWithDeleteIfNotSet( $id_post, $meta_key, $meta_value = null ) {

    /* Sanitize post id. */
    $id_post = absint( $id_post );

    if ( !empty( $id_post ) ) {
      /* Se il parametro meta_value è null elimino il post meta. */
      if ( is_null( $meta_value ) ) {
        delete_post_meta( $id_post, $meta_key );
      }
      else {
        /* Sanitizo il nome della meta key che potrebbe arrivara come name di un campo input array. */
        if ( substr( $meta_key, -2 ) == '[]' ) {
          $meta_key = substr( $meta_key, 0, strlen( $meta_key ) - 2 );
        }
        update_post_meta( $id_post, $meta_key, $meta_value );
      }
    }
  }

  /**
   * Return a single value with a specific meta key
   *
   * @param string $key A meta key
   *
   * @return mixed|null
   */
  public function value( $key ) {
    if ( !empty( $key ) && !empty( $this->_post ) ) {
      return get_post_meta( $this->_post->ID, $key, true );
    }
    return null;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Utility
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * Return ana array of values with a specific meta key
   *
   * @param string $key A meta key
   *
   * @return array|null
   */
  public function values( $key ) {
    if ( !empty( $key ) && !empty( $this->_post ) ) {
      return get_post_meta( $this->_post->ID, $key, false );
    }
    return null;
  }
}

/// @endcond
