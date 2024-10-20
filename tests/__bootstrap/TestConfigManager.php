<?php

namespace Max_Garceau\Plagiarism_Checker\Tests\Bootstrap;

/**
 * Manage shared logic for testing configs here
 *
 * Choosing inheritance over composition because the lift to
 * instantiate all of the config objects is high, would be awkward,
 * and I don't anticipate the testing configs expanding much past
 * this point.
 */
abstract class TestConfigManager {
	/**
	 * Get the root path of the testing suite.
	 *
	 * @return string
	 */
	protected function getRootTestPath(): string {
		return dirname( __DIR__, 1 );
	}

	/**
	 * Get root project path
	 *
	 * @return string
	 */
	protected function getRootProjectPath(): string {
		return dirname( __DIR__, 2 );
	}
}
