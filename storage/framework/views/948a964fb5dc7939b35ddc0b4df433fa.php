 <?php if($offers->isEmpty()): ?>
 <div class="alert alert-info" role="alert">
    No offers found.
</div>
<?php else: ?>
<div class="table-responsive">
    <table class="table  align-middle custom-table">
        <thead class="table-light">
            <tr>
                <th>No.</th>
                <th>Offer Category</th>
                <th>Offer Title </th>
                <th>Business</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Attachments</th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $offers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index =>$offer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
           <tr class="<?php echo e($class); ?>">
               <td><?php echo e($index); ?></td>
                <td>
                    <?php $__empty_1 = true; $__currentLoopData = $offer->categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    
                    <span><?php echo e($category->name); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <span class="text-muted">No Categories</span>
                    <?php endif; ?>
                </td>
                <td><?php echo e($offer->title); ?></td>
                <td><?php echo e($offer->organisation->title ?? 'N/A'); ?></td>

                <td>
                    <?php if(!empty($offer->start_date)): ?>
                    <?php echo e(\Carbon\Carbon::parse($offer->start_date)->format('F j, Y')); ?>

                    <?php else: ?>
                    N/A
                    <?php endif; ?>
                </td>
                <td>
                    <?php if(!empty($offer->end_date)): ?>
                    <?php echo e(\Carbon\Carbon::parse($offer->end_date)->format('F j, Y')); ?>

                    <?php else: ?>
                    N/A
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo e($offer->attachments->count()); ?>

                </td>

                <td class="text-end">

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit offers')): ?>
                    <a href="<?php echo e(route('offers.edit', $offer->id)); ?>" class="btn btn-sm btn-outline-dark custom-action-btn me-2"><i class="bi bi-pencil"></i> Edit</a>
                    <?php endif; ?>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete offers')): ?>
                    <form action="<?php echo e(route('offers.destroy', $offer->id)); ?>" method="POST" style="display:inline-block;">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-sm btn-outline-dark custom-action-btn me-2"
                        onclick="return confirm('Are you sure you want to delete this offer?')"><i class="bi bi-trash"></i> Delete</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>

<?php endif; ?><?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/offers/table_partial.blade.php ENDPATH**/ ?>