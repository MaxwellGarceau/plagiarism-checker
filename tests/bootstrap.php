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
	$wpGlobalTestConfigManager->commandLineHas( 'wp_brain_monkey' ) => ( function () use ( $wpSimulatedConfigManager ) {		
		/**
		 * Load Brain Monkey for function and class mocking
		 * 
		 * We will have to manually mock WP objects inside our tests
		 * but we can use Brain Monkey to make assertions on WP functions
		 */
		$wpSimulatedConfigManager->setupBrainMonkey();
	} )(),

	$wpGlobalTestConfigManager->commandLineHas( 'wp_full' ) => ( function () use ( $wpSimulatedConfigManager ) {
		$wpTestConfigManager = new WpCoreConfigManager( $_SERVER );

		// Copy and overwrite the wp-tests-config.php file every time
		$wpTestConfigManager->overwriteWpCoreConfig();

		$wpTestConfigManager->registerThisPluginWithTestsAddFilter();

		$wpTestConfigManager->bootstrapWpPhpUnit();

	} )(),

	default => ( function () use ( $wpSimulatedConfigManager ) {
		/**
		 * Load WordPress stubs from php-stubs/wordpress-stubs
		 * 
		 * This will let us test WP code, but not interact with it.
		 */
		// $wpSimulatedConfigManager->loadWpStubs();

		// Use Brain Monkey as the default for tests (for now)
		$wpSimulatedConfigManager->setupBrainMonkey();
	} )()
};
