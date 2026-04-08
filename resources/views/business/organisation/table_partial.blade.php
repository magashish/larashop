<div class="table-responsive">
    <table class="table  align-middle custom-table">
        <thead class="table-light">
            <tr>
                <th>Title</th>
                <th>Logo</th>
                <th>Rating</th>
                <th>Pending Ratings </th>
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
                <td>----</td>
                <td>----</td>
                <td>{{ $organisation->allOffers->count() }}</td>
                <td>{{ $organisation->rejectedOffers->count() }}</td>
                <td>{{ $organisation->pendingOffers->count() }}</td>
                <td>{{ $organisation->activeOffers->count() }}</td>
                <td>{{ $organisation->activeAllassociatedUsers->count() }}</td>
                <td class="text-end">
                   <a href="{{ route('business.organisations.show', $organisation->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-1">
                    <i class="bi bi-list-columns-reverse"></i> View all Offers
                </a>
                <a href="{{ route('business.organisations.edit', $organisation->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-1">
                    <i class="bi bi-pencil"></i> Edit
                </a>

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


