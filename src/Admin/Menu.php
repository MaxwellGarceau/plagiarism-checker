<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Admin;

use Max_Garceau\Plagiarism_Checker\Admin\Constants\Menu as Constants;
use Max_Garceau\Plagiarism_Checker\Admin\Settings;

class Menu {

	const SETTINGS_PAGE_CALLBACK = 'plagiarism_checker_settings_page';
	const CAPABILITY = 'read';

	public function __construct(
		private Constants $constants
	) {}

	/**
	 * Add the admin menu for the plugin.
	 */
	public function plagiarism_checker_add_admin_menu( Settings $settings ): void {
		add_menu_page(
			// Leave text domain in string format instead of constant
			__( 'Plagiarism Checker', 'plagiarism-checker' ), // Page title
			__( 'Plagiarism Checker', 'plagiarism-checker' ), // Menu title
			self::CAPABILITY,                                         // Capability - open to subscribers because each user must add their own token
			$this->constants->get_menu_slug(),                           // Menu slug
			[ $settings, self::SETTINGS_PAGE_CALLBACK ],             // Callback function
			'dashicons-admin-tools',                        // Icon
			80                                              // Position
		);
	}
}