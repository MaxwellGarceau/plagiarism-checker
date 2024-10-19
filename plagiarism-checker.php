<?php

declare( strict_types = 1 );

/**
 * Plugin Name:     Plagiarism Checker
 * Plugin URI:
 * Description:     Checks lyrics for plagiarism.
 * Author:          Max Garceau
 * Author URI:      https://resume.maxgarceau.com/
 * Text Domain:     plagiarism-checker
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Plagiarism_Checker
 */

use Max_Garceau\Plagiarism_Checker\Includes\DI_Container;
use Max_Garceau\Plagiarism_Checker\Main;
use Dotenv\Dotenv;

// Include the Composer autoloader.
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

add_action(
	'plugins_loaded',
	function (): void {
		// Bail early if the user is not logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}

		/**
		 * Load the environment variables.
		 * Calling here so that we can be sure we're passing in the root path.
		 */
		$dotenv = Dotenv::createImmutable( plugin_dir_path( __FILE__ ) );

		// Load the environment variables.
		try {
			$dotenv->load();
		} catch ( Exception $e ) {
			error_log( 'Error loading .env file: ' . $e->getMessage() );
			return;
		}

		// Initialize the plugin.
		$container = DI_Container::build_container();

		// Use the container to initialize the main plugin class
		$container->get( Main::class )->init();
	}
);
