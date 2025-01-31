<?php

// Add submenu page under your custom post type
add_action('admin_menu', function () {
  add_submenu_page(
    'edit.php?post_type=iisi_student_result',
    'Import Student Results',
    'Import Results',
    'manage_options',
    'result-import',
    'render_result_import_page'
  );
});


// Render import page
function render_result_import_page()
{
?>
  <div class="wrap">
    <h1>Import Student Results</h1>

    <?php
    // Handle form submission
    if (isset($_POST['submit_csv']) && isset($_FILES['result_csv'])) {
      handle_csv_upload();
    }
    ?>

    <div class="card" style="max-width: 600px; padding: 20px; margin-top: 20px;">
      <form method="post" enctype="multipart/form-data">
        <p>
          <label for="result_csv">Upload CSV File:</label><br>
          <input type="file" name="result_csv" id="result_csv" accept=".csv" required>
        </p>
        <?php wp_nonce_field('import_results_nonce', 'result_import_nonce'); ?>
        <p>
          <a href="<?php echo esc_url(add_query_arg(['action' => 'download_sample_csv'], admin_url('admin.php'))); ?>" class="button button-secondary">
            Download Sample CSV Format
          </a>
        </p>
        <input type="submit" name="submit_csv" class="button button-primary" value="Import Results">
      </form>
    </div>
  </div>
<?php
}

// Handle the CSV upload and processing
function handle_csv_upload()
{
  // Verify nonce
  if (!wp_verify_nonce($_POST['result_import_nonce'], 'import_results_nonce')) {
    wp_die('Security check failed');
  }

  $file = $_FILES['result_csv'];

  // Basic file validation
  if ($file['error'] !== UPLOAD_ERR_OK) {
    echo '<div class="error"><p>Error uploading file. Please try again.</p></div>';
    return;
  }

  if ($file['type'] !== 'text/csv') {
    echo '<div class="error"><p>Please upload a valid CSV file.</p></div>';
    return;
  }

  // Process the CSV
  $results = process_results_csv($file['tmp_name']);

  // Display import results
  if (!empty($results['errors'])) {
    echo '<div class="error"><p><strong>Import Errors:</strong></p>';
    echo '<ul>';
    foreach ($results['errors'] as $error) {
      echo '<li>' . esc_html($error) . '</li>';
    }
    echo '</ul></div>';
  }

  if (!empty($results['duplicates'])) {
    echo '<div class="warning"><p><strong>Duplicate Entries Found:</strong></p>';
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr><th>Student Name</th><th>Registration No</th><th>Class</th><th>Examination</th><th>Existing Entry ID</th></tr></thead><tbody>';
    foreach ($results['duplicates'] as $duplicate) {
      echo '<tr>';
      echo '<td>' . esc_html($duplicate['name']) . '</td>';
      echo '<td>' . esc_html($duplicate['reg_no']) . '</td>';
      echo '<td>' . esc_html($duplicate['class']) . '</td>';
      echo '<td>' . esc_html($duplicate['exam']) . '</td>';
      echo '<td><a href="' . esc_url(get_edit_post_link($duplicate['post_id'])) . '" target="_blank">' .
        esc_html($duplicate['post_id']) . ' (Click to View)</a></td>';
      echo '</tr>';
    }
    echo '</tbody></table></div>';
  }

  if ($results['imported'] > 0) {
    echo '<div class="updated"><p>Successfully imported ' . intval($results['imported']) . ' results.</p></div>';
  }
}

function process_results_csv($file)
{
  $results = array(
    'imported' => 0,
    'errors' => array(),
    'duplicates' => array()
  );

  $handle = fopen($file, 'r');
  if (!$handle) {
    $results['errors'][] = 'Could not open file';
    return $results;
  }

  // Get headers and validate required columns
  $headers = fgetcsv($handle);


  $headers = array_map(function ($header) {
    // Remove zero-width characters, BOM marks, and other invisible characters
    $header = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}\x{0000}-\x{001F}]/u', '', $header);
    $header = trim($header);
    return $header;
  }, $headers);


  $required_columns = array(
    'student_name',
    'reg_no',
    'roll_no',
    'father_name',
    'guardian_name',
    'class',
    'examination',
    'year',
    'total_marks',
    'obtained_marks',
    'percentage',
    'grade',
    'darja',
    'position_in_class',
    'remarks'
  );


  // Validate headers
  foreach ($required_columns as $column) {
    if (!in_array($column, $headers)) {
      $results['errors'][] = "Missing required column: $column";
      fclose($handle);
      return $results;
    }
  }

  // Get column indexes
  $column_indexes = array_flip($headers);

  // Process each row
  $row_number = 2; // Start from 2 to account for headers
  while (($row = fgetcsv($handle)) !== false) {
    // Basic data validation
    if (count($row) !== count($headers)) {
      $results['errors'][] = "Row $row_number: Invalid number of columns";
      $row_number++;
      continue;
    }

    // Get key data for duplicate checking
    $reg_no = $row[$column_indexes['reg_no']];
    $class = $row[$column_indexes['class']];
    $examination = $row[$column_indexes['examination']];
    $student_name = $row[$column_indexes['student_name']];

    // Check for duplicates
    $duplicate = check_duplicate_result($reg_no, $class, $examination);
    if ($duplicate) {
      $results['duplicates'][] = array(
        'name' => $student_name,
        'reg_no' => $reg_no,
        'class' => $class,
        'exam' => $examination,
        'post_id' => $duplicate['id']
      );
      $row_number++;
      continue;
    }

    // Create post
    $post_data = array(
      'post_title' => $student_name,
      'post_type' => 'iisi_student_result',
      'post_status' => 'publish'
    );

    $post_id = wp_insert_post($post_data);
    if (is_wp_error($post_id)) {
      $results['errors'][] = "Row $row_number: Failed to create entry";
      $row_number++;
      continue;
    }

    // Update ACF fields
    $acf_fields = array(
      'iisi_student_reg_no' => 'reg_no',
      'iisi_student_roll_no' => 'roll_no',
      'iisi_student_father_name' => 'father_name',
      'iisi_student_guardian_name' => 'guardian_name',
      'iisi_student_total_marks' => 'total_marks',
      'iisi_student_obtained_marks' => 'obtained_marks',
      'iisi_student_percentage' => 'percentage',
      'iisi_student_grade' => 'grade',
      'iisi_student_darja' => 'darja',
      'iisi_student_position_in_class' => 'position_in_class',
      'iisi_student_remarks' => 'remarks'
    );

    foreach ($acf_fields as $acf_key => $csv_column) {
      update_field($acf_key, $row[$column_indexes[$csv_column]], $post_id);
    }

    // Handle subject marks
    $subjects = get_field('iisi_student_result_subjects_list', 'option');
    if ($subjects) {
      foreach ($subjects as $subject) {
        $subject_name = $subject['iisi_student_result_subject_name'];
        $field_key = 'subject_marks_' . sanitize_title($subject_name);
        if (isset($column_indexes[$subject_name])) {
          update_field($field_key, $row[$column_indexes[$subject_name]], $post_id);
        }
      }
    }

    // Set taxonomies
    wp_set_object_terms($post_id, $class, 'classes');
    wp_set_object_terms($post_id, $examination, 'examination');
    wp_set_object_terms($post_id, $row[$column_indexes['year']], 'years');

    $results['imported']++;
    $row_number++;
  }

  fclose($handle);
  return $results;
}

// Helper function to check for duplicates
function check_duplicate_result($reg_no, $class, $examination)
{
  $args = array(
    'post_type' => 'iisi_student_result',
    'meta_query' => array(
      array(
        'key' => 'iisi_student_reg_no',
        'value' => $reg_no
      )
    ),
    'tax_query' => array(
      'relation' => 'AND',
      array(
        'taxonomy' => 'classes',
        'field' => 'name',
        'terms' => $class
      ),
      array(
        'taxonomy' => 'examination',
        'field' => 'name',
        'terms' => $examination
      )
    ),
    'posts_per_page' => 1
  );

  $query = new WP_Query($args);
  return $query->have_posts() ? array(
    'id' => $query->posts[0]->ID,
    'url' => get_edit_post_link($query->posts[0]->ID)
  ) : false;
}
