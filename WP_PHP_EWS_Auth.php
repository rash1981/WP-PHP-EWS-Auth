<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             1.0.0
 * @package           WP_PHP_EWS_Auth
 *
 * @wordpress-plugin
 * Plugin Name:       Exchange Web Services Authentication 
 * Plugin URI:        https://www.polymessa.com/WP_PHP_EWS_Auth/
 * Description:       Allow existing EWS users to log in to WordPress without the need of creating an extra account.
 * Version:           1.0.0
 * Author:            Wouter S. Schuur
 * Author URI:        https://www.polymessa.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       WP_PHP_EWS_Auth
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-WP_PHP_EWS_Auth-activator.php';
	Plugin_Name_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-WP_PHP_EWS_Auth-deactivator.php';
	Plugin_Name_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_WP_PHP_EWS_Auth' );
register_deactivation_hook( __FILE__, 'deactivate_WP_PHP_EWS_Auth' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-WP_PHP_EWS_Auth.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_WP_PHP_EWS_Auth() {

	$plugin = new WP_PHP_EWS_Auth();
	$plugin->run();

}

run_WP_PHP_EWS_Auth();
