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
	if ( ! origin_authorized() ) {
		return new WP_Error(
			'origin-blocked',
			'Remote site has been blocked from connecting.',
			[ 'status' => 403 ]
		);
	}
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
	if ( $remote_client->save_to_db() !== false ) {
		return get_site_data();
	}
	return new WP_Error(
		'database-error',
		'Error registering remote site.',
		[ 'status' => 500 ]
	);
}

/**
 * Callback to update client data from (same origin) REST POST request.
 *
 * @param WP_REST_Request $request The REST request.
 */
function update_clients_from_rest( $request ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return new WP_Error(
			'permission-denied',
			'You do not have permission to access this resource',
			[ 'status' => 403 ]
		);
	}

	$updated_client_data = $request->get_json_params();
	if ( ! $updated_client_data ) {
		return true;
	}
	foreach ( $updated_client_data as $updated_client ) {
		$client_object = RemoteClient::from_rest( $updated_client );
		if ( true == $updated_client['delete'] ) {
			$client_object->delete_from_db();
		} else {
			$client_object->save_to_db();
		}
	}
	return true;
}

/**
 * Checks if a cross-origin request to the rest api is authorized.
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
	if ( ! get_option( 'allow_remote_requests' ) ) {
		return false;
	};
	if ( ! origin_authorized() ) {
		return false;
	}
	if ( get_option( 'allow_unregistered_requests' ) ) {
		return true;
	}
	$uuid = $request->get_param( 'uuid' );
	if ( empty( $uuid ) ) {
		return false;
	}
	$client = RemoteClient::from_uuid( $uuid );
	if ( is_null( $client->client_id ) || $client->blocked ) {
		return false;
	}
	return true;
}

/**
 * Checks if the origin domain is authorized to make REST requests.
 *
 * @param string|null $origin The url of the origin, or null to use $_SERVER['HTTP_ORIGIN'].
 */
function origin_authorized( $origin = null ) {
	if ( ! get_option( 'allow_remote_requests' ) ) {
		return false;
	};
	$rest_authorized_domains = get_option( 'rest_authorized_domains' );
	if ( is_null( $origin ) ) {
		if ( empty( $_SERVER['HTTP_ORIGIN'] ) ) {
			return false;
		}
		$origin = esc_url_raw( wp_unslash( $_SERVER['HTTP_ORIGIN'] ) );
	}

	$authorized = false;
	if ( ! $rest_authorized_domains ) {
		$authorized = true;
	} else {
		$authorized_domains = array_map( 'trim', explode( ',', $rest_authorized_domains ) );
		foreach ( $authorized_domains as $domain ) {
			if ( strpos( $origin, $domain ) !== false ) {
				$authorized = true;
				break;
			}
		}
	}
	return $authorized;
}

/**
 * Allows REST requests from other sites.
 */
function handle_preflight() {
	if ( ! get_option( 'allow_remote_requests' ) ) {
		return;
	};
	if ( empty( $_SERVER['HTTP_ORIGIN'] ) ) {
		return;
	}

	$origin = esc_url_raw( wp_unslash( $_SERVER['HTTP_ORIGIN'] ) );
	if ( ! origin_authorized( $origin ) ) {
		return;
	}

	header( "Access-Control-Allow-Origin: $origin" );
	header( 'Access-Control-Allow-Methods: POST, GET' );
	header( 'Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization' );
}
add_action( 'rest_api_init', __NAMESPACE__ . '\handle_preflight' );
