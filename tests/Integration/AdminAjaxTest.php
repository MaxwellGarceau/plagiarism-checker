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

		// Mock dependencies - don't worry about API client
		$this->nonce_service = Mockery::mock( Nonce_Service::class );
		$this->api_client    = Mockery::mock( Client::class );
		$this->logger        = Mockery::mock( Logger::class );
		$this->resource      = Mockery::mock( Resource::class );
		$this->validator     = Mockery::mock( Api_Response_Validator::class );

		// Initialize the class
		$this->admin_ajax = new Admin_Ajax( $this->nonce_service, $this->api_client, $this->logger, $this->resource, $this->validator );

		// Mock WordPress helper functions to throw exceptions, simulating die
		// when wp_send_json_* are called
		when( 'wp_send_json_error' )->alias(
			function ( $message, $status_code = null ) {
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

test(
	'returns error for invalid nonce',
	function () {
		// Set up nonce service to return invalid status
		$this->nonce_service
		->shouldReceive( 'verify_nonce' )
		->andReturn( Nonce_Status::INVALID );

		// Expect the logger to log the error
		$this->logger
		->shouldReceive( 'error' )
		->once()
		->with( 'Invalid or expired nonce.' );

		// Expect the wp_send_json_error to be called, which will throw an exception
		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'wp_send_json_error called: Invalid or expired nonce.' );

		// Call the method
		$this->admin_ajax->handle_plagiarism_checker_request();

		// Verify wp_send_json_error was called
		Monkey\Functions\expect( 'wp_send_json_error' )->once()->with( 'Invalid or expired nonce.' );
	}
)->group( 'wp_brain_monkey' );


test(
	'returns error if no text provided',
	function () {
		// Set up nonce service to return valid status
		$this->nonce_service
		->shouldReceive( 'verify_nonce' )
		->andReturn( Nonce_Status::VALID );

		// Set up global $_POST without 'text'
		$_POST = [];

		// Expect the wp_send_json_success to be called, which will throw an exception
		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'wp_send_json_error called' );

		// Call the method
		$this->admin_ajax->handle_plagiarism_checker_request();

		// Verify wp_send_json_error was called
		Monkey\Functions\expect( 'wp_send_json_error' )->once()->with( 'No text provided.' );
	}
)->group( 'wp_brain_monkey' );


test(
	'handles API errors correctly',
	function () {
		// Set up nonce service to return valid status
		$this->nonce_service
		->shouldReceive( 'verify_nonce' )
		->andReturn( Nonce_Status::VALID );

		// Set up global $_POST with text
		$_POST = [ 'text' => 'test text' ];

		// Simulate an API error response
		$wp_error = Mockery::mock( 'WP_Error' );
		$wp_error
		->shouldReceive( 'get_error_message' )
		->andReturn( 'API request failed' );
		$wp_error
		->shouldReceive( 'get_error_data' )
		->andReturn( [] );

		// Expect the API client to return WP_Error
		$this->api_client
		->shouldReceive( 'search_songs' )
		->with( 'test text' )
		->andReturn( $wp_error );

		// Expect the logger to log the error
		$this->logger
		->shouldReceive( 'error' )
		->once()
		->with(
			'API request failed',
			[
				'wp_error_message' => 'API request failed',
				'api_response'     => [],
			]
		);

		// Expect the wp_send_json_success to be called, which will throw an exception
		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'wp_send_json_error called' );

		// Call the method
		$this->admin_ajax->handle_plagiarism_checker_request();

		// Verify wp_send_json_error was called
		Monkey\Functions\expect( 'wp_send_json_error' )->once()->with( 'API request failed' );
	}
)->group( 'wp_brain_monkey' );


test(
	'validates API response for missing required properties',
	function () {
		// Set up nonce service to return valid status
		$this->nonce_service
		->shouldReceive( 'verify_nonce' )
		->andReturn( Nonce_Status::VALID );

		// Set up global $_POST with text
		$_POST = [ 'text' => 'test text' ];

		// Simulate a response missing required properties
		$response_data = [ [ 'result' => [ 'title' => 'Song Title' ] ] ];

		$this->api_client
		->shouldReceive( 'search_songs' )
		->with( 'test text' )
		->andReturn( $response_data ); // Set the return value as the response data

		// Expect the API client to return the response
		$this->api_client
		->shouldReceive( 'search_songs' )
		->with( 'test text' )
		->andReturn( $response_data );

		// Expect the logger to log the error
		$this->logger
		->shouldReceive( 'error' )
		->once()
		->with( 'API request failed. The response is missing required properties.' );

		// Expect the wp_send_json_success to be called, which will throw an exception
		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'wp_send_json_error called' );

		// Call the method
		$this->admin_ajax->handle_plagiarism_checker_request();

		// Verify wp_send_json_error was called with status 422
		Monkey\Functions\expect( 'wp_send_json_error' )->once()->with( 'API request failed.', 422 );
	}
)->group( 'wp_brain_monkey' );


test(
	'returns success if request is valid and response has required properties',
	function () {
		// Set up nonce service to return valid status
		$this->nonce_service
		->shouldReceive( 'verify_nonce' )
		->andReturn( Nonce_Status::VALID );

		// Set up global $_POST with text
		$_POST = [ 'text' => 'test text' ];

		// Simulate a valid response with all required properties
		$response_data = [
			'data' => [
				[
					'result' => [
						'url'            => 'https://example.com',
						'title'          => 'Song Title',
						'primary_artist' => [
							'name' => 'Artist Name',
							'url'  => 'https://artist.com',
						],
						'header_image_thumbnail_url' => 'https://example.com/image.jpg',
					],
				],
			]
		];

		// Expect the API client to return the response
		$this->api_client
		->shouldReceive( 'search_songs' )
		->with( 'test text' )
		->andReturn( $response_data );

		// Mock the validator to confirm it checks for required properties
		$this->validator
		->shouldReceive( 'response_has_required_properties' )
		->once() // Expect the method to be called once
		->with( $response_data['data'] )
		->andReturn( true ); // Assume the response has the required properties

		// Expect the logger to log the success
		$this->logger
		->shouldReceive( 'info' )
		->once()
		->with( 'API request successful. Returning the data to the frontend.', $response_data['data'] );

		// Expect the wp_send_json_success to be called, which will throw an exception
		$this->expectException( \RuntimeException::class );
		$this->expectExceptionMessage( 'wp_send_json_success called' );

		// Call the method
		$this->admin_ajax->handle_plagiarism_checker_request();

		// Verify wp_send_json_success was called with the correct data
		Monkey\Functions\expect( 'wp_send_json_success' )->once()->with( $response_data );
	}
)->group( 'wp_brain_monkey' );
