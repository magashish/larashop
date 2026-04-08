 

<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold"><?php echo e(__('Manage Promo Codes')); ?></div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo e(session('success')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <div class="mb-3 text-end">
                        <a href="<?php echo e(route('promo_codes.create')); ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle"></i> Create New Promo Code
                        </a>
                    </div>

                    <?php if($distinctInfluencerNames->isEmpty()): ?>
                    <div class="alert alert-info" role="alert">
                        No promo codes found or no influencers assigned yet.
                    </div>
                    <?php else: ?>
                    <?php $__currentLoopData = $distinctInfluencerNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $influencerRecord): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                    $influencerName = $influencerRecord->influencer;
                    $codesForThisInfluencer = $promoCodesByInfluencer->get($influencerName, collect());
                    ?>

                    <div class="card mb-4 custom-table-card">
                        <div class="card-header fw-bold"><?php echo e($influencerName ?: 'Unassigned Influencer'); ?></div>

                        <div class="card-body p-0">
                            <?php if($codesForThisInfluencer->isNotEmpty()): ?>
                            <div class="table-responsive">
                                <table class="table custom-table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Promo Code</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Subscription Payment Status </th>
                                            <th>Promo Code Payment Status   </th>
                                            <th>Number of Orders    </th>
                                            <th>Total</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $codesForThisInfluencer; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $promoCode): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($promoCode->promo_code); ?></td>
                                            <td><?php echo e(\Carbon\Carbon::parse($promoCode->start_date)->format('Y-m-d')); ?></td>
                                            <td><?php echo e(\Carbon\Carbon::parse($promoCode->end_date)->format('Y-m-d')); ?></td>
                                            <td>----</td>
                                            <td>----</td>
                                            <td>----</td>
                                            <td>----</td>
                                            <td class="text-end">
                                                <a href="<?php echo e(route('promo_codes.edit', $promoCode->id)); ?>" class="btn btn-sm btn-outline-dark custom-action-btn me-2">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <form action="<?php echo e(route('promo_codes.destroy', $promoCode->id)); ?>" method="POST" style="display:inline-block;">
                                                    <?php echo csrf_field(); ?>
                                                    <?php echo method_field('DELETE'); ?>
                                                    <button type="submit" class="btn btn-sm btn-outline-dark custom-action-btn"
                                                    onclick="return confirm('Are you sure you want to delete this promo code?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                        <?php else: ?>
                        <p class="p-3 text-muted">No promo codes found for this influencer on this page.</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/promo-codes/index.blade.php ENDPATH**/ ?>