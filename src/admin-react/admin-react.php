<?php
/**
 * React for Museum Administration screens.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Enqueue script and style.
 */
function enqueue_admin_react( $hook_suffix ) {

	if ( ! strpos( $hook_suffix, 'wpm-react-admin' ) ) {
		return;
	}

	if ( DEV_BUILD ) {
		$index_path = '/build/';
	} else {
		$index_path = '';
	}

	wp_enqueue_script(
		WPM_PREFIX . 'admin-react',
		plugins_url( $index_path . 'index.js', __FILE__ ),
		[ 'wp-i18n', 'wp-element', 'wp-components', 'wp-api-fetch', 'wp-api' ],
		filemtime( plugin_dir_path( __FILE__ ) . $index_path . 'index.js' ),
		true
	);

	wp_enqueue_style(
		'wordpress-components-styles',
		includes_url( '/css/dist/components/style.min.css' ),
		[],
		filemtime( plugin_dir_path( __FILE__ ) . $index_path . 'index.css' )
	);

	wp_enqueue_style(
		WPM_PREFIX . 'admin-react-style',
		plugins_url( $index_path . 'index.css', __FILE__ ),
		[],
		filemtime( plugin_dir_path( __FILE__ ) . $index_path . 'index.css' )
	);
}

/**
 * Create Admin pages with hooks for React apps.
 */
function create_admin_react_pages() {
	add_menu_page(
		'Museum Administration',
		'Museum Administration',
		'manage_options',
		'wpm-react-admin',
		__NAMESPACE__ . '\react_admin_dashboard',
		museum_icon(),
		78
	);

	add_submenu_page(
		'wpm-react-admin',
		'Dashboard',
		'Dashboard',
		'manage_options',
		'wpm-react-admin',
		__NAMESPACE__ . '\react_admin_dashboard'
	);

	add_submenu_page(
		'wpm-react-admin',
		'General',
		'General',
		'manage_options',
		'wpm-react-admin-general',
		__NAMESPACE__ . '\react_admin_general'
	);

	add_submenu_page(
		'wpm-react-admin',
		'Objects',
		'Objects',
		'manage_options',
		'wpm-react-admin-objects',
		__NAMESPACE__ . '\react_admin_objects'
	);

	add_submenu_page(
		'wpm-react-admin',
		'Museum Remote',
		'Museum Remote',
		'manage_options',
		'wpm-react-admin-museum-remote',
		__NAMESPACE__ . '\react_admin_remote'
	);

	add_submenu_page(
		'wpm-react-admin',
		'OMI-PMH',
		'OMI-PMH',
		'manage_options',
		'wpm-react-admin-omi-pmh',
		__NAMESPACE__ . '\react_admin_omi_pmh'
	);
}

function react_admin_dashboard() {
	echo (
		"<div id='wpm-react-admin-app-container-dashboard'></div>"
	);
}

function react_admin_general() {
	echo (
		"<div id='wpm-react-admin-app-container-general'></div>"
	);
}

function react_admin_objects() {
	echo (
		"<div id='wpm-react-admin-app-container-objects'></div>"
	);
}

function react_admin_remote() {
	echo (
		"<div id='wpm-react-admin-app-container-remote'></div>"
	);
}

function react_admin_omi_pmh() {
	echo (
		"<div id='wpm-react-admin-app-container-omi-pmh'></div>"
	);
}

