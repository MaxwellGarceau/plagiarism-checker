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
 * Class to render the results of the plagiarism check.
 *
 * Refactored into a class because I really don't want this functionality
 * to be exposed except via the one displayResults method.
 *
 * @class PlagiarismResultsRenderer
 */
export class PlagiarismResultsRenderer {
	constructor(private resultsContainer: HTMLDivElement) {}

	public displayResults(results: Results[]): void {
		this.resultsContainer.innerHTML = this.hasResults(results)
			? this.getEmptyResultsHtml()
			: this.generateResultsHtml(results);
		this.resultsContainer.classList.add(
			'plagiarism-checker__results-container--has-results'
		);
	}

	public displayErrors(errorMessage: string): string {
		return `<div class="plagiarism-checker__results-container--error">${errorMessage}</div>`;
	}

	/**
	 * Render the results returned by the API into HTML.
	 * @param {Results[]} result The array of result objects to render.
	 * @return {string} HTML string representing the results.
	 */
	private generateResultsHtml(results: Results[]): string {
		let output = '<ul class="plagiarism-checker__results">';
		output += results
			.map(
				({
					result: {
						url,
						title,
						header_image_thumbnail_url,
						primary_artist,
					},
				}: Results) => {
					const songTitle = `<a href="${url}" class="plagiarism-checker__result-link plagiarism-checker__result-link--song" target="_blank">${title}</a>`;
					const artistName = `<span class="artist-name"><a href="${primary_artist.url}" class="plagiarism-checker__result-link plagiarism-checker__result-link--artist" target="_blank">${primary_artist.name}</a></span>`;
					const thumbnail = `<img src="${header_image_thumbnail_url}" alt="${title} - ${primary_artist.name}" class="plagiarism-checker__result-thumbnail" />`;
					return `<li class="plagiarism-checker__result">${thumbnail}${songTitle} - ${artistName}</li>`;
				}
			)
			.join('\n');
		output += '</ul>';
		return output;
	}

	private getEmptyResultsHtml(): string {
		return '<div class="plagiarism-checker__results-container--no-results">No plagiarism detected!</div>';
	}

	private hasResults(results: Results[]): boolean {
		return results.length === 0;
	}
}
