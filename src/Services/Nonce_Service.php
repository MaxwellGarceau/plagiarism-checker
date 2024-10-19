<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Services;

class Nonce_Service {

	const NONCE_KEY = 'plagiarism_checker_nonce';

	public function create_nonce(): string {
		return wp_create_nonce( self::NONCE_KEY );
	}

	/**
	 * Verify the nonce with check_ajax_referer
	 *
	 * @return int|bool From the WP Codex - "1 if the nonce is valid and generated between 0-12 hours ago, 2 if the nonce is valid and generated between 12-24 hours ago. False if the nonce is invalid."
	 */
	public function verify_nonce(): int|bool {
		return check_ajax_referer( self::NONCE_KEY );
	}
}
