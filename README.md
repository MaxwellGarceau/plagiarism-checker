# Developer readme

## App Design Choices
Below is a brief, dev focused, outline of some of the app design and architecture choices made for this plugin.

### FE
- TypeScript
- Vite
- Scss
- Inherit from WP styles where possible
- Responsive (cool trick with vh and calc to tame the results column)

#### CSS/Scss
This project takes a hybrid approach to CSS. The default is BEM convention via Scss with a light implementation of SMACSS only where absolutely necessary.

However, where it makes sense, Object Oriented CSS is also employed in order to separate some of the more intricate structural components from their styles.

Theme compatibility is a priority as opposed to one particular styling goal.

In short, the CSS for this project should be composable. Avoid the use of global styles where possible. Inherit from WP for the best theme compatibility where possible. Use intelligent fallbacks to styles that will coordinate across the spectrum of designs.

### BE
- PHP 8.3 features
- Composer
- Testing with Pest (Brain Monkey and WP Core options)

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

## App Features

### Admin ajax and API client (to genius.com)
High level
- On form submission an admin ajax request is passed to the BE
- The BE validates/sanitizes and then makes a request to genius.com
- Request is returned, handled, and then sent back to FE
- FE displays output to user

#### Error Handling
The goal here is to handle our own user errors (no API key, invalid search text, etc) while also handling error responses such as invalid/expired auth token, empty response, etc from the API we're requesting data from.

##### BE
The BE has a series of checks to ensure that all possible scenarios are handled and returned in a consistent format to the FE. The Resource class is enforcing consistent response formatting in all responses.

On the FE, TypeScript is checking to ensure that all responses from the BE are routed to the correct places and that data inconsistency will cause problems.

#### Ideas for next steps
- Create a response class for ensuring consistency in delivering responses to the FE
- Create a Resource_Success class and only return Resource_Success|WP_Error from the Song_Controller
- Extend WP_Error and require the error data as an argument (as opposed to just the required message)

### Admin page and user data management
High level
- Handles API token input from users and stores them
- Data storage is on a per user basis
- Custom plugin DB tables for easier separation between site and plugin

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

<details>

<summary>Folders vs Groups for organizing tests</summary>

You can add text within a collapsed section. 
Use the folders to organize tests by Unit, Integration, and Feature. Use the groups to organize tests by how they need to load.

Run your tests by group, not by folder. For example, every test in the "wp_brain_monkey" group needs Brain Monkey to run, but not every test with Brain Monkey is an Integration test. Maybe a class can only really be tested if we sniff out something it's doing inside a WP core function that we need to mock with Brain Monkey. This is WordPress, we need to be flexible with our categorization here.

TODO: When a user runs tests by Unit, Integration, or Feature folders let's organize the tests so that they rerun the bootstrap and load the required setup automatically.

</details>

<details>
	<summary>WordPress Stubs, Brain Monkey, and loading WP Core</summary>

 In addition, we have three ways to handle WP dependencies. Testing code that doesn't include them, mocking them with Brain Monkey, or loading them as normal and testing with a test DB.

- Unit (WP stubs are loaded)
- Simulated Integration (WP Core can be mocked with Brain Monkey)
- Full Integration (WP Core is NOT mocked and a testing DB is used)

It's important that we run each test with the kind of mocking they need. Ideally, we need a system where we can group the tests by their type (Unit, Integration, or Feature) as well as their mocking needs.

At the moment, I'm organizing the test folders by Unit, Integration, and Feature and adding these groups based on the mocking needs.

(Underscores "_" to not conflict with Pest command line)
 - "wp_full" - Load full WP Core 
 - "wp_brain_monkey" - Mock with Brain Monkey to make assertions
 - (default) "wp_stubs" - Load no WP Core and load WP stubs

There's no way to filter these tests at the moment, but we can take care of that once there are enough tests for that kind of dev work to make sense.
</details>

<details>
	<summary>Load both WP Core/Stubs and Brain Monkey together</summary>

In a perfect world, we would be able to load the WP Core or the WP stubs and then overwrite them with Brain Monkey. Not impossible! However, we would need pluggable functions (the hard part) and then need to load them after Brain Monkey (easy part).

I did some experiments with [lucatume/function-mocker](https://github.com/lucatume/function-mocker). Using this package, we can load all of WP Core or all of the WP stubs and then patch out functions that we want to write assertions against.

I really like this library and use it for writing tests on legacy code to pin functionality in place. However, the current stable version on composer hasn't been updated in 6 years and I decided to not tie this project down with code that was that old.

I sent a message to the dev and would like to pick this idea up again in the future if the library maintenance changes.

For the moment, this is on hold, but getting WP set up with Pest and having a selectable way of choosing between WP stubs, Brain Monkey, and WP Core would be a great project in and of itself.
</details>
