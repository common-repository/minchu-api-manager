function popup(elem) {
  elem.fadeIn(300).css("display", "flex");
  //    initSize(elem);
  jQuery("html, body").css({
    "overflow": "hidden"
  });
}
jQuery(function ($) {
  $(".wp_minchu_popup .wp_minchu_mask, .wp_minchu_popup_close").click(function () {
    $(this).parents(".wp_minchu_popup").hide();
    $("html, body").css({
      "overflow": ""
    });
  });

  $(".wp_minchu_popup_box_inner").click(function (e) {
    e.stopPropagation();
  });

  $(window).resize(function () {
    if ($(".wp_minchu_popup_box:visible").length > 0) {
      initSize($(".wp_minchu_popup_box:visible"));
    }
  });
});
