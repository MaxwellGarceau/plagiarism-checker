<?php

namespace Max_Garceau\Plagiarism_Checker\Admin;

use Max_Garceau\Plagiarism_Checker\Admin\Constants\DB;
use Max_Garceau\Plagiarism_Checker\Admin\Notice;
use wpdb;

/**
 * Handles database table creation
 *
 * Also checks if the table exists in the database
 * and displays an admin error if it does not.
 */
class Table_Manager {

	private wpdb $wpdb;
	private string $table_name;
	private string $api_token_key;
	private Notice $notice;

	public function __construct( wpdb $wpdb, DB $constants, Notice $notice ) {
		$this->wpdb          = $wpdb;
		$this->notice        = $notice;
		$this->table_name    = $constants->get_access_token_table_name( $wpdb->prefix );
		$this->api_token_key = $constants->get_api_token_key();
	}

	/**
	 * Create the custom table for storing tokens.
	 */
	public function create_table(): void {
		$charset_collate = $this->wpdb->get_charset_collate();
		$sql             = "CREATE TABLE {$this->table_name} (
			id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
			user_id BIGINT(20) UNSIGNED NOT NULL,
			{$this->api_token_key} VARCHAR(255) NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY user_id (user_id)
		) $charset_collate;";
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Check if the table exists in the database.
	 *
	 * @return bool True if the table exists, false otherwise.
	 */
	private function does_table_exist(): bool {
		$result = $this->wpdb->get_var(
			$this->wpdb->prepare(
				'SHOW TABLES LIKE %s',
				$this->table_name
			)
		);

		return $result === $this->table_name;
	}

	/**
	 * Display an admin notice if the table does not exist.
	 */
	public function maybe_show_admin_notice(): void {
		if ( ! $this->does_table_exist() ) {
			add_action(
				'admin_notices',
				function () {
					$this->notice->display_error_notice(
						'Error: The Plagiarism Checker database table does not exist. Please reinstall or contact support.'
					);
				}
			);
		}
	}
}
