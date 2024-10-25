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

// Include the Composer autoloader.
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

add_action(
	'plugins_loaded',
	function (): void {
		// Bail early if the user is not logged in.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Initialize the plugin.
		$container = DI_Container::build_container();

		// Use the container to initialize the main plugin class
		$container->get( Main::class )->init();
	}
);

// Register activation hook for creating the database table.
register_activation_hook( __FILE__, function() {
	DI_Container::get_container()->get( \Max_Garceau\Plagiarism_Checker\Admin\Table_Manager::class )->create_table();
});
