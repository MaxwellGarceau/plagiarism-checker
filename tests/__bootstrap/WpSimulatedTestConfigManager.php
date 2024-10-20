<?php

namespace Max_Garceau\Plagiarism_Checker\Tests\Bootstrap;

use Max_Garceau\Plagiarism_Checker\Tests\Bootstrap\TestConfigManager;

/**
 * This class is to manage how we mock all of WP when running integration
 * tests that don't require actual interaction with WP core or a DB.
 *
 * Using Brain Monkey for the mocking
 */

class WpSimulatedTestConfigManager extends TestConfigManager {

	/**
	 * Main entry point to initialize the test config.
	 */
	public function init(): void {
		// Set up Brain Monkey for mocking WordPress functions
		\Brain\Monkey\setUp();

		// Register a shutdown function to tear down Brain Monkey after tests
		register_shutdown_function(
			function () {
				\Brain\Monkey\tearDown();
			}
		);
	}
}
