<?php

namespace Max_Garceau\Plagiarism_Checker\Admin;

class Notice {
	/**
	 * Render the admin notice for missing table.
	 */
	public function display_error_notice( $message ): void {
		?>
		<div class="notice notice-error is-dismissible">
			<p><?php esc_html_e( $message ); ?></p>
		</div>
		<?php
	}
}