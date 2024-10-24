<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Admin;

use wpdb;
use Max_Garceau\Plagiarism_Checker\Admin\Constants\DB;

class Token_Storage {

	private wpdb $wpdb;
	private string $table_name;
	private string $api_token_key;

	public function __construct( wpdb $wpdb, DB $constants ) {
		$this->wpdb = $wpdb;

		// Set DB $constants property if we need more than this in the future
		$this->table_name = $constants->get_access_token_table_name( $wpdb->prefix );
		$this->api_token_key = $constants->get_api_token_key();
	}

	/**
	 * Get the API token for the logged-in user.
	 */
	public function get_token( int $user_id ): ?string {
		return $this->wpdb->get_var( $this->wpdb->prepare(
			"SELECT {$this->api_token_key} FROM {$this->table_name} WHERE user_id = %d",
			$user_id
		));
	}

	/**
	 * Save the API token for the logged-in user.
	 */
	public function save_token( int $user_id, string $token ): bool {
		$sanitized_token = sanitize_text_field( $token );

		/**
		 * TODO: Find better access token validation
		 */
		if ( ! preg_match( '/^[A-Za-z0-9]+$/', $sanitized_token ) ) {
			return false; // Invalid token format.
		}

		// Check if the token exists for the user.
		$existing_token = $this->get_token( $user_id );

		if ( $existing_token ) {
			// Update the token.
			$this->wpdb->update(
				$this->table_name,
				[ $this->api_token_key => $sanitized_token ],
				[ 'user_id' => $user_id ],
				[ '%s' ],
				[ '%d' ]
			);
		} else {
			// Insert new token.
			$this->wpdb->insert(
				$this->table_name,
				[ 'user_id' => $user_id, $this->api_token_key => $sanitized_token ],
				[ '%d', '%s' ]
			);
		}
		return true;
	}
}
