@extends('layouts.businessapp')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Organisations / Edit') }}</div>
                <div class="card-body">

                 <div class="row mb-4"> 
                    {{-- @include('business.organisation.stats-dashboard') --}}

                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <div class="mb-3 text-end">
                        <a href="{{ route('business.organisations.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Organisations Admin
                        </a>
                    </div>
                    {{-- IMPORTANT: Keep enctype="multipart/form-data" for file uploads --}}
                    <form action="{{ route('business.organisations.update', $organisation->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') {{-- Required for update requests --}}

                        {{-- Title Field --}}
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" id="title"
                            placeholder="Enter Organisation Title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title', $organisation->title) }}" readonly>
                            @error('title')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        {{-- ABN Field --}}
                        <div class="mb-3">
                            <label for="abn" class="form-label fw-semibold">ABN</label>
                            <input type="text" name="abn" id="abn"
                            placeholder="Enter ABN (Optional)"
                            class="form-control @error('abn') is-invalid @enderror"
                            value="{{ old('abn', $organisation->abn) }}" readonly>

                            @error('abn')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="website" class="form-label fw-semibold">Website</label>
                            <input type="url" name="website" id="website"
                            placeholder="Enter Website url"
                            class="form-control @error('website') is-invalid @enderror"
                            value="{{ old('website', $organisation->website) }}">

                            @error('website')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        

                        <div class="mb-3">
                           <fieldset class="border rounded p-3">
                            <legend class="float-none w-auto px-2">Social Media Accounts</legend>
                            {{-- Instagram Field --}}
                            <div class="mb-3">
                                <label for="instagram" class="form-label">Instagram URL</label>
                                <input type="text" name="instagram" id="instagram"
                                class="form-control @error('instagram') is-invalid @enderror"
                                value="{{ old('instagram', $organisation->instagram) }}">
                                @error('instagram')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Facebook Field --}}
                            <div class="mb-3">
                                <label for="facebook" class="form-label">Facebook URL</label>
                                <input type="text" name="facebook" id="facebook"
                                class="form-control @error('facebook') is-invalid @enderror"
                                value="{{ old('facebook', $organisation->facebook) }}">
                                @error('facebook')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- YouTube Field --}}
                            <div class="mb-3">
                                <label for="youtube" class="form-label">YouTube URL</label>
                                <input type="text" name="youtube" id="youtube"
                                class="form-control @error('youtube') is-invalid @enderror"
                                value="{{ old('youtube', $organisation->youtube) }}">
                                @error('youtube')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- TikTok Field --}}
                            <div class="mb-3">
                                <label for="tiktok" class="form-label">TikTok URL</label>
                                <input type="text" name="tiktok" id="tiktok"
                                class="form-control @error('tiktok') is-invalid @enderror"
                                value="{{ old('tiktok', $organisation->tiktok) }}">
                                @error('tiktok')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            {{-- Twitter Field --}}
                            <div class="mb-3">
                                <label for="twitter" class="form-label">Twitter URL</label>
                                <input type="text" name="twitter" id="twitter"
                                class="form-control @error('twitter') is-invalid @enderror"
                                value="{{ old('twitter', $organisation->twitter) }}">
                                @error('twitter')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </fieldset>
                    </div>

                    <div class="mb-3">
                       <fieldset class="border rounded p-3">
                        <legend class="float-none w-auto px-2">Location Type</legend>
                        {{-- Location Type Radio Buttons - NOW DYNAMIC --}}
                        <div class="mb-3 @error('organisation_address_type') is-invalid @enderror">
                           <div class="d-flex flex-wrap gap-4"> 
                            @foreach (\App\Enums\OrganisationAddressType::labels() as $value => $label)
                            <div class="form-check">
                                <input class="form-check-input address-type-radio" id="{{ $value }}" type="radio" name="organisation_address_type" id="{{ $value }}" value="{{ $value }}"
                                {{ old('organisation_address_type', $organisation->organisation_address_type ?? \App\Enums\OrganisationAddressType::ONLINE) == $value ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $value }}">
                                    {{ $label }}
                                </label>
                            </div>
                            @endforeach
                        </div>

                        @error('organisation_address_type')
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
                            value="{{ old('full_address', $organisation->full_address ?? '') }}"
                            placeholder="Start typing an address...">
                            <div id="address_lookup_status" class="mt-2"></div>
                            @error('full_address')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </fieldset>
            </div>


            {{-- New Field: Image (Single File Upload) --}}
            <div class="mb-3">
                <label for="image" class="form-label fw-semibold">Organisation Image</label>
                <input type="file" name="image" id="image"
                class="form-control @error('image') is-invalid @enderror">
                @error('image')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
                @enderror

                {{-- Display existing image and add remove option --}}
                @if ($organisation->image)
                <div class="mt-3 position-relative organisation-image d-inline-block"> {{-- Added position-relative and d-inline-block --}}
                    <img src="{{ Storage::url($organisation->image) }}" alt="Current Image" class="img-thumbnail" style="max-width: 200px; height: auto;">

                    {{-- Cross icon to remove the current image --}}
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 start-100 translate-middle rounded-circle p-0"
                    style="width: 24px; height: 24px; line-height: 1; font-size: 1rem;"
                    id="removeImageBtn" title="Remove image">
                    <i class="bi bi-x-circle-fill"></i>
                </button>

                {{-- Hidden checkbox that gets checked when the cross icon is clicked --}}
                <input type="checkbox" name="remove_image" id="remove_image_hidden" value="1" class="d-none">

                {{-- Optional: Display a label for accessibility if the checkbox is hidden --}}
                <label class="d-none" for="remove_image_hidden">Remove current image</label>
            </div>
            @endif
        </div>

        <div class="mb-3">


            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="form-label fw-semibold mb-0">
                     Business Users
                </label>


                @if (Auth::user()->isBusinessAccount() )
                <div class="d-flex gap-2">
                    <!-- All Users link button -->
                    <a href="{{ route('business.users.index') }}"
                    class="btn btn-outline-secondary" target="_blank">
                    <i class="bi bi-people"></i> All Users
                </a>
                <!-- Add Users modal button -->
                <button
                type="button"
                class="btn btn-primary"
                id="add_organisation_users"
                data-bs-toggle="modal"
                data-bs-target="#addUsersModal"
                >
                <i class="bi bi-plus"></i> Add Users
            </button>
        </div>
        @endif 
    </div>




                {{-- <div class="input-group">
                    <input id="organisation_search_input" type="text"
                    class="form-control"
                    placeholder="Type to search Business Users ...">
                    <span class="input-group-text" id="search_organisation_btn">
                        <i class="bi bi-search"></i>
                    </span>
                </div>
                <div id="organisation_search_results" class="list-group mt-2"></div> --}}

                @if (!Auth::user()->isBusinessAccount())
                <style>
                    .start-date, .end-date, .roles-column { display:none; }
                </style>
                @endif


                <div id="organisation_all_results" class="list-group-test">
                    <table id="user_organisation_table" class="table table-striped table-hover" >
                        <thead class="tableFloatingHeaderOriginal">
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th class="start-date">Start Date</th>
                                <th class="end-date">End Date</th>
                                <th class="roles-column">Roles</th>
                            </tr>
                        </thead>

                        <tbody>
                          @foreach ($organisation->AllassociatedUsers as $rowKey => $association)
                          @php
                          $startDate = old("user.$rowKey.start_date", $association->pivot->start_date?->format('Y-m-d') ?? '');
                          $endDate = old("user.$rowKey.end_date", $association->pivot->end_date?->format('Y-m-d') ?? '');
                          $selectedRoles = old("user.$rowKey.role", $association->pivot->roles->pluck('id')->toArray());
                          @endphp

                          <tr>
                            <td>
                                {{ $association->name }}
                                <input type="hidden" name="user[{{ $rowKey }}][id]" value="{{ $association->id }}">
                                <input type="hidden" name="user[{{ $rowKey }}][pivot_id]" value="{{ $association->pivot->id ?? '' }}">
                            </td>
                            <td>{{ $association->email }}</td>
                            <td class="start-date">
                                <input type="date" class="form-control start-date" name="user[{{ $rowKey }}][start_date]" value="{{ $startDate }}">
                            </td>
                            <td class="end-date">
                                <input type="date" class="form-control end-date" name="user[{{ $rowKey }}][end_date]" value="{{ $endDate }}">
                                <span class="text-danger">
                                    @error("user.$rowKey.end_date")
                                    {{ $message }}
                                    @enderror
                                </span>
                            </td>
                            <td class="roles-column">
                                @foreach (available_roles() as $roleValue => $roleLabel)
                                <div class="form-check form-check-inline">
                                    <input type="checkbox"
                                    class="form-check-input"
                                    name="user[{{ $rowKey }}][role][]"
                                    value="{{ $roleValue }}"
                                    {{ in_array($roleValue, $selectedRoles) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $roleLabel }}</label>
                                </div>
                                @endforeach
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>

            </div>
        </div>
        {{-- Update Button --}}
        <button type="submit" class="btn btn-primary">
            <i class="bi bi-arrow-up-circle"></i> Update Organisation
        </button>
    </form>
</div>
</div>
</div>
</div>
</div>
</div>



<div class="modal fade" id="addUsersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h2 class="modal-title">Add Business User</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            @include('business.user.business-users-register-form')
        </div>
    </div>
</div>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> {{-- Added jQuery CDN --}}
<script>
    $(document).ready(function () {
        const $removeImageBtn = $('#removeImageBtn');
        const $removeImageHiddenCheckbox = $('#remove_image_hidden');
        const $currentImageDisplay = $removeImageBtn.closest('.organisation-image');
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

<script>
    $(document).on('change', 'input[name="organisation_id"]', function () {
        $('#add_organisation_trigger').removeClass('d-none'); 
    });
    $(document).on('click', '#cancel_organisation_search', function () {
        $('#organisation_search_results').html('');
    });
    $('#search_organisation_btn').on('click', function () {
        const query = $('#organisation_search_input').val();
        $('#organisation_search_results').show();
        if (query.length === 0) {
            $('#organisation_search_results').html('');
            return;
        }
        $.ajax({
            url: '/search-users', 
            method: 'GET',
            data: { q: query },
            success: function (response) {
                let output = `
        <div class="well">
            <a href="javascript:;" id="cancel_organisation_search" class="btn btn-default float-end">
                <i class="bi bi-x-circle"></i> Close
            </a>
            <h4>Search results:</h4>
            <div class="form-group">
                `;

                if (response.length > 0) {
                    response.forEach(function (user) {
                        output += `
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="organisation_id" 
                        id="search_${user.id}" 
                        value="${user.id}" 
                        data-title="${user.name}">
                    <label class="form-check-label" for="search_${user.id}">
                        ${user.name} (business)
                    </label>
                </div>
                        `;
                    });
                } else {
                    output += '<div class="text-muted">No results found</div>';
                }

                output += `
            </div>
            <div class="btn btn-primary mt-2 d-none" id="add_organisation_trigger">
                <i class="bi bi-plus"></i> Add organisation
            </div>
        </div>
                `;

                $('#organisation_search_results').html(output);
            },
            error: function () {
                $('#organisation_search_results').html('<div class="text-danger p-2">Error fetching results</div>');
            }
        });
    });
    $(document).on('click', '#add_organisation_trigger', function () {
        let selected = $('input[name="organisation_id"]:checked');
        if (selected.length > 0) {
            let organisationId = selected.val();
            $.ajax({
                url: `/get-user-detail/${organisationId}`,
                method: 'GET',
                success: function (html) {

                    $('#user_organisation_table tbody').append(html); 
                    $('#organisation_search_results').hide();
                    $('#organisation_search_input').val('');
                },
                error: function (xhr) {
                    alert('Failed to fetch organisation details.');
                    console.error(xhr.responseText);
                }
            });
        } else {
            alert('Please select an organisation first.');
        }
    });
    $(document).on('click', '.remove-organisation-row', function() {
        let rowId = $(this).data('row-id'); 
        if (rowId) {
            $(this).closest('tr').remove();
        } 
    });





    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let formData = form.serialize();
        let actionUrl = form.attr('action');
        form.find('.is-invalid').removeClass('is-invalid');
        form.find('.invalid-feedback').text('');

        $.ajax({
            url: actionUrl,
            type: "POST",
            data: formData,
            success: function(response) {
                if (response.status) {
                    $('#user_organisation_table tbody').append(response.row);
                    $('#addUsersModal').modal('hide');
                    $('#addUserForm')[0].reset();
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;

                    $.each(errors, function(field, messages) {
                        let input = $('[name="' + field + '"]');
                        input.addClass('is-invalid');
                        input.next('.invalid-feedback').text(messages[0]);
                    });
                }
            }
        });
    });



</script>





@endsection
