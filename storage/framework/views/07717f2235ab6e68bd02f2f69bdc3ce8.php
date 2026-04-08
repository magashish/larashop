<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header fw-bold"><?php echo e(__('Prize Draws / Edit: ')); ?> <?php echo e($prizeDraw->title); ?></div>

                <div class="card-body">
                    <?php if(session('status')): ?>
                        <div class="alert alert-success alert-dismissible fade show">
                            <?php echo e(session('status')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <div class="mb-3 text-end">
                        <a href="<?php echo e(route('prize_draws.index')); ?>" class="btn btn-primary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to Prize Draws Admin
                        </a>
                    </div>

                    
                    <form action="<?php echo e(route('prize_draws.update', $prizeDraw->id)); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?> 

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Prize Draw Name</label>
                            <input type="text"
                                   class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   name="title"
                                   value="<?php echo e(old('title', $prizeDraw->title)); ?>"
                                   required>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Prize Type</label>
                            <select class="form-control" name="prize_type" id="prize_type" required>
                                <option value="">Select Prize Type</option>
                                <option value="cash" <?php echo e(old('prize_type', $prizeDraw->prize_type) == 'cash' ? 'selected' : ''); ?>>Cash</option>
                                <option value="non_cash" <?php echo e(old('prize_type', $prizeDraw->prize_type) == 'non_cash' ? 'selected' : ''); ?>>Non-Cash</option>
                            </select>
                        </div>

                        
                        <div class="mb-3" id="cash_field" style="display:none;">
                            <label class="form-label fw-semibold">Cash Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number"
                                       step="0.01"
                                       min="0"
                                       class="form-control"
                                       name="cash_amount"
                                       value="<?php echo e(old('cash_amount', $prizeDraw->cash_amount)); ?>">
                            </div>
                        </div>

                        
                        <div class="mb-3" id="non_cash_field" style="display:none;">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Prize Title</label>
                                <input type="text"
                                       class="form-control"
                                       name="prize_title"
                                       value="<?php echo e(old('prize_title', $prizeDraw->prize_title)); ?>"
                                       placeholder="e.g. Mazda CX-5">
                            </div>

                            <div class="mb-3">
                            <label class="form-label fw-semibold">Prize Sub Title</label>
                            <input type="text"
                                   class="form-control"
                                   name="prize_sub_title"
                                   value="<?php echo e(old('prize_sub_title', $prizeDraw->prize_sub_title)); ?>"
                                   placeholder="e.g. Mazda CX-5">
                        </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold"> Non-Cash Prize Value ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number"
                                           step="0.01"
                                           min="0"
                                           class="form-control"
                                           name="prize_value_amount"
                                           value="<?php echo e(old('prize_value_amount', $prizeDraw->prize_value_amount)); ?>">
                                </div>
                            </div>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Prize Description</label>
                            <textarea class="form-control"
                                      name="prize_description"
                                      rows="3"
                                      required><?php echo e(old('prize_description', $prizeDraw->prize_description)); ?></textarea>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Prize Image</label>
                            
                            <?php if($prizeDraw->prize_image): ?>
                                <div class="mb-2">
                                    <img src="<?php echo e(asset('storage/' . $prizeDraw->prize_image)); ?>" alt="Current Prize Image" style="width: 100px; height: auto;" class="img-thumbnail">
                                    <p class="small text-muted">Current image</p>
                                </div>
                            <?php endif; ?>

                            <input type="file"
                                   class="form-control"
                                   name="prize_image"
                                   accept="image/*"> 
                            <small class="text-muted">Leave blank to keep the current image.</small>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Draw Type</label>
                            <select class="form-control" name="draw_type" required>
                                <option value="">Select Draw Type</option>
                                <option value="subscribers" <?php echo e(old('draw_type', $prizeDraw->draw_type) == 'subscribers' ? 'selected' : ''); ?>>Subscribers (Small Giveaway)</option>
                                <option value="packages" <?php echo e(old('draw_type', $prizeDraw->draw_type) == 'packages' ? 'selected' : ''); ?>>Packages (Major Giveaway)</option>
                            </select>
                        </div>

                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Draw Date</label>
                            <input type="text"
                                   class="form-control datetimepicker"
                                   name="draw_date"
                                   value="<?php echo e(old('draw_date', $prizeDraw->draw_date)); ?>"
                                   required>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input exclude-switch" type="checkbox"
                            id="show_on_site"
                            name="show_on_site"
                            value="1"
                            <?php echo e(old('show_on_site', $prizeDraw->show_on_site) ? 'checked' : ''); ?>>
                            <label class="form-check-label fw-semibold" for="show_on_site">
                                Show on site 
                            </label>
                        </div>



                        <button type="submit" name="action" value="update" class="btn btn-warning">
                            <i class="bi bi-save"></i> Update Prize Draw
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.input-group-text { background-color: #f8f9fa; border-right: none; }
.input-group .form-control { border-left: none; }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
$(document).ready(function () {
    function togglePrizeFields(type) {
        $('#cash_field').hide();
        $('#non_cash_field').hide();

        if (type === 'cash') {
            $('#cash_field').show();
        } else if (type === 'non_cash') {
            $('#non_cash_field').show();
        }
    }

    // Trigger on load to show fields based on existing data
    togglePrizeFields($('#prize_type').val());

    $('#prize_type').on('change', function () {
        togglePrizeFields(this.value);
    });
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/prize-draws/edit.blade.php ENDPATH**/ ?>