<?php

use Max_Garceau\Plagiarism_Checker\Tests\Bootstrap\WpCoreConfigManager;
use Max_Garceau\Plagiarism_Checker\Tests\Bootstrap\WpSimulatedConfigManager;
use Max_Garceau\Plagiarism_Checker\Tests\Bootstrap\WpGlobalConfigManager;

require_once dirname( __DIR__, 1 ) . '/tests/__bootstrap/AbstractConfigManager.php';
require_once dirname( __DIR__, 1 ) . '/tests/__bootstrap/WpGlobalConfigManager.php';

$wpGlobalTestConfigManager = new WpGlobalConfigManager();

/**
 * Autoload vendor/autoload.php from composer project
 */
$wpGlobalTestConfigManager->loadProject();

/**
 * Load WordPress stubs from php-stubs/wordpress-stubs
 */
$wpGlobalTestConfigManager->loadWpStubs();

/**
 * Load the .env.test file
 */
$wpGlobalTestConfigManager->loadDotEnv();

/**
 * TODO: This match will stop at the first match.
 * We may want to revisit this in the future.
 */
match ( true ) {
	$wpGlobalTestConfigManager->commandLineHas( 'simulated_wp' ) => ( function () {
		require_once dirname( __DIR__, 1 ) . '/tests/__bootstrap/WpSimulatedConfigManager.php';
		( new WpSimulatedConfigManager() )->init();
	} )(),

	$wpGlobalTestConfigManager->commandLineHas( 'full_wp' ) => ( function () {
		require_once dirname( __DIR__, 1 ) . '/tests/__bootstrap/WpCoreConfigManager.php';
		$wpTestConfigManager = new WpCoreConfigManager( $_SERVER );

		// Copy and overwrite the wp-tests-config.php file every time
		$wpTestConfigManager->overwriteWpCoreConfig();

		// Register mock theme
		$wpTestConfigManager->registerMockTheme();

		$wpTestConfigManager->bootstrapWpPhpUnit();
	} )(),

	default => ( function () {
		// Default bootstrap - currently doing nothing for lightweight setup
	} )()
};
