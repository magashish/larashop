// =========================
// Handle back button page reload on login
// =========================
window.addEventListener("pageshow", function(event) {
    if (window.location.pathname === "/login") {
        var historyTraversal = event.persisted ||
            (typeof window.performance !== "undefined" && window.performance.navigation.type === 2);
        if (historyTraversal) {
            window.location.reload();
        }
    }
});

// =========================
// Lazyload images & tabs
// =========================
// $(function() {
//     $("img.lazy").lazyload({ effect: "fadeIn" });
//     $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
//         const targetTabPane = $($(e.target).data('bs-target'));
//         targetTabPane.find("img.lazy").each(function() {
//             $(this).trigger("appear");
//         });
//     });
// });

// =========================
// Datepicker & Select2
// =========================
$(document).ready(function() {
    // $('.datepicker').datepicker({
    //     dateFormat: 'yy-mm-dd',
    //     autoclose: true
    // });

    $('.select2').select2({
        placeholder: 'Select categories',
        allowClear: true
    });
});

// =========================
// Newsletter AJAX subscription
// =========================
$('#newsletter-form').on('submit', function(e) {
    e.preventDefault();
    let email = $('#newsletter-email').val();
    $('#newsletter-message').html('Subscribing...');
    $.ajax({
        url: window.appConfig.routes.ajaxSubscribe,
        type: 'POST',
        data: {
            _token: window.appConfig.csrfToken,
            email: email
        },
        success: function(response) {
            if (response.success) {
                $('#newsletter-message').html('<span class="text-success">' + response.message + '</span>');
                $('#newsletter-email').val('');
            } else {
                $('#newsletter-message').html('<span class="text-danger">' + response.message + '</span>');
            }
        },
        error: function() {
            $('#newsletter-message').html('<span class="text-danger">Something went wrong.</span>');
        }
    });
});

// =========================
// Offer modals
// =========================
$(document).ready(function() {
    function openOfferModal(organisationId, offerIds) {
        $.ajax({
            url: `/organisation/${organisationId}?check_only_auth=true&offer_ids=${offerIds.join(',')}`,
            method: 'GET',
            success: function(response) {
                if (response.logged_in) {
                    $.ajax({
                        url: `/organisation/${organisationId}?offer_ids=${offerIds.join(',')}`,
                        method: 'GET',
                        success: function(responseHtml) {
                            $('body').append(responseHtml);
                            const $offerModal = $('#offerModal');
                            $offerModal.one('hidden.bs.modal', function() {
                                $(this).remove();
                            });
                            $offerModal.modal('show');

                            const $tabs = $('.work-tabs-inner .nav-tabs');
                            if ($(window).width() < 990) {
                                if (!$tabs.hasClass('slick-initialized')) {
                                    $tabs.slick({
                                        slidesToShow: 1,
                                        slidesToScroll: 1,
                                        arrows: true,
                                        dots: false,
                                        infinite: false,
                                        variableWidth: true
                                    });
                                }
                            } else {
                                if ($tabs.hasClass('slick-initialized')) {
                                    $tabs.slick('unslick');
                                }
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('AJAX Error:', status, error);
                            alert('Failed to load offer details. Please try again.');
                        }
                    });
                } else {
                    window.location.href = window.appConfig.routes.register;
                }
            },
            error: function() {
                alert('Failed to load offer details. Please try again.');
            }
        });
    }

    $(document).on('click', '.home-offer-detail, .offer-detail', function() {
        const organisationId = $(this).data('id');
        const offerIds = $(this).data('offer-ids').split(',');
        openOfferModal(organisationId, offerIds);
    });

    $(document).on('click', '.redeem_offer_site', function() {
        const offerId = $(this).data('id');
        $.ajax({
            url: `/redeemoffersite/${offerId}`,
            method: 'GET',
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
            }
        });
    });

    $(document).on('click', '.login-check', function() {
        $.ajax({
            url: window.appConfig.routes.loginCheckRedirection + '?check_only_auth=true',
            method: 'GET',
            success: function(response) {
                if (response.logged_in) {
                    window.location.href = window.appConfig.routes.discounts;
                } else {
                    window.location.href = window.appConfig.routes.login;
                }
            },
            error: function() {
                alert('Failed to check login status. Please try again.');
            }
        });
    });

    window.copyCode = function(buttonElement) {
        const codeElement = buttonElement.closest('.discount-bar').querySelector('.discount-code');
        const codeText = codeElement.textContent.trim();
        navigator.clipboard.writeText(codeText);

        const tooltip = buttonElement.querySelector('.tooltip');
        tooltip.style.visibility = 'visible';
        tooltip.style.opacity = '1';
        setTimeout(function() {
            tooltip.style.visibility = 'hidden';
            tooltip.style.opacity = '0';
        }, 2000);
    };
});

// =========================
// Loader & tab selection
// =========================
$(document).ready(function () {

    // ── Loader ────────────────────────────────────────────────────
    setTimeout(function () {
        $('#loader-wrapper').fadeOut('slow');
    }, 200);

    // ── URL tab param ─────────────────────────────────────────────
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if (tab) {
        $(`#${tab}-tab`).tab('show');
    }

    // ── Slick init function ───────────────────────────────────────
    function initSlick($slider) {
        if (!$slider.hasClass('slick-initialized')) {
            $slider.slick({
                infinite: true,
                slidesToShow: 6,
                slidesToScroll: 1,
                autoplay: false,
                autoplaySpeed: 2000,
                arrows: false,
                dots: false,
                lazyLoad: 'ondemand',
                speed: 300,
                cssEase: 'ease-out',
                touchThreshold: 15,
                swipeToSlide: true,
                responsive: [
                    { breakpoint: 1024, settings: { slidesToShow: 4 } },
                    { breakpoint: 768,  settings: { slidesToShow: 3 } },
                    { breakpoint: 480,  settings: { slidesToShow: 2 } }
                ]
            });
        } else {
            $slider.slick('setPosition');
        }
    }

    // ── Already rendered tabs (non-AJAX pages) ────────────────────
    $('.tab-pane').each(function () {
        // sirf wo tabs jo already content rakhte hain (spinner nahi)
        if ($(this).find('.offer-slide').length > 0) {
            $(this).find('.offer-slide').each(function () {
                initSlick($(this));
            });
        }
    });

    // ── AJAX tab loader ───────────────────────────────────────────
    const params     = new URLSearchParams(window.location.search);
    const search     = params.get('search') || '';
    const businessId = params.get('busness') || '';

    function loadTab(btn) {
        const $btn = $(btn);
        if (!$btn || $btn.data('loaded')) return;
        $btn.data('loaded', true);

        const catId      = $btn.data('category-id');
        const offerclass = $btn.data('offerclass') || '';
        const $pane      = $(`#nav-${catId}`);

        // Agar already content hai (non-AJAX page) to skip karo
        if ($pane.find('.offer-slide').length > 0) {
            $pane.find('.offer-slide').each(function () {
                initSlick($(this));
            });
            return;
        }

        $.get($btn.data('load-url'), { search, busness: businessId, offerclass: offerclass })
            .done(function (html) {
                $pane.html(html);

                // Reinit lozad
                if (typeof lozad !== 'undefined') {
                    const observer = lozad('.lozad', { rootMargin: '100px 0px' });
                    observer.observe();
                }

                // Reinit slick
                $pane.find('.offer-slide').each(function () {
                    initSlick($(this));
                });
            })
            .fail(function () {
                $pane.html('<p class="text-danger text-center py-4">Failed to load. Please try again.</p>');
            });
    }

    // Load active tab on page ready
    loadTab($('#nav-tab .nav-link.active[data-load-url]')[0]);

    // Load on tab switch
    $('#nav-tab').on('shown.bs.tab', '.nav-link[data-load-url]', function () {
        loadTab(this);
    });

    // Already rendered tab switch (non-AJAX)
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        const targetId  = $(e.target).attr('data-bs-target');
        const $activePane = $(targetId);
        $activePane.find('.offer-slide').each(function () {
            initSlick($(this));
        });
    });

    // View More
 $(document).on('click', '.load-all-offers-btn', function () {
    const target = $(this).data('target-tab');
    $(`${target} .offer-item-col.d-none`).removeClass('d-none');
    $(this).closest('.action-btn').remove(); // hide ki jagah remove
});

});




// =========================
// Legal tab & accordion scrolling
// =========================
document.addEventListener("DOMContentLoaded", function() {
    const hash = window.location.hash;
    if (hash) {
        const legalTab = document.querySelector('#legal-doc');
        if (legalTab) new bootstrap.Tab(legalTab).show();

        document.querySelectorAll('.accordion-collapse.show').forEach(item => {
            new bootstrap.Collapse(item, { toggle: false }).hide();
        });

        const targetCollapse = document.querySelector(hash);
        if (targetCollapse) {
            new bootstrap.Collapse(targetCollapse, { toggle: true }).show();
            setTimeout(() => {
                window.scrollTo({
                    top: targetCollapse.getBoundingClientRect().top + window.scrollY - 430,
                    behavior: "smooth"
                });
            }, 500);
        }
    }
});

// =========================
// Load all offers button
// =========================
$(document).ready(function() {
    $('.load-all-offers-btn').on('click', function() {
        const button = $(this);
        const targetTabId = button.data('target-tab');
        const hiddenOffers = $(targetTabId).find('.offer-item-col.d-none');
        hiddenOffers.removeClass('d-none');
        button.hide();
    });
});
