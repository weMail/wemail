;(function($) {
    'use strict';

    var router = new VueRouter({
        routes: weMail.routes.map(function (route) {
            return {
                path: route.path,
                name: route.name,
                component: {
                    render: function (render) {
                        return render(weMail.routeComponents[route.component])
                    }
                }
            };
        })
    });

    // Lazy load router components
    router.beforeEach(function (to, from, next) {
        var route = _lodash.filter(weMail.routes, {name: to.name})[0];

        if (route && route.scrollTo) {
            var scrollTo = ('top' === route.scrollTo) ? 0 : route.scrollTo;

            $('body').animate({scrollTop: scrollTo}, 200);
        }

        if (route && route.requires && !weMail.routeComponents[route.component]) {
            router.app.loadingRouteRequires = true;

            $.getScript(route.requires).done(function() {
                setTimeout(function () {
                    router.app.loadingRouteRequires = false;
                    next();
                }, 800);
            });

        } else {
            next();
        }
    });

    // The main Vue instance
    weMail.app = new Vue({
        el: '#wemail-app',
        router: router,
        data: {
            loadingRouteRequires: true
        },
    });

    // Admin menu hack
    var menuRoot = $('#toplevel_page_wemail');

    menuRoot.on('click', 'a', function() {
        var self = $(this);

        $('ul.wp-submenu li', menuRoot).removeClass('current');

        if ( self.hasClass('wp-has-submenu') ) {
            $('li.wp-first-item', menuRoot).addClass('current');
        } else {
            self.parents('li').addClass('current');
        }
    });

    // select the current sub menu on page load
    var currentURL = window.location.href;
    var currentPath = currentURL.substr( currentURL.indexOf('admin.php') );

    $('ul.wp-submenu a', menuRoot).each(function(index, el) {
        if ( $(el).attr( 'href' ) === currentPath ) {
            $(el).parent().addClass('current');
            return;
        }
    });
})(jQuery);
