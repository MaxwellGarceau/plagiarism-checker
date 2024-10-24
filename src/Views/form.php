<section id="plagiarism-checker" class="plagiarism-checker plagiarism-checker--closed">
	<div class="wp-block-button plagiarism-checker__toggle" id="plagiarism-checker__toggle">
		<button class="plagiarism-checker__button button button-primary wp-block-button__link wp-element-button">
			Toggle Form
		</button>
	</div>
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
	
		<div class="wp-block-button">
			<button 
			type="submit" 
			id="plagiarism-checker__submit-button" 
			class="plagiarism-checker__button plagiarism-checker__submit-button button button-primary wp-block-button__link wp-element-button"
			>
			Check for Plagiarism
			</button>
		</div>
		<?php
		if ( isset( $_POST['api_token_not_set'] ) && $_POST['api_token_not_set'] === 'true' ) : ?>
			<p class="plagiarism-checker__error">The Genius API token is missing. Please add it in the plugin settings.</p>
			<?php
		endif;
		?>
	</form>

	<div id="plagiarism-checker__results-container" class="plagiarism-checker__results-container">
		<span class="plagiarism-checker__span">Matching Songs:</label>
		<div id="plagiarism-checker__textarea" class="plagiarism-checker__textarea">Matching songs will display here...</div>
	</div>
</section>
	