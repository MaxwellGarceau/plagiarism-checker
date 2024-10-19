<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Includes;

use Max_Garceau\Plagiarism_Checker\Services\Nonce_Service;
use Max_Garceau\Plagiarism_Checker\Services\Nonce_Status;
use Max_Garceau\Plagiarism_Checker\Includes\Api_Client;

class Admin_Ajax {

	/**
	 * @param Nonce_Service $nonce_service
	 */
	public function __construct(
		private readonly Nonce_Service $nonce_service,
		private readonly Api_Client $api_client
	) {}

	public function handle_plagiarism_checker_request(): void {

		// Verify nonce
		if ( $this->nonce_service->verify_nonce() === Nonce_Status::INVALID ) {
			// TODO: Log failure here (should I?)
			wp_send_json_error( 'Invalid or expired nonce.' );
		}

		// Validate and sanitize
		$text = isset( $_POST['text'] ) ? sanitize_text_field( $_POST['text'] ) : '';

		// TODO: Do I need a looser check here?
		if ( $text === '' ) {
			wp_send_json_error( 'No text provided.' );
		}

		// Make API request to Genius and get response
		$data = $this->api_client->search_songs( $text );
		if ( is_wp_error( $data ) ) {
			// TODO: Log failure here
			wp_send_json_error( $data->get_error_message() );
		}

		// Enforce that we have the properties our app requires
		$data = $this->enforce_required_properties( $data );
		if ( is_wp_error( $data ) ) {
			// TODO: Log failure here

			// The request was processed correctly, but there was a discrepancy
			// in the contract between the client and server.
			wp_send_json_error( 'API request failed.', 422 );
		}

		// Process the response and send it back to the frontend
		wp_send_json_success( $data );
	}

	/**
	 * Ensure that the required properties for our API response are present
	 *
	 * TODO: This is probably better off in another class, but let's revisit
	 * that idea in the future when the app is bigger.
	 *
	 * TODO: I'd like to use Symfony's Validator class here, but I'd prefer to
	 * keep the dependencies minimal. However, let's revisit this in the future.
	 */
	private function enforce_required_properties( array $data ): bool {
		// Ensure required properties are present
		$required_properties = array(
			'id',
			'title',
			'primary_artist' => array(
				'name',
				'url',
			),
		);

		$actual_properties = $this->array_keys_recursive( $data );

		// Ensure that all properties from $required_properties are present in $actual_properties
		return count( array_diff( $required_properties, $actual_properties ) ) === 0;
	}

	/**
	 * Loop through an array and return all keys as the same array structure
	 *
	 * TODO: Not sure where to put this. Leaving here for now since it's a helper
	 * that we only need in this class.
	 *
	 * Answer in php.net
	 * https://www.php.net/manual/en/function.array-keys.php#114584
	 */
	private function array_keys_recursive( array $my_array, int $MAXDEPTH = INF, int $depth = 0, array $array_keys = array() ): array {
		if ( $depth < $MAXDEPTH ) {
			++$depth;
			$keys = array_keys( $my_array );
			foreach ( $keys as $key ) {
				if ( is_array( $my_array[ $key ] ) ) {
					$array_keys[ $key ] = $this->array_keys_recursive( $my_array[ $key ], $MAXDEPTH, $depth );
				}
			}
		}

		return $array_keys;
	}
}
