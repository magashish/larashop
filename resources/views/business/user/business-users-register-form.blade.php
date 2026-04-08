<form id="addUserForm" method="POST" action="{{ route('business.users.store') }}">
    @csrf
    <div class="modal-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="fw-semibold">First Name</label>
                <input type="text" name="first_name" class="form-control">
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="fw-semibold">Last Name</label>
                <input type="text" name="last_name" class="form-control">
                <div class="invalid-feedback"></div>
            </div>
        </div>
        <div class="mb-3">
            <label class="fw-semibold">Email</label>
            <input type="email" name="email" class="form-control">
            <div class="invalid-feedback"></div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="fw-semibold">Password</label>
                <input type="password" name="password" class="form-control">
                <div class="invalid-feedback"></div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="fw-semibold">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control">
            </div>
        </div>
        <div class="mb-3">
            <label class="fw-semibold">Mobile</label>
            <input type="text" name="mobile" class="form-control">
            <div class="invalid-feedback"></div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            Cancel
        </button>
        <button type="submit" class="btn btn-primary">
            Register
        </button>
    </div>
</form>
