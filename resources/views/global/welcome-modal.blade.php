{{-- Welcome Modal HTML (placed just before closing </body>) --}}
    <div class="modal welcomeModal fade" id="welcomeModal" tabindex="-1" aria-labelledby="welcomeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
                <div class="modal-body p-0">
                    <div class="row mx-0">
                        <div class="col-sm-6 px-0">
                            <div class="welcome-wrapper h-100">
                                <div class="sec-head text-uppercase text-center">Welcome</div>

                                <div class="bottom-content pt-sm-0 pt-5">
                                    <div class="welcome-img">
                                        <div class="mob-logo">
                                            <img src="{{ asset('images/logo-full-black.svg') }}" class="img-fluid d-sm-none d-block">
                                        </div>
                                        <img src="{{ asset('images/Lady2.png') }}" class="d-sm-block d-none img-fluid">
                                        <img src="{{ asset('images/Lady.png') }}" class="d-sm-none d-block img-fluid">
                                    </div>
                                    <div class="sec-slider">
                                        <div class="welcome-slider">
                                            <div class="slider-item text-center">
                                                <h6 class="mb-2 text-uppercase">Welcome to Xhale</h6>
                                                <p class="mb-2">...you're steps away from accesing our exclusive discounts and a chance to WIN cash prizes!!!</p>
                                                <a href="#" class="d-sm-none d-block signup">Sign Up <span><i class="bi bi-arrow-right"></i></span></a>
                                            </div>
                                            <div class="slider-item text-center">
                                                <h6 class="mb-2 text-uppercase">Welcome to Xhale</h6>
                                                <p class="mb-2">...you're steps away from accesing our exclusive discounts and a chance to WIN cash prizes!!!</p>
                                                <a href="#" class="d-sm-none d-block signup">Sign Up <span><i class="bi bi-arrow-right"></i></span></a>
                                            </div>
                                            <div class="slider-item text-center">
                                                <h6 class="mb-2 text-uppercase">Welcome to Xhale</h6>
                                                <p class="mb-2">...you're steps away from accesing our exclusive discounts and a chance to WIN cash prizes!!!</p>
                                                <a href="#" class="d-sm-none d-block signup">Sign Up <span><i class="bi bi-arrow-right"></i></span></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 px-0 d-sm-block d-none">
                            <div class="welcome-content text-center h-100">
                                <div class="modal-logo text-center mb-5">
                                    <img src="{{ asset('images/logo-light.svg') }}" class="img-fluid mx-auto">
                                    <p class="mt-3 mb-0 text-white">Don't forget to breath...</p>
                                </div>
                                <div class="action-btns">
                                    <a href="{{ route('register') }}" class="d-block signup">Sign Up</a>
                                    <a href="{{ route('partners') }}" class="d-block login business-signup-btn my-3 text-white">Business Sign Up</a>
                                    <a href="{{ route('login') }}" class="d-block login">Log in</a>
                                    <a class="skip-text mt-3 mb-0 text-white text-uppercase text-white text-uppercase" id="skipWelcomeModal">Skip For Now</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- END Welcome Modal HTML --}}