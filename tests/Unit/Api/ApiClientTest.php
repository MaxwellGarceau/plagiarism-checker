<?php

use Max_Garceau\Plagiarism_Checker\Includes\Api_Client;
use function Brain\Monkey\Functions\expect;
use Monolog\Logger;

it('sends a valid API request and returns the expected result', function () {
    $responseJsonPath = dirname( __DIR__, 2 ) . '/__fixtures/response.json';
    $expected_data = json_decode(file_get_contents($responseJsonPath), true);

    // Mock the wp_remote_post to return a successful response
    $response = [
        'response' => [
            'body' => json_encode($expected_data),
            'code' => 200,
        ]
    ];
    
    expect('wp_remote_get')->once()->andReturn($response);

    /** @var \Monolog\Logger $loggerMock */
    $loggerMock = Mockery::mock(Logger::class);

    $client = new Api_Client($loggerMock);

    $result = $client->search_songs('heart');

    // Assert that $result has 10 entries
    expect(count($result))->toBe(10);

    // Assert that each item in $result matches the expected data
    $index = 0;
    expect($result)->each(function ($item) use ($expected_data, &$index) {
        expect($item)->toMatchArray($expected_data[$index]);
        $index++;
    });
})->group('simulated_wp');

it('returns an empty array when the API response is empty', function () {
    $response = [
        'response' => [
            'body' => json_encode([]), // empty response
            'code' => \WP_Http::OK,
        ]
    ];
    
    expect('wp_remote_get')->once()->andReturn($response);

    /** @var \Monolog\Logger $loggerMock */
    $loggerMock = Mockery::mock(Logger::class);

    $client = new Api_Client($loggerMock);

    $result = $client->search_songs('klfkjadfajdf;kjfd');

    expect($result)->toBe([]);
})->group('simulated_wp');

it('handles an error response from the API', function () {
    // Mock the wp_remote_post to return an error response
    $response = [
        'response' => [
            'code' => \WP_Http::BAD_REQUEST,
        ]
    ];
    
    expect('wp_remote_get')->once()->andReturn($response);

    /** @var \Monolog\Logger $loggerMock */
    $loggerMock = Mockery::mock(Logger::class);

    $client = new Api_Client($loggerMock);

    $result = $client->search_songs('this search will fail');

    expect($result)->toBeInstanceOf(WP_Error::class);
    expect($result->get_error_message())->toMatch("/^(.*)request failed.*$/i");
})->group('simulated_wp');

it('does not sanitize or validate data', function () {
    // The Api_Client is only accessed through Admin_Ajax
    // which already sanitizes and validates the data
})->group('simulated_wp')->skip('This is a note.');

it('throws an error when no api token is set', function () {

    $loggerMock = Mockery::mock(Logger::class)->makePartial();

    // Expect the logger to be called with a specific message
    $loggerMock->shouldReceive('error')
        ->once()
        ->with(Mockery::pattern("/missing/i"), [
            'Class_Name::method_name' => Mockery::pattern('/Api_Client::__construct/i'),
        ]);

    $_ENV['GENIUS_API_TOKEN'] = '';

    /** @var \Monolog\Logger $loggerMock */
    $client = new Api_Client($loggerMock);

    // Assert that the logger was called with the expected message
    expect($loggerMock)->toHaveReceived('error');
})->group('simulated_wp');