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
    wp_enqueue_script('iisi-result-scripts', IISI_RESULT_PLUGIN_URL . 'assets/js/scripts.js', [], IISI_RESULT_VERSION, true);
  }


  if (is_page('results') || (is_singular('iisi_student_result'))) {
    wp_enqueue_style('iisi-result-style', IISI_RESULT_PLUGIN_URL . 'assets/css/style.css', [], IISI_RESULT_VERSION);
    wp_enqueue_style('iisi-result-print-style', IISI_RESULT_PLUGIN_URL . 'assets/css/result-print.css', [], IISI_RESULT_VERSION);


    // 
    // wp_enqueue_style('normalize-css', 'https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css', array(), '8.0.1', 'all');
    wp_enqueue_style('reset-css', 'https://cdn.jsdelivr.net/npm/reset-css@5.0.2/reset.min.css', array(), '8.0.1', 'all');
  }

  if (is_singular('iisi_student_result')) {
    wp_enqueue_style('iisi-single-result', IISI_RESULT_PLUGIN_URL . 'assets/css/single-result.css', [], IISI_RESULT_VERSION);
  }
});


// Remove admin bar for login users on results page
add_action('wp', function () {
  if (is_page('results')) {
    show_admin_bar(false);
  }
});


/**
 * Remove unwanted assets results page
 */

add_action('wp_enqueue_scripts', function () {
  if (is_page('results')) {
    global $wp_styles, $wp_scripts;

    // Allowed styles
    $allowed_styles = array(
      'iisi-result-style',
      'iisi-result-print-style',
      'iisi-single-result',
      // 'normalize-css',
      'reset-css',
    );

    // Allowed scripts
    $allowed_scripts = array(
      'iisi-result-scripts',
    );

    // Remove unwanted styles
    foreach ($wp_styles->queue as $handle) {
      if (!in_array($handle, $allowed_styles)) {
        wp_dequeue_style($handle);
      }
    }

    // Remove unwanted scripts
    foreach ($wp_scripts->queue as $handle) {
      if (!in_array($handle, $allowed_scripts)) {
        wp_dequeue_script($handle);
      }
    }
  }
}, 100);



/**
 * 
 * Test
 * 
 */
