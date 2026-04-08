@extends('layouts.businessapp')
@section('content')
<div class="container-fluid mt-4">
	<div class="row justify-content-center">
		<div class="col-md-12">
			<div class="card shadow">
				<div class="card-header fw-bold">{{ __('Add an Offer for Your Business') }}</div>
				<div class="card-body">
					@if (session('status'))
					<div class="alert alert-success alert-dismissible fade show" role="alert">
						{{ session('status') }}
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
					@endif

					@if ($errors->any())
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<ul class="mb-0">
							@foreach ($errors->all() as $error)
							<li>{{ $error }}</li>
							@endforeach
						</ul>
						<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
					</div>
					@endif

					<div class="mb-3 text-end">
						<a href="{{ route('business.offers.index') }}" class="btn btn-primary btn-sm">
							<i class="bi bi-arrow-left"></i> Back to Offers
						</a>
					</div>

					<form action="{{ route('business.offers.store') }}"  method="POST" enctype="multipart/form-data">
						@csrf
						<div class="mb-3">
							<label for="title" class="form-label fw-semibold">Offer Title </label>
							<input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
							@error('title')
							<div class="invalid-feedback d-block">{{ $message }}</div>
							@enderror
						</div>
						<fieldset class="border rounded p-3 mb-3">
							<legend class="float-none w-auto px-2 fs-6 fw-semibold">If redemption by entering a promo code in your website checkout process</legend>
							<div id="custom_redemption_process_wrap">
								<div class="mb-3">
									<label for="custom_redemption_code" class="form-label fw-semibold">Custom Promo code?</label>
									<input type="text" class="form-control @error('custom_redemption_code') is-invalid @enderror" id="custom_redemption_code" name="custom_redemption_code" value="{{ old('custom_redemption_code') }}">
									@error('custom_redemption_code')
									<div class="invalid-feedback d-block">{{ $message }}</div>
									@enderror
								</div>
								<div class="mb-3">
									<label for="custom_redemption_url" class="form-label fw-semibold">Custom URL (optional):</label>
									<input type="url" class="form-control @error('custom_redemption_url') is-invalid @enderror" id="custom_redemption_url" name="custom_redemption_url" value="{{ old('custom_redemption_url') }}">
									@error('custom_redemption_url')
									<div class="invalid-feedback d-block">{{ $message }}</div>
									@enderror
								</div>
								<div class="mb-3">
									<label for="custom_redemption_description" class="form-label fw-semibold">Custom Description / Instructions (optional):</label>
									<textarea class="form-control @error('custom_redemption_description') is-invalid @enderror" id="custom_redemption_description" name="custom_redemption_description" rows="3">{{ old('custom_redemption_description') }}</textarea>
									@error('custom_redemption_description')
									<div class="invalid-feedback d-block">{{ $message }}</div>
									@enderror
								</div>
							</div>
						</fieldset>

						<div class="mb-3">
							<label for="redemption_limit" class="form-label fw-semibold">Maximum number of redemptions per Xhale Subscriber:</label>
							<input type="number" min="0" step="1" class="form-control @error('redemption_limit') is-invalid @enderror" id="redemption_limit" name="redemption_limit" value="{{ old('redemption_limit', 0) }}">
							<p class="form-text text-muted">0 = unlimited</p>
							@error('redemption_limit')
							<div class="invalid-feedback d-block">{{ $message }}</div>
							@enderror
						</div>

						{{-- Fixed Price, Percentage or Custom Offer? Fieldset --}}
						<fieldset class="border rounded p-3 mb-3">
							<legend class="float-none w-auto px-2 fs-6 fw-semibold">Fixed Price, Percentage or Custom Offer?</legend>
							<div class="row">
								<div class="col-md-4 mb-3">
									<label for="full_price" class="form-label fw-semibold">Full Price:</label>
									<div class="input-group">
										<span class="input-group-text">$</span>
										<input type="number" step="0.01" class="form-control @error('full_price') is-invalid @enderror" id="full_price" name="full_price" value="{{ old('full_price', 0.00) }}">
										@error('full_price')
										<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
								<div class="col-md-4 mb-3">
									<label for="discount_price" class="form-label fw-semibold">Discounted Price:</label>
									<div class="input-group">
										<span class="input-group-text">$</span>
										<input type="number" step="0.01" class="form-control @error('discount_price') is-invalid @enderror" id="discount_price" name="discount_price" value="{{ old('discount_price', 0.00) }}">
										@error('discount_price')
										<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
								<div class="col-md-4 mb-3">
									<label for="percentage_off" class="form-label fw-semibold">Percentage Off:</label>
									<div class="input-group">
										<input type="number" step="0.01" class="form-control @error('percentage_off') is-invalid @enderror" id="percentage_off" name="percentage_off" value="{{ old('percentage_off') }}">
										<span class="input-group-text">%</span>
										@error('percentage_off')
										<div class="invalid-feedback">{{ $message }}</div>
										@enderror
									</div>
								</div>
							</div>
							<div class="mb-3">
								<label for="custom_saving" class="form-label fw-semibold">Custom Saving Text:</label>
								<input type="text" class="form-control @error('custom_saving') is-invalid @enderror" id="custom_saving" name="custom_saving" value="{{ old('custom_saving') }}">
								<p class="form-text text-muted">Max 128 characters</p>
								@error('custom_saving')
								<div class="invalid-feedback">{{ $message }}</div>
								@enderror
							</div>
						</fieldset>

						<div class="mb-3">
							<label for="category_ids" class="form-label fw-semibold">Offer Category:</label>
							<select class="form-control select2 @error('category_ids') is-invalid @enderror" id="category_ids" name="category_ids[]" multiple="multiple">
								@foreach ($categories as $category)
								<option value="{{ $category->id }}" {{ in_array($category->id, old('category_ids', [])) ? 'selected' : '' }}>
									{{ $category->name }}
								</option>
								@endforeach
							</select>
							@error('category_ids')
							<div class="invalid-feedback d-block">{{ $message }}</div>
							@enderror
						</div>

						<div class="mb-3">
							<label for="description" class="form-label fw-semibold">Description </label>
							<textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4" >{{ old('description') }}</textarea>
							@error('description')
							<div class="invalid-feedback d-block">{{ $message }}</div>
							@enderror
						</div>

						<div class="mb-3">
							<label for="terms_conditions" class="form-label fw-semibold">Terms & Conditions </label>
							<textarea class="form-control @error('terms_conditions') is-invalid @enderror" id="terms_conditions" name="terms_conditions" rows="4" >{{ old('terms_conditions') }}</textarea>
							@error('terms_conditions')
							<div class="invalid-feedback d-block">{{ $message }}</div>
							@enderror
						</div>

					

						<div class="mb-3">
							<label for="organisation_id" class="form-label fw-semibold">Business </label>
							<div class="input-group">
								<select class="form-select @error('organisation_id') is-invalid @enderror" id="organisation_id" name="organisation_id" required>
									@foreach ($organisations as $key => $organisation)
									<option value="{{ $organisation->id }}" {{ old('organisation_id') == $organisation->id? 'selected' : '' }} data-address="{{ $organisation->full_address }}">{{ $organisation->title }}</option>
									@endforeach
								</select>

								 @if (Auth::user()->isBusinessAccount())
								<span class="input-group-text">
									<a href="{{ route('business.organisations.create')}}" class="btn btn-primary btn-sm" target="_blank">
										  Add New Organisation <i class="bi bi-arrow-right"></i>
                        
									</a>
								</span>
								  @endif
							</div>

							@error('organisation_id')
							<div class="invalid-feedback d-block">{{ $message }}</div>
							@enderror
						</div>



						<div class="mb-3">
							<fieldset class="border rounded p-3">
								<legend class="float-none w-auto px-2">Location Type</legend>
								{{-- Location Type Radio Buttons - NOW DYNAMIC --}}
								<div class="mb-3 @error('offer_address_type') is-invalid @enderror">
									<div class="d-flex flex-wrap gap-4"> 
									@foreach (\App\Enums\OfferAddressType::labels() as $value => $label)
									<div class="form-check">
										<input class="form-check-input address-type-radio" type="radio" name="offer_address_type" id="{{ $value }}" value="{{ $value }}"
										{{ old('offer_address_type', $offer->offer_address_type ?? \App\Enums\OfferAddressType::ONLINE) == $value ? 'checked' : '' }}>
										<label class="form-check-label" for="{{ $value }}">
											{{ $label }}
										</label>
									</div>
									@endforeach
								</div>

									@error('offer_address_type')
									<div class="invalid-feedback d-block">
										{{ $message }}
									</div>
									@enderror
								</div>

								<div id="physicalAddressFields" > 
									<div class="form-group mb-3">
										<label for="address_search_input" class="form-label">Address:</label>
										<input type="text" name="full_address" id="address_search_input"
										class="form-control @error('full_address') is-invalid @enderror"
										value="{{ old('full_address', $offer->full_address ?? '') }}"
										placeholder="Start typing an address...">
										<div id="address_lookup_status" class="mt-2"></div>
										@error('full_address')
										<div class="invalid-feedback d-block">{{ $message }}</div>
										@enderror
									</div>
								</div>
							</fieldset>
						</div>


						<div class="mb-3">
							<label for="start_date" class="form-label fw-semibold">Start Date </label>
							<input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date') }}" >
							@error('start_date')
							<div class="invalid-feedback d-block">{{ $message }}</div>
							@enderror
						</div>

						<div class="form-check form-switch mb-4" id="no_end_date_wrapper">
                            <input class="form-check-input exclude-switch" type="checkbox"
                            id="no_end_date"
                            name="no_end_date"
                            value="1">
                            <label class="form-check-label fw-semibold" for="no_end_date">
                                No End Date
                            </label>
                        </div>


						<div class="mb-3" id="end_date_wrapper">
							<label for="end_date" class="form-label fw-semibold">End Date </label>
							<input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date') }}" >
							@error('end_date')
							<div class="invalid-feedback d-block">{{ $message }}</div>
							@enderror
						</div>

						
						<div class="mb-3">
							<label for="is_featured" class="form-label fw-semibold">Is Featured?</label>
							<select class="form-select @error('is_featured') is-invalid @enderror" id="is_featured" name="is_featured">
								@foreach (\App\Enums\YesNo::labels() as $value => $label)
								<option value="{{ $value }}" {{ old('is_featured') == $value ? 'selected' : '' }}>
									{{ $label }}
								</option>
								@endforeach
							</select>
							@error('is_featured')
							<div class="invalid-feedback d-block">{{ $message }}</div>
							@enderror
						</div>



						<div class="mb-3">
							<label for="is_paid_advertisement" class="form-label fw-semibold">Is Paid Advertisement?</label>
							<select class="form-select @error('is_paid_advertisement') is-invalid @enderror" id="is_paid_advertisement" name="is_paid_advertisement">
								@foreach (\App\Enums\YesNo::labels() as $value => $label)
								<option value="{{ $value }}" {{ old('is_paid_advertisement') == $value ? 'selected' : '' }}>
									{{ $label }}
								</option>
								@endforeach
							</select>
							@error('is_paid_advertisement')
							<div class="invalid-feedback d-block">{{ $message }}</div>
							@enderror
						</div>


						<div class="mb-3">
							<label for="offer_ongoing_subscription" class="form-label fw-semibold">Is this offer part of an ongoing subscription?</label>
							<select class="form-select @error('offer_ongoing_subscription') is-invalid @enderror" id="offer_ongoing_subscription" name="offer_ongoing_subscription">
								@foreach (\App\Enums\YesNo::labels() as $value => $label)
								<option value="{{ $value }}" {{ old('offer_ongoing_subscription') == $value ? 'selected' : '' }}>
									{{ $label }}
								</option>
								@endforeach
							</select>
							@error('offer_ongoing_subscription')
							<div class="invalid-feedback d-block">{{ $message }}</div>
							@enderror
						</div>



						<div id="featuredOptions">
							<div class="form-group mb-3">
								<label for="bannerImage" class="form-label">Banner Image</label>
								<input type="file" name="banner_image" id="bannerImage" class="form-control">
							</div>
							<div class="form-group mb-3">
								<label for="offerUrl" class="form-label">URL to Offer Page</label>
								<input type="url" name="offer_url" id="offerUrl" class="form-control" placeholder="https://example.com/offer">
							</div>
						</div>



						<div class="mb-3">
                            <label for="image_files" class="form-label fw-semibold">Offer Images</label>
                            <input type="file" name="image_files[]" id="image_files" class="form-control @error('image_files') is-invalid @enderror @error('image_files.*') is-invalid @enderror" multiple>
                            @error('image_files')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            @error('image_files.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
               
                        </div>




						<button type="submit" class="btn btn-primary">
							<i class="bi bi-save"></i> Save Offer
						</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
