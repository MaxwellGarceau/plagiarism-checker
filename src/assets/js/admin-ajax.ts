interface PlagiarismCheckData {
    text: string;
    _ajax_nonce: string;
    _ajax_url: string;
}

const textInput = document.querySelector('#plagiarism-text-input') as HTMLTextAreaElement;
const resultTextarea = document.querySelector('#plagiarism-result') as HTMLTextAreaElement;
const submitButton = document.querySelector('#plagiarism-submit-btn') as HTMLButtonElement;

submitButton.addEventListener('click', async (event) => {
    event.preventDefault();

    const data: PlagiarismCheckData = {
        text: textInput.value,
        _ajax_nonce: (window as any).plagiarismCheckerAjax.nonce, // Using the nonce
        _ajax_url: (window as any).plagiarismCheckerAjax.url
    };

    try {
        const response = await fetch((window as any).plagiarismCheckerAjax.ajax_url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        },
        body: new URLSearchParams(data as any),
        });

        if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const result = await response.text(); // Handle the response from the server
        resultTextarea.value = result; // Display the result in the textarea
        } catch (error) {
            console.error('Error:', error);
            resultTextarea.value = 'An error occurred while checking plagiarism.';
        }
    }
);
  