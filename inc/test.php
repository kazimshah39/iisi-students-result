<?php

function enqueue_custom_jquery()
{
  // Enqueue the jQuery library included with WordPress
  wp_enqueue_script('jquery');
}
add_action('wp_enqueue_scripts', 'enqueue_custom_jquery');
