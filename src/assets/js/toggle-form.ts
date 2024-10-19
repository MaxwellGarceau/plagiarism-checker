export default function toggleForm(): void {
	const toggleButton: HTMLElement | null = document.getElementById('plagiarism-checker__toggle');
	const formContainer: HTMLElement | null = document.getElementById('plagiarism-checker');
  
	if (toggleButton && formContainer) {
	  toggleButton.addEventListener('click', () => {
		// Toggle the open/close state
		// plagiarism-checker--closed is the initial state
		formContainer.classList.toggle('plagiarism-checker--open');
		formContainer.classList.toggle('plagiarism-checker--closed');
	  });
	}
}
