<?php

namespace Tests\Integration;

use Max_Garceau\Plagiarism_Checker\Includes\Api_Client\Admin_Ajax;
use Max_Garceau\Plagiarism_Checker\Services\Nonce_Service;
use Max_Garceau\Plagiarism_Checker\Includes\Api_Client\Client;
use Max_Garceau\Plagiarism_Checker\Services\Nonce_Status;
use Max_Garceau\Plagiarism_Checker\Includes\Api_Client\Resource;
use Max_Garceau\Plagiarism_Checker\Includes\Api_Client\Api_Response_Validator;
use Monolog\Logger;
use Mockery;
use Brain\Monkey;
use function Brain\Monkey\Functions\when;

beforeEach(
	function () {
		// Set up Brain Monkey
		Monkey\setUp();

		$this->wp_error_code = 'genius_api_error';

		// Mock dependencies - don't worry about API client
		/** @var Nonce_Service $nonce_service */
		$this->nonce_service = Mockery::mock( Nonce_Service::class );

		/** @var Client $api_client */
		$this->api_client = Mockery::mock( Client::class );

		/** @var Logger $logger */
		$this->logger = Mockery::mock( Logger::class );

		$this->resource = new Resource();

		$this->validator = new Api_Response_Validator();

		// Initialize the class
		$this->admin_ajax = new Admin_Ajax( $this->nonce_service, $this->api_client, $this->logger, $this->resource, $this->validator );

		// Mock WordPress helper functions to throw exceptions, simulating die
		// when wp_send_json_* are called
		when( 'wp_send_json_error' )->alias(
			function ( array|string $message, $status_code = null ) {
				if ( is_array( $message ) ) {
					$message = json_encode( $message );
				}
				throw new \RuntimeException( "wp_send_json_error called: {$message}", $status_code ?? 500 );
			}
		);
		when( 'wp_send_json_success' )->alias(
			function ( $data ) {
				throw new \RuntimeException( 'wp_send_json_success called', 200 );
			}
		);

		// Mock WordPress helper functions
		when( 'sanitize_text_field' )->returnArg();
		when( 'is_wp_error' )->alias(
			function ( $data ) {
				return is_a( $data, 'WP_Error' );
			}
		);
	}
);

afterEach(
	function () {
		// Tear down Brain Monkey
		Monkey\tearDown();
	}
);

it(
	'returns error for invalid nonce',
	function () {
		// Set up nonce service to return invalid status
		$this->nonce_service
		->shouldReceive( 'verify_nonce' )
		->andReturn( Nonce_Status::INVALID );

		
		// Mock the validator to confirm it checks for required properties
		$this->validator = Mockery::mock( Api_Response_Validator::class );
		$this->validator
		->shouldNotReceive( 'response_has_required_properties' );

		// Expect the logger to log the error
		$this->logger
		->shouldReceive( 'error' )
		->once()
		->with( 'Invalid or expired nonce.' );

		// Mock the Resource class's error method to ensure it is called with correct arguments
		$resource_return = array(
			'success'     => false,
			'data' => array(
				'message'     => 'Invalid or expired nonce.',
				'description' => '',
				'status_code' => 403,
			),
		);

		// Expect the wp_send_json_error to be called, which will throw an exception
		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'wp_send_json_error called: ' . json_encode( $resource_return ) );

		// Call the method
		$this->admin_ajax->handle_plagiarism_checker_request();

		// Verify wp_send_json_error was called
		Monkey\Functions\expect( 'wp_send_json_error' )->once()->with( 'Invalid or expired nonce.' );
	}
)->group( 'wp_brain_monkey' );


it(
	'returns error if no text provided',
	function () {
		// Set up nonce service to return valid status
		$this->nonce_service
		->shouldReceive( 'verify_nonce' )
		->andReturn( Nonce_Status::VALID );

		
		// Mock the validator to confirm it checks for required properties
		$this->validator = Mockery::mock( Api_Response_Validator::class );
		$this->validator
		->shouldNotReceive( 'response_has_required_properties' );

		// Set up global $_POST without 'text'
		$_POST = array();

		// Expect the wp_send_json_error to be called, which will throw an exception
		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'wp_send_json_error called' );

		// Call the method
		$this->admin_ajax->handle_plagiarism_checker_request();

		// Verify wp_send_json_error was called
		Monkey\Functions\expect( 'wp_send_json_error' )->once()->with( 'No text to search was provided.' );
	}
)->group( 'wp_brain_monkey' );


it(
	'handles non 200 API responses correctly',
	function () {
		$search_text = 'test text';
		// Set up nonce service to return valid status
		$this->nonce_service
		->shouldReceive( 'verify_nonce' )
		->andReturn( Nonce_Status::VALID );

		// Set up global $_POST with text
		$_POST = array( 'text' => $search_text );

		$formatted_genius_response = array(
			'message'     => 'Here would be the Genius message.',
			'description' => 'This is a longer response from Genius regarding the request failure.',
			'status_code' => 400,
		);

		$wp_error_message = 'The Genius API request failed - ' . $formatted_genius_response['message'];

		// Simulate an API error response
		$wp_error = Mockery::mock( 'WP_Error' );
		$wp_error
		->shouldReceive( 'get_error_message' )
		->with( $this->wp_error_code )
		->andReturn( $wp_error_message );
		$wp_error
		->shouldReceive( 'get_error_data' )
		->with( $this->wp_error_code )
		->andReturn( [
			'succcess' => false,
			'data' => $formatted_genius_response
		] );

		// Expect the API client to return WP_Error
		$this->api_client
		->shouldReceive( 'search_songs' )
		->with( $search_text )
		->andReturn( $wp_error );

		// Mock the Resource class's error method to ensure it is called with correct arguments

		// We're formatting the resource in the Api_Client\Client
		// As far as Admin_Ajax is concerned, no resource activity happens here
		$resource_return = [
			'success' => false,
			'data' => $formatted_genius_response,
		];

		// We don't get to validator until we have a response
		$this->validator = Mockery::mock( Api_Response_Validator::class );
		$this->validator
		->shouldNotReceive( 'response_has_required_properties' );

		// We're logging the error in the Api_Client\Client
		// As far as Admin_Ajax is concerned, the error is never logged
		$this->logger
		->shouldNotReceive( 'error' );

		// Expect the wp_send_json_success to be called, which will throw an exception
		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'wp_send_json_error called' );

		// Call the method
		$this->admin_ajax->handle_plagiarism_checker_request();

		// Verify wp_send_json_error was called
		Monkey\Functions\expect( 'wp_send_json_error' )->once()->with( $resource_return );
	}
)->group( 'wp_brain_monkey' );


it(
	'throws validation error if response is missing required properties',
	function () {
		// Set up nonce service to return valid status
		$this->nonce_service
		->shouldReceive( 'verify_nonce' )
		->andReturn( Nonce_Status::VALID );

		// Set up global $_POST with text
		$_POST = array( 'text' => 'test text' );

		// Simulate a response missing required properties
		$response_data = array( 'data' => [ array( 'result' => array( 'title' => 'Song Title' ) ) ] );

		// Expect the API client to return the response
		$this->api_client
		->shouldReceive( 'search_songs' )
		->with( 'test text' )
		->andReturn( $response_data );

		// Mock the Resource class's error method to ensure it is called with correct arguments
		$resource_return = array(
			'success'     => false,
			'data' => array(
				'message'     => 'The API response is missing required properties.',
				'description' => 'The API response from Genius did not send back all of the song, artist, and image data that was required.',
				'status_code' => 422,
			),
		);

		// Expect the logger to log the error
		$this->logger
		->shouldReceive( 'error' )
		->once()
		->with( 'API request failed. The response is missing required properties.' );

		// Expect the wp_send_json_success to be called, which will throw an exception
		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'wp_send_json_error called' );

		// Call the method
		// Validator will return false and fail the request
		$this->admin_ajax->handle_plagiarism_checker_request();

		// Verify wp_send_json_error was called with status 422
		Monkey\Functions\expect( 'wp_send_json_error' )->once()->with( $resource_return );
	}
)->group( 'wp_brain_monkey' );


it(
	'returns success if request is valid and response has required properties',
	function () {
		// Set up nonce service to return valid status
		$this->nonce_service
		->shouldReceive( 'verify_nonce' )
		->andReturn( Nonce_Status::VALID );

		// Set up global $_POST with text
		$_POST = array( 'text' => 'test text' );

		// Simulate a valid response with all required properties
		$response_data = array(
			'data' => [
				array(
					'result' => array(
						'url'                        => 'https://example.com',
						'title'                      => 'Song Title',
						'primary_artist'             => array(
							'name' => 'Artist Name',
							'url'  => 'https://artist.com',
						),
						'header_image_thumbnail_url' => 'https://example.com/image.jpg',
					),
				),
			],
		);

		// Expect the API client to return the response
		$this->api_client
		->shouldReceive( 'search_songs' )
		->with( 'test text' )
		->andReturn( $response_data );

		// Expect the logger to log the success
		$this->logger
		->shouldReceive( 'info' )
		->once()
		->with( 'API request successful. Returning the data to the frontend.', $response_data['data'] );

		// Expect the wp_send_json_success to be called, which will throw an exception.
		// If this fails wp_send_json_success was not called. Check beforeEach for alias.
		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'wp_send_json_success called' );

		// Call the method
		// Validator will not return false
		$this->admin_ajax->handle_plagiarism_checker_request();

		// Verify wp_send_json_success was called with the correct data
		Monkey\Functions\expect( 'wp_send_json_success' )->once()->with( $response_data );
	}
)->group( 'wp_brain_monkey' );

// TODO: Let's return a resource from admin ajax
// We validate properties inside the resource and throw errors if anything is wrong
it(
	'always returns a resource to the FE',
	function () {
	}
)->group( 'wp_brain_monkey' )->skip( 'Feature implemented - !!!test not yet written!!!' );
