<?php

namespace Max_Garceau\Plagiarism_Checker\Includes;

class Admin_Ajax {

	const NONCE_KEY = 'plagiarism_checker_nonce';

	// TODO: This doesn't belong here. Move to an Enqueue class later.
	const JS_HANDLE = 'plagiarism-checker-scripts';

	const JS_OBJECT_NAME = 'plagiarismCheckerAjax';

	public function localize_scripts(): void {
		wp_localize_script(
			self::JS_HANDLE,
			self::JS_OBJECT_NAME,
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => $this->create_nonce(),
			)
		);
	}

	private function create_nonce(): string {
		return wp_create_nonce( self::NONCE_KEY );
	}

	public function handle_plagiarism_checker_request(): void {

		// Check nonce
		check_ajax_referer( 'plagiarism_checker_nonce' );

		// Validate and sanitize
		$text = isset( $_POST['text'] ) ? sanitize_text_field( $_POST['text'] ) : '';

		// TODO: Do I need a looser check here?
		if ( $text === '' ) {
			wp_send_json_error( 'No text provided.' );
		}

		// TODO: Set up call to genius.com API
		$response = array(
			'success' => true,
			'data'    => array(),
		);

		if ( is_wp_error( $response ) ) {
			wp_send_json_error( 'API request failed.' );
		}

		// Process the response and send it back to the frontend
		wp_send_json_success( $response );
	}
}
