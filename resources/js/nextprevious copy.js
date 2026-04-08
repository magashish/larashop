var currentStep = 1;

// Email validation function
function validateEmail(email) {
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(email);
}

// Password match validation
function validatePasswordMatch() {
  const password = $('#password').val();
  const confirmPassword = $('#password-confirm').val();
  return password === confirmPassword;
}

// Async function to check email existence
async function checkEmailExists(email) {
  try {
    const response = await $.ajax({
      url: '/check-email',
      method: 'POST',
      data: { 
        email: email,
        _token: $('meta[name="csrf-token"]').attr('content')
      },
      dataType: 'json'
    });
    return response;
  } catch (error) {
    console.error("Error checking email:", error);
    return { exists: false, message: 'Error checking email' };
  }
}

// Modified validateCurrentStep function
async function validateCurrentStep() {
  let isValid = true;
  const $currentStep = $(".step-" + currentStep);
  
  // Clear previous errors
  $currentStep.find(".is-invalid").removeClass("is-invalid");
  $currentStep.find(".invalid-feedback").text("");
  
  // Validate all required fields
  $currentStep.find("input[required]").each(function() {
    const $input = $(this);
    const value = $input.val().trim();
    
    if (!value) {
      isValid = false;
      $input.addClass("is-invalid");
      $input.next(".invalid-feedback").text("This field is required");
    }
  });

  // Special validation for step 2
  if (currentStep === 2) {
    const $emailInput = $('input[name="email"]');
    const email = $emailInput.val().trim();
    
    // Email format validation
    if (!validateEmail(email)) {
      isValid = false;
      $emailInput.addClass("is-invalid");
      $emailInput.next(".invalid-feedback").text("Please enter a valid email address");
      return isValid;
    }
    
    // Email existence check
    $emailInput.prop('disabled', true);
    const $spinner = $('<div class="email-check-spinner spinner-border spinner-border-sm text-primary ms-2"></div>');
    $emailInput.after($spinner);
    
    const emailCheck = await checkEmailExists(email);
    
    $spinner.remove();
    $('.email-check-spinner').remove();
console.log('Spinner removed'); // Check if this logs
$emailInput.prop('disabled', false);

    
    if (emailCheck.exists) {
      isValid = false;
      $emailInput.addClass("is-invalid");
      $emailInput.next(".invalid-feedback").text(emailCheck.message);
    }
    
    // Password match validation
    if (!validatePasswordMatch()) {
      isValid = false;
      $('#password-confirm').addClass("is-invalid");
      $('#password-confirm').next(".invalid-feedback").text("Passwords do not match");
    }
  }

  return isValid;
}

// Update next-step click handler to be async
$(".next-step").click(async function() {
  if (!await validateCurrentStep()) return;
  
  if (currentStep < 3) {
    $(".step-" + currentStep).addClass("animate__animated animate__fadeOutLeft");
    currentStep++;
    setTimeout(function() {
      $(".step").removeClass("animate__animated animate__fadeOutLeft").hide();
      $(".step-" + currentStep)
        .show()
        .addClass("animate__animated animate__fadeInRight");
      updateProgressBar();
      updateStepCircles();
    }, 500);
  }
});

// Rest of your existing functions
function displayStep(stepNumber) {
  if (stepNumber >= 1 && stepNumber <= 3) {
    $(".step").hide();
    $(".step-" + stepNumber).show();
    currentStep = stepNumber;
    updateProgressBar();
    updateStepCircles();
  }
}

function updateProgressBar() {
  var progressPercentage = ((currentStep - 1) / 2) * 100;
  $(".progress-bar").css("width", progressPercentage + "%");
}

function updateStepCircles() {
  $(".step-circle").each(function (index) {
    const stepIndex = index + 1;
    $(this).removeClass("active completed");
    if (stepIndex < currentStep) $(this).addClass("completed");
    if (stepIndex === currentStep) $(this).addClass("active");
  });
}

$(document).ready(function () {
  $("#multi-step-form").find(".step").slice(1).hide();
  updateStepCircles();
  
  // Previous step handler
  $(".prev-step").click(function () {
    if (currentStep > 1) {
      $(".step-" + currentStep).addClass("animate__animated animate__fadeOutRight");
      currentStep--;
      setTimeout(function () {
        $(".step").removeClass("animate__animated animate__fadeOutRight").hide();
        $(".step-" + currentStep).show().addClass("animate__animated animate__fadeInLeft");
        updateProgressBar();
        updateStepCircles();
      }, 500);
    }
  });
});