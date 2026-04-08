@extends('layouts.app') {{-- Adjust this to your main layout file, e.g., 'layouts.admin' for admin panel --}}

@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Prize Draws') }}</div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <div class="mb-3 text-end">
                        <a href="{{ route('prize_draws.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Create New Prize Draw
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table custom-table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title </th>
                                    <th>Prize Draw Imagw </th>
                                    <th>Prize Description</th>
                                    <th>Prize Draw Date </th>
                                    <th>Draw Type </th>
                                    <th>Winner Finalised </th>
                                    <th>Show on Site</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($prizeDraws as $draw)
                                <tr>
                                    <td>{{ $draw->title }}</td> 
                                    <td>{!! $draw->prize_description ?? '' !!}</td>
                                    <td>
                                        @if ($draw->prize_image)
                                        <img src="{{ Storage::url($draw->prize_image) }}" alt="Org Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                        <img src="{{ asset('images/demo.jpg') }}" alt="Org Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        @endif
                                    </td>
                                    <td>{{ $draw->draw_date->format('F d, Y h:i A') }}</td>
                                    <td>{{ Str::title($draw->draw_type) }}</td> 
                                    <td>
                                        @if ($draw->winner)

                                        {{ $draw->winner->name
                                            ? $draw->winner->name
                                            : ($draw->winner->first_name
                                                ? $draw->winner->first_name . ' ' . $draw->winner->last_name
                                                : $draw->winner->email)
                                            }}

                                            @if ($draw->winner_user_finalised == 'yes')
                                            <span class="badge bg-success ms-2">Finalised</span>
                                            @else
                                            <span class="badge bg-warning text-dark ms-2">Not Finalised Yet</span>
                                            @endif
                                            @else
                                            No winner yet
                                            @endif
                                        </td>
                                        <td>
                                            @if($draw->show_on_site)
                                            <span class="badge bg-success">Yes</span>
                                            @else
                                            <span class="badge bg-secondary">No</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('prize_draws.edit', $draw->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2"><i class="bi bi-pencil"></i> Edit</a>
                                            <a href="{{ route('prize_draws.show', $draw->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2"><i class="bi bi-pencil"></i> Draws</a> 
                                            <form action="{{ route('prize_draws.destroy', $draw->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-dark custom-action-btn me-2"
                                                onclick="return confirm('Are you sure you want to delete this offer?')"><i class="bi bi-trash"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No prize draws found.</td>
                                    </tr>
                                    @endforelse
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endsection
