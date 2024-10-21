<?php

namespace Max_Garceau\Plagiarism_Checker\Tests\Bootstrap;

use Max_Garceau\Plagiarism_Checker\Tests\Bootstrap\AbstractConfigManager;

/**
 * TODO: Is this over engineered? Maybe we should put this into a function and call it a day.
 * I like the class approach because it's to make small changes in isolation, but the functional
 * approach is easier to read.
 *
 * What does this do?
 *
 * This class will set the configs for running an actual WP core install
 * with a test database.
 *
 * This initializes the /wp development that repo we've imported
 * into the /tests folder for our environment and test DB
 *
 * - Checks if /wp/wp-tests-config.php exists
 * - Copies it to /wp/tests/phpunit/wp-tests-config.php
 * - Modifies /wp/tests/phpunit/wp-tests-config.php to set the ABSPATH to /wp/src
 * - Modifies /wp/tests/phpunit/wp-tests-config.php to set the DB_NAME, DB_USER, DB_PASSWORD, and DB_HOST
 */
class WpCoreConfigManager extends AbstractConfigManager {

	/**
	 * Path to the sample test config file.
	 *
	 * @var string
	 */
	private string $sampleConfigPath;

	/**
	 * Path to the actual test config file to be used during testing.
	 *
	 * @var string
	 */
	private string $testConfigPath;

	/**
	 * Environment variables for database configuration.
	 *
	 * @var array
	 */
	private array $dbConfig;

	/**
	 * Constructor to set up paths and load environment variables.
	 */
	public function __construct( array $dbConfig ) {
		$this->sampleConfigPath = $this->getRootTestPath() . '/wp/wp-tests-config-sample.php';
		$this->testConfigPath   = $this->getRootTestPath() . '/wp/tests/phpunit/wp-tests-config.php';
		$this->dbConfig         = $this->loadDatabaseConfig( $dbConfig );
	}

	/**
	 * Overwrite /wp/src tests config with our plugins test config
	 */
	public function overwriteWpCoreConfig(): void {
		$this->copySampleConfig();
		$this->updateTestConfig();
	}

	/**
	 * Copy the sample wp-tests-config file to the phpunit tests directory.
	 */
	private function copySampleConfig(): void {
		if ( ! file_exists( $this->testConfigPath ) ) {
			copy( $this->sampleConfigPath, $this->testConfigPath );
		}
	}

	/**
	 * Load database configuration from environment variables.
	 *
	 * @return array
	 */
	private function loadDatabaseConfig( array $dbConfig ): array {
		$requiredKeys = [ 'DB_NAME', 'DB_USER', 'DB_PASSWORD', 'DB_HOST' ];
		foreach ( $requiredKeys as $key ) {
			if ( ! isset( $dbConfig[ $key ] ) ) {
				throw new \InvalidArgumentException( "Missing required database configuration key: $key" );
			}
		}

		return [
			'DB_NAME'     => $dbConfig['DB_NAME'],
			'DB_USER'     => $dbConfig['DB_USER'],
			'DB_PASSWORD' => $dbConfig['DB_PASSWORD'],
			'DB_HOST'     => $dbConfig['DB_HOST'],
		];
	}

	/**
	 * Update the test configuration file with proper paths and database credentials.
	 */
	private function updateTestConfig(): void {
		$configContents = file_get_contents( $this->testConfigPath );

		$configContents = $this->setAbspath( $configContents );
		$configContents = $this->setDatabaseConfig( $configContents );

		file_put_contents( $this->testConfigPath, $configContents );
	}

	/**
	 * Set the ABSPATH to the /wp/src directory in the configuration file.
	 *
	 * @param string $configContents
	 * @return string
	 */
	private function setAbspath( string $configContents ): string {
		return preg_replace(
			"/dirname\s*\(\s*__FILE__\s*\)\s*\.\s*['\"]\/src\/['\"]/",
			"dirname(__FILE__, 3) . '/src/'",
			$configContents
		);
	}

	/**
	 * Replace placeholders in the config file with actual database credentials.
	 *
	 * @param string $configContents
	 * @return string
	 */
	private function setDatabaseConfig( string $configContents ): string {
		foreach ( $this->dbConfig as $key => $value ) {
			$configContents = str_replace( strtolower( $key ), $value, $configContents );
		}
		return $configContents;
	}

	public function registerThisPluginWithTestsAddFilter(): void {
		// Include functions for tests_add_filter()
		require_once $this->getRootTestPath() . '/wp/tests/phpunit/includes/functions.php';

		// Register Mock theme - anonymous function for brevity
		tests_add_filter(
			'muplugins_loaded',
			function () {
				require $this->getRootProjectPath() . '/plagiarism-checker.php';
			}
		);
	}

	public function bootstrapWpPhpUnit(): void {
		// Bootstrap wp/tests/phpunit
		require_once $this->getRootTestPath() . '/wp/tests/phpunit/includes/bootstrap.php';
	}
}
