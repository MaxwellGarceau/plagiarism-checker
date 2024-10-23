<?php

declare( strict_types = 1 );

namespace Max_Garceau\Plagiarism_Checker\Admin;

use Max_Garceau\Plagiarism_Checker\Admin\Menu_Constants;
use Max_Garceau\Plagiarism_Checker\Admin\Token_Storage;

class Settings {

    const SETTINGS_SECTION_ID = 'plagiarism_checker_settings_section';
    const NONCE_ACTION = 'plagiarism_checker_save_token';
    const NONCE_NAME = 'plagiarism_checker_nonce';

    private Menu_Constants $constants;
    private Token_Storage $token_storage;

    public function __construct( Menu_Constants $constants, Token_Storage $token_storage ) {
        $this->constants     = $constants;
        $this->token_storage = $token_storage;
    }

    public function plagiarism_checker_settings_page(): void {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'Plagiarism Checker Settings', $this->constants->get_menu_slug() ); ?></h1>
			<?php $this->plagiarism_checker_settings_section_callback() ?>
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

    public function plagiarism_checker_settings_section_callback(): void {
        echo '<p>' . esc_html__( 'Enter your Genius.com API token below. This is required for the Plagiarism Checker to function.', 'plagiarism-checker' ) . '</p>';
        echo '<p>' . esc_html__( 'Instructions for generating an API token can be found below:', 'plagiarism-checker' ) . '</p>';
        echo '<p><strong>' . esc_html__( 'How to generate an API token:', 'plagiarism-checker' ) . '</strong></p>';
        echo '<p>' . esc_html__( '1. Go to Genius.com and create an account.', 'plagiarism-checker' ) . '</p>';
        echo '<p>' . esc_html__( '2. Visit the API section in your account settings.', 'plagiarism-checker' ) . '</p>';
        echo '<p>' . esc_html__( '3. Generate a new API token.', 'plagiarism-checker' ) . '</p>';
        echo '<p>' . esc_html__( '4. Copy and paste the token into the field below.', 'plagiarism-checker' ) . '</p>';
    }

    private function render_api_token_field(): void {
        $user_id = get_current_user_id();
        $token   = $this->token_storage->get_token( $user_id );
        echo '<input type="text" name="plagiarism_checker_api_token" value="' . esc_attr( $token ) . '" class="regular-text">';
    }
}
