<?php

namespace Tests\Unit\Api;

use Max_Garceau\Plagiarism_Checker\Includes\Api_Client\Client;
use function Brain\Monkey\Functions\expect as monkeyExpect;
use Max_Garceau\Plagiarism_Checker\Includes\Api_Client\Resource;
use Monolog\Logger;
use function Brain\Monkey\Functions\when;
use Mockery;

beforeEach(
	function () {
		$this->apiToken = 'mocked_api_token';
		$this->wp_error_code = 'genius_api_error';
	}
);

it(
	'sends a valid API request and returns the expected result',
	function () {

		$responseJsonPath = dirname( __DIR__, 2 ) . '/__fixtures/response.json';
		$expected_data    = json_decode( file_get_contents( $responseJsonPath ), true );

		/** @var \Monolog\Logger $loggerMock */
		$loggerMock = Mockery::mock( Logger::class );
		$client     = new Client( $loggerMock, new Resource(), $this->apiToken );

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
			// No way to get this from API
			->with(
				$expected_url,
				[
					'headers' => [
						'Authorization' => "Bearer {$this->apiToken}",
					],
					'timeout' => 15,
				]
			)
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

		$result = $client->search_songs( 'heart' );

		// Assert that $result has 10 entries
		expect( count( $result['data'] ) )->toBe( 10 );

		// Assert that the result matches the expected data
		expect( $result['data'] )->toEqualCanonicalizing( $expected_data['response']['hits'] );
	}
)->group( 'wp_brain_monkey' );

it(
	'returns an empty array when the API response is empty',
	function () {
		// This is the empty response from Genius
		$expected_data = [
			'meta'     => [
				'status' => 200,
			],
			'response' => [
				'hits' => [],
			],
		];

		$response = [
			'body'     => json_encode( $expected_data ), // empty response
			'response' => [
				'code'    => 200,   // Ensure this is properly structured and not interpreted as a function
				'message' => 'OK',  // Add message to avoid interpreting as an invalid name
			],
		];

		/** @var \Monolog\Logger $loggerMock */
		$loggerMock = Mockery::mock( Logger::class );
		$client     = new Client( $loggerMock, new Resource(), $this->apiToken );

		// Mock add_query_arg to return the expected API URL
		$expected_url = 'https://api.genius.com/search?q=klfkjadfajdf;kjfd';
		monkeyExpect( 'add_query_arg' )
			->once()
			->with(
				[
					'q' => 'klfkjadfajdf;kjfd',
				],
				'https://api.genius.com/search'
			)
			->andReturn( $expected_url );

		// Mock wp_remote_get to return an empty response
		monkeyExpect( 'wp_remote_get' )->once()
			->with(
				$expected_url,
				[
					'headers' => [
						'Authorization' => "Bearer {$this->apiToken}",
					],
					'timeout' => 15,
				]
			)
			->andReturn( $response );

		// Mock wp_remote_retrieve_body to return the empty body part of the response
		monkeyExpect( 'wp_remote_retrieve_body' )
			->once()
			->with( $response )
			->andReturn( json_encode( $expected_data ) );

		// Mock wp_remote_retrieve_response_code to return a 200 status
		monkeyExpect( 'wp_remote_retrieve_response_code' )
			->once()
			->with( $response )
			->andReturn( 200 );

		$result = $client->search_songs( 'klfkjadfajdf;kjfd' );

		// Assert that $result is an empty array
		expect( $result['data'] )->toBe( $expected_data['response']['hits'] );
	}
)->group( 'wp_brain_monkey' );

it(
    'handles an error response from the API',
    function () {
        // Mock wp_remote_get to return an error response
		$expected_data = [
			'meta'     => [
				'status' => 500,
			],
		];

		$response = [
			'body'     => json_encode( $expected_data ), // empty response
			'response' => [
				'code'    => 500,   // Ensure this is properly structured and not interpreted as a function
			],
		];

		// Mock add_query_arg to return the expected API URL
		$search_text = 'this_search_will_fail';
		$expected_url = 'https://api.genius.com/search?q=' . $search_text;
		
		monkeyExpect( 'add_query_arg' )
			->once()
			->with(
				[
					'q' => $search_text,
				],
				'https://api.genius.com/search'
			)
			->andReturn( $expected_url );

		// Mock WP_Error
		$formatted_genius_response = [
			'error' => 'Here would be the Genius message.',
			'error_description' => 'This is a longer response from Genius regarding the request failure.',
			'status_code' => 400,
		];

		$wp_error_message = 'The Genius API request failed - ' . $formatted_genius_response['error'];

		// Simulate an API error response
		$wp_error = Mockery::mock( 'WP_Error' );
		$wp_error
		->shouldReceive( 'get_error_message' )
		->with( $this->wp_error_code )
		->andReturn( $wp_error_message );
		$wp_error
		->shouldReceive( 'get_error_data' )
		->with( $this->wp_error_code )
		->andReturn( $formatted_genius_response );

        // Mock the logger but leave its behavior for other methods intact
        $loggerMock = Mockery::mock(Logger::class)->shouldIgnoreMissing();

        // Expect the logger to be called with the error message
        $loggerMock->shouldReceive('error')
            ->once()
            ->with(
                Mockery::pattern('/non 200 response/i'),
                Mockery::on(function ($context) use ($search_text) {
                    return $context['search_text'] === $search_text &&
							$context['status_code'] === 500;
                })
            );

        // Create the API client with the logger mock
		/** @var \Monolog\Logger $loggerMock */
		$client = new Client( $loggerMock, new Resource(), $this->apiToken );

		// Mock wp_remote_get to return an empty response
		monkeyExpect( 'wp_remote_get' )->once()
			->with(
				$expected_url,
				[
					'headers' => [
						'Authorization' => "Bearer {$this->apiToken}",
					],
					'timeout' => 15,
				]
			)
			->andReturn( $response );

		// Mock wp_remote_retrieve_body to return the empty body part of the response
		monkeyExpect( 'wp_remote_retrieve_body' )
			->once()
			->with( $response )
			->andReturn( json_encode( $expected_data ) );

		// Mock wp_remote_retrieve_response_code to return a 200 status
		monkeyExpect( 'wp_remote_retrieve_response_code' )
			->once()
			->with( $response )
			->andReturn( 500 );

        // Call the method and assert the results
        $result = $client->search_songs($search_text);

		// Assert that the result is an instance of WP_Error
        expect($result)->toBeInstanceOf(\WP_Error::class);	

		// Assert that the logger was called with the expected error
		expect($loggerMock)->toHaveReceived('error');
    }
)->group('wp_brain_monkey');

it(
	'does not sanitize or validate data',
	function () {
		// The Client is only accessed through Admin_Ajax
		// which already sanitizes and validates the data
	}
)->group( 'wp_brain_monkey' )->skip( 'This is a note.' );

it(
    'throws an error when no api token is set',
    function () {

		when('admin_url')->justReturn('https://example.com/wp-admin/');

        // Mock the logger but leave its behavior for other methods intact
        $loggerMock = Mockery::mock(Logger::class)->shouldIgnoreMissing();

        // Expect the logger to be called with the error message
        $loggerMock->shouldNotReceive('error');

        // Simulate missing API token
        $_ENV['GENIUS_API_TOKEN'] = '';

		// Mock WP_Error
		Mockery::mock( 'WP_Error' );

		/** @var \Monolog\Logger $loggerMock */
		$client = new Client($loggerMock, new Resource(), '');

		$result = $client->search_songs('no-api-token-entered');

        // Assert that the logger was called with the expected error
        expect($result)->toBeInstanceOf(\WP_Error::class);

		// We definitely don't want to make a request if the token is missing
		monkeyExpect('wp_remote_get')->never();
    }
)->group('wp_brain_monkey');
