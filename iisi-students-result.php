<?php

/**
 * Plugin Name: IISI Students Result
 * Description: Manage and import student results with CSV functionality
 * Version: 1.0.0
 * Author: WebPlover
 * Author URI: https://webplover.com/
 */

// Prevent direct access
if (!defined('ABSPATH')) {
  exit;
}

// Define plugin constants
define('IISI_RESULT_VERSION', microtime());
define('IISI_RESULT_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('IISI_RESULT_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once IISI_RESULT_PLUGIN_DIR . 'inc/post-types.php';
require_once IISI_RESULT_PLUGIN_DIR . 'inc/taxonomies.php';
require_once IISI_RESULT_PLUGIN_DIR . 'inc/acf-fields.php';
require_once IISI_RESULT_PLUGIN_DIR . 'inc/importer.php';


// test
require_once IISI_RESULT_PLUGIN_DIR . 'inc/test.php';
require_once IISI_RESULT_PLUGIN_DIR . 'inc/result-form.php';

// Register activation hook

register_activation_hook(__FILE__, function () {

  // Flush rewrite rules
  flush_rewrite_rules();
});

// Register deactivation hook

register_deactivation_hook(__FILE__, function () {
  // Flush rewrite rules
  flush_rewrite_rules();
});
