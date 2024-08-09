(function ($) {

    $(window).on('elementor/frontend/init', function () {

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

        elementorFrontend.hooks.addAction('frontend/element_ready/premium-multi-scroll.default', PremiumScrollHandler);
    });
})(jQuery);