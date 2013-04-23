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



/// @endcond
