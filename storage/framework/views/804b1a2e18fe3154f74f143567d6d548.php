<!doctype html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
     <?php echo $__env->make('global.head-google-tag-manager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <meta charset="utf-8">
    <meta name="fo-verify" content="55411b5e-09d7-4550-8c61-4ea5884199fd" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?php echo e(asset('/images/exhale--logo.png')); ?>" sizes="48x48">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'Laravel')); ?></title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.14.1/themes/base/jquery-ui.css">



    <!-- Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/sass/app.scss',  'resources/css/slick.css', 'resources/css/custom.css', 'resources/js/app.js']); ?>
</head>
<body>
     <?php echo $__env->make('global.body-google-tag-manager', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <div id="main_wrap">
        <div id="navbar_wrap">
         <?php if(!Route::is('admin.login') && 
           !Route::is('admin.register') && 
           !Route::is('admin.password.request') &&
           !Route::is('admin.verify.index') &&  
           !Route::is('admin.password.reset')): ?>
           <?php if(auth()->guard('admin')->check()): ?>
           <?php echo $__env->make('includes.navbar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
           <?php endif; ?>
           <?php endif; ?>
       </div>
       <div id="site_wrap">
        <main class="py-4">
            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
    <div id="footer">
        <div class="container">
            <div class="row">
                <div id="links" class="col-xs-12 text-center">
                    <img src="<?php echo e(asset('/images/admin-logo.svg')); ?>" id="footer_icon" alt="Xhale logo">
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css">
<script type="text/javascript" src="<?php echo e(asset('assets/tinymce48/tinymce.min.js')); ?>"></script>
<script type="text/javascript">
    tinyMCE.init({
        selector: "#description, #small_description, #read_more , #banner_text , #review_text , #payment_description, #terms_conditions, #answer, #cash_giveaway_description, #work_tab_paragraph,#rewards_tab_paragraph, #community_tab_paragraph, #lifestyle_tab_paragraph",
        theme: "modern",
        plugins: [
            "importcss advlist autolink link lists charmap print preview hr anchor pagebreak",
            "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking",
            "save table contextmenu directionality paste textcolor"
        ],
        toolbar: " undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link | forecolor backcolor",
        skin: 'lightgray'
    });
</script>

<script>
    $(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        });

        function makeSortable(selector, type) {
            $(selector).sortable({
                update: function () {
                    let order = [];
                    $(selector + ' tr').each(function (index, element) {
                        order.push({
                            id: $(element).data('id'),
                            position: index + 1
                        });
                    });

                    $.ajax({
                        url: "<?php echo e(route('items.sort')); ?>",
                        type: "POST",
                        data: {
                            type: type,
                            order: order
                        },
                        success: function (response) {
                            console.log(type + ' positions updated');
                        }
                    });
                }
            });
        }
        makeSortable('#sortable', 'faqs');
        makeSortable('#catsortable', 'categories');
        makeSortable('#sortableplan', 'plan');
    });
</script>
<script>



    $(document).ready(function() {
        jQuery('.datepicker').datepicker({
            dateFormat: 'yy-mm-dd', 
            autoclose: true 
        });
        jQuery('.datetimepicker').datetimepicker({
            dateFormat: 'yy-mm-dd', 
            timeFormat: 'HH:mm',
            autoclose: true 
        });

        $('.categories_select2').select2({
            placeholder: 'Select categories',
            allowClear: true 
        });

        $('.featured_offers').select2({
            placeholder: 'Select Offers',
            allowClear: true 
        });
        
        $('.userselect2').select2({
            placeholder: 'Select User',
            allowClear: true 
        });
    });
</script>


<script>
    $(document).ready(function() {
        var organisationSelect = $('#organisation_id');
        organisationSelect.select2({
            placeholder: 'Search for a business...',
            allowClear: true,
            ajax: {
                url: '<?php echo e(route('organisations.getOrganisation')); ?>', 
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            },
            minimumInputLength: 0, 
            templateResult: function (organisation) {
                if (organisation.loading) {
                    return organisation.text;
                }
                var $container = $(
                    "<div class='select2-result-organisation'>" +
                    "<div class='select2-result-organisation__title'><strong>Business Name: </strong>" + organisation.text + "</div>" +
                    "<div class='select2-result-organisation__address'><strong>Business Address: </strong><small>" + organisation.address + "</small></div>" +
                    "</div>"
                    );

                return $container;
            },
            templateSelection: function (organisation) {
                if (!organisation.id) {
                    return organisation.text; 
                }
                return organisation.text;
            },

        });
    });
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBA6MvU0Rnsol0PvKQmm5QXmw0q6rUJ4lE&libraries=places"></script>
<script>
    $(document).ready(function() {
        const $addressFieldsContainer = $('#physicalAddressFields');
        const $addressSearchInput = $('#address_search_input');
        const $statusDiv = $('#address_lookup_status');


        

        function populateAddressFields(place) {
            $('#address_search_input').val(place.formatted_address || '');
            $('#address_lookup_status').empty();

            if (!place.address_components) return;
            const componentMapping = {
                'street_number': 'street_number',
                'route': 'street_name',
                'locality': 'city',
                'administrative_area_level_1': 'state',
                'postal_code': 'postcode',
                'country': 'country'
            };

            place.address_components.forEach(component => {
                component.types.forEach(type => {
                    if (componentMapping[type]) {
                        const customName = componentMapping[type];
                        const value = component.short_name;
                        $('#address_lookup_status').append(`
                    <input type="hidden" name="${customName}" id="${customName}" value="${value}">
                        `);
                    }
                });
            });

            if (place.geometry && place.geometry.location) {
                const lat = place.geometry.location.lat();
                const lng = place.geometry.location.lng();

                $('#address_lookup_status').append(`
            <input type="hidden" name="latitude" id="latitude" value="${lat}">
            <input type="hidden" name="longitude" id="longitude" value="${lng}">
                `);
            }
        }





        const autocomplete = new google.maps.places.Autocomplete($addressSearchInput[0], {
            types: ['address'], 
            componentRestrictions: { 'country': ['au'] } 
        });
        autocomplete.addListener('place_changed', function() {
            const place = autocomplete.getPlace();
            if (!place.geometry) {
                $statusDiv.html('<div class="alert alert-warning">No details available for input: \'' + place.name + '\'</div>');
                return;
            }
            populateAddressFields(place);
        });
        function togglePhysicalAddressFields() {
            const selectedLocationType = $('.address-type-radio:checked').val();
            if (selectedLocationType === 'use_business_address') {
                $addressFieldsContainer.hide();
                $addressSearchInput.prop('required', false);
                $statusDiv.empty(); 
            } else if (selectedLocationType === 'online_only') {
             $addressFieldsContainer.hide();
             $addressSearchInput.prop('required', false);
             $statusDiv.empty(); 
         } else if (selectedLocationType === 'physical_location') {
            var check = $('#address_search_input').val(); 
            if (check === 'online_only') { 
                $('#address_search_input').val(''); 
            }
            $addressFieldsContainer.show();
            $addressSearchInput.prop('disabled', false).prop('required', true);
        }
    }
    $('.address-type-radio').on('change', togglePhysicalAddressFields);
    togglePhysicalAddressFields();
});
</script>
<script>
    $(document).ready(function() {
        function toggleFeaturedOptions() {
            if ($('#is_featured').val() === 'yes') {
                $('#featuredOptions').show();
            } else {
                $('#featuredOptions').hide();
            }
        }
        toggleFeaturedOptions();
        $('#is_featured').on('change', function() {
            toggleFeaturedOptions();
        });
    });
</script> 

<script>
$(document).ready(function () {
    $('.remove-image-btn').on('click', function () {
        var id = $(this).data('id');
        var checkbox = $('#remove_' + id);
        var isChecked = checkbox.prop('checked');

        // Toggle the checkbox
        checkbox.prop('checked', !isChecked);

        // Update button state and text
        if (!isChecked) {
            $(this)
                .html('<i class="bi bi-check-circle"></i> Marked for Removal')
                .removeClass('btn-danger')
                .addClass('btn-warning');
        } else {
            $(this)
                .html('<i class="bi bi-trash"></i> Remove')
                .removeClass('btn-warning')
                .addClass('btn-danger');
        }
    });
});
</script>
<script>
$(document).ready(function () {
    $('.remove-banner-btn').on('click', function () {
        var checkbox = $('#remove_banner_image');
        var wrapper = $(this).closest('.image-wrapper');
        var isChecked = checkbox.prop('checked');
        checkbox.prop('checked', !isChecked);
        if (!isChecked) {
            $(this)
                .html('<i class="bi bi-check-circle"></i> Marked for Removal')
                .removeClass('btn-danger')
                .addClass('btn-warning');


        } else {
            $(this)
                .html('<i class="bi bi-trash"></i> Remove')
                .removeClass('btn-warning')
                .addClass('btn-danger');

        }
    });
});
</script>



<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/2.1.3/TweenMax.min.js"></script>
<script src="<?php echo e(asset('assets/js/TweenMaxAnimation.js')); ?>"></script>



</body>
</html>
<?php /**PATH /home/1476201.cloudwaysapps.com/udhewsxxeh/public_html/resources/views/layouts/app.blade.php ENDPATH**/ ?>