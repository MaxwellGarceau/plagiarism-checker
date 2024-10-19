<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Includes;

use Kucrut\Vite;
use Max_Garceau\Plagiarism_Checker\Services\Nonce_Service;

class Enqueue {

	const JS_HANDLE      = 'plagiarism-checker-scripts';
	const JS_OBJECT_NAME = 'plagiarismCheckerAjax';

	/**
	 * @param Nonce_Service $nonce_service
	 */
	public function __construct( private Nonce_Service $nonce_service ) {}

	// TODO: Move into enqueue class
	public function vite(): void {
		Vite\enqueue_asset(
			__DIR__ . '/../../dist',
			'../../src/assets/js/scripts.ts',
			array(
				'handle'           => self::JS_HANDLE,
				'dependencies'     => array(), // Optional script dependencies. Defaults to empty array.
				'css-dependencies' => array(), // Optional style dependencies. Defaults to empty array.
				'css-media'        => 'all', // Optional.
				'css-only'         => false, // Optional. Set to true to only load style assets in production mode.
				'in-footer'        => true, // Optional. Defaults to false.
			)
		);
	}

	public function theme_json() {
		// Enqueue theme.json styles
		wp_enqueue_style(
			'plagiarism-checker-theme-styles',
			plugin_dir_url( __DIR__ ) . '../../theme.json', // adjust the path as per your plugin structure
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}



	public function localize_scripts( string $nonce ): void {
		wp_localize_script(
			self::JS_HANDLE,
			self::JS_OBJECT_NAME,
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => $this->nonce_service->create_nonce(),
			)
		);
	}
}
