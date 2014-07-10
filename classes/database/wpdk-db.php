<?php

/**
 * Manage the common status of a row in database.
 * You can override this class for own extensions.
 *
 * @class           WPDKDBTableRowStatuses
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-03-01
 * @version         1.0.0
 * @since           1.5.1
 *
 */
class WPDKDBTableRowStatuses {

  // Default standard statuses
  const ALL     = 'all';
  const PUBLISH = 'publish';
  const DRAFT   = 'draft';
  const TRASH   = 'trash';

  /**
   * Return a key pairs array with the list of statuses
   *
   * @brief Statuses
   *
   * @return array
   */
  public static function statuses()
  {
    $statuses = array(
      self::ALL     => __( 'All', WPDK_TEXTDOMAIN ),
      self::DRAFT   => __( 'Draft', WPDK_TEXTDOMAIN ),
      self::PUBLISH => __( 'Publish', WPDK_TEXTDOMAIN ),
      self::TRASH   => __( 'Trash', WPDK_TEXTDOMAIN ),
    );
    return $statuses;
  }

  /**
   * Return a key pairs array with the list of icons for statuses
   *
   * @brief Icons glyph
   *
   * @return array
   */
  public static function icon_statuses()
  {
    $statuses = array(
      self::DRAFT   => WPDKGlyphIcons::html( WPDKGlyphIcons::CLOCK ),
      self::PUBLISH => WPDKGlyphIcons::html( WPDKGlyphIcons::OK ),
      self::TRASH   => WPDKGlyphIcons::html( WPDKGlyphIcons::TRASH ),
    );
    return $statuses;
  }

}

/**
 * A model for a database table.
 * If you would like use this model in a list table view controller, see the interface of WPDKListTableModel.
 *
 * @class           WPDKDBTableModel
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-03-01
 * @version         1.0.0
 *
 */
class WPDKDBTableModel {

  // Extends with your const column

  /**
   * Name of field as primary key
   *
   * @brief Primary key
   *
   * @var string $primary_key
   */
  public $primary_key = '';

  /**
   * The filename of SQL file with the database table structure and init data.
   *
   * @brief SQL file
   *
   * @var string $sql_filename
   */
  public $sql_filename = '';

  /**
   * The name of the database table with the WordPress prefix
   *
   * @brief Table name
   *
   * @var string $table_name
   */
  public $table_name = '';

  /**
   * Used for check the CRUD action results
   *
   * @brief CRUD result
   *
   * @var bool $crud_results
   */
  public $crud_results = false;

  /**
   * Create an instance of WPDKDBTableModel class
   *
   * @brief Construct
   *
   * @param string $table_name  The name of the database table without WordPress prefix
   * @param string $sql_file    Optional. The filename of SQL file with the database table structure and init data.
   * @param string $primary_key Optional. If FALSE the primary key is get from database.
   *
   * @return WPDKDBTableModel
   */
  public function __construct( $table_name, $sql_file = '', $primary_key = 'id' )
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    // Add the WordPres database prefix
    $this->table_name = sprintf( '%s%s', $wpdb->prefix, $table_name );

    // $sql_file must be complete of path
    $this->sql_filename = $sql_file;

    // Try to get the Primary key
    $this->primary_key = empty( $primary_key ) ? $this->primaryKey() : $primary_key;
  }

  /**
   * Return the name of the primary key
   *
   * @brief Get the primay key name column
   *
   * @note  In case you can override this method and return your pwn primary key
   *
   * @return string
   */
  public function primaryKey()
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    $db = DB_NAME;

    $sql = <<<SQL
SELECT COLUMN_NAME
FROM information_schema.COLUMNS
WHERE (TABLE_SCHEMA = '{$db}')
AND (TABLE_NAME = '{$this->table_name}')
AND (COLUMN_KEY = 'PRI');
SQL;

    $result = $wpdb->get_var( $sql );

    return $result;
  }

  // -------------------------------------------------------------------------------------------------------------------
  // CRUD
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Insert a record by values. Return FALSE if error or id of record if successfully.
   *
   * @brief Insert
   *
   * @internal string $prefix A prefix used for filter/action hook, eg: carrier, stat, ...
   * @internal array  $values Array keys values
   * @internal array  $format Optional. Array keys values for format null values
   *
   * @return int|bool
   */
  public function insert()
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    /*
     * since 1.5.1
     * try to avoid 'PHP Strict Standards:  Declaration of ::insert() should be compatible with self::insert'
     *
     * Remeber that if a params is missing it is NULL
     */
    $args = func_get_args();
    list( $prefix, $values ) = $args;
    $format = isset( $args[2] ) ? $args[2] : array();

    /**
     * Filter the values array for insert.
     *
     * @param array $values Array values for insert.
     */
    $values = apply_filters( $prefix . '_insert_values', $values );

    // Insert
    $this->crud_results = $wpdb->insert( $this->table_name, $values, $format );

    // Get the id
    $id = $wpdb->insert_id;

    /**
     * Fires when a record is inserted.
     *
     * @param bool  $result Result of insert.
     * @param array $values Array with values of insert.
     */
    do_action( $prefix . '_inserted', $this->crud_results, $values );

    if ( false == $this->crud_results ) {
      return false;
    }

    // Return the id
    return $id;
  }

  /**
   * Select data
   *
   * @brief Select
   * @note Override this method with your own select
   */
  public function select()
  {
    // Override this method with your own select
  }

  /**
   * Update a record by values. Return FALSE if error or the $where condiction if successfully.
   * You can use the $where condiction returned to get again the record ID.
   *
   * @brief Update
   *
   * @internal string $prefix A prefix used for filter/action hook, eg: carrier, stat, ...
   * @internal array  $values Array keys values
   * @internal array  $where  Array keys values for where update
   * @internal array  $format Optional. Array keys values for format null values
   *
   * @return array|bool
   */
  public function update()
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    /*
     * since 1.5.1
     * try to avoid 'PHP Strict Standards:  Declaration of ::update() should be compatible with self::update'
     *
     * Remeber that if a params is missing it is NULL
     */
    $args = func_get_args();
    list( $prefix, $values, $where ) = $args;
    $format = isset( $args[3] ) ? $args[3] : array();

    /**
     * Filter the values array for update.
     *
     * @param array $values Array values for update.
     */
    $values = apply_filters( $prefix . '_update_values', $values );

    // Update
    $this->crud_results = $wpdb->update( $this->table_name, $values, $where, $format );

    /**
     * Fires when a record is updated.
     *
     * @param bool|int $result Returns the number of rows updated, or false if there is an error.
     * @param array    $values Array with values of update.
     * @param array    $where  Array with values of where condiction.
     */
    do_action( $prefix . '_updated', $this->crud_results, $values, $where );

    if ( false == $this->crud_results ) {
      return false;
    }

    // Get the id
    return $where;
  }

  /**
   * Delete one or more record from table. Return the number of rows affected/selected or false on error.
   * Use the primaryKey.
   *
   * @brief Delete
   *
   * @param int|array $id Any single int or array list of primary keys
   *
   * @return int|bool
   */
  public function delete( $id )
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    // Stability
    if ( empty( $id ) ) {
      return false;
    }

    $ids = implode( ',', (array)$id );

    /**
     * Fires before delete records from table.
     *
     * @param array $ids An array with the list of id.
     */
    do_action( 'wpdk_db_table_model_will_delete-' . $this->table_name, $ids );

    $sql    = <<< SQL
DELETE FROM {$this->table_name}
WHERE {$this->primary_key} IN( {$ids} )
SQL;

    $result = $wpdb->query( $sql );

    return $result;
  }

  /**
   * Map the properties fields of single database row into a destination object model.
   * The $destination_object can implement a method named column_[property] to override map process.
   *
   * @brief Map a database record into a model row
   *
   * @param object $source_row         Database record
   * @param object $destination_object Object to map
   *
   * @return object|bool The destination object with new properties or FALSE if error.
   */
  public function map( $source_row, $destination_object )
  {
    if ( is_object( $source_row ) ) {
      foreach ( $source_row as $field => $value ) {
        $destination_object->$field = $value;
        if ( method_exists( $destination_object, 'column_' . $field ) ) {
          call_user_func( array( $destination_object, 'column_' . $field ), $value );
        }
      }
      return $destination_object;
    }
    return false;
  }

  /**
   * Do an update the table via WordPress dbDelta() function. Apply a new SQL file on the exists (or do not exists)
   * table. Return TRUE on success
   *
   * @brief Update table
   *
   * @return bool
   */
  public function update_table()
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    // Hide database warning and error
    $wpdb->hide_errors();
    $wpdb->suppress_errors();

    // Buffering
    ob_start();

    if ( !empty( $this->sql_filename ) && !empty( $this->table_name ) ) {
      if ( !function_exists( 'dbDelta' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      }
      $content = file_get_contents( $this->sql_filename );
      if ( empty( $content ) ) {
        ob_end_clean();
        return false;
      }

      // Replace table name
      $sql = str_replace( '%s', $this->table_name, $content );

      // Remove comments
      $pattern = '@(([\'"]).*?[^\\\]\2)|((?:\#|--).*?$|/\*(?:[^/*]|/(?!\*)|\*(?!/)|(?R))*\*\/)\s*|(?<=;)\s+@ms';
      /*
       * Commented version
       *
       * $sqlComments = '@
       *     (([\'"]).*?[^\\\]\2) # $1 : Skip single & double quoted expressions
       *     |(                   # $3 : Match comments
       *         (?:\#|--).*?$    # - Single line comments
       *         |                # - Multi line (nested) comments
       *          /\*             #   . comment open marker
       *             (?: [^/*]    #   . non comment-marker characters
       *                 |/(?!\*) #   . ! not a comment open
       *                 |\*(?!/) #   . ! not a comment close
       *                 |(?R)    #   . recursive case
       *             )*           #   . repeat eventually
       *         \*\/             #   . comment close marker
       *     )\s*                 # Trim after comments
       *     |(?<=;)\s+           # Trim after semi-colon
       *     @msx';
       *
       */
      $sql_sanitize = trim( preg_replace( $pattern, '$1', $sql ) );
      preg_match_all( $pattern, $sql, $comments );

      // Only commnets
      //$extractedComments = array_filter( $comments[ 3 ] );

      // Execute delta
      @dbDelta( $sql_sanitize );

      // Clear error
      global $EZSQL_ERROR;
      $EZSQL_ERROR = array();
    }
    ob_end_clean();
    return true;
  }

  // -------------------------------------------------------------------------------------------------------------------
  // UTILITIES
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Return a list of groupped by column. Use 'value' as selector
   *
   *    $results = ::group_by( 'column' );
   *    foreach( $results as $row ) echo $row->value;
   *
   * @brief Group by a column
   *
   * @param string $column Column name
   * @param string $order  Optional. Order 'ASC' or 'DESC'
   *
   * @return mixed
   */
  public function group_by( $column, $order = 'ASC' )
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    $sql = <<< SQL
SELECT {$column} AS value
FROM {$this->table_name}
GROUP BY {$column}
ORDER BY {$column} {$order}
SQL;

    return $wpdb->get_results( $sql );

  }

  /**
   * Return a where condiction with possible OR values
   *
   *     $where[] = ::where( $args, self::COLUMN_STATUS, '', array( WPXSSCouponStatus::ALL ) );
   *
   *     // If $args[self::COLUMN_STATUS] is an array
   *     // ( status = 'pending' OR status = 'confirmed' )
   *
   *     // If $args[self::COLUMN_STATUS] is a string
   *     // ( status = 'pending' )
   *
   *     $where[] = ::where( $args, self::COLUMN_STATUS, 'coupon', array( WPXSSCouponStatus::ALL ) );
   *
   *     // If $args[self::COLUMN_STATUS] is a string
   *     // ( coupon.status = 'pending' )
   *
   * @brief Where
   *
   * @param array  $args         Arguments list
   * @param string $key          A key selector
   * @param string $table_prefix Optional. Table prefix
   * @param array  $not_in       Optional. Value to exclude
   * @param string $cond         Optional. Condiction used, default '=' or 'LIKE'
   *
   * @return string
   */
  public static function where( $args, $key, $table_prefix = '', $not_in = array(), $cond = '=' )
  {
    if ( isset( $args[$key] ) && !empty( $args[$key] ) && !in_array( $args[$key], (array)$not_in ) ) {

      // Append dot to table if exists
      $table_prefix = empty( $table_prefix ) ? '' : $table_prefix . '.';

      // Every array
      $array = (array)$args[$key];
      $stack = array();
      foreach ( $array as $value ) {
        $stack[] = sprintf( "%s%s %s '%s'", $table_prefix, $key, $cond, $value );
      }
      return sprintf( "( %s )", implode( ' OR ', $stack ) );
    }
    return false;
  }

  /**
   * Return a where condiction for date and date time
   *
   *     $where[] = ::where_date( $args, self::COLUMN_DATE );
   *
   *     // If $args[self::COLUMN_DATE] is a string
   *     // ( DATE_FORMAT( col_date, '%Y-%m-%d %H:%i:%s' ) = '2014-01-01' )
   *
   *     // If $args[self::COLUMN_DATE] is an array( '2014-01-01', '2014-02-01' )
   *     // (
   *     //    DATE_FORMAT( col_date, '%Y-%m-%d %H:%i:%s' ) >= '2014-01-01' )
   *     //    AND
   *     //    DATE_FORMAT( col_date, '%Y-%m-%d %H:%i:%s' ) <= '2014-02-01' )
   *     //  )
   *
   * @brief Where for date
   *
   * @param array  $args         Arguments list
   * @param string $column_key   Name of column
   * @param string $table_prefix Optional. Table prefix
   * @param string $accuracy     Optional. Default = '%Y-%m-%d %H:%i:%s'
   *
   * @return string
   */
  public static function where_date( $args, $column_key, $table_prefix = '', $accuracy = '%Y-%m-%d %H:%i:%s' )
  {
    if ( isset( $args[$column_key] ) && !empty( $args[$column_key] ) ) {

      // Append dot to table if exists
      $table_prefix = empty( $table_prefix ) ? '' : $table_prefix . '.';

      // If $args[$column_key] is a string as '2014-01-01'
      if( is_string( $args[$column_key] ) ) {
        $stack[] = sprintf( "DATE_FORMAT( %s%s, '%s' ) %s '%s'", $table_prefix, $column_key, $accuracy, '=', $args[$column_key] );
      }
      // Handle if $args[$column_key] is an array as array( '2014-01-01', '2014-02-01' )
      elseif( is_array( $args[$column_key] ) ) {

        // Handle if $args[$column_key] is an array as array( false, '2014-02-01' )
        if( !empty( $args[$column_key][0] ) ) {
          $stack[] = sprintf( "DATE_FORMAT( %s%s, '%s' ) >= '%s'", $table_prefix, $column_key, $accuracy, $args[$column_key][0] );
        }

        // Handle if $args[$column_key] is an array as array( '2014-02-01' ) or array( '2014-02-01', false )
        if( isset( $args[$column_key][1] ) && !empty( $args[$column_key][1] ) ) {
          $stack[] = sprintf( "DATE_FORMAT( %s%s, '%s' ) <= '%s'", $table_prefix, $column_key, $accuracy, $args[$column_key][1] );
        }
      }

      return sprintf( "( %s )", implode( ' OR ', $stack ) );
    }
    return false;
  }

  /**
   * Return a where condiction with possible OR values for a filter. Useful for JOIN table
   *
   *     $where[] = ::where( $args, self::FILTER_USER_ID, 'ID', 'users' );
   *
   *     // ( users.ID = '34' )
   *
   * @brief Where
   *
   * @param array  $args         Arguments list
   * @param string $filter       A key for filter
   * @param string $key          A key selector
   * @param string $table_prefix Optional. Table prefix
   * @param array  $not_in       Optional. Value to exclude
   * @param string $cond         Optional. Condiction used, default '=' or 'LIKE'
   *
   * @return string
   */
  public static function where_filter( $args, $filter, $key, $table_prefix = '', $not_in = array(), $cond = '=' )
  {
    if ( isset( $args[$filter] ) && !empty( $args[$filter] ) && !in_array( $args[$filter], (array)$not_in ) ) {
      $args[$key] = $args[$filter];

      return self::where( $args, $key, $table_prefix, array(), $cond );
    }
    return false;
  }


}

/**
 * Similar to WPDKListTableModel but some useful init and methods for Database.
 *
 * PHP does not allow to inherit from more than one class. So one way to solve the problem is to provide a pointer to
 * the class that you want to inherit.
 *
 * This class, therefore, is as if it were understood in this way:
 *
 *     class WPDKDBListTableModel extends WPDKListTableModel, WPDKDBTableModel {}
 *
 *
 * @class           WPDKDBListTableModel
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-03-26
 * @version         1.0.0
 *
 */
class WPDKDBListTableModel extends WPDKListTableModel {

  // Common Actions
  const ACTION_NEW     = 'new';
  const ACTION_INSERT  = 'insert';
  const ACTION_UPDATE  = 'update';
  const ACTION_EDIT    = 'action_edit';
  const ACTION_DELETE  = 'action_delete';
  const ACTION_DRAFT   = 'action_draft';
  const ACTION_TRASH   = 'action_trash';
  const ACTION_RESTORE = 'action_restore';

  /**
   * PHP does not allow to inherit from more than one class. So one way to solve the problem is to provide a pointer to
   * the class that you want to inherit.
   *
   * @brief Table
   *
   * @var WPDKDBTableModel $table
   */
  public $table;

  /**
   * Create an instance of WPDKDBListTableModel class
   *
   * @brief Construct
   *
   * @param string $table_name Optional. The name of the database table without WordPress prefix
   * @param string $sql_file   Optional. The filename of SQL file with the database table structure and init data.
   *
   * @return WPDKDBListTableModel
   */
  public function __construct( $table_name = '', $sql_file = '' )
  {
    // Init parent
    parent::__construct();

    // Init the table model
    if( !empty( $table_name ) ) {
      $this->table = new WPDKDBTableModel( $table_name, $sql_file );
    }
  }

  // -------------------------------------------------------------------------------------------------------------------
  // CRUD
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Insert a record by values. Return FALSE if error or id of record if successfully.
   *
   * @brief Insert
   *
   * @internal string $prefix A prefix used for filter/action hook, eg: carrier, stat, ...
   * @internal array  $values Array keys values
   * @internal array  $format Optional. Array keys values for format null values
   *
   * @return int|bool
   */
  public function insert()
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    /*
     * since 1.5.1
     * try to avoid 'PHP Strict Standards:  Declaration of ::insert() should be compatible with self::insert'
     *
     * Remeber that if a params is missing it is NULL
     */
    $args = func_get_args();
    list( $prefix, $values ) = $args;
    $format = isset( $args[2] ) ? $args[2] : array();

    /**
     * Filter the values array for insert.
     *
     * @param array $values Array values for insert.
     */
    $values = apply_filters( $prefix . '_insert_values', $values );

    // Insert
    $result = $wpdb->insert( $this->table->table_name, $values, $format );

    /**
     * Fires when a record is inserted.
     *
     * @param bool  $result Result of insert.
     * @param array $values Array with values of insert.
     */
    do_action( $prefix . '_inserted', $result, $values );

    if ( false == $result ) {
      return false;
    }

    // Get the id
    return $wpdb->insert_id;
  }

  /**
   * Update a record by values. Return FALSE if error or the $where condiction if successfully.
   * You can use the $where condiction returned to get again the record ID.
   *
   * @brief Update
   *
   * @internal string $prefix A prefix used for filter/action hook, eg: carrier, stat, ...
   * @internal array  $values Array keys values
   * @internal array  $where  Array keys values for where update
   * @internal array  $format Optional. Array keys values for format null values
   *
   * @return array|bool
   */
  public function update()
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    /*
     * since 1.5.1
     * try to avoid 'PHP Strict Standards:  Declaration of ::update() should be compatible with self::update'
     *
     * Remeber that if a params is missing it is NULL
     */
    $args = func_get_args();
    list( $prefix, $values, $where ) = $args;
    $format = isset( $args[3] ) ? $args[3] : array();

    /**
     * Filter the values array for update.
     *
     * @param array $values Array values for update.
     */
    $values = apply_filters( $prefix . '_update_values', $values );

    // Update
    $result = $wpdb->update( $this->table->table_name, $values, $where, $format );

    /**
     * Fires when a record is updated.
     *
     * @param bool|int $result Returns the number of rows updated, or false if there is an error.
     * @param array    $values Array with values of update.
     * @param array    $where  Array with values of where condiction.
     */
    do_action( $prefix . '_updated', $result, $values, $where );

    if ( false == $result ) {
      return false;
    }

    // Get the id
    return $where;
  }

  /**
   * Return the integer count of all rows when $distinct param is emmpty or an array of distinct count for $distinct column.
   *
   * @brief    Count
   *
   * @internal string       $distinct Optional. Name of field to distinct group by
   * @internal array|string $status   Optional. Key value paier for where condiction on field: key = fields, vallue = value
   *
   * @return int|array
   */
  public function count()
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    /*
     * since 1.5.1
     * try to avoid 'PHP Strict Standards:  Declaration of [...] should be compatible with [...]
     *
     * Remeber that if a params is missing it is NULL
     */
    $args     = func_get_args();
    $distinct = isset( $args[0] ) ? $args[0] : '';
    $status   = isset( $args[1] ) ? $args[1] : '';

    $where = '';
    if ( !empty( $status ) && is_array( $status ) ) {
      if ( is_numeric( $status[ key( $status ) ] ) ) {
        $where = sprintf( 'WHERE %s = %s', key( $status ), $status[ key( $status ) ] );
      }
      else {
        $where = sprintf( "WHERE %s = '%s'", key( $status ), $status[ key( $status ) ] );
      }
    }

    if ( empty( $distinct ) ) {
      $sql = <<< SQL
SELECT COUNT(*) AS count
  FROM {$this->table->table_name}
  {$where}
SQL;

      return absint( $wpdb->get_var( $sql ) );
    }
    else {
      $sql = <<< SQL
SELECT DISTINCT( {$distinct} ),
  COUNT(*) AS count
  FROM {$this->table->table_name}

  {$where}

  GROUP BY {$distinct}
SQL;

      $results = $wpdb->get_results( $sql, ARRAY_A );
      $result  = array();
      foreach ( $results as $res ) {
        $result[ $res[ $distinct ] ] = $res['count'];
      }

      return $result;
    }
  }

  // -------------------------------------------------------------------------------------------------------------------
  // UTILITIES
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Set one or more record wit a status
   *
   * @brief Set a status
   *
   * @param int    $id     Record ID
   * @param string $status Optional. The status, default WPDKDBTableRowStatuses::PUBLISH
   *
   * @return mixed
   */
  public function status( $id, $status = WPDKDBTableRowStatuses::PUBLISH )
  {
    /**
     * @var wpdb $wpdb
     */
    global $wpdb;

    // Stability
    if ( !empty( $id ) && !empty( $status ) ) {

      // Get the ID
      $id = implode( ',', (array)$id );

      $sql = <<< SQL
UPDATE {$this->table->table_name}
SET status = '{$status}'
WHERE id IN( {$id} )
SQL;

      $num_rows = $wpdb->query( $sql );

      return $num_rows;
    }
    return false;
  }

}