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
 * A replacement experimental of standrd WordPress term object.
 *
 * ## Overview
 *
 * This class introducing a lot of feature for term management.
 *
 *     $term = WPDKterm::term( '%colors' );      // get by name
 *     $term = WPDKterm::term( 'colors-house' ); // get by slug
 *     $term = WPDKterm::term( 116 );            // get by id
 *
 *     $term = WPDKterm::term( 116, 'custom-tax' );  // get by id witha custom taxonomy
 *
 *     $ancestor = WPDKterm::ancestor( $term );  // get the ancestor (top parent)
 *
 * @class           WPDKTerm
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-08-29
 * @version         1.0.1
 *
 */
class WPDKTerm {

  public $count;
  public $description;
  public $name;
  public $parent;
  public $parent_term;
  public $slug;
  public $taxonomy;
  public $term_group;
  public $term_id;
  public $term_taxonomy_id;
  public $permalink;

  /**
   * Create an instance of WPDKTerm class
   *
   * @brief Construct
   *
   * @return WPDKTerm
   */
  private function __construct( $term )
  {
    foreach ( $term as $property => $value ) {
      $this->{$property} = $value;
    }
    $this->permalink = get_term_link( $term );
  }

  /**
   * Return an instance of WPDKTerm class as extended-map of WordPress term.
   *
   * @brief Get term
   *
   * @param int|object|string $term     If integer, will get from database.
   *                                    If object will apply filters and return $term.
   *                                    If string started with `%` will get by `get_term_by( 'name' )`
   *                                    Else if string will get by `get_term_by( 'slug' )`
   * @param string            $taxonomy Taxonomy name that $term is part of.
   * @param string            $output   Optional. Constant OBJECT, ARRAY_A, or ARRAY_N
   * @param string            $filter   Optional. Default is raw or no WordPress defined filter will applied.
   * @param bool              $parent   Optional. If TRUE an object WPDKTerm is create in parent_term property
   *
   * @return WPDKTerm|WP_Error Term Row from database. Will return null if $term is empty. If taxonomy does not
   *        exist then WP_Error will be returned.
   */
  public static function term( $term, $taxonomy, $output = OBJECT, $filter = 'raw', $parent = false )
  {
    if ( is_object( $term ) || is_numeric( $term ) ) {
      $term = get_term( $term, $taxonomy, $output, $filter );
    }
    else {
      $by   = ( '%' === substr( $term, 0, 1 ) ) ? 'name' : 'slug';
      $term = ltrim( $term, '%' );
      $term = get_term_by( $by, $term, $taxonomy, $output, $filter );
    }

    if ( !is_wp_error( $term ) ) {
      $instance = new WPDKTerm( $term );
      /* Get a WPDKTerm object if $parent param is TRUE and a parent id exists. */
      if ( true === $parent && !empty( $instance->parent ) ) {
        $instance->term_parent = self::term( $instance->parent, $taxonomy );
      }
      return $instance;
    }
    return $term;
  }

  /**
   * Return the ancestor (top parent) WPDKTerm object. If FALSE no ancestor object found.
   *
   * @brief Ancestor
   *
   * @return bool|WPDKTerm
   */
  public function ancestor()
  {
    return $this->ancestorOfTerm( $this );
  }

  /**
   * Return the ancestor (top parent) WPDKTerm object. If FALSE no ancestor object found.
   *
   * @brief Ancestor
   *
   * @param WPDKTerm $term An instance of WPDKTerm class
   *
   * @return bool|WPDKTerm
   */
  public static function ancestorOfTerm( WPDKterm $term )
  {

    $term_id = false;
    $result  = false;

    if ( is_a( $term, 'WPDKTerm' ) ) {
      $term_id = $term->term_id;
    }

    if ( !empty( $term_id ) ) {
      while ( !empty( $term->parent ) ) {
        $term   = self::term( $term->parent, $term->taxonomy );
        $result = self::term( $term->term_id, $term->taxonomy );
      }
    }
    return $result;
  }

}

/**
 * Experimental
 *
 * @class           WPDKTerms
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-06-26
 * @version         1.0.0
 *
 * @note            The difference between 'search' and 'name__like' is the leading '%' in the LIKE clause.
 *                  So search is '%search%' and name__like is 'name__like%'
 *
 */
class WPDKTerms {

  /**
   * In WordPress Version 3.2 and above. The 'cache_domain' argument enables a unique cache key to be produced when the
   * query produced by get_terms() is stored in object cache. For instance, if you are using one of this function's
   * filters to modify the query (such as 'terms_clauses'), setting 'cache_domain' to a unique value will not overwrite
   * the cache for similar queries. Default value is 'core'.
   *
   * @brief
   *
   * @var string
   */
  public $cache_domain;
  /**
   * Get all descendents of this term. Default is 0.
   *
   * @brief
   *
   * @var int $child_of
   */
  public $child_of;
  /**
   * An array of term ids to exclude. Also accepts a string of comma-separated ids.
   *
   * @brief Exclude
   *
   * @var int|string|array $exclude
   */
  public $exclude;
  /**
   * An array of parent term ids to exclude
   *
   * @brief
   *
   * @var array $exclude_tree
   */
  public $exclude_tree;
  /**
   * Possible values:
   *
   *     all        - returns an array of term objects - Default
   *     ids        - returns an array of integers
   *     names      - returns an array of strings
   *     count      - (3.2+) returns the number of terms found
   *     id=>parent - returns an associative array where the key is the term id and the value is the parent term id
   *                  if present or 0
   *
   * @brief
   *
   * @var string $fields
   */
  public $fields;
  /**
   * Default is nothing.
   * Allow for overwriting 'hide_empty' and 'child_of', which can be done by setting the value to 'all'.
   *
   * @brief
   *
   * @var string $get
   */
  public $get;
  /**
   * Whether to return empty $terms.
   *
   *    true - Default (i.e. Do not show empty terms)
   *    false
   *
   * @brief Hide empty categories
   *
   * @var bool $hide_empty
   */
  public $hide_empty;
  /**
   * Whether to include terms that have non-empty descendants (even if 'hide_empty' is set to true).
   *  1 (true) - Default
   *  0 (false)
   *
   * @brief
   *
   * @var bool $hierarchical
   */
  public $hierarchical;
  /**
   * An array of term ids to include. Empty returns all.
   *
   * @brief
   *
   * @var array $include
   */
  public $include;
  /**
   * The term name you wish to match. It does a LIKE 'term_name%' query.
   * This matches terms that begin with the 'name__like' string.
   *
   * @brief
   *
   * @var string $name__like
   */
  public $name__like;
  /**
   * The maximum number of terms to return. Default is to return them all.
   *
   * @brief
   *
   * @var int $number
   */
  public $number;
  /**
   * The number by which to offset the terms query.
   *
   * @brief
   *
   * @var int $offset
   */
  public $offset;
  /**
   * Possible values:
   *
   *     ASC - Default
   *     DESC
   *
   * @brief Order
   *
   * @var string $order
   */
  public $order;
  /**
   * Possible values:
   *
   *     id
   *     count
   *     name - Default
   *     slug
   *     term_group - Not fully implemented (avoid using)
   *     none
   *
   * @brief Order by
   *
   * @var string $orderby
   */
  public $orderby;
  /**
   * If true, count all of the children along with the $terms.
   * 1 (true)
   * 0 (false) - Default
   *
   * @brief
   *
   * @var bool $pad_counts
   */
  public $pad_counts;
  /**
   * Get direct children of this term (only terms whose explicit parent is this value). If 0 is passed, only top-level
   * terms are returned. Default is an empty string.
   *
   * @brief
   *
   * @var int $parent
   */
  public $parent;
  /**
   * The term name you wish to match. It does a LIKE '%term_name%' query.
   * This matches terms that contain the 'search' string.
   *
   * @brief
   *
   * @var string $search
   */
  public $search;
  /**
   * Returns terms whose "slug" matches this value. Default is empty string.
   *
   * @brief
   *
   * @var string $slug
   */
  public $slug;
  /**
   * Taxnonomy ID
   *
   * @brief Texnonomy ID
   *
   * @var array|string
   */
  private $_taxonomy;

  /**
   * Create an instance of WPDKTerms class
   *
   * @brief Construct
   *
   * @param string|array $taxonomy Optional. Single or array of taxonomy ID. Default 'category'
   *
   * @return WPDKTerms
   */
  public function __construct( $taxonomy = 'category' )
  {
    $this->_taxonomy = $taxonomy;

    $defaults = array(
      'orderby'      => 'name',
      'order'        => 'ASC',
      'hide_empty'   => true,
      'exclude'      => array(),
      'exclude_tree' => array(),
      'include'      => array(),
      'number'       => '',
      'fields'       => 'all',
      'slug'         => '',
      'parent'       => '',
      'hierarchical' => true,
      'child_of'     => 0,
      'get'          => '',
      'name__like'   => '',
      'pad_counts'   => false,
      'offset'       => '',
      'search'       => '',
      'cache_domain' => 'core'
    );

    foreach ( $defaults as $property => $value ) {
      $this->{$property} = $value;
    }
  }

  /**
   * Return an array of term objects
   *
   * @brief List of terms
   *
   * @uses get_terms()
   *
   * @return array|WP_Error
   */
  public function terms()
  {
    $args = (array)$this;

    /* Remove private. */
    unset( $args['WPDKTerms_taxonomy'] );

    $terms = get_terms( $this->_taxonomy, $args );

    return $terms;
  }

  /**
   * Return the array of term from parent
   *
   * @brief Bread Crumbs
   *
   * @param int|object|string $term     If integer, will get from database.
   *                                    If object will apply filters and return $term.
   *                                    If string started with `%` will get by `get_term_by( 'name' )`
   *                                    Else if string will get by `get_term_by( 'slug' )`
   *
   * @return array
   */
  public function breadCrumbs( $term )
  {
    $from      = $this->term( $term );
    $stack     = array( $from );
    $id_parent = $from->parent;

    while ( !empty( $id_parent ) ) {
      $stack[]   = $term = $this->term( $id_parent );
      $id_parent = $term->parent;
    }

    return array_reverse( $stack );
  }

  /**
   * Return a single term by id, object, name or slug
   *
   * @brief Get single term
   *
   * @param int|object|string $term     If integer, will get from database.
   *                                    If object will apply filters and return $term.
   *                                    If string started with `%` will get by `get_term_by( 'name' )`
   *                                    Else if string will get by `get_term_by( 'slug' )`
   *
   * @return WP_Error|WPDKTerm
   */
  public function term( $term )
  {
    $term = WPDKTerm::term( $term, $this->_taxonomy );

    return $term;

  }
}

/// @endcond