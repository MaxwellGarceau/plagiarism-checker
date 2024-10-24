import { Result, Error } from './types';

/**
 * Class to render the results of the plagiarism check.
 *
 * Opening up Html getters to public
 * I don't want to overengineer this class by accepting a response
 * and trying to do all the routing internally.
 *
 * This level of separate is good for now.
 *
 * @class PlagiarismResultsRenderer
 */
export class PlagiarismResultsRenderer {
	constructor(private resultsContainer: HTMLDivElement) {}

	public displayResults(results: string): void {
		this.resultsContainer.innerHTML = results;
		this.resultsContainer.classList.add(
			'plagiarism-checker__results-container--has-results'
		);
	}

	public getSuccessHtml(results: Result[]): string {
		return this.hasResults(results)
			? this.getEmptyResultsHtml()
			: this.generateResultsHtml(results);
	}

	public getErrorHtml({ message, description, status_code }: Error): string {
		return `<div class="plagiarism-checker__results-container--error">
					<p class="plagiarism-check__error-message"><span class="plagiarism-checker__error-label">Message:</span> ${message}</p>
					<p class="plagiarism-check__error-description"><span class="plagiarism-checker__error-label">Description:</span> ${description}</p>
					<p class="plagiarism-check__error-status-code"><span class="plagiarism-checker__error-label">Status code:</span> ${status_code}</p>
				</div>`;
	}

	public getServerFailureHtml(
		errorMessage: string = 'Error: Failed to fetch results from the server'
	): string {
		return `<div class="plagiarism-checker__results-container--error">
					<p class="plagiarism-check__error-message">${errorMessage}</p>
				</div>`;
	}

	/**
	 * Render the results returned by the API into HTML.
	 * @param {Result[]} result The array of result objects to render.
	 * @return {string} HTML string representing the results.
	 */
	private generateResultsHtml(results: Result[]): string {
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
				}: Result) => {
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

	private hasResults(results: Result[]): boolean {
		return results.length === 0;
	}
}
