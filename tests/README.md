# Tests Directory Structure

This directory contains all test files and test infrastructure for the WP Museum plugin.

## Directory Structure

```
tests/
├── README.md              # This file
├── bootstrap.php          # PHPUnit bootstrap file
├── wp-tests-config.php    # WordPress test configuration
├── includes/              # WordPress test framework scaffolding
├── data/                  # Test data and fixtures
├── phpunit/               # PHPUnit test files
│   ├── test-plugin-loaded.php  # Basic plugin loading test
│   ├── classes/           # Tests for plugin classes
│   └── rest/              # Tests for REST API endpoints
└── playwright/            # Playwright/E2E tests (future)
```

## Running Tests

### PHPUnit Tests

All PHPUnit tests are located in the `phpunit/` directory. Run them using:

```bash
# Run all PHPUnit tests
lando phpunit

# Run a specific test file
lando phpunit tests/phpunit/test-plugin-loaded.php

# Run tests in a specific directory
lando phpunit tests/phpunit/classes/

# Run with debug mode
lando phpunit-debug
```

### Test Categories

- **classes/**: Unit tests for plugin classes and core functionality
- **rest/**: Tests for REST API endpoints and controllers
- **test-plugin-loaded.php**: Basic test to verify plugin loads correctly

## Adding New Tests

### PHPUnit Tests

1. Create new test files in the appropriate subdirectory under `phpunit/`
2. Follow the naming convention: `test-*.php`
3. Extend the appropriate WordPress test case class
4. Tests will be automatically discovered by PHPUnit

### Other Test Types

- **playwright/**: Reserved for future Playwright/E2E tests
- Additional test frameworks can be added as new subdirectories

## Test Infrastructure

- **bootstrap.php**: Initializes WordPress test environment
- **wp-tests-config.php**: WordPress database and configuration settings
- **includes/**: WordPress core test framework files and utilities
- **data/**: Test fixtures, sample data, and mock objects

## Notes

- The scaffolding files (`includes/`, `data/`, `bootstrap.php`, etc.) are part of the WordPress test framework and should not be modified unless necessary
- All actual test files have been moved to the `phpunit/` subdirectory to separate them from scaffolding
- The PHPUnit configuration (`phpunit.xml.dist`) has been updated to look for tests in the `phpunit/` directory