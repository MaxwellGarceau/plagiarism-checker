<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Includes;

use Max_Garceau\Plagiarism_Checker\Services\Nonce_Service;
use Max_Garceau\Plagiarism_Checker\Services\Nonce_Status;
use Monolog\Logger;

class Admin_Ajax {

	/**
	 * @param Nonce_Service $nonce_service The service used to validate nonces.
	 * @param Api_Client    $api_client The client responsible for making requests to the Genius API.
	 * @param Logger        $logger A logger to capture any issues.
	 * @param Resource      $resource A resource instance to handle the formatting of responses.
	 */
	public function __construct(
		private readonly Nonce_Service $nonce_service,
		private readonly Api_Client $api_client,
		private readonly Logger $logger,
		private readonly Resource $resource
	) {}

	public function handle_plagiarism_checker_request(): void {

		// Verify nonce
		if ( $this->nonce_service->verify_nonce() === Nonce_Status::INVALID ) {
			$this->logger->error( 'Invalid or expired nonce.' );
			wp_send_json_error( $this->resource->error( 'Invalid or expired nonce.', '', 403 ) );
		}

		// Validate and sanitize
		$text = isset( $_POST['text'] ) ? sanitize_text_field( $_POST['text'] ) : '';

		// TODO: Do I need a looser check here?
		if ( $text === '' ) {
			wp_send_json_error( $this->resource->error( 'No text to search was provided.', '', 422 ) );
		}

		// Make API request to Genius and get response
		$data = $this->api_client->search_songs( $text );

		// Handle the error response from the API client
		if ( is_wp_error( $data ) ) {
			$error_data = $data->get_error_data();
			wp_send_json_error( $error_data, $error_data['status_code'] ?? 400 );
		}

		// Enforce that we have the properties our app requires
		if ( ! $this->response_has_required_properties( $data['data'] ) ) {
			$this->logger->error( 'API request failed. The response is missing required properties.' );

			// The request was processed correctly, but there was a discrepancy
			// in the contract between the client and server.
			wp_send_json_error( $this->resource->error( 'The API response is missing required properties.', '', 422 ) );
		}

		$this->logger->info( 'API request successful. Returning the data to the frontend.', $data );

		// Send the success response back to the frontend
		wp_send_json_success( $data['data'] );
	}

	/**
	 * Validate that the required properties are present in each result object
	 * in the API response.
	 *
	 * TODO: This is probably better off in another class, but let's revisit
	 * that idea in the future when the app is bigger.
	 *
	 * TODO: I'd like to use Symfony's Validator class here, but I'd prefer to
	 * keep the dependencies minimal. However, let's revisit this in the future.
	 *
	 * @param array $data The actual response data to validate (an array of results).
	 * @return bool True if all result objects have the required properties, false otherwise.
	 */
	private function response_has_required_properties( array $data ): bool {
		error_log( print_r( $data, true ) );
		// Define the required properties structure for each result object
		$required_properties = [
			'url',
			'title',
			'primary_artist' => array( 'name', 'url' ),
		];

		// Loop over each result object and validate its properties
		foreach ( $data as $resource ) {
			if ( ! $this->check_required_properties( $required_properties, $resource['result'] ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Check if all required properties exist in a single result object.
	 *
	 * @param array $required_properties The required properties structure.
	 * @param array $data The result object from the API.
	 * @return bool True if all required properties are present, false otherwise.
	 */
	private function check_required_properties( array $required_properties, array $data ): bool {
		foreach ( $required_properties as $key => $value ) {
			if ( is_array( $value ) ) {
				// If value is an array, check that the corresponding key exists and is an array
				if ( ! isset( $data[ $key ] ) || ! is_array( $data[ $key ] ) ) {
					return false;
				}

				// Recursively check nested properties
				if ( ! $this->check_required_properties( $value, $data[ $key ] ) ) {
					return false;
				}
			} else {
				// Check if the key exists in the data
				if ( ! array_key_exists( $value, $data ) ) {
					return false;
				}
			}
		}

		return true;
	}
}
