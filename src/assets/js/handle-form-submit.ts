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
		header_image_thumbnail_url: string;
	};
};

export default async function handleFormSubmit(event: Event): Promise<void> {
	event.preventDefault();

	const textInput = document.querySelector(
		'#plagiarism-checker__input'
	) as HTMLInputElement;
	const resultsContainer = document.querySelector(
		'#plagiarism-checker__results-container'
	) as HTMLDivElement;

	const data: PlagiarismCheckData = {
		text: textInput.value,
		_ajax_nonce: (window as any).plagiarismCheckerAjax.nonce,
		_ajax_url: (window as any).plagiarismCheckerAjax.ajax_url,
		action: 'plagiarism_checker',
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

		const result = await response.json();
		if (!response.ok) {
			throw new Error(
				`Status: ${result.status_code} - ${result.message}: ${result.description}`
			);
		}

		resultsContainer.innerHTML = renderOutput(result.data);
		resultsContainer.classList.add('plagiarism-checker__results-container--has-results');
	} catch (errorMessage) {
		resultsContainer.innerHTML = `<div class="plagiarism-checker__results-container--error">${errorMessage}</div>`;
	}
}

function renderOutput(result: Results[]): string {
	let output = '<ul class="plagiarism-checker__results">';
	output += result
		.map((e: Results) => {
			const songTitle = `<a href="${e.result.url}" class="plagiarism-checker__result-link plagiarism-checker__result-link--song" target="_blank">${e.result.title}</a>`;
			const artistName = `<span class="artist-name"><a href="${e.result.primary_artist.url}" class="plagiarism-checker__result-link plagiarism-checker__result-link--artist" target="_blank">${e.result.primary_artist.name}</a></span>`;
			const thumbnail = `<img src="${e.result.header_image_thumbnail_url}" alt="${e.result.title} - ${e.result.primary_artist.name}" class="plagiarism-checker__result-thumbnail" />`;
			return `<li class="plagiarism-checker__result">${thumbnail}${songTitle} - ${artistName}</li>`;
		})
		.join('\n');
	output += '</ul>';
	return output;
}
