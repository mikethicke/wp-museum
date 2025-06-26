const { expect } = require("@playwright/test");

/**
 * Utility functions for WordPress Playwright tests
 *
 * This module provides common functionality for testing WordPress sites with Playwright:
 * - Admin login functionality
 * - Plugin activation/deactivation
 * - Museum for WordPress specific helpers
 *
 * @example
 * // Basic usage in a test file:
 * const { loginAsAdmin, activateMuseumPlugin, setupMuseumTest } = require("./utils");
 *
 * test("my test", async ({ page }) => {
 *   await loginAsAdmin(page);
 *   // or
 *   await setupMuseumTest(page); // login + activate plugin
 * });
 */

/**
 * Login as WordPress admin user
 *
 * This function handles the complete login process, including:
 * - Checking if already logged in
 * - Navigating to login page
 * - Filling credentials
 * - Verifying successful login
 * - Handling various browser-specific timing issues
 *
 * @param {import('@playwright/test').Page} page - Playwright page object
 * @param {string|null} adminUser - Admin username (defaults to TEST_WP_ADMIN_USER env var or 'admin')
 * @param {string|null} adminPass - Admin password (defaults to TEST_WP_ADMIN_PASS env var or 'admin')
 * @returns {Promise<string>} The username that was logged in
 *
 * @example
 * // Login with default credentials
 * const username = await loginAsAdmin(page);
 *
 * @example
 * // Login with custom credentials
 * const username = await loginAsAdmin(page, "myuser", "mypass");
 *
 * @throws {Error} If login fails or cannot be verified
 */
async function loginAsAdmin(page, adminUser = null, adminPass = null) {
  // Get credentials from parameters or environment variables
  const username = adminUser || process.env.TEST_WP_ADMIN_USER || "admin";
  const password = adminPass || process.env.TEST_WP_ADMIN_PASS || "admin";

  // First check if we're already logged in by trying to access admin
  await page.goto("/wp-admin/");
  await page.waitForLoadState("networkidle");

  // Check if we're already in admin area
  const currentUrl = page.url();
  if (
    currentUrl.includes("/wp-admin/") &&
    !currentUrl.includes("wp-login.php")
  ) {
    const adminBarExists = await page.locator("#wpadminbar").isVisible();
    const dashboardExists = await page.locator("#wpbody-content").isVisible();
    const adminMenuExists = await page.locator("#adminmenu").isVisible();

    if (adminBarExists || dashboardExists || adminMenuExists) {
      console.log("Already logged in, skipping login process");
      return username;
    }
  }

  // Wait for login form to load
  await page.waitForLoadState("networkidle");

  // Check if login form exists
  const loginFormExists = await page.locator("#loginform").isVisible();
  if (!loginFormExists) {
    throw new Error("Login form not found on login page");
  }

  // Clear any existing values and fill in login credentials
  await page.locator("#user_login").clear();
  await page.locator("#user_pass").clear();
  await page.locator("#user_login").type(username, { delay: 100 });
  await page.locator("#user_pass").type(password, { delay: 100 });

  await page.waitForTimeout(100);

  await page.locator("#user_pass").press("Enter");

  //await page.click("#wp-submit");
  await page.waitForNavigation({ waitUntil: "networkidle" });

  // Check if we're on login page with error
  const currentUrlAfterLogin = page.url();
  if (currentUrlAfterLogin.includes("wp-login.php")) {
    const loginError = await page.locator("#login_error").isVisible();
    if (loginError) {
      const errorText = await page.locator("#login_error").textContent();
      throw new Error(`Login failed: ${errorText}`);
    }
  }

  await page.goto("/wp-admin/");

  // Verify we're in admin area
  const finalUrl = page.url();
  const adminBarExists = await page.locator("#wpadminbar").isVisible();
  const dashboardExists = await page.locator("#wpbody-content").isVisible();
  const adminMenuExists = await page.locator("#adminmenu").isVisible();

  const isInAdmin =
    finalUrl.includes("/wp-admin/") &&
    (adminBarExists || dashboardExists || adminMenuExists);

  if (!isInAdmin) {
    throw new Error(
      `Login verification failed. Current URL: ${finalUrl}, Admin elements found: bar=${adminBarExists}, dashboard=${dashboardExists}, menu=${adminMenuExists}`,
    );
  }

  return username;
}

/**
 * Activate a WordPress plugin
 *
 * This function will:
 * - Navigate to the plugins page
 * - Check if plugin is already active
 * - Activate the plugin if not already active
 * - Verify activation was successful
 * - Not fail if plugin is already activated
 *
 * @param {import('@playwright/test').Page} page - Playwright page object
 * @param {string} pluginName - Display name of the plugin to activate
 * @param {string|null} pluginSlug - Plugin slug/identifier (optional, will be derived from name if not provided)
 * @returns {Promise<boolean>} True if plugin is active after the operation
 *
 * @example
 * // Activate by plugin name
 * await activatePlugin(page, "Hello Dolly");
 *
 * @example
 * // Activate with specific slug
 * await activatePlugin(page, "Hello Dolly", "hello-dolly");
 *
 * @throws {Error} If plugin cannot be found or activation fails
 */
async function activatePlugin(page, pluginName, pluginSlug = null) {
  // Derive plugin slug from name if not provided
  const slug =
    pluginSlug ||
    pluginName
      .toLowerCase()
      .replace(/\s+/g, "-")
      .replace(/[^\w-]/g, "");

  // Navigate to plugins page
  await page.goto("/wp-admin/plugins.php");
  await page.waitForLoadState("networkidle");

  // Check if plugin is already active
  const pluginRow = page
    .locator(`tr[data-slug="${slug}"], tr:has-text("${pluginName}")`)
    .first();
  const isActive = await pluginRow.locator(".plugin-title strong").isVisible();

  if (isActive) {
    // Plugin is already active, verify by checking for "Deactivate" link
    const deactivateLink = await pluginRow
      .locator('a:has-text("Deactivate")')
      .isVisible();
    if (deactivateLink) {
      return true;
    }
  }

  // Try to find and click activate link
  const activateLink = pluginRow.locator('a:has-text("Activate")');
  const activateLinkExists = await activateLink.isVisible();

  if (activateLinkExists) {
    await activateLink.click();
    await page.waitForLoadState("networkidle");

    // Wait for success message or page reload
    await page.waitForTimeout(1000);

    // Verify activation by checking for success notice or deactivate link
    const successNotice = page.locator(
      '.notice-success:has-text("Plugin activated")',
    );
    const deactivateLink = pluginRow.locator('a:has-text("Deactivate")');

    const isActivated =
      (await successNotice.isVisible()) || (await deactivateLink.isVisible());

    if (isActivated) {
      console.log(`Plugin "${pluginName}" activated successfully`);
      return true;
    } else {
      throw new Error(`Failed to activate plugin "${pluginName}"`);
    }
  } else {
    // Plugin might already be active or not found
    const pluginExists = await pluginRow.isVisible();
    if (pluginExists) {
      // Check if it's already active by looking for deactivate link
      const deactivateLink = await pluginRow
        .locator('a:has-text("Deactivate")')
        .isVisible();
      if (deactivateLink) {
        console.log(`Plugin "${pluginName}" is already active`);
        return true;
      } else {
        throw new Error(`Plugin "${pluginName}" found but cannot be activated`);
      }
    } else {
      throw new Error(`Plugin "${pluginName}" not found`);
    }
  }
}

/**
 * Activate the Museum for WordPress plugin specifically
 *
 * This is a convenience wrapper around activatePlugin() for the main
 * Museum for WordPress plugin. It uses the correct plugin name and slug.
 *
 * @param {import('@playwright/test').Page} page - Playwright page object
 * @returns {Promise<boolean>} True if plugin is active after the operation
 *
 * @example
 * await activateMuseumPlugin(page);
 *
 * @throws {Error} If plugin cannot be found or activation fails
 */
async function activateMuseumPlugin(page) {
  return await activatePlugin(page, "Museum for WordPress", "wp-museum");
}

/**
 * Ensure admin is logged in and Museum plugin is activated
 *
 * This is a convenience function that combines loginAsAdmin() and
 * activateMuseumPlugin() for tests that need both operations.
 * Most Museum for WordPress tests should use this function.
 *
 * @param {import('@playwright/test').Page} page - Playwright page object
 * @param {string|null} adminUser - Admin username (optional, uses env vars or defaults)
 * @param {string|null} adminPass - Admin password (optional, uses env vars or defaults)
 * @returns {Promise<void>}
 *
 * @example
 * // Setup for a typical Museum test
 * test("museum functionality", async ({ page }) => {
 *   await setupMuseumTest(page);
 *   // Now logged in as admin with Museum plugin active
 *   // ... rest of test
 * });
 *
 * @example
 * // Setup with custom credentials
 * await setupMuseumTest(page, "customuser", "custompass");
 *
 * @throws {Error} If login or plugin activation fails
 */
async function setupMuseumTest(page, adminUser = null, adminPass = null) {
  await loginAsAdmin(page, adminUser, adminPass);
  await activateMuseumPlugin(page);
}

/**
 * Delete all existing object kinds for test cleanup
 *
 * This function navigates to the objects admin page and deletes all
 * existing object kinds by clicking their Delete buttons and confirming
 * the deletion dialogs. Useful for cleaning up before/after tests.
 *
 * @param {import('@playwright/test').Page} page - Playwright page object
 * @returns {Promise<void>}
 *
 * @example
 * // Clean up all object kinds before a test
 * await deleteAllObjectKinds(page);
 *
 * @throws {Error} If navigation or deletion operations fail
 */
async function deleteAllObjectKinds(page) {
  // Navigate to Museum Administration > Objects
  await page.goto("/wp-admin/admin.php?page=wpm-react-admin-objects");
  await page.waitForLoadState("networkidle");

  // Wait for React app to load
  await page.waitForSelector(".museum-admin-main", { timeout: 15000 });

  // Set up dialog handler to automatically accept deletion confirmations
  // Remove any existing dialog handlers to avoid conflicts
  page.removeAllListeners("dialog");

  // Set up dialog handler to automatically accept deletion confirmations
  page.on("dialog", (dialog) => dialog.accept());

  // Keep deleting until no more delete buttons exist
  let hasDeleteButtons = true;
  while (hasDeleteButtons){
    // Look for any Delete buttons
    const deleteButtons = page.locator('button:has-text("Delete")');
    const deleteButtonCount = await deleteButtons.count();

    if (deleteButtonCount === 0) {
      hasDeleteButtons = false;
      break;
    }

    // Click the first Delete button
    await deleteButtons.first().click();

    // Wait a moment for the deletion to process and page to update
    await page.waitForTimeout(200);

    // Wait for any loading states to complete
    await page.waitForLoadState("networkidle");
  }
}

module.exports = {
  loginAsAdmin,
  activatePlugin,
  activateMuseumPlugin,
  setupMuseumTest,
  deleteAllObjectKinds,
};
