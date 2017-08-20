// jQuery ajax wrapper
function Ajax(url, method, data) {
    return $.ajax({
        url,
        method,
        dataType: 'json',
        data
    });
}

// Ajax get method
weMail.ajax.get = (action, options) => {
    return Ajax(weMail.ajaxurl, 'get', $.extend(true, {
        action: `wemail_${action}`,
        _wpnonce: weMail.nonce
    }, options));
};

// Ajax post method
weMail.ajax.post = (action, options) => {
    return Ajax(weMail.ajaxurl, 'post', $.extend(true, {
        action: `wemail_${action}`,
        _wpnonce: weMail.nonce
    }, options));
};

// API get method
weMail.api.get = (route, query, rootEndPoint) => {
    const url = !rootEndPoint ? (weMail.api.rootEndPoint + route) : (rootEndPoint + route);

    return Ajax(url, 'get', $.extend(true, {
        apiKey: weMail.api.key
    }, query));
};

// API post method
weMail.api.post = (route, query, rootEndPoint) => {
    const url = !rootEndPoint ? (weMail.api.rootEndPoint + route) : (rootEndPoint + route);

    return Ajax(url, 'post', $.extend(true, {
        apiKey: weMail.apiKey
    }, query));
};

// Register vue component to prevent duplicate registration
weMail.component = function (tagName, options, test) {
    if (this.registeredComponents.indexOf(tagName) >= 0) {
        return;
    }

    this.registeredComponents.push(tagName);

    weMail.Vue.component(tagName, options);
};

// Register vuex stores
weMail.registerStore = function (routeName, store) {
    if (!this.stores[routeName]) {
        this.stores[routeName] = {};
    }

    this.stores[routeName] = $.extend(true, this.stores[routeName], store);
};

// Register vue mixins
weMail.registerMixins = function (mixins) {
    weMail._.forEach(mixins, (mixin, name) => {
        if (!weMail.mixins[name]) {
            weMail.mixins[name] = mixin;
        }
    });
};

// Vue Router instance
weMail.router = new weMail.VueRouter({
    routes: weMail.routes.map((route) => {
        return {
            path: route.path,
            name: route.name,
            component: {
                render: (render) => render(weMail.routeComponents[route.component])
            }
        };
    })
});

// Lazy load router components
weMail.router.beforeEach((to, from, next) => {
    const route = weMail._.filter(weMail.routes, {
        name: to.name
    })[0];

    function getComponentScript() {
        const TIMEOUT = 400;

        $.getScript(route.requires).done(() => {
            setTimeout(() => {
                weMail.router.app.showLoadingAnime = false;
                next();
            }, TIMEOUT);
        }).fail((jqxhr, settings, exception) => {
            console.error(exception);
        });
    }

    if (route && route.scrollTo) {
        const scrollTo = (route.scrollTo === 'top') ? 0 : route.scrollTo;
        const DURATION = 200;

        $('body').animate({
            scrollTop: scrollTo
        }, DURATION);
    }

    if (route) {
        weMail.router.app.showLoadingAnime = true;

        weMail
            .ajax
            .get(`get_${route.name}_initial_data`, route.initialData || {})
            .done((response) => {
                weMail.registerStore(route.name, {
                    state: response.data
                });

                if (route.requires && !weMail.routeComponents[route.component]) {
                    if (route.dependencies && route.dependencies.length) {
                        let depLoaded = 0;

                        route.dependencies.forEach((dep) => {
                            $.getScript(dep).done(() => {
                                ++depLoaded;

                                if (depLoaded === route.dependencies.length) {
                                    getComponentScript();
                                }
                            }).fail((jqxhr, settings, exception) => {
                                console.error(exception);
                            });
                        });
                    } else {
                        getComponentScript();
                    }
                } else {
                    weMail.router.app.showLoadingAnime = false;
                    next();
                }
            });
    }
});
