<?php
/**
 * Enqueues javascript and styles for blocks and admin react apps.
 *
 * @package MikeThicke\MuseumRemote
 */

namespace MikeThicke\MuseumRemote;

/**
 * Enqueues scripts and styles for admin.
 */
function enqueue_admin_scripts_and_styles() {
	$asset_file = include MR_REACT_PATH . 'remote.asset.php';
	wp_enqueue_script(
		'museum-remote-admin',
		MR_REACT_URL . 'museum-remote-admin.js',
		$asset_file['dependencies'],
		$asset_file['version'],
		true
	);
	wp_enqueue_style(
		'museum-remote-style-admin',
		MR_REACT_URL . 'remote.css',
		[],
		filemtime( MR_REACT_URL . 'remote.css' )
	);
	wp_enqueue_style(
		'museum-remote-style-front',
		MR_REACT_URL . 'style-remote.css',
		[],
		filemtime( MR_REACT_PATH . 'style-remote.css' )
	);
}
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\enqueue_admin_scripts_and_styles' );

/**
 * Enqueues scripts and styles for frontend.
 */
function enqueue_frontend_scripts_and_styles() {
	$asset_file = include MR_REACT_PATH . 'remote.asset.php';
	wp_enqueue_script(
		'museum-remote-front',
		MR_REACT_PATH . 'museum-remote-front.js',
		$asset_file['dependencies'],
		$asset_file['version'],
		true
	);
	wp_enqueue_style(
		'museum-remote-style-front',
		MR_REACT_PATH . 'style-index.css',
		[],
		filemtime( MR_REACT_PATH . 'style-remote.css' )
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_frontend_scripts_and_styles' );
