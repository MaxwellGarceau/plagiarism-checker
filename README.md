## App Design Choices
Below is a brief, dev focused, outline of some of the app design and architecture choices made for this plugin.

### FE
- TypeScript
- Vite

### BE
- PHP 8.3 features
- Composer
- Testing with PHPUnit

### High level overview
- Output a widget on the sidebar with an input form and submit button
- On submit, send a REST request to genius.com to search for matching song lyrics
- Return that data and output the results below in the form

### Roadmap
Things I would like to do next

#### Functionality
- Support cross referencing an entire song
- Implement better algorithms for "percentage based" plagiarism
- Have different "plagiarism" scores

#### UI
- More controls for the user to minimize the toggle
- A user can enter "request" mode, highlight text, and on mouse release a rest request is sent