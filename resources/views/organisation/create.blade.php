@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Organisations / Create') }}</div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <div class="mb-3 text-end">
                        <a href="{{ route('organisations.index') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Organisations Admin
                        </a>
                    </div>
                    <form action="{{ route('organisations.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Title</label>
                            <input type="text" name="title" id="title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title') }}" required>

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


                <div class="mb-3">
                    <label for="image" class="form-label fw-semibold">Organisation Logo</label>
                    <input type="file" name="image" id="image"
                    class="form-control @error('image') is-invalid @enderror">
                    @error('image')
                    <div class="invalid-feedback d-block">
                        {{ $message }}
                    </div>
                    @enderror

                </div>



                <div class="mb-3">
                    <label for="organisation_id" class="form-label fw-semibold">Business User </label>
                    <div class="input-group">
                        <select class="form-select @error('user_id') is-invalid @enderror" id="business_user_id" name="user_id">
                        </select>
                        <span class="input-group-text">
                          <button
                          type="button"
                          class="btn btn-primary"
                          id="add_organisation_users"
                          data-bs-toggle="modal"
                          data-bs-target="#addBusinessUsersModal"
                          >
                          <i class="bi bi-plus"></i> Add New Business User 
                      </button>

                  </span>
                  @error('user_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror

              </div>
          </div>

          <div class="mb-3">




             <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="form-label fw-semibold mb-0">
                  Sub Business Users
              </label>


              <div class="d-flex gap-2">

                <button
    type="button"
    class="btn btn-primary d-none"
    id="add_sub_business_user_btn"
    data-bs-toggle="modal"
    data-bs-target="#addUsersModal">
    <i class="bi bi-plus"></i> Add New Sub Business User
</button>

        </div>

        
    </div>



    <div id="organisation_all_results" class="list-group-test">
        <table id="user_organisation_table" class="table table-striped table-hover">
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
            </tbody>
        </table>
    </div>
</div>


          <!--  <div class="mb-3">
                 <fieldset class="border rounded p-3">
                    <legend class="float-none w-auto px-2">Add Users</legend>
                    <div class="input-group">
                        <input id="organisation_search_input" type="text"
                        class="form-control"
                        placeholder="Type to search Users ...">
                        <span class="input-group-text" id="search_organisation_btn">
                            <i class="bi bi-search"></i>
                        </span>
                    </div>
                    <div id="organisation_search_results" class="list-group mt-2"></div>
                    <div id="organisation_all_results" class="input-group mt-2">
                        <table id="user_organisation_table" class="table  align-middle custom-table">
                            <thead class="tableFloatingHeaderOriginal">
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Roles</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>

                    </div>
                </fieldset>
            </div>
-->



<button type="submit" class="btn btn-primary">
    <i class="bi bi-save"></i> Save Organisation
</button>
</form>
</div>
</div>
</div>
</div>
</div>


<div class="modal fade" id="addBusinessUsersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h2 class="modal-title">Add Business User</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            @include('organisation.business-users-register-form')
        </div>
    </div>
</div>

<div class="modal fade" id="addUsersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <h2 class="modal-title">Add Sub Business User</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            @include('organisation.sub-business-users-register-form')
        </div>
    </div>
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> 



<script>
    $(document).ready(function () {

        $('#business_user_id').select2({
            placeholder: 'Type business user ...',
            allowClear: true,
            minimumInputLength: 2,
            ajax: {
                url: '{{ route("searchbusinessUsers") }}',
                dataType: 'json',
                delay: 300,
                data: function (params) {
                    return {
                        search: params.term
                    };
                },
                processResults: function (data) {
                    return {
                        results: data
                    };
                }
            }
        });

        $('#business_user_id').on('change', function () {
            let businessId = $(this).val();
            $('#selected_business_id').val($(this).val());
            $('#user_organisation_table tbody').empty();
            if (!businessId) {
                $('#user_organisation_table tbody').empty();
                $('#add_sub_business_user_btn').addClass('d-none'); 
                return;
            }
            $('#add_sub_business_user_btn').removeClass('d-none');
            $.ajax({
                url: '{{ route("searchsubbusinessusers", ":id") }}'
                .replace(':id', businessId),
                type: 'GET',
                success: function (res) {
                    if (res.status) {
                        $('#user_organisation_table tbody').html(res.html);
                        $('#organisation_all_results').slideDown();
                    }
                }
            });
        });




        $('#addBusinessUserForm').on('submit', function(e) {
            e.preventDefault();

            let form = $(this);
            let actionUrl = form.attr('action');

            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');

            $.ajax({
                url: actionUrl,
                type: "POST",
                data: form.serialize(),
                success: function(response) {
                    if (response.status) {
                        let newOption = new Option(
                            response.business.text,
                            response.business.id,
                            true,
                            true
                            );
                        $('#business_user_id').append(newOption).trigger('change');
                        $('#addBusinessUsersModal').modal('hide');
                        form[0].reset();
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

        $('#addSubBusinessUserForm').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let actionUrl = form.attr('action');
            let businessId = $('#business_user_id').val(); 
            $('#selected_business_id').val(businessId);
            form.find('.is-invalid').removeClass('is-invalid');
            form.find('.invalid-feedback').text('');
            $.ajax({
                url: actionUrl,
                type: "POST",
                data: form.serialize(),
                success: function(response) {
                    if (response.status) {
                        $('#user_organisation_table tbody').append(response.row);
                        $('#addUsersModal').modal('hide');
                        form[0].reset();
                        $('#selected_business_id').val(businessId);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;

                        $.each(errors, function(field, messages) {
                            let input = form.find('[name="' + field + '"]');
                            input.addClass('is-invalid');
                            input.next('.invalid-feedback').text(messages[0]);
                        });
                    }
                }
            });
        });


    });
</script>




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
            <h4>Search Results:</h4>
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
                        ${user.first_name} ${user.last_name} (${user.email})
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
                <i class="bi bi-plus"></i> Add User
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



    $(document).ready(function() {
        $('.reject_user_trigger,.approve_user_trigger').on('click', function() {
            var button = $(this); 
            var pivotId = button.data('pivot-id');
            var organisationId = button.data('organisation-id');
            var userId = button.data('user-id');
            var newStatus = button.data('status');
            $.ajax({
               url: "{{ route('organisations.update.status') }}",
               type: 'post', 
               data: {
                pivot_id: pivotId,
                organisation_id: organisationId,
                user_id: userId,
                status: newStatus,
                _token: '{{ csrf_token() }}' 
            },
            success: function(response) {
                console.log('AJAX Success:', response);
                location.reload(); 
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.responseText);
                alert('An error occurred: ' + xhr.responseText);
            }
        });
        });

    });

</script>

@endsection


