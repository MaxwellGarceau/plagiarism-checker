<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker;

use Max_Garceau\Plagiarism_Checker\Views\Form_Controller;
use Max_Garceau\Plagiarism_Checker\Includes\Admin_Ajax;
use Max_Garceau\Plagiarism_Checker\Includes\Enqueue;

/**
 * Loads and coordinates activities of the plugin
 *
 * @class Main The entry point of the plugin
 */
class Main {

	/**
	 * @param Form_Controller $form_controller
	 * @param Admin_Ajax      $admin_ajax
	 * @param Enqueue         $enqueue
	 */
	public function __construct(
		private readonly Form_Controller $form_controller,
		private readonly Admin_Ajax $admin_ajax,
		private readonly Enqueue $enqueue
	) {}

	/**
	 * Initializes the plugin
	 */
	public function init() {
		// Enqueue the Vite assets.
		add_action( 'wp_enqueue_scripts', array( $this->enqueue, 'vite' ) );
		add_action( 'wp_enqueue_scripts', array( $this->enqueue, 'theme_json' ) );

		add_action( 'wp_footer', array( $this->form_controller, 'render' ) );
		add_action( 'wp_enqueue_scripts', array( $this->enqueue, 'localize_scripts' ) );

		// Only logged in users can make these requests. Non logged in users can't use this plugin.
		add_action( 'wp_ajax_plagiarism_checker', array( $this->admin_ajax, 'handle_plagiarism_checker_request' ) );
	}
}
