<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Includes;

use WP_Error;
use WP_Http;
use Monolog\Logger;

class Api_Client {

	/**
	 * The Genius API URL to search for songs.
	 *
	 * Since we only have one endpoint we need to search,
	 * we're going to keep this simple for now.
	 *
	 * Let's break this out into a method that can accept endpoints
	 * if we need to search more endpoints
	 */
	private const API_URL = 'https://api.genius.com/search';

	/**
	 * The Genius API token.
	 */
	private string $api_token;

	/**
	 * @param Logger $logger
	 */
	private Logger $logger;

	public function __construct(
		Logger $logger
	) {
		$this->logger = $logger;

		// TODO: Set this via a WP menu where users can add their own token
		$this->api_token = $_ENV['GENIUS_API_TOKEN'] ?: '';

		// TODO: Add an admin notice if the token is missing
		if ( $this->api_token === '' ) {
			$this->logger->error(
				'The Genius API token is missing.',
				array(
					'Class_Name::method_name' => __CLASS__ . '::' . __FUNCTION__,
				)
			);
		}
	}

	/**
	 * Makes a GET request to the Genius API.
	 *
	 * @param string $text The text to search for.
	 * @return array|WP_Error The response data or WP_Error on failure.
	 */
	public function search_songs( string $text ): array|WP_Error {
		error_log( 'hi' );
		// Prepare the request URL
		$url = add_query_arg(
			array(
				'q' => $text,
			),
			self::API_URL
		);

		// Set up request headers
		$args = array(
			'headers' => array(
				'Authorization' => 'Bearer ' . $this->api_token,
			),
			'timeout' => 15,
		);

		// Make the request
		$response = wp_remote_get( $url, $args );

		// Check if the request failed
		if ( is_wp_error( $response ) ) {
			return $response; // Return WP_Error
		}

		// Get the response body and decode JSON
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, true );

		// Check for errors in the API response
		if ( wp_remote_retrieve_response_code( $response ) !== WP_Http::OK || ! isset( $data['response']['hits'] ) ) {
			$this->logger->error(
				'The Genius API request returned a non 200 response.',
				array(
					'search_text'   => $text,
					'status_code'   => wp_remote_retrieve_response_code( $response ),
					'response_data' => $data,
				)
			);
			return new WP_Error( 'genius_api_error', 'The Genius API request failed or returned invalid data.' );
		}

		return $data['response']['hits'];
	}
}