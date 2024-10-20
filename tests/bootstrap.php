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
	return isset( $GLOBALS['argv'] ) && isset( $GLOBALS['argv'][1] ) && strpos( $GLOBALS['argv'][1], $command ) !== false;
}


/**
 * Mock all of WP Core with Brain Monkey
 */
if ( argVHasCommand( 'simulated_wp' ) ) {
	require_once dirname( __DIR__, 1 ) . '/tests/__bootstrap/WpSimulatedTestConfigManager.php';
	( new WpSimulatedTestConfigManager() )->init();

	/**
	 * Include core bootstrap for an integration test suite
	 *
	 * This will only work if you run the tests from the command line.
	 * Running the tests from IDE such as PhpStorm will require you to
	 * add additional argument to the test run command if you want to run
	 * integration tests.
	 */
} elseif ( argVHasCommand( 'full_wp' ) ) {
	require_once dirname( __DIR__, 1 ) . '/tests/__bootstrap/WpTestConfigManager.php';
	$wpTestConfigManager = new WpTestConfigManager( $_SERVER );
	/**
	 * Copy and overwrite the /wp/tests/phpunit/wp-tests-config.php file every time
	 * It's worth it to avoid the troubleshooting that will inevitably come from a stale config file.
	 */
	// if ( ! file_exists( dirname( __DIR__, 1 ) . '/wp/tests/phpunit/wp-tests-config.php' ) ) {
		require_once dirname( __DIR__, 1 ) . '/tests/__bootstrap/WpTestConfigManager.php';
		$wpTestConfigManager->overwriteWpCoreConfig();
	// }

	// Give access to tests_add_filter() function.
	// TODO: Should this be inside registerMockTheme?
	require_once dirname( __DIR__, 1 ) . '/wp/tests/phpunit/includes/functions.php';

	/**
	 * Register mock theme.
	 */
	$wpTestConfigManager->registerMockTheme();

	// Bootstrap wp/tests/phpunit
	require_once dirname( __DIR__, 1 ) . '/wp/tests/phpunit/includes/bootstrap.php';
} else {
	/**
	 * Default bootstrap
	 */

	// Currently set to nothing to keep the test setup lightweight
}
