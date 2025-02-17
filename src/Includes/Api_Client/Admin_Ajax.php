<?php

declare( strict_types=1 );

namespace Max_Garceau\Plagiarism_Checker\Includes\Api_Client;

use Max_Garceau\Plagiarism_Checker\Services\Nonce_Service;
use Max_Garceau\Plagiarism_Checker\Services\Nonce_Status;
use Max_Garceau\Plagiarism_Checker\Includes\Api_Client\Client;
use Max_Garceau\Plagiarism_Checker\Includes\Api_Client\Resource;
use Max_Garceau\Plagiarism_Checker\Includes\Api_Client\Api_Response_Validator;
use Monolog\Logger;

class Admin_Ajax {

	// TODO: This exists in the Api_Client\Client class too
	// Need to consolidate
	const WP_ERROR_CODE = 'genius_api_error';

	/**
	 * @param Nonce_Service          $nonce_service  The service used to validate nonces.
	 * @param Client                 $api_client     The client responsible for making requests to the Genius API.
	 * @param Logger                 $logger         A logger to capture any issues.
	 * @param Resource               $resource       A resource instance to handle the formatting of responses.
	 * @param Api_Response_Validator $validator      A validator instance to check API response integrity.
	 */
	public function __construct(
		private readonly Nonce_Service $nonce_service,
		private readonly Client $api_client,
		private readonly Logger $logger,
		private readonly Resource $resource,
		private readonly Api_Response_Validator $validator
	) {}

	/**
	 * Handle the plagiarism checker request via AJAX.
	 */
	public function handle_plagiarism_checker_request(): void {
		// Verify nonce.
		if ( $this->nonce_service->verify_nonce() === Nonce_Status::INVALID ) {
			$this->logger->error( 'Invalid or expired nonce.' );
			wp_send_json_error( $this->resource->error( 'Invalid or expired nonce.', '', 403 ) );
		}

		// Validate and sanitize text input.
		$text = isset( $_POST['text'] ) ? sanitize_text_field( $_POST['text'] ) : '';

		if ( $text === '' ) {
			wp_send_json_error( $this->resource->error( 'No text to search was provided.', '', 422 ) );
		}

		// Make API request to Genius and get response.
		$data = $this->api_client->search_songs( $text );

		// Handle errors from the API client.
		if ( is_wp_error( $data ) ) {
			$error_data = $data->get_error_data( self::WP_ERROR_CODE ); // No error formatting here because this was formatted in the API client.

			// TODO: Let's clean this up. Maybe we pass in a resource to WP_Error that we create in the Api_Client?
			wp_send_json_error( $this->resource->error( $error_data['data']['message'], $error_data['data']['description'], $error_data['data']['status_code'] ?? 400 ) );
		}

		// Enforce that we have the required properties in the API response.
		if ( ! $this->validator->response_has_required_properties( $data['data'] ) ) {
			$this->logger->error( 'API request failed. The response is missing required properties.' );
			wp_send_json_error( $this->resource->error( 'The API response is missing required properties.', 'The API response from Genius did not send back all of the song, artist, and image data that was required.', 422 ) );
		}

		$this->logger->info( 'API request successful. Returning the data to the frontend.', $data['data'] );

		// Send the success response back to the frontend.
		wp_send_json_success( $this->resource->success( $data['data'] ), 200 );
	}
}
