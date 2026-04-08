@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Prize Draws / Create') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success alert-dismissible fade show">
                            {{ session('status') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="mb-3 text-end">
                        <a href="{{ route('prize_draws.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Prize Draws Admin
                        </a>
                    </div>

                    <form action="{{ route('prize_draws.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        {{-- Prize Draw Name --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Prize Draw Name</label>
                            <input type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   name="title"
                                   value="{{ old('title') }}"
                                   required>
                        </div>

                        {{-- Prize Type --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Prize Type</label>
                            <select class="form-control" name="prize_type" id="prize_type" required>
                                <option value="">Select Prize Type</option>
                                <option value="cash">Cash</option>
                                <option value="non_cash">Non-Cash</option>
                            </select>
                        </div>

                        {{-- Cash Amount --}}
                        <div class="mb-3" id="cash_field" style="display:none;">
                            <label class="form-label fw-semibold">Cash Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       step="0.01"
                                       min="0"
                                       class="form-control"
                                       name="cash_amount">
                            </div>
                        </div>

                        {{-- Non-Cash Prize Title --}}

                         <div class="mb-3" id="non_cash_field" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Prize Title</label>
                            <input type="text"
                                   class="form-control"
                                   name="prize_title"
                                   placeholder="e.g. Mazda CX-5">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Prize Sub Title</label>
                            <input type="text"
                                   class="form-control"
                                   name="prize_sub_title"
                                   placeholder="e.g. Mazda CX-5">
                        </div>

                        <div class="mb-3">

                             <label class="form-label fw-semibold"> Non-Cash Prize Value ($)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       step="0.01"
                                       min="0"
                                       class="form-control"
                                       name="prize_value_amount">
                            </div>
                        </div>
                    </div>



                        {{-- Prize Description (FOR BOTH) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Prize Description</label>
                            <textarea class="form-control"
                                      name="prize_description"
                                      rows="3"
                                      required
                                      placeholder="Describe the prize..."></textarea>
                        </div>

                        {{-- Prize Image (FOR BOTH) --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Prize Image</label>
                            <input type="file"
                                   class="form-control"
                                   name="prize_image"
                                   accept="image/*"
                                   required>
                            <small class="text-muted">
                                Used on homepage & winner reveal
                            </small>
                        </div>

                        {{-- Draw Type --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Draw Type</label>
                            <select class="form-control" name="draw_type" required>
                                <option value="">Select Draw Type</option>
                                <option value="subscribers">Subscribers (Small Giveaway)</option>
                                <option value="packages">Packages (Major Giveaway)</option>
                            </select>
                        </div>

                        {{-- Draw Date --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Draw Date</label>
                            <input type="text"
                                   class="form-control datetimepicker"
                                   name="draw_date"
                                   required>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input exclude-switch" type="checkbox"
                            id="show_on_site"
                            name="show_on_site"
                            value="1">
                            <label class="form-check-label fw-semibold" for="show_on_site">
                                Show on site 
                            </label>
                        </div>


                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Prize Draw
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<style>
.input-group-text {
    background-color: #f8f9fa;
    border-right: none;
}
.input-group .form-control {
    border-left: none;
}
.input-group .form-control:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
</style>

{{-- jQuery --}}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
$(document).ready(function () {

    function togglePrizeFields(type) {
        $('#cash_field').hide();
        $('#non_cash_field').hide();

        if (type === 'cash') {
            $('#cash_field').show();
        }

        if (type === 'non_cash') {
            $('#non_cash_field').show();
        }
    }

    togglePrizeFields($('#prize_type').val());

    $('#prize_type').on('change', function () {
        togglePrizeFields(this.value);
    });
});
</script>
@endsection
