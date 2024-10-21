<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Includes;

use DI\ContainerBuilder;
use Max_Garceau\Plagiarism_Checker\Utilities\Logger_Init;
use Monolog\Logger;

/**
 * Configure PHP DI
 *
 * TODO: Not sure this file should live here, but it's a start
 */
class DI_Container {

	public static function build_container(): \DI\Container {
		$containerBuilder = new ContainerBuilder();

		// Add custom logger definition
		$containerBuilder->addDefinitions(
			[
				Logger::class => function () {
					// Initialize Logger_Init and return the logger instance
					$logger_init = Logger_Init::init();
					return $logger_init->get_logger();
				},
			]
		);

		// Build and return the container
		return $containerBuilder->build();
	}
}
