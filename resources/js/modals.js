
// 1. Explicitly import Bootstrap here so it's available within this module's scope.
import * as bootstrap from 'bootstrap';
$(document).ready(function () {
        // Define modal IDs in the desired display order
	const modalIds = ['welcomeModal', 'benefitsModal', 'reviewModal'];
	const welcomeModalCookieName = 'xhale_welcome_modal';
        const cookieExpiryDays = 7; // Cookie will expire in 7 days

        // --- Cookie Functions ---
        function setCookie(name, value, days) {
        	let expires = "";
        	if (days) {
        		const date = new Date();
        		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        		expires = "; expires=" + date.toUTCString();
        	}
        	document.cookie = name + "=" + (value || "")  + expires + "; path=/";
        }

        function getCookie(name) {
        	const nameEQ = name + "=";
        	const ca = document.cookie.split(';');
        	for(let i=0; i < ca.length; i++) {
        		let c = ca[i];
        		while (c.charAt(0) === ' ') c = c.substring(1, c.length);
        		if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        	}
        	return null;
        }
        // --- End Cookie Functions ---


        // --- Function to Show the Next Modal in Sequence ---
        function showNextModal(currentIndex) {
        	if (getCookie(welcomeModalCookieName)) {
        		console.log('Welcome modal skipped. Not showing further modals.');
        		return;
        	}
        	if (currentIndex < modalIds.length) {
        		const nextModalId = modalIds[currentIndex];
        		const nextModalElement = document.getElementById(nextModalId);

        		if (nextModalElement) {
        			const nextModal = new bootstrap.Modal(nextModalElement, {
        				backdrop: 'static',
        				keyboard: false
        			});
        			nextModal.show();

        			$(nextModalElement).one('hidden.bs.modal', function () {
        				showNextModal(currentIndex + 1);
        			});

        			const currentModalSkipButton = $(nextModalElement).find('.skip-text');
        			if (currentModalSkipButton.length) {
        				currentModalSkipButton.off('click').on('click', function (e) {
        					e.preventDefault();
        					setCookie(welcomeModalCookieName, 'true', cookieExpiryDays);
                            nextModal.hide(); // Hide the current modal
                        });
        			}
        		} else {
        			showNextModal(currentIndex + 1);
        		}
        	} else {
        	}
        }

        if ($('.welcome-slider').length) {
        	$('.welcome-slider').slick({
        		dots: true,
        		infinite: true,
        		autoplay: true,  
        		speed: 300,
        		slidesToShow: 1,
        		adaptiveHeight: true
        	});
        }

        const isGuest = document.body.dataset.isGuest === 'true'; 
        if (isGuest) {
        	const isModalSkipped = getCookie(welcomeModalCookieName);
        	if (!isModalSkipped) {
        		showNextModal(0);
        	} else {
        		console.log('User has previously skipped welcome modals.');
        	}
        } else {
        	console.log('User is logged in. Welcome modals are not displayed.');
        }



    });
