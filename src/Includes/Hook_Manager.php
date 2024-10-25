<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Includes;

use Max_Garceau\Plagiarism_Checker\Main;

class Hook_Manager {
	public function add_actions( Main $main ): void {
		// Enqueue the Vite assets.
		add_action( 'wp_enqueue_scripts', [ $main->enqueue, 'vite' ] );
		add_action( 'admin_enqueue_scripts', [ $main->enqueue, 'admin_styles' ] );	

		add_action( 'wp_footer', [ $main->form_controller, 'render' ] );
		add_action( 'wp_enqueue_scripts', [ $main->enqueue, 'localize_scripts' ] );

		// Only logged in users can make these requests. Non logged in users can't use this plugin.
		add_action( 'wp_ajax_plagiarism_checker', [ $main->admin_ajax, 'handle_plagiarism_checker_request' ] );

		/**
		 * Admin Menu
		 */
		add_action('admin_menu', function() use ( $main ): void {
			$main->menu->plagiarism_checker_add_admin_menu( $main->settings );
		});

		add_action( 'admin_post_save_plagiarism_checker_token', [ $main->form_handler, 'handle_form_submission' ] );

		add_action( 'admin_init', [ $main->table_manager, 'maybe_show_admin_notice' ] );
	}
}
