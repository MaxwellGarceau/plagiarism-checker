<?php

namespace Tests\Unit\Api;

use Max_Garceau\Plagiarism_Checker\Includes\Api_Client;
use function Brain\Monkey\Functions\expect as monkeyExpect;
use Monolog\Logger;
use Mockery;

beforeEach(
	function () {
		$this->apiToken = 'mocked_api_token';
	}
);

it(
	'sends a valid API request and returns the expected result',
	function () {
		$responseJsonPath = dirname( __DIR__, 2 ) . '/__fixtures/response.json';
		$expected_data    = json_decode( file_get_contents( $responseJsonPath ), true );

		/** @var \Monolog\Logger $loggerMock */
		$loggerMock = Mockery::mock( Logger::class );
		$client     = new Api_Client( $loggerMock, $this->apiToken );

		// Mock add_query_arg to return the expected API URL
		$expected_url = 'https://api.genius.com/search?q=heart';
		monkeyExpect( 'add_query_arg' )
			->once()
			->with(
				array(
					'q' => 'heart',
				),
				'https://api.genius.com/search'
			)
			->andReturn( $expected_url );

		// Mock wp_remote_get to return a successful response
		$response = array(
			'body'     => json_encode( $expected_data ),
			'response' => array(
				'code'    => 200,   // Ensure this is properly structured and not interpreted as a function
				'message' => 'OK',  // Add message to avoid interpreting as an invalid name
			),
		);

		monkeyExpect( 'wp_remote_get' )->once()
			// No way to get this from API
			->with(
				$expected_url,
				array(
					'headers' => array(
						'Authorization' => "Bearer {$this->apiToken}",
					),
					'timeout' => 15,
				)
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
		expect( count( $result ) )->toBe( 10 );

		// Assert that the result matches the expected data
		expect( $result )->toEqualCanonicalizing( $expected_data['response']['hits'] );
	}
)->group( 'wp_brain_monkey' );

it(
	'returns an empty array when the API response is empty',
	function () {
		// This is the empty response from Genius
		$expected_data = array(
			'meta'     => array(
				'status' => 200,
			),
			'response' => array(
				'hits' => array(),
			),
		);

		$response = array(
			'body'     => json_encode( $expected_data ), // empty response
			'response' => array(
				'code'    => 200,   // Ensure this is properly structured and not interpreted as a function
				'message' => 'OK',  // Add message to avoid interpreting as an invalid name
			),
		);

		/** @var \Monolog\Logger $loggerMock */
		$loggerMock = Mockery::mock( Logger::class );
		$client     = new Api_Client( $loggerMock, $this->apiToken );

		// Mock add_query_arg to return the expected API URL
		$expected_url = 'https://api.genius.com/search?q=klfkjadfajdf;kjfd';
		monkeyExpect( 'add_query_arg' )
			->once()
			->with(
				array(
					'q' => 'klfkjadfajdf;kjfd',
				),
				'https://api.genius.com/search'
			)
			->andReturn( $expected_url );

		// Mock wp_remote_get to return an empty response
		monkeyExpect( 'wp_remote_get' )->once()
			->with(
				$expected_url,
				array(
					'headers' => array(
						'Authorization' => "Bearer {$this->apiToken}",
					),
					'timeout' => 15,
				)
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
		expect( $result )->toBe( $expected_data['response']['hits'] );
	}
)->group( 'wp_brain_monkey' );

it(
    'handles an error response from the API',
    function () {
        // Mock wp_remote_get to return an error response
		$expected_data = array(
			'meta'     => array(
				'status' => 500,
			),

			// No response property on failed request
			// 'response' => array(
			// 	'hits' => array(),
			// ),
		);

		$response = array(
			'body'     => json_encode( $expected_data ), // empty response
			'response' => array(
				'code'    => 500,   // Ensure this is properly structured and not interpreted as a function
				// 'message' => '',  // Add message to avoid interpreting as an invalid name
			),
		);

		// Mock add_query_arg to return the expected API URL
		$expected_url = 'https://api.genius.com/search?q=klfkjadfajdf;kjfd';
		
		monkeyExpect( 'add_query_arg' )
			->once()
			->with(
				array(
					'q' => 'klfkjadfajdf;kjfd',
				),
				'https://api.genius.com/search'
			)
			->andReturn( $expected_url );

        // Mock Monolog\Logger
        $loggerMock = Mockery::mock( Logger::class )->makePartial();
        $loggerMock->shouldReceive('error')
            ->once()
            ->with(Mockery::type('string'))
            ->andReturnNull();

        // Create the API client with the logger mock
		/** @var \Monolog\Logger $loggerMock */
		$client = new Api_Client( $loggerMock, $this->apiToken );

		// Mock wp_remote_get to return an empty response
		monkeyExpect( 'wp_remote_get' )->once()
			->with(
				$expected_url,
				array(
					'headers' => array(
						'Authorization' => "Bearer {$this->apiToken}",
					),
					'timeout' => 15,
				)
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
        $result = $client->search_songs('this search will fail');

		// Mock WP_Error
		$wpErrorMock = Mockery::mock('WP_Error');
		$wpErrorMock->shouldReceive('get_error_message')
			->andReturn('The Genius API request failed or returned invalid data.');

        expect($result)->toBeInstanceOf(WP_Error::class);
        expect($result->get_error_message())->toMatch('/^(.*)request failed.*$/i');
    }
)->group('wp_brain_monkey');

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

        // Mock the logger but leave its behavior for other methods intact
        $loggerMock = Mockery::mock(Logger::class)->shouldIgnoreMissing();

        // Expect the logger to be called with the error message
        $loggerMock->shouldReceive('error')
            ->once()
            ->with(
                Mockery::pattern('/missing/i'),
                Mockery::on(function ($context) {
                    return isset($context['Class_Name::method_name']) &&
                           preg_match('/Api_Client::__construct/i', $context['Class_Name::method_name']);
                })
            );

        // Simulate missing API token
        $_ENV['GENIUS_API_TOKEN'] = '';

        // Catch the exception thrown by Api_Client due to missing token
        try {
			/** @var \Monolog\Logger $loggerMock */
            $client = new Api_Client($loggerMock);
        } catch (\Exception $e) {
            expect($e->getMessage())->toContain('API token is missing');
        }

        // Assert that the logger was called with the expected error
        expect($loggerMock)->toHaveReceived('error');

		// We definitely don't want to make a request if the token is missing
		monkeyExpect('wp_remote_get')->never();
    }
)->group('wp_brain_monkey');
