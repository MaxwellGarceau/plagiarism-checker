<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Admin;

use Max_Garceau\Plagiarism_Checker\Admin\Menu_Constants;
use Max_Garceau\Plagiarism_Checker\Admin\Settings;

class Menu {

	const SETTINGS_PAGE_CALLBACK = 'plagiarism_checker_settings_page';

	public function __construct(
		private Menu_Constants $constants
	) {}

	/**
	 * Add the admin menu for the plugin.
	 */
	public function plagiarism_checker_add_admin_menu( Settings $settings ): void {
		add_menu_page(
			// Leave text domain in string format instead of constant
			__( 'Plagiarism Checker', 'plagiarism-checker' ), // Page title
			__( 'Plagiarism Checker', 'plagiarism-checker' ), // Menu title
			$this->constants->get_capability(),                                         // Capability - open to subscribers because each user must add their own token
			$this->constants->get_menu_slug(),                           // Menu slug
			[ $settings, self::SETTINGS_PAGE_CALLBACK ],             // Callback function
			'dashicons-admin-tools',                        // Icon
			80                                              // Position
		);
	}    
}