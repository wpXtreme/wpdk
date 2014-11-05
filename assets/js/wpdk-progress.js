/**
 * wpdkProgress
 *
 * @class           wpdkProgress
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-11-03
 * @version         3.3.0
 * @note            Base on bootstrap v3.3.0
 */

// One time
if( typeof( jQuery.fn.wpdkProgress ) === 'undefined' ) {

  +function( $ )
  {
    'use strict';

    // WPDK change namespace
    $.fn.extend( {

      /**
       * Return the sub bars
       *
       * @param {integer} bar_index Optional. The bar index zero base or nothing to chain the entire list.
       *
       * @returns {*}
       */
      wpdkProgressBars : function()
      {
        var $control = $( this );

        if( arguments.length > 0 ) {
          return $control.find( 'div.wpdk-progress-bar' ).eq( arguments[ 0 ] );
        }
        return $control.find( 'div.wpdk-progress-bar' );
      },

      /**
       * Set property of bar.
       *
       * @param args Optional. Arguments to set.
       *
       * @returns {*|HTMLElement}
       */
      wpdkProgressBar : function( args )
      {
        // Get control
        var $control = $( this );

        // Defaults
        var options = $.extend( {}, {
          percentage         : null,
          label              : $control.data( 'label' ),
          animated           : null,
          striped            : null,
          display_percentage : true
        }, args );

        /**
         * Filter the options before apply.
         *
         * @param {object} options The options object.
         */
        options = wpdk_apply_filters( 'wpdk_ui_progress_options', options );

        // Get useful values
        var min = $control.attr( 'aria-valuemin' );
        var max = $control.attr( 'aria-valuemax' );
        var value_now = $control.attr( 'aria-valuenow' );
        var percentage = value_now;

        // Change percentage ?
        if( options.percentage !== null ) {

          // Sanitize percentage
          percentage = options.percentage < min ? min : ( options.percentage > max ? max : options.percentage );
        }

        /**
         * Filter the percentage before change.
         *
         * @param {integer} percentage The percentage value.
         */
        percentage = wpdk_apply_filters( 'wpdk_ui_progress_percentage', percentage )

        $control.css( { width : percentage + '%' } );
        $control.attr( 'aria-valuenow', percentage );

        if( percentage !== value_now ) {

          /**
           * Fires the when a percentage is changed.
           *
           * @param {*|HTMLElement} control The control.
           * @param {integer} percentage The percentage value.
           */
          wpdk_do_action( 'wpdk_ui_progress_changed', $control, percentage );
        }

        // Display percentage
        if( true === options.display_percentage ) {
          if( options.label == '' ) {
            $control.find( 'span.sr-only' ).html( percentage + '%' );
          }
          else {
            $control.find( 'span.sr-only' ).html( options.label );
          }
        }
        else {
          $control.find( 'span.sr-only' ).html( '' );
        }

        // Animated
        if( options.animated !== null ) {
          $control.toggleClass( 'active', options.animated );
        }

        // Striped
        if( options.striped !== null ) {
          $control.toggleClass( 'wpdk-progress-bar-striped', options.striped );
        }

        return $control;

      }

    } );

  }( jQuery );

}
