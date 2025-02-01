<div id="result-display" class="result-card">
  <div class="header-section">
    <!-- Institute Logo and Name -->
    <div class="institute-info">
      <img src="<?php echo get_field('iisi_student_result_logo', 'option'); ?>" alt="Institute Logo" class="institute-logo">
      <h1 class="institute-name"><?php echo get_field('iisi_student_result_institute_name', 'option'); ?></h1>
    </div>

    <div id="search-controls" class="search-controls">
      <button onclick="toggleForm()" class="action-btn new-search-btn">
        <span class="icon">🔍</span> New Search
      </button>
      <button onclick="window.print();" class="action-btn print-btn">
        <span class="icon">🖨️</span> Print Result
      </button>
    </div>
  </div>
  <div class="result-header">
    <div class="student-info">
      <?php
      // Define an array with labels and corresponding field keys
      $student_info = [
        'Student Name' => get_the_title(),
        "Father's Name" => get_field('iisi_student_father_name'),
        "Guardian's Name" => get_field('iisi_student_guardian_name'),
        'Roll No' => get_field('iisi_student_roll_no'),
        'Registration No' => get_field('iisi_student_reg_no'),
        'Position in Class' => get_field('iisi_student_position_in_class'),
        'Class' => get_the_terms(get_the_ID(), 'classes')[0]->name,
        'Examination' => get_the_terms(get_the_ID(), 'examination')[0]->name,
      ];

      // Loop through the array and generate the info rows dynamically
      foreach ($student_info as $label => $value) {
      ?>
        <div class="info-row">
          <span class="label"><?php echo esc_html($label); ?>:</span>
          <span class="value"><?php echo esc_html($value); ?></span>
        </div>
      <?php
      }
      ?>
    </div>

  </div>

  <div class="result-details">
    <?php
    $total_marks = esc_html(get_field('iisi_student_total_marks'));
    $obtained_marks = esc_html(get_field('iisi_student_obtained_marks'));
    $percentage = esc_html(get_field('iisi_student_percentage'));
    $grade = esc_html(get_field('iisi_student_grade'));
    $darja = esc_html(get_field('iisi_student_darja'));
    ?>

    <div class="marks-summary">
      <?php
      // Calculate overall pass/fail status
      $pass_status = ($obtained_marks / $total_marks * 100) >= 40 ? 'Pass' : 'Fail';

      // Define an associative array for summary items
      $summary_items = [
        'Total Marks' => $total_marks,
        'Obtained Marks' => $obtained_marks,
        'Percentage' => $percentage . '%',
        'Grade' => $grade . ' - ' . $darja,
        'Status' => $pass_status
      ];

      // Loop through items to generate HTML dynamically
      foreach ($summary_items as $label => $value) : ?>
        <div class="summary-item">
          <span class="label"><?php echo esc_html($label); ?>:</span>
          <span class="value<?php echo ($label === 'Grade') ? ' grade' : ''; ?><?php echo ($label === 'Status') ? ($value === 'Fail' ? ' summary-fail-status' : ' summary-pass-status') : ''; ?>">
            <?php echo $value; ?>
          </span>
        </div>
      <?php endforeach; ?>
    </div>


    <div class="subject-marks">
      <h3>Subject-wise Mark Sheet</h3>
      <div class="result-table-container">
        <?php
        $watermark = get_field('iisi_student_result_watermark_image', 'option');
        if ($watermark) {
          echo '<img src="' . esc_url($watermark) . '" alt="Watermark" class="result-watermark">';
        }
        ?>
        <table>
          <thead>
            <tr>
              <th>Subject</th>
              <th>Total Marks</th>
              <th>Obtained Marks</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php
            // Get the subjects from options
            $subjects = get_field('iisi_student_result_subjects_list', 'option');
            // Get examination term ID
            $examination_terms = get_the_terms(get_the_ID(), 'examination');
            $examination_id = $examination_terms[0]->term_id;



            if ($subjects) {
              foreach ($subjects as $subject) {
                $subject_name = $subject['iisi_student_result_subject_name'];
                $field_key = 'subject_marks_' . sanitize_title($subject_name);
                $obtained_marks = get_field($field_key);
                $total_marks = get_field('iisi_examinations_subjectwise_total_marks', 'examination_' . $examination_id);

                if ($obtained_marks) {
                  // Calculate pass/fail status
                  $status = '';

                  if (strtolower($subject_name) === 'مراجعہ حفظ') {
                    $passing_percentage = 50;
                  } else {
                    $passing_percentage = 40;
                  }

                  $percentage = ($obtained_marks / $total_marks) * 100;
                  if ($percentage < $passing_percentage) {
                    $status = 'Fail';
                  } else {
                    $status = 'Pass';
                  }

                  echo '<tr>';
                  echo '<td>' . esc_html($subject_name) . '</td>';
                  echo '<td>' . esc_html($total_marks) . '</td>';
                  echo '<td>' . esc_html($obtained_marks) . '</td>';
                  echo '<td' . ($status === 'Fail' ? ' class="fail-status"' : '') . '>' . esc_html($status) . '</td>';
                  echo '</tr>';
                }
              }
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

    <div class="additional-info">
      <div class="info-item">
        <span class="label">Remarks:</span>
        <span class="value"><?php echo esc_html(get_field('iisi_student_remarks')); ?></span>
      </div>
    </div>
  </div>
  <div class="result-footer">
    <div class="signature-section">
      <div class="signature-box">
        <?php
        $controller_signature = get_field('iisi_student_result_exam_controller_signature', 'option');
        $controller_name = get_field('iisi_student_result_exam_controller_name', 'option');
        if ($controller_signature) {
          echo '<img src="' . esc_url($controller_signature) . '" alt="Controller Signature" class="signature-image">';
        } else {
          echo '<div class="signature-line"></div>';
        }
        ?>
        <p><?php echo esc_html($controller_name ? $controller_name : 'Controller of Examination'); ?></p>
      </div>

    </div>

    <div class="qr-section">
      <?php
      $post_url = get_permalink();
      $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=110x110&data=' . urlencode($post_url);
      ?>
      <img src="<?php echo esc_url($qr_url); ?>" alt="Result QR Code" class="qr-code">
      <p class="qr-text">Scan to verify result</p>
    </div>
  </div>
</div>