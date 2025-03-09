<div id="search-controls" class="search-controls">
  <button onclick="toggleForm()" class="action-btn new-search-btn">
    <span class="icon">🔍</span> New Search
  </button>
  <button onclick="window.print();" class="action-btn print-btn">
    <span class="icon">🖨️</span> Print Result
  </button>
</div>
<div id="result-display" class="result-card">

  <div class="header-section">
    <img src="<?php echo IISI_RESULT_PLUGIN_URL . 'assets/img/result-header.png' ?>" alt="Institute Logo" class="institute-logo">
    <h3 class="heading-1">Detailed Marks Sheet</h3>
    <h3 class="heading-2 aadil-font">کشف الدرجات</h3>
  </div>
  <div class="result-header">
    <div class="student-info">
      <?php
      // Define an array with labels and corresponding field keys
      $student_info = [
        'رجسٹریشن نمبر' => get_field('iisi_student_reg_no'),
        'رول نمبر' => get_field('iisi_student_roll_no'),
        'نام طالب علم' => get_the_title(),
        "ولدیت" => get_field('iisi_student_father_name'),
        'جماعت' => get_the_terms(get_the_ID(), 'classes')[0]->name,
        'امتحان' => get_the_terms(get_the_ID(), 'examination')[0]->name . " (" . get_the_terms(get_the_ID(), 'years')[0]->name . ")",
      ];

      // Loop through the array and generate the info rows dynamically
      foreach ($student_info as $label => $value) {
        // Check if the label corresponds to Registration Number or Roll Number
        $extra_class = !in_array($label, ['رجسٹریشن نمبر', 'رول نمبر', 'امتحان']) ? 'urdu-font' : '';
      ?>
        <div class="info-row">
          <span class="label urdu-font"><?php echo esc_html($label); ?>:</span>
          <span class="value <?php echo esc_attr($extra_class); ?>"><?php echo esc_html($value); ?></span>
        </div>
      <?php
      }
      ?>

    </div>

  </div>

  <div class="result-details">


    <div class="subject-marks">
      <div class="result-table-container">
        <?php
        $watermark = get_field('iisi_student_result_watermark_image', 'option');
        if ($watermark) {
          echo '<img src="' . esc_url($watermark) . '" alt="Watermark" class="result-watermark">';
        }
        ?>
        <table>
          <thead>
            <tr class="aadil-font aadil-font-inner">
              <th>مضمون/کتاب</th>
              <th>کل نمبرات</th>
              <th>حاصل کردہ نمبرات</th>
              <th>کیفیت</th>
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
                  // Calculate status based on new rules
                  $status = '';
                  $status_class = '';

                  if ($obtained_marks === 'L') {
                    $status = 'رخصت';
                    $status_class = 'leave-status';
                  } elseif ($obtained_marks === 'P') {
                    $status = 'ملتوی';
                    $status_class = 'pending-status';
                  } elseif ($obtained_marks === 'C') {
                    $status = 'منسوخ';
                    $status_class = 'cancelled-status';
                  } elseif ($obtained_marks === 'A') {
                    $status = 'فیل بوجہ غیرحاضری';
                    $status_class = 'absent-status';
                  } else {
                    // Numeric marks comparison
                    if (strtolower($subject_name) === 'مراجعہ حفظ') {
                      $passing_percentage = 50;
                    } else {
                      $passing_percentage = 40;
                    }

                    $percentage = ((float) $obtained_marks / (float) $total_marks) * 100;
                    if ($percentage < $passing_percentage) {
                      $status = 'فیل';
                      $status_class = 'fail-status';
                    } else {
                      $status = 'پاس';
                      $status_class = '';
                    }
                  }

                  echo '<tr>';
                  echo '<td class="urdu-font">' . esc_html($subject_name) . '</td>';
                  echo '<td >' . esc_html($total_marks) . '</td>';
                  echo '<td >' . esc_html($obtained_marks) . '</td>';
                  echo '<td class="' . esc_attr($status_class . ' urdu-font') . '">' . esc_html($status) . '</td>';
                  echo '</tr>';
                }
              }
            }
            ?>
            <tr>
              <td class="remarks" colspan="4">
                <span class="label green-text aadil-font">کیفیت:</span>
                <span class="value urdu-font"><?php echo esc_html(get_field('iisi_student_remarks')); ?></span>
              </td>
            </tr>

          </tbody>
        </table>
      </div>
    </div>



    <!--  -->
    <?php
    $total_marks = esc_html(get_field('iisi_student_total_marks'));
    $obtained_marks = esc_html(get_field('iisi_student_obtained_marks'));
    $percentage = esc_html(get_field('iisi_student_percentage'));
    $grade = esc_html(get_field('iisi_student_grade'));
    $darja = esc_html(get_field('iisi_student_darja'));
    $position_in_class = esc_html(get_field('iisi_student_position_in_class'));
    ?>

    <div class="marks-summary">
      <?php
      // Calculate overall pass/fail status
      $pass_status = ($obtained_marks / $total_marks * 100) >= 40 ? 'پاس' : 'فیل';

      $summary_items = [
        'کل نمبرات' => $total_marks,
        'حاصل کردہ نمبرات' => $obtained_marks,
        'فیصد' => $percentage . '%',
        'تقدیر' => $darja . ' - ' . $grade,
        'کلاس میں پوزیشن' => $position_in_class
      ];

      // Loop through items to generate HTML dynamically
      foreach ($summary_items as $label => $value) :
        // Add a special class to all except "تقدیر"
        $extra_class = ($label == 'تقدیر') ? '' : '';
      ?>
        <div class="summary-item">
          <span class="label urdu-font"><?php echo esc_html($label); ?>:</span>
          <span class="value <?php echo esc_attr($extra_class); ?>">
            <?php echo $value; ?>
          </span>
        </div>
      <?php endforeach; ?>

    </div>

  </div>
  <div class="signature-and-qr-section">
    <div class="qr-section">
      <?php
      $post_url = get_permalink();
      $qr_url = 'https://api.qrserver.com/v1/create-qr-code/?size=110x110&data=' . urlencode($post_url);
      ?>
      <img src="<?php echo esc_url($qr_url); ?>" alt="Result QR Code" class="qr-code">
      <p class="qr-text">(Scan to verify)</p>
    </div>
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
        <p class="urdu-font"><?php echo esc_html($controller_name ? $controller_name : 'ناظم امتحانات'); ?></p>
      </div>

    </div>
  </div>
  <p class="print-date urdu-font">
    <span>پرنٹ کی تاریخ:</span>
    <?php
    $locale = 'ur_PK';
    $dateFormatter = new IntlDateFormatter(
      $locale,
      IntlDateFormatter::FULL,
      IntlDateFormatter::NONE,
      'Asia/Karachi',
      IntlDateFormatter::GREGORIAN,
      'd MMMM y'
    );
    echo $dateFormatter->format(time());
    ?>
  </p>
  <p class="last-text green-text">
    Department Of Examinations, Institute of Islamic Science, Islamabad
    <br>
    Website: www.exams.iisi.edu.pk. Email: exams@iisi.edu.pk
  </p>
</div>