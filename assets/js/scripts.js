function toggleForm() {
  const form = document.querySelector(".iisi-form");
  const resultDisplay = document.getElementById("result-display");
  const searchControls = document.getElementById("search-controls");

  if (form.classList.contains("hidden")) {
    form.classList.remove("hidden");
    resultDisplay.style.display = "none";
    searchControls.style.display = "none"; // Hide search controls
  } else {
    form.classList.add("hidden");
    resultDisplay.style.display = "block";
    searchControls.style.display = "block"; // Show search controls
  }
}

// Add this to your existing JavaScript or include in the same file
document.addEventListener("DOMContentLoaded", function () {
  // Hide the form when results are shown
  const resultDisplay = document.getElementById("result-display");
  if (resultDisplay) {
    document.querySelector(".iisi-form").classList.add("hidden");
  }
});

/**
 *
 */

document.addEventListener("DOMContentLoaded", function () {
  const form = document.getElementById("iisi-result-form");
  const rollNoInput = document.getElementById("roll_no");
  const regNoInput = document.getElementById("reg_no");

  form.addEventListener("submit", function (e) {
    // Remove any existing error messages
    const existingError = form.querySelector(".error-message");
    if (existingError) {
      existingError.remove();
    }

    // Check if both fields are empty
    if (!rollNoInput.value.trim() && !regNoInput.value.trim()) {
      e.preventDefault(); // Prevent form submission

      // Create and show error message
      const errorDiv = document.createElement("div");
      errorDiv.className = "error-message";
      errorDiv.textContent =
        "Please enter either Roll Number or Registration Number";

      // Insert error message before the submit button
      const submitBtn = form.querySelector(".submit-btn").parentNode;
      form.insertBefore(errorDiv, submitBtn);

      // Add red border to both inputs to highlight the error
      rollNoInput.classList.add("error-input");
      regNoInput.classList.add("error-input");
    }
  });

  // Remove error styling when user starts typing in either field
  [rollNoInput, regNoInput].forEach((input) => {
    input.addEventListener("input", function () {
      if (this.value.trim()) {
        rollNoInput.classList.remove("error-input");
        regNoInput.classList.remove("error-input");
        const errorMessage = form.querySelector(".error-message");
        if (errorMessage) {
          errorMessage.remove();
        }
      }
    });
  });
});
