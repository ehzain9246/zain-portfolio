(function ($) {
    $(window).on('elementor/frontend/init', function () {

        var premiumWrapperLinkHandler = function ($scope) {

            function isURL(str) {
                // Regular expression for URL validation, supporting Unicode characters
                var urlPattern = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.-]{2,})([\/\w \u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF%.-]*)*\/?$/i;

                // Test the string against the regular expression
                return urlPattern.test(str);
            }

            $scope.on('click.onWrapperLink', function () {

                var settings = $scope.data('premium-element-link');

                if (!settings)
                    return;

                var id = $scope.data('id'),
                    anchor = document.createElement('a'),
                    anchorReal,
                    timeout;

                anchor.id = 'premium-wrapper-link-' + id;

                if (!isURL(settings.href))
                    return;

                anchor.href = settings.href;
                anchor.target = settings.type === 'url' ? settings.link.is_external ? '_blank' : '_self' : '';
                anchor.rel = settings.type === 'url' ? settings.link.nofollow ? 'nofollow noreferer' : '' : '';
                anchor.style.display = 'none';

                document.body.appendChild(anchor);

                anchorReal = document.getElementById(anchor.id);
                anchorReal.click();

                timeout = setTimeout(function () {
                    anchorReal.remove();
                    clearTimeout(timeout);
                });
            });

        };

        elementorFrontend.hooks.addAction("frontend/element_ready/global", premiumWrapperLinkHandler);


    });
})(jQuery);