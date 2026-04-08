$(document).ready(function () {


    $('.clock').each(function () {
        const $thisClock = $(this);
        const dateStr = $thisClock.data('date');

        if (dateStr) {
            // Helper function (Döngü içinde kalabilir veya dışarı taşınabilir)
            function formatTime(totalSeconds) {
                const days = Math.floor(totalSeconds / (3600 * 24));
                const hours = Math.floor((totalSeconds % (3600 * 24)) / 3600);
                const mins = Math.floor((totalSeconds % 3600) / 60);
                const secs = totalSeconds % 60;
                return `${days}d ${hours}h ${mins}m ${secs}s`;
            }

            function getRemainingSeconds() {
                const brisbaneTarget = new Date(dateStr);
                const brisbaneNow = new Date(new Date().toLocaleString('en-US', {
                    timeZone: 'Australia/Brisbane'
                }));
                const diffMs = brisbaneTarget.getTime() - brisbaneNow.getTime();
                return Math.max(0, Math.floor(diffMs / 1000));
            }

            // Her bir saat için ayrı bir FlipClock nesnesi başlat
            let clock = $thisClock.FlipClock(getRemainingSeconds(), {
                clockFace: 'DailyCounter',
                countdown: true,
                autoStart: true
            });

            // Tab değiştirme senkronizasyonu
            document.addEventListener("visibilitychange", function () {
                if (document.visibilityState === 'visible') {
                    const currentActualDiff = getRemainingSeconds();
                    if (currentActualDiff > 0) {
                        clock.setTime(currentActualDiff);
                    }
                }
            });

            // Her saat için ayrı bir interval çalıştır
            const syncInterval = setInterval(function () {
                const currentActualDiff = getRemainingSeconds();
                const clockTime = clock.getTime().getTimeSeconds();

                // Eğer saat gerçek zamandan 1 saniyeden fazla saparsa düzelt
                if (Math.abs(currentActualDiff - clockTime) > 1) {
                    clock.setTime(currentActualDiff);
                }

                // Süre biterse durdur
                if (currentActualDiff <= 0) {
                    clearInterval(syncInterval);
                    clock.stop();
                    $thisClock.html('<span class="text-danger fw-bold">Draw Closed</span>');
                }
            }, 1000);
        }
    });
    
    $('.prize-slider').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: true,
        dots: false,
        autoplay: true,
        autoplaySpeed: 5000
    });
});