<!-- Dynamic Slider with BG, FG, Mobile BG, and Text -->
<div id="slider-container" class="mb-4">
    <h5 class="mt-4 mb-3">Hero Slider Slides</h5>
    
    @php
        $existingSlides = json_decode($metaData['hero_dynamic_slider'] ?? '[]', true);
    @endphp
    
    <div id="slides-wrapper">
        @if(!empty($existingSlides))
            @foreach($existingSlides as $index => $slide)
                <div class="slide-item card p-3 mb-3 border" data-slide-index="{{ $index }}">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="mb-0 slide-number">Slide {{ $index + 1 }}</h6>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeSlide(this)">
                            <i class="bi bi-trash"></i> Remove
                        </button>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Background Image (Desktop)</label>
                            <input type="file" name="slider_bg_new[]" class="form-control" accept="image/*">
                            @if(!empty($slide['bg_image']))
                                <div class="mt-2 existing-image-preview">
                                    <img src="{{ Storage::url($slide['bg_image']) }}" alt="BG" class="img-thumbnail" style="max-height: 80px;">
                                    <input type="hidden" name="slider_bg_existing[]" value="{{ $slide['bg_image'] }}">
                                    <div class="form-check mt-1">
                                        <input type="checkbox" name="slider_bg_remove[]" value="{{ $index }}" class="form-check-input" id="remove_bg_{{ $index }}">
                                        <label class="form-check-label small" for="remove_bg_{{ $index }}">Remove</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Background Image (Mobile)</label>
                            <input type="file" name="slider_bg_mobile_new[]" class="form-control" accept="image/*">
                            @if(!empty($slide['bg_mobile_image']))
                                <div class="mt-2 existing-image-preview">
                                    <img src="{{ Storage::url($slide['bg_mobile_image']) }}" alt="Mobile BG" class="img-thumbnail" style="max-height: 80px;">
                                    <input type="hidden" name="slider_bg_mobile_existing[]" value="{{ $slide['bg_mobile_image'] }}">
                                    <div class="form-check mt-1">
                                        <input type="checkbox" name="slider_bg_mobile_remove[]" value="{{ $index }}" class="form-check-input" id="remove_bg_mobile_{{ $index }}">
                                        <label class="form-check-label small" for="remove_bg_mobile_{{ $index }}">Remove</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Foreground Image</label>
                            <input type="file" name="slider_fg_new[]" class="form-control" accept="image/*">
                            @if(!empty($slide['fg_image']))
                                <div class="mt-2 existing-image-preview">
                                    <img src="{{ Storage::url($slide['fg_image']) }}" alt="FG" class="img-thumbnail" style="max-height: 80px;">
                                    <input type="hidden" name="slider_fg_existing[]" value="{{ $slide['fg_image'] }}">
                                    <div class="form-check mt-1">
                                        <input type="checkbox" name="slider_fg_remove[]" value="{{ $index }}" class="form-check-input" id="remove_fg_{{ $index }}">
                                        <label class="form-check-label small" for="remove_fg_{{ $index }}">Remove</label>
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Slide Text</label>
                            <textarea name="slider_text[]" class="form-control" placeholder="Enter slide title" rows="3">{{ $slide['text'] ?? '' }}</textarea>
                            <input type="hidden" name="slider_index[]" value="{{ $index }}">
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
    
    <!-- Template for new slides (hidden) -->
    <div id="slide-template" style="display: none;">
        <div class="slide-item card p-3 mb-3 border" data-slide-index="new">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0 slide-number">Slide (New)</h6>
                <button type="button" class="btn btn-danger btn-sm" onclick="removeSlide(this)">
                    <i class="bi bi-trash"></i> Remove
                </button>
            </div>
            
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Background Image (Desktop)</label>
                    <input type="file" name="slider_bg_new[]" class="form-control" accept="image/*">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Background Image (Mobile)</label>
                    <input type="file" name="slider_bg_mobile_new[]" class="form-control" accept="image/*">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Foreground Image</label>
                    <input type="file" name="slider_fg_new[]" class="form-control" accept="image/*">
                </div>
                
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Slide Text</label>
                    <textarea name="slider_text[]" class="form-control" placeholder="Enter slide title" rows="3"></textarea>
                    <input type="hidden" name="slider_index[]" value="new">
                </div>
            </div>
        </div>
    </div>
    
    <button type="button" class="btn btn-secondary btn-sm" onclick="addSlide()">
        <i class="bi bi-plus-circle"></i> Add New Slide
    </button>
</div>

<hr class="my-4">

<script>
function addSlide() {
    // Get the template
    const template = document.getElementById('slide-template');
    
    // Clone the template content
    const clone = template.querySelector('.slide-item').cloneNode(true);
    
    // Clear any values in the cloned inputs (in case browser caches them)
    clone.querySelectorAll('input[type="text"], textarea').forEach(input => input.value = '');
    clone.querySelectorAll('input[type="file"]').forEach(input => input.value = '');
    
    // Append to wrapper
    const wrapper = document.getElementById('slides-wrapper');
    wrapper.appendChild(clone);
    
    // Update slide numbers
    updateSlideNumbers();
}

function removeSlide(button) {
    const slideItem = button.closest('.slide-item');
    
    // Confirm before removing
    if (confirm('Are you sure you want to remove this slide?')) {
        slideItem.remove();
        updateSlideNumbers();
    }
}

function updateSlideNumbers() {
    const slides = document.querySelectorAll('#slides-wrapper .slide-item');
    slides.forEach((slide, index) => {
        const heading = slide.querySelector('.slide-number');
        const isNew = slide.dataset.slideIndex === 'new';
        heading.textContent = `Slide ${index + 1}${isNew ? ' (New)' : ''}`;
    });
}

// Existing remove checkbox handler
document.addEventListener('DOMContentLoaded', () => {
    const removeCheckboxes = document.querySelectorAll('.remove-checkbox');
    removeCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', (event) => {
            const parentDiv = event.target.closest('.relative');
            const hiddenInput = parentDiv.querySelector('input[type="hidden"]');
            if (hiddenInput) {
                hiddenInput.disabled = event.target.checked;
            }
        });
    });
    
    // Initialize slide numbers on page load
    updateSlideNumbers();
});
</script>