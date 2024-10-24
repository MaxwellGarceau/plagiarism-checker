<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Includes;

use Kucrut\Vite;
use Max_Garceau\Plagiarism_Checker\Services\Nonce_Service;

class Enqueue {

	private const JS_SCRIPTS_HANDLE = 'plagiarism-checker-scripts';
	private const JS_OBJECT_NAME    = 'plagiarismCheckerAjax';

	/**
	 * @param Nonce_Service $nonce_service
	 */
	public function __construct( private readonly Nonce_Service $nonce_service ) {}

	// TODO: Move into enqueue class
	public function vite(): void {
		Vite\enqueue_asset(
			$this->get_plugin_base_path( '/dist' ),
			// Must be relative to root directory. Kucrut\Vite generates the full path in the plugin.
			'src/assets/js/scripts.ts',
			[
				'handle'           => self::JS_SCRIPTS_HANDLE,
				'dependencies'     => [], // Optional script dependencies. Defaults to empty array.
				'css-dependencies' => [], // Optional style dependencies. Defaults to empty array.
				'css-media'        => 'all', // Optional.
				'css-only'         => false, // Optional. Set to true to only load style assets in production mode.
				'in-footer'        => true, // Optional. Defaults to false.
			]
		);
	}

	public function theme_json(): void {
		// Enqueue theme.json styles
		wp_enqueue_style(
			'plagiarism-checker-theme-styles',
			$this->get_plugin_base_url( '/theme.json' ), // adjust the path as per your plugin structure
			[],
			wp_get_theme()->get( 'Version' )
		);
	}

	public function localize_scripts(): void {
		wp_localize_script(
			self::JS_SCRIPTS_HANDLE,
			self::JS_OBJECT_NAME,
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => $this->nonce_service->create_nonce(),
			]
		);
	}

	public function admin_styles() {
		// Enqueue theme.json styles
		wp_enqueue_style(
			'plagiarism-checker-admin-styles',
			$this->get_plugin_base_url( '/src/Admin/assets/style.css' ), // adjust the path as per your plugin structure
			[],
			wp_get_theme()->get( 'Version' )
		);
	}

	private function get_plugin_base_path( string $path = '' ): string {
		return plugin_dir_path( __FILE__ ) . '../..' . $path;
	}

	private function get_plugin_base_url( string $path = '' ): string {
		return plugin_dir_url( __FILE__ ) . '../..' . $path;
	}
}
