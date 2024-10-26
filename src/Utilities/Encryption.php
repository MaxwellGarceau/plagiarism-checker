<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Utilities;

use Max_Garceau\Plagiarism_Checker\Admin\Notice;

/**
 * Encrypt and decrypt data using the libsodium extension.
 * 
 * Here, we're leaning into the WP functionality by using
 * wp_salt as our key. This way, we can sure we have a stable
 * input that is unique for every different WordPress site.
 * 
 * We also give the user the ability to increase their security
 * by augmenting the native WP salt with a server side secret
 * key. The secret key would have the benefit of being private
 * while simultaneously allowing us to pull in the security
 * benefit in our encryption here.
 */
class Encryption {

	private string $key;

	private Notice $notice;

	public function __construct( Notice $notice ) {
		$this->notice = $notice;

		if ( ! $this->is_libsodium_available() ) {
			add_action(
				'admin_notices',
				function () {
					$this->notice->display_error_notice( __( 'Error: The encryption library "Sodium" is not available on your site! This means the sensitive data you enter into Plagiarism Checker will not be encrypted!!!', 'plagiarism-checker' ) );
				}
			);
		}

		$this->key = $this->generate_key();
	}

	/**
	 * Encrypt data using libsodium.
	 */
	public function encrypt( string $data ): string {
		$nonce     = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
		$encrypted = sodium_crypto_secretbox( $data, $nonce, $this->key );

		// Combine nonce and encrypted data for storage, then encode in base64
		return base64_encode( $nonce . $encrypted );
	}

	/**
	 * Decrypt data using libsodium.
	 */
	public function decrypt( string $encrypted_data ): string {
		$decoded = base64_decode( $encrypted_data );

		// Extract nonce and encrypted data
		$nonce      = mb_substr( $decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, '8bit' );
		$ciphertext = mb_substr( $decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES, null, '8bit' );

		// Attempt decryption and return empty string if decryption fails
		return sodium_crypto_secretbox_open( $ciphertext, $nonce, $this->key ) ?: '';
	}

	/**
	 * Generate a unique, site-specific key using wp_salt.
	 */
	private function generate_key(): string {
		$salt = wp_salt( 'auth' );
		return sodium_crypto_generichash( $salt, '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES );
	}

	/**
	 * Check if libsodium extension is available.
	 */
	private function is_libsodium_available(): bool {
		return extension_loaded( 'sodium' );
	}
}
