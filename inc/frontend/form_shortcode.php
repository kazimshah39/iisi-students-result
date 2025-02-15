<?php

add_shortcode('student_result_form', function () {

  ob_start();

  // Process form submission and show results if form was submitted
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validate required fields
    if (empty($_POST['roll_no']) && empty($_POST['reg_no'])) {
      echo '<div class="error-message non-urdu">Please enter either Roll Number or Registration Number</div>';
    } else {
      // Query arguments
      $args = array(
        'post_type' => 'iisi_student_result',
        'posts_per_page' => 1,
        'tax_query' => array(
          array(
            'taxonomy' => 'examination',
            'field' => 'name',
            'terms' => sanitize_text_field($_POST['examination'])
          ),
          array(
            'taxonomy' => 'years',
            'field' => 'name',
            'terms' => sanitize_text_field($_POST['academic_year'])
          ),
          array(
            'taxonomy' => 'classes',
            'field' => 'name',
            'terms' => sanitize_text_field($_POST['class'])
          )
        ),
        'meta_query' => array(
          'relation' => 'OR',
          array(
            'key' => 'iisi_student_roll_no',
            'value' => sanitize_text_field($_POST['roll_no']),
            'compare' => '='
          ),
          array(
            'key' => 'iisi_student_reg_no',
            'value' => sanitize_text_field($_POST['reg_no']),
            'compare' => '='
          )
        )
      );

      $query = new WP_Query($args);

      if ($query->have_posts()):
        $query->the_post();

        require_once IISI_RESULT_PLUGIN_DIR . 'inc/frontend/result.php';

      else:
        echo '<div class="error-message non-urdu">No result found for the provided information.</div>';
      endif;
      wp_reset_postdata();
    }
  }

  require_once IISI_RESULT_PLUGIN_DIR . 'inc/frontend/form.php';

  return ob_get_clean();
});
