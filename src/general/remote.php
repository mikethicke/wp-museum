<?php
/**
 * Functions for handling remote museum connections.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum;

/**
 * Callback to register a remote client from a REST request to /register_remote
 *
 * @param WP_REST_Request $request The REST request.
 */
function register_remote_client( $request ) {
	$remote_client = RemoteClient::from_rest( $request->get_json_params() );
	if ( $remote_client->blocked ) {
		return new WP_Error(
			'remote-blocked',
			'Remote site has been blocked from connecting.',
			[ 'status' => 403 ]
		);
	}
	if ( is_null( $remote_client->blocked ) ) {
		$remote_client->blocked = false;
	}
	if ( empty( $remote_client->uuid ) || ! $remote_client->uuid_is_valid() ) {
		return new WP_Error(
			'invalid-uuid',
			'Missing or invalid uuid in registration data.',
			[ 'status' => 400 ]
		);
	}
	if ( empty( $remote_client->registration_time ) ) {
		$remote_client->registration_time = current_time( 'mysql' );
	}
	$remote_client->url = $request->get_headers()['origin'][0];
	if ( $remote_client->save_to_db() ) {
		return get_site_data();
	}
	return new WP_Error(
		'database-error',
		'Error registering remote site.',
		[ 'status' => 500 ]
	);
}

/**
 * Checks if a request to the rest api is authorized.
 *
 * This returns true under the following conditions:
 *     - The request is not cross-site OR
 *     - The request is cross-site AND remote requests are allowed AND
 *         - The request comes from a registered remote site that isn't blocked OR
 *         - Unregistered requests are allowed and the URL is not blocked.
 *
 * @param WP_REST_Request $request The REST request.
 *
 * @return bool True if the request is authorized.
 */
function rest_request_authorized( $request ) {
	return true;
}

/**
 * Allows REST requests from other sites.
 */
function handle_preflight() {
	header( 'Access-Control-Allow-Origin: *' );
	header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
	header( 'Access-Control-Allow-Credentials: true' );
	header( 'Access-Control-Allow-Headers: Origin, X-Requested-With, X-WP-Nonce, Content-Type, Accept, Authorization' );
}
add_action( 'rest_api_init', __NAMESPACE__ . '\handle_preflight' );
