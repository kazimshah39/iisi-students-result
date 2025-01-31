<?php

add_filter(
  'acf/validate_value/name=iisi_student_result_subject_name',
  function ($valid, $value, $field, $input) {
    if (!$valid) {
      return $valid;
    }

    // get list of array indexes from $input
    // [ <= this fixes my IDE, it has problems with unmatched brackets
    preg_match_all('/\[([^\]]+)\]/', $input, $matches);
    if (!count($matches[1])) {
      // this should actually never happen
      return $valid;
    }
    $matches = $matches[1];

    // walk the acf input to find the repeater and current row      
    $array = $_POST['acf'];

    $repeater_key = false;
    $repeater_value = false;
    $row_key = false;
    $row_value = false;
    $field_key = false;
    $field_value = false;

    for ($i = 0; $i < count($matches); $i++) {
      if (isset($array[$matches[$i]])) {
        $repeater_key = $row_key;
        $repeater_value = $row_value;
        $row_key = $field_key;
        $row_value = $field_value;
        $field_key = $matches[$i];
        $field_value = $array[$matches[$i]];
        if ($field_key == $field['key']) {
          break;
        }
        $array = $array[$matches[$i]];
      }
    }

    if (!$repeater_key) {
      // this should not happen, but better safe than sorry
      return $valid;
    }

    // look for duplicate values in the repeater
    foreach ($repeater_value as $index => $row) {
      if ($index != $row_key && $row[$field_key] == $value) {
        // Only show error if current field is the newer duplicate (higher index)
        if ($index < $row_key) {
          $valid = sprintf('The subject "%s" already exists in the list. Please enter a different subject name.', esc_html($value));
        }
        break;
      }
    }

    return $valid;
  },
  20,
  4
);
