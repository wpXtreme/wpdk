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
 * WPDKObject is the root class of most WPDK class hierarchies. Through WPDKObject, objects inherit a basic interface to
 * the runtime system and the ability to behave as WPDK objects.
 *
 * @class              WPDKObject
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date               2013-08-26
 * @version            0.2.0
 *
 */
class WPDKObject {

  /**
   * Get accessor. If you have define a public property as 'mickey', and a method `getMickey` is defined too, when you
   * read the property by using:
   *
   *     echo $object->mickey;
   *
   * This is the same thing as:
   *
   *     echo $object->getMickey();
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
   * Isset accessor. Use if defined a method called `isset[Property]()` when isset() is performed.
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
   * Set accessor. If you have define a public property as 'mickey', and a method `setMickey` is defined too, when you
   * set the property by using:
   *
   *     $object->mickey = $value;
   *
   * This is the same thing as:
   *
   *     $object->setMickey( $value );
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
   * Unset accessor. Use if defined a method called `unset[Property]()` when a property is unsetted.
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
   * This method is called when a clone of this object is perform
   *
   * @brief Clone
   * @since 1.2.0
   *
   */
  public function __clone()
  {
  }

  /**
   * Returns the class name. Returns FALSE if called from outside a class.
   *
   * @brief Returns the class object.
   * @since 1.2.0
   *
   * @return string
   */
  public function className()
  {
    return get_called_class();
  }

  /**
   * Returns the name of the parent class of the class of which object is an instance or the name.
   *
   * @brief Returns the class object for the receiver’s superclass.
   * @since 1.2.0
   *
   * @return string
   */
  public function parentClass()
  {
    return get_parent_class( $this );
  }

  /**
   * Do a merge/combine between two object tree.
   * If the old version not contains an object or property, that is added.
   * If the old version contains an object or property less in last version, that is deleted.
   *
   * @brief Object delta compare for combine
   * @since 1.2.0
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

  /**
   * Create and retrun a singleton instance of this class
   *
   * @brief Init
   *
   * @return WPDKObject
   */
  public static function init()
  {
    static $instance = null;
    if ( is_null( $instance ) ) {
      $instance = new self;
    }
    return $instance;
  }

  // Alias
  public static function getInstance()
  {
    return self::init();
  }


}

/// @endcond
