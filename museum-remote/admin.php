<?php
/**
 * The admin section of the plugin, controlling plugin settings.
 *
 * @package MikeThicke\MuseumRemote
 */

namespace MikeThicke\MuseumRemote;

/**
 * Register the admin page for the plugin. This will appear under the Settings
 * menu.
 */
function register_admin_page() {
	add_submenu_page(
		'options-general.php',
		'Museum Remote',
		'Museum Remote',
		'manage_options',
		'museum-remote-admin',
		__NAMESPACE__ . '\admin_page'
	);
}
add_action( 'admin_menu', __NAMESPACE__ . '\register_admin_page' );

/**
 * Returns a placeholder page that will be replaced by the React admin app.
 */
function admin_page() {
	echo (
		"<div id='museum-remote-admin-container'>Loading...</div>"
	);
}
