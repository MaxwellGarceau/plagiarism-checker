<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Services;

/**
 * By default WP returns mixed types from nonces.
 * This enum simplifies the return type so our other
 * code can process it more simply.
 */
enum Nonce_Status: int {
	case VALID         = 1;
	case VALID_BUT_OLD = 2;
	case INVALID       = 0;
}

class Nonce_Service {

	private const NONCE_KEY = 'plagiarism_checker_nonce';

	public function create_nonce(): string {
		return wp_create_nonce( self::NONCE_KEY );
	}

	/**
	 * Verify the nonce with check_ajax_referer
	 *
	 * @return int|bool From the WP Codex - "1 if the nonce is valid and generated between 0-12 hours ago, 2 if the nonce is valid and generated between 12-24 hours ago. False if the nonce is invalid."
	 */
	public function verify_nonce(): Nonce_Status {
		$result = check_ajax_referer( self::NONCE_KEY );

		// Use enum to avoid returning a mixed type
		if ( $result === false ) {
			return Nonce_Status::INVALID;
		}

		return match ( $result ) {
			1 => Nonce_Status::VALID,
			2 => Nonce_Status::VALID_BUT_OLD,
			default => Nonce_Status::INVALID,  // Fallback, in case wp_verify_nonce returns an unexpected value
		};
	}
}
