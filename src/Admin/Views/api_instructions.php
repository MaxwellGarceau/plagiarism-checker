<?php
/**
 * View for the API instructions page.
 *
 * @package PlagiarismChecker\Admin\Views
 */

/**
 * Get the URL for the admin images directory.
 */
function get_admin_img_dir_url( $img = '' ) {
	return plugin_dir_url( __DIR__ ) . 'assets/imgs/' . $img;
}
?>
<section class="plagiarism-checker__api-instructions-container">
	<p>Enter your Genius.com API token below. This is required for the Plagiarism Checker to function.</p>
	<p>Instructions for generating an API token can be found below:</p>
	<h2>How to generate an API token:</h2>
	<ol class="plagiarism-checker__list">
		<li>Go to Genius.com and <a href="https://genius.com/signup" target="_blank">create an account</a>.</li>
		<li>Visit the <a href="https://genius.com/api-clients/new" target="_blank">API section</a> in your account settings.</li>
		<li>
			Fill in the "App Name" and "App Website URL" fields. <span class="plagiarism-checker__highlight">Use <code style="display: inline-block">https://example.com/</code> for your App Website URL.</span> Then click "save".
			<img class="plagiarism-checker__img" src="<?php echo get_admin_img_dir_url( 'new-api-client.png' ); ?>"/>
		</li>
		<li>Find the section that says <span class="plagiarism-checker__highlight">"Client Access Token"</span> and click the button that says <span class="plagiarism-checker__highlight">"Generate Access Token"</span>.
			<img class="plagiarism-checker__img" src="<?php echo get_admin_img_dir_url( 'generate-access-token.png' ); ?>"/>
			<img class="plagiarism-checker__img" src="<?php echo get_admin_img_dir_url( 'copy-access-token.png' ); ?>"/>
		</li>
		<li>Copy and paste the <span class="plagiarism-checker__highlight">"Client Access Token"</span> into the Client Access Token field below.
		</li>
	</ol>
	<?php
	if ( isset( $_GET['status'] ) && $_GET['status'] === 'error' ) {
		?>
			<p class="plagiarism-checker__status plagiarism-checker__error" style="color:#dc3232"><strong>Error:</strong> There was an error saving your token. Please try again.</p>
			<p class="plagiarism-checker__note"><strong>Note:</strong> If you need to regenerate your token, you can do so in the <a href="https://genius.com/api-clients/new" target="_blank">API section</a> of your Genius.com account settings.</p>
		<?php
	}

	if ( isset( $_GET['status'] ) && $_GET['status'] === 'success' ) {
		?>
			<p class="plagiarism-checker__status plagiarism-checker__success" style="color:#00a0d2"><strong>Success:</strong> Your token has been saved.</p>
		<?php
	}
	?>
<section>