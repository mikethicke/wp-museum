<?php
/**
 * REST endpoints for getting and setting Museum Remote options.
 *
 * /remote_data Options stored for the remote museum plugin.
 * [
 *     remote_url : The URL of the host museum.
 *     token      : Unique token identifying remote site to host.
 * ]
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
	 * /remote_data Options stored for the remote museum plugin.
	 */
	register_rest_route(
		REST_NAMESPACE,
		'/remote_data',
		[
			[
				'methods'  => 'GET',
				'callback' => function () {
					return get_site_option( 'museum-remote-data' );
				},
			],
			[
				'methods'             => 'POST',
				'permission_callback' => function () {
					return current_user_can( 'manage_options' );
				},
				'callback'            => function ( $request ) {
					$request_data = $request->get_json_params();
					$new_data     = [
						'url'        => empty( $request_data['url'] ) ?
							null :
							esc_url( $request_data['url'] ),
						'uuid'       => empty( $request_data['uuid'] ) ?
							null :
							sanitize_text_field( $request_data['uuid'] ),
						'title'      => sanitize_text_field( get_bloginfo( 'name' ) ),
						'host_title' => empty( $request_data['host_title'] ) ?
							null :
							sanitize_text_field( $request_data['host_title'] ),
					];
					return update_option(
						'museum-remote-data',
						$new_data
					);
				},
			],
		]
	);
}
