<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-body">

                  <div class="alert alert-info d-flex align-items-center">
                    <i class="bi bi-person-fill fs-1 me-3"></i>
                    <div>
                        Logged in as <strong><?php echo e(Auth::guard('admin')->user()->email); ?></strong> with 
                        <strong><?php echo e(Auth::guard('admin')->user()->level); ?></strong> level access
                    </div>
                </div>

                <?php if(session('status')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('status')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <div class="container-fluid stats_dashboard text-center">
                    <div class="row g-4">

                        <!-- ==================== Subscribers Section ==================== -->
                        <div class="col-md-6">
                            <div class="p-3 h-100 bg-default rounded shadow-sm">
                                <h5 class="stat_title mb-4">Subscribers</h5>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="stat"><?php echo e(get_total_user_count()); ?></div>
                                                <div class="stat_description">Total Consumers</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="stat"><?php echo e(getDailyActiveConsumers()); ?></div>
                                                <div class="stat_description">Daily Active Consumers</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="stat"><?php echo e(getPayingSubscribers()); ?></div>
                                                <div class="stat_description">Active Subscribers</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="stat"><?php echo e(getInactiveSubscribersCount()); ?></div>
                                                <div class="stat_description">InActive Subscribers</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ==================== Offers Section ==================== -->
                        <div class="col-md-6">
                            <div class="p-3 h-100 bg-default rounded shadow-sm">
                                <h5 class="stat_title mb-4">Offers</h5>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="stat"><?php echo e(get_total_organisations_count()); ?></div>
                                                <div class="stat_description">Total Businesses</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="stat"><?php echo e(get_total_offer_count()); ?></div>
                                                <div class="stat_description">Total Offers</div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    $organisations = organisations_data();
                                    ?>

                                    <div class="col-md-2 col-sm-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="stat"><?php echo e($organisations['new']); ?></div>
                                                <div class="stat_description">New</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-sm-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="stat"><?php echo e($organisations['approved']); ?></div>
                                                <div class="stat_description">Approved</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-sm-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="stat"><?php echo e($organisations['rejected']); ?></div>
                                                <div class="stat_description">Rejected</div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php
                                    $offer = offer_data();
                                    ?>

                                    <div class="col-md-2 col-sm-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="stat"><?php echo e($offer['expired']); ?></div>
                                                <div class="stat_description">Expired</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-sm-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="stat"><?php echo e($offer['live']); ?></div>
                                                <div class="stat_description">Live</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-2 col-sm-6">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="stat"><?php echo e($offer['rejected']); ?></div>
                                                <div class="stat_description">Rejected</div>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <?php
                $planIds = [8, 20, 21];

                $counts = getLiveCountsByPlanIds($planIds);

                $goldCount   = $counts[8] ?? 0;
                $silverCount = $counts[20] ?? 0;
                $bronzeCount = $counts[21] ?? 0;

                $totalLive = $goldCount + $silverCount + $bronzeCount;
                ?>

                <div class="row g-4 mt-4 stats_dashboard">
                    <div class="col-md-12">
                        <div class="p-4 h-100 bg-default rounded shadow-sm border">
                            <h5 class="stat_title mb-4 fw-bold text-dark text-center position-relative">
                                Live Packages Overview
                            </h5>


                            <div class="row g-4">

                                <!-- Total Live -->
                                <div class="col-md-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <div class="stat fs-2 fw-bold">
                                                <?php echo e($totalLive); ?>

                                            </div>
                                            <div class="stat_description text-muted">
                                                Total Live Packages
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bronze -->
                                <div class="col-md-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <span class="badge bg-warning text-dark mb-2 px-3 py-2">
                                                Bronze
                                            </span>
                                            <div class="stat fs-3 fw-bold">
                                                <?php echo e($bronzeCount); ?>

                                            </div>
                                            <div class="stat_description text-muted">
                                                Active Packages
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Silver -->
                                <div class="col-md-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <span class="badge bg-warning mb-2 px-3 py-2">
                                                Silver
                                            </span>
                                            <div class="stat fs-3 fw-bold">
                                                <?php echo e($silverCount); ?>

                                            </div>
                                            <div class="stat_description text-muted">
                                                Active Packages
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Gold -->
                                <div class="col-md-3">
                                    <div class="card h-100 border-0 shadow-sm">
                                        <div class="card-body text-center">
                                            <span class="badge bg-warning mb-2 px-3 py-2">
                                                Gold
                                            </span>
                                            <div class="stat fs-3 fw-bold">
                                                <?php echo e($goldCount); ?>

                                            </div>
                                            <div class="stat_description text-muted">
                                                Active Packages
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>





            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>