/**
 * WPDK Swipe Control extend.
 *
 * The swipe() method get/set the current state for a swipe control
 *
 * @internal {string} Optional. Set the current state
 *
 * @deprecated since 1.9.0
 *
 * @returns {string} Return the current state
 */
if( typeof( jQuery.fn.wpdkSwipe ) === 'undefined' ) {

  +function( $ )
  {
    'use strict';

    // @deprecated since 1.9.0 - Use Switch UI Control instead
    $.fn.wpdkSwipe = function()
    {

      var v = ( arguments.length > 0 ) ? arguments[ 0 ] : null;

      if( $( this ).hasClass( 'wpdk-form-swipe' ) ) {

        if( null == v ) {
          return $( this ).children( 'input[type="hidden"]' ).eq( 0 ).val();
        }

        return this.each( function()
        {

          // Get main control
          var $control = $( this );

          // Get sub-elements
          var $knob = $control.children( 'span' ).eq( 0 );
          var $input = $control.children( 'input[type="hidden"]' ).eq( 0 );
          var status = wpdk_is_bool( v ) ? 'on' : 'off';

          /**
           * Fires before change the swipe.
           *
           * Return FALSE to stop the change, otherwise return TRUE to continue.
           *
           * @param {object} $control The swipe control.
           * @param {string} status The swipe status 'on' or 'off'.
           *
           * @return {boolean}
           */
          var result = $control.triggerHandler( WPDKUIComponentEvents.SWIPE_CHANGE, [ $control, status ] );

          if( false === result ) {
            return $input.val();
          }

          // On
          if( wpdk_is_bool( v ) ) {
            $input.val( 'on' );
            $knob.animate( { marginLeft : '23px' }, 100, function()
            {
              $control.addClass( 'wpdk-form-swipe-on' );
            } );

            /**
             * Fires when a swipe is turn on.
             *
             * @param {object} $control The swipe control.
             * @param {string} status The swipe status 'on'.
             */
            $control.trigger( WPDKUIComponentEvents.SWIPE_CHANGED, [ $control, 'on' ] );
          }

          // Off
          else {
            $input.val( 'off' );
            $knob.animate( { marginLeft : '0' }, 100, function()
            {
              $control.removeClass( 'wpdk-form-swipe-on' );
            } );

            /**
             * Fires when a swipe is turn off.
             *
             * @param {object} $control The swipe control.
             * @param {string} status The swipe status 'off'.
             */
            $control.trigger( WPDKUIComponentEvents.SWIPE_CHANGED, [ $control, 'off' ] );
          }

          // @since 1.8.0 - Fires for "customize.php"
          $input.trigger( 'change' );

        } );
      }
    };

  }( jQuery );

}

/**
 * WPDK Switch Control extend.
 */
if( typeof( window.wpdkSwitches ) === 'undefined' ) {

  +function( $ )
  {
    'use strict';

    /**
     * Return all switches on document.
     *
     * @returns {*|HTMLElement}
     */
    window.wpdkSwitches = function()
    {
      return $( '.wpdk-ui-switch input[type="checkbox"]' );
    }

    /**
     * Extends jQuery in order to get all swicthes.
     *
     * @returns {*}
     */
    $.fn.wpdkSwitches = function()
    {
      return $( this ).find( '.wpdk-ui-switch input[type="checkbox"]' );
    };

  }( jQuery );
}

/**
 * WPDK Switch Control extend.
 */
if( typeof( jQuery.fn.wpdkSwitch ) === 'undefined' ) {

  +function( $ )
  {
    'use strict';

    /**
     * WPDK Switch Control extend.
     *
     * @param {string} method Current method.
     * @param {string} oprions Options method.
     *
     * @returns {string} Return the current state
     */
    $.fn.wpdkSwitch = function()
    {

      // Get method
      var method = ( arguments.length > 0 ) ? arguments[ 0 ] : null;

      // Get options
      var options = ( arguments.length > 1 ) ? arguments[ 1 ] : null;

      // Check if the selected control is a switch
      if( !$( this ).hasClass( 'wpdk-ui-switch' ) && !$( this ).hasClass( 'wpdk-ui-switch-input' ) ) {
        return $( this );
      }

      var $this, $input_checkbox;

      // Get input checkbox
      if( $( this ).hasClass( 'wpdk-ui-switch-input' ) ) {
        $input_checkbox = $( this );
        $this = $input_checkbox.parent( 'div.wpdk-ui-switch' );
      }
      else {
        $input_checkbox = $( this ).find( 'input[type="checkbox"]' );
        $this = $( this );
      }

      if( null == options && 'state' == method ) {
        return _state();
      }

      /**
       * Return or change the state of a switch control.
       *
       * @private
       */
      function _state()
      {
        /**
         * Filter the switch state before change.
         *
         * @param {boolean} state The switch state.
         * @param {*} control The switch control.
         */
        var _options = wpdk_apply_filters( 'wpdk_ui_switch_state', options, $this );

        // Return switch state if options is null
        if( null === _options ) {
          return $input_checkbox.is( ':checked' );
        }

        if( true === _options && !$input_checkbox.is( ':checked' ) ) {
          $input_checkbox.attr( 'checked', 'checked' ).change();
        }
        else if( $input_checkbox.is( ':checked' ) ) {
          $input_checkbox.removeAttr( 'checked' ).change();
        }

        /**
         * Fires when a swipe is changed.
         *
         * @param {object} $control The swipe control.
         * @param {string} status The swipe status 'on'.
         */
        wpdk_do_action( 'wpdk_ui_switch_changed', options, $this );

        return $this;
      }

      return this.each( function()
      {

        // Get input checkbox
        if( $( this ).hasClass( 'wpdk-ui-switch-input' ) ) {
          $input_checkbox = $( this );
          $this = $input_checkbox.parent( 'div.wpdk-ui-switch' );
        }
        else {
          $input_checkbox = $( this ).find( 'input[type="checkbox"]' );
          $this = $( this );
        }

        switch( method ) {
          case 'state':
            return _state();
            break;

          case 'toggle':
            options = !$input_checkbox.is( ':checked' );
            return _state();
            break;
        }

      } );

    };

  }( jQuery );

}


/**
 * WPDK Button Toggle extends.
 */
if( typeof( jQuery.fn.wpdkButtonToggle ) === 'undefined' ) {

  +function( $ )
  {
    'use strict';

    $.fn.wpdkButtonToggle = function()
    {
      // Loop into the controls
      return this.each( function()
      {
        // Control
        var $control = $( this );

        // Current state
        var current_state = $control.data( 'current_state' ) || 'false';

        // Alternate state (inverter)
        var alternate_state = ( 'false' == current_state ) ? 'true' : 'false';

        // Current label
        var current_label = $control.html();

        // Alternate label
        var alternate_label = $control.data( 'alternate_label' );

        // Set alternate state
        $control.data( 'current_state', alternate_state );

        // Set alternate label
        $control.html( alternate_label );
        $control.data( 'alternate_label', current_label );

        // Set class
        $control
          .removeClass( 'wpdk-ui-button-toggle-false wpdk-ui-button-toggle-true' )
          .addClass( 'wpdk-ui-button-toggle-' + alternate_state );

        // Trigger event
        $control.trigger( WPDKUIComponentEvents.TOGGLE_BUTTON, [ $control, alternate_state ] );

      } );
    };

    // Init
    $( document ).on( 'click', 'button.wpdk-ui-button-toggle', function( e )
    {
      e.preventDefault();
      $( this ).wpdkButtonToggle();
      return false;
    } );

  }( jQuery );

}

/**
 * WPDK Selectable Checkbox by shift key
 *
 * Usage: $form.find('input[type="checkbox"]').wpdkShiftSelectableCheckbox();
 * replace input[type="checkbox"] with the selector to match your list of checkboxes
 *
 */
if( typeof jQuery.fn.wpdkShiftSelectableCheckbox === 'undefined' ) {
  +function( $ )
  {

    'use strict';

    $.fn.wpdkShiftSelectableCheckbox = function()
    {
      var lastChecked, $boxes = this;

      $boxes.click( function( evt )
      {
        if( !lastChecked ) {
          lastChecked = this;
          return;
        }

        if( evt.shiftKey ) {
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
 * @copyright       Copyright (C) 2012-2015 wpXtreme Inc. All Rights Reserved.
 * @date            2015-01-12
 * @version         1.1.5
 *
 * @history         1.1.2 - Renamed internal _WPDKControls in in _WPDKControls
 * @history         1.1.3 - Added collapsable fieldset
 * @history         1.1.4 - Added file media init.
 * @history         1.1.5 - Added color picker init.
 *
 */

// One time
if( typeof( window.WPDKControls ) === 'undefined' ) {

  // On document ready
  jQuery( function( $ )
  {
    "use strict";

    window.WPDKControls = (function()
    {

      // This object
      var _WPDKControls = {
        version : '1.1.4',
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
        _initSwitches();
        _initColorPicker();
        _initFileMedia();
        _initScrollable();
        _initCollapsableFieldset();

        // Experimental
        _initAccordion();
        _initGuide();

        return _WPDKControls;
      }

      /**
       * Init the color picker ui control. Based on WordPress color picker.
       * @private
       */
      function _initColorPicker()
      {
        var is_chrome = ( window.navigator.userAgent.indexOf( "Chrome" ) != -1 );
        var is_firefox = ( window.navigator.userAgent.indexOf( "Firefox" ) != -1 );
        var is_opera = ( window.navigator.userAgent.indexOf( "Opera" ) != -1 );

        // Firefox, Opera and Chrome supports a native input type color
        if( is_chrome || is_firefox || is_opera ) {
          return;
        }

        // Loop into each color picker control in order to set the right options
        $( '.wpdk-ui-color-picker' ).each( function()
        {
          // Get current control
          var $control = $( this );

          // Default options
          var options = {
            defaultColor : $control.data( 'defaultColor' ) || '#ffffff',
            hide         : $control.data( 'hide' ) || true,
            palettes     : $control.data( 'palettes' ) || true,
            change       : function( event, ui )
            {
              $control.trigger( 'input' );
            },
            clear        : function()
            {
              $control.trigger( 'clear' );
            }
          };

          // Engage
          $control.wpColorPicker( options );

        } );


      }

      /**
       * Init file media control.
       * @since WPDK 1.10.0
       *
       * @private
       */
      function _initFileMedia()
      {
        var wpdk_file_frame, wpdk_attachment;

        $( document ).on( 'click', '.wpdk-ui-file-media button[type="reset"]', false, function( event )
        {
          event.preventDefault();
          var $control = $( this ).parent( '.wpdk-ui-file-media' ).find( 'input[type="hidden"]' );
          var $visible = $( this ).parent( '.wpdk-ui-file-media' ).find( 'input[type="text"]' );

          $control.val( '' );
          $visible.val( '' );

          return false;

        } );

        $( document ).on( 'click', '.wpdk-ui-file-media button[type="button"],.wpdk-ui-file-media input[type="text"]', false, function( event )
        {
          event.preventDefault();

          // If the media frame already exists, reopen it.
          if( wpdk_file_frame ) {
            wpdk_file_frame.open();
            return;
          }

          var $control = $( this ).parent( '.wpdk-ui-file-media' ).find( 'input[type="hidden"]' );
          var $visible = $( this ).parent( '.wpdk-ui-file-media' ).find( 'input[type="text"]' );
          var title = $control.data( 'title' );
          var button_text = $control.data( 'button_text' );

          // Create the media frame.
          wpdk_file_frame = wp.media.frames.file_frame = wp.media( {
            title    : title,
            button   : {
              text : button_text
            },
            multiple : false  // Set to true to allow multiple files to be selected
          } );

          // When an image is selected, run a callback.
          wpdk_file_frame.on( 'select', function()
          {
            // We set multiple to false so only get one image from the uploader
            wpdk_attachment = wpdk_file_frame.state().get( 'selection' ).first().toJSON();

            // Do something with attachment.id and/or attachment.url here
            $control.val( wpdk_attachment.url );
            $visible.val( wpdk_attachment.url );

          } );

          // Finally, open the modal
          wpdk_file_frame.open();
        } );
      }

      /**
       * Init the default event for switch ui control button.
       * @private
       */
      function _initSwitches()
      {
        // Get all switches on page
        wpdkSwitches().each(
          function()
          {
            // Get switch
            var $control = $( this );

            // Check for Ajax action
            var on_switch = $control.data( 'on_switch' );

            if( typeof on_switch !== 'undefined' ) {

              // Ajax
              $.post( wpdk_i18n.ajaxURL, {
                  action    : 'wpdk_action_on_switch',
                  on_switch : on_switch,
                  state     : $control.is( ':checked' )
                }, function( data )
                {
                  var response = new WPDKAjaxResponse( data );

                  if( empty( response.error ) ) {

                    /**
                     * Fires when a switch ui button ajax is successfully returned.
                     *
                     * The dynamic portion of the hook name, on_switch, refers to the action name in data attribute.
                     *
                     * @param {*} response The JSON response object.
                     * @param {*} $control The switch ui control.
                     */
                    wpdk_do_action( 'wpdk_on_switch-' + on_switch, response, $control );

                  }
                  // An error return
                  else {
                    /**
                     * Fires when a switch ui button ajax has error occurs.
                     *
                     * The dynamic portion of the hook name, on_switch, refers to the action name in data attribute.
                     *
                     * @param {*} response The JSON response object.
                     * @param {*} $control The switch ui control.
                     */
                    wpdk_do_action( 'wpdk_on_switch_error-' + on_switch, response, $control );
                  }
                }
              );
            }
          }
        );

      }

      /**
       * Init the collapsable fieldset.
       * @private
       */
      function _initCollapsableFieldset()
      {
        // Remove all previous bind event
        $( document ).off( 'click', 'fieldset.wpdk-form-fieldset.wpdk-fieldset-collapse legend' );

        $( document ).on( 'click', 'fieldset.wpdk-form-fieldset.wpdk-fieldset-collapse legend', function( e )
        {
          // Get current fieldset (parent)
          var $fieldset = $( this ).parents( 'fieldset' );

          // Default classes
          var remove_class = $fieldset.hasClass( 'wpdk-fieldset-collapse-open' ) ? 'wpdk-fieldset-collapse-open' : 'wpdk-fieldset-collapse-close';
          var add_class = $fieldset.hasClass( 'wpdk-fieldset-collapse-open' ) ? 'wpdk-fieldset-collapse-close' : 'wpdk-fieldset-collapse-open';

          // Check for special key
          if( e.altKey ) {

            // Get the parent view
            var $view = $( this ).parents( 'div[data-type="wpdk-view"]' );

            // Set all in this view
            $view.find( 'fieldset.wpdk-form-fieldset.wpdk-fieldset-collapse' ).removeClass( remove_class ).addClass( add_class );
          }
          else {
            $fieldset.removeClass( remove_class ).addClass( add_class );
          }
        } );
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
        // Remove all previous bind event
        $( document ).off( 'click', 'span.wpdk-form-clear-left i' );
        $( document ).off( 'click', '.wpdk-disable-after-click' );
        $( document ).off( 'click', '.wpdk-form-locked' );

        // Init with clear
        $( document ).on( 'click', 'span.wpdk-form-clear-left i', false, function()
        {
          $( this ).prev( 'input' ).val( '' ).trigger( 'change' ).trigger( 'keyup' ).trigger( WPDKUIComponentEvents.CLEAR_INPUT );
        } );

        // Init with disable after click
        $( document ).on( 'click', '.wpdk-disable-after-click', false, function()
        {
          $( this ).addClass( 'disabled' );
        } );

        // Init locked
        $( document ).on( 'click', '.wpdk-form-locked', false, function()
        {
          // Check for custom data confirm
          var message = $( this ).data( 'confirm' );

          // TODO Add event

          if( confirm( empty( message ) ? wpdk_i18n.messageUnLockField : message ) ) {
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
       * @deprecated since 1.9.0
       */
      function _initSwipe()
      {
        // Remove all previous bind event
        $( document ).off( 'click', 'span.wpdk-form-swipe span' );
        $( document ).off( WPDKUIComponents.REFRESH_SWIPE );

        // Bind click on right span
        $( document ).on( 'click', 'span.wpdk-form-swipe span', false, function()
        {
          var $control = $( this ).parent();
          var status = wpdk_is_bool( $control.wpdkSwipe() );
          var enabled = status ? 'off' : 'on';
          $control.trigger( WPDKUIComponentEvents.SWIPE, [ $control, enabled ] );
          $control.wpdkSwipe( enabled );

          // @since 1.7.0 - get the on swipe
          var on_swipe = $control.data( 'on_swipe' );

          if( typeof on_swipe !== 'undefined' ) {

            // Ajax
            $.post( wpdk_i18n.ajaxURL, {
                action   : 'wpdk_action_on_swipe',
                on_swipe : on_swipe,
                enabled  : enabled
              }, function( data )
              {
                var response = new WPDKAjaxResponse( data );

                if( empty( response.error ) ) {
                  // Process response
                }
                // An error return
                else {
                  alert( response.error );
                }
              }
            );
          }

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
        // Remove all previous bind event
        $( document ).off( 'click', '.wpdk-form-scrollable img' );

        $( document ).on( 'click', '.wpdk-form-scrollable img', false, function()
        {
          $( this ).toggleClass( 'wpdk-selected' );
        } );
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
        if( accordion.length ) {
          /* Memorizzo altezza */
          accordion.parent().next( 'div.wpdk-accordion' ).each( function( i, e )
          {
            $( this ).addClass( 'wpdk-accordion-open' ).data( 'height', $( this ).height() );
            if( i > 0 ) {
              $( this ).removeClass( 'wpdk-accordion-open' );
            }
            else {
              $( e ).css( 'height', $( e ).data( 'height' ) + 'px' );
            }
          } );

          accordion.click( function()
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
        $( 'a.wpdk-guide' ).click( function()
        {
          var title, content, url;

          // Set guide title
          title = $( this ).data( 'title' );

          // If guide content is directly into data-content attribute
          if( typeof $( this ).data( 'content' ) != 'undefined' && $( this ).data( 'content' ).length > 0 ) {
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
