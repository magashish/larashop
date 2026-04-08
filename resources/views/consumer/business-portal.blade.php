@extends('layouts.consumer')
@section('content')


<div class="hero-sec login-sec business-portal-wrapper mb-0">
    <div class="container-fluid g-0">
        <div class="row align-items-stretch banner-content g-0">


         <div class="col-xl-6 col-12">
            <div class="container">
                <div class="hero-content signup-multistep-wrapper">
                    <div class="bg-title mx-0 mb-5">
                        <div class="yellow-bg">YOUR OFFERS</div>
                        <div class="black-bg">OUR COMMUNITY</div>
                    </div>

                    <section class="partners-form-box pt-lg-0">
                        <div class="container">
                            <div class="row justify-content-center">
                                <div class="registration-card">
 
                   
                                    @include('global.business-sign-up-form')


                                </div>
                            </div>
                        </div>

                    </section>



                </div>
            </div>
        </div>
        <div class="col-xl-6 col-12 position-relative">
            <div class="hero-img" style="background-image: url(images/package-page-banner-mob.jpg);"></div>
            @include('global.offer-hero-slider')
        </div>
    </div>                    
</div>                    
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBA6MvU0Rnsol0PvKQmm5QXmw0q6rUJ4lE&libraries=places"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('customimage');
        const fileLabel = document.querySelector('.custom-file-label');

        fileInput.addEventListener('change', function(event) {
            const fileName = event.target.files.length > 0 ? event.target.files[0].name : 'Add Logo';
            fileLabel.textContent = fileName;
        });
    });
</script>

<script>
    $(document).ready(function() {
        // Initialize Business Address functionality
        initAddressAutocomplete(
            '#businessphysicalAddressFields',
            '#business_address_search',
            '#business_address_lookup_status',
            '.address-type-radio'
            );

        // Initialize Offer Address functionality
        initAddressAutocomplete(
            '#offerphysicalAddressFields',
            '#offer_address_search',
            '#offer_address_lookup_status',
            '.offer_address-type-radio'
            );

        function initAddressAutocomplete(containerId, inputId, statusDivId, radioSelector) {
            const $addressFieldsContainer = $(containerId);
            const $addressSearchInput = $(inputId);
            const $statusDiv = $(statusDivId);
            let autocomplete = null;

            function populateAddressFields(place) {
                $addressSearchInput.val(place.formatted_address || '');
                $statusDiv.empty();

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
                            const fieldPrefix = containerId.includes('offer') ? 'offer_' : 'business_';
                            $statusDiv.append(`
                                <input type="hidden" name="${fieldPrefix}address_components[${customName}]" value="${value}">
                            `);
                        }
                    });
                });

                if (place.geometry?.location) {
                    const lat = place.geometry.location.lat();
                    const lng = place.geometry.location.lng();
                    const fieldPrefix = containerId.includes('offer') ? 'offer_' : 'business_';
                    $statusDiv.append(`
                        <input type="hidden" name="${fieldPrefix}latitude" value="${lat}">
                        <input type="hidden" name="${fieldPrefix}longitude" value="${lng}">
                    `);
                }
            }

            function initAutocomplete() {
                if (autocomplete) return;
                
                autocomplete = new google.maps.places.Autocomplete($addressSearchInput[0], {
                    types: ['address'],
                    componentRestrictions: { 'country': ['au'] },
                    fields: ['address_components', 'formatted_address', 'geometry']
                });
                
                autocomplete.addListener('place_changed', function() {
                    const place = autocomplete.getPlace();
                    if (!place.geometry) {
                        $statusDiv.html('<div class="alert alert-warning">No details available for this address</div>');
                        return;
                    }
                    populateAddressFields(place);
                });
            }

            function togglePhysicalAddressFields() {
                const selectedType = $(radioSelector + ':checked').val();
                
                if (selectedType === 'physical_location') {
                    $addressFieldsContainer.show();
                    $addressSearchInput.prop('required', true);
                    initAutocomplete();
                } else {
                    $addressFieldsContainer.hide();
                    $addressSearchInput.prop('required', false).val('');
                    $statusDiv.empty();
                    
                    if (autocomplete) {
                        google.maps.event.clearInstanceListeners($addressSearchInput[0]);
                        autocomplete = null;
                    }
                }
            }

            $(radioSelector).on('change', togglePhysicalAddressFields);
            togglePhysicalAddressFields();
        }
    });
</script>



@endsection
