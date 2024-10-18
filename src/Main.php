<?php

namespace Max_Garceau\Plagiarism_Checker;

use Max_Garceau\Plagiarism_Checker\Views\Form_Controller;

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

	public function __construct(
		Form_Controller $form_Controller
	) {
		$this->form_controller = $form_Controller;
	}

	/**
	 * Initializes the plugin
	 */
	public function init() {
		add_action( 'wp_footer', [ $this->form_controller, 'render' ] );
	}
}
