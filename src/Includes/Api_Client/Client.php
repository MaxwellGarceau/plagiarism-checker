<?php

declare( strict_types=1 );

namespace Max_Garceau\Plagiarism_Checker\Includes\Api_Client;

use WP_Error;
use Monolog\Logger;
use Max_Garceau\Plagiarism_Checker\Includes\Api_Client\Resource;

class Client {

	/**
	 * The Genius API URL to search for songs.
	 */
	private const API_URL = 'https://api.genius.com/search';
	private const STATUS_OK = 200;

	// TODO: This exists in the Api_Client\Client class too
	// Need to consolidate
	private const WP_ERROR_CODE = 'genius_api_error';

	private \Closure $wp_error_factory;

	/**
	 * @param Logger   $logger    A logger instance to capture any issues.
	 * @param string   $api_token The API token required to access the Genius API.
	 * @param Resource $resource  A resource instance to handle the formatting of responses.
	 * @param callable $wp_error_factory A factory function to create WP_Error instances.
	 */
	public function __construct(
		private Logger $logger,
		private Resource $resource,
		private string $api_token = '',
		\Closure $wp_error_factory = null
	) {
		/**
		 * Creating WP_Error objects via factory to allow for unit testing
		 */
		$this->wp_error_factory = $wp_error_factory ?: function ( $code, $message, $data ) {
            return new WP_Error( $code, $message, $data );
        };
	}

	/**
	 * Makes a GET request to the Genius API.
	 *
	 * @param string $text The text to search for.
	 * @return array|WP_Error The response data or WP_Error on failure.
	 */
	public function search_songs( string $text ): array|WP_Error {
		// Early return if no API token is set.
		if ( $this->api_token === '' ) {
			$menu_url = add_query_arg(
				array( 'page' => 'plagiarism-checker', 'action' => 'edit' ),
				admin_url( 'post.php' )
			);

			return new WP_Error(
				self::WP_ERROR_CODE,
				'The Genius API token is not set.',
				$this->resource->error(
					'The Genius API token is not set.',
					'The Genius API token is not set. Please set the token in the <a href="' . $menu_url . '">admin menu</a>.',
					401 ),
			);
		}

		/**
		 * NOTE: We aren't validating or sanitizing the data here because
		 * we're already doing that in Admin_Ajax. We should consider adding
		 * sanitization if we change the design in the future.
		 */

		// Prepare the request URL.
		$url = add_query_arg( array( 'q' => $text ), self::API_URL );

		// Set up request headers.
		$args = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->api_token,
			),
			'timeout' => 15,
		);

		// Make the request.
		$response = wp_remote_get( $url, $args );

		// Check if the request failed.
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		// Get the response body and decode JSON.
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		// Check for errors in the API response.
		if ( wp_remote_retrieve_response_code( $response ) !== self::STATUS_OK || ! isset( $data['response']['hits'] ) ) {
			$message = 'The Genius API request failed - ' . ( $data['error'] ?? 'unknown error' );

			// Create a WP_Error from our factory
			$wp_error = call_user_func($this->wp_error_factory,
				self::WP_ERROR_CODE, $message, $this->resource->error(
					$data['error'] ?? 'unknown error',
					$data['error_description'] ?? '',
					wp_remote_retrieve_response_code( $response )
				)
			);

			$this->logger->error( $wp_error->get_error_message( self::WP_ERROR_CODE ), $wp_error->get_error_data( self::WP_ERROR_CODE ) );

			return $wp_error;
		}

		// Return the success response using the Resource class.
		return $this->resource->success( $data['response']['hits'] );
	}
}
