@extends('layouts.businessapp')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Organisations / Create') }}</div>
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

                        <form action="{{ route('business.organisations.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="title" class="form-label fw-semibold">Title</label>
                                <input type="text" name="title" id="title"
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title') }}"> {{-- old() to repopulate on validation error --}}

                                @error('title')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="abn" class="form-label fw-semibold">ABN</label>
                                <input type="text" name="abn" id="abn"
                                class="form-control @error('abn') is-invalid @enderror"
                                value="{{ old('abn') }}">
                                @error('abn')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="abn" class="form-label fw-semibold">Website</label>
                                <input type="url" name="website" id="website"
                                class="form-control @error('website') is-invalid @enderror"
                                value="{{ old('website') }}">

                                @error('website')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>


                            <div class="mb-3">
                             <fieldset class="border rounded p-3">
                                <legend class="float-none w-auto px-2">Social Media Accounts</legend>
                                <div class="mb-3">
                                    <label for="instagram" class="form-label">Instagram URL</label>
                                    <input type="text" name="instagram" id="instagram"
                                    class="form-control @error('instagram') is-invalid @enderror"
                                    value="{{ old('instagram') }}">
                                    @error('instagram')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="facebook" class="form-label">Facebook URL</label>
                                    <input type="text" name="facebook" id="facebook"
                                    class="form-control @error('facebook') is-invalid @enderror"
                                    value="{{ old('facebook') }}">
                                    @error('facebook')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="youtube" class="form-label">YouTube URL</label>
                                    <input type="text" name="youtube" id="youtube"
                                    class="form-control @error('youtube') is-invalid @enderror"
                                    value="{{ old('youtube') }}">
                                    @error('youtube')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="tiktok" class="form-label">TikTok URL</label>
                                    <input type="text" name="tiktok" id="tiktok"
                                    class="form-control @error('tiktok') is-invalid @enderror"
                                    value="{{ old('tiktok') }}">
                                    @error('tiktok')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="twitter" class="form-label">Twitter URL</label>
                                    <input type="text" name="twitter" id="twitter"
                                    class="form-control @error('twitter') is-invalid @enderror"
                                    value="{{ old('twitter') }}">
                                    @error('twitter')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                    @enderror
                                </div>
                            </fieldset>
                        </div>


                    {{-- <div class="mb-3">
                       <fieldset class="border rounded p-3">
                        <legend class="float-none w-auto px-2">Location Type</legend>
                        <div class="mb-3 @error('organisation_address_type') is-invalid @enderror">
                            @foreach (\App\Enums\OrganisationAddressType::labels() as $value => $label)
                            <div class="form-check">
                                <input class="form-check-input address-type-radio" type="radio" name="organisation_address_type" id="{{ $value }}" value="{{ $value }}"
                                {{ old('organisation_address_type', $organisation->organisation_address_type ?? \App\Enums\OrganisationAddressType::ONLINE) == $value ? 'checked' : '' }}>
                                <label class="form-check-label" for="{{ $value }}">
                                    {{ $label }}
                                </label>
                            </div>
                            @endforeach

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
                </div> --}}


                <div class="mb-3">
                 <fieldset class="border rounded p-3">
                    <legend class="float-none w-auto px-2">Location Type</legend>
                    {{-- Location Type Radio Buttons - NOW DYNAMIC --}}
                    <div class="mb-3 @error('organisation_address_type') is-invalid @enderror">
                        <div class="d-flex flex-wrap gap-4"> 
                            @foreach (\App\Enums\OrganisationAddressType::labels() as $value => $label)
                            <div class="form-check">
                                <input class="form-check-input address-type-radio" type="radio" name="organisation_address_type" id="{{ $value }}" value="{{ $value }}"
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
                            value="{{ old('full_address') }}"
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
                            @foreach ($users as $rowKey => $association)
                            <tr>
                                <td>
                                    {{ $association->name }}
                                    <input type="hidden" name="user[{{ $rowKey }}][id]" value="{{ $association->id }}">
                                </td>
                                <td>{{ $association->email }}</td>
                                <td class="start-date">
                                    <input
                                    type="date"
                                    class="form-control start-date"
                                    name="user[{{ $rowKey }}][start_date]"
                                    value="{{ now()->format('Y-m-d') }}"
                                    >
                                </td>
                                <td class="end-date">
                                    <input
                                    type="date"
                                    class="form-control end-date"
                                    name="user[{{ $rowKey }}][end_date]"
                                    value=""
                                    >
                                    <span class="text-danger">
                                        @error("user.$rowKey.end_date")
                                        {{ $message }}
                                        @enderror
                                    </span>
                                </td>
                                <td class="roles-column">
                                    @foreach (available_roles() as $roleValue => $roleLabel)
                                    <div class="form-check form-check-inline">
                                        <input
                                        type="checkbox"
                                        class="form-check-input"
                                        name="user[{{ $rowKey }}][role][]"
                                        value="{{ $roleValue }}"
                                        >
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

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Save Organisation
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


