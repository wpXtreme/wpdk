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
 * Prototipo per una classe base per la gestione di tabelle sul database, dove viene applicato un modello CRUD.
 *
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               02/06/12
 * @version            1.0.0
 *
 * @deprecated         Since 0.6.2 - In working - Used yet by SmartShop
 *
 */

class WPDKDBTable {

    function slug( $table_name ) {
        return sanitize_title( $table_name );
    }


    // -----------------------------------------------------------------------------------------------------------------
    // CRUD Model
    // -----------------------------------------------------------------------------------------------------------------

    /* CREATE */

    /**
     * Crea, inserisce, un record in tabella
     *
     * @param string $table_name Nome della tabella
     * @param array $values Elenco Key value pairs, nome campo/valore
     *
     * @return mixed
     */
    static function create( $table_name, $values ) {
        global $wpdb;

        $result = $wpdb->insert( $table_name, $values );
        return $result;
    }


    /* READ */

    /**
     * Estrae un singolo record
     *
     * @param string           $table_name
     * @param int|object|array $record
     * @param string           $id_field
     * @param string           $output
     *
     * @return bool
     */
    function record( $table_name, $record, $id_field = 'id', $output = OBJECT ) {
        global $wpdb;

        $id_record = false;

        if ( is_numeric( $record ) ) {
            $id_record = $record;
        } elseif ( is_object( $record ) ) {
            $id_record = $record->$id_field;
        } elseif ( is_array( $record ) ) {
            $id_record = $record[$id_field];
        }

        if( false === $id_record ) {
            return false;
        }

        $sql = <<< SQL
SELECT * FROM `{$table_name}`
WHERE `{$id_field}` = {$id_record}
SQL;
        $result = $wpdb->get_row( $sql, $output );
        return $result;
    }

    /**
     * Estrae un insieme di record
     *
     * @param string $table_name
     * @param string $where
     * @param string $order_by
     * @param string $output
     *
     * @return mixed
     */
    function records( $table_name, $where, $order_by = '', $output = OBJECT ) {
        global $wpdb;

        $where = self::where( $where );

        $sql = <<< SQL
SELECT * FROM `{$table_name}`
{$where}
ORDER BY {$order_by}
SQL;
        $result = $wpdb->get_results( $sql, $output );

        return $result;

    }

    /**
     * Prepara una condizione di WHERE partendo da un semplice stringa o da un Key value pairs campo/valore. Se il valore
     * non è una stringa la condizione è campo = valore. Se il valore è una stringa la condizione è campo = 'valore'.
     * Le condizioni sono per defaul in AND, altrimenti passando $glue = 'OR' si modifica il legame.
     *
     *
     * @param string|array $where
     * @param string       $glue Default AND
     *
     * @return string
     */
    static function where( $where, $glue = 'AND' ) {
        $glue = sprintf( ' %s ', $glue );
        if ( !empty( $where ) && is_string( $where ) ) {
            $where = sprintf( 'WHERE 1 AND %s', $where );
        } elseif ( is_array( $where ) ) {
            $where_format = array();
            foreach ( $where as $field => $value ) {
                if ( is_string( $value ) ) {
                    $where_format[] = sprintf( '%s = "%s"', $field, $value );
                } else {
                    $where_format[] = sprintf( '%s = %s', $field, $value );
                }
            }
            $where = sprintf( 'WHERE 1 AND %s', join( $glue, $where_format ) );
        }
        return $where;
    }


    /* UPDATE */

    /**
     * Esegue l'update di un record
     *
     * @param string                $table_name
     * @param int|object|array      $record
     * @param array                 $values
     * @param string                $id_field
     * @param array                 $formats
     *
     * @return mixed
     */
    static function update( $table_name, $record, $values, $id_field = 'id', $formats = array() ) {
        global $wpdb;

        $id_record = false;

        if ( is_numeric( $record ) ) {
            $id_record = $record;
        } elseif ( is_object( $record ) ) {
            $id_record = $record->$id_field;
        } elseif ( is_array( $record ) ) {
            $id_record = $record[$id_field];
        }

        if( false === $id_record ) {
            return false;
        }

        $where = array(
            $id_field => $id_record
        );

        if ( empty( $formats ) ) {
            $result = $wpdb->update( $table_name, $values, $where );
        } else {
            $where_formats = array( '%d' );
            $result        = $wpdb->update( $table_name, $values, $where, $formats, $where_formats );
        }
        return $result;
    }


    /* DELETE */

    /**
     * Elimina uno o più record a partire sempre dall'id
     *
     * @param string          $table_name
     * @param int|array       $record Singolo ID o array di ID da elimininare
     * @param string          $id_field
     *
     * @note Elimina la chiave '_[table name]_status dai post meta. Questa viene usata per memorizzare lo stato precedente
     *       di un record, quando si ha una gestine a stati appunto: vedi 'trash' ad esempio.
     *       Vedi metodo update per dettagli.
     *
     * @return mixed
     */
    static function delete( $table_name, $record, $id_field = 'id' ) {
        global $wpdb;

        $id_records = false;

        if ( is_numeric( $record ) ) {
            $id_records = array( $record );
        }
        elseif ( is_array( $record ) ) {
            $id_records = $record;
        }

        if ( false === $id_records ) {
            return false;
        }

        $meta_key = sprintf( '_%s_status', $table_name );
        foreach ( $id_records as $id ) {
            delete_post_meta( $id, $meta_key );
        }

        $id_records = join( ',', $id_records );

        $sql    = <<< SQL
DELETE FROM `{$table_name}`
WHERE {$id_field} IN ({$id_records} )
SQL;
        $result = $wpdb->query( $sql );

        return $result;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Extra
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Restituisce il numero di record della tabella
     *
     * @param string $table_name
     *
     * @return int
     */
    static function count( $table_name ) {
        global $wpdb;

        $sql = <<< SQL
SELECT COUNT(*) AS count
FROM `{$table_name}`
SQL;
        return absint( $wpdb->get_var( $sql ) );
    }

    /**
     * Legge lo stato attuale di un record. Lo status è per default nel campo 'post_status' se presente.
     *
     * @param string       $table_name
     * @param int    $record
     * @param string $id_field
     * @param string $field_name
     *
     * @return mixed
     */
    function status( $table_name, $record, $id_field = 'id', $field_name = 'post_status' ) {
        global $wpdb;

        $sql    = <<< SQL
SELECT `{$field_name}`
FROM `{$table_name}`
WHERE `{$id_field}` = {$record}
SQL;
        $status = $wpdb->get_var( $sql );

        return $status;
    }


    /**
     * Imposta lo stato di uno o più record a 'trash' e memorizza lo stato attuale nella post meta con chiave
     * '_[table name]_status'
     *
     * @param string    $table_name
     * @param int|array $record
     * @param string    $id_field
     * @param string    $field_name
     * @param string    $value
     *
     * @return mixed
     */
    function trash( $table_name, $record, $id_field = 'id', $field_name = 'post_status', $value = 'trash' ) {
        global $wpdb;

        $id_records = false;

        if ( is_numeric( $record ) ) {
            $id_records = array( $record );
        }
        elseif ( is_array( $record ) ) {
            $id_records = $record;
        }

        if ( false === $id_records ) {
            return false;
        }

        /* Memorizzo lo stato precendete nella tabella options */
        foreach ( $id_records as $id ) {
            $meta_key        = sprintf( '_%s_%s_status', $table_name, $id );
            $previous_status = self::status( $table_name, $id, $id_field, $field_name );
            update_post_meta( $id, $meta_key, $previous_status );
        }

        $id_records = join( ',', $id_records );

        $sql = <<< SQL
UPDATE `{$table_name}`
SET `{$field_name}` = '{$value}'
WHERE `{$id_field}` IN ( {$id_records} )
SQL;

        $result = $wpdb->query( $sql );
        return $result;
    }

    /**
     * Repristina uno o più record dal cestino recuperando lo stato precedente dalla chiave ''_[table name]_status'
     * nella post meta. Se non la trova pone il record in status 'unknown'
     *
     * @param string    $table_name
     * @param int|array $record
     * @param string    $id_field
     * @param string    $field_name
     *
     * @return bool
     */
    function untrash( $table_name, $record, $id_field = 'id', $field_name = 'post_status' ) {
        global $wpdb;

        $id_records = false;

        if ( is_numeric( $record ) ) {
            $id_records = array( $record );
        } elseif ( is_array( $record ) ) {
            $id_records = $record;
        }

        if( false === $id_records ) {
            return false;
        }

        $result = false;

        foreach ( $id_records as $id ) {
            $meta_key        = sprintf( '_%s_%s_status', $table_name, $id );
            $previous_status = get_post_meta( $id, $meta_key, true );
            if ( empty( $previous_status ) ) {
                /* @todo Prendere il primo disponibile in base alla classe ereditaria */
                $previous_status = 'unknown';
            }
            $sql    = <<< SQL
UPDATE `{$table_name}`
SET `{$field_name}` = '{$previous_status}'
WHERE `{$id_field}` = {$id}
SQL;
            $result = $wpdb->query( $sql );

            delete_post_meta( $id, $meta_key );
        }

        return $result;
    }

    /**
     * Restituisce un array in formato SDF
     *
     *
     * @param array $statuses
     *
     * @return array
     */
    function arrayStatusesForSDF( $statuses ) {
        $result   = array();
        if ( !empty( $statuses ) ) {
            $result = array();
            foreach ( $statuses as $key => $status ) {
                $result[$key] = $status['label'];
            }
            /* @todo Questi? */
            unset( $result['all'] );
            unset( $result['trash'] );
        }
        return $result;
    }


    // -----------------------------------------------------------------------------------------------------------------
    // WordPress WP List Table
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Restituisce un array con il tipo di status, la sua label e la count sul database
     *
     * @param string $table_name
     * @param array  $statuses
     * @param string $field_name
     *
     * @return array Restituisce un array con il tipo di status, la sua label e la count sul database
     */
    function statusesWithCount( $table_name, $statuses, $field_name = 'status' ) {
        global $wpdb;

        $sql    = <<< SQL
SELECT DISTINCT( `{$field_name}` ),
       COUNT(*) AS count
FROM `{$table_name}`
GROUP BY `{$field_name}`
SQL;
        $result = $wpdb->get_results( $sql, ARRAY_A );

        foreach ( $result as $status ) {
            if ( !empty( $status['status'] ) ) {
                $statuses[$status['status']]['count'] = $status['count'];
            }
        }

        $statuses['all']['count'] = self::count( $table_name );

        return $statuses;
    }


    // -----------------------------------------------------------------------------------------------------------------
    // Cache
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Legge o imposta un transient/cache.
     * Questa verrà aggiornata ogni qualvolta viene inserito, modificato o cancellato un post.
     *
     * @note Non utilizzato per ora. Ci basiamo sulla cache di WordPress
     *
     * @param string   $table_name
     * @param int      $id     ID del post
     * @param stdClass $record Oggetto record dal database. Passare questo parametro per memorizzarlo in cache.
     *
     * @return stdClass|null Restituisce un oggetto di tipo stdClass o null se errore
     */
    public function cache( $table_name, $id, $record = null ) {
        $slug = self::slug( $table_name );

        if ( !WPDK_CACHE_RECORD ) {
            return self::record( $table_name, $id );
        } elseif ( WPDK_CACHE_RECORD && is_null( $record ) ) {
            if ( isset( $_SESSION[$slug . $id] ) ) {
                unserialize( $_SESSION[$slug . $id] );
            } else {
                $record                = self::record( $table_name, $id );
                $_SESSION[$slug . $id] = serialize( $record );
            }
        } elseif ( WPDK_CACHE_RECORD && is_object( $record ) ) {
            $_SESSION[$slug . $id] = serialize( $record );
        }
        return $record;
    }

}



/**
 * CRUD model for the database table
 *
 * ## Overview
 * Let's we say that is not easy design and coding a well done CRUD engine for WordPress. However we have try to do this
 * in a simple first release.
 *
 * @class              _WPDKDBTable
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2012-11-28
 * @version            0.8.1
 * @note               No stable
 * @deprecated         Since 0.9
 *
 */

class _WPDKDBTable {

  /**
   * @brief Table name
   *
   * Get the Table name with WordPress prefix
   *
   * @var string $tableName
   */
  public $tableName;

  /**
   * @brief Statuses
   *
   * Get the List of statuses
   *
   * @var array $arrayStatuses
   */
  public $arrayStatuses;

  /**
   * @brief SDF Statuses
   *
   * Get the list of statuses in SDF format
   *
   * @var array $sdfArrayStatuses
   */
  public $sdfArrayStatuses;

  /**
   * @brief WordPress wpdb
   *
   * An useful pointer to WordPress wpdb class
   *
   * @var wpdb $wpdb
   */
  public $wpdb;

  /**
   * @brief Primary index name
   *
   * Get the index column name. Default 'id'
   *
   * @var string $_indexName
   */
  private $_indexName;

  /**
   * @brief Construct
   *
   * Create an instance of _WPDKDBTable class
   *
   * @param string $tableName  Table name without WordPress prefix
   * @param string $index_name Optional index name, default `id`
   *
   * @return _WPDKDBTable
   */
  function __construct( $tableName, $index_name = 'id' ) {
    global $wpdb;

    $this->wpdb       = $wpdb;
    $this->tableName  = self::tableName( $tableName );
    $this->_indexName = $index_name;

    $this->arrayStatuses();
    $this->sdfArrayStatuses();

  }

  // -----------------------------------------------------------------------------------------------------------------
  // Statuses
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * @brief Statuses
   *
   * Return the statuses array
   *
   * @note To override
   * @return array
   */
  protected function arrayStatuses() {
    /* to override */
    die( 'function WPDKDBTable::arrayStatuses() must be over-ridden in a sub-class.' );
  }

  /**
   * @brief SDF statuses
   *
   * Return statuses in SDF format. This method call `arrayStatuses()`
   *
   * @return array
   */
  protected function sdfArrayStatuses() {
    $result = array();
    if ( !empty( $this->arrayStatuses ) ) {
      $result = array();
      foreach ( $this->arrayStatuses as $key => $status ) {
        $result[$key] = $status['label'];
      }
      /* @todo Questi? */
      unset( $result['all'] );
      unset( $result['trash'] );
    }
    $this->sdfArrayStatuses = $result;
    return $result;
  }

  /**
   * @brief Status group by count
   *
   * Return a key value pairs array with count group by statuses
   *
   * @todo In the original version the string 'status' was in the input of method. However the key of array return
   *       after select have (and had) the 'status' string in clear. Check if this 'status' string can be pass in input
   *       parameters.
   *
   * @return array
   */
  function arrayStatusesGroupBy() {
    $sql      = <<< SQL
SELECT DISTINCT( `status` ),
       COUNT(*) AS count
FROM `{$this->tableName}`
GROUP BY `status`
SQL;
    $result   = $this->wpdb->get_results( $sql, ARRAY_A );
    $statuses = $this->arrayStatuses;
    foreach ( $result as $status ) {
      if ( !empty( $status['status'] ) ) {
        $statuses[$status['status']]['count'] = $status['count'];
      }
    }

    $statuses['all']['count'] = $this->count();

    return $statuses;
  }

  // -----------------------------------------------------------------------------------------------------------------
  // Info
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * @brief Return the right table name with WordPress prefix
   *
   * Utility for compute the right table name with WordPress prefix
   *
   * @param string $tableName Simple table name
   *
   * @return string Complete WordPress table name
   */
  static function tableName( $tableName ) {
    global $wpdb;
    return sprintf( '%s%s', $wpdb->prefix, $tableName );
  }

  /**
   * @brief Return count of record
   *
   * Return the count of record on database
   *
   * @return int
   */
  function count() {
    $sql = <<< SQL
SELECT COUNT(*) AS count
FROM `{$this->tableName}`
SQL;
    return absint( $this->wpdb->get_var( $sql ) );
  }

  // -----------------------------------------------------------------------------------------------------------------
  // CRUD Model - Create, Read, Update and Delete
  // -----------------------------------------------------------------------------------------------------------------

  /**
   * @brief Select rows
   *
   * @param string $where
   *
   * @return array|null Array of object or NULL
   */
  function query( $where = '1' ) {
    $sql = $this->wpdb->prepare( "SELECT * FROM $this->tableName WHERE $where" );
    $res = $this->wpdb->get_results( $sql );
    /* @todo Qui si potrebbe aggiungere un WP_Error in caso di null con l'errore preso da $this->wpdb->last_error */
    return $res;
  }

  /**
   * @brief Update multiple items
   *
   * Update more rows
   *
   * @param array|int $indexes List of index or single index
   * @param array     $fields  Key value pairs array field => value
   *
   * @return bool|int
   */
  function update( $indexes, $fields ) {

    if ( empty( $indexes ) ) {
      return false;
    }

    if ( !is_array( $indexes ) ) {
      $indexes = array( $indexes );
    }

    if ( !is_array( $fields ) ) {
      /* @todo mmm */
    }

    /* Prepare set. */
    $sets = array();
    foreach ( $fields as $field => $value ) {
      if ( is_numeric( $value ) ) {
        $sets[] = sprintf( '%s = %s', $field, $value );
      }
      elseif ( is_string( $value ) ) {
        $sets[] = sprintf( "%s = '%s'", $field, $value );
      }
    }
    $set = join( ', ', $sets );

    /* Prepare where condiction. */
    $where = sprintf( '%s IN( %s )', $this->_indexName, join( ',', $indexes ) );

    $sql    = $this->wpdb->prepare( "UPDATE $this->tableName SET $set WHERE $where" );
    $result = $this->wpdb->query( $sql );

    return $result;
  }

  /**
   * @brief Delete multiple items
   *
   * Delete more rows
   *
   * @param array|int $indexes List of index or single index
   *
   * @return bool|int
   */
  function delete( $indexes ) {

    if ( empty( $indexes ) ) {
      return false;
    }

    if ( !is_array( $indexes ) ) {
      /* @todo mmm */
    }

    /* Prepare where condiction. */
    $where = sprintf( '%s IN( %s )', $this->_indexName, join( ',', $indexes ) );

    $sql    = $this->wpdb->prepare( "DELETE FROM $this->tableName WHERE $where" );
    $result = $this->wpdb->query( $sql );

    return $result;
  }

  /**
   * @brief Create or update database table
   *
   * Create or update database table. This method is usually called on plugin's activation.
   *
   * @param string $sql_filename Complete path of sql file
   * @param string $tableName    Simple table name without WordPress Prefix. The WordPress prefix is auto add by this
   *                             method
   */
  protected static function updateTable( $sql_filename, $tableName ) {
    if ( !function_exists( 'dbDelta' ) ) {
      require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    }
    $content = file_get_contents( $sql_filename );
    $sql     = sprintf( $content, self::tableName( $tableName ) );
    @dbDelta( $sql );
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
