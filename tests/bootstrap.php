<?php

use Max_Garceau\Plagiarism_Checker\Tests\Bootstrap\WpCoreConfigManager;
use Max_Garceau\Plagiarism_Checker\Tests\Bootstrap\WpSimulatedConfigManager;
use Max_Garceau\Plagiarism_Checker\Tests\Bootstrap\WpGlobalConfigManager;

// Load autoloaded files
require_once dirname( __DIR__, 1 ) . '/vendor/autoload.php';

/**
 * Load the .env.test file
 */
$wpGlobalTestConfigManager = new WpGlobalConfigManager();
$wpGlobalTestConfigManager->loadDotEnv();

$wpSimulatedConfigManager = new WpSimulatedConfigManager();

/**
 * TODO: This match will stop at the first match.
 * We may want to revisit this in the future.
 */
match ( true ) {
	$wpGlobalTestConfigManager->commandLineHas( 'simulated_wp' ) => ( function () use ( $wpSimulatedConfigManager ) {
		/**
		 * Load WordPress stubs from php-stubs/wordpress-stubs
		 */
		$wpSimulatedConfigManager->loadWpStubs();

		$wpSimulatedConfigManager->setupBrainMonkey();
	} )(),

	$wpGlobalTestConfigManager->commandLineHas( 'full_wp' ) => ( function () {
		$wpTestConfigManager = new WpCoreConfigManager( $_SERVER );

		// Copy and overwrite the wp-tests-config.php file every time
		$wpTestConfigManager->overwriteWpCoreConfig();

		// Register mock theme
		$wpTestConfigManager->registerMockTheme();

		$wpTestConfigManager->bootstrapWpPhpUnit();
	} )(),

	default => ( function () use ( $wpSimulatedConfigManager ) {
		/**
		 * Load WordPress stubs from php-stubs/wordpress-stubs
		 *
		 * TODO: Might merge this with simulated_wp
		 */
		$wpSimulatedConfigManager->loadWpStubs();
	} )()
};
