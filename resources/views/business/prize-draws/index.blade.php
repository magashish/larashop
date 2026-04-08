@extends('layouts.businessapp') 
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __(' Subscriber Prize Draws') }}</div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <div class="table-responsive">
                        @if($usersprizeDraws->isNotEmpty())
                        <table class="table custom-table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Prize Draw Date </th>
                                    <th>Draw Type </th>
                                    <th>Winner Finalised </th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($usersprizeDraws as $draw) 
                                <tr>
                                    <td>{{ $draw->draw_date->format('Y-m-d') }}</td>
                                    <td>{{ Str::title($draw->draw_type) }}</td>
                                    <td>
                                        @if ($draw->winner_user_finalised == 'yes')
                                        Completed
                                        @else
                                        In Progress
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('business.prize_draws.show', $draw->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                            <i class="bi bi-list-columns-reverse"></i> View Your Entries
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @else
                        <div class="alert alert-info text-center" role="alert">
                            No prize draws found.
                        </div>
                        @endif
                    </div>
                </div>
            </div>


            <div class="card shadow mt-5">
                <div class="card-header fw-bold">{{ __('Business Prize Draws') }}</div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <div class="table-responsive">
                      @if($organisationsprizeDraws->isNotEmpty())
                      <table class="table custom-table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Prize Draw Date </th>
                                <th>Draw Type </th>
                                <th>Winner Finalised </th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($organisationsprizeDraws as $draw) 
                            <tr>
                                <td>{{ $draw->draw_date->format('Y-m-d') }}</td>
                                <td>{{ Str::title($draw->draw_type) }}</td>
                                <td>
                                    @if ($draw->winner_user_finalised == 'yes')
                                    Completed
                                    @else
                                    In Progress
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('business.prize_draws.show', $draw->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                        <i class="bi bi-list-columns-reverse"></i> View Your Entries
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="alert alert-info" role="alert">
                        No prize draws found. 
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
