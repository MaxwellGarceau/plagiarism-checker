<?php
/**
 * Client Access Token Input Form
 *
 * This form field displays a password input for the client's API access token.
 *
 * @var string $token The API access token associated with the current user.
 *                    Retrieved from the token storage system and sanitized
 *                    for output in the form. Used to authenticate API requests.
 */
?>

<h2>Client Access Token</h2>

<?php
	if ( ! extension_loaded( 'sodium' ) ): ?>
		<p class="plagiarism-checker__status plagiarism-checker__error" style="color:#dc3232">
			<strong>Error:</strong> You can not use Plagiarism Checker if your server does not support the sodium extension for encryption. Please reach out to your host to ask about enabling "libsodium".
		</p>
		<input type="text" name="" value="Your server does not support sodium encryption" disabled class="regular-text">
	<?php else: ?>
		<input type="password" name="plagiarism_checker_api_token" value="<?php echo esc_attr( $token ); ?>" class="regular-text">
	<?php endif; ?>
