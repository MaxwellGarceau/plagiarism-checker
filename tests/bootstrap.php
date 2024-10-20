<?php

use Max_Garceau\Plagiarism_Checker\Tests\Bootstrap\WpCoreTestConfigManager;
use Max_Garceau\Plagiarism_Checker\Tests\Bootstrap\WpSimulatedTestConfigManager;
use Max_Garceau\Plagiarism_Checker\Tests\Bootstrap\WpGlobalTestConfigManager;

require_once dirname( __DIR__, 1 ) . '/tests/__bootstrap/TestConfigManager.php';
require_once dirname( __DIR__, 1 ) . '/tests/__bootstrap/WpGlobalTestConfigManager.php';

$wpGlobalTestConfigManager = new WpGlobalTestConfigManager();

/**
 * Autoload vendor/autoload.php from composer project
 */
$wpGlobalTestConfigManager->loadProject();

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
		require_once dirname( __DIR__, 1 ) . '/tests/__bootstrap/WpSimulatedTestConfigManager.php';
		( new WpSimulatedTestConfigManager() )->init();
	} )(),

	$wpGlobalTestConfigManager->commandLineHas( 'full_wp' ) => ( function () {
		require_once dirname( __DIR__, 1 ) . '/tests/__bootstrap/WpCoreTestConfigManager.php';
		$wpTestConfigManager = new WpCoreTestConfigManager( $_SERVER );

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
