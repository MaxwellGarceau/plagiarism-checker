<?php

namespace Tests\Unit\Api;

use Max_Garceau\Plagiarism_Checker\Includes\Api_Client;
use function Brain\Monkey\Functions\expect as monkeyExpect;
use Monolog\Logger;
use Mockery;

// Set up Brain Monkey before each test
beforeEach(function () {
	parent::setUp();
});

// Tear down Brain Monkey after each test
afterEach(function () {
	parent::tearDown();
});

it(
    'sends a valid API request and returns the expected result',
    function () {
        $responseJsonPath = dirname( __DIR__, 2 ) . '/__fixtures/response.json';
        $expected_data    = json_decode( file_get_contents( $responseJsonPath ), true );

		/** @var \Monolog\Logger $loggerMock */
		$loggerMock = Mockery::mock( Logger::class );

		// Inject the logger mock into the Api_Client
		$client = new Api_Client( $loggerMock );

        // Mock add_query_arg to return the expected API URL
        $expected_url = 'https://api.genius.com/search?q=heart';
        monkeyExpect( 'add_query_arg' )
            ->once()
            ->with(
                [
                    'q' => 'heart',
                ],
                'https://api.genius.com/search'
            )
            ->andReturn( $expected_url );

        // Mock wp_remote_get to return a successful response
        $response = [
            'body'     => json_encode( $expected_data ),
            'response' => [
                'code'    => 200,   // Ensure this is properly structured and not interpreted as a function
                'message' => 'OK',  // Add message to avoid interpreting as an invalid name
            ],
        ];

        monkeyExpect( 'wp_remote_get' )->once()
			// ->with( $expected_url, [
			// 	'headers' => [
			// 		'Authorization' => 'Bearer YOUR_API_TOKEN', // Replace with your actual token or mock this
			// 	],
			// 	'timeout' => 15,
			// ])
            ->andReturn( $response );

        // Mock wp_remote_retrieve_body to return the body part of the response
        monkeyExpect( 'wp_remote_retrieve_body' )
            ->once()
            ->with( $response )
            ->andReturn( json_encode( $expected_data ) );

        // Mock wp_remote_retrieve_response_code to return a 200 status
        monkeyExpect( 'wp_remote_retrieve_response_code' )
            ->once()
            ->with( $response )
            ->andReturn( 200 );

        // Mock WP_Error class (since we're not loading WP Core)
        Mockery::mock( 'alias:WP_Error' );

        $result = $client->search_songs( 'heart' );

        // Assert that $result has 10 entries
        expect( count( $result ) )->toBe( 10 );

		// Assert that the result matches the expected data
		expect( $result )->toEqualCanonicalizing( $expected_data['response']['hits'] );
    }
)->group( 'wp_brain_monkey' );

it(
	'returns an empty array when the API response is empty',
	function () {
		$response = array(
			'response' => array(
				'body' => json_encode( array() ), // empty response
				'code' => \WP_Http::OK,
			),
		);

		expect( 'wp_remote_get' )->once()->andReturn( $response );

		/** @var \Monolog\Logger $loggerMock */
		$loggerMock = Mockery::mock( Logger::class );

		$client = new Api_Client( $loggerMock );

		$result = $client->search_songs( 'klfkjadfajdf;kjfd' );

		expect( $result )->toBe( array() );
	}
)->group( 'wp_brain_monkey' );

it(
	'handles an error response from the API',
	function () {
		// Mock the wp_remote_post to return an error response
		$response = array(
			'response' => array(
				'code' => \WP_Http::BAD_REQUEST,
			),
		);

		expect( 'wp_remote_get' )->once()->andReturn( $response );

		/** @var \Monolog\Logger $loggerMock */
		$loggerMock = Mockery::mock( Logger::class );

		$client = new Api_Client( $loggerMock );

		$result = $client->search_songs( 'this search will fail' );

		expect( $result )->toBeInstanceOf( WP_Error::class );
		expect( $result->get_error_message() )->toMatch( '/^(.*)request failed.*$/i' );
	}
)->group( 'wp_brain_monkey' );

it(
	'does not sanitize or validate data',
	function () {
		// The Api_Client is only accessed through Admin_Ajax
		// which already sanitizes and validates the data
	}
)->group( 'wp_brain_monkey' )->skip( 'This is a note.' );

it(
	'throws an error when no api token is set',
	function () {

		$loggerMock = Mockery::mock( Logger::class )->makePartial();

		// Expect the logger to be called with a specific message
		$loggerMock->shouldReceive( 'error' )
		->once()
		->with(
			Mockery::pattern( '/missing/i' ),
			array(
				'Class_Name::method_name' => Mockery::pattern( '/Api_Client::__construct/i' ),
			)
		);

		$_ENV['GENIUS_API_TOKEN'] = '';

		/** @var \Monolog\Logger $loggerMock */
		$client = new Api_Client( $loggerMock );

		// Assert that the logger was called with the expected message
		expect( $loggerMock )->toHaveReceived( 'error' );
	}
)->group( 'wp_brain_monkey' );
