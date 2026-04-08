@extends('layouts.subscriber')
@section('content')
<div class="db-main-content">
    <div class="container-fluid">
        <div class="account-wrapper">
            <div class="row">
                <div class="col-12">
                    <div class="auth-box py-4">
                        <div class="sec-head mb-4">
                            <h2 class="text-uppercase text-white">Accounts</h2>
                        </div>
                        <div class="account-info-wrapper text-white">
                            <div class="info-item">
                                <div class="item-label text-md-start text-center">
                                    <p class="text-uppercase mb-0">Profile Image</p>
                                </div>
                                <div class="item-data text-center">

                                   @if(Auth::user()->profile_image)
                                   <img  src="{{ Storage::url(Auth::user()->profile_image) }}" alt="Profile Image" class="profile-img img-fluid mx-auto previewImage">
                                   @else
                                   <img  src="{{ asset('images/boy.png') }}" alt="Preview" class="profile-img img-fluid mx-auto previewImage">
                                   @endif
                               </div>
                               <div class="item-action text-md-end text-center">
                                {{-- <button class="remove-btn text-uppercase">Remove</button> --}}
                                <button class="edit-btn text-uppercase" data-bs-toggle="modal" data-bs-target="#editModal1">Edit <i class="bi bi-pencil-square"></i></button>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="item-label text-md-start text-center">
                                <p class="text-uppercase mb-0">Profile Name</p>
                            </div>
                            <div class="item-data text-center">
                                <h6 class="text-uppercase mb-0 user-name-display">{{ Auth::user()->name }}</h6>
                            </div>
                            <div class="item-action text-md-end text-center">
                                {{-- <button class="remove-btn text-uppercase">Remove</button> --}}
                                <button class="edit-btn text-uppercase" data-bs-toggle="modal" data-bs-target="#editModal2">Edit <i class="bi bi-pencil-square"></i></button>
                            </div>
                        </div>

                            {{-- <div class="info-item">
                                <div class="item-label">
                                    <p class="text-uppercase mb-0">Email Address</p>
                                </div>
                                <div class="item-data text-center">
                                    <p class="mb-0">jed_ritchie@hotmail.com</p>
                                </div>
                                <div class="item-action text-md-end text-center">
                                    <button class="remove-btn text-uppercase">Remove</button>
                                    <button class="edit-btn text-uppercase" data-bs-toggle="modal" data-bs-target="#editModal3">Edit <i class="bi bi-pencil-square"></i></button>
                                </div>
                            </div> --}}

                            <div class="info-item">
                                <div class="item-label text-md-start text-center">
                                    <p class="text-uppercase mb-0">Phone Number</p>
                                </div>
                                <div class="item-data text-center">
                                    <p class="mb-0 user-mobile-display">{{ Auth::user()->mobile }}</p>
                                </div>
                                <div class="item-action text-md-end text-center">
                                    {{-- <button class="remove-btn text-uppercase">Remove</button> --}}
                                    <button class="edit-btn text-uppercase" data-bs-toggle="modal" data-bs-target="#editModal4">Edit <i class="bi bi-pencil-square"></i></button>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="item-label text-md-start text-center">
                                    <p class="text-uppercase mb-0">Password</p>
                                </div>
                                <div class="item-data text-center">
                                    <p class="mb-0">****************</p>
                                </div>
                                <div class="item-action text-md-end text-center">
                                    {{-- <button class="remove-btn text-uppercase">Remove</button> --}}
                                    <button class="edit-btn text-uppercase" data-bs-toggle="modal" data-bs-target="#editModal5">Edit <i class="bi bi-pencil-square"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="sec-btn">
                            <form action="{{ route('profile.destroy', auth()->id()) }}" method="POST"
                              class="d-inline" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-sm btn-outline-dark custom-action-btn me-2 text-uppercase w-100">
                                <i class="bi bi-trash"></i>  Delete  Account
                            </button>
                        </form>
                    </div>  

                </div>
            </div>
        </div>
    </div>
</div>
</div>





<div class="modal fade edit-content-modal" id="editModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4">
      <div class="modal-header p-0 pb-2">
        <h5 class="modal-title text-uppercase" id="exampleModalLabel">Change Profile Image</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
    </div>
    <form>
      <div class="modal-body px-0 py-4">        
          <div class="mb-3">
            <label for="" class="col-form-label text-uppercase">Profile Image</label>
            <div class="profile-img h-100">
                <label for="imageInput" class="mt-2 w-100">
                  <div class="image-upload-box d-flex align-items-center justify-content-center position-relative" id="previewBox">

                     @if(Auth::user()->profile_image)
                     <img id="previewImage" src="{{ Storage::url(Auth::user()->profile_image) }}" alt="Profile Image" class="img-fluid previewImage" >
                     @else
                     <img id="previewImage" src="{{ asset('images/upload.png') }}" alt="Preview" class="img-fluid previewImage">
                     @endif
                     <div class="edit-icon"><i class="bi bi-pencil-square"></i></div>
                 </div>
             </label>

             <input type="file" id="imageInput" accept="image/*">
         </div>
     </div>
 </div>
 <div class="modal-footer border-0 p-0">
    <button type="button" class="btn btn-secondary text-uppercase" data-bs-dismiss="modal">Cancel</button>
</div>
</form>
</div>
</div>
</div>




<div class="modal fade edit-content-modal" id="editModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4">
      <div class="modal-header p-0 pb-2">
        <h5 class="modal-title text-uppercase" id="exampleModalLabel">Change Profile Name</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
    </div>
    <form id="nameForm">
        <input type="hidden" name="formname" value="nameForm">
        <div class="modal-body px-0 py-4">        
          <div class="mb-3">
            <label for="" class="col-form-label text-uppercase">FULL NAME</label>
            <div class="">
                <input type="text" class="form-control" name="name" value="{{ Auth::user()->name }}">
            </div>
        </div>
    </div>
    <div class="modal-footer border-0 p-0">
        <button type="button" class="btn btn-secondary text-uppercase" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary text-uppercase update_profile" data-formid="nameForm">Save</button>
    </div>
</form>
</div>
</div>
</div>



<div class="modal fade edit-content-modal" id="editModal4" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4">
      <div class="modal-header p-0 pb-2">
        <h5 class="modal-title text-uppercase" id="exampleModalLabel">Change Phone Number</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
    </div>
    <form id="phoneForm">
       <input type="hidden" name="formname" value="phoneForm">
       <div class="modal-body px-0 py-4">        
          <div class="mb-3">
            <label for="" class="col-form-label text-uppercase">Phone Number <Number></Number></label>
            <div class="">
                <input type="tel" class="form-control" name="mobile" value="{{ old('mobile', Auth::user()->mobile) }}" pattern="\+?\d{10,15}" title="10-15 digits with optional + prefix">
            </div>
        </div>
    </div>
    <div class="modal-footer border-0 p-0">
        <button type="button" class="btn btn-secondary text-uppercase" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary text-uppercase update_profile" data-formid="phoneForm">Save</button>
    </div>
</form>
</div>
</div>
</div>

<div class="modal fade edit-content-modal" id="editModal5" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content p-4">
      <div class="modal-header p-0 pb-2">
        <h5 class="modal-title text-uppercase" id="exampleModalLabel">Change Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">X</button>
    </div>
    <form id="passwordForm">
        <input type="hidden" name="formname" value="passwordForm">
        <div class="modal-body px-0 py-4"> 

           <ul class="list-unstyled mb-0">
            <li><i class="bi bi-circle-fill me-2"></i> Be at least 8 characters long</li>
            <li><i class="bi bi-circle-fill me-2"></i> Be at max 32 characters</li>
            <li><i class="bi bi-circle-fill me-2"></i> Include at least one UPPERCASE letter</li>
            <li><i class="bi bi-circle-fill me-2"></i> Include at least one lowercase letter</li>
            <li><i class="bi bi-circle-fill me-2"></i> Include at least one number</li>
            <li><i class="bi bi-circle-fill me-2"></i> Include at least one special character, such as ~`!@#$%^&amp;*()?&lt;&gt;{}|[]\/-+</li>
        </ul>


        <div class="mb-3">
            <label for="current_password" class="col-form-label text-uppercase">Current Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="current_password" name="current_password">
                <button type="button" class="btn btn-outline-secondary password-toggle-btn" data-target="#current_password">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="col-form-label text-uppercase">New Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password" name="password">
                <button type="button" class="btn btn-outline-secondary password-toggle-btn" data-target="#password">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="col-form-label text-uppercase">Confirm New Password</label>
            <div class="input-group">
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                <button type="button" class="btn btn-outline-secondary password-toggle-btn" data-target="#password_confirmation">
                    <i class="bi bi-eye-slash"></i>
                </button>
            </div>
        </div>


    </div>
    <div class="modal-footer border-0 p-0">
        <button type="button" class="btn btn-secondary text-uppercase" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary text-uppercase update_profile" data-formid="passwordForm">Save</button>
    </div>
</form>
</div>
</div>
</div>
@endsection