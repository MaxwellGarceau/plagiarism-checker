<?php

namespace Max_Garceau\Plagiarism_Checker\Includes;

use Kucrut\Vite;

class Enqueue {
	// TODO: Move into enqueue class
	public function vite(): void {
		Vite\enqueue_asset(
			__DIR__ . '/../../dist',
			'../../src/assets/js/scripts.ts',
			array(
				'handle'           => 'plagiarism-checker-scripts',
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
            plugin_dir_url( __DIR__ ) . '/../../theme.json', // adjust the path as per your plugin structure
            [],
            wp_get_theme()->get( 'Version' )
        );
    }
}
