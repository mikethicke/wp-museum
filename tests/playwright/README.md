# Playwright Testing Utilities for Museum for WordPress

This directory contains Playwright end-to-end tests and utilities for the Museum for WordPress plugin.

## Setup

Tests are configured to run against the local development environment using Lando. The base URL is set to `https://wp-test.lndo.site` in the Playwright configuration.

### Prerequisites

- Lando environment set up and running
- WordPress site accessible at `https://wp-test.lndo.site`
- Admin credentials configured (see Environment Variables below)

### Environment Variables

The following environment variables can be set to customize test behavior:

- `TEST_WP_ADMIN_USER` - WordPress admin username (default: "admin")
- `TEST_WP_ADMIN_PASS` - WordPress admin password (default: "admin")
- `TEST_WP_ADMIN_EMAIL` - WordPress admin email (default: "admin@test.com")

## Running Tests

```bash
# Run all Playwright tests
lando playwright

# Run with HTML reporter
lando playwright-html

# Run specific test file
lando playwright tests/playwright/plugin-basic-functionality.spec.js

# Run tests sequentially (recommended for login-heavy tests)
lando playwright --workers=1

# Run specific test by name
lando playwright --grep="can activate Museum for WordPress plugin"
```

## Test Utilities

The `utils.js` file provides common functionality for WordPress and Museum plugin testing:

### `loginAsAdmin(page, adminUser?, adminPass?)`

Logs in as WordPress admin user. Handles various edge cases including:
- Checking if already logged in
- Browser-specific timing issues
- Login verification across different browsers

```javascript
const { loginAsAdmin } = require("./utils");

test("my test", async ({ page }) => {
  await loginAsAdmin(page);
  // Now logged in as admin
});
```

### `activatePlugin(page, pluginName, pluginSlug?)`

Activates a WordPress plugin by name. Safe to call multiple times - won't fail if plugin is already active.

```javascript
const { activatePlugin } = require("./utils");

test("my test", async ({ page }) => {
  await loginAsAdmin(page);
  await activatePlugin(page, "Hello Dolly");
});
```

### `activateMuseumPlugin(page)`

Convenience function to activate the Museum for WordPress plugin specifically.

```javascript
const { activateMuseumPlugin } = require("./utils");

test("my test", async ({ page }) => {
  await loginAsAdmin(page);
  await activateMuseumPlugin(page);
});
```

### `setupMuseumTest(page, adminUser?, adminPass?)`

Combined function that logs in as admin AND activates the Museum plugin. This is the recommended starting point for most Museum-related tests.

```javascript
const { setupMuseumTest } = require("./utils");

test("museum functionality", async ({ page }) => {
  await setupMuseumTest(page);
  // Now logged in as admin with Museum plugin active
  // ... test Museum functionality
});
```

## Test Files

### `wordpress-basic-functionality.spec.js`
Basic WordPress functionality tests including:
- Front page loads
- Admin login works

### `plugin-basic-functionality.spec.js`
Museum for WordPress plugin tests including:
- Plugin activation
- Admin menu creation
- Settings page access

### `museum-object-kinds.spec.js`
Tests for Museum object kinds functionality (existing).

## Best Practices

1. **Use `setupMuseumTest()` for Museum tests** - This ensures both login and plugin activation
2. **Run tests sequentially for reliability** - Use `--workers=1` flag to avoid login conflicts
3. **Check existing utilities** - Before writing custom login/activation code, use the provided utilities
4. **Handle async operations** - Always await utility functions as they return Promises
5. **Use descriptive test names** - Test names should clearly indicate what functionality is being tested

## Troubleshooting

### Login Issues
- Try running tests with `--workers=1` to avoid concurrent login attempts
- Check that credentials are correct in environment variables
- Verify WordPress site is accessible at the configured base URL

### Plugin Activation Issues
- Ensure the plugin is properly installed in the WordPress site
- Check that the plugin name matches exactly (case-sensitive)
- Verify admin user has permission to activate plugins

### Timeout Issues
- The utilities include built-in waits and retries
- For slow environments, consider increasing timeouts in individual tests
- Use `page.waitForLoadState("networkidle")` for dynamic content

## Configuration

Playwright configuration is in `playwright.config.js`. Key settings:
- Base URL: `https://wp-test.lndo.site`
- Browsers: Chromium, Firefox, WebKit
- Global setup: `global-setup.js` (loads environment variables)
- Parallel execution: Enabled by default (disable with `--workers=1`)