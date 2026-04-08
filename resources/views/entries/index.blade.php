@extends('layouts.app') {{-- Adjust to your layout file --}}
@section('content')


<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">
                    {{ __('Prize Draw Entries') }}
                </div>
                <div class="card-body">

                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif


                    <div class="card mb-4">
                        <div class="card-header fw-bold"> Add Multiple Entries via CSV </div>
                        <div class="card-body d-flex justify-content-between">
                            <a href="{{ route('entries.uploadcsv') }}" class="btn btn-dark text-white">
                                <i class="bi bi-file-earmark-spreadsheet"></i> Upload Entries CSV
                            </a>
                        </div>
                    </div>


                    <div class="mb-3 text-end">
                        <a href="{{ route('entries.create') }}" class="btn btn-success btn-sm">
                            <i class="bi bi-plus-circle"></i> Save Additional Entry
                        </a>
                    </div>




                    {{-- Tab-like links for server-side filtering --}}
                    <ul class="nav nav-pills nav-fill" role="tablist">
                        <li class="nav-item">
                            <a href="{{ route('entries.index') }}" class="nav-link {{ !request('type') ? 'active' : '' }}" role="tab">
                                <i class="bi bi-list-ul me-1"></i> All Entries
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('entries.index', ['type' => 'dynamic']) }}" class="nav-link {{ request('type') == 'dynamic' ? 'active' : '' }}" role="tab">
                                <i class="bi bi-lightning me-1"></i> Dynamic Entries
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('entries.index', ['type' => 'manual']) }}" class="nav-link {{ request('type') == 'manual' ? 'active' : '' }}" role="tab">
                                <i class="bi bi-pencil-square me-1"></i> Manual Entries
                            </a>
                        </li>
                    </ul>


                    <div class="tab-content mt-3">

                        @if ($entries->isEmpty())
                        <div class="alert alert-info">No entries found.</div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Description</th>
                                        <th>Created At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($entries as $entry)
                                    <tr>
                                        <td>{{ $entry->id }}</td>
                                        <td>
                                            {{ trim(($entry->user->first_name ?? '') . ' ' . ($entry->user->last_name ?? '')) ?: 'N/A' }}
                                        </td> 
                                         <td>{{ $entry->user->email ?? 'N/A' }}</td>
                                         <td>
                                            @php
                                            $type = strtolower($entry->type);
                                            $badgeClass = 'bg-secondary';
                                            $badgeText = ucwords($type);

                                            switch ($type) {
                                                case 'paid':
                                                $badgeClass = 'bg-success';
                                                break;
                                                case 'free':
                                                $badgeClass = 'bg-warning';
                                                break;
                                                case 'manual':
                                                $badgeClass = 'bg-info text-dark';
                                                break;
                                            }
                                            @endphp

                                            <span class="badge {{ $badgeClass }} me-2">
                                                {{ $badgeText }}
                                            </span>
                                        </td>
                                        <td>{!! $entry->description !!}</td>
                                        <td>{{ $entry->created_at->format('Y-m-d H:i') }}</td>

                                        <td>
                                            {{-- Check if the entry is of type 'manual' --}}
                                            @if ($entry->type == 'manual')
                                            <form action="{{ route('entries.destroy', $entry) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this manual entry?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                            @else
                                            <span class="text-muted">No actions available</span>

                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-dark pagination-sm">
                                    {{ $entries->appends(request()->input())->links('pagination::bootstrap-4') }}
                                </ul>
                            </nav>
                        </div>
                        @endif
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
@endsection