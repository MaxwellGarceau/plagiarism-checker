<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Admin;

use wpdb;
use Max_Garceau\Plagiarism_Checker\Admin\Constants\DB;

class Token_Storage {

	private wpdb $wpdb;
	private string $table_name;

	const API_TOKEN_KEY = 'genius_api_token';

	public function __construct( wpdb $wpdb, DB $constants ) {
		$this->wpdb = $wpdb;

		// Set DB $constants property if we need more than this in the future
		$this->table_name = $constants->get_access_token_table_name( $wpdb->prefix );
	}

	/**
	 * Get the API token for the logged-in user.
	 */
	public function get_token( int $user_id ): ?string {
		$api_token_key = self::API_TOKEN_KEY;
		return $this->wpdb->get_var( $this->wpdb->prepare(
			"SELECT {$api_token_key} FROM {$this->table_name} WHERE user_id = %d",
			$user_id
		));
	}

	/**
	 * Save the API token for the logged-in user.
	 */
	public function save_token( int $user_id, string $token ): bool {
		$sanitized_token = sanitize_text_field( $token );
		if ( ! preg_match( '/^[A-Za-z0-9]+$/', $sanitized_token ) ) {
			return false; // Invalid token format.
		}

		// Check if the token exists for the user.
		$existing_token = $this->get_token( $user_id );

		if ( $existing_token ) {
			// Update the token.
			$this->wpdb->update(
				$this->table_name,
				[ self::API_TOKEN_KEY => $sanitized_token ],
				[ 'user_id' => $user_id ],
				[ '%s' ],
				[ '%d' ]
			);
		} else {
			// Insert new token.
			$this->wpdb->insert(
				$this->table_name,
				[ 'user_id' => $user_id, self::API_TOKEN_KEY => $sanitized_token ],
				[ '%d', '%s' ]
			);
		}
		return true;
	}

	/**
	 * Create the custom table for storing tokens.
	 * 
	 * TODO: This should be somewhere else
	 */
	public function create_table(): void {
		$api_token_key = self::API_TOKEN_KEY;
		$charset_collate = $this->wpdb->get_charset_collate();
		$sql = "CREATE TABLE {$this->table_name} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			{$api_token_key} VARCHAR(255) NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY user_id (user_id)
		) $charset_collate;";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}
