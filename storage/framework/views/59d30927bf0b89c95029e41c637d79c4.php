<?php
$useCustomRegister = $customRegister ?? false;
?>
<div class="package-plans">
    <div class="container">
        <div class="row g-sm-4 g-3">
            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
            $price = number_format($plan->price, 2, '.', '');
            [$whole, $decimal] = explode('.', $price);

            $planName = strtolower($plan->name);
            switch ($planName) {
                case 'bronze':
                $buttonText = "Start with {$plan->name} - \${$whole}";
                break;
                case 'silver':
                $buttonText = "Choose {$plan->name} - \${$whole}";
                break;
                case 'gold':
                $buttonText = "Buy {$plan->name} - \${$whole}";
                break;
                case 'subscription':
                $buttonText = "Go {$plan->name} - \${$whole}";
                break;
                default:
                $buttonText = "Start with {$plan->name} - \${$whole}";
            }
            ?>
            <?php if(!empty($plan->read_more)): ?>
            <div class="modal fade" id="planModal-<?php echo e($plan->id); ?>" tabindex="-1" aria-labelledby="planModalLabel-<?php echo e($plan->id); ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content <?php echo e(strtolower($plan->name)); ?> position-relative">
                        <div class="access-box <?php echo e(strtolower($plan->name)); ?>">
                            <div class="inner-wrapper">
                                <button type="button" class="btn-close position-absolute" style="top: 15px; right: 15px; z-index: 10;" data-bs-dismiss="modal" aria-label="Close"></button>
                                <div>
                                    <div class="access-title">
                                        <p class="mb-0"><?php echo e($plan->name); ?></p>
                                    </div>
                                    <div class="package-desc mt-3">
                                        <div class="plan-details py-2 px-3">
                                            <?php echo $plan->read_more; ?>

                                        </div>
                                    </div>
                                </div>
                                <div class="access-btn">
                                    


                    <?php if(auth()->check()): ?>
                    <a href="<?php echo e(route('subscription.show', $plan->id)); ?>">
                        <?php echo $buttonText; ?>

                    </a>
                    <?php else: ?>
                    <a class="read-more-circle"
                    data-bs-toggle="modal"
                    data-bs-target="#subscriptionplanModal-<?php echo e($plan->id); ?>">
                       <?php echo $buttonText; ?>

                   </a>
                <?php endif; ?>

                
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>






<?php
$useCustomRegister = $customRegister ?? false;
?>
<div class="package-plans">
    <div class="container">
        <div class="row g-sm-4 g-3">
            <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
            $price = number_format($plan->price, 2, '.', '');
            [$whole, $decimal] = explode('.', $price);

            $planName = strtolower($plan->name);
            switch ($planName) {
                case 'bronze':
                $buttonText = "Start with {$plan->name} - \${$whole}";
                break;
                case 'silver':
                $buttonText = "Choose {$plan->name} - \${$whole}";
                break;
                case 'gold':
                $buttonText = "Buy {$plan->name} - \${$whole}";
                break;
                case 'subscription':
                $buttonText = "Go {$plan->name} - \${$whole}";
                break;
                default:
                $buttonText = "Start with {$plan->name} - \${$whole}";
            }
            ?>
            <?php if(!empty($plan->read_more)): ?>
            <div class="modal fade" id="subscriptionplanModal-<?php echo e($plan->id); ?>" tabindex="-1" aria-labelledby="subscriptionplanModalLabel-<?php echo e($plan->id); ?>" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content <?php echo e(strtolower($plan->name)); ?> position-relative">
                        <div class="access-box <?php echo e(strtolower($plan->name)); ?>">
                            <div class="inner-wrapper">
                                <button type="button" class="btn-close position-absolute" style="top: 15px; right: 15px; z-index: 10;" data-bs-dismiss="modal" aria-label="Close"></button>
                                <div>
                                    <div class="access-title">
                                        <p class="mb-0"><?php echo e($plan->name); ?></p>
                                    </div>
                                    <div class="package-desc mt-3">
                                        <div class="plan-details py-2 px-3 payment-modal">
                                            
                                           <?php echo $__env->make('consumer.blocks.pop-up-subscription-register-form', ['formId' => $plan->id,'buttonText'=>$buttonText], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                       </div>
                                   </div>
                               </div>
                              
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
</div><?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/consumer/blocks/pop-up-subscription.blade.php ENDPATH**/ ?>