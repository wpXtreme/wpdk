<?php
/// @cond private

/**
 * CRUD (Create, Read, Update & Delete) Model for WordPress
 *
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               27/02/12
 * @version            1.0
 * @deprecated         Since 0.6.2 - Use WPDKDBTable instead
 *
 */

/**
 * Interfaccia per generalizzare una sottoclasse CRUD
 *
 * @interface iWPDKCRUD
 * @deprecated Since 0.6.2 -
 *
 */
interface iWPDKCRUD {
    // -----------------------------------------------------------------------------------------------------------------
    // Static values
    // -----------------------------------------------------------------------------------------------------------------

}

/**
 * Classe da ereditare per accedere alle funzioni primitive relative al database
 *
 * @class              WPDKCRUD
 * @deprecated Since 0.6.2 -
 *
 */
class WPDKCRUD implements iWPDKCRUD {

    // -----------------------------------------------------------------------------------------------------------------
    // CRUD (Create, Read, Update & Delete)
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Create a record on database
     *
     * @since              1.0.0
     *
     *
     * @param array $values Array cone chiave (nome campo) e valore
     *
     * @return mixed
     */
    public static function create( $values ) {
        global $wpdb;

        $result = $wpdb->insert( self::tableName(), $values );
        return $result;
    }

    /**
     * Read a specify record o records from database
     *
     * @since              1.0.0
     *
     *
     * @param int    $id
     *  ID del record o null per tutti
     *
     * @param string $id_id
     *  Nome del campo ID
     *
     * @param string $orderby
     *  Stringa ordinamento SQL, default '', Es. ORDER BY 'name'
     *
     * @param string $where
     *  Stringa WHERE in SQL, Es. AND status = 'trash'
     *
     * @param mixed  $output
     *  Tipo di output
     *
     * @return mixed
     *                     Riga o elenco nel formato $output
     */
    public static function read( $id = null, $id_id = 'id', $orderby = '', $where = '', $output = OBJECT ) {
        global $wpdb;

        $table = self::tableName();

        $where_cond = 'WHERE 1';

        if ( !is_null( $id ) ) {
            if ( is_numeric( $id ) ) {
                $where_cond = sprintf( 'WHERE %s = %s', $id_id, $id );
            } elseif ( is_string( $id ) ) {
                $where_cond = sprintf( 'WHERE %s = \'%s\'', $id_id, $id );
            }
        }

        if ( !empty( $where ) ) {
            $where_cond = sprintf( '%s %s', $where_cond, $where );
        }

        $sql = <<< SQL
        SELECT * FROM `{$table}`
        {$where_cond}
        {$orderby}
SQL;
        if ( !is_null( $id ) ) {
            $result = $wpdb->get_row( $sql, $output );
        } else {
            $result = $wpdb->get_results( $sql, $output );
        }
        return $result;
    }

    /**
     * Update a record on database
     *
     * @since              1.0.0
     *
     *
     * @param int     $id
     * ID del record
     *
     * @param array   $values
     * Array con chiave (nome campo) valore
     *
     * @param string  $id_id
     * Nome del campo ID
     *
     * @param array   $formats
     * Array con i formati da usare: hack per parametri NULL
     *
     * @return mixed
     * Risultato della $wpdb->update()
     */
    public static function update( $id, $values, $id_id = 'id', $formats = array() ) {
        global $wpdb;

        $where = array(
            $id_id => $id
        );

        if ( empty( $formats ) ) {
            $result = $wpdb->update( self::tableName(), $values, $where );
        } else {
            $where_formats = array( '%d' );
            $result        = $wpdb->update( self::tableName(), $values, $where, $formats, $where_formats );
        }
        return $result;
    }

    /**
     * Delete one or more records from database.
     * Elimina anche i relativi post meta
     *
     * @since              1.0.0
     *
     *
     * @param int|array $ids
     * ID o array di ID da eliminare
     *
     * @param string    $id_id
     * Nome del campo ID
     *
     * @param bool      $delete_post_meta
     * Elimina la chiave '_[table name]_status dai post meta. Questa viene usata per memorizzare lo stato precedente
     * di un record, quando si ha una gestine a stati appunto: vedi 'trash' ad esempio. Vedi meotdo update per dettagli
     *
     * @return mixed
     *                     Risultato della $wpdb->query()
     */
    public static function delete( $ids, $id_id = 'id', $delete_post_meta = true ) {
        global $wpdb;

        $table = self::tableName();

        if ( !is_array( $ids ) ) {
            $ids = array( $ids );
        }

        if ( $delete_post_meta ) {
            $meta_key = sprintf( '_%s_status', $table );
            foreach ( $ids as $id ) {
                delete_post_meta( $id, $meta_key );
            }
        }

        $ids = implode( ',', $ids );

        $sql    = <<< SQL
		DELETE FROM `{$table}`
		WHERE {$id_id} IN({$ids})
SQL;
        $result = $wpdb->query( $sql );

        return $result;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Extra
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Restituisce il numero dei record di una tabella
     *
     * @since       1.0.0
     *
     * @return int Restituisce il numero totale dei record
     */
    public static function count() {
        global $wpdb;

        $table = self::tableName();

        $sql = <<< SQL
		SELECT COUNT(*) AS count
		FROM `{$table}`
SQL;
        return absint( $wpdb->get_var( $sql ) );
    }

    // -----------------------------------------------------------------------------------------------------------------
    // Extra for trash like WordPress
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Imposta lo stato di un record a 'trash' e memorizza lo stato attuale nella post meta con chiave
     * '_[table name]_status'
     *
     * @since              1.0.0
     *
     *
     * @param int|array $ids
     * ID o Array di ID del record
     *
     * @param string    $id_id
     * ome del campo ID
     *
     * @param string    $status
     * Nome del campo status, default 'post_status'
     *
     * @param string    $value
     * Valore dello stati, default 'trash'
     *
     * @return mixed
     *                     Risultato della $wpdb->query()
     */
    public static function trash( $ids, $id_id = 'id', $status = 'post_status', $value = 'trash' ) {
        global $wpdb;

        $table    = self::tableName();
        $meta_key = sprintf( '_%s_status', $table );

        if ( !is_array( $ids ) ) {
            $ids = array( $ids );
        }

        /* Memorizzo lo stato precendete nella tabella options */
        foreach ( $ids as $id ) {
            $previous_status = self::status( $id, $id_id, $status );
            update_post_meta( $id, $meta_key, $previous_status );
        }

        $ids = implode( ',', $ids );

        $sql = <<< SQL
        UPDATE `{$table}`
        SET `{$status}` = '{$value}'
        WHERE {$id_id} IN({$ids})
SQL;

        $result = $wpdb->query( $sql );
        return $result;

    }

    /**
     * Repristina un record dal cestino recuperando lo stato precedente dalla chiave ''_[table name]_status'
     * nella post meta. Se non la trova pone il record in status 'unknown'
     *
     * @since              1.0.0
     *
     *
     * @param int|array $ids
     * ID o array di ID del record da repristinare
     *
     * @param string    $id_id
     * Nome del campo ID
     *
     * @param string    $status
     * Nome del campo status
     *
     * @return mixed
     *                     Risultato della $wpdb->query() o false se errore
     */
    public static function untrash( $ids, $id_id = 'id', $status = 'post_status' ) {
        global $wpdb;

        $table    = self::tableName();
        $meta_key = sprintf( '_%s_status', $table );
        $result   = false;

        if ( !is_array( $ids ) ) {
            $ids = array( $ids );
        }

        foreach ( $ids as $id ) {
            $previous_status = get_post_meta( $id, $meta_key, true );
            if ( empty( $previous_status ) ) {
                /* @todo Prendere il primo disponibile in base alla classe ereditaria */
                $previous_status = 'unknown';
            }
            $sql    = <<< SQL
            UPDATE `{$table}`
            SET `{$status}` = '{$previous_status}'
            WHERE {$id_id} = {$id}
SQL;
            $result = $wpdb->query( $sql );

            delete_post_meta( $id, $meta_key );
        }

        return $result;

    }

    /**
     * Legge lo stato attuale di un record
     *
     * @since              1.0.0
     *
     *
     * @param int|array $id
     * ID del record da repristinare
     *
     * @param string    $id_id
     * Nome del campo ID
     *
     * @param string    $status
     * Nome del campo status
     *
     * @return string
     *                     Restituisce la stringa che identifica lo stato, ritorno della $wpdb->get_var(()
     */
    private static function status( $id, $id_id = 'id', $status = 'post_status' ) {
        global $wpdb;

        $table = self::tableName();

        $sql    = <<< SQL
        SELECT `{$status}`
        FROM `{$table}`
        WHERE `{$id_id}` = $id
SQL;
        $status = $wpdb->get_var( $sql );

        return $status;
    }

    // -----------------------------------------------------------------------------------------------------------------
    // WordPress WP List Table
    // -----------------------------------------------------------------------------------------------------------------

    /**
     * Restituisce un array con il tipo di status, la sua label e la count sul database
     *
     * @since              1.0.0
     *
     * @return array
     * Restituisce un array con il tipo di status, la sua label e la count sul database
     */
    public static function statusesWithCount() {
        global $wpdb;

        $statuses = self::arrayStatuses();
        $table    = self::tableName();

        $sql    = <<< SQL
        SELECT DISTINCT(`status`),
               COUNT(*) AS count
        FROM `{$table}` GROUP BY `status`
SQL;
        $result = $wpdb->get_results( $sql, ARRAY_A );

        foreach ( $result as $status ) {
            if ( !empty( $status['status'] ) ) {
                $statuses[$status['status']]['count'] = $status['count'];
            }
        }

        $statuses['all']['count'] = self::count();

        return $statuses;
    }


}

/// @endcond