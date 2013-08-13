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
 * ## Overview
 *
 * This abstract class - when inerith - add a more efficent management of properties. Thanks to `__get()` and `__set()`
 * magic method you can set a public property and use `get` and `set` prefix to set itself, like Objective-C.
 *
 * @class              WPDKObject
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-08-13
 * @version            0.2.0
 *
 */
class WPDKObject {

  /**
   * Get accessor
   *
   * @brief Get accessor
   *
   * @param string $name Property name
   *
   * @return mixed
   */
  public function __get( $name )
  {
    //NSLog("%s::%s - %s", __CLASS__, __FUNCTION__, $name);

    if ( method_exists( $this, ( $method = 'get' . ucfirst( $name ) ) ) ) {
      return $this->$method();
    }
    else {
      return $this->$name;
    }
  }

  /**
   * Isset accessor
   *
   * @brief Isset accessor
   *
   * @param string $name Property name
   *
   * @return mixed
   */
  public function __isset( $name )
  {
    if ( method_exists( $this, ( $method = 'isset' . ucfirst( $name ) ) ) ) {
      return $this->$method();
    }
    else {
      return;
    }
  }

  /**
   * Set accessor
   *
   * @brief Set accessor
   *
   * @param string $name  Property name
   * @param mixed  $value Some value
   */
  public function __set( $name, $value )
  {
    if ( method_exists( $this, ( $method = 'set' . ucfirst( $name ) ) ) ) {
      $this->$method( $value );
    }
  }

  /**
   * Unset accessor
   *
   * @brief Unset accessor
   *
   * @param string $name Property name
   */
  public function __unset( $name )
  {
    if ( method_exists( $this, ( $method = 'unset' . ucfirst( $name ) ) ) ) {
      $this->$method();
    }
  }

  /**
   * Do a merge/combine between two object tree.
   * If the old version not contains an object or property, that is added.
   * If the old version contains an object or property less in last version, that is deleted.
   *
   * @brief Object delta compare for combine
   * @since 1.1.3
   *
   * @param mixed $last_version Object tree with new or delete object/value
   * @param mixed $old_version  Current Object tree, loaded from serialize or database for example
   *
   * @return Object the delta Object tree
   */
  public static function delta( $last_version, $old_version )
  {
    $last_version_stack = array();
    $old_version_stack  = array();

    /* Creo un elenco di tutte le proprietà della classe $old_version. Questo elenco mi indica il nome della
     proprietà e il tipo.
    */
    foreach ( $old_version as $key => $value ) {
      $old_version_stack[$key] = $value;
    }

    /* Ora ciclo nella versione recente */
    foreach ( $last_version as $key => $value ) {
      /* Se la precedente versione non contiene la proprietà di quella nuova, la imposto con il valore di default. */
      if ( !isset( $old_version_stack[$key] ) ) {
        $old_version->$key = $value;
      }

      elseif ( empty( $old_version_stack[$key] ) || is_null( $old_version_stack[$key] ) ) {
        $old_version->$key = $value;
      }

      /* La proprietà esiste. */
      else {
        /* Se la proprietà c'è potrebbe essere a sua volta un oggeto, quindi controllo ed eventualmente ciclo su
         questo.
        */
        if ( is_object( $value ) ) {
          self::delta( $value, $old_version->$key );
        }
      }
    }

    /* Precedentemente abbiamo controllato per 'mancanze' nella vecchia classe, ora facciamo un controllo speculare
    cioè verifichiamo che la nuova struttura non abbia eliminato qualcosa.
    Come nel caso precedente creo un elenco delle proprietà dell'ultima versione.
    */
    foreach ( $last_version as $key => $value ) {
      $last_version_stack[$key] = $value;
    }

    /* Ora ciclo nella vecchia versione */
    foreach ( $old_version as $key => $value ) {
      /* Se non esiste più questa proprietà... */
      if ( !isset( $last_version_stack[$key] ) ) {
        /* La elimino */
        unset( $old_version->$key );
      }
    }
    /* Ok, $old_version ora è allineata. */
    return $old_version;
  }

}

/// @endcond
