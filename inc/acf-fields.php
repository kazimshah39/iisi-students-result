<?php

/**
 * Create Subject fields
 */

add_action('acf/init', function () {
  // Get the subjects from the options page repeater
  $subjects = get_field('iisi_student_result_subjects_list', 'option');

  if (!$subjects) return;

  // Create a field group for the subject marks
  acf_add_local_field_group(array(
    'key' => 'group_student_marks',
    'title' => 'Subject Marks',
    'fields' => array(),
    'location' => array(
      array(
        array(
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'iisi_student_result',
        ),
      ),
    ),
  ));

  // Loop through each subject and create a text field
  foreach ($subjects as $index => $subject) {
    $subject_name = $subject['iisi_student_result_subject_name'];
    if (!$subject_name) continue;

    // Create a sanitized key from the subject name
    $field_key = 'subject_marks_' . sanitize_title($subject_name);

    // Add the text field
    acf_add_local_field(array(
      'key' => 'field_' . $field_key,
      'label' => $subject_name,
      'name' => $field_key,
      'type' => 'text',
      'parent' => 'group_student_marks',
      'wrapper' => array(
        'width' => '20',
      ),
    ));
  }
});
