<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Includes\Api_Client;

class Resource {

	/**
	 * Format success response.
	 *
	 * @param array $data The data to send back to the frontend.
	 * @return array The formatted response.
	 */
	public function success( array $data ): array {
		return $this->response( true, $data );
	}

	/**
	 * Format error response.
	 * 
	 * We use this for organizing arguments to WP_Error AND for sending
	 * responses to the FE
	 *
	 * @param string $message The error message.
	 * @param string $description A more detailed description of the error.
	 * @param int $status_code HTTP status code.
	 * @return array The formatted error response.
	 */
	public function error( string $message, string $description = '', int $status_code = 400 ): array {
		return $this->response( false, [
			'message' => $message,
			'description' => $description,
			'status_code' => $status_code,
		] );
	}

	/**
	 * Format response for return to FE.
	 * 
	 * Same structure for FE to determine success or failure.
	 * 
	 * @param array $data The data to send back to the frontend.
	 */
	private function response( $success, $data ): array {
		return [
			'success' => $success,
			'data' => $data
		];
	}
}
