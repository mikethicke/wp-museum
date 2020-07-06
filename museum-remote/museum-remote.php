<?php
/**
 * Remote plugin for Museum for Wordpress.
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

if ( DEV_BUILD ) {
	define( 'MR_BASE_URL', plugin_dir_url( __FILE__ ) . 'src/' );
	define( 'MR_REACT_PATH', '/build/' );
} else {
	define( 'MR_BASE_URL', plugin_dir_url( __FILE__ ) );
	define( 'MR_REACT_PATH', '' );
}

require_once REQUIRE_PREFIX . 'admin.php';
require_once REQUIRE_PREFIX . 'react.php';
require_once REQUIRE_PREFIX . 'rest.php';
require_once REQUIRE_PREFIX . 'collection-block.php';
