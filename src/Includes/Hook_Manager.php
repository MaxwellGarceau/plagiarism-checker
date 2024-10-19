<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Includes;

use Max_Garceau\Plagiarism_Checker\Main;

class Hook_Manager {
	public function add_actions( Main $main ): void {
		// Enqueue the Vite assets.
		add_action( 'wp_enqueue_scripts', array( $main->enqueue, 'vite' ) );
		add_action( 'wp_enqueue_scripts', array( $main->enqueue, 'theme_json' ) );

		add_action( 'wp_footer', array( $main->form_controller, 'render' ) );
		add_action( 'wp_enqueue_scripts', array( $main->enqueue, 'localize_scripts' ) );

		// Only logged in users can make these requests. Non logged in users can't use this plugin.
		add_action( 'wp_ajax_plagiarism_checker', array( $main->admin_ajax, 'handle_plagiarism_checker_request' ) );
	}
}
