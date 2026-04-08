<div class="modal fade" id="newwinnerRevealModal" tabindex="-1" aria-labelledby="newwinnerRevealModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-fullscreen d-flex align-items-center justify-content-center">
        <div class="modal-content modal-dark">
            <div class="modal-body text-center">
              <div class="draw-sec">
                  <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>               
                  <div class="container">
                    <div class="logo-wrapper">
                      <img src="{{ asset('/images/logo.svg') }}" >
                      <a href="{{ env('APP_URL') }}">
                        {{ str_replace(['https://', 'http://', '/'], '', env('APP_URL')) }}
                    </a>                    
                </div>
                <div class="screen-wrapper">
                  <div class="screen-head">
                    <p>Giveaways on {{ $prizeDraw->draw_datetime?->format('l, F jS @ g:i A') ?? 'N/A' }} </p>
                </div>
                <div class="screen-thumb">

                    @if ($draw->prize_image)
                    <img src="{{ Storage::url($draw->prize_image) }}" alt="Org Image">
                    @else
                    <img src="{{ asset('storage/pages/sarah-mazda.webp') }}">
                    @endif

                </div>
            </div>
            <div class="banner" id="banner">
                <div class="mask-top"></div>
                <div class="mask-bottom"></div>
                <div class="mask-left"></div>
                <div class="mask-right"></div>
                <div class="shine"></div>
                <div class="canvas" id="canvas"></div>
            </div>
            <div class="prize-amt">

                @if ($draw->prize_type)
                <span class="prize-sign">$</span><span id="prize-amount">{{ $draw->cash_amount }}</span>
                @else
                <span class="prize-sign">{{ $draw->prize_title }}</span> <span id="prize-amount">{{ $draw->prize_sub_title }}</span>
                @endif
            </div>
            <div class="prize-type-label">{{ $draw->prize_type }} GIVEAWAY</div>
                {{-- <ul class="social-links">
                    <li>
                        <a href="https://www.instagram.com/xhalecoapp" target="_blank">
                            <img src="https://www.xhale.com.au/images/Instagram_icon.webp" alt="Instagram">
                        </a>
                    </li>
                    <li>
                        <a href="https://www.facebook.com/xhalecoapp" target="_blank">
                            <img src="https://www.xhale.com.au/images/Facebook_f_logo.svg" alt="Facebook">
                        </a>
                    </li>
                    <li>
                        <a href="https://tiktok.com/@xhalecoapp" target="_blank">
                            <img src="https://www.xhale.com.au/images/Tiktok_icon.png" alt="TikTok">
                        </a>
                    </li>
                </ul> --}}
            </div>
        </div>
    </div>
</div>
</div>
</div>



<style>


.draw-sec .banner {
    width: 600px;
    height: 120px;
    position: relative;
    border-radius: 14px;
    overflow: hidden;
    border: 2px solid #fad604;
    box-shadow:
    0 0 0 1px rgba(74,144,217,0.4),
    0 0 30px rgba(30,110,255,0.85),
    0 0 70px rgba(20,70,200,0.5),
    0 0 120px rgba(10,50,160,0.3),
    inset 0 0 50px rgba(20,60,180,0.7);
    background: linear-gradient(180deg,
      #0a1845 0%,
      #1648a8 28%,
      #2060d0 50%,
      #1648a8 72%,
      #0a1845 100%
    );
    margin: -50px auto 0;
}

.draw-sec .mask-top,
.draw-sec .mask-bottom {
    position: absolute;
    left: 0; right: 0;
    height: 22px;
    z-index: 20;
    pointer-events: none;
}
.draw-sec .mask-top    { top: 0;    background: linear-gradient(180deg, #0a1845 0%, transparent 100%); }
.draw-sec .mask-bottom { bottom: 0; background: linear-gradient(0deg,   #0a1845 0%, transparent 100%); }

.draw-sec .mask-left,
.draw-sec .mask-right {
    position: absolute;
    top: 0; bottom: 0;
    width: 24px;
    z-index: 20;
    pointer-events: none;
}
.draw-sec .mask-left  { left: 0;  background: linear-gradient(90deg,  #0a1845 0%, transparent 100%); }
.draw-sec .mask-right { right: 0; background: linear-gradient(270deg, #0a1845 0%, transparent 100%); }

.draw-sec .shine {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(255,255,255,0.08) 0%, transparent 55%);
    pointer-events: none;
    z-index: 15;
}

.draw-sec .canvas {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 5;
    padding: 0 16px;
}

.draw-sec .col {
    width: 36px;
    min-width: 36px;
    height: 99px;
    overflow: hidden;
    position: relative;
    flex-shrink: 0;
}

.draw-sec .col.space {
    width: 0px;
    min-width: 0px;
    display: none;
}

.draw-sec .strip {
    position: absolute;
    top: 0;
    left: 0; right: 0;
    display: flex;
    flex-direction: column;
    align-items: center;
    will-change: transform;
}

.draw-sec .letter {
    height: 33px;
    min-height: 33px;
    font-size: 28px;
    font-weight: 600;
    font-style: italic;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    text-shadow: 0 0 6px rgba(255,255,255,0.9),
    0 0 18px rgba(120,200,255,0.6);
    text-transform: uppercase;
}
/* top & bottom row */
.draw-sec .letter.sm {
  /*color: rgba(200, 228, 255, 0.45);*/
  opacity: .2;
}

/* center row (target) */
.draw-sec .letter.target {
  color: #ffffff;
  text-shadow:
  0 0 6px rgba(255,255,255,1),
  0 0 18px rgba(120,200,255,0.9),
  0 0 30px rgba(60,150,255,0.6);
}

.modal-dark .modal-body:has(.draw-sec) {
    border-radius: 0;
}
.draw-sec {
    text-align: center;
    color: #fff;
    width: 100%;
}
.draw-sec .container {
    max-width: 680px;
    margin: 0 auto;
}
.draw-sec .logo-wrapper a {
    font-weight: 400;
    color: #fff;
    text-decoration: none;
}
.draw-sec .screen-wrapper {
    box-shadow: 0 0 40px rgba(250, 214, 4, 0.3);
    border-radius: 15px;
}
.draw-sec .screen-head {
    background-color: #fad604;
    color: #000;
    padding: 18px;
    border-radius: 15px 15px 0 0;
    font-size: 18px;
    font-weight: 500;
    margin-bottom: 3px;
    text-transform: uppercase;
}
.draw-sec .screen-head p {
    margin: 0;
}
.draw-sec .logo-wrapper {
    margin-bottom: 30px;
}
.draw-sec .logo-wrapper img {
    width: 100%;
    height: 50px;
}
.draw-sec .screen-thumb {
    height: 350px;
}
.draw-sec .screen-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 0 0 15px 15px;
}
.draw-sec button.btn-close {
    display: block;
    margin-left: auto;
    filter: brightness(0) invert(1);
    font-size: 20px;
    padding: 20px;
}
.draw-sec .prize-sign {
    font-size: 40px;
    margin-top: 12px;
    color: #ffffff;
}
.draw-sec #prize-amount {
    font-size: 80px;
    font-weight: 600;
    color: #fad604;
}
.draw-sec .prize-type-label {
    font-size: 60px;
    font-weight: 600;
    line-height: normal;
    color: #ffffff;
    text-shadow: 0px 3px 10px #fad604;
    text-transform: uppercase;
}
.draw-sec .social-links {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    list-style: none;
    margin-top: 15px;
}
.draw-sec .social-links img {
    width: 20px;
    height: 20px;
    object-fit: contain;
}

@media screen and (max-width: 767px){
.draw-sec .banner {
    width: 90%;
}
.draw-sec .screen-head {
    padding: 10px;
    font-size: 16px;
}
.draw-sec .prize-type-label {
    font-size: 44px;
}
.draw-sec #prize-amount {
    font-size: 64px;
}
.draw-sec .col {
    width: 30px;
    min-width: 30px;
}
.draw-sec .letter {    
    font-size: 24px;
}
}
</style>



<script>

    let TARGET = '';

    const CHARS = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789 .-'";
    const COL_W  = 42;
    const GAP    = 4;
    const SPACE_W = 18;
    const PAD    = 32;

    function randChar() {
      return CHARS[Math.floor(Math.random() * CHARS.length)];
  }

  function buildReelCol(colEl, targetLetter, stopDelay) {

      const strip = document.createElement('div');
      strip.className = 'strip';
      colEl.appendChild(strip);

      const ROW_H = 33;
      const beforeCount = 25;
      const items = [];

      for (let i = 0; i < beforeCount; i++) {
        items.push({ char: randChar(), cls: 'letter sm' });
    }

    items.push({ char: targetLetter, cls: 'letter target' });

    for (let i = 0; i < 10; i++) {
        items.push({ char: randChar(), cls: 'letter sm' });
    }

    items.forEach(item => {
        const d = document.createElement('div');
        d.className = item.cls;
        d.textContent = item.char;
        strip.appendChild(d);
    });

    const finalIndex = beforeCount;
    const finalY = -(finalIndex * ROW_H - ROW_H);

    strip.style.transition = 'none';
    strip.style.transform = 'translateY(0px)';
    strip.getBoundingClientRect();

    const spinDuration = stopDelay + 100;

    strip.style.transition =
`transform ${spinDuration}ms cubic-bezier(0.4,0,0.05,1)`;
strip.style.transform = `translateY(${finalY}px)`;

setTimeout(() => {
    strip.style.transition = 'transform 90ms ease-out';
    strip.style.transform = `translateY(${finalY + 6}px)`;
    setTimeout(() => {
      strip.style.transition = 'transform 70ms ease-in';
      strip.style.transform = `translateY(${finalY}px)`;
  }, 90);
}, spinDuration - 100);
}

function init() {

  const canvas = document.getElementById('canvas');
  canvas.innerHTML = '';

  if (!TARGET) return;

  const words = TARGET.split(' ');
  const allCols = [];

  words.forEach((word, wi) => {
    if (wi > 0) allCols.push({ type: 'space' });

    word.split('').forEach(ch => {
      allCols.push({ type: 'reel', letter: ch.toUpperCase() });
  });
});

  let reelIdx = 0;

  allCols.forEach(item => {

    const col = document.createElement('div');

    if (item.type === 'reel') {

      col.className = 'col';
      canvas.appendChild(col);

      const BASE_DELAY = 1800;
      const STAGGER = 220;
      const stopDelay = BASE_DELAY + reelIdx * STAGGER;

      buildReelCol(col, item.letter, stopDelay);
      reelIdx++;

  } else {

      col.className = 'col space';
      canvas.appendChild(col);
  }
});
}


/* ✅ START ANIMATION WHEN MODAL FULLY OPENS */
document.addEventListener('DOMContentLoaded', function () {

  const modal = document.getElementById('newwinnerRevealModal');

  modal.addEventListener('shown.bs.modal', function (event) {

    const button = event.relatedTarget;
    TARGET = button.getAttribute('data-name') || '';
    TARGET = TARGET.toUpperCase();

    init();

});

  /* Optional: clear when closed */
  modal.addEventListener('hidden.bs.modal', function () {
    document.getElementById('canvas').innerHTML = '';
});

});

</script>