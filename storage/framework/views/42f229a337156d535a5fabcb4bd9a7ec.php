<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold"><?php echo e(__('User / Edit')); ?></div>
                <div class="card-body">
                    <?php if(session('status')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo e(session('status')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    <div class="mb-3 text-end">
                        <a href="<?php echo e(route('users.index')); ?>" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Users Admin
                        </a>
                    </div>
                    <form method="POST" action="<?php echo e(route('users.update', $user->id)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                    


                         <div class="row">
                            <div class="col-md-6"> 
                                <div class="mb-3">
                                    <label for="first_name" class="form-label fw-semibold">First Name</label>
                                    <input id="first_name" type="text"
                                    class="form-control <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    name="first_name" value="<?php echo e(old('first_name', $user->first_name)); ?>"
                                    placeholder="Enter first name" required>
                                    <?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block">
                                        <?php echo e($message); ?>

                                    </div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            <div class="col-md-6"> 
                                <div class="mb-3">
                                    <label for="last_name" class="form-label fw-semibold">Last Name</label>
                                    <input id="last_name" type="text"
                                    class="form-control <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    name="last_name" value="<?php echo e(old('last_name', $user->last_name)); ?>"
                                    placeholder="Enter last name" required>
                                    <?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback d-block">
                                        <?php echo e($message); ?>

                                    </div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label fw-semibold">Email Address</label>
                            <input id="email" type="email"
                            class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            name="email" value="<?php echo e(old('email', $user->email)); ?>"
                            placeholder="Enter email" required readonly> 
                            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block">
                                <?php echo e($message); ?>

                            </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label fw-semibold">New Password <small class="text-muted">(leave blank to keep current)</small></label>
                            <input id="password" type="password"
                            class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            name="password"
                            placeholder="Enter new password"
                            autocomplete="new-password"> 
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block">
                                <?php echo e($message); ?>

                            </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="mb-4">
                            <label for="password-confirm" class="form-label fw-semibold">Confirm New Password</label>
                            <input id="password-confirm" type="password"
                            class="form-control"
                            name="password_confirmation"
                            placeholder="Re-enter new password"
                            autocomplete="new-password"> 
                        </div>

                        

                        <div class="mb-3">
                            <label for="level" class="form-label fw-semibold">Level</label>
                            <select id="level" name="level" class="form-select <?php $__errorArgs = ['level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <?php $__currentLoopData = $webroles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($role->name); ?>"
                                    <?php echo e(old('level', $user->level ?? '') == $role->name ? 'selected' : ''); ?>>
                                    <?php echo e(Str::title(str_replace('-', ' ', $role->name))); ?>

                                </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block">
                                <?php echo e($message); ?>

                            </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>


                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input exclude-switch" type="checkbox"
                            id="exclude_from_prize_draw"
                            name="exclude_from_prize_draw"
                            value="1"
                            <?php echo e(old('exclude_from_prize_draw', $user->exclude_from_prize_draw) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-semibold" for="exclude_from_prize_draw">
                                Exclude User from Prize Draw
                            </label>
                        </div>

                         <div class="mb-3">
                                <label for="mobile" class="form-label fw-semibold">Mobile</label>
                                <input id="mobile" type="text"
                                class="form-control <?php $__errorArgs = ['mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                name="mobile" value="<?php echo e(old('mobile', $user->mobile)); ?>"
                                placeholder="Enter mobile Number" required>
                                <?php $__errorArgs = ['mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block">
                                    <?php echo e($message); ?>

                                </div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>



                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input exclude-switch" type="checkbox"
                                id="show_stripe_id" value="1">
                                <label class="form-check-label fw-semibold" for="show_stripe_id">
                                    Show Stripe Id
                                </label>
                            </div>

                            <div class="mb-3" id="stripeIdWrapper" style="display: none;">
                                <label for="stripe_id" class="form-label fw-semibold">Stripe Id</label>
                                <input id="stripe_id" type="text"
                                class="form-control <?php $__errorArgs = ['stripe_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                name="stripe_id" value="<?php echo e(old('stripe_id', $user->stripe_id)); ?>"
                                placeholder="Enter Stripe Id">
                                <?php $__errorArgs = ['stripe_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback d-block">
                                    <?php echo e($message); ?>

                                </div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>



                        
                        <div class="mb-4">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <select id="status" name="status" class="form-select <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="active" <?php echo e(old('status', $user->status) == 'active' ? 'selected' : ''); ?>>Active</option>
                                <option value="deactive" <?php echo e(old('status', $user->status) == 'deactive' ? 'selected' : ''); ?>>Deactive</option>
                            </select>
                            <?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback d-block">
                                <?php echo e($message); ?>

                            </div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>


                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input exclude-switch" type="checkbox"
                            id="sms_unsubscribed"
                            name="sms_unsubscribed"
                            value="1"
                            <?php echo e(old('sms_unsubscribed', $user->sms_unsubscribed) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-semibold" for="exclude_from_prize_draw">
                                Sms Unsubscribed
                            </label>
                        </div>


                        <!-- <div class="mb-3" >
                            <label for="organisation_search_input" class="form-label fw-semibold">Search Organisation</label>
                            <div class="input-group">
                                <input id="organisation_search_input" type="text"
                                class="form-control"
                                placeholder="Type to search organisations...">
                                <span class="input-group-text" id="search_organisation_btn">
                                    <i class="bi bi-search"></i>
                                </span>
                            </div>

                            <div id="organisation_search_results" class="list-group mt-2"></div>
                            <div id="organisation_all_results" class="list-group mt-2">
                                <table id="user_organisation_table" class="table table-striped table-hover" >
                                    <thead class="tableFloatingHeaderOriginal">
                                        <tr>
                                            <th>Organisation</th>
                                            <th>Organisation Type</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Roles</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $user->associatedOrganisations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $association): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                        $row_id = (string) Str::uuid();
                                        $startDate = $association->pivot->start_date ?? '';
                                        $endDate = $association->pivot->end_date ?? '';

                                        $startDate = $association->pivot->start_date ? $association->pivot->start_date->format('Y-m-d') : '';
                                        $endDate = $association->pivot->end_date ? $association->pivot->end_date->format('Y-m-d') : '';

                                       // $selectedRoles = json_decode($association->pivot->role, true) ?? []; 
                                        //$selectedRoles = []; 
                                        $selectedRoles = $association->pivot->roles->pluck('id')->toArray();

                                        ?>

                                        <tr id="organisation-row-<?php echo e($row_id); ?>">
                                            <td>
                                                <?php echo e($association->title ?? ''); ?>

                                                
                                                <input type="hidden" name="organisation[<?php echo e($row_id); ?>][id]" value="<?php echo e($association->id); ?>">
                                                
                                                <input type="hidden" name="organisation[<?php echo e($row_id); ?>][pivot_id]" value="<?php echo e($association->pivot->id ?? ''); ?>">
                                            </td>
                                            <td>
                                                <?php echo e(ucfirst(str_replace('_', ' ', $association->organisation_address_type ?? 'N/A'))); ?>

                                            </td>
                                            <td>
                                                <input type="date" class="form-control" name="organisation[<?php echo e($row_id); ?>][start_date]" value="<?php echo e($startDate); ?>">
                                            </td>
                                            <td>
                                                <input type="date" class="form-control" name="organisation[<?php echo e($row_id); ?>][end_date]" value="<?php echo e($endDate); ?>">
                                            </td>

                                            <td>
                                                <?php $__currentLoopData = available_roles(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $roleValue => $roleLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="form-check form-check-inline">
                                                    <input type="checkbox"
                                                    class="form-check-input"
                                                    name="organisation[<?php echo e($row_id); ?>][role][]"
                                                    id="role-<?php echo e($row_id); ?>-<?php echo e($roleValue); ?>"
                                                    value="<?php echo e($roleValue); ?>"
                                                    <?php echo e(in_array($roleValue, $selectedRoles) ? 'checked' : ''); ?>>
                                                    <label class="form-check-label" for="role-<?php echo e($row_id); ?>-<?php echo e($roleValue); ?>">
                                                        <?php echo e($roleLabel); ?>

                                                    </label>
                                                </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>

                            </div>
                        </div>
 -->

                        
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        $('#show_stripe_id').change(function () {
            if ($(this).is(':checked')) {
                $('#stripeIdWrapper').slideDown(); 
            } else {
                $('#stripeIdWrapper').slideUp(); 
            }
        });
    });


    $(document).on('change', 'input[name="organisation_id"]', function () {
        $('#add_organisation_trigger').removeClass('d-none'); 
    });
    $(document).on('click', '#cancel_organisation_search', function () {
        $('#organisation_search_results').html('');
    });
    $('#search_organisation_btn').on('click', function () {
        const query = $('#organisation_search_input').val();
        $('#organisation_search_results').show();
        if (query.length === 0) {
            $('#organisation_search_results').html('');
            return;
        }
        $.ajax({
            url: '/search-organisations', 
            method: 'GET',
            data: { q: query },
            success: function (response) {
                let output = `
        <div class="well">
            <a href="javascript:;" id="cancel_organisation_search" class="btn btn-default float-end">
                <i class="bi bi-x-circle"></i> Close
            </a>
            <h4>Search results:</h4>
            <div class="form-group">
                `;

                if (response.length > 0) {
                    response.forEach(function (org) {
                        output += `
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="organisation_id" 
                        id="search_${org.id}" 
                        value="${org.id}" 
                        data-title="${org.title}">
                    <label class="form-check-label" for="search_${org.id}">
                        ${org.title} (business)
                    </label>
                </div>
                        `;
                    });
                } else {
                    output += '<div class="text-muted">No results found</div>';
                }

                output += `
            </div>
            <div class="btn btn-primary mt-2 d-none" id="add_organisation_trigger">
                <i class="bi bi-plus"></i> Add organisation
            </div>
        </div>
                `;

                $('#organisation_search_results').html(output);
            },
            error: function () {
                $('#organisation_search_results').html('<div class="text-danger p-2">Error fetching results</div>');
            }
        });
    });
    $(document).on('click', '#add_organisation_trigger', function () {
        let selected = $('input[name="organisation_id"]:checked');
        if (selected.length > 0) {
            let organisationId = selected.val();
            $.ajax({
                url: `/get-organisations-detail/${organisationId}`,
                method: 'GET',
                success: function (html) {

                    $('#user_organisation_table tbody').append(html); 
                    $('#organisation_search_results').hide();
                    $('#organisation_search_input').val('');
                },
                error: function (xhr) {
                    alert('Failed to fetch organisation details.');
                    console.error(xhr.responseText);
                }
            });
        } else {
            alert('Please select an organisation first.');
        }
    });
    $(document).on('click', '.remove-organisation-row', function() {
        let rowId = $(this).data('row-id'); 
        if (rowId) {
            $(this).closest('tr').remove();
        } 
    });

</script>




<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/user/edit.blade.php ENDPATH**/ ?>