<tr>
    <td>
        {{ $user->name }}
        <input type="hidden" name="user[{{ $index }}][id]" value="{{ $user->id }}">
    </td>

    <td>{{ $user->email }}</td>

    <td class="start-date">
        <input
            type="date"
            class="form-control start-date"
            name="user[{{ $index }}][start_date]"
            value="{{ now()->format('Y-m-d') }}">
    </td>

    <td class="end-date">
        <input
            type="date"
            class="form-control end-date"
            name="user[{{ $index }}][end_date]">
        <span class="text-danger"></span>
    </td>

    <td class="roles-column">
        @foreach (available_roles() as $roleValue => $roleLabel)
            <div class="form-check form-check-inline">
                <input
                    type="checkbox"
                    class="form-check-input"
                    name="user[{{ $index }}][role][]"
                    value="{{ $roleValue }}">
                <label class="form-check-label">{{ $roleLabel }}</label>
            </div>
        @endforeach
    </td>
</tr>
