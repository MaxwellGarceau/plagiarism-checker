// Import files here
import toggleForm from './toggle-form';
import handleFormSubmit from './handle-form-submit';

// Init TS files event listeners
document.addEventListener('DOMContentLoaded', () => {
	toggleForm();

	const submitButton = document.querySelector(
		'#plagiarism-checker__form'
	) as HTMLButtonElement;
	submitButton.addEventListener('submit', handleFormSubmit);
});

// SCSS
// TODO: Is there a better place to load this?
import '../scss/style.scss';
