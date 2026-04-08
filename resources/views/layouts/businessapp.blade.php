<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
     @include('global.head-google-tag-manager')
    <meta charset="utf-8">
    <meta name="fo-verify" content="55411b5e-09d7-4550-8c61-4ea5884199fd" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
     <link rel="icon" href="{{ asset('/images/exhale--logo.png') }}" sizes="48x48">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- Scripts -->
    @vite(['resources/sass/app.scss',  'resources/css/custom.css', 'resources/js/app.js'])
</head>
<body>
     @include('global.body-google-tag-manager')
    <div id="main_wrap">
        <div id="navbar_wrap">

            @if (!Route::is('login') && 
             !Route::is('register') && 
             !Route::is('password.request') && 
             !Route::is('password.reset'))
             @auth
                 @include('includes.business-navbar')
             @endauth
             @endif

         </div>
         <div id="site_wrap">
            <main class="py-4">
                @yield('content')
            </main>
        </div>
        <div id="footer">
            <div class="container">
                <div class="row">
                    <div id="links" class="col-xs-12 text-center">
                       <img src="{{ asset('/images/admin-logo.svg') }}" id="footer_icon" alt="Xhale logo">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: 'Select categories',
                allowClear: true 
            });
        });
    </script>



<script>
    $(document).ready(function() {
        const currentAuthenticatedUserId = {{ Auth::id() }};
        var organisationSelect = $('#organisation_id');
        organisationSelect.select2({
            placeholder: 'Search for a business...',
            allowClear: true,
            ajax: {
                url: '{{ route('organisations.getOrganisation') }}', 
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term,
                        user_id: currentAuthenticatedUserId
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


        {{-- function populateAddressFields(place) {
            $('#address_search_input').val('');
            if (place.formatted_address) {
                $('#address_search_input').val(place.formatted_address);
                $('#address_search_input').val(place.formatted_address);
            }   
        } --}}

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




</body>
</html>
