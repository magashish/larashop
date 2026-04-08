<?php $__env->startSection('content'); ?>


<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold"><?php echo e(__('Manage Offers')); ?></div>

                <div class="card-body">
                    <?php if(session('status')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo e(session('status')); ?>

                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <ul class="mb-0">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>


                 <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create offers')): ?>
                    <div class="card mb-4">
                        <div class="card-header fw-bold"> Add Multiple Offers via CSV </div>
                        <div class="card-body">
                            <a href="<?php echo e(route('offers.uploadcsv')); ?>" class="btn btn-dark text-white">
                                <i class="bi bi-file-earmark-spreadsheet"></i> Upload CSV
                            </a>
                        </div>
                    </div>
                    <div class="mb-3 text-end">
                        <a href="<?php echo e(route('offers.create')); ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Add New Offer
                        </a>
                    </div>
                    <?php endif; ?>


                    <div class="card mb-4">
                        <div class="card-body">
                            <form>
                                <div class="input-group">
                                    <div class="form-floating">
                                        <input
                                        type="text"
                                        name="business"
                                        id="business"
                                        class="form-control"
                                        placeholder="Search Businesses Here"
                                        value="<?php echo e(request('business')); ?>"
                                        >
                                        <label for="business">Search Businesses Here</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>


                    <div>
                        <ul class="nav nav-pills nav-fill mb-3" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending" type="button" role="tab" aria-controls="pending" aria-selected="true">
                                    <i class="bi bi-clock-history me-1"></i> Pending Approval
                                    <span class="badge rounded-pill bg-warning text-dark ms-1">
                                        <?php echo e(count($offer_new)); ?>

                                    </span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="expired-tab" data-bs-toggle="tab" data-bs-target="#expired" type="button" role="tab" aria-controls="expired" aria-selected="false">
                                    <i class="bi bi-calendar-x me-1"></i> Expired
                                    <span class="badge rounded-pill bg-danger ms-1">
                                        <?php echo e(count($offer_expire)); ?>

                                    </span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="live-tab" data-bs-toggle="tab" data-bs-target="#live" type="button" role="tab" aria-controls="live" aria-selected="false">
                                    <i class="bi bi-check-circle me-1"></i> Live
                                    <span class="badge rounded-pill bg-success ms-1">
                                        <?php echo e(count($live_offers)); ?>

                                    </span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="rejected-tab" data-bs-toggle="tab" data-bs-target="#rejected" type="button" role="tab" aria-controls="rejected" aria-selected="false">
                                    <i class="bi bi-x-circle me-1"></i> Rejected
                                    <span class="badge rounded-pill bg-secondary ms-1">
                                        <?php echo e(count($offer_rejected)); ?>

                                    </span>
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="pending" role="tabpanel" aria-labelledby="pending-tab">
                                <?php echo $__env->make('offers.table_partial', ['offers' => $offer_new ,'class' => 'warning' ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                            <div class="tab-pane fade" id="expired" role="tabpanel" aria-labelledby="expired-tab">
                                <?php echo $__env->make('offers.table_partial', ['offers' => $offer_expire ,'class' => 'expired' ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                            <div class="tab-pane fade" id="live" role="tabpanel" aria-labelledby="live-tab">
                                <?php echo $__env->make('offers.table_partial', ['offers' => $live_offers ,'class' => 'success' ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                            <div class="tab-pane fade" id="rejected" role="tabpanel" aria-labelledby="rejected-tab">
                                <?php echo $__env->make('offers.table_partial', ['offers' => $offer_rejected ,'class' => 'danger' ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/offers/index.blade.php ENDPATH**/ ?>