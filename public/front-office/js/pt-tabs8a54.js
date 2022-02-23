(function (jQuery) {
  "use strict";
  jQuery.fn.pt_tabs = function () {
    return this.each(function () {
      var $tabs = jQuery(this),
        $tabs_head = $tabs.find('.tb-buttons'),
        $tab_content = $tabs.find('.tb-wrap .wrap');

      function set_tab(index) {
        $tabs_head.find('.button').eq(index).addClass('current').siblings().removeClass('current');
        $tab_content.eq(index).children('.tb-m-button').addClass('current').parent().siblings().children('.tb-m-button').removeClass('current');
        $tab_content.eq(index).find('.wrap-inner').slideDown().parent().siblings().find('.wrap-inner').slideUp();

        if ($tabs.find('.isotope').length > 0) {
          $tabs.find('.isotope').isotope();
        }
        jQuery(window).trigger('resize').trigger('scroll');

        setTimeout(function () {
          jQuery(window).trigger('resize').trigger('scroll');
        }, 500);
      }

      $tabs_head.on('click', '.button:not(.current)', function () {
        set_tab(jQuery(this).index());
      });

      $tab_content.on('click', '.tb-m-button:not(.current)', function () {
        set_tab(jQuery(this).parent().index());
      });

      set_tab(0);
    });
  };

})(jQuery);