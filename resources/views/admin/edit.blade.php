@extends('layouts.app')
@section('content')
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold">{{ __('Admin / Edit') }}</div>
                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('status') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif
                    <div class="mb-3 text-end">
                        <a href="{{ route('admins.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Admin Users Admin
                        </a>
                    </div>
                    <form method="POST" action="{{ route('admins.update', $admin->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="name" class="form-label fw-semibold">Name</label>
                            <input id="name" type="text"
                            class="form-control @error('name') is-invalid @enderror"
                            name="name" value="{{ old('name', $admin->name) }}"
                            placeholder="Enter full name" required autofocus>
                            @error('name')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input id="email" type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            name="email" value="{{ old('email', $admin->email) }}"
                            placeholder="Enter email" required readonly> 
                            @error('email')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                            <input id="password" type="password"
                            class="form-control @error('password') is-invalid @enderror"
                            name="password"
                            placeholder="Enter new password"
                            autocomplete="new-password"> {{-- Added autocomplete attribute --}}
                            @error('password')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="password-confirm" class="form-label fw-semibold">Confirm New Password</label>
                            <input id="password-confirm" type="password"
                            class="form-control"
                            name="password_confirmation"
                            placeholder="Re-enter new password"
                            autocomplete="new-password"> {{-- Added autocomplete attribute --}}
                        </div>

                        {{-- Level Field --}}
                     <div class="mb-3">
                            <label for="level" class="form-label fw-semibold">Level</label>
                            <select id="level" name="level" class="form-select @error('level') is-invalid @enderror" required>
                                @foreach($adminroles as $role)
                                <option value="{{ $role->name }}"
                                    {{ old('level', $admin->level ?? '') == $role->name ? 'selected' : '' }}>
                                    {{ Str::title(str_replace('-', ' ', $role->name)) }}
                                </option>
                                @endforeach
                            </select>
                            @error('level')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        {{-- Status Field --}}
                        <div class="mb-4">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="active" {{ old('status', $admin->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="deactive" {{ old('status', $admin->status) == 'deactive' ? 'selected' : '' }}>Deactive</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback d-block">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>

                        <div class="mb-3" id="editor_role">
                            <fieldset class="border rounded p-3">
                                <legend class="float-none w-auto px-2">Permissions</legend>
                                @foreach ($permissions_group as $groupName => $groupPermissions)
                                <div class="mb-3"> 
                                    <fieldset class="border rounded p-3">
                                        <legend class="float-none w-auto px-2">{{ $groupName === '' ? 'Uncategorized' : $groupName }}</legend>
                                        <div class="d-flex flex-column gap-2">
                                            @foreach ($groupPermissions as $permission)
                                            <div class="form-check me-3"> {{-- Add margin-right for spacing between checkboxes --}}
                                                <input class="form-check-input" type="checkbox" name="permissions[]"
                                                value="{{ $permission->name }}" id="perm_{{ $permission->id }}"
                                                {{ isset($hasPermissions) && $hasPermissions->contains($permission->name) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm_{{ $permission->id }}">
                                                    {{ $permission->name }}
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                    </fieldset>
                                </div>
                                @endforeach
                            </fieldset>
                        </div>


                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        const $levelSelect = $('#level');
        const $editorRoleDiv = $('#editor_role');
        function toggleEditorRoleVisibility() {
            if ($levelSelect.val() === 'editor') {
                $editorRoleDiv.show(); 
            } else {
                $editorRoleDiv.hide(); 
            }
        }
        toggleEditorRoleVisibility();
        $levelSelect.on('change', toggleEditorRoleVisibility);
    });
</script>
@endsection
