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
const DB_VERSION     = '0.0.15';               // Change to update db structure.
const DB_SHOW_ERRORS = true;                   // Have WP report db errors.
const IMAGE_DIR      = 'wp-museum';            // Directory to save exported images.

/**
 * Default number of posts per page to retrieve in query_associated_objects.
 *
 * @see collection-functions.php::query_associated_objects()
 */
const DEF_POSTS_PER_PAGE = 20;

/*
 * Classes
 */
require_once 'class-customposttype.php';
require_once 'class-metabox.php';
require_once 'class-objectposttype.php';
require_once 'class-objectkind.php';
require_once 'class-mobjectfield.php';

/*
 * Functions
 */
require_once 'database-functions.php';
require_once 'object-functions.php';
require_once 'collection-functions.php';
require_once 'object-admin-functions.php';
require_once 'custom-post-type-functions.php';
require_once 'display.php';
require_once 'customization.php';

/*
 * Scripts
 */
require_once 'actions-filters.php';
require_once 'capabilities.php';
require_once 'object-post-types.php';
require_once 'object-ajax.php';
require_once 'collection-post-type.php';
require_once 'quick-browse.php';
require_once 'import-export.php';
require_once 'database-upgrade.php';

