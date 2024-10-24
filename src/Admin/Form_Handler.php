<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Admin;

use Max_Garceau\Plagiarism_Checker\Admin\Token_Storage;

class Form_Handler {

	private Token_Storage $token_storage;

	public function __construct( Token_Storage $token_storage ) {
		$this->token_storage = $token_storage;
	}

	/**
	 * TODO: Refactor nonce handling so that we use the Nonce_Service
	 * to handle nonces here as well
	 */
	public function handle_form_submission(): void {
		if ( ! isset( $_POST['plagiarism_checker_nonce'] ) || ! wp_verify_nonce( $_POST['plagiarism_checker_nonce'], 'plagiarism_checker_save_token' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'plagiarism-checker' ) );
		}

		$user_id   = get_current_user_id();
		$api_token = isset( $_POST['plagiarism_checker_api_token'] ) ? sanitize_text_field( wp_unslash( $_POST['plagiarism_checker_api_token'] ) ) : '';

		if ( $this->token_storage->save_token( $user_id, $api_token ) ) {
			wp_safe_redirect( add_query_arg( 'status', 'success', wp_get_referer() ) );
		} else {
			wp_safe_redirect( add_query_arg( 'status', 'error', wp_get_referer() ) );
		}

		exit;
	}
}
