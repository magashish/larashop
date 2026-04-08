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
        @foreach ($roles as $role)
        <div class="form-check form-check-inline">
            <input type="checkbox" class="form-check-input" name="user[{{ $row_id }}][role][]" value="{{ $role->id }}">
            <label class="form-check-label">{{ $role->name }} </label>
        </div>
        @endforeach
        <a type="button" class="btn btn-danger btn-sm remove-organisation-row" data-row-id="{{ $row_id }}">
            <i class="bi bi-trash"></i>
        </a>
    </td>
</tr>



