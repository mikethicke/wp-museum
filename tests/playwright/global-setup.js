// Global setup for Playwright tests
// This file exposes environment variables to all tests

async function globalSetup(config) {
  // Make test credentials available globally
  process.env.TEST_WP_ADMIN_USER = process.env.TEST_WP_ADMIN_USER || 'admin';
  process.env.TEST_WP_ADMIN_PASS = process.env.TEST_WP_ADMIN_PASS || 'admin';
  process.env.TEST_WP_ADMIN_EMAIL = process.env.TEST_WP_ADMIN_EMAIL || 'admin@test.com';

  // Database credentials (if needed for tests)
  process.env.TEST_DB_NAME = process.env.TEST_DB_NAME || 'wptest';
  process.env.TEST_DB_USER = process.env.TEST_DB_USER || 'wptest';
  process.env.TEST_DB_PASS = process.env.TEST_DB_PASS || 'wptest';
  process.env.TEST_DB_HOST = process.env.TEST_DB_HOST || 'wp-test-database';

  console.log('Global setup completed - environment variables loaded');
}

module.exports = globalSetup;
