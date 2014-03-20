/**
 * wpdkButton
 *
 * @class           wpdkButton
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-03-20
 * @version         3.1.1
 * @note            Base on bootstrap: button.js v3.1.0
 *
 * - Rename namespace button with wpdkButton
 * - Rename namespace "bs" with "wpdk"
 * - Rename namespace "wpdk.button" with "wpdk.wpdkButton"
 */

// One time
if( typeof( jQuery.fn.wpdkButton ) === 'undefined' ) {

  /* ========================================================================
   * Bootstrap: button.js v3.1.0
   * http://getbootstrap.com/javascript/#buttons
   * ========================================================================
   * Copyright 2011-2014 Twitter, Inc.
   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
   * ======================================================================== */


  +function ($) {
    'use strict';

    // BUTTON PUBLIC CLASS DEFINITION
    // ==============================

    var Button = function (element, options) {
      this.$element  = $(element)
      this.options   = $.extend({}, Button.DEFAULTS, options)
      this.isLoading = false
    }

    Button.DEFAULTS = {
      loadingText: 'loading...'
    }

    Button.prototype.setState = function (state) {
      var d    = 'disabled'
      var $el  = this.$element
      var val  = $el.is('input') ? 'val' : 'html'
      var data = $el.data()

      state = state + 'Text'

      if (!data.resetText) $el.data('resetText', $el[val]())

      $el[val](data[state] || this.options[state])

      // push to event loop to allow forms to submit
      setTimeout($.proxy(function () {
        if (state == 'loadingText') {
          this.isLoading = true
          $el.addClass(d).attr(d, d)
        } else if (this.isLoading) {
          this.isLoading = false
          $el.removeClass(d).removeAttr(d)
        }
      }, this), 0)
    }

    Button.prototype.toggle = function () {
      var changed = true
      var $parent = this.$element.closest('[data-toggle="buttons"]')

      if ($parent.length) {
        var $input = this.$element.find('input')
        if ($input.prop('type') == 'radio') {
          if ($input.prop('checked') && this.$element.hasClass('active')) changed = false
          else $parent.find('.active').removeClass('active')
        }
        if (changed) $input.prop('checked', !this.$element.hasClass('active')).trigger('change')
      }

      if (changed) this.$element.toggleClass('active')
    }


    // BUTTON PLUGIN DEFINITION
    // ========================

    var old = $.fn.wpdkButton

    $.fn.wpdkButton = function (option) {
      return this.each(function () {
        var $this   = $(this)
        var data    = $this.data('wpdk.wpdkButton')
        var options = typeof option == 'object' && option

        if (!data) $this.data('wpdk.wpdkButton', (data = new Button(this, options)))

        if (option == 'toggle') data.toggle()
        else if (option) data.setState(option)
      })
    }

    $.fn.wpdkButton.Constructor = Button


    // BUTTON NO CONFLICT
    // ==================

    $.fn.wpdkButton.noConflict = function () {
      $.fn.wpdkButton = old
      return this
    }


    // BUTTON DATA-API
    // ===============

    $(document).on('click.wpdk.wpdkButton.data-api', '[data-toggle^=wpdkButton]', function (e) {
      var $btn = $(e.target)
      if (!$btn.hasClass('btn')) $btn = $btn.closest('.btn')
      $btn.wpdkButton('toggle')
      e.preventDefault()
    })

  }(jQuery);
}
