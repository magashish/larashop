@extends('layouts.app') {{-- Adjust this to your main layout file --}}

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Subscription Plans / Edit') }}</div>
                <div class="card-body">
                    <div class="mb-3 text-end">
                        <a href="{{ route('plans.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Subscription Plans 
                        </a>
                    </div>
                    <form action="{{ route('plans.update', $plan->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- Use PUT method for updates --}}

                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Plan Name:</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $plan->name) }}" required>
                            @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>


                        {{-- New: Type Selection Field --}}
                        <div class="mb-3">
                            <label for="type" class="form-label fw-semibold">Offering Type:</label>
                            <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                <option value="">Select a type</option>
                                {{-- The $types array will be passed from your controller --}}
                                @foreach ($types as $value => $label)
                                <option value="{{ $value }}" {{ old('type', $plan->type) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        
                        <div id="subscription-fields" style="display:none;">
                            <div class="mb-3">
                                <label for="stripe_plan" class="form-label fw-semibold">Stripe Price ID:</label>
                                <input type="text" class="form-control @error('stripe_plan') is-invalid @enderror" id="stripe_plan" name="stripe_plan" value="{{ old('stripe_plan', $plan->stripe_plan) }}" required>
                                <small class="form-text text-muted">The unique ID of the Price in Stripe (e.g., price_XXXXXXXXXXXXXX).</small>
                                @error('stripe_plan')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="paypal_plan_id" class="form-label fw-semibold">Paypal Plan ID:</label>
                                <input type="text" class="form-control @error('paypal_plan_id') is-invalid @enderror" id="paypal_plan_id" name="paypal_plan_id" value="{{ old('paypal_plan_id', $plan->paypal_plan_id) }}" required>
                                <small class="form-text text-muted">The unique ID of the Price in Paypal (e.g., P-XXXXXXXXXXXXXX).</small>
                                @error('paypal_plan_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                        </div>

                        <div id="package-fields" style="display:none;">
                            {{-- <div class="mb-3">
                                <label for="entries_into_draw" class="form-label fw-semibold">Entries Into Draw:</label>
                                <input id="entries_into_draw" name="entries_into_draw" type="number"
                                class="form-control @error('entries_into_draw') is-invalid @enderror"
                                value="{{ old('entries_into_draw', $plan->entries_into_draw) }}" placeholder="Enter number of entries" min="1">
                                @error('entries_into_draw')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div> --}}

                            <div class="mb-3">
                                <label for="access_duration_weeks" class="form-label fw-semibold">Access Duration (Weeks):</label>
                                <input id="access_duration_weeks" name="access_duration_weeks" type="number"
                                class="form-control @error('access_duration_weeks') is-invalid @enderror"
                                value="{{ old('access_duration_weeks', $plan->access_duration_weeks) }}" placeholder="Enter access duration in weeks" min="1">
                                @error('access_duration_weeks')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="price" class="form-label fw-semibold">Price (in cents):</label>
                            <input type="number" 
                            class="form-control @error('price') is-invalid @enderror" 
                            id="price" 
                            name="price" 
                            value="{{ old('price', number_format($plan->price, 2, '.', '')) }}" 
                            required 
                            min="0" 
                            step="0.01">
                            <small class="form-text text-muted">Enter the price in cents (e.g., 500 for $5.00).</small>
                            @error('price')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>



                        <div class="mb-3">
                            <label for="price_description" class="form-label fw-semibold">Price Description:</label>
                            <input type="text" class="form-control @error('price_description') is-invalid @enderror" id="price_description" name="price_description" value="{{ old('price_description', $plan->price_description) }}">
                            @error('price_description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input exclude-switch" type="checkbox"
                            id="is_public"
                            name="is_public"
                            value="1"
                            {{ old('is_public', $plan->is_public) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="is_public">
                                Show in Frontend 
                            </label>
                        </div>

                        

                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description:</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $plan->description) }}</textarea>
                            @error('description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                         <div class="mb-3">
                            <label for="small_description" class="form-label fw-semibold">Small Mobile Description::</label>
                            <textarea class="form-control @error('small_description') is-invalid @enderror" id="small_description" name="small_description" rows="3">{{ old('small_description', $plan->small_description) }}</textarea>
                            @error('small_description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="read_more" class="form-label fw-semibold">Read More Description:</label>
                            <textarea class="form-control @error('read_more') is-invalid @enderror" id="read_more" name="read_more" rows="3">{{ old('read_more', $plan->read_more) }}</textarea>
                            @error('read_more')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                         <div class="mb-3">
                            <label for="sub_description" class="form-label fw-semibold">Sub Description:</label>
                            <textarea class="form-control @error('sub_description') is-invalid @enderror" id="sub_description" name="sub_description" rows="3">{{ old('sub_description', $plan->sub_description) }}</textarea>
                            @error('sub_description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>


                        <div class="mb-3">
                            <label for="payment_description" class="form-label fw-semibold">Payment Description :</label>
                            <textarea class="form-control @error('read_more') is-invalid @enderror" id="payment_description" name="payment_description" rows="3">{{ old('payment_description', $plan->payment_description) }}</textarea>
                            @error('payment_description')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>


                         <div class="mb-3">
                            <label for="image" class="form-label fw-semibold">Plan Image</label>
                            <input type="file" name="image" id="image"
                            class="form-control @error('image') is-invalid @enderror">
                            @error('image')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                            @if ($plan->image)
                            <div class="mt-3 position-relative plan-image d-inline-block"> 
                                <img src="{{ Storage::url($plan->image) }}" alt="Current Image" class="img-thumbnail" style="max-width: 200px; height: auto;">

                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 start-100 translate-middle rounded-circle p-0"
                                style="width: 24px; height: 24px; line-height: 1; font-size: 1rem;"
                                id="removeImageBtn" title="Remove image">
                                <i class="bi bi-x-circle-fill"></i>
                            </button>
                            <input type="checkbox" name="remove_image" id="remove_image_hidden" value="1" class="d-none">
                            <label class="d-none" for="remove_image_hidden">Remove current image</label>
                        </div>
                        @endif
                    </div>


                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Update Plan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 
<script>
    $(document).ready(function() {
        const $typeSelect = $('#type');
        const $subscriptionFields = $('#subscription-fields');
        const $packageFields = $('#package-fields');
        function toggleFields() {
            $subscriptionFields.hide();
            $packageFields.hide();
            $('#stripe_plan').removeAttr('required');
           // $('#entries_into_draw').removeAttr('required');
            $('#access_duration_weeks').removeAttr('required');
            if ($typeSelect.val() === 'subscription') {
                $subscriptionFields.show();
                $('#stripe_plan').attr('required', 'required');
            } else if ($typeSelect.val() === 'package') {
                $packageFields.show();
             //  $('#entries_into_draw').attr('required', 'required');
                $('#access_duration_weeks').attr('required', 'required');
            }
        }
        $typeSelect.on('change', toggleFields);
        toggleFields();
        $typeSelect.on('change', function() {
            if (this.value === 'subscription') {
             //   $('#entries_into_draw').val('');
                $('#access_duration_weeks').val('');
            } else if (this.value === 'package') {
                $('#stripe_plan').val('');
            }
        });
    });
</script>


<script>
    $(document).ready(function () {
        const $removeImageBtn = $('#removeImageBtn');
        const $removeImageHiddenCheckbox = $('#remove_image_hidden');
        const $currentImageDisplay = $removeImageBtn.closest('.plan-image');
        if ($removeImageBtn.length && $removeImageHiddenCheckbox.length && $currentImageDisplay.length) {
            $removeImageBtn.on('click', function () {
                if (confirm('Are you sure you want to remove this image?')) {
                    $removeImageHiddenCheckbox.prop('checked', true); 
                    $currentImageDisplay.hide(); 
                    $currentImageDisplay.removeClass('d-inline-block');
                    $('#image').val('');
                }
            });
        }
    });
</script>
@endsection
