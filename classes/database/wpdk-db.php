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
   * Create an instance of WPDKDBTableModel class
   *
   * @brief Construct
   *
   * @param string $table_name The name of the database table without WordPress prefix
   * @param string $sql_file   Optional. The filename of SQL file with the database table structure and init data.
   * @return WPDKDBTableModel
   */
  public function __construct( $table_name, $sql_file = '' )
  {
    global $wpdb;

    // Add the WordPres database prefix
    $this->table_name = sprintf( '%s%s', $wpdb->prefix, $table_name );

    // $sql_file must be complete of path
    $this->sql_filename = $sql_file;

    // Try to get the Primary key
    $this->primary_key = $this->primary_key();
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
  public function primary_key()
  {
    global $wpdb;

    $db = DB_NAME;

    $sql = <<< SQL
SELECT COLUMN_NAME
FROM information_schema.COLUMNS
WHERE (TABLE_SCHEMA = '{$db}')
AND (TABLE_NAME = '{$this->table_name}')
AND (COLUMN_KEY = 'PRI');
SQL;

    return $wpdb->get_var( $sql );
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
    global $wpdb;

    $ids = implode( ',', (array)$id );

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
    global $wpdb;

    // Hide database warning and error
    $wpdb->hide_errors();
    $wpdb->suppress_errors();

    // Buffering
    ob_start();

    if ( !empty( $this->sqlFilename ) && !empty( $this->tableName ) ) {
      if ( !function_exists( 'dbDelta' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      }
      $content = file_get_contents( $this->sqlFilename );
      if ( empty( $content ) ) {
        ob_end_clean();
        return false;
      }

      // Replace table name
      $sql = str_replace( '%s', $this->tableName, $content );

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

  // You'll override with CRUD

}

/**
 * Use this class when your database model is shows in a list table view controller
 *
 * @class           WPDKDBTableModelListTable
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-03-02
 * @version         1.0.0
 *
 */
class WPDKDBTableModelListTable extends WPDKDBTableModel {

  /**
   * Used for check the action and bulk action results
   *
   * @brief Action result
   *
   * @var bool $action_result
   */
  public $action_result = false;

  /**
   * Create an instance of WPDKDBTableModelListTable class
   *
   * @brief Construct
   *
   * @param string $table_name The name of the database table without WordPress prefix
   * @param string $sql_file   Optional. The filename of SQL file with the database table structure and init data.
   *
   * @return WPDKDBTableModelListTable
   */
  public function __construct( $table_name, $sql_file = '' )
  {
    // Init the database table model
    parent::__construct( $table_name, $sql_file );

    // Add action to get the post data
    $action = get_class( $this ) . '-listtable-viewcontroller';
    add_action( $action, array( $this, 'process_bulk_action' ) );
  }

  /**
   * Return a key value pairs array with the list of columns
   *
   * @brief Return the list of columns
   *
   * @return array
   */
  public function get_columns()
  {
    return array();
  }

  /**
   * Return the sortable columns
   *
   * @brief Sortable columns
   *
   * @return array
   */
  public function get_sortable_columns()
  {
    return array();
  }

  /**
   * Return a key value pairs array with statuses supported.
   * You can override this method to return your own statuses.
   *
   * @brief Statuses
   *
   * @return array
   */
  public function get_statuses()
  {
    // Default return the common statuses
    return WPDKDBTableRowStatuses::statuses();
  }

  /**
   * Return a key value pairs array with statuses icons glyph
   *
   * @brief Icons
   *
   * @return array
   */
  public function get_icon_statuses()
  {
    // Default return the common statuses
    return WPDKDBTableRowStatuses::icon_statuses();
  }

  /**
   * Return the count of specific status
   *
   * @brief Count status
   *
   * @param string $status
   *
   * @return int
   */
  public function get_status( $status )
  {
    return;
  }

  /**
   * Return tha array with the action for the current status
   *
   * @brief Action with status
   *
   * @param mixed  $item   The item
   * @param string $status Current status
   *
   * @return array
   */
  public function get_actions_with_status( $item, $status )
  {
    return array();
  }

  /**
   * Return the array with the buk action for the combo menu for a status of view
   *
   * @brief Bulk actions
   *
   * @param string $status Current status
   *
   * @return array
   */
  public function get_bulk_actions_with_status( $status )
  {
    return array();
  }

  /**
   * Get the current action selected from the bulk actions dropdown.
   *
   * @brief Current action
   *
   * @return string|bool The action name or FALSE if no action was selected
   */
  public static function action()
  {
    if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] ) {
      return $_REQUEST['action'];
    }

    if ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] ) {
      return $_REQUEST['action2'];
    }

    return false;
  }

  /**
   * Return the items array. This is an array of key value pairs array
   *
   * @brief Items
   *
   * @return array
   */
  public function select()
  {
    die( __METHOD__ . ' must be override in your subclass' );
  }

  /**
   * Process actions
   *
   * @brief Process actions
   * @since 1.4.21
   */
  public function process_bulk_action()
  {
    // Override when you need to process actions before wp is loaded
  }

}












/**
 * Standard default statuses for a generic table
 *
 * ## Overview
 * This class enum the standard default statuses for a database table
 *
 * @class              __WPDKDBTable
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2014-01-08
 * @version            1.0.1
 * @deprecated         since v1.5.1 - Use WPDKDBTableRowStatuses instead
 *
 */
class WPDKDBTableStatus extends WPDKObject {
  const ALL     = 'all';
  const PUBLISH = 'publish';
  const DRAFT   = 'draft';
  const TRASH   = 'trash';

  /**
   * Override WPDKObject version
   *
   * @brief Version
   *
   * @var string $__version
   */
  public $__version = '1.0.1';

  /**
   * Return a key pairs array with the list of statuses
   *
   * @brief Statuses
   * @since 1.3.0
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
   * Return a sanitized status
   *
   * @brief Sanitize status
   * @since 1.3.0
   *
   * @param string $status   Single status usually pass via $_POST or $_GET
   * @param array  $statuses Optional. A key pairs array of allowed statuses, if null get self::statuses
   *
   * @return bool|string
   */
  public static function sanitizeStatus( $status, $statuses = null )
  {
    $status   = esc_attr( $status );
    $statuses = is_null( $statuses ) ? self::statuses() : $statuses;
    $allowed  = array_keys( $statuses );
    if ( !in_array( $status, $allowed ) ) {
      return false;
    }
    return $status;
  }

}

/**
 * Model for a classic WordPress database table
 *
 * ## Overview
 * This class describe a database table. For default describe the table and gets the primary key name. This primary key
 * is used for operations as delete one or more records.
 *
 * @class              __WPDKDBTable
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-12-10
 * @version            0.1.0
 * @deprecated         since 1.5.1 - Use WPDKDBTableModel instead
 */

class __WPDKDBTable {

  /**
   * Name of field as primary key
   *
   * @brief Primary key
   *
   * @var string $primaryKey
   */
  public $primaryKey;

  /**
   * The filename of SQL file with the database table structure and init data.
   *
   * @brief SQL file
   *
   * @var string $sqlFilename
   */
  public $sqlFilename;

  /**
   * The name of the database table with the WordPress prefix
   *
   * @brief Table name
   *
   * @var string $tableName
   */
  public $tableName;

  /**
   * Create an instance of __WPDKDBTable class
   *
   * @brief Construct
   *
   * @param string $table_name The name of the database table without WordPress prefix
   * @param string $sql_file   Optional. The filename of SQL file with the database table structure and init data.
   *
   * @retur __WPDKDBTable
   */
  public function __construct( $table_name, $sql_file = '' )
  {
    global $wpdb;

    /* Add the WordPres database prefix. */
    $this->tableName = sprintf( '%s%s', $wpdb->prefix, $table_name );

    /* $sql_file must be complete of path. */
    $this->sqlFilename = $sql_file;

    /* Get the Primary key. */
    $this->primaryKey = $this->primaryKey();
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
    global $wpdb;

    $db = DB_NAME;

    $sql = <<< SQL
SELECT COLUMN_NAME
FROM information_schema.COLUMNS
WHERE (TABLE_SCHEMA = '{$db}')
  AND (TABLE_NAME = '{$this->tableName}')
  AND (COLUMN_KEY = 'PRI');
SQL;
    return $wpdb->get_var( $sql );
  }

  /**
   * Return the integer count of all rows when $distinct param is emmpty or an array of distinct count for $distinct column.
   *
   * @brief Count
   *
   * @param string $distinct Optional. Name of field to distinct group by
   * @param array  $status   Optional. Key value paier for where condiction on field: key = fields, vallue = value
   *
   * @return int|array
   */
  public function count( $distinct = '', $status = '' )
  {
    global $wpdb;

    $where = '';
    if ( !empty( $status ) && is_array( $status ) ) {
      if ( is_numeric( $status[key( $status )] ) ) {
        $where = sprintf( 'WHERE %s = %s', key( $status ), $status[key( $status )] );
      }
      else {
        $where = sprintf( "WHERE %s = '%s'", key( $status ), $status[key( $status )] );
      }
    }

    if ( empty( $distinct ) ) {
      $sql = <<< SQL
SELECT COUNT(*) AS count
  FROM {$this->tableName}
  {$where}
SQL;
      return absint( $wpdb->get_var( $sql ) );
    }
    else {
      $sql = <<< SQL
SELECT DISTINCT({$distinct}),
  COUNT(*) AS count
  FROM {$this->tableName}
  {$where}
  GROUP BY {$distinct}
SQL;

      $results = $wpdb->get_results( $sql, ARRAY_A );
      $result  = array();
      foreach ( $results as $res ) {
        $result[$res[$distinct]] = $res['count'];
      }
      return $result;
    }
  }


  /**
   * Delete one or more record from table. Return the number of rows affected/selected or false on error.
   * Use the primaryKey.
   *
   * @brief Delete
   *
   * @param int|array $pks Any single int or array list of primary keys
   *
   * @return int|bool
   */
  public function delete( $pks )
  {
    global $wpdb;

    if ( !is_array( $pks ) ) {
      $pks = array( $pks );
    }

    $ids = implode( ',', $pks );

    $sql    = <<< SQL
DELETE FROM {$this->tableName}
WHERE $this->primaryKey IN({$ids})
SQL;
    $result = $wpdb->query( $sql );

    return $result;
  }

  /**
   * Return a column select group by and sorter
   *
   * @brief Group by a column
   *
   * @param string $column   name of column
   * @param bool   $order_by Optional. Order for column. Defaul TRUE
   * @param string $order    Optional. Type of sorter. Default 'ASC'
   *
   * @return array
   */
  public function groupBy( $column, $order_by = true, $order = 'ASC' )
  {
    global $wpdb;

    $sql_order = $order_by ? sprintf( 'ORDER BY %s %s', $column, $order ) : '';

    $sql     = <<< SQL
SELECT $column
  FROM {$this->tableName}
  GROUP BY $column
  {$sql_order}
SQL;
    $results = $wpdb->get_results( $sql, ARRAY_A );
    $result  = array();
    foreach ( $results as $res ) {
      if ( !empty( $res[$column] ) ) {
        $result[] = $res[$column];
      }
    }
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
          call_user_func( array(
                               $destination_object,
                               'column_' . $field
                          ), $value );
        }
      }
      return $destination_object;
    }
    return false;
  }

  /**
   * Return a single instance of $object class or an array of $object class. FALSE otherwise.
   * If select more rows as array, the OBJECT_K flag is used. In this way you have the key array equal to id of the country
   *
   * @brief      Select a record set
   *
   * @param int    $id         ID of record or array of id
   * @param object $object     Object to map record fields. This object is maped when id is a single number.
   * @param string $output     Optional. Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants. With one of the first
   *                           three, return an array of rows indexed from 0 by SQL result row number.
   *                           Each row is an associative array (column => value, ...), a numerically indexed array
   *                           (0 => value, ...), or an object. ( ->column = value ), respectively.
   *                           With OBJECT_K, return an associative array of row objects keyed by the value of each
   *                           row's first column's value. Duplicate keys are discarded.
   *
   * @deprecated Use _select() instead
   * @todo       This method should be improve with new object_query and where() method below.
   *
   * @return object|array
   */
  public function select( $id = false, $order_by = '', $order = 'ASC', $where = '' )
  {
    global $wpdb;

    $sql_where = '';
    if ( !empty( $id ) ) {
      if ( is_array( $id ) ) {
        $id = implode( ',', $id );
      }
      $sql_where = sprintf( ' AND %s IN(%s)', $this->primaryKey, $id );
    }

    if ( !empty( $where ) ) {
      $sql_where = sprintf( '%s AND %s ', $sql_where, $where );
    }

    $order_by = empty( $order_by ) ? $this->primaryKey : $order_by;

    $sql = <<< SQL
SELECT * FROM {$this->tableName}
WHERE 1 {$sql_where}
ORDER BY {$order_by} {$order}
SQL;

    $rows = $wpdb->get_results( $sql, OBJECT_K );

    return $rows;
  }

  /**
   * Return an array key-value with key as primary key of records.
   *
   * @brief Select
   *
   * @param object $query    Optional. $query An object used for build the where. Usualy a subclass of WPDKDBTableRow
   * @param string $order_by Optional. Order by column name
   * @param string $order    Optional. Order. Default ASC
   *
   * @return array
   */
  public function _select( $query = null, $order_by = '', $order = 'ASC' )
  {
    global $wpdb;

    /* Sanitize order by. */
    $order_by = empty( $order_by ) ? $this->primaryKey : $order_by;

    /* Build where condiction from an obejct (single record). */
    $where = $this->where( $query );

    $sql = <<<SQL
SELECT * FROM {$this->tableName}
WHERE 1 = 1

{$where}

ORDER BY {$order_by} {$order}
SQL;

    $results = $wpdb->get_results( $sql, OBJECT_K );

    return $results;
  }

  /**
   * Do an update the table via WordPress dbDelta() function. Apply a new SQL file on the exists (or do not exists)
   * table. Return TRUE on success
   *
   * @brief Update table
   *
   * @return bool
   */
  public function update()
  {
    global $wpdb;

    // Hide database warning and error
    $wpdb->hide_errors();
    $wpdb->suppress_errors();

    // Buffering
    ob_start();

    if ( !empty( $this->sqlFilename ) && !empty( $this->tableName ) ) {
      if ( !function_exists( 'dbDelta' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      }
      $content = file_get_contents( $this->sqlFilename );
      if ( empty( $content ) ) {
        ob_end_clean();
        return false;
      }

      // Replace table name
      $sql = str_replace( '%s', $this->tableName, $content );

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

  /**
   * Return the WHERE condiction string from a object
   *
   * @brief Build where condiction
   *
   * @param WPDKDBTableRow $query       An instance of WPDKDBTableRow
   * @param string         $prefix      Optional. Table prefix.
   *
   * @return string
   */
  public function where( $query, $prefix = '' )
  {
    $result = '';

    if ( !is_null( $query ) ) {

      /* Sanitize prefix. */
      if ( !empty( $prefix ) ) {
        $prefix = rtrim( $prefix, '.' ) . '.';
      }

      $desc  = $query->desc();
      $stack = array();

      /* Database type to be numeric. */
      $numeric = array(
        'bigint',
        'int',
        'decimal'
      );

      foreach ( $query as $property => $value ) {
        if ( isset( $desc[$property] ) && !is_null( $value ) ) {

          /* Remove ( from type. */
          $type = $desc[$property]->Type;
          $pos  = strpos( $type, '(' );
          $type = ( false === $pos ) ? $type : substr( $type, 0, $pos );

          /* Check for numeric and string. */
          // TODO Implement array support too when value is an array
          if ( in_array( $type, $numeric ) ) {
            $stack[] = sprintf( 'AND %s%s = %s', $prefix, $property, $value );
          }
          else {
            $stack[] = sprintf( 'AND %s%s = \'%s\'', $prefix, $property, $value );
          }
        }
      }

      if ( !empty( $stack ) ) {
        $result = implode( ' ', $stack );
      }
    }

    return $result;
  }

  /**
   * @deprecated Use delete() instead
   */
  public function deleteWherePrimaryKey( $id )
  {
    _deprecated_function( __METHOD__, '1.0.0', 'delete()' );
    $this->delete( $id );
  }

  /**
   * @deprecated Use select() instead
   */
  public function selectWhereID( $id, $object, $output = OBJECT )
  {
    _deprecated_function( __METHOD__, '1.0.0', 'select()' );
    $this->select( $id );
  }

}

/**
 * CRUD model for a single (record) row of database table
 *
 * ## Overview
 * This class is a map of a single record on database. When a record is loaded the column are mapped as properties of
 * this class. For this reason exist the internal private property _excludeProperties. It is used to avoid get the
 * class properties.
 *
 * ### Property naming
 * To avoid property override, all protected, private or public property of this class **must** start with a
 * underscore prefix.
 *
 * @class              WPDKDBTableRow
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-09-27
 * @version            1.0.1
 * @note               No stable - Used by SmartShop carrier
 * @deprecated         since 1.5.1
 *
 */

class WPDKDBTableRow {

  /**
   * Used for bypass null properties
   *
   * @brief Another NULL define
   *
   */
  const NULL_VALUE = '!NULL!';

  /**
   * An instance of table of record
   *
   * @brief Instance of table
   *
   * @var __WPDKDBTable $table
   */
  public $table;

  /**
   * Create an instance of WPDKDBTableRow class
   *
   * @brief Construct
   *
   * @param _WPDKDBTable     $dbtable    Object of database class
   * @param int|array|object $pk         Optional. Any id, array or object
   *
   * @return WPDKDBTableRow
   */
  public function __construct( __WPDKDBTable $dbtable, $pk = null )
  {
    $this->table = $dbtable;

    if ( !is_null( $pk ) ) {
      if ( is_numeric( $pk ) ) {
        $this->initByID( $pk );
      }
      elseif ( is_array( $pk ) ) {
        /* @todo */
      }
      elseif ( is_object( $pk ) ) {
        /* @todo */
      }
    }
  }

  /**
   * Return the array row and init this instance of WPDKDBTableRow from record ID. Return false if an error occour.
   *
   * @brief Init by record ID
   *
   * @param int $pk Record ID - primary key
   *
   * @return bool|array
   */
  private function initByID( $pk )
  {
    global $wpdb;

    /* @todo You can use $this->table->selectWithID( $id ); insetad */

    $sql = sprintf( 'SELECT * FROM %s WHERE %s = %s', $this->table->tableName, $this->table->primaryKey, $pk );

    /* Override. */
    $sql = $this->get_sql( $pk, $sql );

    /* Notify to all */
    $sql = apply_filters( 'wpdk_db_table_' . $this->table->tableName . '_sql', $sql );

    $row = $wpdb->get_row( $sql );

    if ( !is_null( $row ) ) {
      foreach ( $row as $property => $value ) {
        $this->$property = $value;
        if ( method_exists( $this, 'column_' . $property ) ) {
          call_user_func( array( $this, 'column_' . $property ), $value );
        }
      }
      return $row;
    }
    return false;
  }

  /**
   * You can override this method to change the SQL used to retrive te single record information
   *
   * @brief SQL
   *
   * @param int    $pk  The primary id of record
   * @param string $sql The SQL select used to retrive the single record information
   *
   * @return string
   */
  public function get_sql( $pk, $sql )
  {
    return $sql;
  }

  /**
   * Return an instance of WPDKDBTableRow class
   *
   * @brief Get a row
   *
   * @param __WPDKDBTable    $dbtable An instance of __WPDKDBTable class
   * @param int|array|object $pk      Optional. Any id, array or object
   *
   * @note  Not uset yet
   *
   * @return WPDKDBTableRow
   */
  public static function getInstance( __WPDKDBTable $dbtable, $pk = null )
  {
    $instance = new WPDKDBTableRow( $dbtable, $pk );
    return $instance;
  }

  /**
   * Return the DESC table
   *
   * @brief Description
   *
   * @return mixed
   */
  public function desc()
  {
    global $wpdb;
    $sql = sprintf( 'DESC %s', $this->table->tableName );

    $result = $wpdb->get_results( $sql, OBJECT_K );

    return $result;
  }

  /**
   * Override this method to return a filtered key pairs array with column name and default value
   *
   * @brief Defaults value
   */
  public function defaults()
  {
    /* Override */
    return array();
  }
}

/// @endcond

