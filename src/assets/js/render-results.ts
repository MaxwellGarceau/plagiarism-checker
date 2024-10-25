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
		const fallbackMessage = message ?? "Unknown error. Please check your API credentials or contact the developer.";
	
		// Construct each line only if the corresponding variable exists
		const descriptionHtml = description
			? `<div class="plagiarism-checker__error plagiarism-checker__error-description"><p class="plagiarism-checker__error-label">Description:</p> <pre class="plagiarism-checker__pre">${description}</pre></div>`
			: '';
			
		const statusCodeHtml = status_code
			? `<div class="plagiarism-checker__error plagiarism-checker__error-status-code"><p class="plagiarism-checker__error-label">Status code:</p> <pre class="plagiarism-checker__pre">${status_code}</pre></div>`
			: '';
	
		// Return the HTML string, including only the parts that exist
		return `<div class="plagiarism-checker__results">
					<div class="plagiarism-checker__error plagiarism-checker__error-message">
						<p class="plagiarism-checker__error-label">Message:</p> 
						<pre class="plagiarism-checker__pre">${fallbackMessage}</pre>
					</div>
					${descriptionHtml}
					${statusCodeHtml}
				</div>`;
	}
	

	public getServerFailureHtml(
		errorMessage: string = 'Error: Failed to fetch results from the server'
	): string {
		return `<div class="plagiarism-checker__results-container--error">
					<p class="plagiarism-checker__error plagiarism-checker__error-message">${errorMessage}</p>
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
		return '<div class="plagiarism-checker__results-none">No plagiarism detected!</div>';
	}

	private hasResults(results: Result[]): boolean {
		console.log(results.length)
		return results.length === 0;
	}
}
