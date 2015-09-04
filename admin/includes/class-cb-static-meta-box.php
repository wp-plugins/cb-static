<?php

/**
 * Defines and displays the meta box.
 *
 * Since it is so perfect and we're talking GPL-2.0+ -Code,
 * this component is <strong>oh so</strong> based on Justin Tadlocks "Custom Backgrounds Extended" meta box script.
 * I [imagine-a-really-big-fa-heart-icon-here] it.
 *
 * @link              https://github.com/demispatti/cb-static/
 * @since             0.1.0
 * @package           cb_static
 * @subpackage        cb_static/admin/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_static_meta_box {

    /**
     * The ID of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The domain of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string $plugin_domain The domain of this plugin.
     */
    private $plugin_domain;

    /**
     * The version of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string $plugin_version The current version of this plugin.
     */
    private $plugin_version;

    // White-listed values.
    private $allowed_repeat = array( 'no-repeat', 'repeat', 'repeat-x', 'repeat-y' );
    private $allowed_position_x = array( 'left', 'right', 'center' );
    private $allowed_position_y = array( 'top', 'bottom', 'center' );
    private $allowed_attachment = array( 'scroll', 'fixed' );

    /**
     * Whether the theme has a custom backround callback for 'wp_head' output.
     *
     * @since  0.1.0
     * @access public
     * @var    bool
     */
    public $theme_has_callback = false;

    /**
     * Kicks off the meta box.
     *
     * 1. Adds the meta box if the user is on an edit screen and has cap.
     * 2. Displays it.
     * @since    0.1.0
     * @access   public
     *
     * @param    string $plugin_name
     * @param    string $plugin_domain
     * @param    string $plugin_version
     */
    public function __construct( $plugin_name, $plugin_domain, $plugin_version ) {

        $this->plugin_name = $plugin_name;
        $this->plugin_domain = $plugin_domain;
        $this->plugin_version = $plugin_version;

        /* If the current user can't edit custom backgrounds, bail early. */
        if( ! current_user_can('cb_static_edit') && ! current_user_can('edit_theme_options') ) {
            return;
        }

        $this->init();
    }

    /**
     * Register all necessary hooks for this part of the plugin to work with WordPress.
     *
     * @since    0.1.0
     * @access   public
     * @return   void
     */
    private function init() {

        /* Only load on the edit post screen. */
        add_action('load-post.php', array( $this, 'load_post' ));
        add_action('load-post-new.php', array( $this, 'load_post' ));
    }

    /**
     * Add actions for the edit post screen.
     *
     * @since  0.1.0
     * @access public
     */
    public function load_post() {

        $screen = get_current_screen();

        /* If the current theme doesn't support custom backgrounds, bail. */
        if( ! current_theme_supports('custom-background') || ! post_type_supports($screen->post_type, 'custom-background') ) {
            return;
        }

        /* Get the 'wp_head' callback. */
        $wp_head_callback = get_theme_support('custom-background', 'wp-head-callback');

        /* Checks if the theme has set up a custom callback. */
        $this->theme_has_callback = empty( $wp_head_callback ) || '_custom_background_cb' === $wp_head_callback ? false : true;

        // Add the meta box
        add_action('add_meta_boxes', array( &$this, 'add_meta_boxes' ), 5);

        // Save meta data.
        add_action('save_post', array( &$this, 'save_post' ), 10, 2);

    }

    /**
     * Add custom meta boxes.
     *
     * @since  0.1.0
     * @access public
     *
     * @param  string $post_type
     *
     * @return void
     */
    public function add_meta_boxes( $post_type ) {

        add_meta_box($this->plugin_name . '-meta-box', __('cb Static Background', $this->plugin_domain), array( &$this, 'display_meta_box' ), $post_type, 'side', 'core');
    }

    /**
     * Display the custom background meta box.
     *
     * @since  0.1.0
     * @access public
     *
     * @param  object $post
     *
     * @return void
     */
    public function display_meta_box( $post ) {

        // Get the background color.
        $color = trim(get_post_meta($post->ID, '_cb_static_color', true), '#');

        // Get the background image attachment ID.
        $attachment_id = get_post_meta($post->ID, '_cb_static_image_id', true);

        // If an attachment ID was found, get the image source.
        if( ! empty( $attachment_id ) ) {
            $image = wp_get_attachment_image_src(absint($attachment_id), 'post-thumbnail');
        }

        // Get the image URL.
        $url = ! empty( $image ) && isset( $image[0] ) ? $image[0] : '';

        // Get the background image settings.
        $repeat = get_post_meta($post->ID, '_cb_static_repeat', true);
        $position_x = get_post_meta($post->ID, '_cb_static_position_x', true);
        $position_y = get_post_meta($post->ID, '_cb_static_position_y', true);
        $attachment = get_post_meta($post->ID, '_cb_static_attachment', true);

        // Get theme mods.
        $mod_repeat = get_theme_mod('background_repeat', 'repeat');
        $mod_position_x = get_theme_mod('background_position_x', 'left');
        $mod_position_y = get_theme_mod('background_position_y', 'top');
        $mod_attachment = get_theme_mod('background_attachment', 'scroll');

        /**
         * Make sure values are set for the image options.  This should always be set so that we can
         * be sure that the user's background image overwrites the default/WP custom background settings.
         * With one theme, this doesn't matter, but we need to make sure that the background stays
         * consistent between different themes and different WP custom background settings.  The data
         * will only be stored if the user selects a background image.
         */
        $repeat = ! empty( $repeat ) ? $repeat : $mod_repeat;
        $position_x = ! empty( $position_x ) ? $position_x : $mod_position_x;
        $position_y = ! empty( $position_y ) ? $position_y : $mod_position_y;
        $attachment = ! empty( $attachment ) ? $attachment : $mod_attachment;

        // Set up an array of allowed values for the repeat option.
        $repeat_options = array(
            'no-repeat' => __('no repeat', $this->plugin_domain),
            'repeat' => __('repeat', $this->plugin_domain),
            'repeat-x' => __('repeat horizontally', $this->plugin_domain),
            'repeat-y' => __('repeat vertically', $this->plugin_domain)
        );

        // Set up an array of allowed values for the position-x option.
        $position_x_options = array(
            'left' => __('left', $this->plugin_domain),
            'center' => __('center', $this->plugin_domain),
            'right' => __('right', $this->plugin_domain)
        );

        // Set up an array of allowed values for the position-x option.
        $position_y_options = array(
            'top' => __('top', $this->plugin_domain),
            'center' => __('center', $this->plugin_domain),
            'bottom' => __('bottom', $this->plugin_domain)
        );

        // Set up an array of allowed values for the attachment option.
        $attachment_options = array(
            'fixed' => __('fixed', $this->plugin_domain),
            'scroll' => __('scroll', $this->plugin_domain)
        ); ?>

        <!-- Begin hidden fields. -->
        <?php wp_nonce_field('cb_static_nonce_field', 'cb_static_nonce'); ?>
        <input type="hidden" name="cb-static-background-image" id="cb-static-background-image"
               value="<?php echo esc_attr($attachment_id); ?>"/>
        <!-- End hidden fields. -->

        <!-- Begin background color. -->
        <p>
            <label for="cb-static-background-color"><?php _e('Background Color', $this->plugin_domain); ?></label>
            <input type="text" name="cb-static-background-color" id="cb-static-backround-color" class="cb-static-wp-color-picker" value="#<?php echo esc_attr($color); ?>" />
        </p>
        <!-- End background color. -->

        <!-- Begin background image. -->
        <p>
            <a href="#" class="cb-static-add-media cb-static-add-media-img"><img class="cb-static-background-image-url"
                                                                                 src="<?php echo esc_url($url); ?>"
                                                                                 style="max-width: 100%; max-height: 200px; display: block;"/></a>
            <a href="#"
               class="cb-static-add-media cb-static-add-media-text"><?php _e('Set background image', $this->plugin_domain); ?></a>
            <a href="#" class="cb-static-remove-media"><?php _e('Remove background image', $this->plugin_domain); ?></a>
        </p>
        <!-- End background image. -->

        <!-- Begin background image options -->
        <div class="cb-static-background-image-options">

            <p>
                <label for="cb-static-background-repeat"><?php _e('Repeat', $this->plugin_domain); ?></label>
                <select class="widefat" name="cb-static-background-repeat" id="cb-static-background-repeat">
                    <?php foreach( $repeat_options as $option => $label ) { ?>
                        <option
                            value="<?php echo esc_attr($option); ?>" <?php selected($repeat, $option); ?> ><?php echo esc_html($label); ?></option>
                    <?php } ?>
                </select>
            </p>

            <p>
                <label
                    for="cb-static-background-position-x"><?php _e('Horizontal Position', $this->plugin_domain); ?></label>
                <select class="widefat" name="cb-static-background-position-x" id="cb-static-background-position-x">
                    <?php foreach( $position_x_options as $option => $label ) { ?>
                        <option
                            value="<?php echo esc_attr($option); ?>" <?php selected($position_x, $option); ?> ><?php echo esc_html($label); ?></option>
                    <?php } ?>
                </select>
            </p>
            <p>
                <label
                    for="cb-static-background-position-y"><?php _e('Vertical Position', $this->plugin_domain); ?></label>
                <select class="widefat" name="cb-static-background-position-y" id="cb-static-background-position-y">
                    <?php foreach( $position_y_options as $option => $label ) { ?>
                        <option
                            value="<?php echo esc_attr($option); ?>" <?php selected($position_y, $option); ?> ><?php echo esc_html($label); ?></option>
                    <?php } ?>
                </select>
            </p>
            <p>
                <label for="cb-static-background-attachment"><?php _e('Attachment', $this->plugin_domain); ?></label>
                <select class="widefat" name="cb-static-background-attachment" id="cb-static-background-attachment">
                    <?php foreach( $attachment_options as $option => $label ) { ?>
                        <option
                            value="<?php echo esc_attr($option); ?>" <?php selected($attachment, $option); ?> ><?php echo esc_html($label); ?></option>
                    <?php } ?>
                </select>
            </p>

        </div>
        <!-- End background image options. -->

    <?php }

    /**
     * Saves the data from the custom backgrounds meta box.
     *
     * @since  0.1.0
     * @access public
     * @return void | $post_id
     *
     * @param  int $post_id
     * @param  object $post
     */
    public function save_post( $post_id, $post ) {

        // Verify the nonce.
        if( ! isset( $_POST['cb_static_nonce'] ) || ! wp_verify_nonce($_POST['cb_static_nonce'], 'cb_static_nonce_field') ) {

            return;
        }

        // Get the post type object.
        $post_type = get_post_type_object($post->post_type);

        // Check if the current user has permission to edit the post.
        if( ! current_user_can($post_type->cap->edit_post, $post_id) ) {

            return $post_id;
        }

        // Don't save if the post is only a revision.
        if( 'revision' == $post->post_type ) {

            return;
        }

        // Sanitize the value for the color.
        $color = preg_replace('/[^0-9a-fA-F]/', '', $_POST['cb-static-background-color']);

        // Make sure the background image attachment ID is an absolute integer.
        $image_id = absint($_POST['cb-static-background-image']);

        // If there's not an image ID, set background image options to an empty string.
        if( 0 >= $image_id ) {

            $repeat = $position_x = $position_y = $attachment = '';
            // If there is an image ID, validate the background image options.
        } else {

            // Add the image to the pool of uploaded background images for this theme.
            if( ! empty( $image_id ) ) {

                $is_custom_header = get_post_meta($image_id, '_wp_attachment_is_custom_background', true);

                if( $is_custom_header !== get_stylesheet() ) {
                    update_post_meta($image_id, '_wp_attachment_is_custom_background', get_stylesheet());
                }
            }

            // Make sure the values have been white-listed. Otherwise, set an empty string.
            $repeat = in_array($_POST['cb-static-background-repeat'], $this->allowed_repeat) ? $_POST['cb-static-background-repeat'] : '';
            $position_x = in_array($_POST['cb-static-background-position-x'], $this->allowed_position_x) ? $_POST['cb-static-background-position-x'] : '';
            $position_y = in_array($_POST['cb-static-background-position-y'], $this->allowed_position_y) ? $_POST['cb-static-background-position-y'] : '';
            $attachment = in_array($_POST['cb-static-background-attachment'], $this->allowed_attachment) ? $_POST['cb-static-background-attachment'] : '';
        }

        // Set up an array of meta keys and values.
        $meta = array(
            '_cb_static_color' => $color,
            '_cb_static_image_id' => $image_id,
            '_cb_static_repeat' => $repeat,
            '_cb_static_position_x' => $position_x,
            '_cb_static_position_y' => $position_y,
            '_cb_static_attachment' => $attachment,
        );

        // Loop through the meta array and add, update, or delete the post metadata.
        foreach( $meta as $meta_key => $new_meta_value ) {

            // Get the meta value of the custom field key
            $meta_value = get_post_meta($post_id, $meta_key, true);

            // If a new meta value was added and there was no previous value, add it.
            if( $new_meta_value && '' == $meta_value ) {
                add_post_meta($post_id, $meta_key, $new_meta_value, true);
            } // If the new meta value does not match the old value, update it.
            elseif( $new_meta_value && $new_meta_value != $meta_value ) {
                update_post_meta($post_id, $meta_key, $new_meta_value);
            } // If there is no new meta value but an old value exists, delete it.
            elseif( '' == $new_meta_value && $meta_value ) {
                delete_post_meta($post_id, $meta_key, $meta_value);
            }
        }
    }

}
