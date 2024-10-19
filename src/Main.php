<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker;

use Max_Garceau\Plagiarism_Checker\Views\Form_Controller;
use Max_Garceau\Plagiarism_Checker\Includes\Admin_Ajax;
use Max_Garceau\Plagiarism_Checker\Includes\Enqueue;
use Max_Garceau\Plagiarism_Checker\Includes\Hook_Manager;

/**
 * Loads and coordinates activities of the plugin
 *
 * @class Main The entry point of the plugin
 */
class Main {

	/**
	 * @property-read Form_Controller $form_controller
	 * @property-read Admin_Ajax      $admin_ajax
	 * @property-read Enqueue         $enqueue
	 * @property-read Hook_Manager    $hook_manager
	 *
	 * NOTE: Inject classes here and then pass them to
	 * Hook_Manager for the hooks initialization.
	 *
	 * Helps centralization init of the plugin, but
	 * let's revisit this idea if no longer serves us
	 * in the future
	 */
	public function __construct(
		public readonly Form_Controller $form_controller,
		public readonly Admin_Ajax $admin_ajax,
		public readonly Enqueue $enqueue,
		public readonly Hook_Manager $hook_manager
	) {}

	/**
	 * Initializes the plugin
	 */
	public function init() {
		$this->hook_manager->add_actions( $this );
	}
}
