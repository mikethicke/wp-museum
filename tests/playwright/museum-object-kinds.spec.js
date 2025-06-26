const { test, expect } = require("@playwright/test");
const { loginAsAdmin, deleteAllObjectKinds } = require("./utils");
const fs = require("fs");
const path = require("path");

test.describe("Museum Object Kinds", () => {
  test("can create a basic object kind", async ({ page }) => {
    await loginAsAdmin(page);

    // Clean up any existing object kinds before starting
    await deleteAllObjectKinds(page);

    // Navigate to Museum Administration > Objects
    await page.goto("/wp-admin/admin.php?page=wpm-react-admin-objects");
    await page.waitForLoadState("networkidle");

    // Wait for React app to load
    await page.waitForSelector(".museum-admin-main", { timeout: 15000 });

    // Click "Add New" button
    await page.click('button:has-text("Add New")');

    // Wait for the edit page to load
    await page.waitForSelector(".edit-header h1", { timeout: 10000 });

    // Verify we're on the edit page
    const headerText = await page.textContent(".edit-header h1");
    expect(headerText).toBe("New Object Type");

    // Fill in basic object kind information
    await page.fill('input[value="New Object Type"]', "Test Instrument");
    await page.fill('input[value=""]', "Test Instruments"); // plural form
    await page.fill(
      "textarea",
      "A test scientific instrument for automated testing",
    );

    // Add a basic field
    await page.click('button:has-text("Add New Field")');

    // Wait for the field to be added and form to appear
    await page.waitForSelector(".field-form", { timeout: 10000 });

    // Fill in field details - the Label field
    await page.fill(
      '.field-form .field-section label:has-text("Label") input',
      "Test Field",
    );
    await page.selectOption(
      '.field-form .field-section label:has-text("Type") select',
      "plain",
    );

    // Save the object kind
    await page.click('button:has-text("Save")');

    // Wait for save to complete
    await page.waitForSelector(".is-saving", {
      state: "hidden",
      timeout: 15000,
    });

    // Navigate back to main objects page
    await page.goto("/wp-admin/admin.php?page=wpm-react-admin-objects");
    await page.waitForLoadState("networkidle");
    await page.waitForSelector(".museum-admin-main", { timeout: 15000 });

    // Verify the new object kind appears in the list
    await expect(page.locator("text=Test Instrument").first()).toBeVisible();

    // Cleanup: Delete all object kinds
    await deleteAllObjectKinds(page);
  });

  test("can create object kind matching instrument schema and export matches expected JSON", async ({
    page,
  }) => {
    await loginAsAdmin(page);

    // Clean up any existing object kinds before starting
    await deleteAllObjectKinds(page);
    // Load the expected instrument schema from test data
    const instrumentSchemaPath = path.join(
      __dirname,
      "../data/museum/instrument-simplified.json",
    );
    const expectedSchema = JSON.parse(
      fs.readFileSync(instrumentSchemaPath, "utf8"),
    );

    // Navigate to Museum Administration > Objects
    await page.goto("/wp-admin/admin.php?page=wpm-react-admin-objects");
    await page.waitForLoadState("networkidle");

    // Wait for React app to load
    await page.waitForSelector(".museum-admin-main", { timeout: 15000 });

    // Click "Add New" button
    await page.click('button:has-text("Add New")');

    // Wait for the edit page to load
    await page.waitForSelector(".edit-header h1", { timeout: 10000 });

    // Fill in object kind basic information to match instrument schema
    await page.fill('input[value="New Object Type"]', expectedSchema.label);
    await page.fill(
      'label:has-text("Object Name (Plural)") input',
      expectedSchema.label_plural,
    );
    await page.fill("textarea", expectedSchema.description);

    // Set hierarchical option if needed
    if (expectedSchema.hierarchical) {
      await page.check(
        'input[type="checkbox"]:near(:text("Must be categorized"))',
      );
    }

    // Add fields to match the instrument schema
    const fieldsArray = Object.values(expectedSchema.fields).sort(
      (a, b) => a.display_order - b.display_order,
    );

    for (const fieldData of fieldsArray) {
      // Click "Add New Field" button
      await page.click('button:has-text("Add New Field")');

      // Wait for the new field form to appear
      await page.waitForSelector(".field-form >> nth=-1", { timeout: 10000 });

      // Get the last field form (the newly added one)
      const fieldForm = page.locator(".field-form").last();

      // Fill in field information
      await fieldForm
        .locator('label:has-text("Label") input')
        .fill(fieldData.name);
      await fieldForm
        .locator('label:has-text("Type") select')
        .selectOption(fieldData.type);

      // Set field options
      if (fieldData.public) {
        await fieldForm
          .locator('label:has-text("Public") input[type="checkbox"]')
          .check();
      }
      if (fieldData.required) {
        await fieldForm
          .locator('label:has-text("Required") input[type="checkbox"]')
          .check();
      }
      if (fieldData.quick_browse) {
        await fieldForm
          .locator('label:has-text("Quick Browse") input[type="checkbox"]')
          .check();
      }

      // Fill help text if present
      if (fieldData.help_text) {
        await fieldForm
          .locator('label:has-text("Help Text") input')
          .fill(fieldData.help_text);
      }

      // Set max length if specified and field type supports it
      if (
        fieldData.max_length &&
        fieldData.max_length > 0 &&
        (fieldData.type === "plain" || fieldData.type === "rich")
      ) {
        await fieldForm
          .locator('label:has-text("Max Length") input')
          .fill(fieldData.max_length.toString());
      }

      // Set field schema if present
      if (fieldData.field_schema) {
        await fieldForm
          .locator('label:has-text("Field Schema") input')
          .fill(fieldData.field_schema);
      }
    }

    // Save the object kind
    await page.click('button:has-text("Save")');

    // Wait for save to complete
    await page.waitForSelector(".is-saving", {
      state: "hidden",
      timeout: 20000,
    });

    // Navigate back to main objects page
    await page.goto("/wp-admin/admin.php?page=wpm-react-admin-objects");
    await page.waitForLoadState("networkidle");
    await page.waitForSelector(".museum-admin-main", { timeout: 15000 });

    // Find the created object kind and click Export Kind
    const instrumentRow = page.locator('div:has-text("Instrument")').first();
    await instrumentRow.locator('button:has-text("Export Kind")').click();

    // Wait for download to start
    const downloadPromise = page.waitForEvent("download");
    const download = await downloadPromise;

    // Save the downloaded file temporarily
    const downloadPath = path.join(__dirname, "temp-export.json");
    await download.saveAs(downloadPath);

    // Read and parse the exported JSON
    const exportedContent = fs.readFileSync(downloadPath, "utf8");
    const exportedSchema = JSON.parse(exportedContent);

    // Clean up temp file
    fs.unlinkSync(downloadPath);

    // Compare key properties (excluding auto-generated IDs and system fields)
    expect(exportedSchema.label).toBe(expectedSchema.label);
    expect(exportedSchema.label_plural).toBe(expectedSchema.label_plural);
    expect(exportedSchema.description).toBe(expectedSchema.description);
    expect(exportedSchema.hierarchical).toBe(expectedSchema.hierarchical);

    // Verify fields structure exists and has correct count
    expect(exportedSchema.fields).toBeDefined();
    expect(Object.keys(exportedSchema.fields)).toHaveLength(
      Object.keys(expectedSchema.fields).length,
    );

    // Check each field has the expected properties
    for (const [fieldId, expectedField] of Object.entries(
      expectedSchema.fields,
    )) {
      const exportedField = Object.values(exportedSchema.fields).find(
        (f) => f.slug === expectedField.slug,
      );
      expect(exportedField).toBeDefined();
      expect(exportedField.name).toBe(expectedField.name);
      expect(exportedField.type).toBe(expectedField.type);
      expect(exportedField.public).toBe(expectedField.public);
      expect(exportedField.required).toBe(expectedField.required);
      expect(exportedField.quick_browse).toBe(expectedField.quick_browse);

      if (expectedField.help_text) {
        expect(exportedField.help_text).toBe(expectedField.help_text);
      }

      if (expectedField.max_length) {
        expect(exportedField.max_length).toBe(expectedField.max_length);
      }

      if (expectedField.field_schema) {
        expect(exportedField.field_schema).toBe(expectedField.field_schema);
      }
    }

    // Cleanup: Delete all object kinds
    await deleteAllObjectKinds(page);
  });

  test("can navigate to objects admin page successfully", async ({ page }) => {
    await loginAsAdmin(page);
    // Navigate to Museum Administration > Objects
    await page.goto("/wp-admin/admin.php?page=wpm-react-admin-objects");
    await page.waitForLoadState("networkidle");

    // Check that the page has the React container first
    await expect(
      page.locator("#wpm-react-admin-app-container-objects"),
    ).toBeVisible();

    // Wait for React app to load with a longer timeout
    await page.waitForSelector(".museum-admin-main", { timeout: 15000 });

    // Verify page loaded correctly
    await expect(
      page.locator('h1:has-text("Museum Administration")'),
    ).toBeVisible();

    // Verify Add New button is present
    await expect(page.locator('button:has-text("Add New")')).toBeVisible();
  });
});
