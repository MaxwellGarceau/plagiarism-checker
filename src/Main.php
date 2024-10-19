<?php

namespace Max_Garceau\Plagiarism_Checker;

use Kucrut\Vite;
use Max_Garceau\Plagiarism_Checker\Views\Form_Controller;
use Max_Garceau\Plagiarism_Checker\Includes\Admin_Ajax;

/**
 * Loads and coordinates activities of the plugin
 *
 * @class Main The entry point of the plugin
 */
class Main {

	/**
	 * @var Form_Controller $form_controller
	 */
	private Form_Controller $form_controller;

	/**
	 * @var Admin_Ajax $admin_ajax
	 */
	private Admin_Ajax $admin_ajax;

	public function __construct(
		Form_Controller $form_Controller,
		Admin_Ajax $admin_ajax
	) {
		$this->form_controller = $form_Controller;
		$this->admin_ajax      = $admin_ajax;
	}

	/**
	 * Initializes the plugin
	 */
	public function init() {
		// Enqueue the Vite assets.
		add_action(
			'wp_enqueue_scripts',
			function (): void {
				Vite\enqueue_asset(
					__DIR__ . '/../dist',
					'../src/assets/js/scripts.ts',
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
		);

		add_action( 'wp_footer', array( $this->form_controller, 'render' ) );
		add_action( 'wp_enqueue_scripts', array( $this->admin_ajax, 'localize_scripts' ) );

		// Only logged in users can make these requests. Non logged in users can't use this plugin.
		add_action( 'wp_ajax_plagiarism_checker', array( $this->admin_ajax, 'handle_plagiarism_checker_request' ) );
	}
}
