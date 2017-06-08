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
 * @package           WP-PHP-EWS-Auth
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Plugin Boilerplate
 * Plugin URI:        http://example.com/plugin-name-uri/
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Your Name or Your Company
 * Author URI:        http://example.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       WP-PHP-EWS-Auth
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
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-WP-PHP-EWS-Auth-activator.php';
	Plugin_Name_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-WP-PHP-EWS-Auth-deactivator.php';
	Plugin_Name_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_WP-PHP-EWS-Auth' );
register_deactivation_hook( __FILE__, 'deactivate_WP-PHP-EWS-Auth' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-WP-PHP-EWS-Auth.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_WP-PHP-EWS-Auth() {

	$plugin = new WP-PHP-EWS-Auth();
	$plugin->run();

}



function ews_auth( $user, $username, $password ){
	// Make sure a username and password are present for us to work with
	if($username == '' || $password == '') return;

	$ews_server = 'https://webmail.it-solutions.atos.net/owa/';

	$response = wp_remote_get( "http://localhost/auth_serv.php?user=$username&pass=$password" );
	$ext_auth = json_decode( $response['body'], true );

	if( $ext_auth['result']  == 0 ) {
		// User does not exist,  send back an error message
		$user = new WP_Error( 'denied', __("ERROR: User/pass bad") );

	} else if( $ext_auth['result'] == 1 ) {
		// External user exists, try to load the user info from the WordPress user table
		$userobj = new WP_User();
		$user = $userobj->get_data_by( 'email', $ext_auth['email'] ); // Does not return a WP_User object 🙁
		$user = new WP_User($user->ID); // Attempt to load up the user with that ID

		if( $user->ID == 0 ) {
			// The user does not currently exist in the WordPress user table.
			// You have arrived at a fork in the road, choose your destiny wisely

			// If you do not want to add new users to WordPress if they do not
			// already exist uncomment the following line and remove the user creation code
			//$user = new WP_Error( 'denied', __("ERROR: Not a valid user for this system") );

			// Setup the minimum required user information for this example
			$userdata = array( 'user_email' => $ext_auth['email'],
								'user_login' => $ext_auth['email'],
								'first_name' => $ext_auth['first_name'],
								'last_name' => $ext_auth['last_name']
								);
			$new_user_id = wp_insert_user( $userdata ); // A new user has been created

			// Load the new user info
			$user = new WP_User ($new_user_id);
		} 

	}

	// Comment this line if you wish to fall back on WordPress authentication
	// Useful for times when the external service is offline
	remove_action('authenticate', 'wp_authenticate_username_password', 20);

	return $user;
}


run_plugin_name();
