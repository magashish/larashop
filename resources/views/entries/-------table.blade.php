@if ($entries->isEmpty())
    <div class="alert alert-info">No entries found.</div>
@else
    <div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
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
                        <td>{{ $entry->user->name ?? 'N/A' }}</td>
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

                            <span class="badge {{ $badgeClass }}">
                                {{ $badgeText }}
                            </span>
                        </td>
                        <td>{!! $entry->description !!}</td>
                        <td>{{ $entry->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            @if ($entry->type == 'manual')
                                <form action="{{ route('entries.destroy', $entry) }}" method="POST" onsubmit="return confirm('Delete this manual entry?');">
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
