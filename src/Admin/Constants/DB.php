<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Admin\Constants;

class DB {
	const TABLE_NAME_NO_PREFIX = 'plagiarism_checker_access_tokens';
	const API_TOKEN_KEY        = 'genius_api_token';

	/**
	 * Get the table name for the access tokens.
	 *
	 * @param string $prefix The table prefix.
	 * @return string The table name with the $wpdb prefix.
	 */
	public function get_access_token_table_name( string $prefix ): string {
		return $prefix . self::TABLE_NAME_NO_PREFIX;
	}

	/**
	 * Get the API token key.
	 *
	 * We can refactor this to accept an argument and
	 * return a specific key in the future if necessary.
	 *
	 * @return string The API token key.
	 */
	public function get_api_token_key(): string {
		return self::API_TOKEN_KEY;
	}
}
