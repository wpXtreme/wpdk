/**
 * wpdkPopover
 *
 * @class           wpdkPopover
 * @author          =undo= <info@wpxtre.me>
 * @copyright       Copyright (C) 2012-2014 wpXtreme Inc. All Rights Reserved.
 * @date            2014-02-08
 * @version         3.1.0
 * @note            Base on bootstrap: popover.js v3.1.0
 *
 * - Rename namespace popover with wpdkPopover
 * - Rename namespace "bs" with "wpdk"
 * - Rename namespace "bs.popover" with "wpdk.popover"
 * - Rename class `.popover` in `.wpdk-popover`
 * - Rename class `.arrow` in `.popover-arrow`
 *
 */

// Check for jQuery
if (typeof jQuery === 'undefined') { throw new Error('Bootstrap\'s JavaScript requires jQuery') }

// Check for WPDK ToolTip
if( typeof( jQuery.fn.wpdkTooltip ) === 'undefined' ) { throw new Error('WPDK Popover\'s JavaScript requires WPDK ToolTip') }

// One time
if( typeof( jQuery.fn.wpdkPopover ) === 'undefined' ) {

  /* ========================================================================
   * Bootstrap: popover.js v3.1.0
   * http://getbootstrap.com/javascript/#popovers
   * ========================================================================
   * Copyright 2011-2014 Twitter, Inc.
   * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
   * ======================================================================== */

  +function ($) {
    'use strict';

    // POPOVER PUBLIC CLASS DEFINITION
    // ===============================

    var Popover = function (element, options) {
      this.init('wpdkPopover', element, options)
    }

    // This never happen
    // if (!$.fn.tooltip) throw new Error('Popover requires tooltip.js')

    Popover.DEFAULTS = $.extend({}, $.fn.wpdkTooltip.Constructor.DEFAULTS, {
      placement: 'right',
      trigger: 'click',
      content: '',
      template: '<div class="wpdk-popover"><div class="popover-arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>'
    })


    // NOTE: POPOVER EXTENDS tooltip.js
    // ================================

    Popover.prototype = $.extend({}, $.fn.wpdkTooltip.Constructor.prototype)

    Popover.prototype.constructor = Popover

    Popover.prototype.getDefaults = function () {
      return Popover.DEFAULTS
    }

    Popover.prototype.setContent = function () {
      var $tip    = this.tip()
      var title   = this.getTitle()
      var content = this.getContent()

      $tip.find('.popover-title')[this.options.html ? 'html' : 'text'](title)
      $tip.find('.popover-content')[ // we use append for html objects to maintain js events
        this.options.html ? (typeof content == 'string' ? 'html' : 'append') : 'text'
      ](content)

      $tip.removeClass('fade top bottom left right in')

      // IE8 doesn't accept hiding via the `:empty` pseudo selector, we have to do
      // this manually by checking the contents.
      if (!$tip.find('.popover-title').html()) $tip.find('.popover-title').hide()
    }

    Popover.prototype.hasContent = function () {
      return this.getTitle() || this.getContent()
    }

    Popover.prototype.getContent = function () {
      var $e = this.$element
      var o  = this.options

      return $e.attr('data-content')
        || (typeof o.content == 'function' ?
              o.content.call($e[0]) :
              o.content)
    }

    Popover.prototype.arrow = function () {
      return this.$arrow = this.$arrow || this.tip().find('.popover-arrow')
    }

    Popover.prototype.tip = function () {
      if (!this.$tip) this.$tip = $(this.options.template)
      return this.$tip
    }


    // POPOVER PLUGIN DEFINITION
    // =========================

    // WPDK change namespace
    var old = $.fn.wpdkPopover

    // WPDK change namespace
    $.fn.wpdkPopover = function (option) {
      return this.each(function () {
        var $this   = $(this)
        var data    = $this.data('wpdk.wpdkPopover')
        var options = typeof option == 'object' && option

        if (!data && option == 'destroy') return
        if (!data) $this.data('wpdk.wpdkPopover', (data = new Popover(this, options)))
        if (typeof option == 'string') data[option]()
      })
    }

    // WPDK change namespace
    $.fn.wpdkPopover.Constructor = Popover


    // POPOVER NO CONFLICT
    // ===================

    // WPDK change namespace
    $.fn.wpdkPopover.noConflict = function () {
      $.fn.wpdkPopover = old
      return this
    }

    // Auto init
    $( '.wpdk-has-popover' ).wpdkPopover();

  }(jQuery);

}
