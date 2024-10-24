<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Includes;

use DI\Container;
use DI\ContainerBuilder;
use Max_Garceau\Plagiarism_Checker\Utilities\Logger_Init;
use Monolog\Logger;
use Max_Garceau\Plagiarism_Checker\Includes\Api_Client;
use Psr\Container\ContainerInterface;
use Max_Garceau\Plagiarism_Checker\Admin\Token_Storage;
use wpdb;

/**
 * Configure PHP DI
 *
 * TODO: Not sure this file should live here, but it's a start
 */
class DI_Container {

	private static ?Container $container = null;

	// Initialize and return the container
	public static function get_container(): Container {
		if ( self::$container === null ) {
			self::$container = self::build_container();
		}
		return self::$container;
	}

	public static function build_container(): \DI\Container {
		$containerBuilder = new ContainerBuilder();

		/**
		 * Add definitions for the wpdb global variable
		 * Use the global $wpdb object when trying to autoresolve
		 * 
		 * @return wpdb
		 */
		$containerBuilder->addDefinitions([
			wpdb::class => function () {
				global $wpdb;
				return $wpdb;
			},
		]);

		/**
		 * Add custom logger definition
		 */
		$containerBuilder->addDefinitions(
			[
				Logger::class => function () {
					// Initialize Logger_Init and return the logger instance
					$logger_init = Logger_Init::init();
					return $logger_init->get_logger();
				},
			]
		);

		/**
		 * Create the API client with the users API credentials
		 * 
		 * I thought about taking the factory pattern approach, perform
		 * the logic to get the API access token in the factory, and
		 * output a new Api_Client instance with the token.
		 * 
		 * However, I think instantiating the Api_Client with the
		 * token directly in the DI container is a better approach
		 * because it's simpler, requires less verbosity of code,
		 * and is easier to understand.
		 * 
		 * We can switch this up later if necessary, but i like this approach.
		 * 
		 * @param Logger $logger
		 * @param string $api_key
		 * 
		 * @return Api_Client
		 */
		$containerBuilder->addDefinitions(
			[
				Api_Client::class => function ( ContainerInterface $c ) {
					try {
						$logger = $c->get( Logger::class );
						$token_storage = $c->get( Token_Storage::class );
						$user_id = get_current_user_id();
	
						$api_token = $token_storage->get_token( $user_id );
	
						if ( empty( $api_token ) ) {
							throw new \Exception( __( 'API token is missing. Please enter your API token in the settings.', 'plagiarism-checker' ) );
						}
					} catch ( \Exception $e ) {
						$c->get( Logger::class )->info( $e->getMessage(), [
							'user_id' => $user_id,
						] );

						// Fail gracefully, let a user get a 401 error if they try to use the API
						$api_token = '';
					}

					return new Api_Client( $logger, $api_token );
				},
			]
		);

		// Build and return the container
		return $containerBuilder->build();
	}
}
