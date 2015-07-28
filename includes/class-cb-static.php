<?php

/**
 * The core class of the plugin.
 *
 * @link              https://github.com/demispatti/cb-static/
 * @since             0.1.0
 * @package           cb_static
 * @subpackage        cb_static/includes
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */
class cb_static {

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
	 * @var      cb_static_loader $loader Maintains and registers all hooks for the plugin.
	 */
	private $loader;

	/**
	 * 1. Defines the plugin's name, domain and version, and loads its basic dependencies.
	 * 2. Instanciates and assigns the loader.
	 * 3. Loads the language files.
	 * 4. Loads the admin part of the plugin.
	 *
	 * @since  0.1.0
	 * @access public
	 */
	public function __construct() {

		$this->plugin_name    = 'cb-static';
		$this->plugin_domain  = $this->get_plugin_domain();
		$this->plugin_version = '0.1.0';

		$this->load_dependencies();
		$this->set_i18n();
		$this->define_admin_hooks();
	}

	/**
	 * Loads the initial files needed by the plugin and assigns the loader object.
	 *
	 * The class responsible for orchestrating the hooks of the plugin.
	 * The class responsible for defining the internationalization functionality of the plugin.
	 * The class that defines the admin part of the plugin.
	 *
	 * @since  0.1.0
	 * @access private
	 * @return void
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cb-static-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cb-static-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . "admin/class-cb-static-admin.php";

		$this->loader = new cb_static_loader();
	}

	/**
	 * Loads the translation files.
	 *
	 * @since  0.1.0
	 * @access private
	 * @return void
	 */
	private function set_i18n() {

		$i18n = new cb_static_i18n();

		$i18n->set_domain( $this->get_plugin_domain() );

		$this->loader->add_action( 'plugins_loaded', $i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Instanciates the admin object and registers the hooks that shall be executed on it.
	 *
	 * @since    0.1.0
	 * @access   private
	 * @return   void
	 */
	private function define_admin_hooks() {

		$admin = new cb_static_admin( $this->get_plugin_name(), $this->get_plugin_domain(), $this->get_plugin_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $admin, 'add_editing_functionality' );
		// Prepared, Website follows soon
		$this->loader->add_action( 'plugin_row_meta', $admin, 'plugin_row_meta', 10, 2 );
	}

	/**
	 * Runs the loader to execute all of the hooks with WordPress.
	 *
	 * @since    0.1.0
	 * @access   public
	 * @return   void
	 */
	public function run() {

		$this->loader->run();
	}

	/**
	 * Retrieves the name of the plugin used to uniquely identify it within the context of
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
	 * Retrieves the domain of the plugin used to uniquely identify it within the context of
	 * WordPress and to abstract internationalization functionality.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string    The domain of the plugin.
	 */
	public function get_plugin_domain() {

		return $this->get_plugin_name();
	}

	/**
	 * Retrieves the version number of the plugin.
	 *
	 * @since     0.1.0
	 * @access    public
	 * @return    string    The version number of the plugin.
	 */
	public function get_plugin_version() {

		return $this->plugin_version;
	}
}
