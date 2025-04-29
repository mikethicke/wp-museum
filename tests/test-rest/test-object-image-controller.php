<?php
/**
 * Tests for Object_Image_Controller.
 *
 * @package MikeThicke\WPMuseum
 */

namespace MikeThicke\WPMuseum\Tests\REST;

require_once __DIR__ . '/base-rest.php';

/**
 * Tests for Object_Image_Controller endpoints.
 */
class ObjectImageControllerTest extends BaseRESTTest {

	/**
	 * Test object ID.
	 *
	 * @var int
	 */
	protected $object_id;

	/**
	 * Setup test environment.
	 */
	public function setUp(): void {
		parent::setUp();

		// Create a test object
		// This assumes a function exists to create a test object
		// Adjust as necessary for your actual plugin implementation
		// $this->object_id = your_create_object_function();
	}

	/**
	 * Test object image routes.
	 */
	public function test_object_image_routes() {
		// General object images route (requires a valid object ID)
		// Uncomment when you have a valid object ID
		// $this->assertRouteIsAccessible( TEST_REST_NAMESPACE . '/all/' . $this->object_id . '/images' );

		// Only editors+ should be able to edit images
		// $this->assertRouteStatusEquals( TEST_REST_NAMESPACE . '/all/' . $this->object_id . '/images', 401, 'POST', 0 );
		// $this->assertRouteStatusEquals( TEST_REST_NAMESPACE . '/all/' . $this->object_id . '/images', 403, 'POST', $this->user_id );
		// $this->assertRouteIsAccessible( TEST_REST_NAMESPACE . '/all/' . $this->object_id . '/images', 'POST', $this->editor_id );
		// $this->assertRouteIsAccessible( TEST_REST_NAMESPACE . '/all/' . $this->object_id . '/images', 'POST', $this->admin_id );

		// Type-specific object images routes depend on kind type names
		// This test will need to be adapted to use actual kind type names from your system
		// Example:
		// $kinds = get_mobject_kinds();
		// foreach ( $kinds as $kind ) {
		//     $this->assertRouteIsAccessible( TEST_REST_NAMESPACE . '/' . $kind->type_name . '/' . $this->object_id . '/images' );
		// }
	}
}