<?php

/**
 * The plugin bootstrap file.

 * This is a plugin for custom backgrounds on single pages.
 * It requires your theme to support the <a href="http://codex.wordpress.org/Custom_Backgrounds" target="_blank">custom-background</a> feature.
 * Have Fun!

 * in memoriam of Fry and Leela ( 1999 to 2013 ) ;-)

 * Built with Tom McFarlin's WordPress Plugin Boilerplate in mind -
 * which now is maintained by Devin Vinson.

 * https://github.com/DevinVinson/WordPress-Plugin-Boilerplate

 *
 * @link              https://github.com/demispatti/cb-static
 * @since             0.1.0
 * @package           cb_static

 * @wordpress-plugin
 * Plugin Name:       cbStatic
 * Contributors:      OneMoreNerd
 * Plugin URI:        https://github.com/demispatti/cb-static
 * Description:       Allows users to create <a href="http://codex.wordpress.org/Custom_Backgrounds">custom backgrounds</a> for single posts and pages. It requires your theme to support the WordPress <code>custom-background</code> feature.
 * Author:            Demis Patti <demis@demispatti.ch>
 * Author URI:        http://demispatti.ch
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cb-static
 * Domain Path:       /languages
 */

/**
 * If this file is called directly, abort.
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-cb-static-activator.php
 *
 * @since    0.1.0
 * @return   void
 */
function activate_cb_static() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cb-static-activator.php';

	cb_static_activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-cb-static-deactivator.php
 *
 * @since    0.1.0
 * @return   void
 */
function deactivate_cb_static() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cb-static-deactivator.php';

	cb_static_deactivator::deactivate();
}

/**
 * Register the activation and deactivation functionality of the plugin.
 *
 * @since    0.1.0
 */
register_activation_hook( __FILE__, 'activate_cb_static' );
register_deactivation_hook( __FILE__, 'deactivate_cb_static' );

/**
 * Include the core plugin class.
 *
 * @since    0.1.0
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cb-static.php';

/**
 * Runs the plugin.
 *
 * @since    0.1.0
 * @return   void
 */
function run_cb_static() {

	$plugin = new cb_static();

	$plugin->run();
}

run_cb_static();
