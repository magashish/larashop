 @if ($offers->isEmpty())
 <div class="alert alert-info" role="alert">
    No offers found.
</div>
@else
<div class="table-responsive">
    <table class="table  align-middle custom-table">
        <thead class="table-light">
            <tr>
                <th>Offer Category</th>
                <th>Offer Title </th>
                <th>Business</th>
                <th>Rating</th>
                <th>Pending Ratings </th>
                <th>Redemptions</th>
                <th>Subscribers</th>
                <th>Attachments</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($offers as $offer)
           <tr class="{{ $class }}">
                <td>
                    @forelse ($offer->categories as $category)
                    {{-- <span class="badge bg-secondary">{{ $category->name }}</span> --}}
                    <span>{{ $category->name }}</span>
                    @empty
                    <span class="text-muted">No Categories</span>
                    @endforelse
                </td>
                <td>{{ $offer->title }}</td>
                <td>{{ $offer->organisation->title ?? 'N/A' }}</td>

                <td>---</td>
                <td>---</td>
                <td>---</td>
                <td>---</td>

                <td>
                    {{ $offer->attachments->count() }}
                </td>

                <td class="text-end">
                    <a href="{{ route('business.offers.edit', $offer->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2"><i class="bi bi-pencil"></i> Edit</a>
                    <form action="{{ route('business.offers.destroy', $offer->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-dark custom-action-btn me-2"
                        onclick="return confirm('Are you sure you want to delete this offer?')"><i class="bi bi-trash"></i> Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endif


