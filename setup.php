<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             2.16.0
 * @package           kabar
 *
 * @wordpress-plugin
 * Plugin Name:       Kabar library
 * Plugin URI:        https://github.com/gniewomir/kabar
 * Description:       Library of component and modules used to speed up WordPress sites development
 * Version:           2.28.8
 * Author:            Gniewomir Świechowski
 * Author URI:        http://cv.enraged.pl
 * License:           GNU GENERAL PUBLIC LICENSE Version 3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       kabar
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Setup kabar librry
 */
require_once plugin_dir_path(__FILE__).'Src/ServiceLocator.php';
\kabar\ServiceLocator::setup();
