<?php

namespace Max_Garceau\Plagiarism_Checker\Tests\Bootstrap;

use Max_Garceau\Plagiarism_Checker\Tests\Bootstrap\AbstractConfigManager;

/**
 * This class is to manage how we mock all of WP when running integration
 * tests that don't require actual interaction with WP core or a DB.
 *
 * Using Brain Monkey for the mocking
 */

class WpSimulatedConfigManager extends AbstractConfigManager {

	/**
	 * Main entry point to initialize the test config.
	 */
	public function setupBrainMonkey(): void {
		// Set up Brain Monkey for mocking WordPress functions
		\Brain\Monkey\setUp();

		// Register a shutdown function to tear down Brain Monkey after tests
		register_shutdown_function(
			function () {
				\Brain\Monkey\tearDown();
			}
		);
	}

	/**
	 * WordPress stubs by the php-stubs/wordpress-stubs package
	 *
	 * Included so that we can use WP functions and classes with having
	 * to mock then or include the entire WordPress core.
	 */
	public function loadWpStubs(): void {
		require_once $this->getRootProjectPath() . '/vendor/php-stubs/wordpress-stubs/wordpress-stubs.php';
	}
}
