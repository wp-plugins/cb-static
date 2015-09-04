/**
 * The script for the meta box functionality.
 *
 * @link              https://github.com/demispatti/cb-static/
 * @since             0.1.0
 * @package           cb_static
 * @subpackage        cb_static/admin/js
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
(function ($) {
    "use strict";

    jQuery(document).ready(function ($) {

        $(".cb-static-wp-color-picker").wpColorPicker();
        $('label[for="cb-static-background-color"]').hide();

        // Sets the initial view for the meta box.
        if ($(".cb-static-background-image-url").attr("src")) {

            //$('label[for="cb-static-background-color"]').show();
            //$(".wp-picker-container").show();
            $(".cb-static-background-image-url").show();
        } else {

            //$('label[for="cb-static-background-color"]').hide();
            //$(".wp-picker-container").hide();
            $(".cb-static-background-image-url").hide();
        }
        if ($("input#cb-static-background-image").val()) {

            $(".cb-static-add-media-text").hide();
            //$('label[for="cb-static-background-color"]').show();
            //$(".wp-picker-container").show();
            $(".cb-static-background-image-url").show();

            $(".cb-static-remove-media, .cb-static-background-image-options, .cb-static-background-image-url").show();
        } else {

            $(".cb-static-add-media-text").show();
            $(".cb-static-remove-media, .cb-static-background-image-options, .cb-static-background-image-url").hide();
        }

        // Remove media button.
        $(".cb-static-remove-media").click(function (j) {

            j.preventDefault();
            $("#cb-static-background-image").val("");
            $(".cb-static-add-media-text").show();
            $(".cb-static-remove-media, .cb-static-background-image-url, .cb-static-background-image-options").hide();
        });

        // Media frame.
        var cb_static_frame;

        // Add media button.
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

})(jQuery);


