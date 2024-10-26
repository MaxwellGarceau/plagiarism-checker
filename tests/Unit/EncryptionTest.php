<?php

namespace Tests\Unit;

use function Brain\Monkey\Functions\when;

/**
 * TODO: Make these tests
 *
 * These tests are a placeholder for the actual tests that need to be written.
 * This way we know exactly what the requirements for this functionality are.
 *
 * These tests should cover everything in the /src/Admin directory.
 */

use Max_Garceau\Plagiarism_Checker\Utilities\Encryption\Encryption;
use Mockery;

/**
 * Main Functionality tests
 */

beforeEach(function() {
	when('wp_salt')->justReturn('mocked_salt_value');

	global $wpdb;
	$this->wpdb = Mockery::mock('wpdb');
	$this->wpdb->prefix = 'wp_';
});

it(
	'should encrypt and decrypt an access token',
	function () {
		$original = 'This should be the same coming out as it was going in.';
		$encryption = new Encryption();
		$encrypted = $encryption->encrypt($original);
		$decrypted = $encryption->decrypt($encrypted);
		expect($decrypted)->toBe($original);
	}
)->group( 'wp_brain_monkey' );
