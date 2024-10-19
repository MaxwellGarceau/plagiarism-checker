interface PlagiarismCheckData {
	text: string;
	_ajax_nonce: string;
	_ajax_url: string;
	action: string;
}

const textInput = document.querySelector(
	'#plagiarism-checker__input'
) as HTMLTextAreaElement;
const resultTextarea = document.querySelector(
	'#plagiarism-checker__results'
) as HTMLTextAreaElement;

export default async function handleFormSubmit(event) {
	event.preventDefault();

	console.log(window);

	const data: PlagiarismCheckData = {
		text: textInput.value,
		_ajax_nonce: (window as any).plagiarismCheckerAjax.nonce, // Using the nonce
		_ajax_url: (window as any).plagiarismCheckerAjax.ajax_url,
		action: 'plagiarism_checker', // Include the action param
	};

	try {
		const response = await fetch(data._ajax_url, {
			method: 'POST',
			headers: {
				'Content-Type':
					'application/x-www-form-urlencoded; charset=UTF-8',
			},
			body: new URLSearchParams(data as any),
		});

		console.log(response);

		if (!response.ok) {
			throw new Error(`HTTP error! Status: ${response.status}`);
		}

		const result = await response.text();

		// Render the result to the screen
		// TODO: Do I want to render this in a textarea or another HTML element?
		resultTextarea.value = result;
		return false;
	} catch (error) {
		console.error('Error:', error);
		resultTextarea.value = 'An error occurred while checking plagiarism.';
		return false;
	}
}
