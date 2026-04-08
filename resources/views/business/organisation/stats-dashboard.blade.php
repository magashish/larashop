 {{-- Subscribers Card --}}
 <div class="col-md-4 mb-3"> {{-- Added mb-3 for vertical spacing on smaller screens --}}
    <div class="card h-100 bg-light border-0 shadow-sm"> {{-- Used Bootstrap 5 card and h-100 for equal height --}}
        <div class="card-body d-flex flex-column">
            <h5 class="card-title text-muted fw-bold">Subscribers</h5>
            <div class="flex-grow-1 d-flex flex-column justify-content-center">
                <div class="mb-3">
                    <div class="display-4 fw-bold">-</div> {{-- Increased font size for key stat --}}
                    <p class="text-muted mb-0">Total Distinct Customers</p>
                </div>
            </div>
            <div class="row mt-auto pt-2 border-top"> {{-- Added border-top and mt-auto to push to bottom --}}
                <div class="col-6">
                    <div class="text-center">
                        <div class="fw-bold">-</div>
                        <small class="text-muted">Number of Favourites</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center">
                        <div class="fw-bold">-</div>
                        <small class="text-muted">Number of Reviews</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Offers Card --}}
<div class="col-md-4 mb-3">
    <div class="card h-100 bg-light border-0 shadow-sm">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title text-muted fw-bold">Offers</h5>
            <div class="flex-grow-1 d-flex flex-column justify-content-center">
                <div class="mb-3">
                    <div class="display-4 fw-bold">-</div>
                    <p class="text-muted mb-0">Number of Redemptions</p>
                </div>
            </div>
            <div class="row mt-auto pt-2 border-top">
                <div class="col-6">
                    <div class="text-center">
                        <div class="fw-bold">-</div>
                        <small class="text-muted">Active Offers</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center">
                        <div class="fw-bold">-</div>
                        <small class="text-muted">Number of Visits</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- My Business Card --}}
<div class="col-md-4 mb-3">
    <div class="card h-100 bg-light border-0 shadow-sm">
        <div class="card-body d-flex flex-column">
            <h5 class="card-title text-muted fw-bold">My Business</h5>
            <div class="flex-grow-1 d-flex flex-column justify-content-center">
                <div class="mb-3">
                    <div class="display-4 fw-bold">-</div>
                    <p class="text-muted mb-0">Total Value Generated</p>
                </div>
            </div>
            <div class="row mt-auto pt-2 border-top">
                <div class="col-6">
                    <div class="text-center">
                        <div class="fw-bold">-</div>
                        <small class="text-muted">Offer Rating</small>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center">
                        <div class="fw-bold">-</div>
                        <small class="text-muted">Business Rating</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                    {{-- End Stats Dashboard Section --}}