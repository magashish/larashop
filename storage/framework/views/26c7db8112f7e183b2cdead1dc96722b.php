<div class="table-responsive">
    <table class="table  align-middle custom-table">
        <thead class="table-light">
            <tr>
                <th>Title</th>
                <th>Logo</th>
                <th>Total Offers</th>
                <th>Rejected Offers</th>
                <th>Pending Offers</th>
                <th>Active Offers</th>
                <th>Active Users    </th>
                <th class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $organisations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $organisation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="<?php echo e($class); ?>">
                <td><?php echo e($organisation->title); ?></td>
                <td>
                    <?php if($organisation->image): ?>
                    <img src="<?php echo e(Storage::url($organisation->image)); ?>" alt="Org Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                    <?php else: ?>
                    <img src="<?php echo e(asset('images/demo.jpg')); ?>" alt="Org Image" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                    <?php endif; ?>
                </td>
                <td><?php echo e($organisation->allOffers->count()); ?></td>
                <td><?php echo e($organisation->rejectedOffers->count()); ?></td>
                <td><?php echo e($organisation->pendingOffers->count()); ?></td>
                <td><?php echo e($organisation->activeOffers->count()); ?></td>
                <td><?php echo e($organisation->activeAllassociatedUsers->count()); ?></td>
                <td class="text-end">

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view offers')): ?>
                   <a href="<?php echo e(route('organisations.show', $organisation->id)); ?>" class="btn btn-sm btn-outline-dark custom-action-btn me-1 mb-1">
                    <i class="bi bi-list-columns-reverse"></i> View all Offers
                </a>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit organisations')): ?>
                <a href="<?php echo e(route('organisations.edit', $organisation->id)); ?>" class="btn btn-sm btn-outline-dark custom-action-btn me-1 mb-1"><i class="bi bi-pencil"></i> Edit</a>
                <?php endif; ?>

                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete organisations')): ?>
                <form action="<?php echo e(route('organisations.destroy', $organisation->id)); ?>" method="POST" style="display:inline-block;">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-sm btn-outline-dark custom-action-btn me-2 mb-1"
                    onclick="return confirm('Are you sure you want to delete this organisation?')"><i class="bi bi-trash"></i> Delete</button>
                </form> 
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="11" class="text-center py-4">No organisations found for this status.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>
</div>

<?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/organisation/table_partial.blade.php ENDPATH**/ ?>