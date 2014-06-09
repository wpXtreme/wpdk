/**
 * wpdkModal
 *
 * @class           wpdkModal
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-03-20
 * @version         3.1.1
 * @note            Base on bootstrap: modal.js v3.1.0
 *
 * - Rename namespace modal with wpdkModal
 * - Rename namespace "bs" with "wpdk"
 * - Rename namespace "bs.modal" with "wpdk.wpdkModal"
 * - Rename `modal-backdrop` class in `wpdk-modal-backdrop`
 *
 */

// One time
if( typeof( jQuery.fn.wpdkPopover ) === 'undefined' ) {

/* ========================================================================
 * Bootstrap: modal.js v3.1.0
 * http://getbootstrap.com/javascript/#modals
 * ========================================================================
 * Copyright 2011-2014 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 * ======================================================================== */


+function ($) {
  'use strict';

  // MODAL CLASS DEFINITION
  // ======================

  var Modal = function (element, options) {
    this.options   = options
    this.$element  = $(element)
    this.$backdrop =
    this.isShown   = null

    if (this.options.remote) {
      this.$element
        .find('.modal-content')
        .load(this.options.remote, $.proxy(function () {
          this.$element.trigger('loaded.wpdk.wpdkModal')
        }, this))
    }
  }

  Modal.DEFAULTS = {
    backdrop: true,
    keyboard: true,
    show: true
  }

  Modal.prototype.toggle = function (_relatedTarget) {
    return this[!this.isShown ? 'show' : 'hide'](_relatedTarget)
  }

  Modal.prototype.show = function (_relatedTarget) {
    var that = this
    var e    = $.Event('show.wpdk.wpdkModal', { relatedTarget: _relatedTarget })

    this.$element.trigger(e)

    if (this.isShown || e.isDefaultPrevented()) return

    this.isShown = true

    this.escape()

    this.$element.on('click.dismiss.wpdk.wpdkModal', '[data-dismiss="wpdkModal"]', $.proxy(this.hide, this))

    this.backdrop(function () {
      var transition = $.support.transition && that.$element.hasClass('fade')

      if (!that.$element.parent().length) {
        that.$element.appendTo(document.body) // don't move modals dom position
      }

      that.$element
        .show()
        .scrollTop(0)

      if (transition) {
        that.$element[0].offsetWidth // force reflow
      }

      that.$element
        .addClass('in')
        .attr('aria-hidden', false)

      that.enforceFocus()

      var e = $.Event('shown.wpdk.wpdkModal', { relatedTarget: _relatedTarget })

      transition ?
        that.$element.find('.modal-dialog') // wait for modal to slide in
          .one($.support.transition.end, function () {
            that.$element.focus().trigger(e)
          })
          .emulateTransitionEnd(300) :
        that.$element.focus().trigger(e)
    })
  }

  Modal.prototype.hide = function (e) {
    if (e) e.preventDefault()

    e = $.Event('hide.wpdk.wpdkModal')

    this.$element.trigger(e)

    if (!this.isShown || e.isDefaultPrevented()) return

    this.isShown = false

    this.escape()

    $(document).off('focusin.wpdk.wpdkModal')

    this.$element
      .removeClass('in')
      .attr('aria-hidden', true)
      .off('click.dismiss.wpdk.wpdkModal')

    $.support.transition && this.$element.hasClass('fade') ?
      this.$element
        .one($.support.transition.end, $.proxy(this.hideModal, this))
        .emulateTransitionEnd(300) :
      this.hideModal()
  }

  Modal.prototype.enforceFocus = function () {
    $(document)
      .off('focusin.wpdk.wpdkModal') // guard against infinite focus loop
      .on('focusin.wpdk.wpdkModal', $.proxy(function (e) {
        if (this.$element[0] !== e.target && !this.$element.has(e.target).length) {
          this.$element.focus()
        }
      }, this))
  }

  Modal.prototype.escape = function () {
    if (this.isShown && this.options.keyboard) {
      this.$element.on('keyup.dismiss.wpdk.wpdkModal', $.proxy(function (e) {
        e.which == 27 && this.hide()
      }, this))
    } else if (!this.isShown) {
      this.$element.off('keyup.dismiss.wpdk.wpdkModal')
    }
  }

  Modal.prototype.hideModal = function () {
    var that = this
    this.$element.hide()
    this.backdrop(function () {
      that.removeBackdrop()
      that.$element.trigger('hidden.wpdk.wpdkModal')
    })
  }

  Modal.prototype.removeBackdrop = function () {
    this.$backdrop && this.$backdrop.remove()
    this.$backdrop = null
  }

  Modal.prototype.backdrop = function (callback) {
    var animate = this.$element.hasClass('fade') ? 'fade' : ''

    if (this.isShown && this.options.backdrop) {
      var doAnimate = $.support.transition && animate

      this.$backdrop = $('<div class="wpdk-modal-backdrop ' + animate + '" />')
        .appendTo(document.body)

      this.$element.on('click.dismiss.wpdk.wpdkModal', $.proxy(function (e) {
        if (e.target !== e.currentTarget) return
        this.options.backdrop == 'static'
          ? this.$element[0].focus.call(this.$element[0])
          : this.hide.call(this)
      }, this))

      if (doAnimate) this.$backdrop[0].offsetWidth // force reflow

      this.$backdrop.addClass('in')

      if (!callback) return

      doAnimate ?
        this.$backdrop
          .one($.support.transition.end, callback)
          .emulateTransitionEnd(150) :
        callback()

    } else if (!this.isShown && this.$backdrop) {
      this.$backdrop.removeClass('in')

      $.support.transition && this.$element.hasClass('fade') ?
        this.$backdrop
          .one($.support.transition.end, callback)
          .emulateTransitionEnd(150) :
        callback()

    } else if (callback) {
      callback()
    }
  }


  // MODAL PLUGIN DEFINITION
  // =======================

  var old = $.fn.wpdkModal

  $.fn.wpdkModal = function (option, _relatedTarget) {
    return this.each(function () {
      var $this   = $(this)
      var data    = $this.data('wpdk.wpdkModal')
      var options = $.extend({}, Modal.DEFAULTS, $this.data(), typeof option == 'object' && option)

      if (!data) $this.data('wpdk.wpdkModal', (data = new Modal(this, options)))
      if (typeof option == 'string') data[option](_relatedTarget)
      else if (options.show) data.show(_relatedTarget)
    })
  }

  $.fn.wpdkModal.Constructor = Modal


  // MODAL NO CONFLICT
  // =================

  $.fn.wpdkModal.noConflict = function () {
    $.fn.wpdkModal = old
    return this
  }

  // MODAL DATA-API
  // ==============

  $(document).on('click.wpdk.wpdkModal.data-api', '[data-toggle="wpdkModal"]', function (e) {
    var $this   = $(this)
    var href    = $this.attr('href')
    var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) //strip for ie7
    var option  = $target.data('wpdk.wpdkModal') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data())

    if ($this.is('a')) e.preventDefault()

    $target
      .wpdkModal(option, this)
      .one('hide', function () {
        $this.is(':visible') && $this.focus()
      })
  })

  $(document)
    .on('show.wpdk.wpdkModal', '.wpdk-modal', function () { $(document.body).addClass('wpdk-modal-open') })
    .on('hidden.wpdk.wpdkModal', '.wpdk-modal', function () { $(document.body).removeClass('wpdk-modal-open') })

    // Extends with Permanent dismiss
    $( document ).on( 'click', '.wpdk-modal button.close.wpdk-modal-permanent-dismiss', function() {
      var $this    = $(this);
      var modal_id = $this.closest( '.wpdk-modal' ).attr( 'id' );

      // Ajax
      $.post( wpdk_i18n.ajaxURL, {
          action      : 'wpdk_action_modal_dismiss',
          modal_id    : modal_id
        }, function ( data )
        {
          var response = new WPDKAjaxResponse( data );

          if ( empty( response.error ) ) {
            // Process response

          }
          // An error return
          else {
            alert( response.error );
          }
        }
      );
    } );

}(jQuery);

}

// Once time...
if ( typeof( window.WPDKUIModalDialog ) === 'undefined' ) {

  /**
   * Utility WPDKUI Modal dialog.
   *
   * @class           WPDKUIModalDialog
   * @author          =undo= <info@wpxtre.me>
   * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
   * @date            2014-02-13
   * @version         1.0.0
   *
   */
  window.WPDKUIModalDialog = function ( id, title, content )
  {

    "use strict";

    // Remove conflict
    var $ = window.jQuery;

    // This object
    var $t = {
      version        : '1.0.0',
      id             : id,
      title          : title || '',
      content        : content || '',
      width          : '',
      height         : '',
      dismiss_button : true,
      data           : [],

      html       : _html,
      display    : _display,
      add_button : _add_button
    };

    // Private
    var buttons = [];

    /**
     * Return the HTML aria title format
     *
     * @return string
     */
    function _aria_title()
    {
      return $t.id + '-title';
    }

    /**
     * Return the HTML markup for top right dismiss button [x]
     *
     * @return {string}
     */
    function _dismiss_button()
    {
      var result = '';
      if ( $t.dismiss_button ) {
        result = '<button type="button" class="close" data-dismiss="wpdkModal" aria-hidden="true">×</button>';
      }
      return result;
    }

    /**
     * Build the inline CSS style for width and heght
     *
     * @return string
     */
    function _size()
    {
      var result = '',
        styles = {},
        style,
        stack = [];

      if ( !empty( $t.width ) ) {
        styles.width = $t.width + 'px';
      }

      if ( !empty( $t.height ) ) {
        styles.height = $t.height + 'px';
      }

      for ( style in styles ) {
        stack.push( style + ':' + styles[style] );
      }

      if ( !empty( stack ) ) {
        result = 'style="' + implode( ';', stack ) + '"';
      }

      return result;
    }

    /**
     * Return the HTML markup for footer buttons
     *
     * @return string
     */
    function _buttons()
    {
      var result = '',
        key,
        buttons = '';

      for ( key in $t.buttons ) {
        var $value = $t.buttons[key];
        var $class = isset( $value['classes'] ) ? $value['classes'] : isset( $value['class'] ) ? $value['class'] : '';
        var $label = isset( $value['label'] ) ? $value['label'] : '';
        var $data_dismiss = ( isset( $value['dismiss'] ) && true == $value['dismiss'] ) ? 'data-dismiss="wpdkModal"' : '';
        buttons += sprintf( '<button id="%s" class="button %s" %s aria-hidden="true">%s</button>', key, $class, $data_dismiss, $label );
      }

      if ( !empty( buttons ) ) {
        result = sprintf( '<div class="modal-footer">%s</div>', buttons );
      }

      return result;
    }

    /**
     * Return the HTML attribute markup for 'data-' attribute
     *
     * @return string
     */
    function _data()
    {
      var result = '',
        stack = [],
        key,
        value;

      for ( key in $t.data ) {
        value = $t.data[key];
        stack.push( sprintf( 'data-%s="%s"', key, value ) );
      }

      if( !empty( stack ) ) {
        result = join( ' ', stack );
      }

      return result;
    }

    /**
     * Return the HTML markup for modal
     *
     * @return string
     */
    function _html()
    {

      return '<div class="wpdk-modal hide fade" ' +
        _data() +
        'id="' + $t.id + '"' +
        'tabindex="-1"' +
        'role="dialog"' +
        'aria-labelledby="' + _aria_title() + '"' +
        'aria-hidden="true">' +
        '<div ' + _size() + ' class="modal-dialog">' +
        '<div class="modal-content">' +
        '<div class="modal-header">' +
        _dismiss_button() +
        '<h4 class="modal-title" id="' + _aria_title() + '">' + $t.title + '</h4>' +
        '</div>' +
        '<div class="modal-body">' +
        $t.content +
        '</div>' +
        _buttons() +
        '</div>' +
        '</div>' +
        '</div>';
    }

    /**
     * Display the modal
     */
    function _display()
    {
      // Attach under the body
      $( 'body' ).append( _html() );

      // Get element
      var $modal = $( '#' + $t.id );

      // Display
      $modal.wpdkModal( 'show' );

      // Remove HTML markup when hide
      $modal.on( 'hidden.wpdk.wpdkModal', function ()
      {
        $( this ).remove();
      } );
    }

    /**
     * Add a footer button
     *
     * @param {string} id
     * @param {string} label
     * @param {boolean} dismiss Boolean
     * @param {string} classes Additional classes
     */
    function _add_button( id, label, dismiss, classes )
    {
     buttons[id] = {
        label   : label,
        classes : classes || '',
        dismiss : dismiss || true
      };
    }

    /**
     * Add an attribute data
     */
    function _add_data( $key, $value )
    {
      $t.data.push(
        {
          key   : key,
          value : $value
        } );
    };

    /**
     * Return the HTML markup for button tag to open this modal dialog
     *
     * @param {string} label Text button label
     * @param {string} classes Additional class
     *
     * @return {string}
     */
    this.button_open_modal = function ( label, classes )
    {
      var id = '#' + $t.id;
      return sprintf( '<button class="button %s" type="button" data-toggle="modal" data-target="%s">%s</button>', ( classes || '' ), id, label );
    }

    return $t;

  };
}

// Once time...
if ( typeof( window.WPDKTwitterBootstrapModal ) === 'undefined' ) {

  /**
   * Utility for Twitter Bootstrap Modal dialog.
   *
   * @class           WPDKTwitterBootstrapModal
   * @author          =undo= <info@wpxtre.me>
   * @copyright       Copyright (C) 2012-2013 wpXtreme Inc. All Rights Reserved.
   * @date            2013-10-30
   * @version         0.2.2
   * @deprecated      since 1.4.21 - use WPDKUIModalDialog instead
   *
   */
  window.WPDKTwitterBootstrapModal = function ( $id, $title, $content )
  {

    /**
     * Resolve conflict
     *
     * @type {jQuery}
     */
    var $ = window.jQuery;

    this.version = '0.2.2';
    this.id = $id;
    this.title = $title;
    this.content = $content;
    this.width = '';
    this.height = '';
    this.close_button = true;
    this.buttons = [];
    this.data = [];

    /**
     * @type {WPDKTwitterBootstrapModal}
     */
    var $t = this;

    /**
     * Return the HTML aria title format
     *
     * @return string
     */
    function aria_title()
    {
      return $t.id + '-title';
    }

    /**
     * Return the HTML markup for top right dismiss button [x]
     *
     * @return {string}
     */
    function close_button()
    {
      var $result = '';
      if ( $t.close_button ) {
        $result = '<button type="button" class="close" data-dismiss="wpdkModal" aria-hidden="true">×</button>';
      }
      return $result;
    }

    /**
     * Build the inline CSS style for width and heght
     *
     * @return string
     */
    function size()
    {
      var result = '', styles = {}, style, stack = [];

      if ( !empty( $t.width ) ) {
        styles.width = $t.width + 'px';
      }

      if ( !empty( $t.height ) ) {
        styles.height = $t.height + 'px';
      }

      for ( style in styles ) {
        stack.push( style + ':' + styles[style] );
      }

      if ( !empty( stack ) ) {
        result = 'style="' + implode( ';', stack ) + '"';
      }

      return result;
    }

    /**
     * Return the HTML markup for footer buttons
     *
     * @return string
     */
    function buttons()
    {
      var result = '', key, str_buttons = '';

      if ( !empty( $t.buttons ) ) {
        for ( key in $t.buttons ) {
          var $value = $t.buttons[key];
          var $class = isset( $value['classes'] ) ? $value['classes'] : isset( $value['class'] ) ? $value['class'] : '';
          var $label = isset( $value['label'] ) ? $value['label'] : '';
          var $data_dismiss = ( isset( $value['dismiss'] ) && true == $value['dismiss'] ) ? 'data-dismiss="wpdkModal"' : '';
          str_buttons += sprintf( '<button id="%s" class="button %s" %s aria-hidden="true">%s</button>', key, $class, $data_dismiss, $label );
        }
      }

      if ( !empty( str_buttons ) ) {
        result = sprintf( '<div class="modal-footer">%s</div>', buttons );
      }

      return result;
    }

    /**
     * Return the HTML attribute markup for 'data-' attribute
     *
     * @return string
     */
    function data()
    {
      var result = '', stack = [], key, value;
      if ( !empty( $t.data ) ) {
        for ( key in $t.data ) {
          value = $t.data[key];
          stack.push( sprintf( 'data-%s="%s"', key, value ) );
        }
        result = join( ' ', stack );
      }
      return result;
    }

    /**
     * Return the HTML markup for Twitter boostrap modal
     *
     * @return string
     */
    this.html = function ()
    {

      return '<div class="wpdk-modal hide fade" ' +
        data() +
        'id="' + $t.id + '"' +
        'tabindex="-1"' +
        'role="dialog"' +
        'aria-labelledby="' + aria_title() + '"' +
        'aria-hidden="true">' +
        '<div ' + size() + ' class="modal-dialog">' +
        '<div class="modal-content">' +
        '<div class="modal-header">' +
        close_button() +
        '<h4 class="modal-title" id="' + aria_title() + '">' + $t.title + '</h4>' +
        '</div>' +
        '<div class="modal-body">' +
        $t.content +
        '</div>' +
        buttons() +
        '</div>' +
        '</div>' +
        '</div>';
    };

    /**
     * Display the Twitter boostrap modal
     */
    this.display = function ()
    {
      $( 'body' ).append( $t.html() );
      var modal = $( '#' + $t.id );
      modal.wpdkModal( 'show' );
      modal.on( 'hidden', function ()
      {
        $( this ).remove();
      } );

      /* Twitter Bootstrap v.3.0.0 */
      modal.on( 'hidden.wpdk.wpdkModal', function ()
      {
        $( this ).remove();
      } );
    };

    /**
     * Add a footer button
     *
     * @param {string} id
     * @param {string} label
     * @param {boolean} dismiss Boolean
     * @param {string} classes Additional classes
     */
    this.add_buttons = function ( id, label, dismiss, classes )
    {
      $t.buttons[id] = {
        label   : label,
        classes : classes || '',
        dismiss : dismiss || true
      };
    };

    /**
     * Add an attribute data
     */
    this.add_data = function ( $key, $value )
    {
      $t.data.push(
        {
          key   : key,
          value : $value
        } );
    };

    /**
     * Return the HTML markup for button tag to open this modal dialog
     *
     * @param {string} label Text button label
     * @param {string} classes Additional class
     *
     * @return {string}
     */
    this.button_open_modal = function ( label, classes )
    {
      var id = '#' + $t.id;
      return sprintf( '<button class="button %s" type="button" data-toggle="modal" data-target="%s">%s</button>', ( classes || '' ), id, label );
    }

  };
}
