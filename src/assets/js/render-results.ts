// render-results.ts

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

/**
 * Render the results returned by the API into HTML.
 * @param {Results[]} result The array of result objects to render.
 * @return {string} HTML string representing the results.
 */
function generateResultsHtml(results: Results[]): string {
	let output = '<ul class="plagiarism-checker__results">';
	output += results
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

function getEmptyResultsHtml(): string {
	return '<div class="plagiarism-checker__results-container--no-results">No plagiarism detected!</div>';
}

function hasResults(results: Results[]): boolean {
	return results !== undefined && results !== null && results.length === 0;
}

export function displayResults(results: Results[], resultsContainer: HTMLDivElement): void {
	resultsContainer.innerHTML = hasResults(results)
		? getEmptyResultsHtml()
		: generateResultsHtml(results);

	resultsContainer.classList.add('plagiarism-checker__results-container--has-results');
}