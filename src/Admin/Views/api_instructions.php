<p>Enter your Genius.com API token below. This is required for the Plagiarism Checker to function.</p>
<p>Instructions for generating an API token can be found below:</p>
<p><strong>How to generate an API token:</strong></p>
<p>1. Go to Genius.com and create an account.</p>
<p>2. Visit the API section in your account settings.</p>
<p>3. Generate a new API token.</p>
<p>4. Copy and paste the token into the field below.</p>
<?php
	if ( isset( $_GET['status'] ) && $_GET['status'] === 'error' ) {
		?>
		<p style="color:#dc3232"><strong>Error:</strong> There was an error saving your token. Please try again.</p>
		<p><strong>Note:</strong> If you need to regenerate your token, you can do so in the API section of your Genius.com account settings.</p>
		<?php
	}

	if ( isset( $_GET['status'] ) && $_GET['status'] === 'success' ) {
		?>
		<p style="color:#00a0d2"><strong>Success:</strong> Your token has been saved.</p>
		<?php
	}
?>