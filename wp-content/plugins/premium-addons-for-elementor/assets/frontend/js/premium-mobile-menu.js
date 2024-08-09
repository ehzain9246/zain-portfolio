(function ($) {
    $(window).on('elementor/frontend/init', function () {

        var PremiumMobileMenuHandler = elementorModules.frontend.handlers.Base.extend({

            getDefaultSettings: function () {

                return {
                    slick: {
                        infinite: false,
                        rows: 0,
                        draggable: true,
                        pauseOnHover: true,
                        slidesToScroll: 1,
                        autoplay: false,
                    },
                    selectors: {
                        wrap: '.premium-mobile-menu__wrap',
                        list: '.premium-mobile-menu__list'

                    }
                }
            },

            getDefaultElements: function () {

                var selectors = this.getSettings('selectors');

                return {
                    $wrap: this.$element.find(selectors.wrap),
                    $list: this.$element.find(selectors.list),
                }

            },
            bindEvents: function () {
                this.run();
            },

            getSlickSettings: function () {

                var settings = this.getElementSettings(),
                    rtl = this.elements.$wrap.data("rtl"),
                    colsNumber = settings.items_to_show,
                    prevArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-prev" aria-label="Previous" role="button" style=""><i class="fas fa-angle-left" aria-hidden="true"></i></a>',
                    nextArrow = '<a type="button" data-role="none" class="carousel-arrow carousel-next" aria-label="Next" role="button" style=""><i class="fas fa-angle-right" aria-hidden="true"></i></a>',
                    slides_tab = settings.items_to_show_tablet,
                    slides_mob = settings.items_to_show_mobile,
                    spacing_tab = settings.carousel_spacing_tablet,
                    spacing_mob = settings.carousel_spacing_mobile,
                    currentDeviceMode = elementorFrontend.getCurrentDeviceMode();

                if (-1 !== currentDeviceMode.indexOf('mobile') && 'yes' !== settings.carousel_arrows_mobile) {
                    prevArrow = '';
                    nextArrow = '';

                } else if (-1 !== currentDeviceMode.indexOf('tablet') && 'yes' !== settings.carousel_arrows_tablet) {
                    prevArrow = '';
                    nextArrow = '';
                }

                return Object.assign(this.getSettings('slick'), {

                    slidesToShow: colsNumber,
                    responsive: [{
                        breakpoint: 1025,
                        settings: {
                            slidesToShow: slides_tab,
                            centerPadding: spacing_tab + "px",
                            nextArrow: settings.carousel_arrows_tablet ? nextArrow : '',
                            prevArrow: settings.carousel_arrows_tablet ? prevArrow : '',
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: slides_mob,
                            centerPadding: spacing_mob + "px",
                            nextArrow: settings.carousel_arrows_mobile ? nextArrow : '',
                            prevArrow: settings.carousel_arrows_mobile ? prevArrow : '',
                        }
                    }
                    ],
                    rtl: rtl ? true : false,
                    autoplaySpeed: settings.speed || 5000,
                    prevArrow: settings.carousel_arrows ? prevArrow : '',
                    nextArrow: settings.carousel_arrows ? nextArrow : '',
                    centerMode: settings.carousel_center,
                    centerPadding: settings.carousel_spacing + "px",

                });


            },

            run: function () {

                var $list = this.elements.$list;

                var carousel = this.getElementSettings('carousel');

                if (carousel)
                    $list.slick(this.getSlickSettings());

            }

        });

        elementorFrontend.elementsHandler.attachHandler('premium-mobile-menu', PremiumMobileMenuHandler);
    });
})(jQuery);