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

type Results = {
	result: {
		title: string;
		url: string;
		primary_artist: {
			name: string;
			url: string;
		};
	};
};

export default async function handleFormSubmit(event: Event): Promise<void> {
	event.preventDefault();
	
	// Query the DOM elements we need
	const textInput = document.querySelector(
		'#plagiarism-checker__input'
	) as HTMLInputElement;
	const resultTextarea = document.querySelector(
		'#plagiarism-checker__results'
	) as HTMLDivElement;

	const data: PlagiarismCheckData = {
		text: textInput.value,
		_ajax_nonce: (window as any).plagiarismCheckerAjax.nonce, // Using the nonce
		_ajax_url: (window as any).plagiarismCheckerAjax.ajax_url,
		action: 'plagiarism_checker', // Include the action param
	};

	try {
		const response: Response = await fetch(data._ajax_url, {
			method: 'POST',
			headers: {
				'Content-Type':
					'application/x-www-form-urlencoded; charset=UTF-8',
			},
			body: new URLSearchParams(data as any),
		});

		if (!response.ok) {
			throw new Error(`HTTP error! Status: ${response.status}`);
		}

		const result: Results[] = await response.json().then((res) => res.data);

		// Render the result to the screen
		resultTextarea.innerHTML = renderOutput(result);
	} catch (error) {
		console.error('Error:', error);
		resultTextarea.innerHTML =
			'An error occurred while checking plagiarism.';
	}
}

function renderOutput(result: Results[]): string {
	let output = '<ul>';
	output += result
		.map((e: Results) => {
			const songTitle = `<a href="${e.result.url}" target="_blank">${e.result.title}</a>`;
			const artistName = `<a href="${e.result.primary_artist.url}" target="_blank">${e.result.primary_artist.name}</a>`;
			return `<li>${songTitle} - ${artistName}</li>`;
		})
		.join('\n');
	output += '</ul>';
	return output;
}
