<div id="plagiarism-checker" class="plagiarism-checker plagiarism-checker--closed">
	<button id="plagiarism-checker__toggle" class="plagiarism-checker__toggle">
		Toggle Form
	</button>
	<form id="plagiarism-checker__form" class="plagiarism-checker__form">
		<label for="plagiarism-checker__input" class="plagiarism-checker__label">Enter Lyrics:</label>
		<input 
		type="text" 
		id="plagiarism-checker__input" 
		class="plagiarism-checker__input" 
		name="lyric-input" 
		placeholder="Type your lyrics here..." 
		required 
		/>
	  
		<button 
		type="submit" 
		id="plagiarism-checker__submit-button" 
		class="plagiarism-checker__submit-button"
		>
		Check for Plagiarism
		</button>
		<?php
		if ( isset( $_POST['api_token_not_set'] ) && $_POST['api_token_not_set'] === 'true' ) : ?>
			<p class="plagiarism-checker__error">The Genius API token is missing. Please add it in the plugin settings.</p>
			<?php
		endif;
		?>
	</form>
  
	<div id="plagiarism-checker__results" class="plagiarism-checker__results">
		<span class="plagiarism-checker__span">Matching Songs:</label>
		<div id="plagiarism-checker__textarea" class="plagiarism-checker__textarea">Matching songs go here...</div>
	</div>
	</div>
	