(function (jQuery) {
  "use strict";
  jQuery.fn.yprm_accordion = function () {
    return this.each(function () {
      var $this = jQuery(this);

      $this.on('click', '.top', function() {
        if(jQuery(this).parent().hasClass('current')) {
          jQuery(this).parent().removeClass('current').find('.wrap').slideUp(300);
        } else {
          jQuery(this).parent().addClass('current').find('.wrap').slideDown(300).parent().siblings().removeClass('current').find('.wrap').slideUp(300);
        }

        setTimeout(function () {
          jQuery(window).trigger('resize.px.parallax');
        }, 300);
      });
    });
  };

})(jQuery);