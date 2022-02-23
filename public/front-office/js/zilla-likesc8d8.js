jQuery(function (jQuery) {

  jQuery('.zilla-likes').on('click', function () {
    var link = jQuery(this);
    if (link.hasClass('active')) return false;

    var id = jQuery(this).attr('id'),
      postfix = link.attr('data-postfix');

    jQuery.ajax({
      type: 'POST',
      url: zilla_likes.ajaxurl,
      data: {
        action: 'zilla-likes',
        likes_id: id,
        postfix: postfix,
      },
      xhrFields: {
        withCredentials: true,
      },
      success: function (data) {
        link.addClass('active').attr('title', 'You already like this').find('span').html(data);
      },
    });

    return false;
  });

  if (jQuery('body.ajax-zilla-likes').length) {
    jQuery('.zilla-likes').each(function () {
      var id = jQuery(this).attr('id');
      jQuery(this).load(zilla_likes.ajaxurl, {
        action: 'zilla-likes',
        post_id: id,
      });
    });
  }
});