@php
$row_id = (string) Str::uuid(); 
@endphp
<tr class="{{ $user->id }}">
    <td>{{ $user->name ?? '' }} </td>
    <td>{{ $user->email ?? '' }} </td>
    <td class="has-error">
        <input type="hidden" class="form-control" name="user[{{ $row_id }}][id]" value="{{ $user->id }}">
        <input type="date" class="form-control" name="user[{{ $row_id }}][start_date]" required>
    </td>
    <td>
        <input type="date" class="form-control" name="user[{{ $row_id }}][end_date]" required>
    </td>
    <td>
         @foreach (available_roles() as $roleValue => $roleLabel)
        <div class="form-check form-check-inline">
            <input type="checkbox"
            class="form-check-input"
            name="user[{{ $row_id }}][role][]"
            id="role-{{ $row_id }}-{{ $roleValue }}"
            value="{{ $roleValue }}">
            <label class="form-check-label" for="role-{{ $row_id }}-{{ $roleValue }}">
                {{ $roleLabel }}
            </label>
        </div>
        @endforeach
        <a type="button" class="btn btn-danger btn-sm remove-organisation-row" data-row-id="{{ $row_id }}">
            <i class="bi bi-trash"></i>
        </a>
    </td>
</tr>



