;(function($) {
    'use strict';

    function ajaxRequest(url, method, data) {
        return $.ajax({
            url: url,
            method: method,
            dataType: 'json',
            data: data
        });
    }

    weMail.ajax.get = function (action, options) {
        return ajaxRequest(weMail.ajaxurl, 'get', $.extend(true, {
            action: 'wemail_' + action,
            _wpnonce: weMail.nonce,
        }, options));
    }

    weMail.ajax.post = function (action, options) {
        return ajaxRequest(weMail.ajaxurl, 'post', $.extend(true, {
            action: 'wemail_' + action,
            _wpnonce: weMail.nonce,
        }, options));
    }

    weMail.api.get = function (route, query) {
        var url = route ? weMail.apiRootEndPoint + route : weMail.apiRootEndPoint;

        return ajaxRequest(url, 'get', $.extend(true, {
            apiKey: weMail.apiKey
        }, query));
    }

    weMail.api.post = function (route, query) {
        var url = route ? weMail.apiRootEndPoint + route : weMail.apiRootEndPoint;

        return ajaxRequest(url, 'post', $.extend(true, {
            apiKey: weMail.apiKey
        }, query));
    }

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

    // Register vue component to prevent duplicate registration
    weMail.component = function (tagName, options) {
        if (this.registeredComponents.indexOf(tagName) >= 0) {
            return;
        }

        this.registeredComponents.push(tagName);

        Vue.component(tagName, options);
    }

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
