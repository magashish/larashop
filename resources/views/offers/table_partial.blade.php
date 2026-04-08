 @if ($offers->isEmpty())
 <div class="alert alert-info" role="alert">
    No offers found.
</div>
@else
<div class="table-responsive">
    <table class="table  align-middle custom-table">
        <thead class="table-light">
            <tr>
                <th>No.</th>
                <th>Offer Category</th>
                <th>Offer Title </th>
                <th>Business</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Attachments</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($offers as $index =>$offer)
           <tr class="{{ $class }}">
               <td>{{ $index }}</td>
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

                <td>
                    @if(!empty($offer->start_date))
                    {{ \Carbon\Carbon::parse($offer->start_date)->format('F j, Y') }}
                    @else
                    N/A
                    @endif
                </td>
                <td>
                    @if(!empty($offer->end_date))
                    {{ \Carbon\Carbon::parse($offer->end_date)->format('F j, Y') }}
                    @else
                    N/A
                    @endif
                </td>
                <td>
                    {{ $offer->attachments->count() }}
                </td>

                <td class="text-end">

                    @can('edit offers')
                    <a href="{{ route('offers.edit', $offer->id) }}" class="btn btn-sm btn-outline-dark custom-action-btn me-2"><i class="bi bi-pencil"></i> Edit</a>
                    @endcan

                    @can('delete offers')
                    <form action="{{ route('offers.destroy', $offer->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-dark custom-action-btn me-2"
                        onclick="return confirm('Are you sure you want to delete this offer?')"><i class="bi bi-trash"></i> Delete</button>
                    </form>
                    @endcan
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@endif