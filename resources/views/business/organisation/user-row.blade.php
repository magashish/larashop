<tr>
    <td>
        {{ $association->name }}
        <input type="hidden" name="user[{{ $rowKey }}][id]" value="{{ $association->id }}">
    </td>
    <td>{{ $association->email }}</td>
    <td class="start-date">
        <input type="date" class="form-control start-date"
        name="user[{{ $rowKey }}][start_date]">
    </td>
    <td class="end-date">
        <input type="date" class="form-control end-date"
        name="user[{{ $rowKey }}][end_date]">
        <span class="text-danger">
       
        </span>
    </td>
    <td class="roles-column">
        @foreach (available_roles() as $roleValue => $roleLabel)
        <div class="form-check form-check-inline">
            <input type="checkbox"
            class="form-check-input"
            name="user[{{ $rowKey }}][role][]"
            value="{{ $roleValue }}">
            <label class="form-check-label">{{ $roleLabel }}</label>
        </div>
        @endforeach
    </td>
</tr>
