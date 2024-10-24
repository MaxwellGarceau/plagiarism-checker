import { PlagiarismResultsRenderer } from './render-results';

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
	statusText: string;
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

	const renderer = new PlagiarismResultsRenderer(resultsContainer);

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
				'Content-Type':
					'application/x-www-form-urlencoded; charset=UTF-8',
			},
			body: new URLSearchParams(data as any),
		});

		// Parse the JSON response from the backend
		const parsedJson = await response.json();

		const results = parsedJson?.data;

		// Error with fetch request - we didn't even receive an error respoce
		if (!response.ok && results === undefined) {
			throw new Error(
				`Failed to fetch results from the server - Status: ${response.status} - ${response.statusText}`
			);
		}

		// Request succeeded, but we didn't get the answer we wanted
		const resultsHtml = results.success ?
			renderer.getSuccessHtml(results) :
			renderer.getErrorHtml(results);

		// Render the result using the imported renderResults function
		renderer.displayResults(resultsHtml);
	} catch (errorMessage) {
		// Display an error message if the request fails
		resultsContainer.innerHTML = renderer.getServerFailureHtml(errorMessage);
	}
}
