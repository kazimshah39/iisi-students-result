<?php
function iisi_student_result_form_shortcode()
{
  // Enqueue necessary styles
  wp_enqueue_style('iisi-result-style', IISI_RESULT_PLUGIN_URL . 'css/style.css');
  wp_enqueue_style('iisi-result-print-style', IISI_RESULT_PLUGIN_URL . 'css/result-print.css');
  wp_enqueue_script('iisi-result-scripts', IISI_RESULT_PLUGIN_URL . 'js/scripts.js', array('jquery'), '1.0', true);

  // Get taxonomies terms
  $examinations = get_terms(['taxonomy' => 'examination', 'hide_empty' => false]);
  $years = get_terms(['taxonomy' => 'years', 'hide_empty' => false]);
  $classes = get_terms(['taxonomy' => 'classes', 'hide_empty' => false]);

  ob_start();

  // Process form submission and show results if form was submitted
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Validate required fields
    if (empty($_POST['roll_no']) && empty($_POST['reg_no'])) {
      echo '<div class="error-message">Please enter either Roll Number or Registration Number</div>';
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
?>
        <div id="search-controls" class="search-controls">
          <button onclick="toggleForm()" class="action-btn">
            <span class="icon">🔍</span> New Search
          </button>
          <button onclick="window.print();" class="action-btn">
            <span class="icon">🖨️</span> Print Result
          </button>
        </div>
        <div id="result-display" class="result-card">
          <div class="header-section">
            <!-- Institute Logo and Name -->
            <div class="institute-info">
              <img src="<?php echo get_field('iisi_student_result_logo', 'option'); ?>" alt="Institute Logo" class="institute-logo">
              <h1 class="institute-name"><?php echo get_field('iisi_student_result_institute_name', 'option'); ?></h1>
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
              <?php
              $watermark = get_field('iisi_student_result_watermark_image', 'option');
              if ($watermark) {
                echo '<img src="' . esc_url($watermark) . '" alt="Watermark" class="result-watermark">';
              }
              ?>
              <h3>Subject-wise Mark Sheet</h3>
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
              <!-- Dummy QR Code for now -->
              <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAUAAAAFCAYAAACNbyblAAAAHElEQVQI12P4//8/w38GIAXDIBKE0DHxgljNBAAO9TXL0Y4OHwAAAABJRU5ErkJggg==" alt="Result QR Code" class="qr-code">
              <p class="qr-text">Scan to verify result</p>
            </div>
          </div>
        </div>
  <?php
      else:
        echo '<div class="error-message">No result found for the provided information.</div>';
      endif;
      wp_reset_postdata();
    }
  }
  ?>

  <div class="iisi-result-container">
    <form id="iisi-result-form" class="iisi-form" method="POST">

      <div class="form-group">
        <label for="examination">Examination*</label>
        <select name="examination" id="examination" required>
          <option value="">Select Examination</option>
          <?php foreach ($examinations as $exam): ?>
            <option value="<?php echo esc_attr($exam->name); ?>"
              <?php selected(isset($_POST['examination']) ? $_POST['examination'] : '', $exam->name); ?>>
              <?php echo esc_html($exam->name); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="year">Year*</label>
        <select name="academic_year" id="year" required>
          <option value="">Select Year</option>
          <?php foreach ($years as $year): ?>
            <option value="<?php echo esc_attr($year->name); ?>"
              <?php selected(isset($_POST['academic_year']) ? $_POST['academic_year'] : '', $year->name); ?>>
              <?php echo esc_html($year->name); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label for="class">Class*</label>
        <select name="class" id="class" required>
          <option value="">Select Class</option>
          <?php foreach ($classes as $class): ?>
            <option value="<?php echo esc_attr($class->name); ?>"
              <?php selected(isset($_POST['class']) ? $_POST['class'] : '', $class->name); ?>>
              <?php echo esc_html($class->name); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group-inline">
        <div class="form-group">
          <label for="roll_no">Roll Number</label>
          <input type="text" name="roll_no" id="roll_no" class="required-one"
            value="<?php echo esc_attr(isset($_POST['roll_no']) ? $_POST['roll_no'] : ''); ?>">
        </div>
        <div class="form-seperator">OR</div>
        <div class="form-group">
          <label for="reg_no">Registration Number</label>
          <input type="text" name="reg_no" id="reg_no" class="required-one"
            value="<?php echo esc_attr(isset($_POST['reg_no']) ? $_POST['reg_no'] : ''); ?>">
        </div>
      </div>

      <div class="form-group">
        <button type="submit" class="submit-btn">View Result</button>
      </div>
    </form>
  </div>
<?php
  return ob_get_clean();
}
add_shortcode('student_result_form', 'iisi_student_result_form_shortcode');
