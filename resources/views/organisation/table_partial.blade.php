<div class="table-responsive">
    <table class="table  align-middle custom-table">
        <thead class="table-light">
            <tr>
                <th>Title</th>
                <th>Logo</th>
                <th>Total Offers</th>
                <th>Rejected Offers</th>
                <th>Pending Offers</th>
                <th>Active Offers</th>
                <th>Active Users    </th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($organisations as $organisation)
            <tr class="{{ $class }}">
                <td>{{ $organisation->title }}</td>
                <td>
                    @if ($organisation->image)
                    <img src="{{ Storage::url($organisation->image) }}" alt="Org Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                    @else
                    <img src="{{ asset('images/demo.jpg') }}" alt="Org Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                    @endif
                </td>
                <td>{{ $organisation->allOffers->count() }}</td>
                <td>{{ $organisation->rejectedOffers->count() }}</td>
                <td>{{ $organisation->pendingOffers->count() }}</td>
                <td>{{ $organisation->activeOffers->count() }}</td>
                <td>{{ $organisation->activeAllassociatedUsers->count() }}</td>
                <td class="text-end">

                @can('view offers')
                   <a href="{{ route('organisations.show', $organisation->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-1 mb-1">
                    <i class="bi bi-list-columns-reverse"></i> View all Offers
                </a>
                @endcan

                @can('edit organisations')
                <a href="{{ route('organisations.edit', $organisation->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-1 mb-1"><i class="bi bi-pencil"></i> Edit</a>
                @endcan

                @can('delete organisations')
                <form action="{{ route('organisations.destroy', $organisation->id) }}" method="POST" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-dark custom-action-btn me-2 mb-1"
                    onclick="return confirm('Are you sure you want to delete this organisation?')"><i class="bi bi-trash"></i> Delete</button>
                </form> 
                @endcan
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="11" class="text-center py-4">No organisations found for this status.</td>
        </tr>
        @endforelse
    </tbody>
</table>
</div>

