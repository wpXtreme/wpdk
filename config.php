<?php
/**
 * Configuration file
 *
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
 * @date            2013-02-06
 * @version         0.8.2
 *
 * @note            This file will be overwrite in future update. Please edit with care and only for debug.
 *
 */

// -----------------------------------------------------------------------------------------------------------------
// Debug with watch dog
// -----------------------------------------------------------------------------------------------------------------

/* Debug log file */
define( 'WPDK_LOG_FILE', trailingslashit( dirname( __FILE__ ) ) . 'log.php' );

define( 'WPDK_WATCHDOG_DEBUG', true );
define( 'WPDK_WATCHDOG_DEBUG_ON_FILE', true );
define( 'WPDK_WATCHDOG_DEBUG_ON_DATABASE', true );
define( 'WPDK_WATCHDOG_DEBUG_ON_TRIGGER_ERROR', false );

// -----------------------------------------------------------------------------------------------------------------
// Transient Cache
// -----------------------------------------------------------------------------------------------------------------
define( 'WPDK_CACHE_POST', true );
define( 'WPDK_CACHE_RECORD', true );

