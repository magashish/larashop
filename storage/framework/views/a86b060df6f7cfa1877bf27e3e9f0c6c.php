 

<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold"><?php echo e(__('Prize Draws')); ?></div>
                <div class="card-body">
                    <?php if(session('status')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo e(session('status')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3 text-end">
                        <a href="<?php echo e(route('prize_draws.create')); ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Create New Prize Draw
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table custom-table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title </th>
                                    <th>Prize Draw Imagw </th>
                                    <th>Prize Description</th>
                                    <th>Prize Draw Date </th>
                                    <th>Draw Type </th>
                                    <th>Winner Finalised </th>
                                    <th>Show on Site</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $prizeDraws; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $draw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><?php echo e($draw->title); ?></td> 
                                    <td><?php echo $draw->prize_description ?? ''; ?></td>
                                    <td>
                                        <?php if($draw->prize_image): ?>
                                        <img src="<?php echo e(Storage::url($draw->prize_image)); ?>" alt="Org Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                        <img src="<?php echo e(asset('images/demo.jpg')); ?>" alt="Org Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo e($draw->draw_date->format('F d, Y h:i A')); ?></td>
                                    <td><?php echo e(Str::title($draw->draw_type)); ?></td> 
                                    <td>
                                        <?php if($draw->winner): ?>

                                        <?php echo e($draw->winner->name
                                            ? $draw->winner->name
                                            : ($draw->winner->first_name
                                                ? $draw->winner->first_name . ' ' . $draw->winner->last_name
                                                : $draw->winner->email)); ?>


                                            <?php if($draw->winner_user_finalised == 'yes'): ?>
                                            <span class="badge bg-success ms-2">Finalised</span>
                                            <?php else: ?>
                                            <span class="badge bg-warning text-dark ms-2">Not Finalised Yet</span>
                                            <?php endif; ?>
                                            <?php else: ?>
                                            No winner yet
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($draw->show_on_site): ?>
                                            <span class="badge bg-success">Yes</span>
                                            <?php else: ?>
                                            <span class="badge bg-secondary">No</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <a href="<?php echo e(route('prize_draws.edit', $draw->id)); ?>" class="btn btn-sm btn-outline-dark custom-action-btn me-2"><i class="bi bi-pencil"></i> Edit</a>
                                            <a href="<?php echo e(route('prize_draws.show', $draw->id)); ?>" class="btn btn-sm btn-outline-dark custom-action-btn me-2"><i class="bi bi-pencil"></i> Draws</a> 
                                            <form action="<?php echo e(route('prize_draws.destroy', $draw->id)); ?>" method="POST" style="display:inline-block;">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-sm btn-outline-dark custom-action-btn me-2"
                                                onclick="return confirm('Are you sure you want to delete this offer?')"><i class="bi bi-trash"></i> Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No prize draws found.</td>
                                    </tr>
                                    <?php endif; ?>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/prize-draws/index.blade.php ENDPATH**/ ?>