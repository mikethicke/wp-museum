# Museum Test Data

This directory contains simplified museum object data for testing the WP Museum plugin.

## Files

### instrument-simplified.json
A simplified version of the instrument object kind definition. This contains:

- **Basic metadata**: kind_id, name, label, description
- **Essential fields only**: 10 core fields instead of the original 32+
- **Field types represented**:
  - `plain`: Simple text fields (accession number, name, manufacturer, etc.)
  - `rich`: HTML/rich text fields (description, condition)
  - `date`: Date fields (date of manufacture)
  - `flag`: Boolean fields (on display status)

### sample-instrument.json
A sample instrument record that follows the simplified schema:

- **Object**: Brass Telescope from 1925
- **Complete field data**: Shows how each field type is populated
- **Images**: Sample image metadata using real test images from `tests/data/images/`
- **Categories/Tags**: Example taxonomies

## Key Simplifications

The original instrument schema had 32 fields. This simplified version includes only the most essential fields:

1. **Accession Number** (required) - Unique identifier
2. **Name** (required) - Display name
3. **Description** - Rich text description
4. **Manufacturer** - Who made it
5. **Date of Manufacture** - When it was made
6. **Primary Materials** - What it's made of
7. **Dimensions** - Physical size
8. **Condition** - Current state
9. **Location** (private) - Storage location
10. **On Display** (private) - Display status flag

## Usage

These files can be used for:
- Unit testing museum object creation/editing
- Integration testing with the WordPress database
- UI testing for museum object display
- API testing for museum data endpoints

### Images

The sample records reference actual test images from `tests/data/images/` including:
- `test-image-large.jpg` - Main product photos
- `canola.jpg`, `waffles.jpg`, `sugarloaf-mountain.jpg` - Various detail shots
- `gradient-square.jpg` - Technical/optical details

This ensures tests can work with real image files rather than broken links.

## Schema Notes

- Field IDs are sequential (1-10)
- `public` flag controls whether field appears on public pages
- `required` flag enforces validation
- `quick_browse` flag indicates fields shown in list views
- `max_length` of 0 means unlimited length (for rich text fields)