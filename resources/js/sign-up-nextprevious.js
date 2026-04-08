// ====================================================================
// A: GLOBAL HELPER FUNCTIONS (Used by all forms, defined once)
// ====================================================================

// Email format validation function
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Helper function to create the Bootstrap spinner element
function createSpinner() {
    const spinner = document.createElement('div');
    spinner.className = 'spinner-border spinner-border-sm text-primary ms-2';
    return spinner;
}

// Async function to check email existence via AJAX
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

// Async function for server-side password strength validation via AJAX
async function validatePassword(password) {
    try {
        const response = await $.ajax({
            url: '/validate-password',
            method: 'POST',
            data: {
                password: password,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json'
        });
        return response;
    } catch (error) {
        console.error("Password validation error:", error);
        return {
            valid: false,
            message: error.responseJSON?.message || 'Error validating password'
        };
    }
}

// Async function to check mobile existence via AJAX
async function checkMobileExists(mobile) {
    try {
        const response = await $.ajax({
            url: '/check-mobile',
            method: 'POST',
            data: {
                mobile: mobile,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: 'json'
        });
        return response;
    } catch (error) {
        console.error("Mobile validation error:", error);
        return {
            exists: false,
            message: error.responseJSON?.message || 'Error validating mobile number'
        };
    }
}


// Save unregistered user (Step-1 only)
async function saveUnregisteredUser(data) {
    try {
        await $.ajax({
            url: '/unregistered-users/store',
            method: 'POST',
            data: {
                ...data,
                _token: $('meta[name="csrf-token"]').attr('content')
            }
        });
    } catch (error) {
        console.error('Unregistered user save failed', error);
    }
}



// ====================================================================
// B: DOCUMENT READY (Initialization and Isolation)
// ====================================================================

$(document).ready(function () {

    // 🎯 Use $.each() to loop through ALL containers and initialize them separately
    $(".signup-multistep-wrapper").each(function () {

        // --- Setup and Scoping for THIS SPECIFIC Form Instance ---
        
        const $wrapper = $(this); // The current form container
        const $steps = $wrapper.find(".step");
        const totalSteps = $steps.length;
        let currentStep = 1; // State is unique to this form!

        // --- Scoped UI Helpers (functions are local to THIS form) ---

        function updateProgressBar() {
            const progressPercentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
            $wrapper.find(".progress-bar").css("width", progressPercentage + "%");
        }

        function updateStepCircles() {
            $wrapper.find(".step-circle").each(function (index) {
                const stepIndex = index + 1;
                $(this).removeClass("active completed");
                if (stepIndex < currentStep) {
                    $(this).addClass("completed");
                } else if (stepIndex === currentStep) {
                    $(this).addClass("active");
                }
            });
        }

        // --- Core Validation Logic (Fully Scoped) ---
        
        async function validateCurrentStep() {
            let isValid = true;
            // Scopes the current step container to THIS form
            const $currentStep = $wrapper.find(".step-" + currentStep);

            // 1. Clear errors and validate required fields
            $currentStep.find("input").each(function () {
                const $input = $(this);
                // Finds feedback relative to the input in THIS form
                const $feedback = $input.nextAll(".invalid-feedback"); 

                $input.removeClass("is-invalid");
                $feedback.removeClass("d-block").text("");

                if ($input.prop("required") && !$input.val().trim()) {
                    isValid = false;
                    $input.addClass("is-invalid");
                    $feedback.text("This field is required.").addClass("d-block");
                }
            });
            
            if (!isValid) return false;

            // 2. Special validation logic for Step 1
            if (currentStep === 1) { 
                
                // --- Email Validation (Scoped) ---
                const $emailInput = $currentStep.find('input[name="email"]'); 
                const email = $emailInput.val().trim();
                const $emailFeedback = $emailInput.nextAll(".invalid-feedback");

                if ($emailInput.length) {
                    if (!validateEmail(email)) {
                        isValid = false;
                        $emailInput.addClass("is-invalid");
                        $emailFeedback.text("Please enter a valid email address").addClass('d-block');
                        return isValid; 
                    }

                    $emailInput.prop('disabled', true);
                    const $spinnerEmail = $(createSpinner()).addClass('email-check-spinner');
                    $emailInput.after($spinnerEmail);
                    
                    const emailCheck = await checkEmailExists(email);
                    
                    $spinnerEmail.remove();
                    $emailInput.prop('disabled', false);
                    
                    if (emailCheck.exists) {
                        isValid = false;
                        $emailInput.addClass("is-invalid");
                        $emailFeedback.text(emailCheck.message).addClass('d-block');
                    }
                }
                
                if (!isValid) return false;

                // --- Mobile Validation (Scoped) ---
                const $mobileInput = $currentStep.find('input[name="mobile"]');
                const mobile = $mobileInput.val().trim();
                const $mobileFeedback = $mobileInput.nextAll(".invalid-feedback");
                
                if ($mobileInput.length && mobile) {
                    $mobileInput.prop('disabled', true);
                    const $mobileSpinner = $(createSpinner());
                    $mobileInput.after($mobileSpinner);
                    
                    const mobileCheck = await checkMobileExists(mobile);
                    
                    $mobileSpinner.remove();
                    $mobileInput.prop('disabled', false);
                    
                    if (mobileCheck.exists) {
                        isValid = false;
                        $mobileInput.addClass("is-invalid");
                        $mobileFeedback.text(mobileCheck.message).addClass('d-block');
                    }
                }

                if (!isValid) return false;
                
                
                // --- Password Match and Strength Validation (Scoped) ---
                // Finds the password inputs with IDs within the current step container
                const $passwordInput = $currentStep.find('input#password');
                const $passwordConfirmInput = $currentStep.find('input#password-confirm'); 
                
                if ($passwordInput.length && $passwordConfirmInput.length) { 
                    
                    // Password Match Check
                    if ($passwordInput.val() !== $passwordConfirmInput.val()) {
                         isValid = false;
                         $passwordConfirmInput.addClass("is-invalid");
                         $passwordConfirmInput.nextAll(".invalid-feedback").text("Passwords do not match").addClass('d-block');
                    }
                    if (!isValid) return false;

                    // Password Strength (AJAX)
                    const passwordInput = $passwordInput[0]; // Native DOM element
                    const $passwordFeedback = $passwordInput.nextAll(".invalid-feedback");
                    
                    if (passwordInput.value) {
                        passwordInput.disabled = true;
                        const passwordSpinner = createSpinner();
                        $passwordInput.after(passwordSpinner); 
                        
                        const passwordCheck = await validatePassword(passwordInput.value);
                        
                        $(passwordSpinner).remove();
                        passwordInput.disabled = false;
                        
                        if (!passwordCheck.valid) {
                            $passwordInput.addClass('is-invalid');
                            $passwordFeedback.text(passwordCheck.message).addClass('d-block');
                            isValid = false;
                        }
                    }
                }
            } // End of currentStep === 1 check
            
            return isValid;
        }

        // --- Initialization and Event Handlers (Scoped) ---

        // Initialize: Hide all except the first step
        $steps.slice(1).hide(); 
        updateStepCircles();

        // Previous step handler
        $wrapper.find(".sign-up-prev-step, .prev-step").click(function () {
            if (currentStep > 1) {
                $wrapper.find(".step-" + currentStep).addClass("animate__animated animate__fadeOutRight");
                currentStep--;
                setTimeout(function () {
                    $steps.removeClass("animate__animated animate__fadeOutRight").hide();
                    $wrapper.find(".step-" + currentStep)
                    .show()
                    .addClass("animate__animated animate__fadeInLeft");
                    updateProgressBar();
                    updateStepCircles();
                }, 500);
            }
        });

        // Next button handler - Now uses async/await
        // $wrapper.find(".sign-up-next-step, .next-step").click(async function () {
        //     const isValid = await validateCurrentStep();
        //     if (!isValid) return;
        //     if (currentStep < totalSteps) {
        //         $wrapper.find(".step-" + currentStep).addClass("animate__animated animate__fadeOutLeft");
        //         currentStep++;
        //         setTimeout(function () {
        //             $steps.removeClass("animate__animated animate__fadeOutLeft").hide();
        //             $wrapper.find(".step-" + currentStep)
        //             .show()
        //             .addClass("animate__animated animate__fadeInRight");
        //             updateProgressBar();
        //             updateStepCircles();
        //         }, 500);
        //     }
        // });

        $wrapper.find(".sign-up-next-step, .next-step").click(async function () {
            const isValid = await validateCurrentStep();
            if (!isValid) return;

    // --- Save Step-1 user to unregistered_users ---
            if (currentStep === 1) {
                const $step1 = $wrapper.find(".step-1");
                const payload = {
                    name: $step1.find('input[name="name"]').val() || null,
                    email: $step1.find('input[name="email"]').val(),
                    mobile: $step1.find('input[name="mobile"]').val() || null,
                };

        // Save via AJAX
                await saveUnregisteredUser(payload);
            }

    // --- Move to next step ---
            if (currentStep < totalSteps) {
                $wrapper.find(".step-" + currentStep).addClass("animate__animated animate__fadeOutLeft");
                currentStep++;
                setTimeout(function () {
                    $steps.removeClass("animate__animated animate__fadeOutLeft").hide();
                    $wrapper.find(".step-" + currentStep)
                    .show()
                    .addClass("animate__animated animate__fadeInRight");
                    updateProgressBar();
                    updateStepCircles();
                }, 500);
            }
        });



        
        // Toggle password icon handler (Scoped)
        $wrapper.find('.toggle-password').on('click', function () {
            const passwordInput = $(this).siblings('input');
            const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
            passwordInput.attr('type', type);
            $(this).toggleClass('bi-eye-slash-fill bi-eye-fill');
        });
    }); // End of $.each loop
});




// B: DOCUMENT READY (Initialization and Logic Isolation)

$(document).ready(function () {
    // 🎯 Loop through all multi-step form wrappers
    $(".business-multistep-wrapper").each(function () {
        
        // --- LOCAL SCOPE SETUP ---
        const $wrapper = $(this);
        const $steps = $wrapper.find(".step");
        const totalSteps = $steps.length;
        let currentStep = 1; // Unique state variable for THIS form

        // --- LOCAL UI FUNCTIONS ---

        function updateProgressBar() {
            // totalSteps - 1 (assuming 3 steps means progress goes 0%, 50%, 100%)
            const progressPercentage = ((currentStep - 1) / (totalSteps - 1)) * 100; 
            $wrapper.find(".progress-bar").css("width", progressPercentage + "%");
        }

        function updateStepCircles() {
            $wrapper.find(".step-circle").each(function (index) {
                const stepIndex = index + 1;
                $(this).removeClass("active completed");
                if (stepIndex < currentStep) $(this).addClass("completed");
                if (stepIndex === currentStep) $(this).addClass("active");
            });
        }
        
        // --- LOCAL VALIDATION FUNCTION ---
        
        async function validateCurrentStep() {
            let isValid = true;
            // Scopes the current step container to THIS form
            const $currentStep = $wrapper.find(".step-" + currentStep);
            
            // Clear errors for THIS step's inputs
            $currentStep.find(".is-invalid").removeClass("is-invalid");
            $currentStep.find(".invalid-feedback").text("");
            
            // Validate all required fields within THIS step
            $currentStep.find("input[required]").each(function() {
                const $input = $(this);
                const value = $input.val().trim();
                
                if (!value) {
                    isValid = false;
                    $input.addClass("is-invalid");
                    // Assuming .invalid-feedback is a sibling or next sibling
                    $input.next(".invalid-feedback").text("This field is required"); 
                }
            });
            
            if (!isValid) return false;
            

            // Special validation for step 2 (adjust step number if needed)
            if (currentStep === 2) {
                
                // --- Email Validation (Scoped) ---
                // CRITICAL: Scope the input search to the current step/wrapper
                const $emailInput = $currentStep.find('input[name="email"]');
                const email = $emailInput.val().trim();
                const $emailFeedback = $emailInput.next(".invalid-feedback"); // Assuming immediate sibling
                
                if ($emailInput.length) {
                    // Email format validation
                    if (!validateEmail(email)) {
                        isValid = false;
                        $emailInput.addClass("is-invalid");
                        $emailFeedback.text("Please enter a valid email address");
                        return isValid;
                    }
                    
                    // Email existence check (AJAX)
                    $emailInput.prop('disabled', true);
                    const $spinner = $(createSpinner()).addClass('email-check-spinner');
                    $emailInput.after($spinner);
                    
                    const emailCheck = await checkEmailExists(email);
                    
                    $spinner.remove();
                    $emailInput.prop('disabled', false);

                    if (emailCheck.exists) {
                        isValid = false;
                        $emailInput.addClass("is-invalid");
                        $emailFeedback.text(emailCheck.message);
                    }
                }
                
                if (!isValid) return false;


                // --- Password Match Validation (Scoped) ---
                // CRITICAL: Find inputs by ID within the CURRENT step/wrapper
                const $passwordInput = $currentStep.find('#password');
                const $passwordConfirmInput = $currentStep.find('#password-confirm');

                if ($passwordInput.length && $passwordConfirmInput.length) {
                    if ($passwordInput.val() !== $passwordConfirmInput.val()) {
                        isValid = false;
                        $passwordConfirmInput.addClass("is-invalid");
                        $passwordConfirmInput.next(".invalid-feedback").text("Passwords do not match");
                    }
                    if (!isValid) return false;
                    
                    // --- Password Strength Validation (AJAX - Scoped) ---
                    const passwordDOMInput = $passwordInput[0]; // Get native DOM element
                    
                    if (passwordDOMInput.value) {
                        passwordDOMInput.disabled = true;
                        const passwordSpinner = createSpinner();
                        passwordDOMInput.parentNode.insertBefore(passwordSpinner, passwordDOMInput.nextSibling);
                        
                        const passwordCheck = await validatePassword(passwordDOMInput.value);
                        
                        passwordSpinner.remove();
                        passwordDOMInput.disabled = false;
                        
                        if (!passwordCheck.valid) {
                            passwordDOMInput.classList.add('is-invalid');
                            // Ensure you target the feedback element correctly
                            $(passwordDOMInput).next(".invalid-feedback").text(passwordCheck.message);
                            isValid = false;
                        }
                    }
                }
            } // End of currentStep === 2 check

            return isValid;
        }

        // --- Event Handlers (Scoped to $wrapper) ---

        // Initialize: Hide all except the first step
        $steps.slice(1).hide(); 
        updateStepCircles();
        
        // Toggle password handler (Scoped)
        $wrapper.find('.toggle-password').on('click', function () {
             const passwordInput = $(this).siblings('input');
             const type = passwordInput.attr('type') === 'password' ? 'text' : 'password';
             passwordInput.attr('type', type);
             $(this).toggleClass('bi-eye-slash-fill bi-eye-fill');
        });

        // Next step click handler (Scoped)
        $wrapper.find(".next-step").click(async function() {
            if (!await validateCurrentStep()) return;
            
            if (currentStep < totalSteps) {
                $wrapper.find(".step-" + currentStep).addClass("animate__animated animate__fadeOutLeft");
                currentStep++;
                setTimeout(function() {
                    $steps.removeClass("animate__animated animate__fadeOutLeft").hide();
                    $wrapper.find(".step-" + currentStep)
                    .show()
                    .addClass("animate__animated animate__fadeInRight");
                    updateProgressBar();
                    updateStepCircles();
                }, 500);
            }
        });

        // Previous step handler (Scoped)
        $wrapper.find(".prev-step").click(function () {
            if (currentStep > 1) {
                $wrapper.find(".step-" + currentStep).addClass("animate__animated animate__fadeOutRight");
                currentStep--;
                setTimeout(function () {
                    $steps.removeClass("animate__animated animate__fadeOutRight").hide();
                    $wrapper.find(".step-" + currentStep).show().addClass("animate__animated animate__fadeInLeft");
                    updateProgressBar();
                    updateStepCircles();
                }, 500);
            }
        });

    }); // End of $.each loop
});