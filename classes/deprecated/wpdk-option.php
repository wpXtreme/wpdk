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
 * The WPDKOption class extends the default basic handling of a WordPress option stored into DB
 *
 * @copyright          Copyright (c) wpXtreme, Inc
 * @author             yuma - <info@wpxtre.me>
 * @date               2012-11-28
 * @version            0.1.0
 *
 */
class WPDKOption {

  //-------------------------------------------------------------------------------------------
  // Internal properties
  //-------------------------------------------------------------------------------------------

  /**
   * The option key to be used in WordPress DB interface.
   *
   * @brief The option key
   *
   * @var string $optionKey
   *
   * @since 0.0.1
   */
  public $optionKey;

  /**
   * The option value related to the option key. This private member contains always the result of the last operation
   * performed on option key.
   *
   * @brief The option value
   *
   * @var mixed $_optionValue
   *
   * @since 0.0.1
   */
  private $_optionValue;



  //-------------------------------------------------------------------------------------------
  // Internal constants
  //-------------------------------------------------------------------------------------------

  /**
   * int - Used to set option value of an option key with simple merging of internal values.
   *
   * @brief Set option value with no update of structure
   *
   * @since 0.0.1
   */
  const MERGE_VALUES =  1;

  /**
   * int - Used to set option value of an option key with merging of internal values and updating of structure
   *
   * @brief Set option value with the update of structure
   *
   * @since 0.0.1
   */
  const ALIGN_STRUCTURE =  2;



  //-------------------------------------------------------------------------------------------
  // Methods
  //-------------------------------------------------------------------------------------------

  /**
   * The constructor of the class. The behaviour of this constructor is related to its input parameters. It can get the
   * option value related to the option key in input. Or it can directly set the option key to a specific option value
   * according to desired algorithm. Or else, it can create an empty object, with subsequent invocation of methods for
   * getting or setting specific option keys.
   *
   * @brief The class constructor
   *
   * @since 0.0.1
   *
   * @param string $sOptionKey The option key to be used in WordPress DB interface.
   * @param mixed  $mOptionValue (optional) The option value to set for the option key. Default to NULL, i.e. get the
   * option value only, if exists.
   * @param int    $iTypeOfSet (optional) The updating algorithm to be used. Default to $this->MERGE, that means merge all
   * option values without any structure check. If set to $this->DELTA, also option value structure is checked and eventually
   * modifies. See documentation about setOption method for details.
   *
   */
  function __construct( $sOptionKey, $mOptionValue = NULL, $iTypeOfSet = self::MERGE_VALUES ) {

    $this->optionKey     = sanitize_key( $sOptionKey );
    $this->_optionValue  = NULL;

    // If option key exists, try to get its option value
    if( ! empty( $this->optionKey )) {
      $this->_optionValue = $this->getOption();
    }

    // Set new option value, if requested in instance creation
    if( ! is_null( $mOptionValue )) {
      $this->_optionValue = $this->_internalSetOption( $mOptionValue, $iTypeOfSet );
    }

  }


    /**
     * Align structure between DataBase and actual real instance. After this algorithm, the resultant option value is
     * written into DB. Aligning structure does not touch values, because DB stores old values, and it must be preserved.
     *
     * @brief Align structure between DB and NEW: historically called delta.
     *
     * @since 0.0.1
     *
     * @param mixed  $mNewOptionValue The option value to set for the option key.
     *
     * @return mixed|false The new option value if the option has been correctly set, FALSE otherwise
     *
     */
  function alignStructureWith( $mNewOptionValue ) {

    // Call internal set method with proper params
    return $this->_internalSetOption( $mNewOptionValue, self::ALIGN_STRUCTURE );

  }


    /**
     * Merge values between DataBase and actual real instance. After this algorithm, the resultant option value is
     * written into DB. Merging does not touch structure: if necessary, call alignStructure before.
     *
     * @brief Merge values between DB and NEW.
     *
     * @since 0.0.1
     *
     * @param mixed  $mNewOptionValue The option value to set for the option key.
     *
     * @return mixed|false The new option value if the option has been correctly set, FALSE otherwise
     *
     */
  function setOption( $mNewOptionValue ) {

    // Call internal set method with proper params
    return $this->_internalSetOption( $mNewOptionValue, self::MERGE_VALUES );

  }



/**
   * Set or update the option value of an option key, according to the algorithm described into _mergeValues method.
   * After this algorithm, the resultant option value is written into DB.
   *
   * @brief Set option value of an option key
   *
   * @since 0.0.1
   *
   * @param mixed  $mNewOptionValue The option value to set for the option key.
   * @param int    $iTypeOfSet The updating algorithm to be used. If set to $this::MERGE_VALUES, merge all option
   * values without any structure check. If set to $this::ALIGN_STRUCTURE, structure of both DB and NEW instance are aligned
   * without setting any value. See method description for details.
   *
   * @return mixed - the new option value if the option has been correctly set, FALSE otherwise
   *
   */
  private function _internalSetOption( $mNewOptionValue, $iTypeOfSet ) {

    // Check option key syntax
    if( FALSE == $this->_checkOptionKey()) {
      return FALSE;
    }

    // Try to get the option value from DB, if exists
    $mDBOptionValue = ( is_null( $this->_optionValue ))?$this->getOption( $this->optionKey ):$this->_optionValue;

    // If root type of DB and NEW are BOTH array or BOTH object, perform a merging
    if( ( is_array( $mDBOptionValue ) && is_array( $mNewOptionValue )) ||
        ( is_object( $mDBOptionValue ) && is_object( $mNewOptionValue ))
      ) {
      $this->_optionValue = $this->_mergeValues( $mDBOptionValue, $mNewOptionValue, $iTypeOfSet );
    }
    // If root type of DB and NEW are the same, but neither array nor object, or if root types of DB and NEW are different,
    // perform a brutal overwrite of DBV with NEWV, but only if MERGE_VALUES.
    else {
      if( self::MERGE_VALUES == $iTypeOfSet ) {
        $this->_optionValue = $mNewOptionValue;
      }
    }

    // Update or set option into DB
    // WARNING: update_options return FALSE even if NEW=DB, but this is not a real FALSE situation
    $bStatus = update_option( $this->optionKey, $this->_optionValue );
    if( FALSE == $bStatus) {
      $mFoo = get_option( $this->optionKey );
      if( $mFoo != $this->_optionValue ) {
        return FALSE;
      }
    }

    return $this->_optionValue;

  }



  /**
   * Get the option value related to the option key stored into the class instance.
   *
   * @brief Get option value
   *
   * @since 0.0.1
   *
   * @return mixed - The option value, or NULL if the option is not existent at all. Can't return FALSE on option
   * nonexistence, because the option value itself can be FALSE.
   *
   */
  function getOption()  {

    // Check option key syntax
    if( FALSE == $this->_checkOptionKey()) {
      return NULL;
    }

    // Get, or retry to get, if not already available
    if( NULL == $this->_optionValue ) {
      $this->_optionValue = get_option( $this->optionKey, NULL );
    }

    return $this->_optionValue;

  }



  /**
   * Delete physically from DB the option key stored into the class instance.
   *
   * @brief Delete option key and related value from DB
   *
   * @since 0.0.1
   *
   * @return bool - TRUE if the option has been correctly deleted, FALSE otherwise
   *
   */
  function deleteOption() {

    // Check option key syntax
    if( FALSE == $this->_checkOptionKey()) {
      return FALSE;
    }

    $bStatus = FALSE;

    // Don't delete a key that does not exist
    if( isset( $this->_optionValue )) {
      $bStatus = delete_option( $this->optionKey );
      $this->_optionValue = NULL;   // delete cache
    }

    return $bStatus;

  }



  /**
   * Perform a merge of option values taken from DB and passed from input, and return the result. Both values are arrays OR
   * objects, i.e., both values to merge are of the same type.
   *
   * The algorithm used to merge value is as follow:
   *
   * DBV is the value now stored into DB for the option key.
   * NEWV is the new value to set for the option key.
   * - If I choose MERGE_VALUES algorithm:
   * -- Keys that are in DBV and are in NEWV becomes equals to related NEWV values
   * - If I choose ALIGN_STRUCTURE algorithm:
   * -- Keys that aren't in DBV but are in NEWV are added with related values
   * -- Keys that are in DBV but aren't in NEWV are DELETED.
   *
   * @brief Merge option values, if both arrays OR objects
   *
   * @since 1.0.0
   *
   * @param mixed  $mDBOptionValue The option value for the option key now stored into DB
   * @param mixed  $mNewOptionValue The new option value to set for the option key.
   * @param int    $iTypeOfSet The updating algorithm to be used. If set to $this::MERGE_VALUES, merge all option
   * values without any structure check. If set to $this::ALIGN_STRUCTURE, structure of both DB and NEW instance are aligned
   * without setting any value. See method description for details. See method description for details.
   *
   * @return bool - TRUE if the option has been correctly set, FALSE otherwise
   *
   */
    private function _mergeValues( $mDBOptionValue, $mNewOptionValue, $iTypeOfSet ) {

        // The result, initially equal to DB
        $mResult = $mDBOptionValue;

        // First of all, update same keys and add new keys if necessary
        foreach ( $mNewOptionValue as $sKey => $mValue ) {
            $mDBValue = false;
            if ( is_array( $mNewOptionValue ) ) {
                $mDBValue = ( isset( $mDBOptionValue[$sKey] ) ) ? $mDBOptionValue[$sKey] : NULL;
            }
            elseif ( is_object( $mNewOptionValue ) ) {
                $mDBValue = ( isset( $mDBOptionValue->$sKey ) ) ? $mDBOptionValue->$sKey : NULL;
            }

            // if BOTH DB and NEW type IS array or object, engage recursion
            if ( ( is_array( $mDBValue ) && is_array( $mValue ) ) || ( is_object( $mDBValue ) && is_object( $mValue ) )
            ) {

                if ( is_array( $mNewOptionValue ) ) {
                    $mResult[$sKey] = $this->_mergeValues( $mDBOptionValue[$sKey], $mValue, $iTypeOfSet );
                }
                elseif ( is_object( $mNewOptionValue ) ) {
                    $mResult->$sKey = $this->_mergeValues( $mDBOptionValue->$sKey, $mValue, $iTypeOfSet );
                }

            }
            // DB and NEW are different in type, or NEW has a new key that DB doesn't have:
            // Update value happens ONLY in MERGE_VALUES mode
            // Add new key->value pairs happens ONLY in ALIGN_STRUCTURE mode
            else {

                if ( is_array( $mNewOptionValue ) ) {
                    if ( self::ALIGN_STRUCTURE == $iTypeOfSet ) {
                        if ( !isset( $mDBOptionValue[$sKey] ) ) {
                            $mResult[$sKey] = ''; // just to align structure, not values!
                        }
                    }
                    elseif ( self::MERGE_VALUES == $iTypeOfSet ) {
                        $mResult[$sKey] = $mValue;
                    }
                }
                elseif ( is_object( $mNewOptionValue ) ) {
                    if ( self::ALIGN_STRUCTURE == $iTypeOfSet ) {
                        if ( !isset( $mDBOptionValue->$sKey ) ) {
                            $mResult->$sKey = ''; // just to align structure, not values!
                        }
                    }
                    elseif ( self::MERGE_VALUES == $iTypeOfSet ) {
                        $mResult->$sKey = $mValue;
                    }
                }

            }

        }

        // Now, if I'm in ALIGN_STRUCTURE mode, delete DB keys or properties that aren't in NEW
        if ( $iTypeOfSet == self::ALIGN_STRUCTURE ) {
            foreach ( $mDBOptionValue as $sKey => $mValue ) {
                if ( is_array( $mDBOptionValue ) ) {
                    if ( FALSE == array_key_exists( $sKey, $mNewOptionValue ) ) {
                        unset( $mResult[$sKey] );
                    }
                }
                elseif ( is_object( $mDBOptionValue ) ) {
                    $sClassName = get_class( $mNewOptionValue );
                    if ( FALSE == property_exists( $sClassName, $sKey ) ) {
                        unset( $mResult->$sKey );
                    }
                }
            }
        }

        return $mResult;

    }


    /**
   * Private checking of option key
   *
   * @brief Check option key syntax
   *
   * @since 1.0.0
   *
   * @return bool - TRUE if the option key is valid, FALSE otherwise
   *
   */
  private function _checkOptionKey() {

    // Option must exist, and must be a string
    if( empty( $this->optionKey ) ||
        ! is_string( $this->optionKey )
      ) {
      return FALSE;
    }

    return TRUE;

  }

}

/// @endcond