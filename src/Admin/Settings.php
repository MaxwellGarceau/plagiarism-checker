<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Admin;

use Max_Garceau\Plagiarism_Checker\Admin\Constants\Menu;
use Max_Garceau\Plagiarism_Checker\Admin\Token_Storage;

/**
 * Settings page for the Plagiarism Checker plugin.
 *
 * I would love to use the options API because it's quick, convenient,
 * and secure, but I anticipate the admin page growing and supporting
 * a plethora of API clients. I would prefer to start the admin page
 * without the constraints of the settings API admin system creation.
 *
 * I also like separating the plugin data from the site as it makes debugging,
 * migration, and overall data management easier.
 */
class Settings {

	const NONCE_ACTION = 'plagiarism_checker_save_token';
	const NONCE_NAME   = 'plagiarism_checker_nonce';

	private Menu $constants;
	private Token_Storage $token_storage;

	public function __construct( Menu $constants, Token_Storage $token_storage ) {
		$this->constants     = $constants;
		$this->token_storage = $token_storage;
	}

	/**
	 * TODO: Refactor nonce generation so that we use the Nonce_Service
	 * to generate nonces here as well
	 */
	public function plagiarism_checker_settings_page(): void {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Plagiarism Checker Settings', $this->constants->get_menu_slug() ); ?></h1>
			<?php $this->plagiarism_checker_settings_section_callback(); ?>
			<form method="post" action="admin-post.php">
				<input type="hidden" name="action" value="save_plagiarism_checker_token">
				<?php
				wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );
				$this->render_api_token_field();
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	/**
	 * Callback for the settings section.
	 *
	 * Load the HTML from an html file.
	 */
	public function plagiarism_checker_settings_section_callback(): void {
		require plugin_dir_path( __DIR__ ) . 'Admin/Views/api_instructions.php';
	}

	private function render_api_token_field(): void {
		$user_id = get_current_user_id();
		$token   = $this->token_storage->get_token( $user_id );
		echo '<input type="password" name="plagiarism_checker_api_token" value="' . esc_attr( $token ) . '" class="regular-text">';
	}
}
