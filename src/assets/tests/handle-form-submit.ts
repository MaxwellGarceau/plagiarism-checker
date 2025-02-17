import { vi, describe, it, expect, beforeEach } from 'vitest';
import handleFormSubmit from '../js/handle-form-submit';
import { FetchResponse } from '../js/types';

// Mock the DOM elements
let textInput: HTMLInputElement;
let resultTextarea: HTMLDivElement;
let mockConsoleError: ReturnType<typeof vi.spyOn>;

// Mock the global window object for Ajax nonce and URL
(globalThis as any).plagiarismCheckerAjax = {
	nonce: 'mock_nonce',
	ajax_url: 'https://example.com/ajax-url',
};

// Mock fetch response data
const mockResponseData: FetchResponse = {
	data: {
		success: true,
		data: [
			{
				result: {
					title: 'Test Song',
					url: 'https://example.com/song',
					primary_artist: {
						name: 'Test Artist',
						url: 'https://example.com/artist',
					},
					header_image_thumbnail_url: 'https://example.com/image',
				},
			},
		],
	},
};

// Create a mock response object that adheres to the Response interface
// This is a little extra, but it helps with type checking
const createMockResponse = (
	ok: boolean,
	status: number,
	data: any = null
): Response => {
	return {
		ok,
		status,
		json: () => Promise.resolve(data),
		headers: new Headers(),
		redirected: false,
		statusText: ok ? 'OK' : 'Internal Server Error',
		type: 'basic' as ResponseType, // Use ResponseType enum
		url: 'https://example.com',
		clone: function () {
			return this;
		},
		body: null,
		bodyUsed: false,
		arrayBuffer: async () => new ArrayBuffer(0),
		blob: async () => new Blob(),
		formData: async () => new FormData(),
		text: async () => JSON.stringify(data),
	} as Response; // Cast to Response type
};

// Helper to mock a successful fetch response
const mockFetchSuccess = (): void => {
	globalThis.fetch = vi.fn(() =>
		Promise.resolve(createMockResponse(true, 200, mockResponseData))
	);
};

// Helper to mock a failed fetch response
const mockFetchFailure = (): void => {
	globalThis.fetch = vi.fn(() =>
		Promise.resolve(createMockResponse(false, 500))
	);
};

beforeEach((): void => {
	// Mock console.error to prevent error messages during test
	mockConsoleError = vi.spyOn(console, 'error').mockImplementation(() => {});

	// Reset the DOM elements before each test
	document.body.innerHTML = `
    <form id="plagiarism-checker__form">
      <input id="plagiarism-checker__input"></input>
	  <div id="plagiarism-checker__results-container" class="plagiarism-checker__results-container">
	  </div>
    </form>
  `;
	textInput = document.querySelector(
		'#plagiarism-checker__input'
	) as HTMLInputElement;
	resultTextarea = document.querySelector(
		'#plagiarism-checker__results-container'
	) as HTMLDivElement;

	// Set a value for the text input
	textInput.value = 'Sample text for plagiarism check';
});

describe('handleFormSubmit', () => {
	it('should render results on successful form submission', async (): Promise<void> => {
		mockFetchSuccess();

		// Create a mock submit event
		const form = document.querySelector(
			'#plagiarism-checker__form'
		) as HTMLFormElement;
		const event = new Event('submit', { bubbles: true, cancelable: true });

		// Trigger the submit event
		form.dispatchEvent(event);

		// Wait for handleFormSubmit to finish processing
		await handleFormSubmit(event);

		// Expect the resultTextarea to contain the result output
		expect(resultTextarea.innerHTML).toContain('Test Song');
		expect(resultTextarea.innerHTML).toContain('Test Artist');
	});

	it('should display an error message on failed form submission', async (): Promise<void> => {
		mockFetchFailure();

		// Create a mock submit event
		const form = document.querySelector(
			'#plagiarism-checker__form'
		) as HTMLFormElement;
		const event = new Event('submit', { bubbles: true, cancelable: true });

		// Trigger the submit event
		form.dispatchEvent(event);

		// Wait for handleFormSubmit to finish processing
		await handleFormSubmit(event);

		// Expect the resultTextarea to contain the error message
		expect(resultTextarea.innerHTML).toBe(
			`<div class="plagiarism-checker__results-container--error">
					<p class="plagiarism-checker__error plagiarism-checker__error-message">Error: Failed to fetch results from the server - Status: 500 - Internal Server Error</p>
				</div>`
		);
	});
});
