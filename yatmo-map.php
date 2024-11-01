<?php

/**
 * Plugin Name: Simple Yatmo Map | Free map with points of interest!
 * Plugin URI: https://wordpress.org/plugins/yatmo-map/
 * Description: A plugin for creating a Yatmo JS map with a shortcode with or without points of interest (everyday needs, motorways entrances, public transports lines, etc.) and nice features!
 * Author: Yatmo SRL
 * Author URI: https://yatmo.com
 * Text Domain: yatmo-map
 * Domain Path: /languages/
 * Version: 1.0.0
 * Tags: map, yatmo, pois, points of interest, maps
 * License: GPLv2 or later
 * The plugin itself is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *  
 * Yatmo Map is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit; 
}

define('yatmo_map__PLUGIN_VERSION', '3.3.0');
define('yatmoMapPluginFile', __FILE__);
define('yatmoMapPluginDir', plugin_dir_path(__FILE__));

// import main class
require_once yatmoMapPluginDir . 'class.yatmo-map.php';

// uninstall hook
register_uninstall_hook(__FILE__, array('yatmo_map', 'uninstall'));

add_action('init', array('yatmo_map', 'init'));
function start_output_buffer() {
    ob_start();
}