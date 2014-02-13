/**
 * wpdkModal
 *
 * @class           wpdkModal
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-02-08
 * @version         3.1.0
 * @note            Base on bootstrap: modal.js v3.1.0
 *
 * - Rename namespace modal with wpdkModal
 * - Rename namespace "bs" with "wpdk"
 * - Rename namespace "bs.modal" with "wpdk.modal"
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
          this.$element.trigger('loaded.wpdk.modal')
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
    var e    = $.Event('show.wpdk.modal', { relatedTarget: _relatedTarget })

    this.$element.trigger(e)

    if (this.isShown || e.isDefaultPrevented()) return

    this.isShown = true

    this.escape()

    this.$element.on('click.dismiss.wpdk.modal', '[data-dismiss="modal"]', $.proxy(this.hide, this))

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

      var e = $.Event('shown.wpdk.modal', { relatedTarget: _relatedTarget })

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

    e = $.Event('hide.wpdk.modal')

    this.$element.trigger(e)

    if (!this.isShown || e.isDefaultPrevented()) return

    this.isShown = false

    this.escape()

    $(document).off('focusin.wpdk.modal')

    this.$element
      .removeClass('in')
      .attr('aria-hidden', true)
      .off('click.dismiss.wpdk.modal')

    $.support.transition && this.$element.hasClass('fade') ?
      this.$element
        .one($.support.transition.end, $.proxy(this.hideModal, this))
        .emulateTransitionEnd(300) :
      this.hideModal()
  }

  Modal.prototype.enforceFocus = function () {
    $(document)
      .off('focusin.wpdk.modal') // guard against infinite focus loop
      .on('focusin.wpdk.modal', $.proxy(function (e) {
        if (this.$element[0] !== e.target && !this.$element.has(e.target).length) {
          this.$element.focus()
        }
      }, this))
  }

  Modal.prototype.escape = function () {
    if (this.isShown && this.options.keyboard) {
      this.$element.on('keyup.dismiss.wpdk.modal', $.proxy(function (e) {
        e.which == 27 && this.hide()
      }, this))
    } else if (!this.isShown) {
      this.$element.off('keyup.dismiss.wpdk.modal')
    }
  }

  Modal.prototype.hideModal = function () {
    var that = this
    this.$element.hide()
    this.backdrop(function () {
      that.removeBackdrop()
      that.$element.trigger('hidden.wpdk.modal')
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

      this.$element.on('click.dismiss.wpdk.modal', $.proxy(function (e) {
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
      var data    = $this.data('wpdk.modal')
      var options = $.extend({}, Modal.DEFAULTS, $this.data(), typeof option == 'object' && option)

      if (!data) $this.data('wpdk.modal', (data = new Modal(this, options)))
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

  $(document).on('click.wpdk.modal.data-api', '[data-toggle="modal"]', function (e) {
    var $this   = $(this)
    var href    = $this.attr('href')
    var $target = $($this.attr('data-target') || (href && href.replace(/.*(?=#[^\s]+$)/, ''))) //strip for ie7
    var option  = $target.data('wpdk.modal') ? 'toggle' : $.extend({ remote: !/#/.test(href) && href }, $target.data(), $this.data())

    if ($this.is('a')) e.preventDefault()

    $target
      .wpdkModal(option, this)
      .one('hide', function () {
        $this.is(':visible') && $this.focus()
      })
  })

  $(document)
    .on('show.wpdk.modal', '.wpdk-modal', function () { $(document.body).addClass('wpdk-modal-open') })
    .on('hidden.wpdk.modal', '.wpdk-modal', function () { $(document.body).removeClass('wpdk-modal-open') })

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
  window.WPDKUIModalDialog = function ( $id, $title, $content )
  {

    // Remove conflict
    var $ = window.jQuery;

    this.version = '1.0.0';
    this.id = $id;
    this.title = $title;
    this.content = $content;
    this.width = '';
    this.height = '';
    this.close_button = true;
    this.buttons = [];
    this.data = [];

    /**
     * @type {WPDKUIModalDialog}
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
        $result = '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
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
      var result = '', key, buttons = '';
      if ( !empty( $t.buttons ) ) {
        for ( key in $t.buttons ) {
          var $value = $t.buttons[key];
          var $class = isset( $value['classes'] ) ? $value['classes'] : isset( $value['class'] ) ? $value['class'] : '';
          var $label = isset( $value['label'] ) ? $value['label'] : '';
          var $data_dismiss = ( isset( $value['dismiss'] ) && true == $value['dismiss'] ) ? 'data-dismiss="modal"' : '';
          buttons += sprintf( '<button id="%s" class="button %s" %s aria-hidden="true">%s</button>', key, $class, $data_dismiss, $label );
        }
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
     * Return the HTML markup for modal
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
     * Display the modal
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

      // Twitter Bootstrap v.3.1.0
      modal.on( 'hidden.wpdk.modal', function ()
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
        $result = '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>';
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
      var result = '', key, buttons = '';
      if ( !empty( $t.buttons ) ) {
        for ( key in $t.buttons ) {
          var $value = $t.buttons[key];
          var $class = isset( $value['classes'] ) ? $value['classes'] : isset( $value['class'] ) ? $value['class'] : '';
          var $label = isset( $value['label'] ) ? $value['label'] : '';
          var $data_dismiss = ( isset( $value['dismiss'] ) && true == $value['dismiss'] ) ? 'data-dismiss="modal"' : '';
          buttons += sprintf( '<button id="%s" class="button %s" %s aria-hidden="true">%s</button>', key, $class, $data_dismiss, $label );
        }
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
      modal.on( 'hidden.wpdk.modal', function ()
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
