<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Admin\Constants;

class DB {
	const TABLE_NAME_NO_PREFIX = 'plagiarism_checker_access_tokens';

	/**
	 * Get the table name for the access tokens.
	 * 
	 * @param string $prefix The table prefix.
	 * @return string The table name with the $wpdb prefix.
	 */
	public function get_access_token_table_name( string $prefix ): string {
		return $prefix . self::TABLE_NAME_NO_PREFIX;
	}
}