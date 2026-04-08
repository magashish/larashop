<div class="modal upsellModal fade" id="upsellModal" tabindex="-1" aria-labelledby="upsellModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-0">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
            <div class="modal-body p-0">
                <div class="row mx-0">
                    <div class="col-sm-6 px-0">
                        <div class="img-wrapper position-relative d-flex align-items-end h-100">
                            <div class="mob-logo mx-auto py-3 d-sm-none d-block">
                                <img src="{{ asset('images/logo-full-black.svg') }}" class="img-fluid">
                            </div>
                            <img src="{{ asset('images/Lady.png') }}" class="modal-girl-img img-fluid">
                        </div>
                    </div>
                    <div class="col-sm-6 px-0">
                        <div class="content-wrapper position-relative text-center h-100">
                            <h4 class="modal-title">Congratulations</h4>
                            @php
                            $discountedPrice = $package_plan->price * 0.50;
                            @endphp
                            <p> You don’t have any active package yet. Start today and get your first **{{ $package_plan->name }}** package! at an exclusive 50% discount  — add it now with one click.</p>
                            {{-- <p>Congratulations on your **{{ $package_plan->name }}** package! For today only, grab a second {{ $package_plan->name }} package at 50% off — add it now with one click.</p> --}}
                            <p class="fw-semibold">Original Price: ${{ number_format($package_plan->price, 2) }}</p>
                            <p class="fw-semibold">Your Special Price: **${{ number_format($discountedPrice, 2) }}** (50% off!)</p>

                            <form action="{{ route('subscription.purchase_upsell', ['plan' => $package_plan->id]) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success text-uppercase w-100">
                                    YES, PROCEED
                                </button>
                            </form>
                            <button type="button" class="btn btn-secondary text-uppercase w-100 mt-sm-3 mt-2" data-bs-dismiss="modal">No thanks, I don't want to save money</button>
                        </div>
                    </div>
                </div>                
            </div>
        </div>
    </div>
</div>