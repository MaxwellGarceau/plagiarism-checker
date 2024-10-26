<?php

namespace Tests\Integration;

/**
 * TODO: Make these tests
 *
 * These tests are a placeholder for the actual tests that need to be written.
 * This way we know exactly what the requirements for this functionality are.
 *
 * These tests should cover everything in the /src/Admin directory.
 */

/**
 * Main Functionality tests
 */

it(
	'should create the plagiarism checker DB tables upon plugin activation',
	function () {
	}
)->group( 'wp_full' )->skip( 'Feature implemented - !!!test not yet written!!!' );

it(
	'should save the API token to the DB',
	function () {
	}
)->group( 'wp_full' )->skip( 'Feature implemented - !!!test not yet written!!!' );

/**
 * Security tests
 */

it(
	'should prevent malicious input from being saved to the DB',
	function () {
	}
)->group( 'wp_full' )->skip( 'Feature implemented - !!!test not yet written!!!' );

it(
	'should prevent entering a token without a nonce',
	function () {
	}
)->group( 'wp_full' )->skip( 'Feature implemented - !!!test not yet written!!!' );

it(
	'should not display one users access token to another users',
	function () {
	}
)->group( 'wp_full' )->skip( 'Feature implemented - !!!test not yet written!!!' );

/**
 * Secondary tests
 */

it(
	'should return the API token when queried, if exists, and populate the input',
	function () {
	}
)->group( 'wp_full' )->skip( 'Feature implemented - !!!test not yet written!!!' );

it(
	'should display a warning when the DB tables are not created',
	function () {
	}
)->group( 'wp_full' )->skip( 'Feature implemented - !!!test not yet written!!!' );

it(
	'should refuse to save invalid access tokens',
	function () {
	}
)->group( 'wp_full' )->skip( 'Feature implemented - !!!test not yet written!!!' );

/**
 * UI tests
 */

it(
	'should create the menu page',
	function () {
	}
)->group( 'wp_full' )->skip( 'Feature implemented - !!!test not yet written!!!' );

it(
	'should create the settings page',
	function () {
	}
)->group( 'wp_full' )->skip( 'Feature implemented - !!!test not yet written!!!' );

it(
	'should display a success message when a token is correctly saved to the DB',
	function () {
	}
)->group( 'wp_full' )->skip( 'Feature implemented - !!!test not yet written!!!' );

it(
	'should display an error message when a token does not meet validation criteria',
	function () {
	}
)->group( 'wp_full' )->skip( 'Feature implemented - !!!test not yet written!!!' );
