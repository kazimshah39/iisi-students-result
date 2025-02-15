<?php

// Get taxonomies terms
$examinations = get_terms(['taxonomy' => 'examination', 'hide_empty' => false]);
$years = get_terms(['taxonomy' => 'years', 'hide_empty' => false]);
$classes = get_terms(['taxonomy' => 'classes', 'hide_empty' => false]);
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
      <select name="academic_year" id="year" class="non-urdu non-urdu-inner" required>
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
        <input type="text" name="roll_no" id="roll_no" class="required-one non-urdu"
          value="<?php echo esc_attr(isset($_POST['roll_no']) ? $_POST['roll_no'] : ''); ?>">
      </div>
      <div class="form-seperator">OR</div>
      <div class="form-group">
        <label for="reg_no">Registration Number</label>
        <input type="text" name="reg_no" id="reg_no" class="required-one non-urdu"
          value="<?php echo esc_attr(isset($_POST['reg_no']) ? $_POST['reg_no'] : ''); ?>">
      </div>
    </div>

    <div class="form-group">
      <button type="submit" class="submit-btn non-urdu">View Result</button>
    </div>
  </form>
</div>