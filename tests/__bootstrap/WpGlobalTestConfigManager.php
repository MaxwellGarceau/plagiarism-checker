<?php

namespace Max_Garceau\Plagiarism_Checker\Tests\Bootstrap;

use Dotenv\Dotenv;
use Max_Garceau\Plagiarism_Checker\Tests\Bootstrap\AbstractConfigManager;

/**
 * Manage global requirements for testing
 */
class WpGlobalTestConfigManager extends AbstractConfigManager {
	public function loadProject(): void {
		// Autoload everything for unit tests.
		require_once $this->getRootProjectPath() . '/vendor/autoload.php';
	}

	public function loadDotEnv(): void {
		/**
		 * Load the test environment variables.
		 * Calling here so that we can be sure we're passing in the root path.
		 */
		if ( ! file_exists( $this->getRootProjectPath() . '/.env.test' ) ) {
			die( 'Error: .env.test file is missing. Aborting tests. Please check ' . __FILE__ . "\n" );
		}

		$dotenv = Dotenv::createImmutable( $this->getRootProjectPath(), '/.env.test' );

		// Load the environment variables.
		try {
			$dotenv->load();
		} catch ( \Exception $e ) {
			error_log( 'Error loading .env.test file in' . __FILE__ . ' :' . $e->getMessage() );
		}
	}

	/**
	 * Easier command line checking to route arguments to the correct loading sequence
	 */
	public function commandLineHas( string $command, string $argv = null ): bool {
		if ( $argv === null ) {
			$argv = $GLOBALS['argv'];
		}

		return isset( $GLOBALS['argv'][1] ) && strpos( $GLOBALS['argv'][1], $command ) !== false;
	}
}
