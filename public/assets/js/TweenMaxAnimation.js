 const alphabet = " ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+-=[]{}\\|;:'\",<.>/?`~";
    const targetWord = $("#revealButton").data("name");
    console.log(targetWord);

    class Flip {
        constructor($e, $index, targetChar) {
            this.wrapper = $e;
            this.base = $e.find(".base");
            this.cover = $e.find(".cover");
            this.coverTxt = $e.find(".cover .coverTxt");
            this.flipper = $e.find(".flipper");
            this.txt = $e.find(".tickerTxt");
            this.index = $index;
            this.targetChar = targetChar;
                            this.currentCharIndex = 0; // Start at 'A'
                            this.animSpeed = Math.random() * 200 + 100; // Randomize animation speed in milliseconds
                            this.timeline = null;
                        }

                        flipToTarget() {
                            const self = this;
                            const targetIndex = alphabet.indexOf(this.targetChar);

                            // Create a timeline for sequential flipping
                            this.timeline = new TimelineMax({
                                repeat: targetIndex - this.currentCharIndex,
                                onRepeat: function() {
                                    self.currentCharIndex =
                                    (self.currentCharIndex + 1) % alphabet.length;
                                    const currentChar = alphabet[self.currentCharIndex];

                                    // Update flipper text dynamically
                                    self.coverTxt.text(currentChar);
                                    self.txt.text(currentChar);
                                },
                                onComplete: function() {
                                    // Final state update
                                    self.coverTxt.text(self.targetChar);
                                    self.txt.text(self.targetChar);
                                },
                            });

                            // Add flipping animation to the timeline
                            this.timeline.add(
                                TweenMax.to(this.flipper, this.animSpeed / 1000, {
                                    rotationX: 0,
                                    ease: Quad.easeIn,
                                    onStart: this.flipStart.bind(this),
                                    onUpdate: this.flipProgress.bind(this),
                                    onComplete: this.flipEnd.bind(this),
                                })
                                );
                        }

                        flipStart() {
                            this.cover.show();
                            this.flipper.show();
                        }

                        flipProgress() {
                            const angle = this.flipper.transform("rotationX");

                            if (angle < 90) {
                                this.txt.addClass("inverse");
                                this.txt.text(alphabet[this.currentCharIndex]);
                            }
                        }

                        flipEnd() {
                            this.txt.removeClass("inverse");
                            this.flipper.hide();
                            this.cover.hide();
                        }
                    }

                    function createTickers(targetWord) {
                        $(".outerWrap").empty(); // Clear existing tickers

                        for (let i = 0; i < targetWord.length; i++) {
                            const targetChar = targetWord.charAt(i);
                            const $html = `
                                            <div class="tickerWrap">
                                            <div class="base">
                                                <span class="tickerTxt">&nbsp;</span>
                                            </div>
                                            <div class="cover">
                                                <span class="coverTxt">&nbsp;</span>
                                            </div>
                                            <div class="flipper">
                                                <span class="tickerTxt">&nbsp;</span>
                                            </div>
                                            </div>
                            `;
                            $(".outerWrap").append($html);

                            // Initialize flipper for each character
                            const $flipper = new Flip($(".tickerWrap").eq(i), i, targetChar);
                            setTimeout(() => {
                                $flipper.flipToTarget();
                            }, i * 100); // Stagger the flipping
                        }
                    }

                    $(document).on("click", "#revealButton", function() {
                        createTickers(targetWord);
                    });

                    // From https://github.com/Luxiyalu/jquery-transformer/blob/master/jquery-transformer.js
                    (function() {
                        (function($, TweenMax) {
                            return ($.prototype.transform = function(prop, value) {
                                var target;
                                if (prop == null) {
                                    return this.css("transform");
                                } else if (!prop) {
                                    this.css("transform", "none");
                                    return this;
                                }
                                if (value != null) {
                                    target = {};
                                    target[prop] = value;
                                    TweenMax.set(this, target);
                                    return this;
                                } else {
                                    if (this[0]._gsTransform == null) {
                                        TweenMax.set(this, {
                                            x: "+=0",
                                        });
                                    }
                                    return this[0]._gsTransform[prop];
                                }
                            });
                        })(window.jQuery, window.TweenMax);
                    }).call(this);