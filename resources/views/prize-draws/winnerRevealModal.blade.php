
{{-- Winner Reveal Modal --}}
<div class="modal fade" id="winnerRevealModal" tabindex="-1" aria-labelledby="winnerRevealModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-fullscreen d-flex align-items-center justify-content-center">
        <div class="modal-content modal-dark">
            <div class="modal-header">
                <h5 class="modal-title text-warning" id="winnerRevealModalLabel">
                    <i class="bi bi-trophy-fill"></i> Winner
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-5">
                <div class="box">
                    <div class="mb-4">
                     <p class="text-center" style="margin-top: 20px;">
                        <img src="{{ asset('/images/logo.svg') }}" alt="logo">
                    </p>
                    <h3 class="mb-3">🏆 Congratulations! 🏆</h3>
                    <h3 class="lead  mb-4">The winner is...</h3>
                </div>
                <div id="site_wrap" class="container-fluid" role="main">
                    <div class="row">
                        <div id="admin_wrap" class="col-xs-12 text-center">
                            <div class="outerWrap"></div>
                        </div>
                    </div>
                </div>
                <div class="mt-4">
                    <div id="confetti-container" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 9999;"></div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>
{{-- CSS Styles --}}
<style>

    #winnerRevealModal h3 {
        color: #ffffff;
        font-size: 16pt;
        margin-top: 30px;
        margin-bottom: 15px;
        padding-bottom: 15px;
        padding-left: 25px;
        font-family: "Roboto Slab", sans-serif;
        font-weight: 600;
        text-transform: uppercase;
    }
    .box {
        margin-right: 5px;
        margin-bottom: 0;
        padding: 30px 30px 0 30px;
        background-color: #000000;
        border: 0;
        border-right: 4px solid #fad604;
    }

    /* Modal styling */
    .modal-dark {
        background: #1a1a1a;
        border: 3px solid #FFD700;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.9);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        min-height: 90vh;
    }

    .modal-dark .modal-header {
        background: #000;
        border-bottom: 2px solid #FFD700;
        border-radius: 20px 20px 0 0;
        width: 100%;
        text-align: center;
        justify-content: center;
        position: relative;
    }

    .modal-dark .modal-body {
        background: #000;
        color: #FFD700;
        border-radius: 0 0 20px 20px;
        width: 100%;
        text-align: center;
    }

    /* Title */
    .modal-title {
        color: #FFD700 !important;
        font-weight: bold;
        text-shadow: 0 0 10px rgba(255, 215, 0, 0.6);
        font-size: 1.8rem;
    }

    /* Centering inner elements */
    .modal-body {
        display: flex;
        flex-direction: column;
        align-items: center;
       {{--  justify-content: center;
        min-height: 70vh; --}}
    }

    /* Winner ticker styling */


    #winnerRevealModal  .outerWrap {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        padding: 30px;
        background: #000;
        border-radius: 15px;
        margin:auto;
    }

    .btn-close-white {
        filter: invert(1) sepia(1) saturate(5) hue-rotate(45deg);
    }

    /* Confetti animation */
    .confetti-piece {
        position: absolute;
        width: 12px;
        height: 12px;
        background: #FFD700;
        animation: confetti-fall 3s ease-in-out forwards;
        border: 1px solid #000;
    }

    @keyframes confetti-fall {
        0% { transform: translateY(-100vh) rotate(0deg); opacity: 1; }
        100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
    }

    /* Glow animation */
    @keyframes modalGlow {
        0% { box-shadow: 0 0 30px rgba(255, 215, 0, 0.3); }
        100% { box-shadow: 0 0 50px rgba(255, 215, 0, 0.6); }
    }

    .modal-dark {
        animation: modalGlow 2s ease-in-out infinite alternate;
    }

    @media (max-width: 768px) {
        .tickerWrap {
            font-size: 2rem;
            min-width: 40px;
            min-height: 65px;
        }
        .outerWrap {
            padding: 15px;
        }
    }
</style>

