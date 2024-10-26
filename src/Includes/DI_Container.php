<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Includes;

use DI\Container;
use DI\ContainerBuilder;
use Max_Garceau\Plagiarism_Checker\Utilities\Logger_Init;
use Monolog\Logger;
use Max_Garceau\Plagiarism_Checker\Includes\Api_Client\Client;
use Psr\Container\ContainerInterface;
use Max_Garceau\Plagiarism_Checker\Admin\Token_Storage;
use Max_Garceau\Plagiarism_Checker\Includes\Api_Client\Resource;
use Max_Garceau\Plagiarism_Checker\Admin\Constants\DB as DB_Constants;
use Max_Garceau\Plagiarism_Checker\Admin\Notice;
use Max_Garceau\Plagiarism_Checker\Utilities\Encryption\Encryption_Disabled_Strategy;
use Max_Garceau\Plagiarism_Checker\Utilities\Encryption\Libsodium_Encryption_Strategy;
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
		$containerBuilder->addDefinitions(
			[
				wpdb::class => function () {
					global $wpdb;
					return $wpdb;
				},
			]
		);

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
		 * output a new Client instance with the token.
		 *
		 * However, I think instantiating the Client with the
		 * token directly in the DI container is a better approach
		 * because it's simpler, requires less verbosity of code,
		 * and is easier to understand.
		 *
		 * We can switch this up later if necessary, but i like this approach.
		 *
		 * @param Logger $logger
		 * @param string $api_key
		 *
		 * @return Client
		 */
		$containerBuilder->addDefinitions(
			[
				Client::class => function ( ContainerInterface $c ) {
					$logger        = $c->get( Logger::class );
					$token_storage = $c->get( Token_Storage::class );
					$user_id       = get_current_user_id();

					// Try to get the API token
					$api_token = $token_storage->get_token( $user_id );

					// If the API token is missing, handle it gracefully
					if ( empty( $api_token ) ) {
						$logger->warning(
							'API token is missing. Please enter your API token in the settings.',
							[ 'user_id' => $user_id ]
						);

						// Return an empty token, let the user get a 401 if they use the client
						$api_token = '';
					}

					// Return the Client instance with the logger and (possibly empty) API token
					return new Client( $logger, new Resource(), $api_token  );
				},
			]
		);

		/**
		 * Send the Token_Storage class the correct encryption class
		 * 
		 * If the server supports libsodium then use the Libsodium_Encryption_Strategy class
		 * Otherwise, we'll send an Encryption_Disabled_Strategy class that does not encrypt.
		 */
		$containerBuilder->addDefinitions([
			Token_Storage::class => function ( ContainerInterface $c ) {
				
				// If the server does not support libsodium, display an admin notice
				if ( ! extension_loaded( 'sodium' ) ) {
					add_action(
						'admin_notices',
						function () use ( $c ) {
							$c->get( Notice::class )->display_error_notice( __( 'Error: The encryption library "Sodium" is not available on your site! This means the sensitive data you enter into Plagiarism Checker will not be encrypted!!!', 'plagiarism-checker' ) );
						}
					);
				}

				$encryption = extension_loaded( 'sodium' )
					? new Libsodium_Encryption_Strategy()
					: new Encryption_Disabled_Strategy();
				$wpdb = $c->get( wpdb::class );
				$db_constants = new DB_Constants();

				return new Token_Storage( $wpdb, $db_constants, $encryption );
			}
		]);

		// Build and return the container
		return $containerBuilder->build();
	}
}
