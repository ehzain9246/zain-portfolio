(function ($) {

    if ('undefined' == typeof window.paCheckSafari) {

        window.paCheckSafari = checkSafariBrowser();

        function checkSafariBrowser() {

            var iOS = /iP(hone|ad|od)/i.test(navigator.userAgent) && !window.MSStream;

            if (iOS) {
                var allowedBrowser = /(Chrome|CriOS|OPiOS|FxiOS)/.test(navigator.userAgent);

                if (!allowedBrowser) {
                    var isFireFox = '' === navigator.vendor;
                    allowedBrowser = allowedBrowser || isFireFox;
                }

                var isSafari = /WebKit/i.test(navigator.userAgent) && !allowedBrowser;

            } else {
                var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);
            }

            if (isSafari) {
                return true;
            }

            return false;
        }

    }

    $(window).on('elementor/frontend/init', function () {
        var ModuleHandler = elementorModules.frontend.handlers.Base;

        // if ('1' === PremiumProSettings.magicSection) {

        // }

        // Hover Box Handler
        var PremiumFlipboxHandler = function ($scope, $) {
            var $flipboxElement = $scope.find(".premium-flip-main-box"),
                height = $flipboxElement.height() / 2,
                width = $flipboxElement.width() / 2,
                currentDevice = elementorFrontend.getCurrentDeviceMode(),
                isTouch = !['desktop', 'widescreen', 'laptop'].includes(currentDevice);

            if ($scope.hasClass("premium-flip-style-cube")) {

                $flipboxElement.on("mouseenter touchstart", function () {

                    height = $flipboxElement.height() / 2,
                        width = $flipboxElement.width() / 2;

                    moveCube('rotateY', 'premium-flip-frontrl', -90, width);
                    moveCube('rotateY', 'premium-flip-backrl', 0, width);

                    moveCube('rotateY', 'premium-flip-frontlr', 90, width);
                    moveCube('rotateY', 'premium-flip-backlr', 0, width);

                    moveCube('rotateX', 'premium-flip-fronttb', -90, height);
                    moveCube('rotateX', 'premium-flip-backtb', 0, height);

                    moveCube('rotateX', 'premium-flip-frontbt', 90, height);
                    moveCube('rotateX', 'premium-flip-backbt', 0, height);

                });

                $flipboxElement.on("mouseleave", function () {
                    flipBackCubeBox();
                });

            }

            //to rotate elements
            function moveCube(rotation, el, deg, h) {
                $flipboxElement.find('.' + el).css({
                    'transform': rotation + '(' + deg + 'deg) translateZ(' + h + 'px)',
                    '-webkit-transform': rotation + '(' + deg + 'deg) translateZ(' + h + 'px)',
                    '-moz-transform': rotation + '(' + deg + 'deg) translateZ(' + h + 'px)'
                });
            }

            if (!$scope.hasClass("premium-flip-style-flip"))
                return;


            $flipboxElement.on("mouseenter touchstart", function () {

                $(this).addClass("flipped");

                if (!$flipboxElement.data("flip-animation"))
                    return;

                if ($(this).children(".premium-flip-front").hasClass("premium-flip-frontrl")) {

                    $(this).find(".premium-flip-front .premium-flip-front-content-container .premium-flip-text-wrapper").removeClass("PafadeInLeft").addClass("PafadeInRight");

                    $(this).find(".premium-flip-back .premium-flip-back-content-container .premium-flip-back-text-wrapper").addClass("PafadeInLeft").removeClass("PafadeInRight");

                } else if (
                    $(this).children(".premium-flip-front").hasClass("premium-flip-frontlr")
                ) {
                    $(this).find(".premium-flip-front .premium-flip-front-content-container .premium-flip-text-wrapper").removeClass("PafadeInRevLeft").addClass("PafadeInRevRight");

                    $(this).find(".premium-flip-back .premium-flip-back-content-container .premium-flip-back-text-wrapper").addClass("PafadeInRevLeft")
                        .removeClass("PafadeInRevRight");
                }
            });

            $flipboxElement.on("mouseleave", function () {

                flipBackBox();

            });

            if (isTouch) {

                $(document).on('click', function (event) {

                    if (!$(event.target).closest('.premium-flip-main-box').length) {

                        flipBackBox();

                        if ($scope.hasClass("premium-flip-style-cube")) {
                            flipBackCubeBox();
                        }

                    }

                })
            }


            function flipBackBox() {

                $flipboxElement.removeClass("flipped");

                if (!$flipboxElement.data("flip-animation"))
                    return;

                if (
                    $flipboxElement
                        .children(".premium-flip-front")
                        .hasClass("premium-flip-frontrl")
                ) {
                    $flipboxElement
                        .find(
                            ".premium-flip-front .premium-flip-front-content-container .premium-flip-text-wrapper"
                        )
                        .addClass("PafadeInLeft")
                        .removeClass("PafadeInRight");

                    $flipboxElement
                        .find(
                            ".premium-flip-back .premium-flip-back-content-container .premium-flip-back-text-wrapper"
                        )
                        .removeClass("PafadeInLeft")
                        .addClass("PafadeInRight");
                } else if (
                    $flipboxElement
                        .children(".premium-flip-front")
                        .hasClass("premium-flip-frontlr")
                ) {
                    $flipboxElement
                        .find(
                            ".premium-flip-front .premium-flip-front-content-container .premium-flip-text-wrapper"
                        )
                        .addClass("PafadeInRevLeft")
                        .removeClass("PafadeInRevRight");

                    $flipboxElement
                        .find(
                            ".premium-flip-back .premium-flip-back-content-container .premium-flip-back-text-wrapper"
                        )
                        .removeClass("PafadeInRevLeft")
                        .addClass("PafadeInRevRight");
                }

            }

            function flipBackCubeBox() {

                moveCube('rotateY', 'premium-flip-frontrl', 0, width);
                moveCube('rotateY', 'premium-flip-backrl', 90, width);

                moveCube('rotateY', 'premium-flip-frontlr', 0, width);
                moveCube('rotateY', 'premium-flip-backlr', -90, width);

                moveCube('rotateX', 'premium-flip-fronttb', 0, height);
                moveCube('rotateX', 'premium-flip-backtb', 90, height);

                moveCube('rotateX', 'premium-flip-frontbt', 0, height);
                moveCube('rotateX', 'premium-flip-backbt', -90, height);

            }
        };

        // Unfold Handler
        var PremiumUnfoldHandler = ModuleHandler.extend({

            getDefaultSettings: function () {

                return {
                    selectors: {
                        unfoldContentWrap: '.premium-unfold-content-wrap',
                        unfoldButtonTxt: '.premium-unfold-button-text',
                        unfoldContent: '.premium-unfold-content',
                        unfoldIcon: '.premium-unfold-icon',
                        unfoldIconHolder: '.premium-icon-holder-unfolded',
                        unfoldGradient: '.premium-unfold-gradient',
                        foldIconHolder: '.premium-icon-holder-fold',
                    }
                }
            },

            getDefaultElements: function () {
                var selectors = this.getSettings('selectors'),
                    elements = {
                        $unfoldElem: this.$element,
                    };

                elements.$unfoldContentWrap = elements.$unfoldElem.find(selectors.unfoldContentWrap);
                elements.$unfoldButtonTxt = elements.$unfoldElem.find(selectors.unfoldButtonTxt);
                elements.$unfoldContent = elements.$unfoldElem.find(selectors.unfoldContent);
                elements.$unfoldIcon = elements.$unfoldElem.find(selectors.unfoldIcon);
                elements.$unfoldIconHolder = elements.$unfoldElem.find(selectors.unfoldIconHolder);
                elements.$unfoldGradient = elements.$unfoldElem.find(selectors.unfoldGradient);
                elements.$foldIconHolder = elements.$unfoldElem.find(selectors.foldIconHolder);

                return elements;
            },

            bindEvents: function () {
                this.run();
            },

            run: function () {

                var $unfoldElem = this.elements.$unfoldElem,
                    $unfoldButtonTxt = this.elements.$unfoldButtonTxt,
                    $unfoldContent = this.elements.$unfoldContent,
                    $unfoldIcon = this.elements.$unfoldIcon,
                    $unfoldIconHolder = this.elements.$unfoldIconHolder,
                    $unfoldGradient = this.elements.$unfoldGradient,
                    $foldIconHolder = this.elements.$foldIconHolder,
                    settings = this.getElementSettings(),
                    $unfoldContentWrap = this.elements.$unfoldContentWrap,
                    contentHeight = parseInt($unfoldContentWrap.outerHeight()),
                    foldHeight = this.getFoldHeight(),
                    foldSelect = settings.premium_unfold_fold_height_select,
                    foldText = settings.premium_unfold_button_fold_text,
                    unfoldText = settings.premium_unfold_button_unfold_text,
                    foldEase = settings.premium_unfold_fold_easing,
                    unfoldEase = settings.premium_unfold_unfold_easing,
                    foldDur = 'custom' === settings.premium_unfold_fold_dur_select ? settings.premium_unfold_fold_dur * 1000 : settings.premium_unfold_fold_dur_select,
                    unfoldDur = 'custom' === settings.premium_unfold_unfold_dur_select ? settings.premium_unfold_unfold_dur * 1000 : settings.premium_unfold_unfold_dur_select;

                if ("percent" === foldSelect) {
                    foldHeight = (foldHeight / 100) * contentHeight;
                }

                $unfoldButtonTxt.text(foldText);

                $unfoldContent.css('height', foldHeight);

                $unfoldIcon.html($unfoldIconHolder.html());

                $unfoldElem.on('click', '.premium-button', function (e) {

                    e.preventDefault();

                    setTimeout(function () {
                        $unfoldElem.removeClass('prevented');
                    }, foldDur + 50);

                    if (!$unfoldElem.hasClass('prevented')) {

                        $unfoldElem.addClass('prevented');

                        var text = $unfoldContent.hasClass("toggled") ? unfoldText : foldText;

                        $unfoldButtonTxt.text(text);

                        if ($unfoldContent.hasClass("toggled")) {

                            contentHeight = parseInt($unfoldContentWrap.outerHeight());

                            $unfoldContent.css("overflow", "visible");

                            $unfoldContent.animate({ height: contentHeight }, unfoldDur, unfoldEase).removeClass("toggled");

                        } else {

                            $unfoldContent.css("overflow", "hidden");
                            $unfoldContent.animate({ height: foldHeight }, foldDur, foldEase).addClass("toggled");
                        }

                        $unfoldGradient.toggleClass("toggled");

                        if ($unfoldContent.hasClass("toggled")) {
                            $unfoldIcon.html($unfoldIconHolder.html());
                        } else {
                            $unfoldIcon.html($foldIconHolder.html());
                        }
                    }
                });
            },

            getFoldHeight: function () {
                var settings = this.getElementSettings(),
                    suffix = 'desktop' === elementorFrontend.getCurrentDeviceMode() ? '' : '_' + elementorFrontend.getCurrentDeviceMode(),
                    unit = settings.premium_unfold_fold_height_select,
                    defaultHeight = 60;

                if ('pixel' === unit) {
                    defaultHeight = 100;
                    suffix = '_pix' + suffix;
                }

                return undefined != settings['premium_unfold_fold_height' + suffix] ? settings['premium_unfold_fold_height' + suffix] : defaultHeight;

            },

        });

        // Facebook Messenger Handler
        var PremiumFbChatHandler = function ($scope, $) {

            var premiumFbChat = $scope.find(".premium-fbchat-container"),
                premiumFbChatSettings = premiumFbChat.data("settings"),
                currentDevice = elementorFrontend.getCurrentDeviceMode();

            if (premiumFbChat.length > 0) {

                if ("mobile" === currentDevice && premiumFbChatSettings["hideMobile"]) {
                    return;
                }

                window.fbAsyncInit = function () {
                    FB.init({
                        appId: premiumFbChatSettings["appId"],
                        autoLogAppEvents: !0,
                        xfbml: !0,
                        version: "v2.12"
                    });
                };
                (function (a, b, c) {
                    var d = a.getElementsByTagName(b)[0];
                    a.getElementById(c) ||
                        ((a = a.createElement(b)),
                            (a.id = c),
                            (a.src =
                                "https://connect.facebook.net/" +
                                premiumFbChatSettings["lang"] +
                                "/sdk/xfbml.customerchat.js"),
                            d.parentNode.insertBefore(a, d));
                })(document, "script", "facebook-jssdk");


                $(".elementor-element-overlay .elementor-editor-element-remove").on(
                    "click",
                    function () {

                        var $this = $(this),
                            parentId = $this.parents("section.elementor-element");

                        if (parentId.find("#premium-fbchat-container").length) {
                            document.location.href = document.location.href;
                        }

                    }
                );

            }
        };

        // Twitter Feed Handler
        var PremiumTwitterFeedHandler = function ($scope, $) {
            var $elem = $scope.find(".premium-twitter-feed-wrapper"),
                $loading = $elem.find(".premium-loading-feed"),
                settings = $elem.data("settings"),
                carousel = 'yes' === $elem.data("carousel");

            function get_tweets_data() {
                $elem
                    .find(".premium-social-feed-container")
                    .socialfeed({
                        twitter: {
                            accounts: settings.accounts,
                            limit: settings.limit || 2,
                            consumer_key: 'AgV213XdiJzwCvrdaDRsxnwti',
                            consumer_secret: 'qRfkwcdL4y9l18WFd0sDIEYUC34iJGmCKUzniS6YomO3crBOkU',
                            token: "776918558542561280-E0hfZKFOYweZQYLQmEcqdvy8RsjrYtg",
                            secret: "rVLihQdh90lhbzvVlMW5fZolaATLlBbUXOyANpBb6RDOe",
                            tweet_mode: "extended",
                            header: settings.header
                        },
                        length: settings.length || 130,
                        show_media: 'yes' === settings.showMedia,
                        readMore: settings.readMore,
                        template: settings.template,
                        callback: function () {
                            $loading.removeClass("premium-show-loading");
                            $elem.imagesLoaded(function () {
                                handleTwitterFeed();
                            });
                        }
                    });
            }

            function handleTwitterFeed() {
                var headerWrap = $elem.find('.premium-twitter-user-cover');

                if (carousel) {

                    var autoPlay = 'yes' === $elem.data("play"),
                        speed = $elem.data("speed") || 5000,
                        rtl = $elem.data("rtl"),
                        colsNumber = $elem.data("col"),
                        prevArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                        nextArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>';

                    headerWrap.prependTo($elem);
                    $(headerWrap).not(':first').remove();

                    $elem.find(".premium-social-feed-container").slick({
                        infinite: true,
                        slidesToShow: colsNumber,
                        slidesToScroll: colsNumber,
                        responsive: [{
                            breakpoint: 1025,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                        ],
                        autoplay: autoPlay,
                        autoplaySpeed: speed,
                        rows: 0,
                        rtl: rtl ? true : false,
                        nextArrow: nextArrow,
                        prevArrow: prevArrow,
                        draggable: true,
                        pauseOnHover: true
                    });
                }

                if (!carousel && settings.layout === "grid-layout" && !settings.even) {

                    var masonryContainer = $elem.find(".premium-social-feed-container");

                    masonryContainer.isotope({
                        itemSelector: ".premium-social-feed-element-wrap",
                        percentPosition: true,
                        layoutMode: "masonry",
                        animationOptions: {
                            duration: 750,
                            easing: "linear",
                            queue: false
                        }
                    });

                    headerWrap.prependTo($elem);
                    $(headerWrap).not(':first').remove();

                }
            }

            $.ajax({
                url: get_tweets_data(),
                beforeSend: function () {
                    $loading.addClass("premium-show-loading");
                },
                error: function () {
                    console.log("error getting data from Twitter");
                }
            });

        };

        // Alert Box Handler
        var PremiumAlertBoxHandler = function ($scope, $) {
            var $barElem = $scope.find(".premium-notbar-outer-container"),
                settings = $barElem.data("settings"),
                _this = $($barElem);

            if (_this.length > 0) {

                //If animation is set, we need to keep it hidden until we trigger the animation.
                if (!settings.entranceAnimation) {
                    $barElem.removeClass('elementor-invisible');
                }

                if (!elementorFrontend.isEditMode() && (settings.logged || !$("body").hasClass("logged-in"))) {
                    if (settings.cookies) {
                        if (notificationReadCookie("premiumNotBar-" + settings.id)) {
                            $barElem.css("display", "none");
                        }
                    }
                }

                function notificationSetCookie(cookieName, cookieValue) {
                    var today = new Date(),
                        expire = new Date();

                    expire.setTime(today.getTime() + 3600000 * settings.interval);

                    document.cookie = cookieName + "=" + encodeURI(cookieValue) + ";expires=" + expire.toGMTString() + "; path=/";
                }

                function notificationReadCookie(cookieName) {
                    var theCookie = " " + document.cookie;

                    var ind = theCookie.indexOf(" " + cookieName + "=");

                    if (ind == -1) ind = theCookie.indexOf(";" + cookieName + "=");

                    if (ind == -1 || cookieName == "") return "";

                    var ind1 = theCookie.indexOf(";", ind + 1);

                    if (ind1 == -1) ind1 = theCookie.length;

                    return unescape(theCookie.substring(ind + cookieName.length + 2, ind1));
                }

                if (settings.location === "top" && settings.position === "premium-notbar-relative") {

                    $($barElem).detach();

                    $($barElem).addClass('premium-notbar-top');

                    $("body").prepend(_this);

                    if (settings.type === 'notification') {

                        $($barElem).addClass('premium-notbar-notification-top-' + settings.id);

                        $("body").find('.premium-notbar-notification-top-' + settings.id).not(":first").remove();

                    }
                }

                if (settings.location === "top" && settings.type === 'alert') {

                    $("body").find('.premium-notbar-notification-top-' + settings.id).remove();
                }

                if (settings.location !== "top" && settings.type === 'notification') {

                    $("body").find('.premium-notbar-notification-top-' + settings.id).remove();
                }

                if ('yes' === settings.customPos) {
                    if (settings.layout === "boxed" || settings.type === 'alert') {

                        var barWidth = $barElem.find(".premium-notbar").parent().width();
                        $barElem.find(".premium-notbar").css("width", barWidth);

                        $(window).on("resize", function () {
                            barWidth = $barElem.find(".premium-notbar").parent().width();
                            $barElem.find(".premium-notbar").css("width", barWidth);
                        });
                    }
                }

                triggerAnimations();

                function triggerAnimations() {

                    if (settings.entranceAnimation) {
                        $barElem.removeClass('elementor-invisible');
                        $barElem.find('.premium-notbar').addClass('animated ' + settings.entranceAnimation);
                    }

                }

                $barElem.find(".premium-notbar-close").on("click", function () {

                    //Handle cookies behavior.
                    if (!elementorFrontend.isEditMode() && (settings.logged || !$("body").hasClass("logged-in"))) {
                        if (settings.cookies) {
                            if (!notificationReadCookie("premiumNotBar-" + settings.id)) {
                                notificationSetCookie("premiumNotBar-" + settings.id, true);
                            }
                        }
                    }

                    if (settings.closeAction === 'hide') {

                        if ('top' === settings.location) {

                            if (settings.position === "premium-notbar-fixed") {
                                $barElem.find('.premium-notbar').addClass('notbar-hidden-top');
                            } else {
                                $barElem.css('overflow', 'hidden');
                                $barElem.animate({
                                    height: "0"
                                }, 300);
                            }

                        } else if ('bottom' === settings.location) {

                            $barElem.find('.premium-notbar').addClass('notbar-hidden-bottom');

                        } else {

                            $barElem.addClass('notbar-hidden');
                        }

                    } else {

                        var $elementToRemove = null;
                        switch (settings.elementToRemove) {
                            case 'widget':
                                $elementToRemove = $scope;
                                break;

                            case 'column':
                                $elementToRemove = $scope.closest('.e-con.e-child');
                                break;

                            default:
                                $elementToRemove = $scope.closest('.e-con.e-parent');
                        }

                        if (elementorFrontend.isEditMode())
                            return;

                        $elementToRemove.css('overflow', 'hidden');
                        $elementToRemove.animate({
                            height: 0,
                            padding: 0,
                            margin: 0,
                            borderWidth: 0
                        }, 300);

                    }
                });

            }
        };

        var PremiumChartHandler = ModuleHandler.extend({

            getDefaultSettings: function () {

                return {
                    selectors: {
                        chartElem: '.premium-chart-container',
                        chartCanvas: '.premium-chart-canvas',
                    }
                }

            },

            getDefaultElements: function () {

                var selectors = this.getSettings('selectors');

                return {
                    $chartElem: this.$element.find(selectors.chartElem),
                    $chartCanvas: this.$element.find(selectors.chartCanvas),
                }

            },

            bindEvents: function () {
                //Fix conflict with tabs widget.
                var _this = this,
                    $closestTab = this.elements.$chartElem.closest(".premium-tabs-content-section, .elementor-tab-content"),
                    closestTabID = this.elements.$chartElem.closest(".premium-tabs").attr('id'),
                    isHScrollWidget = this.elements.$chartElem.closest(".premium-hscroll-temp");

                //Don't forget to check first tab.
                if (!$closestTab.length && !isHScrollWidget.length) {
                    this.run();
                } else if (isHScrollWidget.length) {

                    var isRendered = false,
                        parentSectionWidth = isHScrollWidget.outerWidth();

                    $(window).on("scroll", function () {

                        if (!isRendered && $(window).scrollTop() >= isHScrollWidget.data("scroll-offset") - (parentSectionWidth / 2)) {
                            _this.run();
                            isRendered = true;
                        }

                    });

                } else {

                    var tabIndex = $closestTab.index();

                    //For the active tab on page load.
                    setTimeout(function () {
                        if ($closestTab.is(':visible')) {
                            _this.run();
                        }
                    }, 300);

                    $(document).on('click', "#" + closestTabID + " li[data-list-index='" + tabIndex + "']", function () {
                        //Make sure we are targeting a visible chart that was not rendered before.
                        // if (_this.elements.$chartElem.is(':visible') && !_this.elements.$chartElem.hasClass("chart-rendered")) {
                        if (!_this.elements.$chartElem.hasClass("chart-rendered")) {
                            _this.run();
                        }
                    });

                    if ($closestTab.hasClass('elementor-tab-content')) {

                        var elementorTabID = $closestTab.attr('id').replace('elementor-tab-content-', ''),
                            isRendered = false;

                        if (!isRendered) {

                            isRendered = true;
                            $(document).on('click', "#elementor-tab-title-" + elementorTabID, function () {
                                setTimeout(function () {
                                    if (!_this.elements.$chartElem.hasClass("chart-rendered")) {
                                        console.log("render");
                                        _this.run();
                                    }
                                }, 300);

                            });
                        }

                    }
                }

            },

            chartInstance: null,
            columnsData: null,
            run: function () {

                var settings = this.getElementSettings(),
                    $chartElem = this.elements.$chartElem;

                $chartElem.addClass("chart-rendered");

                this.columnsData = $chartElem.data("chart");

                var $checkModal = $chartElem.closest(".premium-modal-box-modal");

                if ($checkModal.length || "load" === settings.render_event) {

                    this.getChartData();

                } else {
                    var _this = this;
                    new Waypoint({
                        element: this.elements.$chartCanvas,
                        offset: Waypoint.viewportHeight() - 250,
                        triggerOnce: true,
                        handler: function () {
                            _this.getChartData();
                            this.destroy();
                        }
                    });
                }

            },

            getSingleOptions: function () {

                var settings = this.getElementSettings();

                return {
                    scale: {
                        ticks: {
                            beginAtZero: settings.y_axis_begin,
                            stepSize: settings.step_size,
                            suggestedMax: settings.y_axis_max,
                            callback: function (tick) {
                                var locale = settings.format_locale || false;
                                return tick.toLocaleString(locale);
                            }
                        }
                    }
                };

            },

            getMultiOptions: function () {

                var settings = this.getElementSettings(),
                    type = settings.type;

                return {
                    scales: {
                        xAxes: [{
                            barPercentage: ('bar' === type && settings.x_column_width.size) ? settings.x_column_width.size : 0.9,
                            display: ("pie" === type || "doughnut" === type) ? false : true,
                            gridLines: {
                                display: settings.x_axis_grid,
                                color: settings.x_axis_grid_color,
                                lineWidth: settings.x_axis_grid_width.size,
                                drawBorder: true
                            },
                            scaleLabel: {
                                display: settings.x_axis_label_switch,
                                labelString: settings.x_axis_label,
                                fontColor: settings.x_axis_label_color,
                                fontSize: settings.x_axis_label_size
                            },
                            ticks: {
                                fontSize: settings.x_axis_labels_size || 12,
                                fontColor: settings.x_axis_labels_color || '#54595f',
                                stepSize: settings.step_size,
                                maxRotation: settings.x_axis_label_rotation || 0,
                                minRotation: settings.x_axis_label_rotation || 0,
                                beginAtZero: settings.x_axis_begin,
                                callback: function (tick) {
                                    var locale = settings.format_locale || false;
                                    return tick.toLocaleString(locale);
                                }
                            }
                        }],
                        yAxes: [{
                            display: ("pie" === type || "doughnut" === type) ? false : true,
                            type: 'horizontalBar' !== type ? settings.data_type : 'category',
                            gridLines: {
                                display: settings.y_axis_grid,
                                color: settings.y_axis_grid_color,
                                lineWidth: settings.y_axis_grid_width.size,
                            },
                            scaleLabel: {
                                display: settings.y_axis_label_switch,
                                labelString: settings.y_axis_label,
                                fontColor: settings.y_axis_label_color,
                                fontSize: settings.y_axis_label_size
                            },
                            ticks: {
                                suggestedMin: settings.y_axis_min,
                                suggestedMax: settings.y_axis_max,
                                fontSize: settings.y_axis_labels_size || 12,
                                fontColor: settings.y_axis_labels_color || '#54595f',
                                beginAtZero: settings.y_axis_begin,
                                stepSize: settings.step_size,
                                callback: function (tick) {
                                    var locale = settings.format_locale || false;
                                    return tick.toLocaleString(locale);
                                }
                            }
                        }]
                    }
                };

            },

            getGlobalOptions: function (ctx) {

                var _this = this,
                    settings = this.getElementSettings(),
                    type = settings.type,
                    currentDevice = elementorFrontend.getCurrentDeviceMode(),
                    eventsArray = ["mousemove", "mouseout", "click", "touchstart", "touchmove"],
                    printVal = settings.value_on_chart,
                    event = ("pie" === type || "doughnut" === type) && printVal ? false : eventsArray;

                settings.legPos = settings.legend_position;
                if ("desktop" !== currentDevice) {
                    if (settings.legend_hide)
                        settings.legend_display = false;

                    settings.legPos = settings['legend_position_' + currentDevice];

                }

                return {
                    maintainAspectRatio: false,
                    layout: {
                        padding: {
                            top: "polarArea" === type ? 6 : 0
                        }
                    },
                    events: event,
                    animation: {
                        duration: settings.duration || 500,
                        easing: settings.start_animation,
                        onComplete: function () {
                            if (!event) {
                                this.defaultFontSize = 16;
                                ctx.font =
                                    '15px "Helvetica Neue", "Helvetica", "Arial", sans-serif';

                                ctx.fillStyle = "#000";

                                ctx.textAlign = "center";
                                ctx.textBaseline = "bottom";

                                this.data.datasets.forEach(function (dataset) {
                                    for (var i = 0; i < dataset.data.length; i++) {
                                        var model =
                                            dataset._meta[Object.keys(dataset._meta)[0]].data[i]
                                                ._model,
                                            total =
                                                dataset._meta[Object.keys(dataset._meta)[0]].total,
                                            mid_radius =
                                                model.innerRadius +
                                                (model.outerRadius - model.innerRadius) / 2,
                                            start_angle = model.startAngle,
                                            end_angle = model.endAngle,
                                            mid_angle = start_angle + (end_angle - start_angle) / 2;

                                        var x = mid_radius * Math.cos(mid_angle);
                                        var y = mid_radius * Math.sin(mid_angle);

                                        ctx.fillStyle = settings.y_axis_labels_color;

                                        var percent =
                                            String(Math.round((dataset.data[i] / total) * 100)) + "%";

                                        ctx.fillText(percent, model.x + x, model.y + y + 15);
                                    }
                                });
                            }
                        }
                    },
                    tooltips: {
                        enabled: settings.tool_tips,
                        mode: settings.tool_tips_mode,
                        callbacks: {
                            label: function (tooltipItem, data) {
                                var prefixString = "";
                                if ("pie" == type || "doughnut" == type || "polarArea" == type) {
                                    prefixString = data.labels[tooltipItem.index] + ": ";
                                }

                                var dataset = data.datasets[tooltipItem.datasetIndex];

                                var total = dataset.data.reduce(function (previousValue, currentValue) {
                                    return parseFloat(previousValue) + parseFloat(currentValue);
                                });

                                var currentValue = dataset.data[tooltipItem.index];

                                if (!settings.tool_tips_percent) {
                                    var locale = settings.format_locale || false;
                                    currentValue = parseFloat(currentValue).toLocaleString(locale);
                                }

                                var percentage = ((currentValue / total) * 100).toPrecision(3);

                                if (_this.$element.hasClass('extend-tooltips')) {
                                    return [prefixString, (settings.tool_tips_percent ? percentage + "%" : currentValue)];
                                } else {
                                    return (prefixString + (settings.tool_tips_percent ? percentage + "%" : currentValue));
                                }


                            }
                        }
                    },
                    legend: {
                        display: settings.legend_display,
                        position: settings.legPos,
                        reverse: settings.legend_reverse,
                        labels: {
                            usePointStyle: settings.legend_circle,
                            boxWidth: parseInt(settings.legend_item_width),
                            fontColor: settings.legend_text_color || '#54595f',
                            fontSize: parseInt(settings.legend_text_size)
                        }
                    }

                };

            },

            renderChart: function () {

                var widgetID = this.getID(),
                    ctx = document.getElementById('premium-chart-canvas-' + widgetID).getContext("2d"),
                    globalOptions = this.getGlobalOptions(ctx),
                    settings = this.getElementSettings(),
                    columnsData = this.columnsData,
                    xLabels = settings.x_axis_labels || '',
                    type = settings.type,
                    data = {
                        labels: 'custom' === settings.data_source ? xLabels.split(",") : [],
                        datasets: []
                    };

                this.chartInstance = new Chart(ctx, {
                    type: type,
                    data: data,
                    options: Object.assign(globalOptions, ("radar" !== type && "polarArea" !== type) ? this.getMultiOptions() : this.getSingleOptions())
                });

                if ('custom' === settings.data_source) {
                    var _this = this;
                    columnsData.forEach(function (element) {

                        if ("pie" !== type && "doughnut" !== type && "polarArea" !== type) {
                            if ("object" === typeof element.backgroundColor) {

                                //We need to make sure add gradient colors or not.
                                if ("empty" !== element.backgroundColor[element.backgroundColor.length - 1]) {
                                    var gradient = ctx.createLinearGradient(0, 0, 0, 600),
                                        secondColor = element.backgroundColor[1] ?
                                            element.backgroundColor[1] :
                                            element.backgroundColor[0];

                                    gradient.addColorStop(0, element.backgroundColor[0]);
                                    gradient.addColorStop(1, secondColor);
                                    element.backgroundColor = gradient;
                                    element.hoverBackgroundColor = gradient;
                                }

                            }
                        }

                        data.datasets.push(element);

                        _this.chartInstance.update();
                    });

                    $('#premium-chart-canvas-' + widgetID).on("click", function (evt) {
                        var activePoint = _this.chartInstance.getElementAtEvent(evt);
                        if (activePoint[0]) {
                            var URL =
                                _this.chartInstance.data.datasets[activePoint[0]._datasetIndex].links[
                                activePoint[0]._index
                                ];
                            if (URL != null && URL != "") {
                                window.open(URL, settings.y_axis_urls_target ? '_blank' : '_top');
                            }
                        }
                    });

                }

            },

            getChartData: function () {

                var dataSource = this.getElementSettings('data_source'),
                    columnsData = this.columnsData,
                    _this = this;

                if ('custom' === dataSource) {
                    this.renderChart();
                } else {

                    var $chartElem = this.elements.$chartElem;

                    $chartElem.append('<div class="premium-loading-feed"><div class="premium-loader"></div></div>');

                    if (columnsData.url) {
                        $.ajax({
                            url: columnsData.url,
                            type: "GET",
                            success: function (res) {
                                $chartElem.find(".premium-loading-feed").remove();
                                _this.renderCSVChart(res);
                            },
                            error: function (err) {
                                console.log(err);
                            }
                        });
                    }

                }

            },

            renderCSVChart: function (res) {

                var widgetID = this.getID(),
                    ctx = document.getElementById('premium-chart-canvas-' + widgetID).getContext("2d"),
                    _this = this,
                    rowsData = res.split(/\r?\n|\r/),
                    columnsData = this.columnsData,
                    labels = (rowsData.shift()).split(columnsData.separator),
                    globalOptions = this.getGlobalOptions(ctx),
                    settings = this.getElementSettings(),
                    type = settings.type,
                    data = {
                        labels: labels,
                        datasets: []
                    };


                this.chartInstance = new Chart(ctx, {
                    type: type,
                    data: data,
                    options: Object.assign(globalOptions, ("radar" !== type && "polarArea" !== type) ? this.getMultiOptions() : this.getSingleOptions())
                });

                rowsData.forEach(function (row, index) {
                    if (row.length !== 0) {
                        var colData = {};

                        colData.data = row.split(columnsData.separator);
                        //add properties only if repeater element exists
                        if (columnsData.props[index]) {
                            colData.borderColor = columnsData.props[index].borderColor;
                            colData.borderWidth = columnsData.props[index].borderWidth;
                            colData.backgroundColor = columnsData.props[index].backgroundColor;
                            colData.hoverBackgroundColor = columnsData.props[index].hoverBackgroundColor;
                            colData.label = columnsData.props[index].title;
                        }

                        data.datasets.push(colData);
                        _this.chartInstance.update();

                    }
                });

            }

        });


        // Instagram Feed Handler
        var instaCounter = 0,
            PremiumInstaFeedHandler = function ($scope, $) {
                instaCounter++;

                var $instaElem = $scope.find(".premium-instafeed-container"),
                    $loading = $instaElem.find(".premium-loading-feed"),
                    settings = $instaElem.data("settings"),
                    carousel = $instaElem.data("carousel");

                if (!settings)
                    return;

                var feed = new Instafeed({
                    api: settings.api,
                    target: settings.id,
                    feed: settings.feed,
                    get: "user",
                    tagName: settings.tags,
                    sortBy: settings.sort,
                    limit: settings.limit,
                    videos: settings.videos,
                    words: settings.words,
                    overlay: settings.overlay,
                    templateData: {
                        likes: settings.likes,
                        comments: settings.comments,
                        description: settings.description,
                        link: settings.link,
                        share: settings.share
                    },
                    afterLoad: function () {

                        //Remove loading spinner
                        $loading.removeClass("premium-show-loading");

                        setTimeout(function () {
                            $($instaElem).find(".premium-insta-feed-wrap a[data-rel^='prettyPhoto']")
                                .prettyPhoto({
                                    theme: settings.theme,
                                    hook: "data-rel",
                                    opacity: 0.7,
                                    show_title: false,
                                    deeplinking: false,
                                    overlay_gallery: false,
                                    custom_markup: "",
                                    default_width: 900,
                                    default_height: 506,
                                    social_tools: ""
                                });


                            $instaElem.imagesLoaded(function () {

                                if (carousel) {
                                    instaCarouselHandler();
                                } else if (settings.masonry) {
                                    instagramMasonryGrid();
                                }

                                $scope.find(".elementor-invisible").removeClass("elementor-invisible");

                            });

                        }, 100);

                    }
                });

                try {
                    feed.run();
                } catch (err) {
                    console.log(err);
                }



                function instagramMasonryGrid() {
                    $instaElem.isotope({
                        itemSelector: ".premium-insta-feed",
                        percentPosition: true,
                        layoutMode: "masonry",
                        animationOptions: {
                            duration: 750,
                            easing: "linear",
                            queue: false
                        }
                    });

                }

                function instaCarouselHandler() {

                    var autoPlay = $instaElem.data("play"),
                        speed = $instaElem.data("speed"),
                        rtl = $instaElem.data("rtl"),
                        colsNumber = $instaElem.data("col"),
                        colsNumberTablet = $instaElem.data("col-tab"),
                        colsNumberMobile = $instaElem.data("col-mobile"),
                        prevArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                        nextArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>';

                    $instaElem.find(".premium-insta-grid").slick({
                        infinite: true,
                        slidesToShow: colsNumber,
                        slidesToScroll: colsNumber,
                        responsive: [{
                            breakpoint: 1025,
                            settings: {
                                slidesToShow: colsNumberTablet,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: colsNumberMobile,
                                slidesToScroll: 1
                            }
                        }
                        ],
                        autoplay: autoPlay,
                        autoplaySpeed: speed,
                        rows: 0,
                        rtl: rtl ? true : false,
                        nextArrow: nextArrow,
                        prevArrow: prevArrow,
                        draggable: true,
                        pauseOnHover: true
                    });
                }

                //Handle Instagram Videos
                if (settings.videos) {
                    $instaElem.on('click', '.premium-insta-video-wrap', function () {
                        var $instaVideo = $(this).find("video");
                        $instaVideo.get(0).play();
                        $instaVideo.css("visibility", "visible");
                    });
                }
            };

        // Facebook Feed Handler
        var PremiumFacebookHandler = ModuleHandler.extend({

            getDefaultSettings: function () {
                return {
                    selectors: {
                        elementWrap: '.premium-social-feed-element-wrap',
                        feedWrapper: '.premium-facebook-feed-wrapper',
                        loader: '.premium-loading-feed',

                    }
                }
            },

            getDefaultElements: function () {
                var selectors = this.getSettings('selectors'),
                    elements = {
                        $elementWrap: this.$element.find(selectors.elementWrap),
                        $feedWrapper: this.$element.find(selectors.feedWrapper),
                        $loader: this.$element.find(selectors.loader),
                    };

                return elements;
            },

            bindEvents: function () {
                this.run();
            },

            run: function () {

                var $loader = this.elements.$loader;

                this.elements.$elementWrap.remove();

                $.ajax({
                    url: this.get_facebook_data(),
                    beforeSend: function () {
                        $loader.addClass("premium-show-loading");
                    },
                    error: function () {
                        console.log("error getting data from Facebook");
                    }
                });

            },

            get_facebook_data: function () {

                var _this = this,
                    $paFbElem = this.elements.$feedWrapper,
                    $loader = this.elements.$loader,
                    settings = this.getElementSettings(),
                    id = this.$element.data('id'),
                    widgetSettings = $paFbElem.data('settings');

                $paFbElem
                    .find(".premium-social-feed-container")
                    .socialfeed({
                        facebook: {
                            accounts: ['!' + settings.account_id],
                            limit: settings.post_number || 2,
                            access_token: settings.access_token,
                        },
                        length: settings.content_length || 130,
                        show_media: 'yes' === settings.posts_media,
                        readMore: settings.read_text,
                        template: widgetSettings.template,
                        adminPosts: settings.admin_posts,
                        callback: function () {
                            $loader.removeClass("premium-show-loading");
                            $paFbElem.imagesLoaded(function () {
                                _this.handleFacebookFeed();
                            });
                        }
                    });
            },

            //new function for handling carousel option
            handleFacebookFeed: function () {

                var $paFbElem = this.elements.$feedWrapper,
                    settings = this.getElementSettings(),
                    widgetSettings = $paFbElem.data('settings');

                if ('yes' === settings.feed_carousel) {

                    var autoPlay = 'yes' === settings.carousel_play,
                        speed = settings.carousel_autoplay_speed || 5000,
                        rtl = elementorFrontend.config.is_rtl,
                        colsNumber = $paFbElem.data("col"),
                        prevArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                        nextArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>';

                    $paFbElem.find(".premium-social-feed-container").slick({
                        infinite: true,
                        slidesToShow: colsNumber,
                        slidesToScroll: colsNumber,
                        responsive: [{
                            breakpoint: 1025,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: 1,
                                slidesToScroll: 1
                            }
                        }
                        ],
                        autoplay: autoPlay,
                        autoplaySpeed: speed,
                        rows: 0,
                        rtl: rtl ? true : false,
                        nextArrow: nextArrow,
                        prevArrow: prevArrow,
                        draggable: true,
                        pauseOnHover: true
                    });
                }

                if ('yes' != settings.feed_carousel && widgetSettings.layout === "grid-layout" && !widgetSettings.even) {

                    var masonryContainer = $paFbElem.find(".premium-social-feed-container");

                    masonryContainer.isotope({
                        itemSelector: ".premium-social-feed-element-wrap",
                        percentPosition: true,
                        layoutMode: "masonry",
                        animationOptions: {
                            duration: 750,
                            easing: "linear",
                            queue: false
                        }
                    });
                }
            }

        });

        // Tabs Handler
        var PremiumTabsHandler = ModuleHandler.extend({

            settings: {},

            getDefaultSettings: function () {
                return {
                    selectors: {
                        premiumTabsElem: '.premium-tabs',
                        navList: '.premium-tabs-nav-list',
                        navListItem: '.premium-tabs-nav-list-item',
                        contentWrap: '.premium-content-wrap',
                        currentTab: '.premium-tabs-nav-list-item.tab-current',
                        currentClass: 'tab-current',
                        tabContent: '.premium-accordion-tab-content'
                    }
                }
            },

            getDefaultElements: function () {
                var selectors = this.getSettings('selectors'),
                    elements = {
                        $premiumTabsElem: this.$element.find(selectors.premiumTabsElem),
                        $navList: this.$element.find(selectors.navList).first(),
                        $contentWrap: this.$element.find(selectors.contentWrap),
                        $navListItem: this.$element.find(selectors.navListItem),
                    };

                return elements;
            },

            bindEvents: function () {
                this.run();
            },

            run: function () {

                var _this = this,
                    $premiumTabsElem = this.elements.$premiumTabsElem,
                    $navList = this.elements.$navList,
                    currentDevice = elementorFrontend.getCurrentDeviceMode(),
                    elementSettings = this.getElementSettings(),
                    selectors = this.getSettings('selectors'),
                    navigation = [];

                //Fix conflict issue when shortcodes are added in tabs content.
                if ('object' === typeof elementSettings.premium_tabs_repeater) {
                    elementSettings.premium_tabs_repeater.forEach(function (item) {
                        navigation.push(item.custom_tab_navigation);
                    });
                }

                this.settings = {
                    id: '#premium-tabs-' + this.$element.data('id'),
                    start: parseInt(elementSettings.default_tab_index),
                    autoChange: elementSettings.autochange,
                    delay: elementSettings.autochange_delay,
                    list: this.elements.$navListItem,
                    carousel: elementSettings.carousel_tabs,
                    accordion: elementSettings.accordion_tabs,
                    tabColor: elementSettings.premium_tab_background_color,
                    activeTabColor: elementSettings.premium_tab_active_background_color
                };

                //apply outline on the tabs
                if (this.settings.activeTabColor) {
                    if ('00' === this.settings.activeTabColor.slice(-2)) {

                        var tabsType = elementSettings.premium_tab_type,
                            borderSide = 'horizontal' === tabsType ? "top" : elementorFrontend.config.is_rtl ? "right" : "left";

                        borderSide += "-color";

                        $premiumTabsElem.find(".premium-tab-arrow").css("border-" + borderSide, "#fff");
                    }
                }

                if (this.settings.carousel) {
                    if (-1 === elementSettings.carousel_tabs_devices.indexOf(currentDevice)) {
                        this.settings.carousel = false;
                        $premiumTabsElem.removeClass("elementor-invisible");
                    }

                }

                //We use another if condition to check the carousel tabs again if disabled on the current device.
                if (this.settings.carousel) {

                    //Make sure slick is initialized before showing tabs.
                    $navList.on('init', function () {
                        $premiumTabsElem.removeClass("elementor-invisible");
                    });

                    elementorFrontend.waypoint(
                        $navList,
                        function () {
                            $navList.slick(_this.getSlickSettings());
                        }
                    );

                    $navList.on('click', selectors.navListItem, function () {
                        var tabIndex = $(this).data("slick-index");
                        $navList.slick('slickGoTo', tabIndex);
                    });

                    //activate tab only if carousel tabs number is odd.
                    $navList.on("afterChange", function () {
                        var numberOfTabs = _this.getCarouselTabs();

                        if (1 === numberOfTabs % 2)
                            $navList.find(".premium-tabs-nav-list-item.slick-current").trigger('click');
                    });

                    //Fix arrow pointer not showing on small screens.
                    if ('style1' === elementSettings.premium_tab_style_selected) {

                        setTimeout(function () {
                            var navListHeight = $navList.find(".slick-list").eq(0).outerHeight();
                            $navList.find(".slick-list").outerHeight(navListHeight + 10);
                        }, 2500);

                    }

                }

                if (this.settings.accordion && elementSettings.accordion_tabs_devices.includes(currentDevice)) {
                    this.$element.find('.premium-tabs, .premium-tabs-nav-list').addClass('premium-accordion-tabs');
                    this.elements.$contentWrap.css('display', 'none');
                } else {
                    new CBPFWTabs($premiumTabsElem, this.settings);
                }

                $(document).ready(function () {

                    if (_this.settings.accordion && elementSettings.accordion_tabs_devices.includes(currentDevice)) {

                        $premiumTabsElem.find(".premium-content-wrap").remove();
                        _this.changeToAccodrion();

                    } else {
                        //Check this as it conflicts with Elementor swiper.
                        // if ($premiumTabsElem.find(".swiper-container").length < 1) {
                        //The comment removed because it will conflict if the swiper is a media carousel and the images are anchors.
                        $premiumTabsElem.find(".premium-accordion-tab-content").remove();
                        // }

                    }

                    navigation.map(function (item, index) {
                        if (item) {
                            $(item).on("click", function () {

                                if (!_this.settings.carousel) {
                                    var $tabToActivate = $navList.find("li.premium-tabs-nav-list-item").eq(index);

                                    $tabToActivate.trigger('click');
                                } else {
                                    var activeTabHref = _this.elements.$contentWrap.find("section").eq(index).attr("id");

                                    $("a[href=#" + activeTabHref + "]").eq(1).trigger('click');

                                    _this.$element.find(".slick-current").trigger('click');
                                }

                            });
                        }
                    });
                });

            },

            getCarouselTabs: function () {
                var elementSettings = this.getElementSettings(),
                    currentDevice = elementorFrontend.getCurrentDeviceMode(),
                    numberofTabs = 5;

                switch (true) {
                    case currentDevice.includes('mobile'):
                        numberofTabs = elementSettings.tabs_number_mobile;
                        break;
                    case currentDevice.includes('tablet'):
                        numberofTabs = elementSettings.tabs_number_tablet;
                        break;
                    default:
                        numberofTabs = elementSettings.tabs_number;
                        break;
                }

                return numberofTabs;

            },

            getSlickSettings: function () {

                var elementSettings = this.getElementSettings(),
                    prevArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                    nextArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>',
                    slides_tab = elementSettings.tabs_number_tablet,
                    slides_mob = elementSettings.tabs_number_mobile,
                    spacing_tab = elementSettings.slides_spacing_tablet,
                    spacing_mob = elementSettings.slides_spacing_mobile;

                return {
                    infinite: true,
                    autoplay: false,
                    rows: 0,
                    dots: false,
                    useTransform: true,
                    centerMode: true,
                    draggable: false,
                    slidesToShow: elementSettings.tabs_number || 5,
                    responsive: [{
                        breakpoint: 1025,
                        settings: {
                            slidesToShow: slides_tab,
                            centerPadding: spacing_tab + "px",
                            nextArrow: elementSettings.carousel_arrows_tablet ? nextArrow : '',
                            prevArrow: elementSettings.carousel_arrows_tablet ? prevArrow : '',
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: slides_mob,
                            centerPadding: spacing_mob + "px",
                            nextArrow: elementSettings.carousel_arrows_mobile ? nextArrow : '',
                            prevArrow: elementSettings.carousel_arrows_mobile ? prevArrow : '',
                        }
                    }
                    ],
                    rtl: elementorFrontend.config.is_rtl,
                    nextArrow: elementSettings.carousel_arrows ? nextArrow : '',
                    prevArrow: elementSettings.carousel_arrows ? prevArrow : '',
                    centerPadding: elementSettings.slides_spacing + "px",
                }

            },

            changeToAccodrion: function () {

                var $premiumTabsElem = this.elements.$premiumTabsElem,
                    _this = this,
                    $navListItem = this.elements.$navListItem,
                    elementSettings = this.getElementSettings(),
                    selectors = this.getSettings('selectors'),
                    accDur = elementSettings.accordion_tabs_anim_duration,
                    scrollAfter = elementSettings.accordion_animation;

                if (-1 !== this.settings.start) {
                    $premiumTabsElem.find(".premium-tabs-nav-list-item:eq(" + this.settings.start + ")").addClass("tab-current");
                }

                var contentId = $premiumTabsElem.find(selectors.currentTab).data('content-id');

                $premiumTabsElem.find('.premium-accordion-tab-content:not(' + contentId + ')').slideUp(accDur, 'swing');

                $navListItem.click(function () {

                    var $clickedTab = $(this);

                    if ($clickedTab.hasClass(selectors.currentClass)) {

                        $clickedTab.toggleClass(selectors.currentClass);

                        _this.$element.find($clickedTab.data('content-id')).slideUp(accDur, 'swing');

                    } else {

                        $navListItem.removeClass(selectors.currentClass);

                        $clickedTab.toggleClass(selectors.currentClass);

                        _this.$element.find(selectors.tabContent).slideUp(accDur, 'swing');

                        _this.$element.find($clickedTab.data('content-id')).slideDown(accDur, 'swing');
                    }

                    if (scrollAfter) {
                        setTimeout(function () {
                            $('html, body').animate({
                                scrollTop: $clickedTab.offset().top - 100
                            }, 1000);
                        }, accDur === 'slow' ? 700 : 400);
                    }

                });

            },

        });

        window.CBPFWTabs = function (t, settings) {

            var self = this,
                id = settings.id,
                i = settings.start,
                isClicked = false,
                canNavigate = true;

            self.el = t;

            self.options = {
                start: i
            };

            self.extend = function (t, s) {
                for (var i in s) s.hasOwnProperty(i) && (t[i] = s[i]);
                return t;
            };

            self._init = function () {

                self.tabs = $(id).find(".premium-tabs-nav").first().find("li.premium-tabs-nav-list-item");
                self.items = $(id).find(".premium-content-wrap").first().find("> section");
                self.carouselTabs = $(id).find('.premium-tabs-nav-list').first();

                self.current = -1;

                //No tabs will be active by default.
                if (-1 !== self.options.start) {
                    self._show();
                }

                self._initEvents();

                if (settings.autoChange) {
                    self.runAutoNavigation();
                }

                if (settings.carousel) {
                    self.carouselTabs.on('beforeChange', function () {
                        canNavigate = false;
                    });

                    self.carouselTabs.on('afterChange', function () {
                        canNavigate = true;
                    });

                }
            };

            self._initEvents = function () {

                self.tabs.each(function (index, tab) {

                    var listIndex = $(tab).data("list-index");

                    $(tab).find("a.premium-tab-link").on("click", function (s) {
                        s.preventDefault();
                    });

                    $(tab).on("click", function (s) {

                        //If a tab is clicked by the user, not auto navigation
                        if (s.originalEvent)
                            isClicked = true;

                        s.preventDefault();

                        //If tab is clicked twice
                        if ($(tab).hasClass("tab-current"))
                            return;

                        self._show(listIndex, tab);
                    });
                });
            };

            self._show = function (tabIndex, tab) {

                if (!canNavigate)
                    return;

                if (self.current >= 0) {
                    self.tabs.removeClass("tab-current");
                    self.items.removeClass("content-current");
                }

                self.current = tabIndex;

                //Activate the first tab if no tab is clicked yet
                if (void 0 == tabIndex) {
                    self.current = self.options.start >= 0 && self.options.start < self.items.length ? self.options.start : 0;
                    tab = settings.carousel ? self.tabs.filter(".slick-center:not(.slick-cloned)") : self.tabs[self.current];
                }

                self.tabs.removeClass("premium-zero-height");

                if (settings.carousel) {

                    setTimeout(function () {
                        var $currentActivetab = self.tabs.filter(".slick-center:not(.slick-cloned)");
                        $currentActivetab.addClass("tab-current");

                        //Hide separator for arrow pointer if active background is set
                        // if (settings.tabColor !== settings.activeTabColor && 'undefined' != typeof settings.activeTabColor) {
                        //     $currentActivetab.addClass("premium-zero-height");
                        //     $currentActivetab.prev().addClass("premium-zero-height");
                        // }

                    }, 100);
                } else {
                    $(tab).addClass("tab-current");

                    //Hide separator for arrow pointer if active background is set
                    // if (settings.tabColor !== settings.activeTabColor && 'undefined' != typeof settings.activeTabColor) {

                    //     $(tab).prevAll('.premium-tabs-nav-list-item').first().addClass("premium-zero-height");
                    //     $(tab).addClass("premium-zero-height");
                    // }

                }

                var $activeContent = self.items.eq(self.current);

                $activeContent.addClass("content-current");

                if ($('.premium-mscroll-yes').length < 1 && $('.premium-hscroll-outer-wrap').length < 1)
                    window.dispatchEvent(new Event('resize'));

                //Fix Media Grid height issue.
                if ($activeContent.find(".premium-gallery-container").length > 0) {
                    var $mediaGrid = $activeContent.find(".premium-gallery-container"),
                        layout = $mediaGrid.data("settings").img_size;
                    setTimeout(function () {

                        // if (0 === $mediaGrid.outerHeight()) {
                        if ('metro' === layout) {
                            $mediaGrid.trigger('resize');
                        } else {
                            $mediaGrid.isotope("layout");
                        }
                        // }

                    }, 100);

                }


                if (self.items.find(".slick-slider").length > 0)
                    self.items.find(".slick-slider").slick('pause').slick('freezeAnimation');

                if ($activeContent.find(".slick-slider").length > 0) {

                    setTimeout(function () {

                        $activeContent.find(".slick-slider").slick("play").slick("resumeAnimation");

                        $activeContent.find(".slick-slider").slick('setPosition').slick('setPosition');
                    }, 100);

                }

                //Make sure videos are paused
                if (self.items.find("video").length > 0) {
                    self.items.not(".content-current").find("video").each(function (index, elem) {
                        $(elem).get(0).pause();
                    });
                }

                if (self.items.find("iframe").length > 0) {
                    self.items.not(".content-current").find("iframe").each(function (index, elem) {

                        var source = $(elem).parent().attr("data-src");

                        $(elem).attr("src", source);
                    });
                }

            };

            self.runAutoNavigation = function () {

                var $navListItem = settings.list,
                    index = settings.start > 0 ? settings.start : 0;

                var autoChangeInterval = setInterval(function () {

                    //If user clicks a tab, then stop auto navigation.
                    if (isClicked) {
                        clearInterval(autoChangeInterval);
                        return;
                    }

                    index++;

                    if (index > $navListItem.length - 1)
                        index = 0;

                    $navListItem.eq(index).trigger('click');
                }, settings.delay * 1000);

            };

            self.options = self.extend({}, self.options);
            self.extend(self.options, i);
            self._init();

        };


        // Magic Section Handler
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


        // Preview Window Handler
        var PremiumPreviewWindowHandler = function ($scope, $) {
            var $prevWinElem = $scope.find(".premium-preview-image-wrap"),
                settings = $prevWinElem.data("settings"),
                currentDevice = elementorFrontend.getCurrentDeviceMode(),
                minWidth = null,
                maxWidth = null;

            if (-1 !== currentDevice.indexOf("mobile")) {
                minWidth = settings.minWidthMobs;
                maxWidth = settings.maxWidthMobs;
                //We need to make sure that content will not go out of screen.
                settings.side = ['top', 'bottom'];
            } else if (-1 !== currentDevice.indexOf("tablet")) {
                minWidth = settings.minWidthTabs;
                maxWidth = settings.maxWidthTabs;
            } else {
                minWidth = settings.minWidth;
                maxWidth = settings.maxWidth;
            }

            if (settings.responsive) {

                var previewImageOffset = $prevWinElem.offset().left;

                if (previewImageOffset < settings.minWidth) {
                    var difference = settings.minWidth - previewImageOffset;

                    settings.minWidth = settings.minWidth - difference;
                }
            }


            var $figure = $prevWinElem.find('.premium-preview-image-figure'),
                floatData = $figure.data();

            if (floatData.float) {

                if ($scope.hasClass("pa-previmg-disable-fe-yes")) {

                    if (window.paCheckSafari)
                        return;
                }

                var animeSettings = {
                    targets: $figure[0],
                    loop: true,
                    direction: 'alternate',
                    easing: 'easeInOutSine'
                };

                if (floatData.floatTranslate) {

                    animeSettings.translateX = {
                        duration: floatData.floatTranslateSpeed * 1000,
                        value: [floatData.floatxStart || 0, floatData.floatxEnd || 0]
                    };

                    animeSettings.translateY = {
                        duration: floatData.floatTranslateSpeed * 1000,
                        value: [floatData.floatyStart || 0, floatData.floatyEnd || 0]
                    };

                }

                if (floatData.floatRotate) {

                    animeSettings.rotateX = {
                        duration: floatData.floatRotateSpeed * 1000,
                        value: [floatData.rotatexStart || 0, floatData.rotatexEnd || 0]
                    };

                    animeSettings.rotateY = {
                        duration: floatData.floatRotateSpeed * 1000,
                        value: [floatData.rotateyStart || 0, floatData.rotateyEnd || 0]
                    };

                    animeSettings.rotateZ = {
                        duration: floatData.floatRotateSpeed * 1000,
                        value: [floatData.rotatezStart || 0, floatData.rotatezEnd || 0]
                    };

                }

                if (floatData.floatOpacity) {
                    animeSettings.opacity = {
                        duration: floatData.floatOpacitySpeed * 1000,
                        value: floatData.floatOpacityValue || 0
                    };
                }

                anime(animeSettings);

            }

            // if interactive is enabled and delay = 0 >> make out-delay more than zero to enable interactive.
            var delay = 0 === settings.delay && settings.active ? [0, 0.1] : settings.delay;

            $prevWinElem.find(".premium-preview-image-inner-trig-img").tooltipster({
                functionBefore: function () {
                    if (settings.hideMobiles && ['mobile', 'mobile_extra'].includes(currentDevice)) {
                        return false;
                    }
                },
                functionInit: function (instance, helper) {
                    var content = $(helper.origin).find("#tooltip_content").detach();
                    instance.content(content);
                },
                functionReady: function () {
                    $(".tooltipster-box").addClass("tooltipster-box-" + settings.id);

                    //prevent class overlapping.
                    var premElements = $('.tooltipster-box-' + settings.id),
                        length = premElements.length;

                    if (premElements.length > 1) {
                        delete premElements[length - 1];
                        premElements.removeClass('tooltipster-box-' + settings.id);
                    }

                },
                contentCloning: true,
                plugins: ['sideTip'],
                animation: settings.anim,
                animationDuration: settings.animDur,
                delay: delay,
                updateAnimation: null,
                trigger: "custom",
                triggerOpen: {
                    tap: true,
                    mouseenter: true
                },
                triggerClose: {
                    tap: true,
                    mouseleave: true
                },
                arrow: settings.arrow,
                contentAsHTML: true,
                autoClose: false,
                maxWidth: maxWidth,
                minWidth: minWidth,
                distance: settings.distance,
                interactive: settings.active,
                minIntersection: 16,
                side: settings.side
            });

        };

        // Behance Feed Handler
        var PremiumBehanceFeedHandler = function ($scope, $) {
            var $behanceElem = $scope.find(".premium-behance-container"),
                $loading = $scope.find(".premium-loading-feed"),
                settings = $behanceElem.data("settings");

            function get_behance_data() {

                $behanceElem.embedBehance({
                    apiKey: 'XQhsS66hLTKjUoj8Gky7FOFJxNMh23uu',
                    userName: settings.username,
                    project: 'yes' === settings.project,
                    owners: 'yes' === settings.owner,
                    appreciations: 'yes' === settings.apprectiations,
                    views: 'yes' === settings.views,
                    publishedDate: 'yes' === settings.date,
                    fields: 'yes' === settings.fields,
                    projectUrl: 'yes' === settings.url,
                    infiniteScrolling: false,
                    description: 'yes' === settings.desc,
                    animationEasing: "easeInOutExpo",
                    ownerLink: true,
                    tags: true,
                    containerId: settings.id,
                    itemsPerPage: settings.number,
                    coverSize: settings.cover_size
                });
            }

            elementorFrontend.waypoint(
                $scope,
                function () {

                    $.ajax({
                        url: get_behance_data(),
                        beforeSend: function () {
                            $loading.addClass("premium-show-loading");
                        },
                        success: function () {
                            $loading.removeClass("premium-show-loading");
                        },
                        error: function () {
                            console.log("error getting data from Behance");
                        }
                    });

                }
            );

        };

        // Image Layers Handler
        var PremiumImageLayersHandler = function ($scope, $) {

            var $imgLayers = $scope.find(".premium-img-layers-wrapper"),
                currentDevice = elementorFrontend.getCurrentDeviceMode(),
                layers = $imgLayers.find(".premium-img-layers-list-item"),
                applyOn = $imgLayers.data("devices"),
                disableFEOnSafari = $scope.hasClass("pa-imglayers-disable-fe-yes");

            layers.each(function (index, layer) {
                var $layer = $(layer),
                    data = $layer.data(),
                    hideOn = data.layerHide,
                    isRemoved = false;

                if ('object' == typeof hideOn && hideOn.length > 0) {

                    hideOn.map(function (device) {

                        if ('desktop' === device && -1 == currentDevice.indexOf('mobile') && -1 == currentDevice.indexOf('tablet')) {
                            $layer.remove();
                            isRemoved = true;
                        } else if (-1 !== currentDevice.indexOf(device)) {
                            $layer.remove();
                            isRemoved = true;
                        }

                    });
                }

                if (isRemoved)
                    return;

                if (data.scrolls) {
                    if (-1 !== applyOn.indexOf(currentDevice)) {

                        var instance = null,
                            effects = [],
                            vScrollSettings = {},
                            hScrollSettings = {},
                            oScrollSettings = {},
                            bScrollSettings = {},
                            rScrollSettings = {},
                            scaleSettings = {},
                            grayScaleSettings = {},
                            settings = {};

                        if (data.scrolls) {

                            if (data.vscroll) {
                                effects.push('translateY');
                                vScrollSettings = {
                                    speed: data.vscrollSpeed,
                                    direction: data.vscrollDir,
                                    range: {
                                        start: data.vscrollStart,
                                        end: data.vscrollEnd
                                    }
                                };
                            }
                            if (data.hscroll) {
                                effects.push('translateX');
                                hScrollSettings = {
                                    speed: data.hscrollSpeed,
                                    direction: data.hscrollDir,
                                    range: {
                                        start: data.hscrollStart,
                                        end: data.hscrollEnd
                                    }
                                };
                            }
                            if (data.oscroll) {
                                effects.push('opacity');
                                oScrollSettings = {
                                    level: data.oscrollLevel,
                                    fade: data.oscrollEffect,
                                    range: {
                                        start: data.oscrollStart,
                                        end: data.oscrollEnd
                                    }
                                };
                            }
                            if (data.bscroll) {
                                effects.push('blur');
                                bScrollSettings = {
                                    level: data.bscrollLevel,
                                    blur: data.bscrollEffect,
                                    range: {
                                        start: data.bscrollStart,
                                        end: data.bscrollEnd
                                    }
                                };
                            }
                            if (data.rscroll) {
                                effects.push('rotate');
                                rScrollSettings = {
                                    speed: data.rscrollSpeed,
                                    direction: data.rscrollDir,
                                    range: {
                                        start: data.rscrollStart,
                                        end: data.rscrollEnd
                                    }
                                };
                            }
                            if (data.scale) {
                                effects.push('scale');
                                scaleSettings = {
                                    speed: data.scaleSpeed,
                                    direction: data.scaleDir,
                                    range: {
                                        start: data.scaleStart,
                                        end: data.scaleEnd
                                    }
                                };
                            }
                            if (data.gscale) {
                                effects.push('gray');
                                grayScaleSettings = {
                                    level: data.gscaleLevel,
                                    gray: data.gscaleEffect,
                                    range: {
                                        start: data.gscaleStart,
                                        end: data.gscaleEnd
                                    }
                                };
                            }

                        }

                        settings = {
                            elType: 'Widget',
                            vscroll: vScrollSettings,
                            hscroll: hScrollSettings,
                            oscroll: oScrollSettings,
                            bscroll: bScrollSettings,
                            rscroll: rScrollSettings,
                            scale: scaleSettings,
                            gscale: grayScaleSettings,
                            effects: effects
                        };

                        instance = new premiumImageLayersEffects(layer, settings);
                        instance.init();

                    }

                } else if (data.float) {
                    if (disableFEOnSafari) {
                        if (window.paCheckSafari)
                            return;
                    }

                    var floatXSettings = null,
                        floatYSettings = null,
                        floatRotateXSettings = null,
                        floatRotateYSettings = null,
                        floatRotateZSettings = null;

                    var animeSettings = {
                        targets: $layer[0],
                        loop: true,
                        direction: 'alternate',
                        easing: 'easeInOutSine'
                    };

                    if (data.floatTranslate) {

                        floatXSettings = {
                            duration: data.floatTranslateSpeed * 1000,
                            value: [data.floatxStart || 0, data.floatxEnd || 0]
                        };

                        animeSettings.translateX = floatXSettings;

                        floatYSettings = {
                            duration: data.floatTranslateSpeed * 1000,
                            value: [data.floatyStart || 0, data.floatyEnd || 0]
                        };

                        animeSettings.translateY = floatYSettings;

                    }

                    if (data.floatRotate) {

                        floatRotateXSettings = {
                            duration: data.floatRotateSpeed * 1000,
                            value: [data.rotatexStart || 0, data.rotatexEnd || 0]
                        };

                        animeSettings.rotateX = floatRotateXSettings;

                        floatRotateYSettings = {
                            duration: data.floatRotateSpeed * 1000,
                            value: [data.rotateyStart || 0, data.rotateyEnd || 0]
                        };

                        animeSettings.rotateY = floatRotateYSettings;

                        floatRotateZSettings = {
                            duration: data.floatRotateSpeed * 1000,
                            value: [data.rotatezStart || 0, data.rotatezEnd || 0]
                        };

                        animeSettings.rotateZ = floatRotateZSettings;

                    }

                    if (data.floatOpacity) {
                        animeSettings.opacity = {
                            duration: data.floatOpacitySpeed * 1000,
                            value: data.floatOpacityValue || 0
                        };
                    }


                    anime(animeSettings);
                }

                if ($layer.data("layer-animation") && " " != $layer.data("layer-animation")) {

                    new Waypoint({
                        element: $($imgLayers),
                        offset: Waypoint.viewportHeight() - 150,
                        handler: function () {

                            $layer.addClass("animated " + $layer.data("layer-animation"));

                            //Opacity should sync animation delay before setting it to 1.
                            var animationDelay = $layer.css("animation-delay") ? parseFloat($layer.css("animation-delay").replace("s", "")) : 0;

                            setTimeout(function () {
                                $layer.css("opacity", 1);
                            }, animationDelay * 1000);
                        }
                    });
                }

                if ($layer.hasClass('premium-mask-yes')) {
                    var html = '';
                    $layer.find('.premium-img-layers-text').text().split(' ').forEach(function (word) {
                        html += ' <span class="premium-mask-span">' + word + '</span>';
                    });

                    $layer.find('.premium-img-layers-text').text('').append(html);

                    elementorFrontend.waypoint($scope, function () {
                        $layer.find('.premium-img-layers-text').addClass('premium-mask-active');
                    }, {
                        offset: Waypoint.viewportHeight() - 150,
                        triggerOnce: true
                    });
                }

            });


            $imgLayers.find('.premium-img-layers-list-item[data-parallax="true"]').each(function () {

                var $this = $(this),
                    resistance = $(this).data("rate"),
                    reverse = -1;

                if ($this.data("mparallax-reverse"))
                    reverse = 1;

                $imgLayers.mousemove(function (e) {
                    TweenLite.to($this, 0.2, {
                        x: reverse * ((e.clientX - window.innerWidth / 2) / resistance),
                        y: reverse * ((e.clientY - window.innerHeight / 2) / resistance)
                    });
                });

                if ($this.data("mparallax-init")) {
                    $imgLayers.mouseleave(function () {
                        TweenLite.to($this, 0.4, {
                            x: 0,
                            y: 0
                        });
                    });
                }

            });



            var tilts = $imgLayers.find('.premium-img-layers-list-item[data-tilt="true"]');

            if (tilts.length > 0) {
                tilt = UniversalTilt.init({
                    elements: tilts,
                    callbacks: {
                        onMouseLeave: function (el) {
                            el.style.boxShadow = "0 45px 100px rgba(255, 255, 255, 0)";
                        },
                        onDeviceMove: function (el) {
                            el.style.boxShadow = "0 45px 100px rgba(255, 255, 255, 0.3)";
                        }
                    }
                });
            }

        };

        // Image Layers Editor Handler
        var PremiumImageLayersEditorHandler = function ($scope, $) {

            var $imgLayers = $scope.find(".premium-img-layers-wrapper"),
                settings = {
                    repeater: 'premium_img_layers_images_repeater',
                    item: '.premium-img-layers-list-item',
                    width: 'premium_img_layers_width',
                    hor: 'premium_img_layers_hor_position',
                    ver: 'premium_img_layers_ver_position',
                    tab: 'premium_img_layers_content',
                    offset: 0,
                    widgets: ["drag", "resize"]
                },
                instance = null;

            instance = new premiumEditorBehavior($imgLayers, settings);
            instance.init();

        };

        // Image Comparison Handler
        var PremiumImageCompareHandler = function ($scope, $) {

            var $imgCompareElem = $scope.find(".premium-images-compare-container"),
                settings = $imgCompareElem.data("settings");

            $imgCompareElem.imagesLoaded(function () {
                $imgCompareElem.twentytwenty({
                    orientation: settings.orientation,
                    default_offset_pct: settings.visibleRatio,
                    switch_before_label: settings.switchBefore,
                    before_label: settings.beforeLabel,
                    switch_after_label: settings.switchAfter,
                    after_label: settings.afterLabel,
                    move_slider_on_hover: settings.mouseMove,
                    click_to_move: settings.clickMove,
                    show_drag: settings.showDrag,
                    show_sep: settings.showSep,
                    no_overlay: settings.overlay,
                    horbeforePos: settings.beforePos,
                    horafterPos: settings.afterPos,
                    verbeforePos: settings.verbeforePos,
                    verafterPos: settings.verafterPos
                });
            });
        };

        // Content Switcher Handler
        var PremiumContentToggleHandler = function ($scope, $) {

            var $contentToggle = $scope.find(".premium-content-toggle-container");

            var radioSwitch = $contentToggle.find(".premium-content-toggle-switch"),
                contentList = $contentToggle.find(".premium-content-toggle-two-content");

            changeSwitcherLayout();

            $(window).on('resize', function () {
                changeSwitcherLayout();
            })

            radioSwitch.prop('checked', false);

            var sides = {};
            sides[0] = contentList.find(
                'li[data-type="premium-content-toggle-monthly"]'
            );
            sides[1] = contentList.find(
                'li[data-type="premium-content-toggle-yearly"]'
            );

            radioSwitch.on("click", function (event) {

                var selected_filter = $(event.target).val();

                if ($(this).hasClass("premium-content-toggle-switch-active")) {

                    selected_filter = 0;

                    $(this).toggleClass(
                        "premium-content-toggle-switch-normal premium-content-toggle-switch-active"
                    );

                    hide_not_selected_items(sides, selected_filter);

                } else if ($(this).hasClass("premium-content-toggle-switch-normal")) {

                    selected_filter = 1;

                    $(this).toggleClass(
                        "premium-content-toggle-switch-normal premium-content-toggle-switch-active"
                    );

                    hide_not_selected_items(sides, selected_filter);

                }
            });

            function changeSwitcherLayout() {

                $contentToggle.removeClass('premium-toggle-stack-yes premium-toggle-stack-no');

                var computedStyle = getComputedStyle($scope[0]);

                $contentToggle.addClass("premium-toggle-stack-" + computedStyle.getPropertyValue('--pa-content-toggle-stack'));

            }

            function hide_not_selected_items(sides, filter) {
                $.each(sides, function (key, value) {
                    if (key != filter) {
                        $(this)
                            .removeClass("premium-content-toggle-is-visible")
                            .addClass("premium-content-toggle-is-hidden");
                    } else {
                        $(this)
                            .addClass("premium-content-toggle-is-visible")
                            .removeClass("premium-content-toggle-is-hidden");
                    }
                });
            }
        };

        // Hotspots Handler
        var PremiumImageHotspotHandler = function ($scope, $) {

            var $hotspotsElem = $scope.find(".premium-image-hotspots-container"),
                hotspots = $hotspotsElem.find(".tooltip-wrapper"),
                settings = $hotspotsElem.data("settings"),
                isEdit = elementorFrontend.isEditMode(),
                currentDevice = elementorFrontend.getCurrentDeviceMode(),
                triggerClick = null,
                triggerHover = null,
                triggerClose = true,
                touchDevices = ['tablet', 'mobile', 'tablet_extra', 'mobile_extra'];

            //Tooltips to be opened by default.
            var $openedByDefault = $hotspotsElem.find(".tooltip-wrapper[data-active=true]");

            //Always trigger on click on touch devices
            if (touchDevices.includes(currentDevice) || settings.trigger === "click") {
                triggerClick = true;
                triggerHover = false;
            } else if (settings.trigger === "hover") {
                triggerClick = false;
                triggerHover = true;
            }

            if ('' !== settings.iconHover) {
                $hotspotsElem.find('.premium-image-hotspots-main-icons > svg').addClass('elementor-animation-' + settings.iconHover);
            }

            var $floatElem = $hotspotsElem.find('.premium-image-hotspots-img-wrap'),
                floatData = $floatElem.data();

            if (floatData.float) {

                if ($scope.hasClass("pa-hotspots-disable-fe-yes")) {
                    if (window.paCheckSafari)
                        return;
                }

                var animeSettings = {
                    targets: $floatElem[0],
                    loop: true,
                    direction: 'alternate',
                    easing: 'easeInOutSine'
                };

                if (floatData.floatTranslate) {

                    animeSettings.translateX = {
                        duration: floatData.floatTranslateSpeed * 1000,
                        value: [floatData.floatxStart || 0, floatData.floatxEnd || 0]
                    };

                    animeSettings.translateY = {
                        duration: floatData.floatTranslateSpeed * 1000,
                        value: [floatData.floatyStart || 0, floatData.floatyEnd || 0]
                    };

                }

                if (floatData.floatRotate) {

                    animeSettings.rotateX = {
                        duration: floatData.floatRotateSpeed * 1000,
                        value: [floatData.rotatexStart || 0, floatData.rotatexEnd || 0]
                    };

                    animeSettings.rotateY = {
                        duration: floatData.floatRotateSpeed * 1000,
                        value: [floatData.rotateyStart || 0, floatData.rotateyEnd || 0]
                    };

                    animeSettings.rotateZ = {
                        duration: floatData.floatRotateSpeed * 1000,
                        value: [floatData.rotatezStart || 0, floatData.rotatezEnd || 0]
                    };

                }

                if (floatData.floatOpacity) {
                    animeSettings.opacity = {
                        duration: floatData.floatOpacitySpeed * 1000,
                        value: floatData.floatOpacityValue || 0
                    };
                }

                anime(animeSettings);

            }

            if (settings.trigger === "click") {
                triggerClose = 'yes' === settings.triggerClose;
            }

            hotspots.tooltipster({
                functionBefore: function () {

                    $openedByDefault.map(function (index, elem) {
                        $(elem).tooltipster('instance').close();
                    });

                    if (!triggerClose) {

                        $('#tooltip_content .premium-world-clock__time-wrapper').addClass('premium-addons__v-hidden');
                        $('#tooltip_content .premium-weather__outer-wrapper').css({ visibility: 'hidden', opacity: 0 });

                        $hotspotsElem.find(".tooltip-wrapper").map(function (index, elem) {
                            $(elem).tooltipster('instance').close();
                        });
                    }

                    if (settings.hideMobiles && "mobile" === currentDevice)
                        return false;
                },
                functionInit: function (instance, helper) {

                    if (!helper)
                        return;

                    if (isEdit) {

                        var templateID = $(helper.origin).data('template-id');
                        if (undefined !== templateID && '' !== templateID) {
                            $.ajax({
                                type: 'GET',
                                url: PremiumProSettings.ajaxurl,
                                data: {
                                    action: 'get_elementor_template_content',
                                    templateID: templateID
                                }
                            }).success(function (response) {
                                var data;

                                try {
                                    data = JSON.parse(response).data;
                                } catch (error) {
                                    data = response.data;
                                }

                                if (undefined !== data.template_content) {
                                    instance.content(data.template_content);
                                }
                            });
                        }
                    }

                    var content = $(helper.origin).find("#tooltip_content").detach();
                    instance.content(content);

                    $(helper.origin).find(".premium-image-hotspots-tooltips-wrapper").remove();

                },
                functionReady: function (origin, tooltipObj) {

                    var $tooltipContent = $(tooltipObj.tooltip);

                    $tooltipContent.find('.premium-world-clock__time-wrapper').css('opacity', 1);
                    $tooltipContent.find('.premium-weather__outer-wrapper').css({ visibility: 'visible', opacity: 1 });

                    // render lottie animation.
                    if ($tooltipContent.find('.premium-lottie-animation').length) {
                        var instance = new premiumLottieAnimations($tooltipContent);
                        instance.init();
                    }

                    if ($(tooltipObj.origin).find('.magnet-spot').length > 0)
                        $tooltipContent.css('cursor', 'none');

                    if (!$(".tooltipster-base").hasClass('premium-tooltipster-base')) { // make sure 'premium-tooltipster-base' is added
                        $(".tooltipster-base").addClass('premium-tooltipster-base');
                    }

                    if (!$(".tooltipster-base").hasClass('premium-hotspots-tooltip')) { // make sure 'premium-hotspots-tooltip' is added
                        $(".tooltipster-base").addClass('premium-hotspots-tooltip');
                    }

                    $(".tooltipster-box").addClass("tooltipster-box-" + settings.id);
                    $(".tooltipster-arrow").addClass("tooltipster-arrow-" + settings.id);

                    //Used to refresh the tooltip position to fix issues when large tooltip padding is added
                    hotspots.tooltipster('reposition');

                },
                contentCloning: true,
                plugins: ["sideTip"],
                animation: settings.anim,
                animationDuration: settings.animDur,
                delay: [settings.delay, 0.001],
                trigger: "custom",
                triggerOpen: {
                    click: triggerClick,
                    tap: true,
                    mouseenter: triggerHover
                },
                triggerClose: {
                    click: triggerClose,
                    tap: true,
                    mouseleave: triggerHover
                },
                arrow: settings.arrow,
                contentAsHTML: true,
                autoClose: false,
                minWidth: settings.minWidth,
                maxWidth: settings.maxWidth,
                distance: settings.distance,
                interactive: settings.active,
                minIntersection: 16,
                side: settings.side,
                functionPosition: function (instance, helper, position) {

                    var customHorPos = $(helper.origin).data('tooltip-h'),
                        customVerPos = $(helper.origin).data('tooltip-v'),
                        widgetPos = $floatElem[0].getBoundingClientRect();

                    if (customHorPos) {
                        customHorPos = customHorPos / 100;
                        position.coord.left = widgetPos.x + (widgetPos.width * customHorPos)
                    }


                    if (customVerPos) {
                        customVerPos = customVerPos / 100;
                        position.coord.top = widgetPos.y + (widgetPos.height * customVerPos)
                    }

                    return position;
                }
            });

            $openedByDefault.map(function (index, elem) {

                $(elem).tooltipster('instance').open();
            });

        };

        // Hotspots Editor Handler
        var PremiumImageHotspotEditorHandler = function ($scope, $) {

            var $hotspotsElem = $scope.find(".premium-image-hotspots-container"),
                settings = {
                    repeater: 'premium_image_hotspots_icons',
                    item: '.premium-image-hotspots-main-icons',
                    hor: 'preimum_image_hotspots_main_icons_horizontal_position',
                    ver: 'preimum_image_hotspots_main_icons_vertical_position',
                    tab: 'premium_image_hotspots_icons_settings',
                    offset: 1,
                    widgets: ["drag"]
                },
                instance = null;

            instance = new premiumEditorBehavior($hotspotsElem, settings);
            instance.init();

        };

        // Table Handler
        var PremiumTableHandler = function ($scope, $) {

            var $tableElem = $scope.find(".premium-table"),
                $premiumTableWrap = $scope.find(".premium-table-wrap"),
                settings = $tableElem.data("settings");

            if (!settings)
                return;

            //Table Sort
            if (settings.sort) {
                if (
                    $(window).outerWidth() > 767 ||
                    ($(window).outerWidth() < 767 && settings.sortMob)
                ) {
                    $tableElem.tablesorter({
                        cssHeader: "premium-table-sort-head",
                        cssAsc: "premium-table-up",
                        cssDesc: "premium-table-down",
                        usNumberFormat: settings.usNumbers,
                        sortReset: true,
                        sortRestart: true
                    });
                } else {
                    $tableElem.find(".premium-table-sort-icon").css("display", "none");
                }
            }

            //Table search
            if (settings.search) {
                $premiumTableWrap.find(".premium-table-search-field").keyup(function () {

                    _this = this;

                    $tableElem.find("tbody tr").each(function () {

                        if ($(this).text().toLowerCase().indexOf($(_this).val().toLowerCase()) === -1) {
                            $(this).addClass("premium-table-search-hide");
                        } else {

                            $(this).removeClass("premium-table-search-hide");
                            if ($(this).hasClass('premium-table-row-hidden')) {
                                $(this).removeClass("premium-table-row-hidden").addClass('hidden-by-default');
                            }

                        }

                    });

                    if ($(_this).val().toLowerCase().length === 0) {
                        $tableElem.find(".hidden-by-default").each(function () {
                            $(this).addClass('premium-table-row-hidden').removeClass('hidden-by-default');
                        });
                    }
                });

            }

            //Table show records
            if (settings.records) {

                $premiumTableWrap
                    .find(".premium-table-records-box")
                    .on("change", function () {
                        var rows = $(this)
                            .find("option:last")
                            .val(),
                            value = parseInt(this.value);

                        if (1 === value) {
                            $tableElem.find("tbody tr").not(".premium-table-search-hide").removeClass("premium-table-hide");
                        } else {
                            $tableElem.find("tbody tr:gt(" + (value - 2) + ")").not(".premium-table-search-hide").addClass("premium-table-hide");

                            $tableElem.find("tbody tr:lt(" + (value - 1) + ")").not(".premium-table-search-hide").removeClass("premium-table-hide");
                        }
                    });
            }

            //Tables with CSV Files
            if (settings.dataType === "csv" && '' != settings.csvFile) {

                //If the file is Google Spreadsheet, then we need to  use CORS Proxy.
                // if (-1 !== settings.csvFile.indexOf("docs.google.com"))
                //     settings.csvFile = "https://cors-anywhere.herokuapp.com/" + settings.csvFile;

                $.ajax({
                    url: PremiumProSettings.ajaxurl,
                    type: "POST",
                    data: {
                        action: 'handle_table_data',
                        id: settings.id,
                        security: PremiumProSettings.nonce,
                    },
                    success: function (res) {

                        if (res.data) {

                            handleCsvData(res.data);

                            if (settings.pagination === "yes")
                                handleTablePagination();

                        } else {

                            $.ajax({
                                url: settings.csvFile,
                                type: "GET",
                                success: function (res) {

                                    $.ajax({
                                        url: PremiumProSettings.ajaxurl,
                                        type: "POST",
                                        data: {
                                            action: 'handle_table_data',
                                            id: settings.id,
                                            expire: settings.reload,
                                            tableData: res,
                                            security: PremiumProSettings.nonce,
                                        },
                                        success: function (res) {
                                            console.log(res);
                                        }
                                    });

                                    if (!res)
                                        return;

                                    handleCsvData(res);

                                    if (settings.pagination === "yes")
                                        handleTablePagination();

                                },
                                error: function (err) {
                                    console.log(err);
                                }
                            });

                        }

                    }
                });

                //Handle CSV Data
                function handleCsvData(data) {

                    var rowsData = data.split(/\r?\n|\r/),
                        firstRow = settings.firstRow,
                        table_data = "head" === firstRow ? '<thead class="premium-table-head">' : '<tbody class="premium-table-body">';

                    for (var count = 0; count < rowsData.length; count++) {
                        var cell_data = rowsData[count].split(settings.separator);
                        table_data += '<tr class="premium-table-row">';
                        for (
                            var cell_count = 0; cell_count < cell_data.length; cell_count++
                        ) {
                            if (count === 0 && "head" === firstRow) {
                                table_data +=
                                    '<th class="premium-table-cell"><span class="premium-table-text">' +
                                    cell_data[cell_count];
                                table_data += "</span></th>";
                            } else {
                                table_data +=
                                    '<td class="premium-table-cell"><span class="premium-table-text">' +
                                    cell_data[cell_count] +
                                    "</span></td>";
                            }
                        }
                        table_data += "</tr>";
                        if (count === 0 && "head" === firstRow) {
                            table_data += "</thead>";
                        }
                    }
                    $tableElem.html("");
                    $tableElem.html(table_data);
                }

            }


            if (settings.dataType === "custom" && settings.pagination === "yes")
                handleTablePagination();

            function handleTablePagination() {

                var tableRows = $tableElem.find("tbody tr").length,
                    pages = Math.ceil(tableRows / settings.rows);

                $tableElem.find("tbody tr:gt(" + (settings.rows - 1) + ")").addClass("premium-table-row-hidden");

                var paginationHtml = '';
                for (var count = 0; count < pages; count++) {
                    var current = 0 === count ? "current" : "";
                    paginationHtml += "<li><a href='#' class='page-numbers " + current + "' data-page='" + count + "'>" + (count + 1) + "</a></li>";
                }

                $scope.find(".premium-table-pagination li").eq(0).after(paginationHtml);


                $scope.on("click", ".premium-table-pagination li a", function (e) {
                    e.preventDefault();

                    var $this = $(this);

                    if ($this.hasClass("current") || $this.hasClass("custom-page"))
                        return;

                    $premiumTableWrap.append('<div class="premium-loading-feed"><div class="premium-loader"></div></div>');

                    setTimeout(function () {

                        var page = $this.data("page");

                        $tableElem.find("tbody tr").removeClass("premium-table-row-hidden");

                        $scope.find(".premium-table-pagination a.current").removeClass("current");

                        if (!$this.hasClass("prev") && !$this.hasClass("next"))
                            $this.addClass("current");

                        if ($this.hasClass('next') || (pages - 1) === page) {
                            $tableElem.find("tbody tr:lt(" + ((pages - 1) * settings.rows) + ")").addClass("premium-table-row-hidden");
                        } else if ($this.hasClass('prev') || 0 === page) {
                            $tableElem.find("tbody tr:gt(" + (settings.rows - 1) + ")").addClass("premium-table-row-hidden");
                        } else {
                            var gt = ((page + 1) * settings.rows - 1);
                            $tableElem.find("tbody tr:gt(" + gt + ")").addClass("premium-table-row-hidden");
                            $tableElem.find("tbody tr:lt(" + page * settings.rows + ")").addClass("premium-table-row-hidden");
                        }

                        $premiumTableWrap.find(".premium-loading-feed").remove();
                        $('html, body').animate({
                            scrollTop: (($premiumTableWrap.offset().top) - 100)
                        }, 'slow');
                    }, 1000);

                });


            }

        };

        // Reviews Handler
        var PremiumReviewHandler = ModuleHandler.extend({

            getDefaultSettings: function () {

                return {
                    selectors: {
                        premiumRevElem: '.premium-fb-rev-container',
                        revsContainer: '.premium-fb-rev-reviews',
                        dotsContainer: '.premium-fb-dots-container',
                        dotsElem: '.slick-dots',
                        revPage: '.premium-fb-rev-page',
                        nextPage: 'premium-fb-page-next-yes',
                        emptyDots: '.premium-fb-empty-dots',
                        reviewWrap: '.premium-fb-rev-review-wrap'
                    },
                }
            },

            getDefaultElements: function () {

                var selectors = this.getSettings('selectors'),
                    elements = {
                        $premiumRevElem: this.$element.find(selectors.premiumRevElem),
                        $revsContainer: this.$element.find(selectors.revsContainer),
                        $reviewWrap: this.$element.find(selectors.reviewWrap),
                    };

                elements.$revPage = elements.$premiumRevElem.find(selectors.revPage);

                return elements;
            },

            bindEvents: function () {
                this.run();
            },

            run: function () {

                var carousel = this.getElementSettings('reviews_carousel'),
                    revStyle = this.getElementSettings('reviews_style'),
                    $revsContainer = this.elements.$revsContainer,
                    $premiumRevElem = this.elements.$premiumRevElem,
                    slickSettings = this.getSlickSettings(),
                    selectors = this.getSettings('selectors');

                if (carousel) {

                    var isInfinite = this.getElementSettings('infinite_autoplay');

                    if ("even" === revStyle && isInfinite) {

                        var $reviewWrap = this.elements.$reviewWrap,
                            heights = new Array();

                        $reviewWrap.each(function (index, rev) {

                            var height = $(rev).outerHeight();

                            heights.push(height);
                        });

                        var maxHeight = Math.max.apply(null, heights);

                        $reviewWrap.css("height", maxHeight + "px");

                    }

                    $revsContainer.slick(slickSettings.settings);
                }

                if ((slickSettings.general.dots && this.$element.hasClass(selectors.nextPage)) || (slickSettings.general.dots && slickSettings.general.arrows)) {

                    $('<div class="premium-fb-dots-container"></div>').appendTo($premiumRevElem);

                    var $dotsContainer = $premiumRevElem.find(selectors.dotsContainer),
                        $dotsElem = $revsContainer.find(selectors.dotsElem);

                    $('<div class="premium-fb-empty-dots"></div>').appendTo($dotsContainer);

                    $($dotsElem).appendTo($dotsContainer);

                    if (this.$element.hasClass(selectors.nextPage)) {
                        var pageWidth = this.elements.$revPage.outerWidth();

                        $dotsContainer.find(selectors.emptyDots).css('width', pageWidth + 'px');
                    }
                }

                if ("masonry" === revStyle && 1 !== slickSettings.general.colsNumber && !carousel) {
                    $revsContainer.isotope(this.getIsotopeSettings());
                }

            },

            getSlickSettings: function () {

                var settings = this.getElementSettings(),
                    slickCols = this.getSlickCols(),
                    generalSettings = {
                        autoPlay: 'yes' === settings.carousel_play ? true : false,
                        infinite: 'yes' === settings.infinite_autoplay ? true : false,
                        colsNumber: slickCols.colsNumber,
                        colsNumberTablet: slickCols.colsNumberTablet,
                        colsNumberMobile: slickCols.colsNumberMobile,
                        speed: settings.carousel_autoplay_speed || 5000,
                        dots: ['all', 'dots'].includes(settings.carousel_navigation) ? true : false,
                        arrows: ['all', 'arrows'].includes(settings.carousel_navigation) ? true : false,
                        prevArrow: '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Next" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                        nextArrow: '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>'
                    };

                generalSettings.rows = generalSettings.infinite ? settings.rows : 0;

                return {
                    general: generalSettings,
                    settings: {
                        infinite: true,
                        slidesToShow: generalSettings.colsNumber,
                        slidesToScroll: generalSettings.infinite ? 1 : generalSettings.colsNumber,
                        responsive: [{
                            breakpoint: 1025,
                            settings: {
                                slidesToShow: generalSettings.infinite ? 1 : generalSettings.colsNumberTablet,
                                slidesToScroll: 1,
                                autoplaySpeed: generalSettings.speed,
                                speed: 300,
                                centerMode: generalSettings.infinite ? true : false,
                                centerPadding: '30px',
                            }
                        },
                        {
                            breakpoint: 768,
                            settings: {
                                slidesToShow: generalSettings.infinite ? 1 : generalSettings.colsNumberMobile,
                                slidesToScroll: 1,
                                autoplaySpeed: generalSettings.speed,
                                speed: 300,
                                centerMode: generalSettings.infinite ? true : false,
                                centerPadding: '30px',
                            }
                        }
                        ],
                        useTransform: true,
                        autoplay: generalSettings.infinite ? true : generalSettings.autoPlay,
                        speed: generalSettings.infinite ? generalSettings.speed : 300,
                        autoplaySpeed: generalSettings.infinite ? 0 : generalSettings.speed,
                        rows: generalSettings.rows,
                        rtl: elementorFrontend.config.is_rtl,
                        arrows: generalSettings.arrows,
                        nextArrow: generalSettings.nextArrow,
                        prevArrow: generalSettings.prevArrow,
                        draggable: true,
                        pauseOnHover: generalSettings.infinite ? false : true,
                        dots: generalSettings.dots,
                        cssEase: generalSettings.infinite ? "linear" : "ease",
                        customPaging: function () {
                            return '<i class="fas fa-circle"></i>';
                        },
                    }
                }

            },

            getSlickCols: function () {
                var slickCols = this.getElementSettings(),
                    colsNumber = slickCols.reviews_columns,
                    colsNumberTablet = slickCols.reviews_columns_tablet || colsNumber,
                    colsNumberMobile = slickCols.reviews_columns_mobile || colsNumber;

                return {
                    colsNumber: parseInt(100 / colsNumber.substr(0, colsNumber.indexOf('%'))),
                    colsNumberTablet: parseInt(100 / colsNumberTablet.substr(0, colsNumberTablet.indexOf('%'))),
                    colsNumberMobile: parseInt(100 / colsNumberMobile.substr(0, colsNumberMobile.indexOf('%'))),
                }

            },

            getIsotopeSettings: function () {
                return {
                    itemSelector: ".premium-fb-rev-review-wrap",
                    percentPosition: true,
                    layoutMode: "masonry",
                    animationOptions: {
                        duration: 750,
                        easing: "linear",
                        queue: false
                    }
                }
            },

        });


        // Divider Handler
        var PremiumDividerHandler = function ($scope, $) {
            var $divider = $scope.find(".premium-separator-container"),
                sepSettings = $divider.data("settings"),
                leftBackground = null,
                rightBackground = null;

            if ("custom" === sepSettings) {
                leftBackground = $divider
                    .find(".premium-separator-left-side")
                    .data("background");

                $divider
                    .find(".premium-separator-left-side hr")
                    .css("border-image", "url( " + leftBackground + " ) 20% round");

                rightBackground = $divider
                    .find(".premium-separator-right-side")
                    .data("background");

                $divider
                    .find(".premium-separator-right-side hr")
                    .css("border-image", "url( " + rightBackground + " ) 20% round");
            }

        };

        // Whatsapp Chat Handler
        var $whatsappElemHandler = function ($scope, $) {
            var $whatsappElem = $scope.find(".premium-whatsapp-container"),
                settings = $whatsappElem.data("settings"),
                currentDevice = elementorFrontend.getCurrentDeviceMode();

            if (settings.hideMobile) {
                if ("mobile" === currentDevice) {
                    $($whatsappElem).css("display", "none");
                }
            } else if (settings.hideTab) {
                if ("tablet" === currentDevice) {
                    $($whatsappElem).css("display", "none");
                }
            }

            if (settings.tooltips) {
                $whatsappElem.find(".premium-whatsapp-link").tooltipster({
                    functionInit: function (instance, helper) {
                        var content = $(helper.origin)
                            .find("#tooltip_content")
                            .detach();
                        instance.content(content);
                    },
                    functionReady: function () {
                        $(".tooltipster-box").addClass(
                            "tooltipster-box-" + settings.id
                        );
                    },
                    animation: settings.anim,
                    contentCloning: true,
                    trigger: "hover",
                    arrow: true,
                    contentAsHTML: true,
                    autoClose: false,
                    minIntersection: 16,
                    interactive: true,
                    delay: 0,
                    side: ["right", "left", "top", "bottom"]
                });
            }
        };

        // Multi Scroll Handler
        var PremiumScrollHandler = function ($scope, $) {
            var $elem = $scope.find(".premium-multiscroll-wrap"),
                settings = $elem.data("settings"),
                id = settings["id"];

            function loadMultiScroll() {
                $("#premium-scroll-nav-menu-" + id).removeClass(
                    "premium-scroll-responsive"
                );

                $("#premium-multiscroll-" + id).multiscroll({
                    verticalCentered: true,
                    menu: "#premium-scroll-nav-menu-" + id,
                    leftSelector: ".premium-multiscroll-left-" + id,
                    rightSelector: ".premium-multiscroll-right-" + id,
                    sectionSelector: ".premium-multiscroll-temp-" + id,
                    sectionsColor: [],
                    keyboardScrolling: settings.keyboard,
                    navigation: settings.dots,
                    navigationPosition: settings.dotsPos,
                    navigationVPosition: settings.dotsVPos,
                    navigationTooltips: settings.dotsText,
                    navigationColor: "#000",
                    loopBottom: settings.btmLoop,
                    loopTop: settings.topLoop,
                    css3: true,
                    paddingTop: 0,
                    paddingBottom: 0,
                    normalScrollElements: null,
                    touchSensitivity: 5,
                    anchors: settings.anchors,
                    fit: settings.fit,
                    cellHeight: settings.cellHeight,
                    id: id,
                    leftWidth: settings.leftWidth,
                    rightWidth: settings.rightWidth,
                    entranceAnimation: settings.entranceAnimation
                });

                $(fixedContent[0]).removeClass('premium-addons__v-hidden');


                if (settings.entranceAnimation) {
                    $(fixedContent[0]).addClass('animated ' + settings.entranceAnimation);
                }
            }

            var leftTemps = $elem.find(".premium-multiscroll-left-temp"),
                rightTemps = $elem.find(".premium-multiscroll-right-temp"),
                fixedContent = $elem.find(".premium-multiscroll-fixed-temp"),
                hideTabs = settings.hideTabs,
                hideMobs = settings.hideMobs,
                deviceType = $("body").data("elementor-device-mode"),
                navArray = leftTemps.data("navigation"),
                currentDevice = elementorFrontend.getCurrentDeviceMode(),
                count = leftTemps.length;

            function reOrderTemplates() {

                $elem.parents(".elementor-top-section").removeClass("elementor-section-height-full");

                $.each(rightTemps, function (index) {

                    if (settings.rtl) {

                        $(leftTemps[index]).insertAfter(rightTemps[index]);

                    } else {

                        $(rightTemps[index]).insertAfter(leftTemps[index]);

                    }
                });
                $($elem).find(".premium-multiscroll-inner").removeClass("premium-scroll-fit").css("min-height", settings["cellHeight"] + "px");
            }

            switch (true) {
                case hideTabs && hideMobs:
                    if (!deviceType.includes("tablet") && !deviceType.includes("mobile")) {
                        loadMultiScroll();
                    } else {
                        reOrderTemplates();
                    }
                    break;
                case hideTabs && !hideMobs:
                    if (!deviceType.includes("tablet")) {
                        loadMultiScroll();
                    } else {
                        reOrderTemplates();
                    }
                    break;
                case !hideTabs && hideMobs:
                    if (!deviceType.includes("mobile")) {
                        loadMultiScroll();
                    } else {
                        reOrderTemplates();
                    }
                    break;
                case !hideTabs && !hideMobs:
                    loadMultiScroll();
                    break;
            }

            function hideTemplate(template) {

                if (0 !== count) {
                    count--;
                    $(template).addClass('premium-multiscroll-hide');
                }
            }

            leftTemps.each(function (index, template) {

                var hideOn = $(template).data('hide');

                if (-1 < hideOn.indexOf(currentDevice)) {

                    hideTemplate(template);
                }
            });

            rightTemps.each(function (index, template) {

                var hideOn = $(template).data('hide');

                if (-1 < hideOn.indexOf(currentDevice)) {

                    hideTemplate(template);
                }
            });

            $(document).ready(function () {

                navArray.map(function (item, index) {
                    if (item) {

                        $(item).on("click", function () {

                            $("#premium-multiscroll-" + id).multiscroll.moveTo(index);

                        })
                    }

                });

            })

        };


        var PremiumImageAccordionHandler = ModuleHandler.extend({

            getDefaultSettings: function () {

                return {
                    selectors: {
                        accordionElem: '.premium-accordion-section',
                        accordionItems: '.premium-accordion-li',
                        accordionTemplate: '.premium-accord-temp',
                        accordionDesc: '.premium-accordion-description'
                    }
                }

            },
            getDefaultElements: function () {

                var selectors = this.getSettings('selectors');

                return {
                    $accordionElem: this.$element.find(selectors.accordionElem),
                    $accordionItems: this.$element.find(selectors.accordionItems),
                    $accordionTemplate: this.$element.find(selectors.accordionTemplate),
                    $accordionDesc: this.$element.find(selectors.accordionDesc)
                }

            },
            bindEvents: function () {
                this.run();
            },

            run: function () {

                var $window = $(window),
                    $accordionElem = this.elements.$accordionElem,
                    $accordionItems = this.elements.$accordionItems;

                if (elementorFrontend.isEditMode()) {
                    this.checkAccordionTemps();
                }

                //Trigger Hovered Image Width Function on page load only if Default Index option value is set.
                if ($accordionElem.find('.premium-accordion-li-active').length > 0) {
                    this.resizeImgs();
                }

                var _this = this,
                    hideDesc = this.getElementSettings('hide_description_thresold');

                $window.resize(function () {
                    _this.elements.$accordionDesc.css('display', hideDesc > $window.outerWidth() ? 'none' : 'block');

                    _this.resizeImgs();
                });

                $accordionItems.hover(function () {

                    $accordionItems.removeClass('premium-accordion-li-active');

                    if (!$(this).hasClass('premium-accordion-li-active')) {

                        $(this).addClass('premium-accordion-li-active');
                    }

                    _this.resizeImgs();
                });

                $accordionItems.mouseleave(function () {
                    $accordionElem.find('.premium-accordion-li, .premium-accordion-ul, .premium-accordion-overlay-wrap').attr('style', '');
                    $accordionItems.removeClass('premium-accordion-li-active');
                });

            },
            checkAccordionTemps: function () {

                var $window = $(window);

                this.elements.$accordionTemplate.each(function (index, img) {

                    var templateID = $(img).data("template");

                    if (undefined !== templateID && '' !== templateID) {
                        $.ajax({
                            type: "GET",
                            url: PremiumProSettings.ajaxurl,
                            dataType: "html",
                            data: {
                                action: "get_elementor_template_content",
                                templateID: templateID
                            }
                        }).success(function (response) {

                            var data;

                            try {
                                data = JSON.parse(response).data;
                            } catch (error) {
                                data = response.data;
                            }

                            if (undefined !== data.template_content) {
                                $(img).html(data.template_content);
                                $window.resize();
                            }
                        });
                    }
                });

            },

            resizeImgs: function () {

                var settings = this.getElementSettings(),
                    $accordionElem = this.elements.$accordionElem,
                    $accordionItems = this.elements.$accordionItems,
                    count = $accordionItems.length,
                    currentDevice = elementorFrontend.getCurrentDeviceMode(),
                    suffix = 'desktop' === currentDevice ? '' : '_' + currentDevice;

                var imgWidth = settings['active_img_size' + suffix].size;

                if ('horizontal' === settings.direction_type) {

                    if (imgWidth) {
                        var width = 'width: calc( (100% - ' + imgWidth + '% ) /' + (count - 1) + ')';
                        $accordionElem.find('.premium-accordion-li:not(.premium-accordion-li-active)').attr('style', width);
                    }

                } else {

                    var imgHeight = settings['height' + suffix].size,
                        initialHeight = ('' === imgHeight) ? 200 : imgHeight,
                        height = ('' === imgWidth) ? 400 : initialHeight * count * imgWidth * 0.01;

                    $accordionElem.find('.premium-accordion-li-active').attr('style', 'height: ' + height + 'px !important');

                    if ('' !== imgWidth) {
                        $accordionElem.find('.premium-accordion-li:not(.premium-accordion-li-active)').attr('style', 'height: calc( (' + initialHeight * count + 'px - ' + height + 'px ) /' + (count - 1) + ')');
                    }

                }

                if (100 === imgWidth) {
                    $accordionItems.css({
                        'padding': 0,
                        'margin': 0
                    });

                    $accordionElem.find('.premium-accordion-overlay-wrap').css('width', '100%');
                    $accordionElem.find('.premium-accordion-ul').css('borderSpacing', '0 0');
                }

            }


        });

        // Background Transition Handler
        var PremiumColorTransitionHandler = ModuleHandler.extend({

            settings: {},

            getDefaultSettings: function () {
                return {
                    selectors: {
                        scrollElement: '.premium-scroll-background',
                    }
                }
            },

            getDefaultElements: function () {
                var selectors = this.getSettings('selectors'),
                    elements = {
                        $scrollElement: this.$element.find(selectors.scrollElement)
                    };

                return elements;
            },

            bindEvents: function () {

                var _this = this,
                    //Used to delay trigger if content will be shown on the page. For example, Black Friday bar.
                    delay = _this.$element.hasClass("delay-trigger") ? 500 : 0;

                setTimeout(function () {
                    _this.setWidgetSettings();
                    _this.run();
                }, delay)

            },

            setWidgetSettings: function () {

                var repeaterSettings = this.getRepeaterSettings(),
                    currentDevice = elementorFrontend.getCurrentDeviceMode();

                var layoutSettings = {
                    offset: null,
                    isNull: false,
                    isSolid: true,
                    elements: repeaterSettings.elements,
                    downColors: repeaterSettings.downColors,
                    upColors: repeaterSettings.upColors,
                    itemsIDs: repeaterSettings.itemsIDs,
                    downOffsets: repeaterSettings.downOffsets,
                    upOffsets: repeaterSettings.upOffsets,
                    id: this.$element.data('id'),
                    offset: 'offset' + ('desktop' === currentDevice ? '' : '_' + currentDevice)
                };

                layoutSettings.$firstElement = $('#' + layoutSettings.elements[0]);
                layoutSettings.$lastElement = $('#' + layoutSettings.elements[layoutSettings.elements.length - 1]);

                // we need to check if elements really exists before proceeding forward
                if (layoutSettings.$firstElement.length && layoutSettings.$lastElement.length) {

                    layoutSettings.firstElemOffset = layoutSettings.$firstElement.offset().top;
                    layoutSettings.lastElemOffset = layoutSettings.$lastElement.offset().top;
                    layoutSettings.lastElemeHeight = layoutSettings.$lastElement.outerHeight();
                    layoutSettings.lastElemeBot = layoutSettings.lastElemOffset + layoutSettings.lastElemeHeight;
                }

                this.settings = layoutSettings;

            },

            run: function () {

                var _this = this,
                    $window = $(window),
                    $scrollElement = this.elements.$scrollElement;

                //Widget Settings
                var elements = this.settings.elements,
                    downColors = this.settings.downColors,
                    downOffsets = this.settings.downOffsets,
                    upOffsets = this.settings.upOffsets,
                    itemsIDs = this.settings.itemsIDs;

                //Make sure all IDs refer to existing elements.
                for (var i = 0; i < elements.length; i++) {
                    if (!$('#' + elements[i]).length) {
                        $scrollElement.html('<div class="premium-error-notice">Please make sure that IDs added to the widget are valid</div>');
                        this.settings.isNull = true;
                        break;
                    }
                }

                if (this.settings.isNull)
                    return;

                //Change to desktop offset if empty.
                if (undefined == this.getElementSettings(this.settings.offset))
                    this.settings.offset = 'offset';

                $('<div id="premium-color-transition-' + this.settings.id + '" class="premium-color-transition"></div>').prependTo($('body'));

                $(document).ready(function () {
                    if ($('.premium-color-transition').length > 1)
                        $window.on('scroll', _this.checkVisible);
                });

                downColors.forEach(function (color) {
                    if (-1 !== color.indexOf('//'))
                        _this.settings.isSolid = false;
                });

                if (!this.getElementSettings('gradient'))
                    this.settings.isSolid = false;

                if (this.settings.isSolid) {

                    this.rowTransitionalColor = function ($row, firstColor, secondColor) {
                        "use strict";

                        var firstColor = _this.hexToRgb(firstColor),
                            secondColor = _this.hexToRgb(secondColor);

                        var scrollPos = 0,
                            currentRow = $row,
                            beginningColor = firstColor,
                            endingColor = secondColor,
                            percentScrolled, newRed, newGreen, newBlue, newColor;

                        $(document).scroll(function () {
                            var animationBeginPos = currentRow.offset().top,
                                endPart = currentRow.outerHeight() / 4,
                                animationEndPos = animationBeginPos + currentRow.outerHeight() - endPart;

                            scrollPos = $(this).scrollTop();

                            if (scrollPos >= animationBeginPos && scrollPos <= animationEndPos) {
                                percentScrolled = (scrollPos - animationBeginPos) / (currentRow.outerHeight() - endPart);
                                newRed = Math.abs(beginningColor.r + (endingColor.r - beginningColor.r) * percentScrolled);
                                newGreen = Math.abs(beginningColor.g + (endingColor.g - beginningColor.g) * percentScrolled);
                                newBlue = Math.abs(beginningColor.b + (endingColor.b - beginningColor.b) * percentScrolled);

                                newColor = "rgb(" + newRed + "," + newGreen + "," + newBlue + ")";

                                $('#premium-color-transition-' + _this.settings.id).css({
                                    backgroundColor: newColor
                                });

                            } else if (scrollPos > animationEndPos) {
                                $('#premium-color-transition-' + _this.settings.id).css({
                                    backgroundColor: endingColor
                                });
                            }
                        });

                    };

                    this.hexToRgb = function (hex) {

                        if (-1 !== hex.indexOf("rgb")) {
                            var rgb = (hex.substring(hex.indexOf("(") + 1)).split(",");
                            return {
                                r: parseInt(rgb[0]),
                                g: parseInt(rgb[1]),
                                b: parseInt(rgb[2])
                            };

                        } else {
                            var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
                            return result ? {
                                r: parseInt(result[1], 16),
                                g: parseInt(result[2], 16),
                                b: parseInt(result[3], 16)
                            } : null;
                        }
                    };

                    $('#premium-color-transition-' + this.settings.id).css({
                        backgroundColor: downColors[0]
                    });

                    var parent_node = $("#premium-color-transition-" + this.settings.id).closest(".elementor");

                    if (0 === parent_node.length)
                        parent_node = $(".elementor").first();

                    var i = 0,
                        arry_len = downColors.length,
                        isLooped = null;

                    $(".elementor > .elementor-element, .elementor-section-wrap > .elementor-element").each(function () {
                        if (arry_len <= i)
                            i = 0;

                        var firstColor = i,
                            secondColor = i + 1;

                        if (downColors[firstColor] !== '' && downColors[firstColor] != undefined) {
                            firstColor = downColors[firstColor];
                        }
                        if (downColors[secondColor] !== '' && downColors[secondColor] != undefined) {
                            isLooped = false;
                            secondColor = downColors[secondColor];
                        } else {
                            i = 0;
                            isLooped = true;
                            secondColor = i;
                            secondColor = downColors[secondColor];
                        }

                        _this.rowTransitionalColor($(this), firstColor, secondColor);
                        if (!isLooped)
                            i++;
                    });

                } else {

                    //Refresh all Waypoints instances.
                    Waypoint.refreshAll();

                    var currentActiveIndex = null;
                    elements.forEach(function (element, index) {

                        $('<div class="premium-color-transition-layer elementor-repeater-item-' + itemsIDs[index] + '" data-direction="down"></div>').prependTo($('#premium-color-transition-' + _this.settings.id));

                        $('<div class="premium-color-transition-layer elementor-repeater-item-' + itemsIDs[index] + '" data-direction="up"></div>').prependTo($('#premium-color-transition-' + _this.settings.id));

                        if (-1 !== downColors[index].indexOf('//')) {

                            $('.elementor-repeater-item-' + itemsIDs[index] + '[data-direction="down"]').css('background-image', 'url(' + downColors[index] + ')');

                        }

                        if (-1 !== _this.settings.upColors[index].indexOf('//')) {

                            $('.elementor-repeater-item-' + itemsIDs[index] + '[data-direction="up"]').css('background-image', 'url(' + _this.settings.upColors[index] + ')');

                        }

                        if (_this.visible($('#' + element), true)) {
                            $('.elementor-repeater-item-' + itemsIDs[index] + '[data-direction="down"]').addClass('layer-active');
                            currentActiveIndex = index;
                        }

                        elementorFrontend.waypoint(
                            $('#' + element),
                            function (direction) {

                                if ('down' === direction) {

                                    var downBackground = _this.settings.downColors[index];

                                    if (_this.checkDifferentBackgrounds(downBackground, currentActiveIndex)) {
                                        $('.premium-color-transition-layer').removeClass('layer-active');
                                        $('.elementor-repeater-item-' + itemsIDs[index] + '[data-direction="down"]').addClass('layer-active');
                                        currentActiveIndex = index;
                                    }

                                }
                            }, {
                            offset: downOffsets[index],
                            triggerOnce: false
                        }
                        );

                        elementorFrontend.waypoint(
                            $('#' + element),
                            function (direction) {
                                if ('up' === direction) {

                                    var upBackground = _this.settings.upColors[index];

                                    if (_this.checkDifferentBackgrounds(upBackground, currentActiveIndex)) {

                                        $('.premium-color-transition-layer').removeClass('layer-active');
                                        $('.elementor-repeater-item-' + itemsIDs[index] + '[data-direction="up"]').addClass('layer-active');

                                        currentActiveIndex = index;
                                    }

                                }
                            }, {
                            offset: upOffsets[index],
                            triggerOnce: false
                        }
                        );

                    });

                }

            },

            // Compare between the color to be changed and the current color of active layer.
            // If equal, then no need to change the color.
            checkDifferentBackgrounds: function (background, active) {

                var currentActiveDir = $('#premium-color-transition-' + this.settings.id + ' .layer-active').data('direction'),
                    currentActiveBackground;

                if ('down' === currentActiveDir) {
                    currentActiveBackground = this.settings.downColors[active];
                } else {
                    currentActiveBackground = this.settings.upColors[active];
                }

                //If current active is null, then none of the sections are in the viewport. We must change background.
                return null != active ? -1 == currentActiveBackground.indexOf(background) : true;

            },

            getRepeaterSettings: function () {
                var repeater = this.getElementSettings('id_repeater'),
                    elements = [],
                    downColors = [],
                    itemsIDs = [],
                    upColors = [],
                    downOffsets = [],
                    upOffsets = [],
                    globalOffset = this.getElementSettings('offset') || 30;

                repeater.forEach(function (element, index) {

                    elements.push(element.section_id);
                    itemsIDs.push(element._id);

                    element.down_background = element.down_color;

                    if ('image' === element.scroll_down_type && '' !== element.down_image.url) {
                        element.down_background = element.down_image.url;
                    }

                    element.up_background = element.up_color;

                    if ('image' === element.scroll_up_type && '' !== element.up_image.url) {
                        element.up_background = element.up_image.url;
                    }

                    if ('' === element.up_background) {
                        element.up_background = element.down_background;
                    }

                    downColors.push(element.down_background);
                    upColors.push(element.up_background);

                    switch (element.scroll_down_offset) {
                        case '':
                            downOffsets.push(0 === index ? 'bottom-in-view' : globalOffset);
                            break;
                        case 'top-in-view':
                            downOffsets.push('0');
                            break;
                        case 'bottom-in-view':
                            downOffsets.push(element.scroll_down_offset);
                            break;
                        default:
                            downOffsets.push(element.scroll_down_custom_offset.size + element.scroll_down_custom_offset.unit);
                            break;
                    }

                    switch (element.scroll_up_offset) {
                        case '':
                            upOffsets.push("-" + globalOffset);
                            break;
                        case 'top-in-view':
                            upOffsets.push('0');
                            break;
                        case 'bottom-in-view':
                            upOffsets.push(element.scroll_up_offset);
                            break;
                        default:
                            upOffsets.push("-" + element.scroll_up_custom_offset.size + element.scroll_up_custom_offset.unit);
                            break;
                    }
                });

                return {
                    elements: elements,
                    downColors: downColors,
                    upColors: upColors,
                    itemsIDs: itemsIDs,
                    downOffsets: downOffsets,
                    upOffsets: upOffsets,
                };

            },

            checkVisible: function () {
                var settings = this.settings,
                    $window = $(window);

                if (undefined === settings.firstElemOffset || undefined === settings.lastElemOffset)
                    return;

                if ($window.scrollTop() >= settings.lastElemeBot - settings.lastElemeHeight / 4) {
                    var index = $('#premium-color-transition-' + settings.id).index();
                    if (0 !== index)
                        $('#premium-color-transition-' + settings.id).addClass('premium-bg-transition-hidden');

                }
                if (($window.scrollTop() >= settings.firstElemOffset) && ($window.scrollTop() < settings.lastElemOffset)) {
                    $('#premium-color-transition-' + settings.id).removeClass('premium-bg-transition-hidden');
                }
            },

            visible: function (selector, partial, hidden) {
                var s = selector.get(0),
                    $window = $(window),
                    vpHeight = $window.outerHeight(),
                    clientSize =
                        hidden === true ? s.offsetWidth * s.offsetHeight : true;

                if (typeof s.getBoundingClientRect === "function") {
                    var rec = s.getBoundingClientRect();
                    var tViz = rec.top >= 0 && rec.top < vpHeight,
                        bViz = rec.bottom > 0 && rec.bottom <= vpHeight,
                        vVisible = partial ? tViz || bViz : tViz && bViz,
                        vVisible =
                            rec.top < 0 && rec.bottom > vpHeight ? true : vVisible;
                    return clientSize && vVisible;
                } else {
                    var viewTop = 0,
                        viewBottom = viewTop + vpHeight,
                        position = $window.position(),
                        _top = position.top,
                        _bottom = _top + $window.height(),
                        compareTop = partial === true ? _bottom : _top,
                        compareBottom = partial === true ? _top : _bottom;
                    return (
                        !!clientSize &&
                        (compareBottom <= viewBottom && compareTop >= viewTop)
                    );
                }
            }

        });

        var PremiumIconBoxHandler = function ($scope, $) {

            $scope.find(".elementor-invisible").removeClass("elementor-invisible");

            var devices = ['widescreen', 'desktop', 'laptop', 'tablet', 'tablet_extra', 'mobile', 'mobile_extra'].filter(function (ele) { return ele != elementorFrontend.getCurrentDeviceMode(); });

            devices.map(function (device) {
                device = ('desktop' !== device) ? device + '-' : '';
                $scope.removeClass(function (index, selector) {
                    return (selector.match(new RegExp("(^|\\s)premium-" + device + "icon-box\\S+", 'g')) || []).join(' ');
                });
            });

            if ($scope.data("box-tilt")) {
                var reverse = $scope.data("box-tilt-reverse");

                UniversalTilt.init({
                    elements: $scope,
                    settings: {
                        reverse: reverse
                    },
                    callbacks: {
                        onMouseLeave: function (el) {
                            el.style.boxShadow = "0 45px 100px rgba(255, 255, 255, 0)";
                        },
                        onDeviceMove: function (el) {
                            el.style.boxShadow = "0 45px 100px rgba(255, 255, 255, 0.3)";
                        }
                    }
                });
            }

        };

        window.premiumImageLayersEffects = function (element, settings) {

            var self = this,
                $el = $(element),
                scrolls = $el.data("scrolls"),
                elementSettings = settings,
                elType = elementSettings.elType,
                elOffset = $el.offset();

            //Check if Horizontal Scroll Widget
            var isHScrollWidget = $el.closest(".premium-hscroll-temp").length;

            self.elementRules = {};

            self.init = function () {

                if (scrolls || 'SECTION' === elType) {

                    if (!elementSettings.effects.length > 0) {
                        return;
                    }
                    self.setDefaults();
                    self.initScroll('load');
                    elementorFrontend.elements.$window.on('scroll', self.initScroll);
                } else {

                    elementorFrontend.elements.$window.off('scroll', self.initScroll);
                    return;
                }

            };

            self.setDefaults = function () {

                elementSettings.defaults = {};
                elementSettings.defaults.axis = 'y';

            };

            self.transform = function (action, percents, data) {

                if ("down" === data.direction) {
                    percents = 100 - percents;
                }

                if (data.range) {
                    if (data.range.start > percents && !isHScrollWidget) {
                        percents = data.range.start;
                    }

                    if (data.range.end < percents && !isHScrollWidget) {
                        percents = data.range.end;
                    }
                }

                if ("rotate" === action) {
                    elementSettings.defaults.unit = "deg";
                } else {
                    elementSettings.defaults.unit = "px";
                }

                self.updateElement(
                    "transform",
                    action,
                    self.getStep(percents, data) + elementSettings.defaults.unit
                );

            };

            self.getPercents = function () {
                var dimensions = self.getDimensions();

                var startOffset = innerHeight;

                if (isHScrollWidget) startOffset = 0;

                (elementTopWindowPoint = dimensions.elementTop - pageYOffset),
                    (elementEntrancePoint = elementTopWindowPoint - startOffset);

                passedRangePercents =
                    (100 / dimensions.range) * (elementEntrancePoint * -1);

                return passedRangePercents;
            };

            self.initScroll = function (event) {

                if ("load" === event) {
                    $el.css("transition", "all 1s ease");
                } else {
                    $el.css("transition", "none");
                }

                if (elementSettings.effects.includes('translateY')) {

                    self.initVScroll();

                }

                if (elementSettings.effects.includes('translateX')) {

                    self.initHScroll();

                }

                if (elementSettings.effects.includes('opacity')) {

                    self.initOScroll();

                }

                if (elementSettings.effects.includes('blur')) {

                    self.initBScroll();

                }

                if (elementSettings.effects.includes('gray')) {

                    self.initGScroll();

                }

                if (elementSettings.effects.includes('rotate')) {

                    self.initRScroll();

                }

                if (elementSettings.effects.includes('scale')) {

                    self.initScaleScroll();

                }

            };

            self.initVScroll = function () {
                var percents = self.getPercents();

                self.transform("translateY", percents, elementSettings.vscroll);
            };

            self.initHScroll = function () {
                var percents = self.getPercents();

                self.transform("translateX", percents, elementSettings.hscroll);
            };

            self.getDimensions = function () {
                var elementOffset = elOffset;

                var dimensions = {
                    elementHeight: $el.outerHeight(),
                    elementWidth: $el.outerWidth(),
                    elementTop: elementOffset.top,
                    elementLeft: elementOffset.left
                };

                dimensions.range = dimensions.elementHeight + innerHeight;

                return dimensions;
            };

            self.getStep = function (percents, options) {
                return -(percents - 50) * options.speed;
            };

            self.initOScroll = function () {
                var percents = self.getPercents(),
                    data = elementSettings.oscroll,
                    movePoint = self.getEffectMovePoint(
                        percents,
                        data.fade,
                        data.range
                    ),
                    level = data.level / 10,
                    opacity =
                        1 -
                        level +
                        self.getEffectValueFromMovePoint(level, movePoint);

                $el.css("opacity", opacity);
            };

            self.initBScroll = function () {

                var percents = self.getPercents(),
                    data = elementSettings.bscroll,
                    movePoint = self.getEffectMovePoint(percents, data.blur, data.range),
                    blur = data.level - self.getEffectValueFromMovePoint(data.level, movePoint);

                self.updateElement('filter', 'blur', blur + 'px');

            };

            self.initGScroll = function () {

                var percents = self.getPercents(),
                    data = elementSettings.gscale,
                    movePoint = self.getEffectMovePoint(percents, data.gray, data.range),
                    grayScale = 10 * (data.level - self.getEffectValueFromMovePoint(data.level, movePoint));

                self.updateElement('filter', 'grayscale', grayScale + '%');

            };

            self.initRScroll = function () {
                var percents = self.getPercents();

                self.transform("rotate", percents, elementSettings.rscroll);
            };

            self.getEffectMovePoint = function (percents, effect, range) {
                var point = 0;

                if (percents < range.start) {
                    if ("down" === effect) {
                        point = 0;
                    } else {
                        point = 100;
                    }
                } else if (percents < range.end) {
                    point = self.getPointFromPercents(
                        range.end - range.start,
                        percents - range.start
                    );

                    if ("up" === effect) {
                        point = 100 - point;
                    }
                } else if ("up" === effect) {
                    point = 0;
                } else if ("down" === effect) {
                    point = 100;
                }

                return point;
            };

            self.initScaleScroll = function () {
                var percents = self.getPercents(),
                    data = elementSettings.scale,
                    movePoint = self.getEffectMovePoint(
                        percents,
                        data.direction,
                        data.range
                    );

                this.updateElement(
                    "transform",
                    "scale",
                    1 + (data.speed * movePoint) / 1000
                );
            };

            self.getEffectValueFromMovePoint = function (level, movePoint) {
                return (level * movePoint) / 100;
            };

            self.getPointFromPercents = function (movableRange, percents) {
                var movePoint = (percents / movableRange) * 100;

                return +movePoint.toFixed(2);
            };

            self.updateElement = function (propName, key, value) {
                if (!self.elementRules[propName]) {
                    self.elementRules[propName] = {};
                }

                if (!self.elementRules[propName][key]) {
                    self.elementRules[propName][key] = true;

                    self.updateElementRule(propName);
                }

                var cssVarKey = "--" + key;

                element.style.setProperty(cssVarKey, value);
            };

            self.updateElementRule = function (rule) {
                var cssValue = "";

                $.each(self.elementRules[rule], function (variableKey) {
                    cssValue += variableKey + "(var(--" + variableKey + "))";
                });

                $el.css(rule, cssValue);
            };

        };

        window.premiumEditorBehavior = function ($element, settings) {

            var self = this,
                $el = $element,
                elementSettings = settings,
                editModel = null,
                repeater = null;


            var items = $el.find(elementSettings.item),
                tag = $el.prop('tagName');

            if ($el.hasClass('e-con'))
                tag = 'SECTION';

            self.init = function () {

                editModel = self.getEditModelBycId();

                if (!items.length || undefined === editModel) {
                    return;
                }

                repeater = editModel.get(elementSettings.repeater).models;

                if (elementSettings.widgets.includes("resize")) {

                    var resizableOptions = self.getResizableOptions();

                }

                var draggableOptions = self.getDraggableOptions();

                if ('SECTION' !== tag) {
                    var $widget = elementor.previewView.$childViewContainer.find('.elementor-widget-wrap');
                    $widget.find(elementSettings.item).closest('.elementor-widget-wrap').sortable('disable');
                }


                items.filter(function () {

                    if ('absolute' === $(this).css('position')) {

                        $(this).draggable(draggableOptions);

                        if (elementSettings.widgets.includes("resize")) {

                            if (!$(this).hasClass("parallax-svg"))
                                $(this).resizable(resizableOptions);

                        }

                    }

                });

            };

            self.getDraggableOptions = function () {

                if ('premium_img_layers_images_repeater' === elementSettings.repeater) {
                    elementor.listenTo(elementor.channels.deviceMode, 'change', function () {
                        $el.find(elementSettings.item).each(function (index, item) {
                            $(item).removeAttr("style");

                            window.PremiumWidgetsEditor.reRender(elementorFrontend.getCurrentDeviceMode());
                        });
                    });

                }

                var draggableOptions = {};

                draggableOptions.stop = function (e, ui) {

                    var index = self.layerToEdit(ui.helper),
                        deviceSuffix = self.getCurrentDeviceSuffix(),
                        hUnit = 'SECTION' === tag ? '%' : repeater[index].get(elementSettings.hor + deviceSuffix).unit,
                        hWidth = window.elementor.helpers.elementSizeToUnit(ui.helper, ui.position.left, hUnit),
                        vUnit = repeater[index].get(elementSettings.ver + deviceSuffix).unit,
                        vWidth = ('%' === vUnit || 'SECTION' === tag) ? self.verticalOffsetToPercent(ui.helper, ui.position.top) : window.elementor.helpers.elementSizeToUnit(ui.helper, ui.position.top, vUnit),
                        settingToChange = {};


                    if (-1 !== elementSettings.repeater.indexOf('parallax')) {

                        ui.helper.removeClass("premium-parallax-center");
                        settingToChange['premium_parallax_layer_hor'] = 'custom';
                        settingToChange['premium_parallax_layer_ver'] = 'custom';

                    }

                    settingToChange[elementSettings.hor + deviceSuffix] = {
                        unit: hUnit,
                        size: hWidth
                    };

                    settingToChange[elementSettings.ver + deviceSuffix] = {
                        unit: vUnit,
                        size: vWidth
                    };

                    if ('SECTION' !== tag) {
                        $el.trigger('click');
                    } else {
                        $el.find('i.eicon-handle').eq(0).trigger('click');
                    }

                    window.PremiumWidgetsEditor.activateEditorPanelTab(elementSettings.tab);

                    repeater[index].setExternalChange(settingToChange);

                };

                return draggableOptions;

            };

            self.getResizableOptions = function () {

                var resizableOptions = {};

                resizableOptions.handles = self.setHandle();
                resizableOptions.stop = function (e, ui) {

                    var index = self.layerToEdit(ui.element),
                        deviceSuffix = self.getCurrentDeviceSuffix(),
                        unit = 'SECTION' === tag ? '%' : repeater[index].get(elementSettings.width + deviceSuffix).unit,
                        width = window.elementor.helpers.elementSizeToUnit(ui.element, ui.size.width, unit),
                        settingToChange = {};

                    settingToChange[elementSettings.width + deviceSuffix] = {
                        unit: unit,
                        size: width
                    };

                    if ('SECTION' !== tag) {
                        $el.trigger('click');
                    } else {
                        $el.find('i.eicon-handle').eq(0).trigger('click');
                    }

                    window.PremiumWidgetsEditor.activateEditorPanelTab(elementSettings.tab);

                    repeater[index].setExternalChange(settingToChange);

                };

                return resizableOptions;

            };

            self.getModelcId = function () {

                return $el.closest('.elementor-element').data('model-cid');

            };

            self.getEditModelBycId = function () {

                var cID = self.getModelcId();

                return elementorFrontend.config.elements.data[cID];

            };

            self.getCurrentDeviceSuffix = function () {

                var currentDeviceMode = elementorFrontend.getCurrentDeviceMode();

                return ('desktop' === currentDeviceMode) ? '' : '_' + currentDeviceMode;

            };

            self.layerToEdit = function ($layer) {

                var offset = elementSettings.offset;

                if ('SECTION' === tag && !$el.hasClass("premium-lottie-yes")) {
                    var length = $el.find(elementSettings.item).length;

                    if (length > 1) {
                        return (length - 1) - $el.find($layer).index();
                    }
                }

                return ($el.find($layer).index()) - offset;

            };

            self.verticalOffsetToPercent = function ($el, size) {

                size = size / ($el.offsetParent().height() / 100);

                return Math.round(size * 1000) / 1000;

            };

            self.setHandle = function () {

                return window.elementor.config.is_rtl ? 'w' : 'e';

            };

        };

        var functionalHandlers = {
            'premium-addon-flip-box.default': PremiumFlipboxHandler,
            'premium-addon-facebook-chat.default': PremiumFbChatHandler,
            'premium-twitter-feed.default': PremiumTwitterFeedHandler,
            'premium-notbar.default': PremiumAlertBoxHandler,
            'premium-addon-instagram-feed.default': PremiumInstaFeedHandler,
            'premium-whatsapp-chat.default': $whatsappElemHandler,
            'premium-addon-magic-section.default': PremiumMagicSectionHandler,
            'premium-addon-preview-image.default': PremiumPreviewWindowHandler,
            'premium-behance-feed.default': PremiumBehanceFeedHandler,
            'premium-img-layers-addon.default': PremiumImageLayersHandler,
            'premium-addon-image-comparison.default': PremiumImageCompareHandler,
            'premium-addon-content-toggle.default': PremiumContentToggleHandler,
            'premium-addon-image-hotspots.default': PremiumImageHotspotHandler,
            'premium-tables-addon.default': PremiumTableHandler,
            'premium-divider.default': PremiumDividerHandler,
            'premium-multi-scroll.default': PremiumScrollHandler,
            'premium-addon-icon-box.default': PremiumIconBoxHandler,
        };

        var classHandlers = {
            'premium-unfold-addon': PremiumUnfoldHandler,
            'premium-image-accordion': PremiumImageAccordionHandler,
            'premium-addon-tabs': PremiumTabsHandler,
            'premium-chart': PremiumChartHandler,
            'premium-facebook-reviews': PremiumReviewHandler,
            'premium-google-reviews': PremiumReviewHandler,
            'premium-yelp-reviews': PremiumReviewHandler,
            'premium-color-transition': PremiumColorTransitionHandler,
            'premium-facebook-feed': PremiumFacebookHandler,
        };

        $.each(functionalHandlers, function (elemName, func) {
            if ('object' === typeof func) {
                $.each(func, function (index, handler) {
                    elementorFrontend.hooks.addAction('frontend/element_ready/' + elemName, handler);
                })
            } else {
                elementorFrontend.hooks.addAction('frontend/element_ready/' + elemName, func);
            }

        });

        $.each(classHandlers, function (elemName, clas) {
            elementorFrontend.elementsHandler.attachHandler(elemName, clas);
        });

        if (elementorFrontend.isEditMode()) {

            elementorFrontend.hooks.addAction(
                "frontend/element_ready/premium-img-layers-addon.default",
                PremiumImageLayersEditorHandler
            );

            elementorFrontend.hooks.addAction(
                "frontend/element_ready/premium-addon-image-hotspots.default",
                PremiumImageHotspotEditorHandler
            );

        }

    });
})(jQuery);