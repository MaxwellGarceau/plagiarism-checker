<?php
/**
 * Plugin Name:     Plagiarism Checker
 * Plugin URI:      
 * Description:     Checks lyrics for plagiarism.
 * Author:          Max Garceau
 * Author URI:      https://resume.maxgarceau.com/
 * Text Domain:     plagiarism-checker
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Plagiarism_Checker
 */

// Include the Composer autoloader.
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

add_action( 'plugins_loaded', function (): void {
    // Bail early if the user is not logged in.
    if ( ! is_user_logged_in() ) {
        return;
    }

    // Initialize the plugin.
    $container = new \DI\Container();
    $container->get( \Max_Garceau\Plagiarism_Checker\Main::class )->init();
} );
