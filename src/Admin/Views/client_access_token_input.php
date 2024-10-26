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
<input type="password" name="plagiarism_checker_api_token" value="<?php echo esc_attr( $token ); ?>" class="regular-text">
