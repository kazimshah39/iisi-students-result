<?php
get_header();

while (have_posts()) : the_post();

  require_once IISI_RESULT_PLUGIN_DIR . 'inc/frontend/result.php';

endwhile;

get_footer();
