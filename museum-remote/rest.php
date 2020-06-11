<?php
/**
 * REST endpoints for getting and setting Museum Remote options.
 *
 * /remote_url URL of the remote museuem site.
 *
 * @package MikeThicke\MuseumRemote
 */

namespace MikeThicke\MuseumRemote;

const REST_NAMESPACE = 'museum-remote/v1';

/**
 * Register REST endpoints.
 */
function rest_routes() {
	/**
	 * /remote_url URL of the remote museuem site.
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/remote_url',
		[
			[
				'methods' => 'GET',
				'callback' => function() {
					return get_site_option( 'museum-remote-url' );
				}
			],
			[
				'methods' => 'POST',
				'permission_callback' => function() {
					return current_user_can( 'manage_options' );
				},
				'callback' => function( $request ) {
					return update_option(
						'museum-remote-url',
						esc_url( $request->get_body() )
					);
				}
			]
		]
	);
}
add_action( 'rest_api_init', __NAMESPACE__ . '\rest_routes' );


