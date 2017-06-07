<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/khristophor/wordpress-stats
 * @since             1.0.0
 * @package           Wordpress_Stats
 *
 * @wordpress-plugin
 * Plugin Name:       WordPress Statistics
 * Plugin URI:        https://github.com/khristophor/wordpress-stats/wordpress-stats-uri/
 * Description:       This plugin displays common statistics about the current WordPress install.
 * Version:           1.0.0
 * Author:            Chris Wiseman
 * Author URI:        https://github.com/khristophor/wordpress-stats/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wordpress-stats
 * Domain Path:       /languages
 */

if ( file_exists( dirname( __FILE__ ) . '/vendor/autoload.php' ) ) {
	require_once dirname( __FILE__ ) . '/vendor/autoload.php';
}

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in lib/Activator.php
 */
\register_activation_hook( __FILE__, '\WordpressStats\Activator::activate' );

/**
 * The code that runs during plugin deactivation.
 * This action is documented in lib/Deactivator.php
 */
\register_deactivation_hook( __FILE__, '\WordpressStats\Deactivator::deactivate' );

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
\add_action( 'plugins_loaded', function () {
	$plugin = new \WordpressStats\Plugin();
	$plugin->run();
} );
