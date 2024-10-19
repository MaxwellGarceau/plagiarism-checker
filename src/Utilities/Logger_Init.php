<?php

namespace Max_Garceau\Plagiarism_Checker\Utilities;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\NullHandler;
use Monolog\Formatter\LineFormatter;
use Monolog\ErrorHandler;
// use Dotenv\Dotenv;

class Logger_Init {

	protected $logger;
	protected $log_channel;
	protected $log_level;
	protected $log_file;

	public function __construct( $log_channel = null, $log_level = null, $log_file = null ) {
		// // Try to load .env file and default to hardcoded values if not available
		// $this->load_env();

		// Use passed values or fall back to .env values, and default values as the last resort
		$this->log_channel = $log_channel ?: getenv( 'LOG_CHANNEL' ) ?: 'plagiarism_checker';
		$this->log_level   = $log_level ?: getenv( 'LOG_LEVEL' ) ?: $this->get_default_log_level();
		$this->log_file    = $log_file ?: getenv( 'LOG_FILE' ) ?: WP_CONTENT_DIR . '/debug.log';

		$this->init_logger();
	}

	// protected function load_env() {
	// Load the .env file using Symfony Dotenv component
	// $dotenv = Dotenv::createImmutable( dirname( __DIR__, 2 ) );
	// $env_path = WP_PLUGIN_DIR . '/plagiarism-checker/.env';

	// Check if the .env file exists before loading it
	// if (file_exists($env_path)) {
	// $dotenv->load($env_path);
	// }
	// }

	protected function init_logger(): void {
		$this->logger = new Logger( $this->log_channel );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ) {
			// Create log handler with default file or WP_DEBUG_LOG path
			$handler = new StreamHandler( $this->log_file, $this->log_level );

			// Optional: Customize log formatting (you can adjust this to your needs)
			$formatter = new LineFormatter( null, null, true, true );
			$handler->setFormatter( $formatter );

			$this->logger->pushHandler( $handler );
		} else {
			// Do not log on production environment or if WP_DEBUG is off
			$this->logger->pushHandler( new NullHandler() );
		}

		// Register Monolog error handler to catch PHP errors, warnings, etc.
		ErrorHandler::register( $this->logger );
	}

	protected function get_default_log_level() {
		// Default log level to error and above in production
		return ( defined( 'WP_ENV' ) && getenv( 'WP_ENV' ) === 'production' ) ? \Monolog\Level::Error : \Monolog\Level::Debug;
	}

	public function get_logger(): Logger {
		return $this->logger;
	}

	public static function init( $log_channel = null, $log_level = null, $log_file = null ): self {
		return new self( $log_channel, $log_level, $log_file );
	}
}
