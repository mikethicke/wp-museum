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

	$asset_file = include ( WPM_BUILD_DIR . 'admin.asset.php' );
	$pu = WPM_BUILD_URL . 'admin-react.js';
	wp_enqueue_script(
		WPM_PREFIX . 'admin-react',
		WPM_BUILD_URL . 'admin-react.js',
		$asset_file['dependencies'],
		$asset_file['version'],
		true
	);

	wp_enqueue_style(
		'wordpress-components-styles',
		includes_url( '/css/dist/components/style.min.css' ),
		[],
		filemtime( WPM_BUILD_DIR . 'index.css' )
	);

	wp_enqueue_style(
		WPM_PREFIX . 'admin-react-style',
		WPM_BUILD_URL. 'admin.css',
		[],
		filemtime( WPM_BUILD_DIR . 'admin.css' )
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

