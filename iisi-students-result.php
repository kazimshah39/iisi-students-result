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
require_once IISI_RESULT_PLUGIN_DIR . 'inc/acf/acf.php';
require_once IISI_RESULT_PLUGIN_DIR . 'inc/generate_sample_csv.php';
require_once IISI_RESULT_PLUGIN_DIR . 'inc/importer.php';
require_once IISI_RESULT_PLUGIN_DIR . 'inc/frontend/form_shortcode.php';

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


/**
 * Enqueue
 */


add_action('wp_enqueue_scripts',  function () {
  if (is_page('results')) {
    wp_enqueue_style('iisi-result-style', IISI_RESULT_PLUGIN_URL . 'css/style.css', [], IISI_RESULT_VERSION);
    wp_enqueue_style('iisi-result-print-style', IISI_RESULT_PLUGIN_URL . 'css/result-print.css', [], IISI_RESULT_VERSION);
    wp_enqueue_script('iisi-result-scripts', IISI_RESULT_PLUGIN_URL . 'js/scripts.js', [], IISI_RESULT_VERSION, true);
  }
});
