<?php
/**
 * Filesystem helper.
 *
 * @class              WPDKFilesystem
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-04-02
 * @version            1.1.0
 */

class WPDKFilesystem {

  /**
   * Return the file size well formatted.
   *
   * @brief Get the file size
   *
   * @param string $filename      File name or path to a file
   * @param int    $precision     Digits to display after decimal
   *
   * @return string|bool Size (B, KiB, MiB, GiB, TiB, PiB, EiB, ZiB, YiB) or boolean
   */
  public static function fileSize( $filename, $precision = 2 ) {
    static $units = array(
      'Bytes',
      'KB',
      'MB',
      'G',
      'T',
      'P',
      'E',
      'Z',
      'Y'
    );

    if ( is_file( $filename ) ) {
      if ( !realpath( $filename ) ) {
        $filename = $_SERVER['DOCUMENT_ROOT'] . $filename;
      }
      $bytes = filesize( $filename );

      // hardcoded maximum number of units @ 9 for minor speed increase
      $e = floor( log( $bytes ) / log( 1024 ) );
      return sprintf( '%.' . $precision . 'f ' . $units[$e], ( $bytes / pow( 1024, floor( $e ) ) ) );
    }
    return false;
  }

  /**
   * Return an array with all matched files from root folder. This method release the follow filters:
   *
   * * wpdk_rglob_find_dir( true, $file ) - when find a dir
   * * wpdk_rglob_find_file( true, $file ) - when find a a file
   * * wpdk_rglob_matched( $regexp_result, $file, $match ) - after preg_match() done
   *
   * @brief get all matched files
   * @since 1.0.0.b4
   *
   * @param string $path    Folder root
   * @param string $match   Optional. Regex to apply on file name. For example use '/^.*\.(php)$/i' to get only php file.
   *                        Defaul is empty
   *
   * @return array
   */
  public static function recursiveScan( $path, $match = '' ) {

    /**
     * Return an array with all matched files from root folder.
     *
     * @brief get all matched files
     * @note Internal recursive use only
     *
     * @param string $path    Folder root
     * @param string $match   Optional. Regex to apply on file name. For example use '/^.*\.(php)$/i' to get only php file
     * @param array  $result  Optional. Result array. Empty form first call
     *
     * @return array
     */
    function _rglob( $path, $match = '', &$result = array() ) {
      $files = glob( trailingslashit( $path ) . '*', GLOB_MARK );
      if ( false !== $files ) {
        foreach ( $files as $file ) {
          if ( is_dir( $file ) ) {
            $continue = apply_filters( 'wpdk_rglob_find_dir', true, $file );
            if ( $continue ) {
              _rglob( $file, $match, $result );
            }
          }
          elseif ( !empty( $match ) ) {
            $continue = apply_filters( 'wpdk_rglob_find_file', true, $file );
            if ( false == $continue ) {
              break;
            }
            $regexp_result = array();
            $error = preg_match( $match, $file, $regexp_result );
            if ( 0 !== $error || false !== $error ) {
              $regexp_result = apply_filters( 'wpdk_rglob_matched', $regexp_result, $file, $match );
              if ( !empty( $regexp_result ) ) {
                $result[] = $regexp_result[0];
              }
            }
          }
          else {
            $result[] = $file;
          }
        }
        return $result;
      }
    }

    $result = array();

    return _rglob( $path, $match, $result );
  }

  /**
   * Return the extension of a filename
   *
   * @brief Extension
   *
   * @param string $file A filename
   *
   * @return string
   */
  public static function ext( $file ) {
    return end( explode( '.', strtolower( basename( $file ) ) ) );
  }

}