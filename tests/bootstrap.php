<?php

// Autoload everything for unit tests.
require_once dirname( __DIR__, 1 ) . '/vendor/autoload.php';

use Dotenv\Dotenv;

/**
 * Load the test environment variables.
 * Calling here so that we can be sure we're passing in the root path.
 */
if (! file_exists(dirname( __DIR__, 1 ) . '/.env.test')) {
	die("Error: .env.test file is missing. Aborting tests. Please check " . __FILE__ . "\n");
}

$dotenv = Dotenv::createImmutable( dirname( __DIR__, 1 ), '/.env.test' );

// Load the environment variables.
try {
	$dotenv->load();
} catch ( Exception $e ) {
	error_log( 'Error loading .env.test file in' . __FILE__ . ' :' . $e->getMessage() );
}

// Toggle mocking of WordPress functions
if ( isset( $GLOBALS['argv'] ) && isset( $GLOBALS['argv'][1] ) && strpos( $GLOBALS['argv'][1], 'simulated_wp' ) !== false ) {
	// Set up Brain Monkey for mocking WordPress functions
	\Brain\Monkey\setUp();

	// Register a shutdown function to tear down Brain Monkey after tests
	register_shutdown_function(function () {
		\Brain\Monkey\tearDown();
	});
}

/**
 * Include core bootstrap for an integration test suite
 *
 * This will only work if you run the tests from the command line.
 * Running the tests from IDE such as PhpStorm will require you to
 * add additional argument to the test run command if you want to run
 * integration tests.
 */
if ( isset( $GLOBALS['argv'] ) && isset( $GLOBALS['argv'][1] ) && strpos( $GLOBALS['argv'][1], 'integration' ) !== false ) {

	if ( ! file_exists( dirname( __DIR__, 1 ) . '/wp/tests/phpunit/wp-tests-config.php' ) ) {
		// We need to set up core config details and test details
		copy( dirname( __DIR__, 1 ) . '/wp/wp-tests-config-sample.php', dirname( __DIR__, 1 ) . '/wp/tests/phpunit/wp-tests-config.php' );

		// Change certain constants from the test's config file.
		$testConfigPath     = dirname( __DIR__, 1 ) . '/wp/tests/phpunit/wp-tests-config.php';
		$testConfigContents = file_get_contents( $testConfigPath );

		$testConfigContents = str_replace( "dirname( __FILE__ ) . '/src/'", "dirname(__FILE__, 3) . '/src/'", $testConfigContents );
		$testConfigContents = str_replace( 'youremptytestdbnamehere', $_SERVER['DB_NAME'], $testConfigContents );
		$testConfigContents = str_replace( 'yourusernamehere', $_SERVER['DB_USER'], $testConfigContents );
		$testConfigContents = str_replace( 'yourpasswordhere', $_SERVER['DB_PASSWORD'], $testConfigContents );
		$testConfigContents = str_replace( 'localhost', $_SERVER['DB_HOST'], $testConfigContents );

		file_put_contents( $testConfigPath, $testConfigContents );
	}

	// Give access to tests_add_filter() function.
	require_once dirname( __DIR__, 1 ) . '/wp/tests/phpunit/includes/functions.php';

	/**
	 * Register mock theme.
	 */
	function _register_theme() {
		$themeDir     = dirname( __DIR__, 1 );
		$currentTheme = basename( $themeDir );
		$themeToot    = dirname( $themeDir );

		add_filter(
			'theme_root',
			function () use ( $themeToot ) {
				return $themeToot;
			}
		);

		register_theme_directory( $themeToot );

		add_filter(
			'pre_option_template',
			function () use ( $currentTheme ) {
				return $currentTheme;
			}
		);

		add_filter(
			'pre_option_stylesheet',
			function () use ( $currentTheme ) {
				return $currentTheme;
			}
		);
	}

	tests_add_filter( 'muplugins_loaded', '_register_theme' );

	require_once dirname( __DIR__, 1 ) . '/wp/tests/phpunit/includes/bootstrap.php';
}
