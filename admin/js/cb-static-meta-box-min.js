jQuery(document).ready(function ($) {
    $(".cb-static-wp-color-picker").wpColorPicker();
    $('label[for="cb-static-background-color"]').hide();
    if ($(".cb-static-background-image-url").attr("src")) {
        $(".cb-static-background-image-url").show();
    } else {
        $(".cb-static-background-image-url").hide();
    }
    if ($("input#cb-static-background-image").val()) {
        $(".cb-static-add-media-text").hide();
        $(".cb-static-background-image-url").show();
        $(".cb-static-remove-media, .cb-static-background-image-options, .cb-static-background-image-url").show();
    } else {
        $(".cb-static-add-media-text").show();
        $(".cb-static-remove-media, .cb-static-background-image-options, .cb-static-background-image-url").hide();
    }
    $(".cb-static-remove-media").click(function (j) {
        j.preventDefault();
        $("#cb-static-background-image").val("");
        $(".cb-static-add-media-text").show();
        $(".cb-static-remove-media, .cb-static-background-image-url, .cb-static-background-image-options").hide();
    });
    var cb_static_frame;
    $(".cb-static-add-media").click(function (j) {
        j.preventDefault();
        if (cb_static_frame) {
            cb_static_frame.open();
            return;
        }
        cb_static_frame = wp.media.frames.cb_static_frame = wp.media({
            className: "media-frame cb-static-frame",
            frame: "select",
            multiple: false,
            title: cbStaticFrame.title,
            library: {type: "image"},
            button: {text: cbStaticFrame.button}
        });
        cb_static_frame.on("select", function () {
            var media_attachment = cb_static_frame.state().get("selection").first().toJSON();
            $("#cb-static-background-image").val(media_attachment.id);
            $(".cb-static-background-image-url").attr("src", media_attachment.url);
            $(".cb-static-add-media-text").hide();
            $(".cb-static-background-image-url, .cb-static-remove-media, .cb-static-background-image-options").show();
        });
        cb_static_frame.open();
    });
});
