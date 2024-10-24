<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Admin\Constants;

/**
 * Constants for the admin menu
 * 
 * Put constants here to be able to easily pass an instance
 * to the classes that need them.
 * 
 * Both Menu and Settings have separate scaffolding functionality.
 * Let's keep them separate by only passing them the constants here.
 */
class Menu {
	const MENU_SLUG = 'plagiarism-checker';
	const CAPABILITY = 'read';

	public function get_menu_slug(): string {
		return self::MENU_SLUG;
	}

	public function get_capability(): string {
		return self::CAPABILITY;
	}
}