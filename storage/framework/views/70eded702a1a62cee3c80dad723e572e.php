<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold"> <?php echo e(__('Manage Users')); ?> </div>
                <div class="card-body">
                    <?php if(session('status')): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 rounded-3 auto-dismiss" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i> 
                        <?php echo e(session('status')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    <div class="mb-3 text-end">
                        <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add a User
                        </a>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header fw-bold"> <?php echo e(__('Export Users')); ?> </div>
                        <div class="card-body">
                            <p>Click the button below to generate a CSV of all users.</p>
                             <a href="<?php echo e(route('users.export_csv', request()->query())); ?>" class="btn btn-dark text-white"> 
                                <i class="bi bi-file-earmark-spreadsheet"></i> Generate CSV
                            </a>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <form>
                                <div class="input-group">
                                    <div class="form-floating">
                                        <input
                                        type="text"
                                        name="email"
                                        class="form-control"
                                        placeholder="Search Email address"
                                        value="<?php echo e(request('email')); ?>"
                                        >
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>


                    <ul class="nav nav-pills nav-fill mb-3" role="tablist">
                        
                        <li class="nav-item" role="presentation">
                            <a href="<?php echo e(route('users.index')); ?>" class="nav-link <?php echo e(!request()->hasAny(['sort_by', 'level' ,'live']) ? 'active' : ''); ?>">
                                <i class="bi bi-list-ul me-1"></i> All Users
                            </a>
                        </li>

                        <li class="nav-item" role="presentation">
                            <a href="<?php echo e(route('users.index', ['live' => 'live'])); ?>" class="nav-link <?php echo e(request('live') == 'live' ? 'active' : ''); ?>">
                                <i class="bi bi-person-circle me-1"></i> Users with Active Packages/Subscriptions
                            </a>
                        </li>

                        <li class="nav-item" role="presentation">
                            <a href="<?php echo e(route('users.index', ['live' => 'expire'])); ?>" class="nav-link <?php echo e(request('live') == 'expire' ? 'active' : ''); ?>">
                                <i class="bi bi-person-check-fill me-1"></i> Users with Expired Packages/Subscriptions
                            </a>
                        </li>

                        <li class="nav-item" role="presentation">
                            <a href="<?php echo e(route('users.index', ['live' => 'inactive'])); ?>" class="nav-link <?php echo e(request('live') == 'inactive' ? 'active' : ''); ?>">
                                <i class="bi bi-person-check-fill me-1"></i> Users Without Active Packages/Subscriptions
                            </a>
                        </li>

                        <li class="nav-item" role="presentation">
                            <a href="<?php echo e(route('users.index', ['live' => 'smssubscribe'])); ?>" class="nav-link <?php echo e(request('live') == 'smssubscribe' ? 'active' : ''); ?>">
                                <i class="bi bi-person-check-fill me-1"></i> Sms subscribed
                            </a>
                        </li>

                        
                        
                        
                        
                    </ul>




                    <div class="table-responsive">
                        <table class="table  align-middle custom-table">
                            <thead class="table-light">
                                <tr>
                                    
                                    <th>Name </th>
                                    <th>Email</th>
                                    <th>Mobile</th>
                                    
                                    <th>User status</th>
                                    
                                    <th>Exclude From Prize Draw</th>
                                    <th>Sms Unsubscribed</th>
                                    <th>Two Factor Code</th>
                                    
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    
                                    <td><?php echo e($user->name 
                                        ?: trim($user->first_name . ' ' . $user->last_name) 
                                        ?: $user->email); ?>

                                </td>
                                <td><?php echo e($user->email); ?></td>
                                <td><?php echo e($user->mobile); ?></td>
                                
                                <td><?php echo e($user->status); ?></td>
                                
                                <td>
                                    <?php if($user->exclude_from_prize_draw): ?>
                                    <span class="badge bg-danger">Yes</span>
                                    <?php else: ?>
                                    <span class="badge bg-success">No</span>
                                    <?php endif; ?>
                                </td>

                                <td>
                                    <?php if($user->sms_unsubscribed): ?>
                                    <span class="badge bg-danger">Yes</span>
                                    <?php else: ?>
                                    <span class="badge bg-success">No</span>
                                    <?php endif; ?>
                                </td>

                                <td><?php echo e($user->two_factor_code); ?></td>
                                
                                <td>

                                    <a href="<?php echo e(route('userpackagessubscriptions', $user->id)); ?>" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                        <i class="bi bi-file-earmark-bar-graph"></i> Packages & Subscriptions
                                    </a>

                                    <a href="<?php echo e(route('userActivity', $user->id)); ?>" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                        <i class="bi bi-file-earmark-bar-graph"></i> Activity
                                    </a>

                                    <a href="<?php echo e(route('users.entries', $user->id)); ?>" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                        <i class="bi bi-file-earmark-bar-graph"></i> Entries ( <?php echo e($user->main_entries_count); ?>)
                                    </a>
                                    <a href="<?php echo e(route('users.edit', $user->id)); ?>" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="<?php echo e(route('admin.logs.show', ['users',$user->id])); ?>" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                        <i class="bi bi-clipboard-data"></i> Log
                                    </a>
                                    <form action="<?php echo e(route('users.destroy', $user->id)); ?>" method="POST"
                                      class="d-inline" onsubmit="return confirm('Are you sure?');">
                                      <?php echo csrf_field(); ?>
                                      <?php echo method_field('DELETE'); ?>
                                      <button type="submit" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                        <i class="bi bi-trash"></i> Delete
                                      </button>
                                   </form> 
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">No users found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            
                    <?php if($users->hasPages()): ?>
                        <div class="d-flex justify-content-center mt-4">
                            <nav aria-label="Page navigation">
                                <ul class="pagination pagination-dark pagination-sm">
                                    
                                    <?php echo e($users->links('pagination::bootstrap-4')); ?>

                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                    
        </div>  
    </div> 
</div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/user/index.blade.php ENDPATH**/ ?>