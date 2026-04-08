<div class="table-responsive">
    <table class="table  align-middle custom-table">
        <thead class="table-light">
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Description</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($plans as $plan)
            <tr>
                <td>{{ $plan->name }}</td>
                <td>${{ number_format($plan->price , 2) }}</td> {{-- Assuming price is in cents --}}
                <td>{!! Str::limit($plan->description, 500) ?? 'N/A' !!}</td>
                <td class="text-end">
                    <a href="{{ route('plans.edit', $plan->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2" title="Edit"><i class="bi bi-pencil"></i> Edit</a>
                    <form action="{{ route('plans.destroy', $plan->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-dark custom-action-btn me-2" title="Delete" onclick="return confirm('Are you sure you want to delete this plan?');"><i class="bi bi-trash"></i> Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted">No subscription plans found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>