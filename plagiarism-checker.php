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
use Max_Garceau\Plagiarism_Checker\Utilities\Env_Loader;

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
		Env_Loader::load_env( plugin_dir_path( __FILE__ ) );

		// Initialize the plugin.
		$container = DI_Container::build_container();

		// Use the container to initialize the main plugin class
		$container->get( Main::class )->init();
	}
);
