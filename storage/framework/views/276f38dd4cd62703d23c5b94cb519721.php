 
<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                
                <div class="card-header fw-bold">
                    <?php if($user->first_name || $user->last_name): ?>
                        <?php echo e($user->first_name); ?> <?php echo e($user->last_name); ?>

                    <?php else: ?>
                        <?php echo e($user->email); ?>

                    <?php endif; ?> 
                    <?php echo e(__(' - Packages & Subscriptions')); ?>

                </div>

                <div class="card-body">

                    
                    <?php if(session('status')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo e(session('status')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    
                    <?php if($allRecords->isEmpty()): ?>
                        <div class="alert alert-info">No package or subscription records found.</div>
                    <?php else: ?>
                        <div class="table-responsive mt-3">
                            <table class="table table-striped table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Id</th>
                                        <th>Type</th>
                                        <th>Plan</th>
                                        <th>Status</th>
                                        <th>Started</th>
                                        <th>Ends</th>
                                        <th>Stripe ID</th>
                                    </tr>
                                </thead>

                                <tbody>
                                <?php $__currentLoopData = $allRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $record): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($record->id); ?></td>
                                        <td class="fw-bold text-uppercase">
                                            <?php echo e($record->type ?? 'subscription'); ?>

                                        </td>

                                        <td>
                                            <?php echo e($record->plan->name ?? ($record->name ?? 'N/A')); ?>

                                        </td>

                                        <td>
                                            <?php
                                                $status = $record->status ?? $record->stripe_status ?? 'N/A';
                                                $badgeClass = $status == 'active' ? 'bg-success' : 'bg-danger';
                                            ?>
                                            <span class="badge <?php echo e($badgeClass); ?>"><?php echo e(ucfirst($status)); ?></span>
                                        </td>

                                        <td><?php echo e($record->starts_at ? $record->starts_at->format('d M Y') : '—'); ?></td>

                                        <td><?php echo e($record->ends_at ? $record->ends_at->format('d M Y') : '—'); ?></td>

                                        <td><?php echo e($record->stripe_id ?? '—'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>

                            </table>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/user/packagessubscriptions.blade.php ENDPATH**/ ?>