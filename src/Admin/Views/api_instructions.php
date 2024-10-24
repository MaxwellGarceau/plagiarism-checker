<p>Enter your Genius.com API token below. This is required for the Plagiarism Checker to function.</p>
<p>Instructions for generating an API token can be found below:</p>
<h2>How to generate an API token:</h2>
<p>1. Go to Genius.com and <a href="https://genius.com/signup" target="_blank">create an account</a>.</p>
<p>2. Visit the <a href="https://genius.com/api-clients/new" target="_blank">API section</a> in your account settings.</p>
<p>3. Fill in the fields and generate a new API token.</p>
<p>4. Copy and paste the "Client Secret" into the field below.</p>
<p class="plagiarism-checker__note">More information on the Genius.com API can be found in the <a href="https://docs.genius.com/#/authentication-h1" target="_blank">developer documentation</a>.</p>
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