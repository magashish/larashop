@if(auth()->check() && !auth()->user()->terms_accepted)
<div class="modal fade" id="termsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content terms-modal">
            <div class="terms-header">
                <button type="button" class="terms-close" data-bs-dismiss="modal">✕</button>
                <div class="terms-icon">📋</div>
                <h4>UPDATED TERMS</h4>
                <div class="terms-divider"></div>
            </div>
            <div class="terms-body">
                <p class="terms-text">
                    Please review and accept our updated
                    <a href="{{ route('supportandlegal') }}#terms-conditions" target="_blank">Terms & Conditions</a>
                    to continue using the platform.
                </p>

                <form method="POST" action="{{ route('accept.terms') }}">
                    @csrf
                    <div class="terms-checkbox">
                        <input type="checkbox" id="acceptTerms" required>
                        <label for="acceptTerms">
                            I agree that I have read our
                            <a href="{{ route('supportandlegal') }}#terms-conditions" target="_blank">
                                terms and conditions & I am over 18 years of age
                            </a>
                        </label>
                    </div>
                    <button type="submit" class="terms-btn">
                        CONTINUE
                    </button>
                </form>
            </div>

        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    var myModal = new bootstrap.Modal(document.getElementById('termsModal'), {
        backdrop: 'static',
        keyboard: false
    });
    myModal.show();
});
</script>
@endif