<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Admin;

use wpdb;
use Max_Garceau\Plagiarism_Checker\Admin\Constants\DB;
use Max_Garceau\Plagiarism_Checker\Utilities\Encryption\Encryption_Strategy_Interface;

class Token_Storage {

	private wpdb $wpdb;
	private string $table_name;
	private string $api_token_key;
	private Encryption_Strategy_Interface $encryption;

	public function __construct( wpdb $wpdb, DB $constants, Encryption_Strategy_Interface $encryption ) {
		$this->wpdb = $wpdb;
		$this->table_name    = $constants->get_access_token_table_name( $wpdb->prefix );
		$this->api_token_key = $constants->get_api_token_key();
		$this->encryption    = $encryption;
	}

	/**
	 * Get the API token for the logged-in user, decrypted.
	 */
	public function get_token( int $user_id ): ?string {
		$encrypted_token = $this->wpdb->get_var(
			$this->wpdb->prepare(
				"SELECT {$this->api_token_key} FROM {$this->table_name} WHERE user_id = %d",
				$user_id
			)
		);

		return $encrypted_token ? $this->encryption->decrypt( $encrypted_token ) : null;
	}

	/**
	 * Save the API token for the logged-in user, encrypted.
	 */
	public function save_token( int $user_id, string $token ): bool {
		$sanitized_token = sanitize_text_field( $token );

		if ( ! preg_match( $this->get_validation_regex(), $sanitized_token ) ) {
			return false; // Invalid token format.
		}

		$encrypted_token = $this->encryption->encrypt( $sanitized_token );

		// Check if the token exists for the user.
		$existing_token = $this->get_token( $user_id );

		if ( $existing_token ) {
			// Update the token.
			$this->wpdb->update(
				$this->table_name,
				[ $this->api_token_key => $encrypted_token ],
				[ 'user_id' => $user_id ],
				[ '%s' ],
				[ '%d' ]
			);
		} else {
			// Insert new token.
			$this->wpdb->insert(
				$this->table_name,
				array(
					'user_id'            => $user_id,
					$this->api_token_key => $encrypted_token,
				),
				[ '%d', '%s' ]
			);
		}
		return true;
	}

	/**
	 * Allowed characters:
	 *
	 * - A-Z: Uppercase letters.
	 * - a-z: Lowercase letters.
	 * - 0-9: Digits.
	 * - -: Hyphen.
	 * - _: Underscore.
	 * - .: Dot.
	 * - @: At symbol.
	 */
	private function get_validation_regex(): string {
		return '/^[A-Za-z0-9\-_\.@]+$/';
	}
}
