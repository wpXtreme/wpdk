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
 * Standard default statuses for a generic table
 *
 * ## Overview
 * This class enum the standard default statuses for a database table
 *
 * @class              __WPDKDBTable
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-12-10
 * @version            0.1.0
 *
 */
class WPDKDBTableStatus {
  const ALL     = 'all';
  const PUBLISH = 'publish';
  const TRASH   = 'trash';
}

/**
 * Model for a classic WordPress database table
 *
 * ## Overview
 *
 * @class              __WPDKDBTable
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-12-10
 * @version            0.1.0
 *
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
  public function __construct( $table_name, $sql_file = '' ) {
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
   * @note In case you can override this method and return your pwn primary key
   *
   * @return string
   */
  public function primaryKey() {
    global $wpdb;

    $db = DB_NAME;

    $sql = <<< SQL
SELECT `COLUMN_NAME`
FROM `information_schema`.`COLUMNS`
WHERE (`TABLE_SCHEMA` = '{$db}')
  AND (`TABLE_NAME` = '{$this->tableName}')
  AND (`COLUMN_KEY` = 'PRI');
SQL;
    return $wpdb->get_var( $sql );
  }

  /**
   * Do an update the table via WordPress dbDelta() function
   *
   * @brief Update table
   */
  public function update() {
    if ( !empty( $this->sqlFilename ) && !empty( $this->tableName ) ) {
      if ( !function_exists( 'dbDelta' ) ) {
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
      }
      $content = file_get_contents( $this->sqlFilename );
      /* @todo Check if content is empty */

      /* @todo Replace sprintf() with str_replace( '%s', $this->tableName ) - because more instances */
      $sql = sprintf( $content, $this->tableName );
      @dbDelta( $sql );
    }

  }

  /**
   * Map the properties fields of single database row into a destination object model.
   * The $destination_object can implement a method named `column_[property]` to override map process.
   *
   * @brief Map a database record into a model row
   *
   * @param object $source_row         Database record
   * @param object $destination_object Object to map
   *
   * @return object|bool The destination object with new properties or FALSE if error.
   */
  public function map( $source_row, $destination_object ) {
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
   * @deprecated Use select() instead
   */
  public function selectWhereID( $id, $object, $output = OBJECT ) {
    _deprecated_function( __CLASS__ . '::' . __FUNCTION__, '1.0.0', 'delete()' );
    $this->select( $id );
  }

  /**
   * Return a single instance of $object class or an array of $object class. FALSE otherwise.
   * If select more rows as array, the OBJECT_K flag is used. In this way you have the key array equal to id of the country
   *
   * @brief Select a record set
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
   * @todo This method should be improve with new object_query and where() method below.
   *
   * @return object|array
   */
  public function select( $id = false, $order_by = '', $order = 'ASC', $where = '' ) {
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
SELECT * FROM `{$this->tableName}`
WHERE 1 {$sql_where}
ORDER BY {$order_by} {$order}
SQL;

    $rows = $wpdb->get_results( $sql, OBJECT_K );

    return $rows;
  }

  /**
   * Return the integer count of all rows when $distinct param is emmpty or an array of distinct count for $distinct column.
   *
   * @brief Count
   *
   * @param string $distinct Optional. Name of field to distinct group by
   * @param array $status Optional. Key value paier for where condiction on field: key = fields, vallue = value
   *
   * @return int|array
   */
  public function count( $distinct = '', $status = '' ) {
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
  FROM `{$this->tableName}`
  {$where}
SQL;
      return absint( $wpdb->get_var( $sql ) );
    }
    else {
      $sql = <<< SQL
SELECT DISTINCT(`{$distinct}`),
  COUNT(*) AS count
  FROM `{$this->tableName}`
  {$where}
  GROUP BY `{$distinct}`
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
  public function groupBy( $column, $order_by = true, $order = 'ASC' ) {
    global $wpdb;

    $sql_order = $order_by ? sprintf( 'ORDER BY `%s` %s', $column, $order ) : '';

    $sql     = <<< SQL
SELECT `$column`
  FROM `{$this->tableName}`
  GROUP BY `$column`
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

  // -----------------------------------------------------------------------------------------------------------------
  // Delete
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * @deprecated Use delete() instead
   */
  public function deleteWherePrimaryKey( $id ) {
    _deprecated_function( __CLASS__ . '::' . __FUNCTION__, '1.0.0', 'delete()' );
    $this->delete( $id );
  }

  /**
   * Delete one or more record
   *
   * @brief Delete
   *
   * @param int|array $id Any id or array of id values
   *
   * @return mixed
   */
  public function delete( $id ) {
    global $wpdb;

    if ( is_array( $id ) ) {
      $id = join( ',', $id );
    }

    $sql = sprintf( 'DELETE FROM `%s` WHERE %s IN(%s)', $this->tableName, $this->primaryKey, $id );
    return $wpdb->query( $sql );
  }


  /**
   * Return the WHERE condiction string from a object
   *
   * @brief Build where condiction
   *
   * @param _WPDKDBTableRow $order_query An instance of _WPDKDBTableRow
   * @param string $prefix Optional. Table prefix.
   *
   * @return string
   */
  public function where( $order_query, $prefix = '' )
  {
    $result = '';

    if ( !is_null( $order_query ) ) {

      /* Sanitize prefix. */
      if ( !empty( $prefix ) ) {
        $prefix = rtrim( $prefix, '.' ) . '.';
      }

      $desc  = $order_query->desc();
      $stack = array();

      /* Database type to be numeric. */
      $numeric = array(
        'bigint',
        'int',
        'decimal'
      );

      foreach ( $order_query as $property => $value ) {
        if ( isset( $desc[$property] ) && !is_null( $value ) ) {

          /* Remove `(` from type. */
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

}

/**
 * CRUD model for a single (record) row of database table
 *
 * ## Overview
 * This class is a map of a single record on database. When a record is loaded the column are mapped as propertis of
 * this class. For this reason exist the internal private property `_excludeProperties`. It is used to avoid get the
 * class properties.
 *
 * ### Property naming
 * To avoid property override, all `protected`, `private` or `public` property of this class **must** start with a
 * underscore prefix.
 *
 * @class              _WPDKDBTableRow
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 * @note               No stable - Used by SmartShop carrier
 *
 */

class _WPDKDBTableRow {

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
   * Create an instance of _WPDKDBTableRow class
   *
   * @brief Construct
   *
   * @param _WPDKDBTable     $dbtable    Object of database class
   * @param int|array|object $id         Optional. Any id, array or object
   *
   * @return _WPDKDBTableRow
   */
  public function __construct( __WPDKDBTable $dbtable, $id = null ) {

    $this->table = $dbtable;

    if ( !is_null( $id ) ) {
      if ( is_numeric( $id ) ) {
        $this->initByID( $id );
      }
      elseif ( is_array( $id ) ) {
        /* @todo */
      }
      elseif ( is_object( $id ) ) {
        /* @todo */
      }
    }
  }

  /**
   * Return the array row and init this instance of _WPDKDBTableRow from record ID. Return false if an error occour.
   *
   * @brief Init by record ID
   *
   * @param int $id Record ID
   *
   * @return bool|array
   */
  private function initByID( $id ) {
    global $wpdb;

    /* @todo You can use $this->table->selectWithID( $id ); insetad */

    $sql = sprintf( 'SELECT * FROM %s WHERE %s = %s', $this->table->tableName, $this->table->primaryKey, $id );

    /* Override. */
    $sql = $this->get_sql( $id, $sql );

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
   * @param int    $id  The primary id of record
   * @param string $sql The SQL select used to retrive the single record information
   *
   * @return string
   */
  public function get_sql( $id, $sql ) {
    return $sql;
  }

  /**
   * Return an instance of _WPDKDBTableRow class
   *
   * @brief Get a row
   *
   * @param __WPDKDBTable    $dbtable Pointer to table
   * @param int|array|object $id      Optional. Any id, array or object
   *
   * @note Not uset yet
   *
   * @return _WPDKDBTableRow
   */
  public static function getInstance( __WPDKDBTable $dbtable, $id = null ) {
    $instance = new _WPDKDBTableRow( $dbtable, $id );
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

  // -----------------------------------------------------------------------------------------------------------------
  // CRUD Model - Create, Read, Update and Delete
  // -----------------------------------------------------------------------------------------------------------------

//  /**
//   * Create a new row. The Key value pairs array field => value is get form object properties. If a property is null or
//   * not set, the field => value is not passed to sql insert. For insert a NULL value use the constant
//   * `self::NULL_VALUE`
//   *
//   * @brief Create (insert) a new row
//   *
//   * @return bool|int Number of insert record or FALSE if error or not properties set
//   */
//  public function insert() {
//
//    $data = array();
//
//    foreach ( $this as $property => $value ) {
//      if ( !in_array( $property, $this->_excludeProperties ) ) {
//        if ( !is_null( $value ) ) {
//          $data[$property] = ( $value == self::NULL_VALUE ) ? null : $value;
//        }
//      }
//    }
//
//    if ( !empty( $data ) ) {
//      $result = $this->_wpdb->insert( $this->_table->tableName, $data );
//      return $result;
//    }
//
//    return false;
//  }
//
//  /**
//   * @brief Update a row
//   *
//   * @param array $and_where Key value pairs array for addition where condictions
//   *
//   * @return int|bool The number of rows updated, or FALSE on error.
//   */
//  public function update( $and_where = array() ) {
//
//    $index_name = $this->_indexName;
//    if ( empty( $this->$index_name ) ) {
//      return false;
//    }
//
//    $where = array(
//      $index_name => $this->$index_name
//    );
//
//    if ( !empty( $and_where ) ) {
//      $where = array_merge( $where, $and_where );
//    }
//
//    $data = array();
//
//    foreach ( $this as $property => $value ) {
//      if ( !in_array( $property, $this->_excludeProperties ) && $this->_indexName !== $property ) {
//        if ( !is_null( $value ) ) {
//          $data[$property] = ( $value == self::NULL_VALUE ) ? null : $value;
//        }
//      }
//    }
//
//    if ( !empty( $data ) ) {
//      $result = $this->_wpdb->update( $this->_table->tableName, $data, $where );
//      return $result;
//    }
//
//    return false;
//  }
//
//  /**
//   * Utility for return a generic object (stdClass) with properties and values mapped on record.
//   *
//   * @brief Return the original record object
//   *
//   * @return stdClass
//   */
//  public function toString() {
//    $data = new stdClass();
//
//    foreach ( $this as $property => $value ) {
//      if ( !in_array( $property, $this->_excludeProperties ) ) {
//        if ( !is_null( $value ) ) {
//          $data->$property = ( $value == self::NULL_VALUE ) ? null : $value;
//        }
//      }
//    }
//
//    return $data;
//  }

}

/// @endcond

