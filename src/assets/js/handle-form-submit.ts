import { displayResults } from './render-results';

type PlagiarismCheckData = {
	text: string;
	_ajax_nonce: string;
	_ajax_url: string;
	action: string;
};

type Response = {
	ok: boolean;
	json: Function;
	status: number;
};

export default async function handleFormSubmit(event: Event): Promise<void> {
	event.preventDefault();

	// Select DOM elements for the text input and results container
	const textInput = document.querySelector(
		'#plagiarism-checker__input'
	) as HTMLInputElement;
	const resultsContainer = document.querySelector(
		'#plagiarism-checker__results-container'
	) as HTMLDivElement;

	// Prepare the data payload for the AJAX request
	const data: PlagiarismCheckData = {
		text: textInput.value,
		_ajax_nonce: (window as any).plagiarismCheckerAjax.nonce,
		_ajax_url: (window as any).plagiarismCheckerAjax.ajax_url,
		action: 'plagiarism_checker',
	};

	try {
		// Send a POST request to the backend using fetch API
		const response: Response = await fetch(data._ajax_url, {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
			},
			body: new URLSearchParams(data as any),
		});

		// Parse the JSON response from the backend
		const result = await response.json();
		
		// Handle non-OK responses by throwing an error
		if (!response.ok) {
			throw new Error(
				`Status: ${result.status_code} - ${result.message}: ${result.description}`
			);
		}

		// Render the result using the imported renderResults function
		displayResults(result.data, resultsContainer);

	} catch (errorMessage) {
		// Display an error message if the request fails
		resultsContainer.innerHTML = `<div class="plagiarism-checker__results-container--error">${errorMessage}</div>`;
	}
}
