/**
 * WPDK Swipe Control extend.
 *
 * The swipe() method get/set the current state for a swipe control
 *
 * @internal {string} Optional. Set the current state
 *
 * @returns {string} Return the current state
 */
if ( typeof( jQuery.fn.wpdkSwipe ) === 'undefined' ) {

  +function ( $ )
  {
    'use strict';

    $.fn.wpdkSwipe = function ()
    {

      var v = ( arguments.length > 0 ) ? arguments[0] : null;

      if ( $( this ).hasClass( 'wpdk-form-swipe' ) ) {

        if ( null == v ) {
          return $( this ).children( 'input[type=hidden]' ).eq( 0 ).val();
        }

        return this.each( function ()
        {

          // Get main control
          var $control = $( this );

          // Get sub-elements
          var knob = $control.children( 'span' ).eq( 0 );
          var input = $control.children( 'input[type=hidden]' ).eq( 0 );

          if ( false === $control.triggerHandler( WPDKUIComponentEvents.SWIPE_CHANGE, [ $control, wpdk_is_bool( v ) ? 'on' : 'off'] ) ) {
            return input.val();
          }

          // On
          if ( wpdk_is_bool( v ) ) {
            //$control.trigger( 'change', [ $control, 'on'] );
            input.val( 'on' );
            knob.animate( { marginLeft : '23px' }, 100, function ()
            {
              $control.addClass( 'wpdk-form-swipe-on' );
            } );
            $control.trigger( WPDKUIComponentEvents.SWIPE_CHANGED, [ $control, 'on'] );
          }

          // Off
          else {
            //$control.trigger( 'change', [ $control, 'off'] );
            input.val( 'off' );
            knob.animate( { marginLeft : '0' }, 100, function ()
            {
              $control.removeClass( 'wpdk-form-swipe-on' );
            } );
            $control.trigger( WPDKUIComponentEvents.SWIPE_CHANGED, [ $control, 'off'] );
          }
        } );
      }
    };

  }( jQuery );

}

/**
 * WPDK Selectable Checkbox by shift key
 *
 * Usage: $form.find('input[type="checkbox"]').wpdkShiftSelectableCheckbox();
 * replace input[type="checkbox"] with the selector to match your list of checkboxes
 *
 */
if ( typeof jQuery.fn.wpdkShiftSelectableCheckbox === 'undefined' ) {
  +function ( $ )
  {

    'use strict';

    $.fn.wpdkShiftSelectableCheckbox = function ()
    {
      var lastChecked, $boxes = this;

      $boxes.click( function ( evt )
      {
        if ( !lastChecked ) {
          lastChecked = this;
          return;
        }

        if ( evt.shiftKey ) {
          var start = $boxes.index( this ), end = $boxes.index( lastChecked );
          $boxes.slice( Math.min( start, end ), Math.max( start, end ) + 1 )
            .attr( 'checked', lastChecked.checked )
            .trigger( 'change' );
        }

        lastChecked = this;
      } );
    };
  }( jQuery );
}

/**
 * This class init all WPDK controls, attach new event and perform special actions.
 *
 * @class           WPDKControls
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-09-01
 * @version         1.1.2
 * 
 * @history         1.1.2 - Renamed internal _WPDKControls in in _WPDKControls
 *
 */

// One time
if ( typeof( window.WPDKControls ) === 'undefined' ) {

  // On document ready
  jQuery( function ( $ )
  {
    "use strict";

    window.WPDKControls = (function ()
    {

      // This object
      var _WPDKControls = {
        version : '1.1.2',
        init    : _init,

        preferencesForm : _preferencesForm
      };

      /**
       * Init
       *
       * @returns {object}
       * @private
       */
      function _init()
      {
        _initInput();
        _initSwipe();
        _initScrollable();

        // Experimental
        _initAccordion();
        _initGuide();

        return _WPDKControls;
      }

      /**
       * Do a several init for common class:
       *
       *  - Clear a field when an append span elementi with `wpdk-form-clear-left` class is found
       *  - Disable a field after `click` when `wpdk-disable-after-click` class is found
       *  - Locked and unlocked control for `wpdk-form-locked` class
       *
       * @private
       */
      function _initInput()
      {
        // Init with clear
        $( document ).on( 'click', 'span.wpdk-form-clear-left i', false, function ()
        {
          $( this ).prev( 'input' ).val( '' ).trigger( 'change' ).trigger( 'keyup' ).trigger( WPDKUIComponentEvents.CLEAR_INPUT );
        } );

        // Init with disable after click
        $( document ).on( 'click', '.wpdk-disable-after-click', false, function ()
        {
          $( this ).addClass( 'disabled' );
        } );

        // Init locked
        $( document ).on( 'click', '.wpdk-form-locked', false, function ()
        {
          // Check for custom data confirm
          var message = $( this ).data( 'confirm' );

          // TODO Add event

          if ( confirm( empty( message ) ? wpdk_i18n.messageUnLockField : message ) ) {
            $( this )
              .attr( 'class', 'wpdk-form-unlocked' )
              .prev( 'input' )
              .removeAttr( 'readonly' );
          }
        } );

      }

      /**
       * Initialize the WPDK custom Swipe Control.
       * When a Swipe Control is clicked (or swipe) an event `swipe` is trigged:
       *
       *     $('.wpdk-form-swipe').on( WPDKUIComponentEvents.SWIPE, function(a, swipeButton, status ) {});
       *
       * @private
       */
      function _initSwipe()
      {
        // Remove all previous bind event
        $( document ).off( 'click', 'span.wpdk-form-swipe span' );

        // Bind click on right span
        $( document ).on( 'click', 'span.wpdk-form-swipe span', false, function ()
        {
          var control = $( this ).parent();
          var status = wpdk_is_bool( control.wpdkSwipe() );
          var enabled = status ? 'off' : 'on';
          control.trigger( WPDKUIComponentEvents.SWIPE, [ control, enabled ] );
          control.wpdkSwipe( enabled );
        } );

        // Refreshing
        $( document ).on( WPDKUIComponents.REFRESH_SWIPE, _initSwipe );
      }

      /**
       * Initialize the scrollale control
       *
       * @since 1.4.7
       * @since 1.4.7
       * @private
       */
      function _initScrollable()
      {
        var scrollable = $( '.wpdk-form-scrollable' );
        if ( scrollable.length ) {
          $( document ).on( 'click', '.wpdk-form-scrollable img', false, function ()
          {
            $( this ).toggleClass( 'wpdk-selected' );
          } );
        }
      }

      // ---------------------------------------------------------------------------------------------------------------
      // Experimental
      // ---------------------------------------------------------------------------------------------------------------

      /**
       * Initialize the wpdk accordion
       * @todo Experimental
       *
       * @private
       */
      function _initAccordion()
      {
        var accordion = $( 'i.wpdk-openclose-accordion' );
        if ( accordion.length ) {
          /* Memorizzo altezza */
          accordion.parent().next( 'div.wpdk-accordion' ).each( function ( i, e )
          {
            $( this ).addClass( 'wpdk-accordion-open' ).data( 'height', $( this ).height() );
            if ( i > 0 ) {
              $( this ).removeClass( 'wpdk-accordion-open' );
            }
            else {
              $( e ).css( 'height', $( e ).data( 'height' ) + 'px' );
            }
          } );

          accordion.click( function ()
          {
            /* Chiudo tutti gli altri - solo i fieldsset del form parent */
            var form = $( this ).parents( 'form' );
            form.find( 'fieldset' ).removeClass( 'wpdk-accordion-open' );
            form.find( 'fieldset div.wpdk-accordion' ).css( 'height', '0' );

            /* Me stesso lo apro */
            $( this ).parents( 'fieldset' ).addClass( 'wpdk-accordion-open' );

            /* Animation su container */
            var $container = $( this ).parent().next( 'div.wpdk-accordion' );
            $container.css( 'height', $container.data( 'height' ) + 'px' );

          } );
        }
      }

      /**
       * Init the guide engine to open a modal twitter window with an iframe to developer center
       *
       * @since 1.0.0.b4
       * @todo This engine will be move into wpXtreme
       *
       * @private
       */
      function _initGuide()
      {
        $( 'a.wpdk-guide' ).click( function ()
        {
          var title, content, url;

          // Set guide title
          title = $( this ).data( 'title' );

          // If guide content is directly into data-content attribute
          if ( typeof $( this ).data( 'content' ) != 'undefined' && $( this ).data( 'content' ).length > 0 ) {
            content = $( this ).data( 'content' );
          }
          // If guide is in Developer Center
          else {
            url = sprintf( 'https://developer.wpxtre.me/api/v1/articles/%s', $( this ).attr( 'href' ) );
            content = sprintf( '<iframe class="wpdk-iframe-guide" frameborder="0" height="520" width="530" src="%s"></iframe>', url );
          }

          var modal = new WPDKTwitterBootstrapModal( 'wpdk-guide', title, content );
          modal.height = 512;
          modal.display();

          return false;
        } );
      }

      // ---------------------------------------------------------------------------------------------------------------
      // Utility
      // ---------------------------------------------------------------------------------------------------------------

      /**
       * Return the standard form for a preferences view. See wpdk-ui.php for more detail.
       *
       * @since 1.2.0
       *
       * @param {string} id Preferences form ID
       *
       * @return {*|jQuery|HTMLElement}
       */
      function _preferencesForm( id )
      {
        return $( 'form#wpdk_preferences_view_form-' + id );
      }

      return _init();

    })();

  } );
}
