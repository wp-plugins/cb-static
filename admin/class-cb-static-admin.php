<?php

/**
 * The admin part of the plugin.
 * @link              https://github.com/demispatti/cb-static/
 * @since             0.1.0
 * @package           cb_static
 * @subpackage        cb_static/admin
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_static_admin {

    /**
     * The unique identifier of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    private $plugin_name;

    /**
     * The unique identifier of this plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string $plugin_domain The string used to uniquely identify this plugin.
     */
    private $plugin_domain;

    /**
     * The current version of the plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      string $plugin_version The current version of the plugin.
     */
    private $plugin_version;

    /**
     * The loader that's responsible for maintaining and registering all hooks that power the plugin.
     *
     * @since    0.1.0
     * @access   private
     * @var      object $loader Maintains and registers all hooks for the plugin.
     */
    private $loader;

    /**
     * Kicks off the admin part of the plugin.
     *
     * 1. Loads the dependencies the admin part relies on.
     * 2. Defines post type support.
     * 3. Defines the functionality for the custom backgrounds.
     * 4. Checks if the theme supports custom backgrounds and the user's permission to interact with this plugin.
     * 5. Defines the meta box.
     * 6. Loads the meta informations for the plugin meta row.
     *
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

        $this->load_dependencies();
        $this->define_post_type_support();
        $this->define_functionality();
    }

    /**
     * Loads the initial files needed by the admin part of the plugin and assigns the loader object.
     *
     * The class responsible for orchestrating the actions and filters of the core plugin.
     * The class responsible for the post type support.
     * The class responsible for the main functionality.
     * The class responsible for loading and saving post data.
     * The class responsible for the meta box.
     *
     * @since    0.1.0
     * @access   public
     * @return   void
     */
    public function load_dependencies() {

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-cb-static-loader.php';

        require_once plugin_dir_path(dirname(__FILE__)) . "admin/includes/class-cb-static-post-type-support.php";

        require_once plugin_dir_path(dirname(__FILE__)) . "admin/includes/class-cb-static-custom-background-support.php";

        require_once plugin_dir_path(dirname(__FILE__)) . "admin/includes/class-cb-static-meta-box.php";

        $this->loader = new cb_static_loader();
    }


    public function enqueue_styles( $hook_suffix ) {

        if( ! in_array($hook_suffix, array( 'post-new.php', 'post.php' )) || ! current_user_can('cb_static_edit') ) {
            return;
        }

        wp_enqueue_style('wp-color-picker');

        wp_enqueue_style(
            $this->plugin_name . '-admin-css',
            plugin_dir_url(__FILE__) . 'css/admin.css',
            array(),
            $this->plugin_version,
            'all'
        );
    }

    /**
     * Registers and enqueues the script for the admin part of the plugin.
     *
     * But first we check if we're in the right spot and
     * if the current user owns the capability needed to interact with this plugin.
     *
     * @since    0.1.0
     * @access   public
     * @return   void
     *
     * @param    $hook_suffix
     */
    public function enqueue_scripts( $hook_suffix ) {

        if( ! in_array($hook_suffix, array( 'post-new.php', 'post.php' )) || ! current_user_can('cb_static_edit') ) {
            return;
        }

        wp_enqueue_script('wp-color-picker');

        wp_enqueue_script('media-views');

        $admin_js_file = $this->plugin_name . '-meta-box-js';

        wp_register_script($admin_js_file, plugin_dir_url(__FILE__) . 'js/cb-static-meta-box.js', array( 'jquery', 'wp-color-picker', 'media-views' ), $this->plugin_version, false);
        wp_enqueue_script($admin_js_file);

        wp_localize_script($admin_js_file, 'cbStaticFrame', array(

                'title' => __('Set Background Image', $this->plugin_domain),
                'button' => __('Set background image', $this->plugin_domain)
            )
        );
    }

    /**
     * Defines the functionality for editing the custom background -
     * if the current user has the capability to do so.
     *
     * @since    0.1.0
     * @access   public
     * @return   void
     */
    public function add_editing_functionality() {

        if( ! current_user_can('cb_static_edit') ) {
            return;
        }

        $this->define_meta_box();
    }

    /**
     * Registers the action to execute on the object regarding the post type support.
     *
     * @since    0.1.0
     * @access   private
     * @return   void
     */
    private function define_post_type_support() {

        $post_type_support = new cb_static_post_type_support($this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_version());
        $post_type_support->enable_post_type_support();

        $this->loader->add_action('init', $post_type_support, 'enable_post_type_support');
    }

    /**
     * Registers the initial function to execute on the object.
     *
     * of the plugin.
     * @since    0.1.0
     * @access   private
     * @return   void
     */
    private function define_functionality() {

        $functionality = new cb_static_functionality($this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_version());

        $this->loader->add_action('after_setup_theme', $functionality, 'add_theme_support', 95);
    }

    /**
     * Registers the meta box related features to execute them on the just created object.
     * @since    0.1.0
     * @access   private
     * @return   void
     */
    private function define_meta_box() {

        $metabox = new cb_static_meta_box($this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_version());

        $this->loader->add_action('add_meta_boxes', $metabox, 'add_meta_boxes');
    }

    /**
     * Adds support, rating, and donation links to the plugin row meta on the plugins admin screen.
     *
     * @since    0.1.0
     * @access   public
     * @return   array
     *
     * @param    array $meta
     * @param    string $file
     */
    public function plugin_row_meta( $meta, $file ) {

        $plugin = plugin_basename('cb-static/cb-static.php');

        if( $file == $plugin ) {
            $meta[] = '<a href="https://github.com/demispatti/cb-static" target="_blank">' . __('Plugin support', $this->plugin_domain) . '</a>';
            $meta[] = '<a href="http://wordpress.org/plugins/cb-static" target="_blank">' . __('Rate plugin', $this->plugin_domain) . '</a>';
            //$meta[] = '<a href="http://demispatti.ch/plugins" target="_blank">' . __('Donate', $this->plugin_domain) . '</a>';
        }

        return $meta;
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    0.1.0
     * @access   public
     * @return   void
     */
    public function run() {

        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     0.1.0
     * @access    public
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name() {

        return $this->plugin_name;
    }

    /**
     * The domain of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     0.1.0
     * @access    public
     * @return    string    The domain of the plugin.
     */
    public function get_plugin_domain() {

        return $this->plugin_domain;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     0.1.0
     * @access    public
     * @return    string    The version number of the plugin.
     */
    public function get_plugin_version() {

        return $this->plugin_version;
    }
}
