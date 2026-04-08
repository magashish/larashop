<script src="https://js.stripe.com/v3/"></script>
<script>

/* ================================================
   GATEWAY TOGGLE — Stripe / PayPal
================================================= */
document.addEventListener('DOMContentLoaded', () => {

    // Main form toggle
    document.querySelectorAll('input[name="gateway_choice_main"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const stripeSection = document.getElementById('stripe-section-main');
            const paypalSection = document.getElementById('paypal-section-main');
            if (this.value === 'stripe') {
                if (stripeSection) stripeSection.style.display = 'block';
                if (paypalSection) paypalSection.style.display = 'none';
            } else {
                if (stripeSection) stripeSection.style.display = 'none';
                if (paypalSection) paypalSection.style.display = 'block';
            }
        });
    });

    // Component forms toggle
    document.querySelectorAll('[name^="gateway_choice_"]').forEach(function(radio) {
        const name = radio.getAttribute('name');
        if (name === 'gateway_choice_main') return;
        radio.addEventListener('change', function() {
            const id = name.replace('gateway_choice_', '');
            const stripeSection = document.getElementById('stripe-section-' + id);
            const paypalSection = document.getElementById('paypal-section-' + id);
            if (this.value === 'stripe') {
                if (stripeSection) stripeSection.style.display = 'block';
                if (paypalSection) paypalSection.style.display = 'none';
            } else {
                if (stripeSection) stripeSection.style.display = 'none';
                if (paypalSection) paypalSection.style.display = 'block';
            }
        });
    });

});

/* ================================================
   PAYPAL HANDLER — Main Form
================================================= */
async function handlePayPalClickMain() {

    // Field elements
    const nameEl   = document.getElementById('name');
    const emailEl  = document.getElementById('signupemail');
    const mobileEl = document.getElementById('mobile');
    const termsEl  = document.getElementById('flexCheckPayPalMain');

    const name   = nameEl?.value?.trim()   || '';
    const email  = emailEl?.value?.trim()  || '';
    const mobile = mobileEl?.value?.trim() || '';

    // Error divs — field ke neeche
    const nameErr   = nameEl?.closest('.input-group-icon')?.querySelector('.invalid-feedback')
                      || nameEl?.parentElement?.querySelector('.invalid-feedback');
    const emailErr  = emailEl?.parentElement?.querySelector('.invalid-feedback');
    const mobileErr = mobileEl?.parentElement?.querySelector('.invalid-feedback');
    const planErr   = document.getElementById('paypal-plan-error-main');

    // Clear all errors
    [nameEl, emailEl, mobileEl].forEach(el => el?.classList.remove('is-invalid'));
    [nameErr, emailErr, mobileErr, planErr].forEach(el => {
        if (el) { el.textContent = ''; el.style.display = 'none'; }
    });
    const oldTermsErr = document.getElementById('terms-error-paypal-main');
    if (oldTermsErr) { oldTermsErr.textContent = ''; oldTermsErr.style.display = 'none'; }

    let hasError = false;

    // Plan check
    const selectedPlan = document.querySelector('#payment-form input[name="selected_plan_id"]:checked');
    if (!selectedPlan) {
        if (planErr) { planErr.textContent = 'Please select a plan.'; planErr.style.display = 'block'; }
        hasError = true;
    }

    // Field validation
    if (!name) {
        nameEl?.classList.add('is-invalid');
        if (nameErr) { nameErr.textContent = 'Full name is required.'; nameErr.style.display = 'block'; }
        hasError = true;
    }
    if (!email) {
        emailEl?.classList.add('is-invalid');
        if (emailErr) { emailErr.textContent = 'Email is required.'; emailErr.style.display = 'block'; }
        hasError = true;
    } else if (!validateEmail(email)) {
        emailEl?.classList.add('is-invalid');
        if (emailErr) { emailErr.textContent = 'Please enter a valid email address.'; emailErr.style.display = 'block'; }
        hasError = true;
    }
    if (!mobile || mobile.length < 8) {
        mobileEl?.classList.add('is-invalid');
        if (mobileErr) { mobileErr.textContent = 'Valid phone number is required.'; mobileErr.style.display = 'block'; }
        hasError = true;
    }

    // Terms check
    if (!termsEl || !termsEl.checked) {
        let termsErrEl = document.getElementById('terms-error-paypal-main');
        if (!termsErrEl) {
            termsErrEl = document.createElement('div');
            termsErrEl.id = 'terms-error-paypal-main';
            termsErrEl.className = 'text-danger small mt-1';
            termsEl?.closest('.form-check')?.after(termsErrEl);
        }
        termsErrEl.textContent = 'Please agree to the terms and conditions.';
        termsErrEl.style.display = 'block';
        hasError = true;
    }

    if (hasError) return;

    // AJAX — email + mobile DB check
    const btn = document.querySelector('#paypal-section-main button[type="button"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Checking...'; }

    try {
        const [emailResult, mobileResult] = await Promise.all([
            checkEmailExists(email),
            checkMobileExists(mobile)
        ]);

        if (emailResult.exists) {
            emailEl?.classList.add('is-invalid');
            if (emailErr) { emailErr.textContent = emailResult.message || 'This email is already registered.'; emailErr.style.display = 'block'; }
            if (btn) { btn.disabled = false; btn.innerHTML = '🅿 Pay with PayPal'; }
            return;
        }
        if (mobileResult.exists) {
            mobileEl?.classList.add('is-invalid');
            if (mobileErr) { mobileErr.textContent = mobileResult.message || 'This mobile number is already registered.'; mobileErr.style.display = 'block'; }
            if (btn) { btn.disabled = false; btn.innerHTML = '🅿 Pay with PayPal'; }
            return;
        }
    } catch(e) {
        console.error('PayPal validation error:', e);
        if (btn) { btn.disabled = false; btn.innerHTML = '🅿 Pay with PayPal'; }
        return;
    }

    // Sab pass — hidden form fields fill karke submit karo
    const planId = selectedPlan.value;
    document.getElementById('paypal-plan-main').value   = planId;
    document.getElementById('paypal-name-main').value   = name;
    document.getElementById('paypal-email-main').value  = email;
    document.getElementById('paypal-mobile-main').value = mobile;

    // Direct form submit — native JS submit (not jQuery, not form submit event)
    document.getElementById('paypal-hidden-form-main').submit();
}

/* ================================================
   PAYPAL HANDLER — Component Forms (formId based)
================================================= */
async function handlePayPalClick(formId) {

    const form     = document.getElementById('payment-form-' + formId);
    const termsEl  = document.getElementById('flexCheckPayPal-' + formId);

    // Field elements
    const nameEl   = form?.querySelector('input[name="name"]');
    const emailEl  = form?.querySelector('input[name="email"]');
    const mobileEl = form?.querySelector('input[name="mobile"]');

    const name   = nameEl?.value?.trim()   || '';
    const email  = emailEl?.value?.trim()  || '';
    const mobile = mobileEl?.value?.trim() || '';

    // Error divs
    const nameErr   = nameEl?.closest('.input-group')?.parentElement?.querySelector('.invalid-feedback');
    const emailErr  = emailEl?.closest('.input-group')?.parentElement?.querySelector('.invalid-feedback');
    const mobileErr = mobileEl?.closest('.input-group')?.parentElement?.querySelector('.invalid-feedback');
    const planErr   = document.getElementById('paypal-plan-error-' + formId);

    // Clear all errors
    [nameEl, emailEl, mobileEl].forEach(el => el?.classList.remove('is-invalid'));
    [nameErr, emailErr, mobileErr, planErr].forEach(el => {
        if (el) { el.textContent = ''; el.style.display = 'none'; }
    });
    const oldTermsErr = document.getElementById('terms-error-paypal-' + formId);
    if (oldTermsErr) { oldTermsErr.textContent = ''; oldTermsErr.style.display = 'none'; }

    let hasError = false;

    // Plan check
    const selectedPlan = form?.querySelector('input[name="selected_plan_id"]:checked');
    if (!selectedPlan) {
        if (planErr) { planErr.textContent = 'Please select a plan.'; planErr.style.display = 'block'; }
        hasError = true;
    }

    // Field validation
    if (!name) {
        nameEl?.classList.add('is-invalid');
        if (nameErr) { nameErr.textContent = 'Full name is required.'; nameErr.style.display = 'block'; }
        hasError = true;
    }
    if (!email) {
        emailEl?.classList.add('is-invalid');
        if (emailErr) { emailErr.textContent = 'Email is required.'; emailErr.style.display = 'block'; }
        hasError = true;
    } else if (!validateEmail(email)) {
        emailEl?.classList.add('is-invalid');
        if (emailErr) { emailErr.textContent = 'Please enter a valid email address.'; emailErr.style.display = 'block'; }
        hasError = true;
    }
    if (!mobile || mobile.length < 8) {
        mobileEl?.classList.add('is-invalid');
        if (mobileErr) { mobileErr.textContent = 'Valid phone number is required.'; mobileErr.style.display = 'block'; }
        hasError = true;
    }

    // Terms check
    if (!termsEl || !termsEl.checked) {
        let termsErrEl = document.getElementById('terms-error-paypal-' + formId);
        if (!termsErrEl) {
            termsErrEl = document.createElement('div');
            termsErrEl.id = 'terms-error-paypal-' + formId;
            termsErrEl.className = 'text-danger small mt-1';
            termsEl?.closest('.form-check')?.after(termsErrEl);
        }
        termsErrEl.textContent = 'Please agree to the terms and conditions.';
        termsErrEl.style.display = 'block';
        hasError = true;
    }

    if (hasError) return;

    // AJAX — email + mobile DB check
    const btn = document.getElementById('paypal-section-' + formId)?.querySelector('button[type="button"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Checking...'; }

    try {
        const [emailResult, mobileResult] = await Promise.all([
            checkEmailExists(email),
            checkMobileExists(mobile)
        ]);

        if (emailResult.exists) {
            emailEl?.classList.add('is-invalid');
            if (emailErr) { emailErr.textContent = emailResult.message || 'This email is already registered.'; emailErr.style.display = 'block'; }
            if (btn) { btn.disabled = false; btn.innerHTML = '🅿 Pay with PayPal'; }
            return;
        }
        if (mobileResult.exists) {
            mobileEl?.classList.add('is-invalid');
            if (mobileErr) { mobileErr.textContent = mobileResult.message || 'This mobile number is already registered.'; mobileErr.style.display = 'block'; }
            if (btn) { btn.disabled = false; btn.innerHTML = '🅿 Pay with PayPal'; }
            return;
        }
    } catch(e) {
        console.error('PayPal validation error:', e);
        if (btn) { btn.disabled = false; btn.innerHTML = '🅿 Pay with PayPal'; }
        return;
    }

    // Sab pass — hidden form submit
    const planId = selectedPlan.value;
    document.getElementById('paypal-plan-input-' + formId).value   = planId;
    document.getElementById('paypal-name-input-' + formId).value   = name;
    document.getElementById('paypal-email-input-' + formId).value  = email;
    document.getElementById('paypal-mobile-input-' + formId).value = mobile;

    document.getElementById('paypal-hidden-form-' + formId).submit();
}

/* ================================================
   HELPERS — GLOBAL
================================================= */
async function checkMobileExists(mobile) {
    try {
        const response = await $.ajax({
            url: '/check-mobile', method: 'POST',
            data: { mobile, _token: $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json'
        });
        return response;
    } catch (error) {
        return { exists: false, message: error.responseJSON?.message || 'Error validating mobile number' };
    }
}

async function checkEmailExists(email) {
    try {
        const response = await $.ajax({
            url: '/check-email', method: 'POST',
            data: { email, _token: $('meta[name="csrf-token"]').attr('content') },
            dataType: 'json'
        });
        return response;
    } catch (error) {
        return { exists: false, message: 'Error checking email' };
    }
}

function validateEmail(email) { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email); }
function createSpinner() {
    const s = document.createElement('div');
    s.className = 'spinner-border spinner-border-sm text-primary ms-2';
    return s;
}

/* ================================================
   MAIN FORM — STRIPE INIT (register-form)
================================================= */
document.addEventListener('DOMContentLoaded', () => {
    console.log('Stripe init started');

    const stripeKey = "{{ config('services.stripe.key') }}";
    if (!stripeKey) return console.error('Stripe publishable key missing');

    const stripe   = Stripe(stripeKey);
    const elements = stripe.elements();

    const form          = document.getElementById('payment-form');
    const cardMount     = document.getElementById('card-element');
    const cardButton    = document.getElementById('card-button');
    const planRadios    = document.querySelectorAll('#payment-form input[name="selected_plan_id"]');
    const termsCheckbox = document.getElementById('flexCheckDefault');
    const walletOverlay = document.getElementById('wallet-overlay');

    if (!form || !cardMount) return console.error('Payment form or card mount missing');

    let cardErrors = document.getElementById('card-errors');
    if (!cardErrors) {
        cardErrors = document.createElement('div');
        cardErrors.id = 'card-errors';
        cardErrors.className = 'text-danger mt-2';
        cardErrors.setAttribute('role', 'alert');
        cardMount.after(cardErrors);
    }

    const walletErrors = document.getElementById('wallet-errors') || cardErrors;

    /* --- Overlay --- */
    function toggleWalletOverlay() {
        if (walletOverlay) walletOverlay.style.display = 'block';
        if (walletErrors) walletErrors.textContent = '';
    }

    if (walletOverlay) {
        walletOverlay.addEventListener('click', async function(e) {
            e.stopPropagation();
            e.preventDefault();

            const nameEl   = document.getElementById('name');
            const emailEl  = document.getElementById('signupemail');
            const mobileEl = document.getElementById('mobile');
            const name     = nameEl?.value?.trim();
            const email    = emailEl?.value?.trim();
            const mobile   = mobileEl?.value?.trim();

            const nameErr   = nameEl?.closest('.input-group-icon')?.querySelector('.invalid-feedback') || nameEl?.parentElement?.querySelector('.invalid-feedback');
            const emailErr  = emailEl?.parentElement?.querySelector('.invalid-feedback');
            const mobileErr = mobileEl?.parentElement?.querySelector('.invalid-feedback');

            [nameEl, emailEl, mobileEl].forEach(el => el?.classList.remove('is-invalid'));
            [nameErr, emailErr, mobileErr].forEach(el => { if(el) { el.textContent = ''; el.style.display = 'none'; } });
            if (walletErrors) walletErrors.textContent = '';

            let hasError = false;

            if (!name) { nameEl?.classList.add('is-invalid'); if (nameErr) { nameErr.textContent = 'Full name is required.'; nameErr.style.display = 'block'; } hasError = true; }
            if (!email) { emailEl?.classList.add('is-invalid'); if (emailErr) { emailErr.textContent = 'Email is required.'; emailErr.style.display = 'block'; } hasError = true; }
            else if (!validateEmail(email)) { emailEl?.classList.add('is-invalid'); if (emailErr) { emailErr.textContent = 'Please enter a valid email address.'; emailErr.style.display = 'block'; } hasError = true; }
            if (!mobile || mobile.length < 8) { mobileEl?.classList.add('is-invalid'); if (mobileErr) { mobileErr.textContent = 'Valid phone number is required.'; mobileErr.style.display = 'block'; } hasError = true; }

            if (termsCheckbox && !termsCheckbox.checked) {
                let termsErrEl = document.getElementById('terms-error-main');
                if (!termsErrEl) {
                    termsErrEl = document.createElement('div');
                    termsErrEl.id = 'terms-error-main';
                    termsErrEl.className = 'text-danger small mt-1';
                    termsCheckbox.closest('.form-check')?.after(termsErrEl);
                }
                termsErrEl.textContent = 'Please agree to the terms and conditions.';
                termsErrEl.style.display = 'block';
                hasError = true;
            } else {
                const termsErrEl = document.getElementById('terms-error-main');
                if (termsErrEl) { termsErrEl.textContent = ''; termsErrEl.style.display = 'none'; }
            }

            if (hasError) return;

            if (walletErrors) walletErrors.textContent = 'Checking...';
            walletOverlay.style.pointerEvents = 'none';

            try {
                const [emailResult, mobileResult] = await Promise.all([checkEmailExists(email), checkMobileExists(mobile)]);
                walletOverlay.style.pointerEvents = 'auto';

                if (emailResult.exists) {
                    emailEl?.classList.add('is-invalid');
                    if (emailErr) { emailErr.textContent = emailResult.message || 'This email is already registered.'; emailErr.style.display = 'block'; }
                    if (walletErrors) walletErrors.textContent = '';
                    return;
                }
                if (mobileResult.exists) {
                    mobileEl?.classList.add('is-invalid');
                    if (mobileErr) { mobileErr.textContent = mobileResult.message || 'This mobile number is already registered.'; mobileErr.style.display = 'block'; }
                    if (walletErrors) walletErrors.textContent = '';
                    return;
                }
            } catch(err) {
                walletOverlay.style.pointerEvents = 'auto';
                console.error('Wallet AJAX error:', err);
            }

            if (walletErrors) walletErrors.textContent = '';
            walletOverlay.style.display = 'none';

            const prContainer = document.getElementById('payment-request-button-container');
            if (prContainer) {
                const btn = prContainer.querySelector('button, [role="button"], iframe');
                if (btn) btn.click();
            }
        });
    }

    if (termsCheckbox) termsCheckbox.addEventListener('change', function() {
        if (walletErrors) walletErrors.textContent = '';
        const termsErrEl = document.getElementById('terms-error-main');
        if (termsErrEl) { termsErrEl.textContent = ''; termsErrEl.style.display = 'none'; }
    });

    /* --- Stripe Card --- */
    const card = elements.create('card', {
        style: {
            base: { fontSize: '16px', color: '#32325d', fontFamily: 'system-ui, -apple-system, BlinkMacSystemFont', '::placeholder': { color: '#aab7c4' } },
            invalid: { color: '#fa755a' }
        }
    });

    let cardMounted = false;
    function mountCardIfVisible() {
        const step2 = document.querySelector('.step-2');
        if (step2 && !cardMounted && getComputedStyle(step2).display !== 'none') {
            card.mount(cardMount);
            cardMounted = true;
        }
    }

    const step2 = document.querySelector('.step-2');
    if (step2) new MutationObserver(mountCardIfVisible).observe(step2, { attributes: true, attributeFilter: ['style'] });
    mountCardIfVisible();

    card.on('change', e => { cardErrors.textContent = e.error ? e.error.message : ''; });

    /* --- Plan Helpers --- */
    function getSelectedPlan() {
        let planId = null, name = '', priceCents = 0;
        planRadios.forEach(radio => {
            if (!radio.checked) return;
            planId = radio.value;
            const label = document.querySelector(`label[for="${radio.id}"]`);
            if (!label) return;
            const priceEl = label.querySelector('.price');
            const nameEl  = label.querySelector('.package-name');
            if (nameEl) name = nameEl.textContent.trim().split('-')[0].trim();
            if (priceEl) priceCents = Math.round(parseFloat(priceEl.textContent.replace(/[$,]/g, '')) * 100);
        });
        return { planId, name, priceCents };
    }

    function setHiddenInputs(planId, paymentMethodId) {
        let pm = document.getElementById('payment-method-id');
        if (!pm) { pm = document.createElement('input'); pm.type = 'hidden'; pm.name = 'payment_method'; pm.id = 'payment-method-id'; form.appendChild(pm); }
        pm.value = paymentMethodId;
        let plan = document.getElementById('pr_selected_plan_id_hidden');
        if (plan) plan.value = planId;
    }

    /* --- Form Submit --- */
    form.addEventListener('submit', async e => {
        if (form.dataset.wallet === 'true') return;
        if (termsCheckbox && !termsCheckbox.checked) {
            e.preventDefault();
            cardErrors.textContent = 'Please agree to the terms and conditions before proceeding.';
            return;
        }
        e.preventDefault();
        const { planId } = getSelectedPlan();
        if (!planId) { cardErrors.textContent = 'Please select a plan'; return; }
        cardButton.disabled = true;
        cardButton.textContent = 'Processing...';
        const { paymentMethod, error } = await stripe.createPaymentMethod({ type: 'card', card });
        if (error) { cardErrors.textContent = error.message; cardButton.disabled = false; cardButton.textContent = 'Submit'; return; }
        setHiddenInputs(planId, paymentMethod.id);
        form.submit();
    });

    /* --- Google Pay / Apple Pay / Link Pay --- */
    let paymentRequest, prButton;

    function initPaymentRequest() {
        const plan      = getSelectedPlan();
        const container = document.getElementById('payment-request-button-container');
        if (!container || !plan.planId || plan.priceCents <= 0) { if (container) container.style.display = 'none'; return; }
        if (paymentRequest) { paymentRequest.update({ total: { label: plan.name, amount: plan.priceCents } }); return; }

        paymentRequest = stripe.paymentRequest({
            country: 'AU', currency: 'aud',
            total: { label: plan.name, amount: plan.priceCents },
            requestPayerName: true, requestPayerEmail: true
        });
        prButton = elements.create('paymentRequestButton', { paymentRequest });

        paymentRequest.canMakePayment().then(result => {
            if (result) {
                prButton.mount(container);
                container.style.display = 'block';
                toggleWalletOverlay();
            } else {
                container.style.display = 'none';
            }
        });

        paymentRequest.on('paymentmethod', async ev => {
            walletErrors.textContent = '';
            if (termsCheckbox && !termsCheckbox.checked) { ev.complete('fail'); walletErrors.textContent = 'Please agree to the terms and conditions.'; return; }
            const { planId } = getSelectedPlan();
            if (!planId) { ev.complete('fail'); walletErrors.textContent = 'Please select a plan.'; return; }
            const name   = document.getElementById('name')?.value?.trim();
            const email  = document.getElementById('signupemail')?.value?.trim();
            const mobile = document.getElementById('mobile')?.value?.trim();
            if (!name || !email || !mobile) { ev.complete('fail'); walletErrors.textContent = 'Please complete your contact details.'; return; }
            try {
                const [emailResult, mobileResult] = await Promise.all([checkEmailExists(email), checkMobileExists(mobile)]);
                if (emailResult.exists) { ev.complete('fail'); walletErrors.textContent = emailResult.message || 'This email is already registered.'; return; }
                if (mobileResult.exists) { ev.complete('fail'); walletErrors.textContent = mobileResult.message || 'This mobile is already registered.'; return; }
            } catch(e) { console.error('Wallet AJAX error:', e); }
            setHiddenInputs(planId, ev.paymentMethod.id);
            form.dataset.wallet = 'true';
            ev.complete('success');
            form.submit();
        });
    }

    initPaymentRequest();
    planRadios.forEach(r => r.addEventListener('change', initPaymentRequest));
    console.log('Stripe initialized successfully');
});

/* ================================================
   COMPONENT FORMS — STRIPE INIT (popup forms)
================================================= */
document.addEventListener('DOMContentLoaded', () => {
    const stripeKey = "{{ config('services.stripe.key') }}";
    if (!stripeKey) return console.error('Stripe key missing');
    const stripe = Stripe(stripeKey);
    document.querySelectorAll('.payment-form').forEach(form => {
        const id = form.dataset.formId;
        if (id) initStripeForm(stripe, form, id);
    });
});

function initStripeForm(stripe, form, id) {
    const elements      = stripe.elements();
    const cardMount     = document.getElementById('card-element-' + id);
    const cardButton    = document.getElementById('card-button-' + id);
    const cardErrors    = document.getElementById('card-errors-' + id);
    const termsCheckbox = document.getElementById('flexCheckDefault-' + id);
    const walletOverlay = document.getElementById('wallet-overlay-' + id);
    const walletErrors  = document.getElementById('wallet-errors-' + id);
    const planRadios    = form.querySelectorAll('input[name="selected_plan_id"]');
    const emailInput    = form.querySelector('input[name="email"]');
    const mobileInput   = form.querySelector('input[name="mobile"]');
    const nameInput     = form.querySelector('input[name="name"]');
    const emailError    = emailInput?.closest('.input-group')?.parentElement?.querySelector('.invalid-feedback');
    const mobileError   = mobileInput?.closest('.input-group')?.parentElement?.querySelector('.invalid-feedback');

    if (!cardMount) return console.error('Card mount missing for form: ' + id);
    
    const card = elements.create('card', {
        style: {
            base: {
                fontSize: '16px',
                color: '#fff',
                fontFamily: 'system-ui, -apple-system, BlinkMacSystemFont',
                '::placeholder': { color: '#fff' },
                ':-webkit-autofill': { color: '#fff' },
            },
            invalid: { color: '#dc3545' },
        }
    });

    card.mount(cardMount);
    card.on('change', e => { if (cardErrors) cardErrors.textContent = e.error ? e.error.message : ''; });

    /* --- Overlay --- */
    function toggleWalletOverlay() {
        if (walletOverlay) walletOverlay.style.display = 'block';
        if (walletErrors) walletErrors.textContent = '';
    }

    if (walletOverlay) {
        walletOverlay.addEventListener('click', async function(e) {
            e.stopPropagation();
            e.preventDefault();

            const name   = nameInput?.value?.trim();
            const email  = emailInput?.value?.trim();
            const mobile = mobileInput?.value?.trim();
            const nameErr   = nameInput?.closest('.input-group')?.parentElement?.querySelector('.invalid-feedback');
            const emailErr  = emailInput?.closest('.input-group')?.parentElement?.querySelector('.invalid-feedback');
            const mobileErr = mobileInput?.closest('.input-group')?.parentElement?.querySelector('.invalid-feedback');

            [nameInput, emailInput, mobileInput].forEach(el => el?.classList.remove('is-invalid'));
            [nameErr, emailErr, mobileErr].forEach(el => { if(el) { el.textContent = ''; el.style.display = 'none'; } });
            if (walletErrors) walletErrors.textContent = '';

            let hasError = false;

            if (!name) { nameInput?.classList.add('is-invalid'); if (nameErr) { nameErr.textContent = 'Full name is required.'; nameErr.style.display = 'block'; } hasError = true; }
            if (!email) { emailInput?.classList.add('is-invalid'); if (emailErr) { emailErr.textContent = 'Email is required.'; emailErr.style.display = 'block'; } hasError = true; }
            else if (!validateEmail(email)) { emailInput?.classList.add('is-invalid'); if (emailErr) { emailErr.textContent = 'Please enter a valid email address.'; emailErr.style.display = 'block'; } hasError = true; }
            if (!mobile || mobile.length < 8) { mobileInput?.classList.add('is-invalid'); if (mobileErr) { mobileErr.textContent = 'Valid phone number is required.'; mobileErr.style.display = 'block'; } hasError = true; }

            if (termsCheckbox && !termsCheckbox.checked) {
                let termsErrEl = document.getElementById('terms-error-' + id);
                if (!termsErrEl) {
                    termsErrEl = document.createElement('div');
                    termsErrEl.id = 'terms-error-' + id;
                    termsErrEl.className = 'text-danger small mt-1';
                    termsCheckbox.closest('.form-check')?.after(termsErrEl);
                }
                termsErrEl.textContent = 'Please agree to the terms and conditions.';
                termsErrEl.style.display = 'block';
                hasError = true;
            } else {
                const termsErrEl = document.getElementById('terms-error-' + id);
                if (termsErrEl) { termsErrEl.textContent = ''; termsErrEl.style.display = 'none'; }
            }

            if (hasError) return;

            if (walletErrors) walletErrors.textContent = 'Checking...';
            walletOverlay.style.pointerEvents = 'none';

            try {
                const [emailResult, mobileResult] = await Promise.all([checkEmailExists(email), checkMobileExists(mobile)]);
                walletOverlay.style.pointerEvents = 'auto';
                if (emailResult.exists) { emailInput?.classList.add('is-invalid'); if (emailErr) { emailErr.textContent = emailResult.message || 'This email is already registered.'; emailErr.style.display = 'block'; } if (walletErrors) walletErrors.textContent = ''; return; }
                if (mobileResult.exists) { mobileInput?.classList.add('is-invalid'); if (mobileErr) { mobileErr.textContent = mobileResult.message || 'This mobile number is already registered.'; mobileErr.style.display = 'block'; } if (walletErrors) walletErrors.textContent = ''; return; }
            } catch(err) {
                walletOverlay.style.pointerEvents = 'auto';
                console.error('Wallet AJAX error:', err);
            }

            if (walletErrors) walletErrors.textContent = '';
            walletOverlay.style.display = 'none';
            const prContainer = document.getElementById('payment-request-button-container-' + id);
            if (prContainer) {
                const btn = prContainer.querySelector('button, [role="button"], iframe');
                if (btn) btn.click();
            }
        });
    }

    if (termsCheckbox) termsCheckbox.addEventListener('change', function() {
        if (walletErrors) walletErrors.textContent = '';
        const termsErrEl = document.getElementById('terms-error-' + id);
        if (termsErrEl) { termsErrEl.textContent = ''; termsErrEl.style.display = 'none'; }
    });

    /* --- Plan Helpers --- */
    function getSelectedPlan() {
        let planId = null, name = '', priceCents = 0;
        planRadios.forEach(radio => {
            if (!radio.checked) return;
            planId = radio.value;
            const label = form.querySelector(`label[for="${radio.id}"]`);
            if (!label) return;
            const priceEl = label.querySelector('.price');
            const nameEl  = label.querySelector('.package-name');
            if (nameEl) name = nameEl.textContent.trim().split('-')[0].trim();
            if (priceEl) priceCents = Math.round(parseFloat(priceEl.textContent.replace(/[$,]/g, '')) * 100);
        });
        return { planId, name, priceCents };
    }

    function setHiddenInputs(planId, paymentMethodId) {
        const pm   = document.getElementById('payment-method-id-' + id);
        const plan = document.getElementById('pr_selected_plan_id_hidden-' + id);
        if (pm)   pm.value   = paymentMethodId;
        if (plan) plan.value = planId;
    }

    function showFieldError(input, errorEl, message) { if (input) input.classList.add('is-invalid'); if (errorEl) { errorEl.textContent = message; errorEl.style.display = 'block'; } }
    function clearFieldError(input, errorEl) { if (input) input.classList.remove('is-invalid'); if (errorEl) { errorEl.textContent = ''; errorEl.style.display = 'none'; } }

    async function validateEmailAndMobile() {
        let isValid = true;
        const email  = emailInput?.value?.trim();
        const mobile = mobileInput?.value?.trim();
        clearFieldError(emailInput, emailError);
        clearFieldError(mobileInput, mobileError);
        if (!email || !validateEmail(email)) { showFieldError(emailInput, emailError, 'Please enter a valid email address.'); isValid = false; }
        if (!mobile || mobile.length < 8)   { showFieldError(mobileInput, mobileError, 'Please enter a valid mobile number.'); isValid = false; }
        if (!isValid) return false;
        const spinner = createSpinner();
        if (cardButton) { cardButton.disabled = true; cardButton.textContent = 'Checking...'; cardButton.appendChild(spinner); }
        const [emailResult, mobileResult] = await Promise.all([checkEmailExists(email), checkMobileExists(mobile)]);
        if (cardButton) { cardButton.disabled = false; cardButton.textContent = 'Submit'; }
        if (emailResult.exists)  { showFieldError(emailInput, emailError, emailResult.message || 'This email is already registered.'); isValid = false; }
        if (mobileResult.exists) { showFieldError(mobileInput, mobileError, mobileResult.message || 'This mobile number is already registered.'); isValid = false; }
        return isValid;
    }

    form.addEventListener('submit', async e => {
        if (form.dataset.wallet === 'true') return;
        e.preventDefault();
        if (termsCheckbox && !termsCheckbox.checked) { if (cardErrors) cardErrors.textContent = 'Please agree to the terms and conditions.'; return; }
        const { planId } = getSelectedPlan();
        if (!planId) { if (cardErrors) cardErrors.textContent = 'Please select a plan'; return; }
        const isValid = await validateEmailAndMobile();
        if (!isValid) return;
        if (cardButton) { cardButton.disabled = true; cardButton.textContent = 'Processing...'; }
        const { paymentMethod, error } = await stripe.createPaymentMethod({ type: 'card', card });
        if (error) { if (cardErrors) cardErrors.textContent = error.message; if (cardButton) { cardButton.disabled = false; cardButton.textContent = 'Submit'; } return; }
        setHiddenInputs(planId, paymentMethod.id);
        form.submit();
    });

    let paymentRequest;
    function initPaymentRequest() {
        const plan      = getSelectedPlan();
        const container = document.getElementById('payment-request-button-container-' + id);
        if (!container || !plan.planId || plan.priceCents <= 0) { if (container) container.style.display = 'none'; return; }
        if (paymentRequest) { paymentRequest.update({ total: { label: plan.name, amount: plan.priceCents } }); return; }
        paymentRequest = stripe.paymentRequest({ country: 'AU', currency: 'aud', total: { label: plan.name, amount: plan.priceCents }, requestPayerName: true, requestPayerEmail: true });
        const prButton = elements.create('paymentRequestButton', { paymentRequest });
        paymentRequest.canMakePayment().then(result => {
            if (result) { prButton.mount(container); container.style.display = 'block'; toggleWalletOverlay(); }
            else container.style.display = 'none';
        });
        paymentRequest.on('paymentmethod', async ev => {
            if (walletErrors) walletErrors.textContent = '';
            const isValid = await validateEmailAndMobile();
            if (!isValid) { ev.complete('fail'); return; }
            setHiddenInputs(plan.planId, ev.paymentMethod.id);
            form.dataset.wallet = 'true';
            ev.complete('success');
            form.submit();
        });
    }

    initPaymentRequest();
    planRadios.forEach(r => r.addEventListener('change', initPaymentRequest));
    console.log('Stripe initialized for form: ' + id);
}
</script>