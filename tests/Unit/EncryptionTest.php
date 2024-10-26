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

use Max_Garceau\Plagiarism_Checker\Includes\DI_Container;
use Max_Garceau\Plagiarism_Checker\Utilities\Encryption\Libsodium_Encryption_Strategy;
use Max_Garceau\Plagiarism_Checker\Admin\Token_Storage;
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
		$encryption = new Libsodium_Encryption_Strategy();
		$encrypted = $encryption->encrypt($original);
		$decrypted = $encryption->decrypt($encrypted);
		expect($decrypted)->toBe($original);
	}
)->group( 'wp_brain_monkey' );

it('uses Libsodium_Encryption when libsodium is enabled', function () {
    // Simulate libsodium available
    Mockery::mock('alias:sodium')->shouldReceive('extension_loaded')->andReturn(true);

    // Set up PHP-DI container with custom definitions
	$container = DI_Container::build_container();

    // Resolve Token_Storage from container
    $tokenStorage = $container->get(Token_Storage::class);

    // Use Reflection to access private "encryption" property in Token_Storage
    $reflection = new \ReflectionClass($tokenStorage);
    $encryptionProperty = $reflection->getProperty('encryption');
    $encryptionProperty->setAccessible(true);
    $encryptionInstance = $encryptionProperty->getValue($tokenStorage);

    // Assert that Token_Storage received an instance of Libsodium_Encryption
    expect($encryptionInstance)->toBeInstanceOf(Libsodium_Encryption_Strategy::class);
})->group('wp_brain_monkey')->skip('Feature implemented - !!!test not yet written!!!');