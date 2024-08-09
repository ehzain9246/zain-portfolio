(function ($) {

    $(window).on('elementor/frontend/init', function () {

        var PremiumMagicSectionHandler = function ($scope, $) {

            $('html').addClass('msection-html');

            if ($(".premium-magic-section-body-inner").length < 1)
                $("body").wrapInner('<div class="premium-magic-section-body-inner" />');

            var $bodyInnerWrap = $("body .premium-magic-section-body-inner"),
                id = $scope.data('id'),
                $magicElem = $scope.find(".premium-msection-wrap"),
                settings = $magicElem.data("settings"),
                type = settings.type,
                style = settings.style,
                position = settings.position,
                $magicBtn = $scope.find('.premium-msection-btn'),
                computedStyle = getComputedStyle($scope[0]);


            if (['elastic', 'bubble', 'wave'].includes(style)) {
                var morphShape = Snap('#msection-shape-' + id + ' svg'),
                    myPath = morphShape.select("path");
            }

            function getWraptoOrg(duration) {

                if (!duration)
                    duration = 500;

                $('body').addClass('animating');

                $bodyInnerWrap.css('transform', 'none');

                $('html').css('height', 'auto');

                setTimeout(function () {

                    $('html').removeClass('offcanvas-open');
                    $('body').removeClass('animating');

                    //If the off canvas content is showing under content, then it should be hidden again after everything gets back to the initial state.
                    if (['slidealong', 'rotate'].includes(style))
                        $magicElem.addClass('premium-addons__v-hidden');

                    console.log("back to normal");
                }, duration);

            }

            getWraptoOrg(10);

            if ('selector' === settings.trigger)
                $magicBtn = $(settings.selector);


            var isHidden = true,
                boxWidth = computedStyle.getPropertyValue('--pa-msection-width'),
                boxHeight = computedStyle.getPropertyValue('--pa-msection-height');

            if (!boxWidth && (['right', 'left'].includes(position) || 'corner' === type)) {

                if ($magicElem.find('.elementor').length > 0) {

                    boxWidth = $magicElem.outerWidth();

                    //If elastic, then don't go to box full width.
                    if ('elastic' === style)
                        boxWidth = boxWidth / 2;

                    boxWidth = boxWidth + 'px';

                    if ($magicElem.find('.premium-lottie-animation').length > 0) {

                        setTimeout(function () {

                            boxWidth = $magicElem.outerWidth();

                            if ('elastic' === style)
                                boxWidth = boxWidth / 2;

                            boxWidth = boxWidth + 'px';

                        }, 1300);

                    }

                } else {
                    boxWidth = 'elastic' === style ? '15vw' : '30vw';

                    $magicElem.css('width', boxWidth);
                }
            }

            if (!boxHeight && (['top', 'bottom'].includes(position) || 'corner' === type)) {

                boxHeight = $magicElem.outerHeight();

                //If elastic, then don't go to box full height.
                if ('elastic' === style)
                    boxHeight = boxHeight / 2;

                boxHeight = boxHeight + 'px';
            }

            //To give the default styling by CSS.
            // if (['overlay', 'push', 'rotate', 'fall', 'elastic', 'bubble', 'wave'].includes(style))
            $magicElem.addClass('msection-' + style);

            if (["push", "reveal", 'slidealong', 'rotate', 'fall', 'elastic'].includes(style)) {

                //Wrap the body content in this wrapper.
                $('body > #premium-magic-section-' + id).remove();
                $('body').prepend($scope.find('.premium-msection-wrap'));

            } else if ('slide' === style) {

                var hPos = -1 !== position.indexOf('left') ? 'left' : 'right',
                    vPos = -1 !== position.indexOf('top') ? 'top' : 'bottom';

                if (-1 !== position.indexOf('top')) {
                    $magicElem.css('top', '-' + boxHeight);
                } else if (-1 !== position.indexOf('bottom')) {
                    $magicElem.css('bottom', '-' + boxHeight);
                }

                if (-1 !== position.indexOf('left')) {
                    $magicElem.css('left', '-' + boxWidth);
                } else if (-1 !== position.indexOf('right')) {
                    $magicElem.css('right', '-' + boxWidth);
                }

            }

            //Put the overlay on top.
            $('.premium-magic-section-body-inner > .premium-msection-overlay-' + id).remove();
            $('.premium-magic-section-body-inner').prepend($scope.find('.premium-msection-overlay'));

            if (['elastic', 'bubble', 'wave'].includes(style)) {
                var morphShape = Snap('#msection-shape-' + id + ' svg'),
                    myPath = morphShape.select("path");
            }

            $magicBtn.on("click", function () {

                //If it's out, then show it.
                if (isHidden) {

                    $magicElem.show();

                    $('html').css({
                        'height': '100%',
                        // 'overflow-y': 'scroll'
                    });

                    $('html').addClass('offcanvas-open');

                    //Show overlay
                    $(".premium-msection-overlay-" + id).removeClass("premium-addons__v-hidden");

                    //Show the content if reveal or similar effects.
                    $magicElem.removeClass('premium-addons__v-hidden');


                    //Remove the default styling.
                    // if (['push', 'overlay', 'rotate', 'fall', 'elastic', 'bubble', 'wave'].includes(style))
                    $magicElem.removeClass('msection-' + style);


                    //For body wrap effects.
                    //Pushes the body to left/right for push/reveal and show content.
                    if (['push', 'reveal', 'slidealong', 'rotate', 'fall', 'elastic'].includes(style)) {

                        if (['right', 'left'].includes(position)) {

                            sign = 'right' === position ? '-' : '';

                            $bodyInnerWrap.css('transform', 'translateX(' + sign + boxWidth + ')');

                        } else {

                            sign = 'bottom' === position ? '-' : '';

                            $bodyInnerWrap.css('transform', 'translateY(' + sign + boxHeight + ')');
                        }

                    }

                    if (style === 'slidealong') {
                        $magicElem.css('transform', 'translateX(0)');

                    } else if (style === 'slide') {

                        $magicElem.animate({
                            [hPos]: 0,
                            [vPos]: 0
                        }, 450, "swing", function () {
                            isHidden = false;

                        });
                    }

                    setTimeout(function () {
                        isHidden = false;
                    }, 550);

                    if ('elastic' === style) {

                        // Change the 'd' attribute of the path element
                        myPath.animate({
                            d: "M-1,0C-1,0,100,0,100,0C100,0,100,-1,100,395C100,799,100,800,100,800C100,800,-1,800,-1,800C-1,800,-1,0,-1,0C-1,0,-1,0,-1,0"
                        }, settings.e_dur || 350);
                    } else if ('bubble' === style || 'wave' === style) {

                        var morphSteps = $('#msection-shape-' + id).data('morph-open').split(';'),
                            stepsTotal = morphSteps.length;

                        console.log(morphSteps);
                        var pos = 0,
                            nextStep = function (pos) {
                                if (pos > stepsTotal - 1) {
                                    return;
                                }

                                // Change the 'd' attribute of the path element
                                myPath.animate({ 'path': morphSteps[pos] }, pos === 0 ? 400 : 500, pos === 0 ? mina.easein : mina.elastic, function () { nextStep(pos); });

                                pos++;
                            };

                        nextStep(pos);

                    }

                }


            });

            //On Click outside, close everything.
            if (settings.clickOutside) {

                $("body").on("click", function (event) {

                    var mSectionContent = ".premium-msection-btn, .premium-msection-btn *, .premium-msection-wrap, .premium-msection-wrap *, .premium-tabs-nav-list-item";

                    if (!$(event.target).is($(mSectionContent))) {
                        !isHidden && $magicElem.find(".premium-msection-close").trigger("click");
                    }
                });
            }

            $magicElem.find(".premium-msection-close").on("click", function () {

                $(".premium-msection-overlay-" + id).addClass("premium-addons__v-hidden");

                //Add the default styling again.
                $magicElem.addClass('msection-' + style);


                //We don't want to trigger this for each close action.
                if (!$('body').hasClass('animating'))
                    getWraptoOrg();


                if ('slide' === style) {

                    $magicElem.animate({
                        [hPos]: '-' + boxWidth,
                        [vPos]: '-' + boxHeight
                    }, 450, "swing", function () {

                        isHidden = true;


                    });
                } else {

                    if ('slidealong' === style) {
                        sign = 'left' === position ? '-' : '';
                        $magicElem.css('transform', 'translateX(' + sign + '50%)');
                    }

                    setTimeout(function () {
                        isHidden = true;

                        if ('elastic' === style) {

                            myPath.animate({
                                d: "M-1,0h101c0,0-97.833,153.603-97.833,396.167C2.167,627.579,100,800,100,800H-1V0z"
                            }, 100);
                        } else if ('bubble' === style) {

                            myPath.animate({
                                d: "M-7.312,0H0c0,0,0,113.839,0,400c0,264.506,0,400,0,400h-7.312V0z"
                            }, 100);

                        } else if ('wave' === style) {

                            myPath.animate({
                                d: "M0,100h1000l0,0c0,0-136.938,0-224,0c-193,0-170.235-1.256-278-35C399,34,395,0,249,0C118,0,0,100,0,100L0,100z"
                            }, 100);

                        }

                        // if ('morph' !== style)
                        $magicElem.hide();

                    }, 500);
                }

            });

        };

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-addon-magic-section.default', PremiumMagicSectionHandler);
    });
})(jQuery);