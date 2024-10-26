<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Utilities\Encryption;

interface Encryption_Strategy_Interface {
	public function encrypt( string $data ): string;
	public function decrypt( string $encrypted_data ): string;
}
