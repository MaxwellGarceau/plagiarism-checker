<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Includes;

use Max_Garceau\Plagiarism_Checker\Services\Nonce_Service;

class Admin_Ajax {

	/**
	 * @param Nonce_Service $nonce_service
	 */
	public function __construct( private Nonce_Service $nonce_service ) {}

	public function handle_plagiarism_checker_request(): void {

		// Check nonce
		if ( $this->nonce_service->verify_nonce() === false ) {
			wp_send_json_error( 'Invalid nonce.' );
		}

		// Validate and sanitize
		$text = isset( $_POST['text'] ) ? sanitize_text_field( $_POST['text'] ) : '';

		// TODO: Do I need a looser check here?
		if ( $text === '' ) {
			wp_send_json_error( 'No text provided.' );
		}

		// TODO: Set up call to genius.com API
		$response = new \WP_REST_Response( json_decode( file_get_contents( plugin_dir_path( __DIR__ ) . '../tests/fixtures/response.json' ), true ) );

		if ( is_wp_error( $response ) || $response->get_status() !== 200 ) {
			wp_send_json_error( 'API request failed.' );
		}

		$data = $response->get_data();

		// Process the response and send it back to the frontend
		wp_send_json_success( $data['response']['hits'] );
	}
}
