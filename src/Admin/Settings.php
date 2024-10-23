<?php

namespace Max_Garceau\Plagiarism_Checker\Admin;

use Max_Garceau\Plagiarism_Checker\Admin\Menu_Constants;

class Settings {

	/**
	 * If we need to make more groups we could break these sections out
	 * into separate classes that we pass to Settings.
	 */
	const SETTINGS_SECTION_ID = 'plagiarism_checker_settings_section';
	const SETTINGS_SECTION_GROUP = 'plagiarism_checker_settings_group';
	const OPTION_NAME = 'plagiarism_checker_api_token';

	public function __construct(
		private Menu_Constants $constants
	) {}

	function plagiarism_checker_settings_page() {
		?>
		<div class="wrap">
			<h1><?php _e('Plagiarism Checker Settings', $this->constants->get_menu_slug()); ?></h1>
			<form method="post" action="options.php">
				<?php
				// This function prints out all hidden setting fields
				settings_fields( self::SETTINGS_SECTION_GROUP );
				do_settings_sections($this->constants->get_menu_slug());
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
	
	public function plagiarism_checker_settings_init() {
		// Register a new setting for storing the API token
		register_setting( self::SETTINGS_SECTION_GROUP, self::OPTION_NAME, [
			'sanitize_callback' => 'sanitize_text_field',
		] );
	
		// Add a new section to the settings page
		add_settings_section(
			self::SETTINGS_SECTION_ID,
			__( 'API Token Settings', $this->constants->get_menu_slug() ),
			[ $this, 'plagiarism_checker_settings_section_callback' ],
			$this->constants->get_menu_slug()
		);
	
		// Add the input field for the API token
		add_settings_field(
			self::OPTION_NAME,
			__( 'Genius.com API Token', $this->constants->get_menu_slug() ),
			[ $this, 'plagiarism_checker_api_token_callback' ],
			$this->constants->get_menu_slug(),
			self::SETTINGS_SECTION_ID
		);
	}
	
	function plagiarism_checker_settings_section_callback() {
		echo '<p>' . __('Enter your Genius.com API token below. This is required for the Plagiarism Checker to function.', 'plagiarism-checker') . '</p>';
		echo '<p>' . __('Instructions for generating an API token can be found below:', 'plagiarism-checker') . '</p>';
		echo '<p><strong>' . __('How to generate an API token:', 'plagiarism-checker') . '</strong></p>';
		echo '<p>' . __('1. Go to Genius.com and create an account.', 'plagiarism-checker') . '</p>';
		echo '<p>' . __('2. Visit the API section in your account settings.', 'plagiarism-checker') . '</p>';
		echo '<p>' . __('3. Generate a new API token.', 'plagiarism-checker') . '</p>';
		echo '<p>' . __('4. Copy and paste the token into the field below.', 'plagiarism-checker') . '</p>';
	}
	
	/**
	 * TODO: Don't use options API
	 * Use own DB table instead
	 */
	function plagiarism_checker_api_token_callback() {
		$token = get_option( self::OPTION_NAME, '' );
		echo '<input type="text" name="' . self::OPTION_NAME . '" value="' . esc_attr($token) . '" class="regular-text">';
	}
	
}