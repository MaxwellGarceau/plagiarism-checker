<?php

namespace Max_Garceau\Plagiarism_Checker\Views;

use Max_Garceau\Plagiarism_Checker\Views\Interface_Renderable;

/**
 * Renders the form for the plugin
 *
 * Keep the PHP logic here to separate the FE HTML from the BE logic
 */
class Form_Controller implements Interface_Renderable {
	public function render(): void {
		require plugin_dir_path( __FILE__ ) . '../Views/form.html';
	}
}
