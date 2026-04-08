@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('User / Edit') }}</div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <div class="mb-3 text-end">
                        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Users Admin
                        </a>
                    </div>
                    <form method="POST" action="{{ route('users.update', $user->id) }}">
                        @csrf
                        @method('PUT')
                    


                         <div class="row">
                            <div class="col-md-6"> {{-- Column for First Name --}}
                                <div class="mb-3">
                                    <label for="first_name" class="form-label fw-semibold">First Name</label>
                                    <input id="first_name" type="text"
                                    class="form-control @error('first_name') is-invalid @enderror"
                                    name="first_name" value="{{ old('first_name', $user->first_name) }}"
                                    placeholder="Enter first name" required>
                                    @error('first_name')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6"> {{-- Column for Last Name --}}
                                <div class="mb-3">
                                    <label for="last_name" class="form-label fw-semibold">Last Name</label>
                                    <input id="last_name" type="text"
                                    class="form-control @error('last_name') is-invalid @enderror"
                                    name="last_name" value="{{ old('last_name', $user->last_name) }}"
                                    placeholder="Enter last name" required>
                                    @error('last_name')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input id="email" type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email', $user->email) }}"
                            placeholder="Enter email" required readonly> 
                            @error('email')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                            <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password"
                            placeholder="Enter new password"
                            autocomplete="new-password"> {{-- Added autocomplete attribute --}}
                            @error('password')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="password-confirm" class="form-label fw-semibold">Confirm New Password</label>
                            <input id="password-confirm" type="password"
                            class="form-control"
                            name="password_confirmation"
                            placeholder="Re-enter new password"
                            autocomplete="new-password"> {{-- Added autocomplete attribute --}}
                        </div>

                        {{-- Level Field --}}

                        <div class="mb-3">
                            <label for="level" class="form-label fw-semibold">Level</label>
                            <select id="level" name="level" class="form-select @error('level') is-invalid @enderror" required>
                                @foreach($webroles as $role)
                                <option value="{{ $role->name }}"
                                    {{ old('level', $user->level ?? '') == $role->name ? 'selected' : '' }}>
                                    {{ Str::title(str_replace('-', ' ', $role->name)) }}
                                </option>
                                @endforeach
                            </select>
                            @error('level')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>


                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input exclude-switch" type="checkbox"
                            id="exclude_from_prize_draw"
                            name="exclude_from_prize_draw"
                            value="1"
                            {{ old('exclude_from_prize_draw', $user->exclude_from_prize_draw) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="exclude_from_prize_draw">
                                Exclude User from Prize Draw
                            </label>
                        </div>

                         <div class="mb-3">
                                <label for="mobile" class="form-label fw-semibold">Mobile</label>
                                <input id="mobile" type="text"
                                class="form-control @error('mobile') is-invalid @enderror"
                                name="mobile" value="{{ old('mobile', $user->mobile) }}"
                                placeholder="Enter mobile Number" required>
                                @error('mobile')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>



                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input exclude-switch" type="checkbox"
                                id="show_stripe_id" value="1">
                                <label class="form-check-label fw-semibold" for="show_stripe_id">
                                    Show Stripe Id
                                </label>
                            </div>

                            <div class="mb-3" id="stripeIdWrapper" style="display: none;">
                                <label for="stripe_id" class="form-label fw-semibold">Stripe Id</label>
                                <input id="stripe_id" type="text"
                                class="form-control @error('stripe_id') is-invalid @enderror"
                                name="stripe_id" value="{{ old('stripe_id', $user->stripe_id) }}"
                                placeholder="Enter Stripe Id">
                                @error('stripe_id')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>



                        {{-- Status Field --}}
                        <div class="mb-4">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="deactive" {{ old('status', $user->status) == 'deactive' ? 'selected' : '' }}>Deactive</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>


                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input exclude-switch" type="checkbox"
                            id="sms_unsubscribed"
                            name="sms_unsubscribed"
                            value="1"
                            {{ old('sms_unsubscribed', $user->sms_unsubscribed) ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold" for="exclude_from_prize_draw">
                                Sms Unsubscribed
                            </label>
                        </div>


                        <!-- <div class="mb-3" >
                            <label for="organisation_search_input" class="form-label fw-semibold">Search Organisation</label>
                            <div class="input-group">
                                <input id="organisation_search_input" type="text"
                                class="form-control"
                                placeholder="Type to search organisations...">
                                <span class="input-group-text" id="search_organisation_btn">
                                    <i class="bi bi-search"></i>
                                </span>
                            </div>

                            <div id="organisation_search_results" class="list-group mt-2"></div>
                            <div id="organisation_all_results" class="list-group mt-2">
                                <table id="user_organisation_table" class="table table-striped table-hover" >
                                    <thead class="tableFloatingHeaderOriginal">
                                        <tr>
                                            <th>Organisation</th>
                                            <th>Organisation Type</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Roles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($user->associatedOrganisations as $association)
                                        @php
                                        $row_id = (string) Str::uuid();
                                        $startDate = $association->pivot->start_date ?? '';
                                        $endDate = $association->pivot->end_date ?? '';

                                        $startDate = $association->pivot->start_date ? $association->pivot->start_date->format('Y-m-d') : '';
                                        $endDate = $association->pivot->end_date ? $association->pivot->end_date->format('Y-m-d') : '';

                                       // $selectedRoles = json_decode($association->pivot->role, true) ?? []; 
                                        //$selectedRoles = []; 
                                        $selectedRoles = $association->pivot->roles->pluck('id')->toArray();

                                        @endphp

                                        <tr id="organisation-row-{{ $row_id }}">
                                            <td>
                                                {{ $association->title ?? '' }}
                                                {{-- Hidden input to send the organisation ID with the form --}}
                                                <input type="hidden" name="organisation[{{ $row_id }}][id]" value="{{ $association->id }}">
                                                {{-- HIDDEN INPUT TO SEND PIVOT ID FOR UPDATES/DELETES --}}
                                                <input type="hidden" name="organisation[{{ $row_id }}][pivot_id]" value="{{ $association->pivot->id ?? '' }}">
                                            </td>
                                            <td>
                                                {{ ucfirst(str_replace('_', ' ', $association->organisation_address_type ?? 'N/A')) }}
                                            </td>
                                            <td>
                                                <input type="date" class="form-control" name="organisation[{{ $row_id }}][start_date]" value="{{ $startDate }}">
                                            </td>
                                            <td>
                                                <input type="date" class="form-control" name="organisation[{{ $row_id }}][end_date]" value="{{ $endDate }}">
                                            </td>

                                            <td>
                                                @foreach (available_roles() as $roleValue => $roleLabel)
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox"
                                                    class="form-check-input"
                                                    name="organisation[{{ $row_id }}][role][]"
                                                    id="role-{{ $row_id }}-{{ $roleValue }}"
                                                    value="{{ $roleValue }}"
                                                    {{ in_array($roleValue, $selectedRoles) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="role-{{ $row_id }}-{{ $roleValue }}">
                                                        {{ $roleLabel }}
                                                    </label>
                                                </div>
                                                @endforeach
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                            </div>
                        </div>
 -->

                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#show_stripe_id').change(function () {
            if ($(this).is(':checked')) {
                $('#stripeIdWrapper').slideDown(); 
            } else {
                $('#stripeIdWrapper').slideUp(); 
            }
        });
    });


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
            url: '/search-organisations', 
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
                    response.forEach(function (org) {
                        output += `
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="organisation_id" 
                        id="search_${org.id}" 
                        value="${org.id}" 
                        data-title="${org.title}">
                    <label class="form-check-label" for="search_${org.id}">
                        ${org.title} (business)
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
                url: `/get-organisations-detail/${organisationId}`,
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

</script>




@endsection
