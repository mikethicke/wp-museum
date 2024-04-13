<?php
/**
 * Remote plugin for Museum for WordPress.
 *
 * @package MikeThicke\MuseumRemote
 * @author Mike Thicke
 *
 * @wordpress-plugin
 * Plugin Name: Museum for WordPress - Remote
 * Description: Allows a WordPress site to remotely connect to another Museum for WordPress site.
 * Version: 0.1.1
 * Author: Mike Thicke
 * Author URI: http://www.mikethicke.com
 * Text Domain: museum-remote
 */

namespace MikeThicke\MuseumRemote;

/**
 * Is this a development build of the plugin?
 *
 * The development build and release build could have different directory structures,
 * primarily for transpiled code in blocks.
 */
const DEV_BUILD = true;

define( 'REQUIRE_PREFIX', plugin_dir_path( __FILE__ ) );

define( 'MR_BASE_PLUGIN_DIR', WP_PLUGIN_DIR . '/museum-remote/' );
define( 'MR_BASE_PLUGIN_URL', WP_PLUGIN_URL . '/museum-remote/' );

if ( DEV_BUILD ) {
	define( 'MR_BASE_URL', MR_BASE_PLUGIN_URL . 'src/' );
	define( 'MR_REACT_PATH', MR_BASE_PLUGIN_DIR . 'build/' );
	define( 'MR_REACT_URL', MR_BASE_PLUGIN_URL . 'build/' );
} else {
	define( 'MR_BASE_URL', MR_BASE_PLUGIN_URL );
	define( 'MR_REACT_PATH', MR_BASE_PLUGIN_DIR . 'react/' );
	define( 'MR_REACT_URL', MR_BASE_PLUGIN_URL . 'react/' );
}

require_once REQUIRE_PREFIX . 'admin.php';
require_once REQUIRE_PREFIX . 'react.php';
require_once REQUIRE_PREFIX . 'rest.php';
require_once REQUIRE_PREFIX . 'collection-block.php';
