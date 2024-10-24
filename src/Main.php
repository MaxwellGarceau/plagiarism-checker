<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker;

use Max_Garceau\Plagiarism_Checker\Views\Form_Controller;
use Max_Garceau\Plagiarism_Checker\Includes\Api_Client\Admin_Ajax;
use Max_Garceau\Plagiarism_Checker\Includes\Enqueue;
use Max_Garceau\Plagiarism_Checker\Includes\Hook_Manager;
use Max_Garceau\Plagiarism_Checker\Admin\Menu;
use Max_Garceau\Plagiarism_Checker\Admin\Settings;
use Max_Garceau\Plagiarism_Checker\Admin\Form_Handler;
use Max_Garceau\Plagiarism_Checker\Admin\Table_Manager;

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
	 * @property-read Menu            $menu
	 * @property-read Settings        $settings
	 * @property-read Form_Handler    $form_handler
	 * @property-read Table_Manager   $table_manager
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
		public readonly Hook_Manager $hook_manager,
		public readonly Menu $menu,
		public readonly Settings $settings,
		public readonly Form_Handler $form_handler,
		public readonly Table_Manager $table_manager
	) {}

	/**
	 * Initializes the plugin
	 */
	public function init() {
		// Init WP Hooks
		$this->hook_manager->add_actions( $this );
	}
}
