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

// Your code starts here.

use Kucrut\Vite;
use Max_Garceau\Plagiarism_Checker\Main;
use Max_Garceau\Plagiarism_Checker\Views\Form_Controller;

// Include the Composer autoloader.
require_once plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';

// Initialize the plugin.

// TODO: Add PHP-DI when the number of classes expands.
$main = new Main( new Form_Controller() );
$main->init();

// Enqueue the Vite assets.
add_action( 'admin_enqueue_scripts', function (): void {
    Vite\enqueue_asset(
        __DIR__ . '/dist',
        'src/assets/js/scripts.ts',
        [
            'handle' => 'plagiarism-checker-scripts',
            'dependencies' => [], // Optional script dependencies. Defaults to empty array.
            'css-dependencies' => [], // Optional style dependencies. Defaults to empty array.
            'css-media' => 'all', // Optional.
            'css-only' => false, // Optional. Set to true to only load style assets in production mode.
            'in-footer' => true, // Optional. Defaults to false.
        ]
    );
} );
