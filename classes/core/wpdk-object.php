<?php
/**
 * ## Overview
 *
 * This abstract class - when inerith - add a more efficent management of properties.
 * WPDKObject is the root class of most WPDK class hierarchies. Through WPDKObject, objects inherit a basic interface to
 * the runtime system and the ability to behave as WPDK objects.
 *
 * @class              WPDKObject
 * @author             =undo= <info@wpxtre.me>
 * @copyright          Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date               2014-06-10
 * @version            0.5.1
 *
 */
class WPDKObject {

  /**
   * Returns the version number assigned to the class.
   * If no version has been set, the default is '1.0.0'.
   * Version numbers are needed for decoding or unarchiving, so older versions of an object can be detected and decoded
   * correctly.
   *
   * @brief The version number assigned to the class.
   * @since 1.2.0
   *
   * @var string $__version
   */
   public $__version = '1.0.0';

  /**
   * Returns the class name. Returns FALSE if called from outside a class.
   *
   * @brief Returns the class object.
   * @since 1.2.0
   *
   * @return string
   */
  public function __className()
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
  public function __parentClass()
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
   *     echo $b->__isClass( 'a' );  // FALSE
   *
   * @brief Brief
   * @since 1.2.0
   *
   * @param string $class Class name
   *
   * @return bool
   */
  public function __isClass( $class )
  {
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
   *     echo $b->__isSubclassOfClass( 'a' );           // TRUE
   *     echo $b->__isSubclassOfClass( 'WPDKObject' );  // TRUE
   *
   * @brief Return TRUE if the receiving class is a subclass of —or identical to— $class, otherwise FALSE.
   * @since 1.2.0
   *
   * @param object $class A class object
   *
   * @return bool
   */
  public function __isSubclassOfClass( $class )
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
  public static function __delta( $last_version, $old_version )
  {
    $last_version_stack = array();
    $old_version_stack  = array();

    // Create a list of all properties of old_version class.
    foreach ( $old_version as $key => $value ) {
      $old_version_stack[$key] = $value;
    }

    // Loop in the recent version
    foreach ( $last_version as $key => $value ) {

      // If the old_version has not this new property then set it to default value
      if ( !isset( $old_version_stack[$key] ) ) {
        $old_version->$key = $value;
      }

      elseif ( empty( $old_version_stack[$key] ) || is_null( $old_version_stack[$key] ) ) {
        $old_version->$key = $value;
      }

      // Property exist
      else {

        // The proprty could be an object, then loop in
        if ( is_object( $value ) ) {
          // @since 1.5.18 - fix `__PHP_Incomplete_Class`
          if ( class_exists( get_class( $value ) ) ) {
            self::__delta( $value, $old_version->$key );
          }
          else {
            unset( $old_version->$key );
          }
        }
      }
    }

    // Loop for deleted propertis
    foreach ( $last_version as $key => $value ) {
      $last_version_stack[$key] = $value;
    }

    // Loop in the old version
    foreach ( $old_version as $key => $value ) {

      // If the property is no longer implement
      if ( !isset( $last_version_stack[$key] ) ) {

        // Delete property
        if( isset( $old_version->$key ) ) {
          unset( $old_version->$key );
        }
      }
    }

    // Game over
    return $old_version;
  }

  /**
   * @deprecated since 1.4.8 - use __delta() instead
   */
  public static function delta( $last_version, $old_version )
  {
    _deprecated_function( __CLASS__ . '::' . __FUNCTION__, '1.4.8', '__delta()' );
    return self::__delta( $last_version, $old_version );
  }

  /**
   * Useful static method to dump any variable with a `<pre>` HTML tag wrap
   *
   * @brief Dump a variable
   * @since 1.3.0
   *
   * @param mixed $var     Some variable
   * @param bool  $monitor Optional. If true a old style monitor layout is displayed
   */
  public static function __dump( $var, $monitor = false )
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