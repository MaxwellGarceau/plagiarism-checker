<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Utilities\Encryption;

use Max_Garceau\Plagiarism_Checker\Utilities\Encryption\Encryption_Interface;

/**
 * Returns original data and disables encryption
 *
 * I've added fallbacks and an admin notice to display if libsodium
 * is not available. Personally, I think this is major overkill
 * since this plugin is not supported on PHP versions below 8.1.
 *
 * TODO: Do we really want this complexity?
 */
class Encryption_Disabled implements Encryption_Interface {

	public function encrypt( string $data ): string {
		return $data; // Return original data if encryption is disabled
	}

	public function decrypt( string $encrypted_data ): string {
		return $encrypted_data; // Return original data if decryption is disabled
	}
}
