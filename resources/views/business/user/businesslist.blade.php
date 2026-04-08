@extends('layouts.businessapp')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">

           @if (session('status'))
           <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif


        <div class="card shadow mb-5">
            <div class="card-header fw-bold">{{ __('Manage My Account') }}</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 mb-3"> 
                        <a href="{{ route('business.myaccount.edit', $user->id) }}" class="btn btn-primary w-100 py-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-pencil fs-4 me-2"></i>
                            <span class="fs-5">Edit my details</span>
                        </a>
                    </div>
                    <div class="col-sm-6 mb-3"> 
                        <a href="{{ route('business.password.edit', $user->id) }}" class="btn btn-secondary w-100 py-3 d-flex align-items-center justify-content-center">
                            <i class="bi bi-key fs-4 me-2"></i> 
                            <span class="fs-5">Change password</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>



        {{-- <div class="card shadow">
            <div class="card-header fw-bold">{{ __('Business Accounts') }}</div>
            <div class="card-body">

                <p class="alert alert-info">Any Business accounts you have access to will show here. You can also search for businesses to request to be added to the account.</p>

                <button class="btn btn-primary" type="button" id="organisation_search_btn">Search for a Business to add to your Account</button>
                <div class="mb-3">
                    <div id="organisation_search_div" class="list-group mt-2">
                        <div>
                            <p class="alert alert-info">Search for the businesses to request to be added to the account. The business must already exist on Xhale</p>

                        </div>
                        <label for="organisation_search_input" class="form-label fw-semibold">Business name for Link Request:</label>
                        <div class="input-group">
                            <input id="organisation_search_input" type="text"
                            class="form-control"
                            placeholder="Type to search organisations...">
                            <span class="input-group-text" id="search_organisation_btn">
                                <i class="bi bi-search"></i> 
                            </span>
                        </div>
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
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($user->associatedOrganisations as $association)
                                @php
                                $row_id = (string) Str::uuid();
                                $startDate = $association->pivot->start_date ? $association->pivot->start_date->format('Y-m-d') : '';
                                $endDate = $association->pivot->end_date ? $association->pivot->end_date->format('Y-m-d') : '';
                                $selectedRoles = $association->pivot->roles->pluck('id')->toArray();
                                $displayRoles = [];
                                foreach ($selectedRoles as $selectedRoleValue) {
                                    if (isset($availableRoles[$selectedRoleValue])) {
                                        $displayRoles[] = $availableRoles[$selectedRoleValue];
                                    }
                                }
                                $status = $association->pivot->status ?? 'unknown'; 
                                $rowClass = '';
                                if ($status === 'pending') {
                                    $rowClass = 'table-warning'; 
                                } elseif ($status === 'approve') {
                                    $rowClass = 'table-success'; 
                                } elseif ($status === 'reject') {
                                    $rowClass = 'table-danger'; 
                                }

                                @endphp
                                <tr id="organisation-row-{{ $row_id }}" class="{{ $rowClass }}">

                                    <td>
                                        {{ $association->title ?? '' }}
                                    </td>
                                    <td>
                                        business 
                                    </td>
                                    <td>
                                        @if($status == "pending")
                                        Link requested  {{ $startDate }}
                                        @else
                                       {{ $startDate }}
                                       @endif


                                        
                                    </td>
                                    <td>
                                        {{ $endDate }}
                                    </td>

                                    <td>
                                        @if (!empty($displayRoles))
                                        {{ implode(', ', $displayRoles) }}
                                        @endif
                                    </td>
                                    <td></td>

                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
 --}}

    </div>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                <i class="bi bi-plus"></i>  Request link to access this Business
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
                url: `/add-organisations-detail/${organisationId}`,
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
        $('#organisation_search_div').hide();
        $('#organisation_search_btn').on('click', function() {
            $('#organisation_search_div').slideToggle(); 
        });
    });

</script>




@endsection
