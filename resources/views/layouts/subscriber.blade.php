<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
     @include('global.head-google-tag-manager')
    <meta charset="utf-8">
    <meta name="fo-verify" content="55411b5e-09d7-4550-8c61-4ea5884199fd" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=1">
    <link rel="icon" href="{{ asset('/images/exhale--logo.png') }}" sizes="48x48">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>


    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flipclock/0.7.8/flipclock.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/litepicker/dist/css/litepicker.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    @vite(['resources/sass/app.scss',  'resources/css/slick.css',  'resources/css/external.css',  'resources/css/style.css', 'resources/js/app.js'])
</head>
<body>
     @include('global.body-google-tag-manager')
    <div class="dashboard-wrapper">
        <div class="container-fluid px-0">
            <div class="dashboard-row mx-0">
                <!-- Sidebar -->
                @include('includes.subscriber-sidebar')
                
                <!-- /Sidebar -->
                <!-- Main Content -->
                <main class="px-0">
                    <div class="dashboard-header">

                        @php
                        $activeSubscription = user_active_subscription_package();
                        @endphp
                        @if (!$activeSubscription)
                        <div class="top-bar text-center">
                            <p class="mb-0">You do not have an active subscription. <a href="{{ route("membership.dashboard") }}">Subscribe Now!</a></p>
                        </div>
                        @endif

                       

                        <header>
                            <div class="header">
                                <div class="container">
                                    <div class="row">
                                        <div class="col-12 head">
                                            <div class="header-inner">
                                                <div class="logo">
                                                    <a href="{{ url('/') }}"><img src="{{ asset('images/logo.svg') }}"></a>
                                                </div>
                                                <div class="navbar">
                                                    @include('includes.subscriber-navbar')
                                                </div>
                                                @include('global.nav-login-signup-btn')
                                                <div class="menu_icon">
                                                    <span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </header>
                    </div>
                    <div id="site_wrap">
                        @yield('content')
                    </div>
                </main>
            </div>
        </div>
    </div>



    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flipclock/0.7.8/flipclock.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/litepicker/dist/bundle.js"></script> --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "1000"
        };
    </script>

    <script>
        $(document).ready(function() {
    // Image preview functionality
            $('#imageInput').change(function(e) {
                const file = e.target.files[0];
                if (file) {
            // Validate file type
                    if (!file.type.match('image.*')) {
                        alert('Please select an image file (JPEG, PNG, JPG)');
                        return;
                    }

            // Validate file size (2MB max)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Image must be less than 2MB');
                        return;
                    }

            // Preview image
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        $('.previewImage').attr('src', event.target.result);
                    };
                    reader.readAsDataURL(file);

            // Immediately upload the file via AJAX
                    uploadImage(file);
                }
            });

            function uploadImage(file) {
                let formData = new FormData();
                formData.append('image', file);
                formData.append('_token', '{{ csrf_token() }}');

                $.ajax({
                    url: '{{ route("profile.image.upload") }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                // Show loading indicator
               // $('#previewBox').append('<div class="upload-loader">Uploading...</div>');
                    },
                    success: function(response) {
                // Handle success response
                        if (response.success) {
                            toastr.success(response.message);
                    // Update image source if returned from server
                            if (response.image_url) {
                                $('.previewImage').attr('src', response.image_url);
                            }
                        }
                    },
                    error: function(xhr) {
                // Handle error
                        let errorMessage = xhr.responseJSON?.message || 'Upload failed';
                    },
                    complete: function() {
                // Hide loading indicator
               // $('.upload-loader').remove();
                    }
                });
            }
        });






        $(document).ready(function() {
    // Handle save button clicks
            $('.update_profile').on('click', function() {
                const form = $(this).closest('form');
                const formData = form.serialize();
                const submitBtn = $(this);

        // Disable button and show loading state
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Saving...');

                $.ajax({
                    url: '{{ route("profile.update") }}',
                    type: 'POST',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            toastr.success(response.message);
                            form.closest('.modal').modal('hide');
                            if (response.new_mobile) {
                                $('.user-mobile-display').text(response.new_mobile);
                            }
                            if (response.new_name) {
                                $('.user-name-display').text(response.new_name);
                            }
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON?.errors;
                        if (errors) {
                            Object.values(errors).forEach(error => {
                                toastr.error(error[0]);
                            });
                        } else {
                            toastr.error(xhr.responseJSON?.message || 'An error occurred');
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).text('Save');
                    }
                });
            });


        });


        $(document).ready(function() {
            $('.toggle-password').on('click', function() {
                const target = $(this).data('target');
                const icon = $(this).find('i');

                $(target).attr('type', function(index, attr) {
                    return attr === 'password' ? 'text' : 'password';
                });

                icon.toggleClass('bi-eye-slash bi-eye');
            });
        });


        $(document).ready(function() {
    $('.password-toggle-btn').on('click', function() {
        const target = $(this).data('target');
        const $input = $(target);
        const $icon = $(this).find('i');

        if ($input.attr('type') === 'password') {
            $input.attr('type', 'text');
            $icon.removeClass('bi-eye-slash').addClass('bi-eye');
        } else {
            $input.attr('type', 'password');
            $icon.removeClass('bi-eye').addClass('bi-eye-slash');
        }
    });
});



    </script>



<!-- Calender JS -->
{{-- <script>
    const picker = new Litepicker({
      element: document.createElement('input'), 
      parentEl: document.getElementById('calendar-container'),
      inlineMode: true,      
      singleMode: false,      
      numberOfMonths: 1,      
      numberOfColumns: 1,
      format: 'YYYY-MM-DD'
  });
</script> --}}






{{-- 
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.print.min.css" media="print" />

--}}



<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.43/moment-timezone-with-data.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/en-au.js"></script>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.print.min.css" media="print" />




<style>
  /* Custom style for the selected day */
  .fc-day.selected-day {
    background-color: yellow !important;
}

/* Change the background color of the entire calendar view */
#calendar {
    background-color: white; /* Light grey background for the container */
    border-radius: 8px;
    padding: 10px;
}

.fc-day-header {
    background-color: white;
    color: black;
    padding: 10px !important;
}





/* General event box styling */
.fc-event {
  border: none !important;             /* Remove default thin border */
  padding: 5px 8px !important;         /* Add spacing inside event */
  font-weight: 600;                    /* Bold text */
  border-radius: 6px !important;       /* Rounded edges */
  background-color: #fad604 !important; /* Default highlight color */
  color: #000 !important;              /* Ensure text is visible */
  border-radius: 10px !important;
}




</style>


<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

{{--     $('#calendar').fullCalendar({
        header: {
            left: 'prev',
            center: 'title',
            right: 'next'
        },
        editable: false,
        eventLimit: true,
        timezone: 'Australia/Sydney',
        events: {
            url: '{{ route('events.api') }}',
            method: 'GET',
            failure: function() {
                alert('There was an error while fetching events!');
            }
        },
        eventRender: function(event, element) {
            console.log("Event:", event.title, 
                        "Start:", event.start.format(), 
                        "End:", event.end ? event.end.format() : null);
        }
    }); --}}
});
</script>


<script>
$(document).ready(function() {
    var $calendarResult = $('#calendar-result'); 

    $('#calendar').fullCalendar({
        header: false,
        footer: {
            left: 'prev',
            center: 'title',
            right: 'next'
        },

        editable: false,
        eventLimit: true,
        timezone: 'Australia/Sydney',
        events: {
            url: '{{ route('events.api') }}',
            method: 'GET',
            failure: function() {
                alert('There was an error while fetching events!');
            }
        },

        eventAfterAllRender: function(view) {
            $calendarResult.empty();
            var events = $('#calendar').fullCalendar('clientEvents');

            // If no events at all → show message
            if (!events || events.length === 0) {
                $calendarResult.html(`
                    <div class="no-events text-center py-5">
                        <p class="mb-0 fw-bold">No events found</p>
                    </div>
                `);
                return; // stop further rendering
            }

            var grouped = {};
            var eventCount = 0; // counter for total events

            // Group events by month/year
            $.each(events, function(i, event) {
                var monthYear = moment(event.start).format('MMMM YYYY');
                if (!grouped[monthYear]) {
                    grouped[monthYear] = [];
                }
                grouped[monthYear].push(event);
            });

            // Wrapper for all months
            var $allWrapper = $('<div class="all-months"></div>');
            $calendarResult.append($allWrapper);

            // Loop through each month group
            $.each(grouped, function(monthYear, eventsInMonth) {
                var monthId = monthYear.replace(/\s+/g, '-').toLowerCase(); // unique ID
                var $monthBlock = $(`
                    <div class="month-block mb-4" id="month-${monthId}">
                        <div class="sec-tag mb-3">
                            <p class="mb-0 text-uppercase">CASH GIVEAWAYS >> <span>${monthYear}</span></p>
                        </div>
                        <div class="month-events"></div>
                    </div>
                `);

                var $monthContainer = $monthBlock.find('.month-events');
                var monthVisible = false; // check if this month has at least one visible event

                $.each(eventsInMonth, function(i, event) {
                    eventCount++;
                    var isHidden = eventCount > 4;
                    if (!isHidden) monthVisible = true;

                    var startDate = event.custom_start;
                    var endDate = event.custom_end;

                    var html = `<div class="inner-box mb-4 event-item ${isHidden ? 'hidden' : ''}">
                        <div class="row g-1">
                            <div class="col-9">
                                <div class="sec-content">
                                    <h6 class="text-uppercase">${event.title}</h6>
                                    <p class="mb-1">${startDate}${endDate ? ' → ' + endDate : ''}</p>
                                    ${event.url ? `
                                        <a href="${event.url}" target="_blank" 
                                           class="btn btn-with-icon d-flex align-items-center justify-content-between">
                                            WATCH LIVE
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
                                                    <path d="M438.6 278.6c12.5-12.5 
                                                    12.5-32.8 0-45.3l-160-160c-12.5-12.5-32.8-12.5-45.3 
                                                    0s-12.5 32.8 0 45.3L338.8 224 
                                                    32 224c-17.7 0-32 14.3-32 
                                                    32s14.3 32 32 32l306.7 0L233.4 
                                                    393.4c-12.5 12.5-12.5 32.8 
                                                    0 45.3s32.8 12.5 45.3 0l160-160z"></path>
                                                </svg>
                                            </span>
                                        </a>` : ''}
                                </div>
                            </div>
                            <div class="col-3">
                                <div class="sec-img h-100">
                                    <img src="${event.image}" class="img-fluid" alt="${event.title} Image">
                                </div>
                            </div>
                        </div>
                    </div>`;
                    $monthContainer.append(html);
                });

                // If no visible event in this month → hide the whole block
                if (!monthVisible) {
                    $monthBlock.addClass('hidden');
                }

                $allWrapper.append($monthBlock);
            });

            // Add "Load More" button if more than 4 events total
            if (eventCount > 4) {
                $calendarResult.append(`<button class="btn btn-primary load-more mt-3">Load More</button>`);
            }

            // Handle load more button click
            $calendarResult.off('click', '.load-more').on('click', '.load-more', function() {
                $('.hidden').removeClass('hidden'); // show both months + events
                $(this).remove(); // remove button after showing all
            });
        }
    });
});
</script>

<script>
    $(document).ready(function() {
        $('#upsellModal').modal('show');
    });
</script>

<style>
.hidden { display: none; }
.no-events { font-size: 18px; color: #666; }
</style>

</body>
</html>