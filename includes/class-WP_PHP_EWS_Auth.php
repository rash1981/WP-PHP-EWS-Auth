<?php

use \jamesiarmes\PhpEws\Client;

use \jamesiarmes\PhpEws\Request\GetUserAvailabilityRequestType;
use \jamesiarmes\PhpEws\Request\GetUserConfigurationType;

use \jamesiarmes\PhpEws\ArrayType\ArrayOfMailboxData;
use \jamesiarmes\PhpEws\Enumeration\SuggestionQuality;

use \jamesiarmes\PhpEws\Type\Duration;
use \jamesiarmes\PhpEws\Type\EmailAddressType;
use \jamesiarmes\PhpEws\Type\MailboxData;
use \jamesiarmes\PhpEws\Type\SuggestionsViewOptionsType;


use \jamesiarmes\PhpEws\Enumeration\ResponseClassType;
use \jamesiarmes\PhpEws\Enumeration\DistinguishedFolderIdNameType;
use \jamesiarmes\PhpEws\Enumeration\UserConfigurationPropertyType;

use \jamesiarmes\PhpEws\Type\DistinguishedFolderIdType;
use \jamesiarmes\PhpEws\Type\UserConfigurationNameType;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    WP_PHP_EWS_Auth
 * @subpackage WP_PHP_EWS_Auth/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    WP_PHP_EWS_Auth
 * @subpackage WP_PHP_EWS_Auth/includes
 * @author     Wouter S. Schuur <w.s.schuur@gmail.com>
 */
class WP_PHP_EWS_Auth {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Plugin_Name_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'WP_PHP_EWS_Auth';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Plugin_Name_Loader. Orchestrates the hooks of the plugin.
	 * - Plugin_Name_i18n. Defines internationalization functionality.
	 * - Plugin_Name_Admin. Defines all hooks for the admin area.
	 * - Plugin_Name_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-WP_PHP_EWS_Auth-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-WP_PHP_EWS_Auth-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-WP_PHP_EWS_Auth-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-WP_PHP_EWS_Auth-public.php';

		$this->loader = new WP_PHP_EWS_Auth_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new WP_PHP_EWS_Auth_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new WP_PHP_EWS_Auth_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new WP_PHP_EWS_Auth_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		//echo 'tralala';

		$this->loader->add_filter( 'authenticate', $this, 'demo_auth', 10, 3);

	}

	function demo_auth( $user, $username, $password ){

	    //die('we in bizniz baybee.');
	    // Make sure a username and password are present for us to work with
	    if($username == '' || $password == '') return;

		/* old example code */
	    // $response = wp_remote_get( "http://localhost/auth_serv.php?user=$username&pass=$password" );
	    // $ext_auth = json_decode( $response['body'], true );

		/* connect to exchange */

		$email = '';
        $start = new \DateTime('next monday 00:00:00');
        $end = new \DateTime('next tuesday 00:00:00');
        $meeting_duration = 60;
        // Replace with the timezone you would like the user's availability displayed
        // in.
        $timezone = 'Eastern Standard Time';
        // Set connection information.
        $host = '';
        $username = '';
        $password = '';
		

		// var_dump($_POST);
		// username $_POST['log'];
		// password $_POST['pwd'];
		include(dirname(__FILE__).'/../settings.php');
		
		$username = $_POST['log'];
		$username = $_POST['pwd'];	

        $version = Client::VERSION_2010;
        $client = new Client($host, $username, $password, $version);
        //$client->setTimezone($timezone);

		try{

			// Build the request to query for user credentials within exchange. 
			$request = new GetUserConfigurationType();
			$request->UserConfigurationProperties = UserConfigurationPropertyType::ALL;

			// Set the name of the configuration to retrieve.
			$name = new UserConfigurationNameType();
			$name->DistinguishedFolderId = new DistinguishedFolderIdType();
			$name->DistinguishedFolderId->Id = DistinguishedFolderIdNameType::ROOT;
			$name->Name = 'OWA.UserOptions';
			$request->UserConfigurationName = $name;

			$response = $client->GetUserConfiguration($request);
			
			
			$auth = 1;
		} catch( Exception $e ) {
			//var_dump($e->getMessage());
			$auth = 0;
		}
		// if($client){
		// 	var_dump($client);
		// 	die ('huzzah ! no acces.');
		// }

		if( $auth  == 0 ) {
			die('user has nog access. no local set should be checked');
	     	remove_action('authenticate', 'wp_authenticate_username_password', 20);
			// User does not exist in external set, check local set
			$user = wp_authenticate($username, $password);


			var_dump($user);
			die('user has nog access. no local set should be checked');
			if(!$user){
				$user = new WP_Error( 'denied', __("ERROR: User/pass bad") );	
			}
			die('no external user found');

		} else if( $auth  == 1 ) {
			// External user exists, try to load the user info from the WordPress user table
			$userobj = new WP_User();
			
			// remove slash from ww930 string as wordpress will clean it from the username
			$wp_user_name = str_replace('\\', '', $username);
			
			$user = $userobj->get_data_by( 'login',  $wp_user_name); // Does not return a WP_User object 🙁
			

			if( $user->ID == 0 ) {
	            // The user does not currently exist in the WordPress user table.
	            // You have arrived at a fork in the road, choose your destiny wisely

	            // If you do not want to add new users to WordPress if they do not
	            // already exist uncomment the following line and remove the user creation code
	            //$user = new WP_Error( 'denied', __("ERROR: Not a valid user for this system") );

	            // Setup the minimum required user information for this example
	        	$userdata = array( 
					'user_email' => $email,
	                'user_login' => $username,
	        		'first_name' => $firstname,
	                'last_name' => $lastname,
					'role' => 'editor'
	            );
	            
				$new_user_id = wp_insert_user( $userdata ); // A new user has been created

				// Load the new user info
				$user = new WP_User ($new_user_id);
			} else {
				$user = new WP_User($user->ID); // Attempt to load up the user with that IDs
			}

			var_dump($user);

		}

	     // Comment this line if you wish to fall back on WordPress authentication
	     // Useful for times when the external service is offline
	     remove_action('authenticate', 'wp_authenticate_username_password', 20);
		 //die('found userino');
	     return $user;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
