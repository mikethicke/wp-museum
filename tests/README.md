# WP Museum Testing

This project uses a custom testing setup that leverages Lando's `wp-test-server` service instead of the traditional WordPress test suite's temporary installation approach.

## Architecture

- **wp-test-server**: A dedicated Lando service running a clean WordPress installation at `/app/wordpress-test/`
- **wp-test-database**: A separate MariaDB database for testing
- **PHPUnit Integration**: Tests run against the live WordPress instance in `wp-test-server`

## Benefits

- **Consistent Environment**: Tests run against the same WordPress setup every time
- **Faster Setup**: No need to download/install WordPress for each test run
- **Real Environment**: Tests run against a real WordPress installation, not a mock
- **Isolated**: Test database and WordPress instance are completely separate from development

## Running Tests

### Quick Test Run
```bash
lando test
```
This resets the test environment and runs all PHPUnit tests.

### Manual Steps
```bash
# Reset test environment to clean state
lando test-reset

# Run PHPUnit tests
lando phpunit

# Run PHPUnit with debugging
lando phpunit-debug
```

### Test Structure

- `tests/bootstrap.php` - PHPUnit bootstrap file
- `tests/wp-tests-config.php` - WordPress test configuration
- `tests/phpunit/` - PHPUnit test files
- `tests/includes/` - WordPress test framework files

## Environment Variables

The test environment uses these environment variables (set in `.lando.yml`):

- `TEST_DB_NAME`: Test database name (default: wptest)
- `TEST_DB_USER`: Test database user (default: wptest)
- `TEST_DB_PASS`: Test database password (default: wptest)
- `TEST_DB_HOST`: Test database host (default: wp-test-database)
- `TEST_WP_ADMIN_USER`: WordPress admin username (default: admin)
- `TEST_WP_ADMIN_PASS`: WordPress admin password (default: admin)
- `TEST_WP_ADMIN_EMAIL`: WordPress admin email (default: admin@test.com)

## Writing Tests

Tests should extend `WP_UnitTestCase` from the WordPress test framework:

```php
<?php

class MyFeatureTest extends WP_UnitTestCase {
    
    public function test_my_feature() {
        // Your test code here
        $this->assertTrue(true);
    }
}
```

## Database State

Each test run starts with a completely fresh WordPress installation:
- Database is dropped and recreated
- WordPress is reinstalled with default settings
- The wp-museum plugin is activated
- No other plugins or customizations are present

This ensures tests are isolated and reproducible.