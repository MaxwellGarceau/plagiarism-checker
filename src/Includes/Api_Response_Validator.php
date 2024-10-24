<?php

declare( strict_types=1 );

namespace Max_Garceau\Plagiarism_Checker\Includes;

class Api_Response_Validator {

	/**
	 * Validate that the required properties are present in each result object in the API response.
	 *
	 * @param array $data The actual response data to validate (an array of results).
	 * @return bool True if all result objects have the required properties, false otherwise.
	 */
	public function response_has_required_properties( array $data ): bool {
		$required_properties = array(
			'url',
			'title',
			'primary_artist' => array( 'name', 'url' ),
			'header_image_thumbnail_url',
		);

		// Loop over each result object and validate its properties.
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
				// If value is an array, check that the corresponding key exists and is an array.
				if ( ! isset( $data[ $key ] ) || ! is_array( $data[ $key ] ) ) {
					return false;
				}

				// Recursively check nested properties.
				if ( ! $this->check_required_properties( $value, $data[ $key ] ) ) {
					return false;
				}
			} else {
				// Check if the key exists in the data.
				if ( ! array_key_exists( $value, $data ) ) {
					return false;
				}
			}
		}

		return true;
	}
}
