<?php

require_once IISI_RESULT_PLUGIN_DIR . 'inc/acf/student-subject-fields.php';
require_once IISI_RESULT_PLUGIN_DIR . 'inc/acf/subject-repeater-validator.php';

add_action('acf/include_fields', function () {
  acf_add_local_field_group([
    'key' => 'group_67979eab73bd6',
    'title' => 'Examinations Fields',
    'fields' => [
      [
        'key' => 'field_67979eac73f57',
        'label' => 'Subjectwise Total Marks',
        'name' => 'iisi_examinations_subjectwise_total_marks',
        'type' => 'number',
        'required' => 1,
        'wrapper' => ['width' => '']
      ]
    ],
    'location' => [
      [
        [
          'param' => 'taxonomy',
          'operator' => '==',
          'value' => 'examination',
        ]
      ]
    ],
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'active' => true
  ]);

  acf_add_local_field_group([
    'key' => 'group_679702ab5c66b',
    'title' => 'Student Fields',
    'fields' => [
      [
        'key' => 'field_6797032cb5038',
        'label' => 'Roll No',
        'name' => 'iisi_student_roll_no',
        'type' => 'number',
        'required' => 1,
        'wrapper' => ['width' => '50']
      ],
      [
        'key' => 'field_6797033db5039',
        'label' => 'Reg No',
        'name' => 'iisi_student_reg_no',
        'type' => 'text',
        'wrapper' => ['width' => '50']
      ],
      [
        'key' => 'field_679702adb5035',
        'label' => 'Father Name',
        'name' => 'iisi_student_father_name',
        'type' => 'text',
        'wrapper' => ['width' => '50']
      ],
      [
        'key' => 'field_679702f8b5036',
        'label' => 'Guardian Name',
        'name' => 'iisi_student_guardian_name',
        'type' => 'text',
        'wrapper' => ['width' => '50']
      ],
      [
        'key' => 'field_67970311b5037',
        'label' => 'Total Marks',
        'name' => 'iisi_student_total_marks',
        'type' => 'number',
        'wrapper' => ['width' => '33']
      ],
      [
        'key' => 'field_67970464a0323',
        'label' => 'Obtained Marks',
        'name' => 'iisi_student_obtained_marks',
        'type' => 'number',
        'wrapper' => ['width' => '33']
      ],
      [
        'key' => 'field_6797036bf0b11',
        'label' => 'Percentage',
        'name' => 'iisi_student_percentage',
        'type' => 'number',
        'wrapper' => ['width' => '33']
      ],
      [
        'key' => 'field_679703a3f6a15',
        'label' => 'Grade',
        'name' => 'iisi_student_grade',
        'type' => 'text',
        'wrapper' => ['width' => '33']
      ],
      [
        'key' => 'field_67976152a8461',
        'label' => 'Darja',
        'name' => 'iisi_student_darja',
        'type' => 'text',
        'wrapper' => ['width' => '33']
      ],
      [
        'key' => 'field_679703b3f6a16',
        'label' => 'Position in Class',
        'name' => 'iisi_student_position_in_class',
        'type' => 'text',
        'wrapper' => ['width' => '33']
      ],
      [
        'key' => 'field_679703c4f6a17',
        'label' => 'Remarks',
        'name' => 'iisi_student_remarks',
        'type' => 'textarea',
        'rows' => 5
      ]
    ],
    'location' => [
      [
        [
          'param' => 'post_type',
          'operator' => '==',
          'value' => 'iisi_student_result',
        ]
      ]
    ],
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'active' => true
  ]);

  acf_add_local_field_group([
    'key' => 'group_67970e96b9251',
    'title' => 'Students Result Settings',
    'fields' => [
      [
        'key' => 'field_67970e98d3983',
        'label' => 'Subjects',
        'type' => 'tab',
        'placement' => 'top'
      ],
      [
        'key' => 'field_67970ecf5cd5c',
        'label' => 'Subjects List',
        'name' => 'iisi_student_result_subjects_list',
        'type' => 'repeater',
        'layout' => 'table',
        'button_label' => 'Add New Subject',
        'sub_fields' => [
          [
            'key' => 'field_67970f765cd5d',
            'label' => 'Subject Name',
            'name' => 'iisi_student_result_subject_name',
            'type' => 'text'
          ]
        ]
      ],
      [
        'key' => 'field_6799b06c64883',
        'label' => 'Result Page',
        'type' => 'tab',
        'placement' => 'top'
      ],
      [
        'key' => 'field_6799b09764885',
        'label' => 'Institute Name',
        'name' => 'iisi_student_result_institute_name',
        'type' => 'text'
      ],
      [
        'key' => 'field_6799b07d64884',
        'label' => 'Logo',
        'name' => 'iisi_student_result_logo',
        'type' => 'image',
        'return_format' => 'url',
        'preview_size' => 'thumbnail'
      ],
      [
        'key' => 'field_679a441964e2a',
        'label' => 'Exam Controller Signature',
        'name' => 'iisi_student_result_exam_controller_signature',
        'type' => 'image',
        'instructions' => 'Upload a PNG image of the Exam Controller\'s signature with a size of 300x80 pixels.',
        'return_format' => 'url',
        'preview_size' => 'thumbnail'
      ],
      [
        'key' => 'field_679a444f64e2b',
        'label' => 'Exam Controller Name',
        'name' => 'iisi_student_result_exam_controller_name',
        'type' => 'text'
      ],
      [
        'key' => 'field_679a447b64e2d',
        'label' => 'Watermark Image',
        'name' => 'iisi_student_result_watermark_image',
        'type' => 'image',
        'instructions' => 'Upload a PNG image for the watermark with a size of 500x500 pixels.',
        'return_format' => 'url',
        'preview_size' => 'thumbnail'
      ]
    ],
    'location' => [
      [
        [
          'param' => 'options_page',
          'operator' => '==',
          'value' => 'iisi_students_result_settings',
        ]
      ]
    ],
    'position' => 'normal',
    'style' => 'default',
    'label_placement' => 'top',
    'active' => true
  ]);
});

add_action('init', function () {
  register_taxonomy('classes', ['iisi_student_result'], [
    'labels' => [
      'name' => 'Classes',
      'singular_name' => 'Class',
      'menu_name' => 'Classes',
      'add_new_item' => 'Add New Class',
      'edit_item' => 'Edit Class'
    ],
    'public' => true,
    'show_in_rest' => true
  ]);

  register_taxonomy('examination', ['iisi_student_result'], [
    'labels' => [
      'name' => 'Examinations',
      'singular_name' => 'Examination',
      'menu_name' => 'Examinations',
      'add_new_item' => 'Add New Examination',
      'edit_item' => 'Edit Examination'
    ],
    'public' => true,
    'show_in_rest' => true
  ]);

  register_taxonomy('years', ['iisi_student_result'], [
    'labels' => [
      'name' => 'Years',
      'singular_name' => 'Year',
      'menu_name' => 'Years',
      'add_new_item' => 'Add New Year',
      'edit_item' => 'Edit Year'
    ],
    'public' => true,
    'show_in_rest' => true
  ]);

  register_post_type('iisi_student_result', [
    'labels' => [
      'name' => 'Students Result',
      'singular_name' => 'Student Result',
      'menu_name' => 'Students Result',
      'add_new' => 'Add New Student Result',
      'edit_item' => 'Edit Student Result'
    ],
    'public' => true,
    'show_in_rest' => true,
    'menu_position' => 4,
    'menu_icon' => 'dashicons-database',
    'supports' => ['title', 'custom-fields'],
    'rewrite' => ['slug' => 'student-result']
  ]);
});

add_filter('enter_title_here', function ($default, $post) {
  if ($post->post_type === 'iisi_student_result') {
    return 'Add Student Name';
  }
  return $default;
}, 10, 2);

add_action('acf/init', function () {
  acf_add_options_page([
    'page_title' => 'Students Result Settings',
    'menu_slug' => 'iisi_students_result_settings',
    'parent_slug' => 'edit.php?post_type=iisi_student_result',
    'menu_title' => 'Settings'
  ]);
});
