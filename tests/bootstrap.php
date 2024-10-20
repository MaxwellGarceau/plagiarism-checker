<?php

// Autoload everything for unit tests.
require_once dirname( __DIR__, 1 ) . '/vendor/autoload.php';

use Dotenv\Dotenv;

/**
 * Load the test environment variables.
 * Calling here so that we can be sure we're passing in the root path.
 */
if ( ! file_exists( dirname( __DIR__, 1 ) . '/.env.test' ) ) {
	die( 'Error: .env.test file is missing. Aborting tests. Please check ' . __FILE__ . "\n" );
}

$dotenv = Dotenv::createImmutable( dirname( __DIR__, 1 ), '/.env.test' );

// Load the environment variables.
try {
	$dotenv->load();
} catch ( Exception $e ) {
	error_log( 'Error loading .env.test file in' . __FILE__ . ' :' . $e->getMessage() );
}

/**
 * Easier command line checking to route arguments to the correct loading sequence
 */
function argVHasCommand( string $command ): bool {
	return isset( $GLOBALS['argv'][1] ) && strpos( $GLOBALS['argv'][1], $command ) !== false;
}

/**
 * Route command arguments to appropriate configuration using a match statement
 */
$command = $GLOBALS['argv'][1] ?? '';

/**
 * TODO: This match will stop at the first match.
 * We may want to revisit this in the future.
 */
match ( true ) {
	argVHasCommand( 'simulated_wp' ) => ( function () {
		require_once dirname( __DIR__, 1 ) . '/tests/__bootstrap/WpSimulatedTestConfigManager.php';
		( new WpSimulatedTestConfigManager() )->init();
	} )(),

	argVHasCommand( 'full_wp' ) => ( function () {
		require_once dirname( __DIR__, 1 ) . '/tests/__bootstrap/WpTestConfigManager.php';
		$wpTestConfigManager = new WpTestConfigManager( $_SERVER );

		// Copy and overwrite the wp-tests-config.php file every time
		$wpTestConfigManager->overwriteWpCoreConfig();

		// Include functions for tests_add_filter()
		require_once dirname( __DIR__, 1 ) . '/wp/tests/phpunit/includes/functions.php';

		// Register mock theme
		$wpTestConfigManager->registerMockTheme();

		// Bootstrap wp/tests/phpunit
		require_once dirname( __DIR__, 1 ) . '/wp/tests/phpunit/includes/bootstrap.php';
	} )(),

	default => ( function () {
		// Default bootstrap - currently doing nothing for lightweight setup
	} )()
};
