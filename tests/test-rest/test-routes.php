<?php
/**
 * Tests for all REST API routes.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum\Tests\REST;

require_once __DIR__ . '/base-rest.php';

/**
 * Comprehensive test for all REST API routes.
 */
class RoutesTest extends BaseRESTTest {

	/**
	 * Test object ID.
	 *
	 * @var int
	 */
	protected $object_id;

	/**
	 * Collection ID.
	 *
	 * @var int
	 */
	protected $collection_id;

	/**
	 * Object type names from all kinds.
	 *
	 * @var array
	 */
	protected $kind_type_names = [];

	/**
	 * Setup test environment.
	 */
	public function setUp(): void {
		parent::setUp();

		// This setup would ideally create test objects and collections
		// and gather kind type names
		// This is a placeholder - in a real implementation, you would use actual plugin functions
		// to create test data

		// $this->object_id = create_test_object();
		// $this->collection_id = create_test_collection();
		// $kinds = get_mobject_kinds();
		// foreach ($kinds as $kind) {
		//     $this->kind_type_names[] = $kind->type_name;
		// }
	}

	/**
	 * Test all public-readable routes.
	 */
	public function test_public_read_routes() {
		// These routes should be publicly accessible
		$routes = [
			TEST_REST_NAMESPACE . '/collections',
			TEST_REST_NAMESPACE . '/mobject_kinds',
			TEST_REST_NAMESPACE . '/all',
			TEST_REST_NAMESPACE . '/search',
			TEST_REST_NAMESPACE . '/site_data',
		];

		foreach ($routes as $route) {
			$this->assertRouteIsAccessible($route);
		}
	}

	/**
	 * Test editor-readable routes.
	 */
	public function test_editor_read_routes() {
		// These routes should be accessible by editors
		$routes = [
			TEST_REST_NAMESPACE . '/admin_options',
		];

		foreach ($routes as $route) {
			$this->assertRouteIsAccessible($route, 'GET', $this->editor_id);
		}
	}

	/**
	 * Test admin-writable routes.
	 */
	public function test_admin_write_routes() {
		// These routes should be accessible for writing by admins
		$routes = [
			TEST_REST_NAMESPACE . '/admin_options',
			TEST_REST_NAMESPACE . '/mobject_kinds',
			TEST_REST_NAMESPACE . '/remote_clients',
		];

		foreach ($routes as $route) {
			$this->assertRouteIsAccessible($route, 'POST', $this->admin_id);
		}
	}

	/**
	 * Test routes that require specific IDs.
	 */
	public function test_id_specific_routes() {
		// Uncomment and adjust when you have valid IDs
		
		// Collection routes
		// if (!empty($this->collection_id)) {
		//     $this->assertRouteIsAccessible(TEST_REST_NAMESPACE . '/collections/' . $this->collection_id);
		//     $this->assertRouteIsAccessible(TEST_REST_NAMESPACE . '/collections/' . $this->collection_id . '/objects');
		// }

		// Object routes
		// if (!empty($this->object_id)) {
		//     $this->assertRouteIsAccessible(TEST_REST_NAMESPACE . '/all/' . $this->object_id);
		//     $this->assertRouteIsAccessible(TEST_REST_NAMESPACE . '/all/' . $this->object_id . '/images');
		//     $this->assertRouteIsAccessible(TEST_REST_NAMESPACE . '/all/' . $this->object_id . '/children');
		// }
	}

	/**
	 * Test kind-specific routes.
	 */
	public function test_kind_specific_routes() {
		// This test needs real kind type names to work properly
		// Uncomment when you have actual kind type names

		// foreach ($this->kind_type_names as $type_name) {
		//     // Base route for this kind
		//     $this->assertRouteIsAccessible(TEST_REST_NAMESPACE . '/' . $type_name);
		
		//     // Kind fields route
		//     $this->assertRouteIsAccessible(TEST_REST_NAMESPACE . '/' . $type_name . '/fields');
		
		//     // Kind-specific object routes (if we have a valid object ID)
		//     if (!empty($this->object_id)) {
		//         $this->assertRouteIsAccessible(TEST_REST_NAMESPACE . '/' . $type_name . '/' . $this->object_id);
		//         $this->assertRouteIsAccessible(TEST_REST_NAMESPACE . '/' . $type_name . '/' . $this->object_id . '/images');
		//         $this->assertRouteIsAccessible(TEST_REST_NAMESPACE . '/' . $type_name . '/' . $this->object_id . '/children');
		//     }
		
		//     // Kind-specific mobject_kinds route
		//     $this->assertRouteIsAccessible(TEST_REST_NAMESPACE . '/mobject_kinds/' . $type_name);
		// }
	}
}