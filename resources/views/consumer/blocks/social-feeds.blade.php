<section class="social-feeds darkgray-bg">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-12">
                <div class="bg-title small-title">
                    <div class="yellow-bg">CHECK OUT OUR</div>
                    <div class="black-bg">SOCIAL FEED</div>
                </div>
            </div>
            <div class="col-12">
                <div id="insta-feed" class="slick-slider-container"></div>
            </div>
        </div>
    </div>
</section>

 <style>
    #insta-feed {
        padding-bottom: 60px;
    }
    #insta-feed .slick-slide img,
    #insta-feed .slick-slide video {
        border-radius: 15px;
        width: 100%;
        height: 300px;
        object-fit: cover;
    }
</style> 

<script>
document.addEventListener('DOMContentLoaded', function () {
    fetch('/api/instagram/posts')
        .then(res => res.text())       // JSON ki jagah text/html lo
        .then(html => {
            const feed = document.getElementById('insta-feed');

            if (!html.trim()) {
                feed.innerHTML = '<p>No posts found.</p>';
                return;
            }
            feed.innerHTML = html;     
            $('#insta-feed').slick({
                slidesToShow: 4,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 2000,
                arrows: true,
                dots: false,
                responsive: [
                    { breakpoint: 1024, settings: { slidesToShow: 3 } },
                    { breakpoint: 768,  settings: { slidesToShow: 2 } },
                    { breakpoint: 480,  settings: { slidesToShow: 1 } }
                ]
            });
        })
        .catch(err => {
            console.error('Instagram feed error:', err);
        });
});
</script>