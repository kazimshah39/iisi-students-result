<?php

// Add this near your other functions

function generate_sample_csv()
{
  // Verify user capabilities
  if (!current_user_can('manage_options')) {
    wp_die('Unauthorized access');
  }

  // Get required column names from the existing validation
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

  // Get subjects from options page
  $subjects = get_field('iisi_student_result_subjects_list', 'option');
  $subject_columns = array();
  if ($subjects) {
    foreach ($subjects as $subject) {
      $subject_columns[] = $subject['iisi_student_result_subject_name'];
    }
  }

  // Combine all columns
  $headers = array_merge($required_columns, $subject_columns);

  // Create sample data
  $sample_data = array(
    array_combine($headers, array_map(function ($header) use ($subject_columns) {
      // Generate appropriate sample data based on column type
      if ($header === 'student_name') return 'John Doe';
      if ($header === 'reg_no') return '2024-001';
      if ($header === 'roll_no') return '101';
      if ($header === 'father_name') return 'James Doe';
      if ($header === 'guardian_name') return 'James Doe';
      if ($header === 'class') return 'Class 10';
      if ($header === 'examination') return 'Final Term';
      if ($header === 'year') return '2024';
      if ($header === 'total_marks') return '1100';
      if ($header === 'obtained_marks') return '950';
      if ($header === 'percentage') return '86.36';
      if ($header === 'grade') return 'A';
      if ($header === 'darja') return 'Mumtaz';
      if ($header === 'position_in_class') return '1';
      if ($header === 'remarks') return 'Excellent Performance';
      if (in_array($header, $subject_columns)) return '85'; // Sample marks for subjects
      return '';
    }, $headers))
  );

  // Set headers for CSV download
  header('Content-Type: text/csv');
  header('Content-Disposition: attachment; filename="sample-results.csv"');
  header('Pragma: no-cache');
  header('Expires: 0');

  // Create CSV
  $output = fopen('php://output', 'w');
  fputcsv($output, $headers);
  foreach ($sample_data as $row) {
    fputcsv($output, $row);
  }
  fclose($output);
  exit;
}

// Add this action to handle the download
add_action('admin_init', function () {
  if (isset($_GET['action']) && $_GET['action'] === 'download_sample_csv') {
    generate_sample_csv();
  }
});
