const { test, expect } = require("@playwright/test");

test.describe("WordPress Basic Functionality", () => {
  test("front page loads successfully", async ({ page }) => {
    // Navigate to the WordPress front page and verify response
    const response = await page.goto("/");

    // Verify the page returns a 200 status code
    expect(response.status()).toBe(200);

    // Wait for the page to load completely
    await page.waitForLoadState("networkidle");

    // Check that the page title is set (not empty)
    const title = await page.title();
    expect(title).toBeTruthy();
    expect(title.length).toBeGreaterThan(0);

    // Check that the page has loaded successfully by looking for common WordPress elements
    // This checks for either the body tag or any content wrapper
    const bodyExists = await page.locator("body").isVisible();
    expect(bodyExists).toBe(true);

    // Check that the page contains some content (not completely empty)
    const bodyText = await page.locator("body").textContent();
    expect(bodyText.trim().length).toBeGreaterThan(0);
  });

  test("can login to WordPress admin dashboard", async ({ page }) => {
    // Get credentials from environment variables
    const adminUser = process.env.TEST_WP_ADMIN_USER || "admin";
    const adminPass = process.env.TEST_WP_ADMIN_PASS || "admin";

    // Navigate to WordPress login page
    const response = await page.goto("/wp-login.php");
    expect(response.status()).toBe(200);

    // Wait for login form to load
    await page.waitForLoadState("networkidle");

    // Fill in login credentials
    await page.fill("#user_login", adminUser);
    await page.fill("#user_pass", adminPass);

    // Click login button
    await page.click("#wp-submit");

    // Wait for dashboard to load
    await page.waitForLoadState("networkidle");

    // Verify we're on the dashboard by checking for admin bar
    const adminBarExists = await page.locator("#wpadminbar").isVisible();
    expect(adminBarExists).toBe(true);

    // Verify dashboard title contains "Dashboard"
    const title = await page.title();
    expect(title).toContain("Dashboard");

    // Verify we can see the main dashboard content
    const dashboardContent = await page.locator("#wpbody-content").isVisible();
    expect(dashboardContent).toBe(true);

    // Check that we're logged in as the expected user
    const howdyText = await page
      .locator("#wp-admin-bar-my-account a .display-name")
      .first()
      .textContent();
    expect(howdyText).toBe(adminUser);
  });
});
