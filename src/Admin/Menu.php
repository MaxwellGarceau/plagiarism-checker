<?php

namespace Max_Garceau\Plagiarism_Checker\Admin;

class Menu {
	/**
	 * Add the admin menu for the plugin.
	 */
	public function plagiarism_checker_add_admin_menu() {
		add_menu_page(
			__('Plagiarism Checker', 'plagiarism-checker'), // Page title
			__('Plagiarism Checker', 'plagiarism-checker'), // Menu title
			'read',                                         // Capability - open to subscribers because each user must add their own token
			'plagiarism-checker',                           // Menu slug
			'plagiarism_checker_settings_page',             // Callback function
			'dashicons-admin-tools',                        // Icon
			80                                              // Position
		);
	}    
}