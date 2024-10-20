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

## Testing

TLDR: Don't write end to end feature tests unless you really need to. Let's keep an eye on ways to both run tests based on what kind of mocking they need as well as what group they're part of.

- Pest (version 1 to use Yoast WP Testing Utils)
- Yoast WP Testing Utils (provides access to WP_Unit functions)

### Methodology
This project has three main test categories

- Unit
- Integration (Testing 2 or more components together)
- Feature (End to end test)

If possible, try to keep the majority of your tests to Unit and Simulated Integration tests. Full integration tests take more overhead, are harder to run, and harder to troubleshoot. In addition, they are very easy to misuse and can result in tests that have a vague scope and make it difficult to localize errors. These tests should be saved for end to end functionality, for difficult bugs, or areas where failure would be critical.

### No mocking, mocking, and loading WP Core

In addition, we have three ways to handle WP dependencies. Testing code that doesn't include them, mocking them with Brain Monkey, or loading them as normal and testing with a test DB.

- Unit
- Simulated Integration (WP Core is mocked)
- Full Integration (WP Core is NOT mocked and a testing DB is used)

It's important that we run each test with the kind of mocking they need. Ideally, we need a system where we can group the tests by their type (Unit, Integration, or Feature) as well as their mocking needs.

At the moment, I'm organizing the test folders by Unit, Integration, and Feature and adding these groups based on the mocking needs.

 - "full_wp" - Load full WP Core 
 - "simulated_wp" - Load simulated WP Core
 - "no_wp" - Load no WP Core

There's no way to filter these tests, but we can take care of that once there are enough tests for that kind of dev work to make sense.