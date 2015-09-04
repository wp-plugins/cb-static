<?php

/**
 * Fired during plugin activation.
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
class cb_static_activator {

    /**
     * The variable that holds the name of the capability which is necessary
     * to interact with this plugin.
     *
     * @since    0.1.0
     * @access   static
     * @var      string $capability The name of the capability.
     */
    public static $capability = 'cb_static_edit';

    /**
     * Fired during activation of the plugin.
     * Adds the capability to edit custom backgrounds to the administrator role.
     * @since    0.1.0
     * @access   static
     * @return   void
     */
    public static function activate() {

        // Gets the administrator role.
        $role = get_role('administrator');

        // If the acting user has admin rights, the capability gets added.
        if( ! empty( $role ) ) {
            $role->add_cap(self::$capability);
        }
    }
}
