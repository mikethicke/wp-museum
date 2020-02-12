<?php
/**
 * Museum for WordPress
 *
 * @package MikeThicke\WPMuseum
 * @author Mike Thicke
 *
 * @wordpress-plugin
 * Plugin Name: Museum for WordPress
 * Description: Manages a database of museum objects.
 * Version: 0.1.0
 * Author: Mike Thicke
 * Author URI: http://www.mikethicke.com
 * Text Domain: wp-museum
 */

namespace MikeThicke\WPMuseum;

const WPM_PREFIX     = 'wpm_';                 // Prefix for database tables.
const CSS_VERSION    = '0.0.1';                // Change to force reload of CSS.
const SCRIPT_VERSION = '0.0.1';                // Change to force reload of JS.
const CACHE_GROUP    = 'MikeThicke\WPMuseum';  // For caching db queries.
const DB_VERSION     = '0.0.16';               // Change to update db structure.
const DB_SHOW_ERRORS = true;                   // Have WP report db errors.
const IMAGE_DIR      = 'wp-museum';            // Directory to save exported images.
const REST_NAMESPACE = 'wp-museum/v1';         // Root for rest routes.

/**
 * Is this a development build of the plugin?
 *
 * The development build and release build could have different directory structures,
 * primarily for transpiled code in blocks.
 *
 * @see blocks/blocks.php
 */
const DEV_BUILD = true;

/**
 * Default number of posts per page to retrieve in query_associated_objects.
 *
 * @see collection-functions.php::query_associated_objects()
 */
const DEF_POSTS_PER_PAGE = 20;

/*
 * Classes
 */
require_once 'classes/class-customposttype.php';
require_once 'classes/class-metabox.php';
require_once 'classes/class-objectposttype.php';
require_once 'classes/class-objectkind.php';
require_once 'classes/class-mobjectfield.php';

/*
 * Functions
 */
require_once 'general/database-functions.php';
require_once 'general/object-functions.php';
require_once 'general/collection-functions.php';
require_once 'admin/object-admin-functions.php';
require_once 'general/custom-post-type-functions.php';
require_once 'general/rest.php';
require_once 'public/display.php';
require_once 'admin/customization.php';

/*
 * Scripts
 */
require_once 'actions-filters.php';
require_once 'general/capabilities.php';
require_once 'general/object-post-types.php';
require_once 'general/object-ajax.php';
require_once 'general/collection-post-type.php';
require_once 'admin/quick-browse.php';
require_once 'admin/import-export.php';
require_once 'general/database-upgrade.php';

/*
 * Blocks
 */
require_once 'blocks/blocks.php';

