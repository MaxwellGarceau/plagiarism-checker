import { describe, it, beforeEach, expect } from 'vitest';
import toggleForm from '../js/toggle-form';

describe('toggleForm', () => {
  let toggleButton: HTMLElement;
  let formContainer: HTMLElement;

  beforeEach(() => {
    // Setup the DOM structure
    document.body.innerHTML = `
      <div id="plagiarism-checker" class="plagiarism-checker--closed"></div>
      <button id="plagiarism-checker__toggle"></button>
    `;

    // Get references to the elements
    toggleButton = document.getElementById('plagiarism-checker__toggle')!;
    formContainer = document.getElementById('plagiarism-checker')!;

    // Call the toggleForm function to set up the event listener
    toggleForm();
  });

  it('should toggle the form between open and closed when the button is clicked', () => {
    // Initially, the form is closed
    expect(formContainer.classList.contains('plagiarism-checker--closed')).toBe(true);
    expect(formContainer.classList.contains('plagiarism-checker--open')).toBe(false);

    // Simulate a click on the toggle button
    toggleButton.click();

    // After the click, the form should be open
    expect(formContainer.classList.contains('plagiarism-checker--closed')).toBe(false);
    expect(formContainer.classList.contains('plagiarism-checker--open')).toBe(true);

    // Simulate another click to close the form
    toggleButton.click();

    // After the second click, the form should be closed again
    expect(formContainer.classList.contains('plagiarism-checker--closed')).toBe(true);
    expect(formContainer.classList.contains('plagiarism-checker--open')).toBe(false);
  });
});
