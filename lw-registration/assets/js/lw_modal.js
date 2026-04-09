var lw_modal = function (element) {
  var $html =
    '<div id="lw_model"><div id="lw_model_container"><div id="lw_model_container_inner"><div id="lw_model_container_inner_holder"> <div id="close_lw_model"><i class="fa fa-times"></i></div><div id="lw_model_content"><div id="lw_model_content_inner"></div></div></div></div></div></div>';
  if (jQuery("#lw_model").length > 0) {
    jQuery("#lw_model").remove();
  }
  jQuery("body").prepend($html);
  jQuery("#lw_model_content_inner").append(jQuery(element));
};

jQuery(document).on("click", "#close_lw_model", function () {
  jQuery(this).parents("#lw_model").remove();
  var body = jQuery("body");
  body.removeClass("lw-tv-overlay");
  //if only has one single video add removed iframe back
  // var singleVideo = jQuery("#lw_single_livestream");
  // if (singleVideo.length > 0) {
  //   jQuery(".fluid-width-video-wrapper").each(function () {
  //     var iframe = jQuery(this).attr("data_iframe");
  //     var iframeDom = jQuery(
  //       `<iframe src="${iframe}" frameborder="0" allow="autoplay; fullscreen; picture-in-picture"></iframe>`
  //     );
  //     jQuery(this).append(iframeDom);
  //   });
  //   return;
  // }
  // //add removed iframe back for more than 1 videos
  // jQuery(".live_vimeo_videos .vimeo_container.live").each(function () {
  //   var iframe = jQuery(this).attr("data_iframe");
  //   var iframeDom = jQuery(
  //     `<iframe src="${iframe}" frameborder="0" allow="autoplay; fullscreen; picture-in-picture"></iframe>`
  //   );
  //   jQuery(this).append(iframeDom);
  // });
});

jQuery(document).ready(function () {
  //var dom = jQuery('<p>Sample Model</p >');
  //lw_modal(dom);
});
