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
 * @date               2013-09-17
 * @version            0.4.0
 *
 */
class WPDKObject {

  /**
   * Returns the version number assigned to the class.
   * If no version has been set, the default is 0.
   * Version numbers are needed for decoding or unarchiving, so older versions of an object can be detected and decoded
   * correctly.
   *
   * @brief The version number assigned to the class.
   * @since 1.2.0
   *
   * @var string|int $version
   */
   public $version = 0;

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
   * Return TRUE if the instance is an instance of $class.
   * This method is different from `is_a()`:
   *
   *     class a extends WPDKObject {}
   *     class b extends a {}
   *
   *     $b = new b();
   *
   *     echo is_a( $b, 'a' );     // TRUE
   *     echo $b->isClass( 'a' );  // FALSE
   *
   * @brief Brief
   * @since 1.2.0
   *
   * @param string $class Class name
   *
   * @return bool
   */
  public function isClass( $class ) {
    return (  $class == get_class( $this ) );
  }

  /**
   * Returns a Boolean value that indicates whether the receiving class is a subclass of, or identical to, a given class.
   *
   *     class a extends WPDKObject {}
   *     class b extends a {}
   *
   *     $b = new b();
   *
   *     echo $b->isSubclassOfClass( 'a' );           // TRUE
   *     echo $b->isSubclassOfClass( 'WPDKObject' );  // TRUE
   *
   * @brief Return TRUE if the receiving class is a subclass of —or identical to— $class, otherwise FALSE.
   * @since 1.2.0
   *
   * @param object $class A class object
   *
   * @return bool
   */
  public function isSubclassOfClass( $class )
  {
    return is_subclass_of( $this, $class );
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
   * Useful static method to dump any variable with a <pre> HTML tag wrap
   *
   * @brief Dump a variable
   * @since 1.3.0
   *
   * @param mixed $var SOme variable
   *
   */
  public static function dump( $var, $monitor = false )
  {
    ob_start(); ?>
    <pre <?php if( $monitor ) : ?> style="height:100px;overflow-y:scroll;background-color:#222;color:#ffa841" <?php endif; ?>><?php var_dump( $var ) ?></pre><?php
    $content = ob_get_contents();
    ob_end_clean();

    $replaces = array(
      "=>\n" => ' => ',
      '  '   => ' ',
      "\t"   => '',
    );

    $content = strtr( $content, $replaces );
    echo $content;
  }

}

/// @endcond
