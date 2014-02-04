<?php

/**
 * Useful class to manage periodic inteval
 *
 * @class           WPDKCronSchedules
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-02-04
 * @version         1.0.0
 * @since 1.4.20
 *
 */
class WPDKCronSchedules {

  const HALF_HOUR   = 'wpdk_half_hour';
  const TWO_MINUTES = 'wpdk_two_minutes';

  /**
   * Return a singleton instance of WPDKCronSchedules class
   *
   * @brief Singleton
   *
   * @return WPDKCronSchedules
   */
  public static function init()
  {
    static $instance = null;
    if ( is_null( $instance ) ) {
      $instance = new WPDKCronSchedules();
    }
    return $instance;
  }

  /**
   * Create an instance of WPDKCronSchedules class
   *
   * @brief Construct
   *
   * @return WPDKCronSchedules
   */
  public function __construct()
  {
    // Custom periodic interval
    add_filter( 'cron_schedules', array( $this, 'cron_schedules' ) );
  }

  /**
   * Set WPDK custom periodic interval
   *
   * @brief Custom periodic interval
   *
   * @param array $schedules
   *
   * @return array
   */
  public function cron_schedules( $schedules )
  {
    $new_schedules = array(
      self::HALF_HOUR   => array(
        'interval' => HOUR_IN_SECONDS / 2,
        'display'  => __( 'Half hour', WPDK_TEXTDOMAIN )
      ),
      self::TWO_MINUTES => array(
        'interval' => MINUTE_IN_SECONDS * 2,
        'display'  => __( 'Two minutes', WPDK_TEXTDOMAIN )
      ),
    );

    return array_merge( $schedules, $new_schedules );
  }

}


/**
 * WordPress Cron Controller (model)
 * @class           WPDKCronController
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-02-04
 * @version         1.0.0
 * @since           1.4.20
 *
 */
class WPDKCronController {

  /**
   * List of registered cron
   *
   * @brief Cron jobs
   *
   * @var array $cron
   */
  public $cron = array();

  /**
   * Create an instance of WPDKCronController class
   *
   * @brief Construct
   *
   * @return WPDKCronController
   */
  public function __construct()
  {
    $this->cron = _get_cron_array();
  }

  /**
   * Remove/Unschedule a cron job
   *
   * @brief Remove
   *
   * @param string $name Unique cron name
   */
  public static function remove( $name )
  {
    $name = sanitize_title( $name );

    // Get time of next scheduled run
    $timestamp = wp_next_scheduled( $name );

    // Unschedule custom action hook
    wp_unschedule_event( $timestamp, $name );
  }

  /**
   * Unschedule all cron jobs attached to a specific hook.
   *
   * @brief Remove all
   *
   * @param string $name Unique cron name
   */
  public static function removeAll( $name )
  {
    $name = sanitize_title( $name );
    wp_clear_scheduled_hook( $name );
  }

}


/**
 * Abastract common layer to schedule an event
 *
 * @class           WPDKCron
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-02-04
 * @version         1.0.0
 * @since           1.4.20
 *
 */
class WPDKCron {

  public $name = '';
  public $timestamp = 0;
  public $recurrence = 0;

  /**
   * Create an instance of WPDKRecurringCron class
   *
   * @brief Construct
   *
   * @param string $name       Unique cron name
   * @param string $timestap   Optional. Timestamp for when to run the event.
   * @param string $recurrence Optional. How often the event should recur.
   *
   * @return WPDKRecurringCron
   */
  public function __construct( $name, $timestap = false, $recurrence = 0 )
  {
    $this->name       = sanitize_title( $name );
    $this->timestamp  = empty( $timestap ) ? time() : $timestap;
    $this->recurrence = $recurrence;

    // Add action
    add_action( $this->name, array( $this, 'cron' ) );

    // If this cron jobs is not scheduled then add to the WP list
    if ( !wp_next_scheduled( $this->name ) ) {

      // Recurring
      if ( empty( $timestap ) && !empty( $recurrence ) ) {
        wp_schedule_event( time(), $recurrence, $this->name );
      }
      // Single event
      elseif ( !empty( $timestap ) ) {
        wp_schedule_single_event( $timestap, $this->name );
      }
    }
  }

  /**
   * Override with your schedule cron process
   *
   * @brief Cron
   *
   */
  public function cron( )
  {
    // Override
  }

  /**
   * Remove/Unschedule a cron job
   *
   * @brief Remove
   *
   * @param string $name Unique cron name
   */
  public function remove()
  {
    WPDKCronController::remove( $this->name );
  }

}


/**
 * Easy way to recurring event
 *
 * @class           WPDKRecurringCron
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-02-04
 * @version         1.0.0
 * @since           1.4.20
 *
 */
class WPDKRecurringCron extends WPDKCron {

  /**
   * Create an instance of WPDKRecurringCron class
   *
   * @brief Construct
   *
   * @param string $name       Unique cron name
   * @param string $recurrence Optional. How often the event should recur.
   *
   * @return WPDKRecurringCron
   */
  public function __construct( $name, $recurrence = 0 )
  {
    parent::__construct( $name, false, $recurrence );
  }
}

/**
 * Easy way to single event
 *
 * @class           WPDKSingleCron
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-02-04
 * @version         1.0.0
 * @since           1.4.20
 *
 */
class WPDKSingleCron extends WPDKCron {

  /**
   * Create an instance of WPDKSingleCron class
   *
   * @brief Construct
   *
   * @param string $name       Unique cron id used as hook
   * @param string $timestap Timestamp for when to run the event.
   *
   * @return WPDKSingleCron
   */
  public function __construct( $name, $timestap )
  {
    parent::__construct( $name, $timestap );
  }

}