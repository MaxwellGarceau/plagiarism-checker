<?php
/**
 * PHPUnit bootstrap file.
 *
 * @package Plagiarism_Checker
 */

// // Set APP_ENV to 'testing'
// $_ENV['APP_ENV'] = 'testing';

// // Define the path to the .env.test file
// $envTestFile = dirname( __DIR__ ) . '/.env.test';

// // Check if .env.test exists, if not throw an error
// if (!file_exists($envTestFile)) {
//     echo "Error: .env.test file is missing. Please create a .env.test file to run tests." . PHP_EOL;
//     exit(1);
// }

// // Load the .env.test file
// require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// $dotenv = Dotenv\Dotenv::createImmutable(dirname( __DIR__ ), '.env.test');
// $dotenv->load();

// $_tests_dir = $_ENV['WP_TESTS_DIR'] ?? rtrim(sys_get_temp_dir(), '/\\') . '/wordpress-tests-lib';

// // Forward custom PHPUnit Polyfills configuration to PHPUnit bootstrap file.
// if (isset($_ENV['WP_TESTS_PHPUNIT_POLYFILLS_PATH'])) {
//     define( 'WP_TESTS_PHPUNIT_POLYFILLS_PATH', $_ENV['WP_TESTS_PHPUNIT_POLYFILLS_PATH'] );
// }

// if ( ! file_exists( "{$_tests_dir}/includes/functions.php" ) ) {
//     echo "Could not find {$_tests_dir}/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL;
//     exit( 1 );
// }

// // Give access to tests_add_filter() function.
// require_once "{$_tests_dir}/includes/functions.php";

// /**
//  * Manually load the plugin being tested.
//  */
// function _manually_load_plugin() {
//     require dirname( dirname( __FILE__ ) ) . '/plagiarism-checker.php';
// }

// tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// // Start up the WP testing environment.
// require "{$_tests_dir}/includes/bootstrap.php";
