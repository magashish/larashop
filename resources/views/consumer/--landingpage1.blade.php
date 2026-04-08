@extends('layouts.consumer')
@section('content')
<style>
	.win-mazda {
	    max-width: 750px;
	    padding-left: 20%;
	}
	.giveaways-banner-sec {
	    padding: 8% 0px 12%;
	    background-size: cover;
	    background-position: center;
	}
	.jed {
    	max-width: 300px;
	    margin-top: -27%;
	}
	.access-img {
	    max-width: 300px;
	}
	.xhale-access-row {
	    display: flex;
	}
	.xhale-access {
	    margin-top: -40px;
	}
	.access-column {
	    margin-top: -55px;
	}
	.win-title-img img {
	    max-width: 650px;
	    margin: 0 auto;
	}
	.win-mazda-car {
	    padding-top: 70px;
	    padding-bottom: 80px;
	}
	.win-title-img {
	    margin-bottom: -4.5%;
	    position: relative;
	}
	.car-giveaways .bg-title.small-title {
	    max-width: 350px;
	}
	.giveaways-contnet {
	    padding-left: 10%;
	}
	.giveaways-contnet p {
	    
	    max-width: 420px;
	}
	.giveaways-contnet-inner{
		padding-left: 7%;
	}
	.slider-img img {
	    border-radius: 30px;
	}
	.car-giveaways {
	    padding-bottom: 50px;
	}
	.real-giveaway-sec .bg-title {
	    margin: 0 auto;
	    max-width: 340px;
	}
</style>


	<div class="banner-outer-wrapper">
        <div class="giveaways-banner-sec" style="background-image: url('/images/herolp-img.jpg');">
            <div class="container">
                <div class="win-mazda">
                	<img src="/images/win.png">
                </div>
            </div>
        </div>
    </div>
    <div class="xhale-access">
    	<div class="container">
    		<div class="xhale-access-row">
    			<div class="jed">
	    			<img src="/images/jed.png">
	    		</div>
	    		<div class="access-img">
	    			<img src="https://www.xhale.com.au/storage/pages/harcourt-text.png">
	    		</div>
    		</div>
    		
    	</div>
    </div>
    <div class="access-column">
    	<div class="container">
    		<div class="row">
    			<div class="col-xl-3 col-md-6 col-12">
    				<div class="access-col-img">
    					<img src="/images/bronze.png">
    				</div>
    			</div>
    			<div class="col-xl-3 col-md-6 col-12">
    				<div class="access-col-img">
    					<img src="/images/gold.png">
    				</div>
    			</div>
    			<div class="col-xl-3 col-md-6 col-12">
    				<div class="access-col-img">
    					<img src="/images/silver.png">
    				</div>
    			</div>
    			<div class="col-xl-3 col-md-6 col-12">
    				<div class="access-col-img">
    					<img src="/images/subscriptions.png">
    				</div>
    			</div>
    		</div>
    	</div>
    </div>
    <div class="win-mazda-car">
    	<div class="container">
    		<div class="row">
    			<div class="col-12">
    				<div class="win-title-img">
    					<img src="/images/win-car.svg">
    				</div>
    			</div>

    			<div class="col-12">
    				<div class="slider-img">
    					<img src="/images/herolp-img.jpg">
    				</div>
    			</div>

    		</div>
    	</div>
    </div>
    <div class="car-giveaways">
    	<div class="container">
    		<div class="row align-items-end">
    			<div class="col-md-6 col-12">
    				<img src="/images/Mazda-car-with-bow.png">
    			</div>
    			
    			<div class="col-md-6 col-12">
    				<div class="giveaways-contnet">    				
    					<div class="bg-title small-title">
		                    <div class="yellow-bg">CAR GIVEAWAY</div>
		                    <div class="black-bg">2025 CX5</div>
		                </div>
		                <div class="giveaways-contnet-inner">
			                <p>Big give away -23rd of June - Subscribe or buy a package to be in the chance to WIN!!</p>

			                <div class="action-btn d-flex flex-wrap align-items-center gap-2">
	                            <a class="login" href="https://www.xhale.com.au/login">Log In</a>
								<a class="signup" data-bs-toggle="modal" data-bs-target="#SignUpModal">Sign Up</a>
	                        </div>
                        </div>
		            </div>
    			</div>

    			<div class="col-12">
                        <div class="clock flip-clock-wrapper"><span class="flip-clock-divider days"><span class="flip-clock-label">Days</span></span><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">9</div></div><div class="down"><div class="shadow"></div><div class="inn">9</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">9</div></div><div class="down"><div class="shadow"></div><div class="inn">9</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><span class="flip-clock-divider hours"><span class="flip-clock-label">Hours</span><span class="flip-clock-dot top"></span><span class="flip-clock-dot bottom"></span></span><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">9</div></div><div class="down"><div class="shadow"></div><div class="inn">9</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">9</div></div><div class="down"><div class="shadow"></div><div class="inn">9</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><span class="flip-clock-divider minutes"><span class="flip-clock-label">Minutes</span><span class="flip-clock-dot top"></span><span class="flip-clock-dot bottom"></span></span><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">9</div></div><div class="down"><div class="shadow"></div><div class="inn">9</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">9</div></div><div class="down"><div class="shadow"></div><div class="inn">9</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><span class="flip-clock-divider seconds"><span class="flip-clock-label">Seconds</span><span class="flip-clock-dot top"></span><span class="flip-clock-dot bottom"></span></span><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">9</div></div><div class="down"><div class="shadow"></div><div class="inn">9</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul><ul class="flip "><li class="flip-clock-before"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">9</div></div><div class="down"><div class="shadow"></div><div class="inn">9</div></div></a></li><li class="flip-clock-active"><a href="#"><div class="up"><div class="shadow"></div><div class="inn">0</div></div><div class="down"><div class="shadow"></div><div class="inn">0</div></div></a></li></ul></div>
                    </div>


    		</div>
    	</div>
    </div>

    <section class="real-giveaway-sec">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="bg-title small-title">
                        <div class="yellow-bg">REAL GIVEAWAYS</div>
                        <div class="black-bg">REAL WINNERS</div>
                    </div>
                </div>
                <div class="col-12">
                	
					<div class="video">
						<div class="ratio ratio-16x9">
							<video class="video w-full rounded-md shadow-md" poster="https://www.xhale.com.au/storage/pages/6941002181b2f_ Poster Image.webp" src="https://www.xhale.com.au/storage/pages/M4p7ZQAJWUlT9WSfyiWC4QbYInrMblM2AuBWHJRa.mp4" controls="" preload="metadata" controlslist="nodownload">
							</video>
						</div>
					</div>

                </div>

            </div>
        </div>
    </section>


    <h1 class="text-center">USE PARTNER SLIDER FROM HOME PAGE</h1>



    <section class="social-feeds darkgray-bg">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-12">
                        <div class="bg-title small-title">
                            <div class="yellow-bg">CHECK OUT OUR</div>
                            <div class="black-bg">SOCIAL FEED</div>
                        </div>
                    </div>



                </div>

            </div>
        </section>
        @endsection

