<?php

namespace Max_Garceau\Plagiarism_Checker\Utilities;

use Dotenv\Dotenv;

class Env_Loader {

	public static function load_env( $root_path = null ): void {
		$dotenv = Dotenv::createImmutable( $root_path );
		$dotenv->load();
	}
}
