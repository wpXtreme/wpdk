<?php

/**
 * Manage the entire Database with usefule methods like:
 *
 * 1. Export
 *
 * @class           WPDKDB
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-10-24
 * @version         1.0.0
 * @since 1.6.2
 *
 */
class WPDKDB extends wpdb {

  // Default chanck size when dump a SQL export on filesystem.
  const DUMP_SQL_FILE_CHUNCK_SIZE = 100;

  /**
 	 * Whether to use mysqli over mysql.
 	 *
 	 * @var bool $mysqli
 	 */
 	public $mysqli = false;

  /**
   * Return a singleton instance of WPDKDB class
   *
   * @brief Singleton
   *
   * @return WPDKDB
   */
  public static function init()
  {
    static $instance = null;
    if( is_null( $instance ) ) {
      $instance = new self();
    }

    return $instance;
  }

  /**
   * Create an instance of WPDKDB class
   *
   * @brief Construct
   *
   * @return WPDKDB
   */
  public function __construct()
  {
    // Extends WordPress Database class
    parent::__construct( DB_USER, DB_PASSWORD, DB_NAME, DB_HOST );

    /*
     * Remake check for MySQLi extension because WordPress keep this flag as private.
     *
     * Use ext/mysqli if it exists and:
     *
     *  - WP_USE_EXT_MYSQL is defined as false, or
     *  - We are a development version of WordPress, or
     *  - We are running PHP 5.5 or greater, or
     *  - ext/mysql is not loaded.
     */
    if( function_exists( 'mysqli_connect' ) ) {
      if( defined( 'WP_USE_EXT_MYSQL' ) ) {
        $this->mysqli = !WP_USE_EXT_MYSQL;
      }
      elseif( version_compare( phpversion(), '5.5', '>=' ) || !function_exists( 'mysql_connect' ) ) {
        $this->mysqli = true;
      }
      elseif( false !== strpos( $GLOBALS[ 'wp_version' ], '-' ) ) {
        $this->mysqli = true;
      }
    }


  }

  /**
   * Reads the Database table in $table and creates SQL Statements for recreating structure and data then return the
   * DUMP SQL. If you set the $filename params then the dump is store on filesystem too.
   *
   * Taken partially from phpMyAdmin and partially from Alain Wolf, Zurich - Switzerland
   *
   * Website: http://restkultur.ch/personal/wolf/scripts/db_backup/
   *
   * @brief Dump
   *
   * @param string $table    Table name.
   * @param string $filename Optional. Complete path of a filename where store the dump.
   *
   * @return string
   */
  public function dumpWithTable( $table, $filename = '' )
  {
    // Prepare dump
    $dump = '';

    // Main information on dump
    $dump .= "# --------------------------------------------------------\n";
    $dump .= "# Date:      " . date( 'j F, Y H:i:s') ."\n";
    $dump .= "# Database:  " . DB_NAME ."\n";
    $dump .= "# Table:     " . $table ."\n";
    $dump .= "# --------------------------------------------------------\n";

    // Add SQL statement to drop existing table
    $dump .= "\n";
    $dump .= "#\n";
    $dump .= "# Delete any existing table " .self::backquote( $table ) . "\n";
    $dump .= "#\n";
    $dump .= "\n";
    $dump .= "DROP TABLE IF EXISTS " .self::backquote( $table ) . ";\n";

    // Comment in SQL-file
    $dump .= "\n";
    $dump .= "#\n";
    $dump .= "# Table structure of table " .self::backquote( $table ) . "\n";
    $dump .= "#\n";
    $dump .= "\n";

    // Get table structure
    $query  = 'SHOW CREATE TABLE ' .self::backquote( $table );
    $result = $this->mysqli ? mysqli_query( $this->dbh, $query ) : mysql_query( $query, $this->dbh );

    if( $result ) {

      // Get num rows
      $num_rows = $this->mysqli ? mysqli_num_rows( $result ) : mysql_num_rows( $result );

      if( $num_rows > 0 ) {
        $sql_create_arr = $this->mysqli ? mysqli_fetch_array( $result ) : mysql_fetch_array( $result );
        $dump .= $sql_create_arr[ 1 ];
      }

      if( $this->mysqli ) {
        mysqli_free_result( $result );
      }
      else {
        mysql_free_result( $result );
      }
      $dump .= ' ;';

    }

    // Get table contents
    $query  = 'SELECT * FROM ' . self::backquote( $table );

    /**
     * Filter the query used to select the rows to dump.
     *
     * The dynamic portion of the hook name, $table, refers to the database table name.
     *
     * @param string $query The SQL query.
     */
    $query = apply_filters( 'wpdk_db_dump_query-' . $table, $query );

    $result = $this->mysqli ? mysqli_query( $this->dbh, $query ) : mysql_query( $query, $this->dbh );

    $fields_cnt = 0;
    $rows_cnt   = 0;

    if( $result ) {
      $fields_cnt = $this->mysqli ? mysqli_num_fields( $result ) : mysql_num_fields( $result );
      $rows_cnt   = $this->mysqli ? mysqli_num_rows( $result ) : mysql_num_rows( $result );
    }

    // Comment in SQL-file
    $dump .= "\n";
    $dump .= "\n";
    $dump .= "#\n";
    $dump .= "# Data contents of table " . $table . " (" . $rows_cnt . " records)\n";
    $dump .= "#\n";
    $dump .= "\n";

    /**
     * Filter the addition SQL comment before printing the INSERT rows.
     *
     * The dynamic portion of the hook name, $table, refers to the database table name.
     *
     * @param string $comment Default empty.
     */
    $dump .= apply_filters( 'wpdk_db_dump_info_before_inserts-' . $table, '' );

    // Checks whether the field is an integer or not
    for( $j = 0; $j < $fields_cnt; $j++ ) {

      if( $this->mysqli ) {
        $object          = mysqli_fetch_field_direct( $result, $j );
        $field_set[ $j ] = $object->name;
        $type            = $object->type;
      }
      else {
        $field_set[ $j ] = self::backquote( mysql_field_name( $result, $j ) );
        $type            = mysql_field_type( $result, $j );
      }

      // Is number?
      $field_num[ $j ] = in_array( $type, array( 'tinyint', 'smallint', 'mediumint', 'int', 'bigint' ) );
    }

    // Sets the scheme
    $entries     = 'INSERT INTO ' . self::backquote( $table ) . ' VALUES (';
    $search      = array( '\x00', '\x0a', '\x0d', '\x1a' ); //\x08\\x09, not required
    $replace     = array( '\0', '\n', '\r', '\Z' );
    $current_row = 0;
    $batch_write = 0;

    while( $row = $this->mysqli ? mysqli_fetch_row( $result ) : mysql_fetch_row( $result ) ) {

      $current_row++;

      // build the statement
      for( $j = 0; $j < $fields_cnt; $j++ ) {

        if( !isset( $row[ $j ] ) ) {
          $values[ ] = 'NULL';

        }
        elseif( $row[ $j ] === '0' || $row[ $j ] !== '' ) {

          // a number
          if( $field_num[ $j ] ) {
            $values[ ] = $row[ $j ];
          }

          else {
            $values[ ] = "'" . str_replace( $search, $replace, self::addslashes( $row[ $j ] ) ) . "'";
          }

        }
        else {
          $values[ ] = "''";
        }

      }

      $dump .= "\n" . $entries . implode( ', ', $values ) . ") ;";

      // write the rows in batches of 100
      if( $batch_write === self::DUMP_SQL_FILE_CHUNCK_SIZE ) {
        $batch_write = 0;

        // Write on disk
        if( !empty( $filename ) ) {
          $result = WPDKFilesystem::append( $dump, $filename );

          // TODO Fires an error or filters to stop the execution

          $dump = '';
        }
      }

      $batch_write++;

      unset( $values );

    }

    if( $this->mysqli ) {
      mysqli_free_result( $result );
    }
    else {
      mysql_free_result( $result );
    }

    // Create footer/closing comment in SQL-file
    $dump .= "\n";
    $dump .= "\n";
    $dump .= "#\n";
    $dump .= "# End of data contents of table " . $table . "\n";
    $dump .= "# --------------------------------------------------------\n";
    $dump .= "\n";
    $dump .= "\n";

    // Write on disk
    if( !empty( $filename ) ) {
      $result = WPDKFilesystem::append( $dump, $filename );

      // TODO Fires an error
    }

    return $dump;


  }

  /**
   * Utility method to import a SQL (pure) file usually an export.
   *
   * @brief Import from a file.
   *
   * @param string $filename Path of file.
   *
   * @return bool|mysqli_result|resource|\WPDKError
   */
  public function importWithFilename( $filename )
  {
    // Load
    $sql = file_get_contents( $filename );

    // Stability
    if( empty( $sql ) ) {
      return new WPDKError( 'wpxdbm-import-sql-file-empty', __( 'SQL file empty' ), $filename );
    }

    $result = $this->executeQuery( $sql );

    //WPXtreme::log( $result, 'executeQuery' );

    if( false === $result ) {
      return new WPDKError( 'wpdk-db-import-query', __( 'Error while import:'  ) . ' ' . $this->last_error, array( $filename, $sql ) );
    }

    return $result;
  }

  /**
   * Utility to execute a pure query in MySQL or MySQLi extension.
   *
   * @brief Execute SQL statement
   *
   * @param string $query SQL statement.
   *
   * @return bool|mysqli_result|resource
   */
  public function executeQuery( $query )
  {
    // Remove comments
    $query = self::removeComments( $query );

    //WPXtreme::log( $query, '$query' );

    // Explode for statement
    $stack = preg_split( '/[$;]\s+\n/m', $query );

    //WPXtreme::log( $stack, '$stack' );

    // Loop into the statements
    foreach( $stack as $sql ) {
      $sql_line = trim( $sql );
      if( !empty( $sql_line ) ) {
        $sql_line .= ';';

        //WPXtreme::log( $sql_line, 'EXECUTE' );

        $result = $this->mysqli ? mysqli_query( $this->dbh, $sql_line ) : mysql_query( $sql_line, $this->dbh );
      }
    }

    return $result;

  }

  // -------------------------------------------------------------------------------------------------------------------
  // STATIC UTILITIES HELPER METHODS
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Add backquotes to string or array group. Return a string if input param is a string, otherwise return an array if
   * the input params is an array.
   *
   * @param string|array $value Table or list of table.
   *
   * @return array|string
   */
  public static function backquote( $value )
  {

    if( !empty( $value ) && is_string( $value ) && '*' == $value ) {
      return $value;
    }

    if( is_array( $value ) ) {
      return array_map( create_function( '$a', 'return "`" . $a . "`";' ), $value );
    }

    return sprintf( '`%s`',  $value );

  }

  /**
   * Better addslashes for SQL queries.
   * Taken from phpMyAdmin.
   *
   * @param string $value   Optional. Default empty.
   * @param bool   $is_like Optional. Default FALSE.
   *
   * @return mixed
   */
  public static function addslashes( $value = '', $is_like = false )
  {

    if( $is_like ) {
      $value = str_replace( '\\', '\\\\\\\\', $value );
    }

    else {
      $value = str_replace( '\\', '\\\\', $value );
    }

    $value = str_replace( '\'', '\\\'', $value );

    return $value;
  }

  /**
   * Return a SQL statement without comments.
   *
   * @brief Remove commnets.
   *
   * @param string $query SQL statement.
   *
   * @return string
   */
  public static function removeComments( $query )
  {
    // Remove comments
    $pattern = '/^-{2,}.*|^#\s.*/m';
    $query = trim( preg_replace( $pattern, '', $query ) );

    return $query;
  }


}


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
   * Insert a record by values and return the id on successfull, otherwise return WP_Error.
   *
   * The insert result is store in `$this->crud_results`.
   *
   * @brief Insert
   *
   * @use $this->crud_results
   *
   * @param string $prefix A prefix used for filter/action hook, eg: carrier, stat, ...
   * @param array  $values Array keys values
   * @param array  $format Optional. Array keys values for format null values
   *
   * @return int|WP_Error
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

    if ( false === $this->crud_results ) {
      return new WP_Error( $prefix . '-insert', __( 'Error while insert' ), array( $this->table_name, $values, $format ) );
    }

    // Get the id
    $id = $wpdb->insert_id;

    /**
     * Fires when a record is inserted.
     *
     * @param bool  $result Result of insert.
     * @param array $values Array with values of insert.
     */
    do_action( $prefix . '_inserted', $this->crud_results, $values );

    // Return the id
    return $id;
  }

  /**
   * Select data
   *
   * @brief Select
   * @note  Override this method with your own select
   */
  public function select()
  {
    // Override this method with your own select
  }

  /**
   * Update a record by values and retrun TRUE onsuccessfully, otherwise return a WP_Error.
   *
   * The update result is store in `$this->crud_results`.
   *
   * @brief Update
   *
   * @use $this->crud_results
   *
   * @param string $prefix A prefix used for filter/action hook, eg: carrier, stat, ...
   * @param array  $values Array keys values
   * @param array  $where  Array keys values for where update
   * @param array  $format Optional. Array keys values for format null values
   *
   * @return int|WP_Error
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

    if ( false === $this->crud_results ) {
      return new WP_Error( $prefix . '-update', __( 'Error while update' ), array( $values, $where, $format ) );
    }

    /**
     * Fires when a record is updated.
     *
     * @param bool|int $result Returns the number of rows updated, or false if there is an error.
     * @param array    $values Array with values of update.
     * @param array    $where  Array with values of where condiction.
     */
    do_action( $prefix . '_updated', $this->crud_results, $values, $where );

    // Successfully
    return true;
  }

  /**
   * Delete one or more record from table. Return the number of rows affected/selected or WP_Error.
   * Use the primaryKey.
   *
   * The delete result is store in `$this->crud_results`.
   *
   * @brief Delete
   *
   * @use $this->crud_results
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

    $ids = trim( implode( ',', (array) $id ) );

    // Stability
    if ( empty( $ids ) ) {
      return false;
    }

    /**
     * Fires before delete records from table.
     *
     * @param array $ids An array with the list of id.
     */
    do_action( 'wpdk_db_table_model_will_delete-' . $this->table_name, $ids );

    $sql = <<< SQL
DELETE FROM {$this->table_name}
WHERE {$this->primary_key} IN( {$ids} )
SQL;

    $this->crud_results = $wpdb->query( $sql );

    if( false === $this->crud_results ) {
      return new WP_Error( 'delete', __( 'Error while delete' ), $sql );
    }

    return $this->crud_results;
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

    if ( ! empty( $this->sql_filename ) && ! empty( $this->table_name ) ) {
      if ( ! function_exists( 'dbDelta' ) ) {
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
      $sql_sanitize = WPDKDB::removeComments( $sql );

      //WPXtreme::log( $sql_sanitize );

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
    if ( isset( $args[ $key ] ) && ! empty( $args[ $key ] ) && ! in_array( $args[ $key ], (array) $not_in ) ) {

      // Append dot to table if exists
      $table_prefix = empty( $table_prefix ) ? '' : $table_prefix . '.';

      // Every array
      $array = (array) $args[ $key ];
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
    if ( isset( $args[ $column_key ] ) && ! empty( $args[ $column_key ] ) ) {

      // Append dot to table if exists
      $table_prefix = empty( $table_prefix ) ? '' : $table_prefix . '.';

      // If $args[$column_key] is a string as '2014-01-01'
      if ( is_string( $args[ $column_key ] ) ) {
        $stack[] = sprintf( "DATE_FORMAT( %s%s, '%s' ) %s '%s'", $table_prefix, $column_key, $accuracy, '=', $args[ $column_key ] );
      }
      // Handle if $args[$column_key] is an array as array( '2014-01-01', '2014-02-01' )
      elseif ( is_array( $args[ $column_key ] ) ) {

        // Handle if $args[$column_key] is an array as array( false, '2014-02-01' )
        if ( ! empty( $args[ $column_key ][0] ) ) {
          $stack[] = sprintf( "DATE_FORMAT( %s%s, '%s' ) >= '%s'", $table_prefix, $column_key, $accuracy, $args[ $column_key ][0] );
        }

        // Handle if $args[$column_key] is an array as array( '2014-02-01' ) or array( '2014-02-01', false )
        if ( isset( $args[ $column_key ][1] ) && ! empty( $args[ $column_key ][1] ) ) {
          $stack[] = sprintf( "DATE_FORMAT( %s%s, '%s' ) <= '%s'", $table_prefix, $column_key, $accuracy, $args[ $column_key ][1] );
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
    if ( isset( $args[ $filter ] ) && ! empty( $args[ $filter ] ) && ! in_array( $args[ $filter ], (array) $not_in ) ) {
      $args[ $key ] = $args[ $filter ];

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
 * @date            2014-09-09
 * @version         1.0.1
 *
 * @history         1.0.1 - Refresh methods insert(), update() and delete()
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
    if ( ! empty( $table_name ) ) {
      $this->table = new WPDKDBTableModel( $table_name, $sql_file );
    }
  }

  // -------------------------------------------------------------------------------------------------------------------
  // CRUD
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Insert a record by values. Return FALSE if error or id of record if successfully.
   *
   * You can override this method in your subclass.
   *
   * @brief    Insert
   *
   * @param string $prefix A prefix used for filter/action hook, eg: carrier, stat, ...
   * @param array  $values Array keys values
   * @param array  $format Optional. Array keys values for format null values
   *
   * @sa       WPDKDBTableModel::insert()
   *
   * @return int|bool
   */
  public function insert()
  {

    // Stability warning
    if ( is_null( $this->table ) ) {
      die( __METHOD__ . ' must be override in your subclass' );
    }

    /*
     * since 1.5.1
     * try to avoid 'PHP Strict Standards:  Declaration of ::insert() should be compatible with self::insert'
     *
     * Remeber that if a params is missing it is NULL
     */
    $args = func_get_args();
    list( $prefix, $values ) = $args;
    $format = isset( $args[2] ) ? $args[2] : array();

    // Get the id
    return $this->table->insert( $prefix, $values, $format );
  }

  /**
   * Update a record by values. Return FALSE if error or the $where condiction if successfully.
   * You can use the $where condiction returned to get again the record ID.
   *
   * You can override this method in your subclass.
   *
   * @brief    Update
   *
   * @param string $prefix A prefix used for filter/action hook, eg: carrier, stat, ...
   * @param array  $values Array keys values
   * @param array  $where  Array keys values for where update
   * @param array  $format Optional. Array keys values for format null values
   *
   * @sa       WPDKDBTableModel::update()
   *
   * @return array|bool
   */
  public function update()
  {
    // Stability warning
    if ( is_null( $this->table ) ) {
      die( __METHOD__ . ' must be override in your subclass' );
    }

    /*
     * since 1.5.1
     * try to avoid 'PHP Strict Standards:  Declaration of ::update() should be compatible with self::update'
     *
     * Remeber that if a params is missing it is NULL
     */
    $args = func_get_args();
    list( $prefix, $values, $where ) = $args;
    $format = isset( $args[3] ) ? $args[3] : array();

    // Get the id
    return $this->table->update( $prefix, $values, $where, $format );
  }

  /**
   * Delete one or more record from table. Return the number of rows affected/selected or false on error.
   * Use the primaryKey.
   *
   * You can override this method in your subclass.
   *
   * @brief Delete
   * @since 1.5.16
   *
   * @param int|array $id Any single int or array list of primary keys
   *
   * @sa    WPDKDBTableModel::delete()
   *
   * @return int|bool
   */
  public function delete( $id )
  {
    // Stability warning
    if ( is_null( $this->table ) ) {
      die( __METHOD__ . ' must be override in your subclass' );
    }

    return $this->table->delete( $id );
  }

  /**
   * Return the integer count of all rows when $distinct param is emmpty or an array of distinct count for $distinct column.
   *
   * @brief    Count
   *
   * @param string       $distinct Optional. Name of field to distinct group by
   * @param array|string $status   Optional. Key value paier for where condiction on field: key = fields, vallue = value
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
    if ( ! empty( $status ) && is_array( $status ) ) {
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

      //WPXtreme::log( $sql );

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

      //WPXtreme::log( $sql );

      $results = $wpdb->get_results( $sql, ARRAY_A );

      // Prepare result array
      $result  = array();

      // Prepare all
      $result[ WPDKDBTableRowStatuses::ALL ] = 0;

      // Loop into results
      foreach ( $results as $res ) {
        $result[ $res[ $distinct ] ] = $res['count'];
        $result[ WPDKDBTableRowStatuses::ALL ] += $res['count'];
      }

      return $result;
    }
  }

  // -------------------------------------------------------------------------------------------------------------------
  // UTILITIES
  // -------------------------------------------------------------------------------------------------------------------

  /**
   * Set one or more record with a status.
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
    if ( ! empty( $id ) && ! empty( $status ) ) {

      // Get the ID
      $id = implode( ',', (array) $id );

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