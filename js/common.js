jQuery(function($){
  var left = 0;
  $("body").animate({
    opacity: 1
  }, 800);
  //初期表示を１番目の画像に設定
  setMainImg($(".wp_minchu_thumb_img").first());
  $(".wp_minchu_thumb_img").first().attr("data-target", true);
  left = $(".wp_minchu_thumb_img").first().index() * $(".wp_minchu_thumb_img").first().outerWidth(true);

  $(".wp_minchu_thumb_img").click(function () {
    setMainImg($(this));
    changeTarget($(this));
    scrollX($(this).index() * $(this).outerWidth(true), $(this).outerWidth(true));
  });
  if ($(".wp_minchu_thumb_img").length == 1) {
    $(".wp_minchu_arrow_left, .wp_minchu_arrow_right").hide();
  }
  $(".wp_minchu_wall_left, .wp_minchu_arrow_left").click(function () {
    if ($(".wp_minchu_thumb_img").length == 1) {
      return;
    }
    var thumb = $(".wp_minchu_thumb_img[data-target=true]").prev(".wp_minchu_thumb_img");
    if (thumb.length == 0) {
      thumb = $(".wp_minchu_thumb_img").last();
    }
    setMainImg(thumb);
    changeTarget(thumb);
    scrollX(thumb.index() * thumb.outerWidth(true), thumb.outerWidth(true));
  });
  $(".wp_minchu_wall_right, .wp_minchu_arrow_right").click(function () {
    if ($(".wp_minchu_thumb_img").length == 1) {
      return;
    }
    var thumb = $(".wp_minchu_thumb_img[data-target=true]").next(".wp_minchu_thumb_img");
    if (thumb.length == 0) {
      thumb = $(".wp_minchu_thumb_img").first();
    }
    setMainImg(thumb);
    changeTarget(thumb);
    scrollX(thumb.index() * thumb.outerWidth(true), thumb.outerWidth(true));
  });

  function scrollX(newLeft, w) {
    if (left == newLeft) {
      return;
    } else if (left < newLeft) {
      $(".wp_minchu_thumbs_inner").animate({
        scrollLeft: newLeft
      }, 300);
      left = newLeft;
    } else if (left > newLeft) {
      $(".wp_minchu_thumbs_inner").animate({
        scrollLeft: newLeft
      }, 300);
      left = newLeft;
    }
  }

  function setMainImg(src) {
    $(".wp_minchu_car_img_main").attr("src", src.find("img").attr("src"));
    var alt = src.find("img").attr("alt");
    if (alt == "") {
      $(".wp_minchu_img_cmt").hide();
    } else {
      $(".wp_minchu_img_cmt").show();
      $(".wp_minchu_img_cmt").text(src.find("img").attr("alt"));
    }
  }

  function changeTarget(thumb) {
    $(".wp_minchu_thumb_img").attr("data-target", false);
    thumb.attr("data-target", true);
  }
  $(".wp_minchu_section_vr .wp_minchu_vr_img").click(function () {
    'use strict';
    var src = $(this).find("img").attr("src");
    $("#wp_minchu_info").show();
    $(".wp_minchu_popup_img").empty().createThetaViewer(src);
    popup($("#wp_minchu_vrPopup"));
  });
});
