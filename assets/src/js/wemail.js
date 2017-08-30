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
weMail.api.get = (endpoint, query, root) => {
    const url = !root ? (weMail.api.root + endpoint) : (root + endpoint);

    return Ajax(url, 'get', $.extend(true, {
        apiKey: weMail.api.key
    }, query));
};

// API post method
weMail.api.post = (endpoint, query, root) => {
    const url = !root ? (weMail.api.root + endpoint) : (root + endpoint);

    return Ajax(url, 'post', $.extend(true, {
        apiKey: weMail.apiKey
    }, query));
};

// Register vue component to prevent duplicate registration
weMail.component = function (name, component) {
    if (this.registeredComponents.hasOwnProperty(name)) {
        return;
    }

    this.registeredComponents[name] = component;
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

weMail.getMixins = function (...mixins) {
    return mixins.map((mixin) => weMail.mixins[mixin]);
};

// Vuex Store instance
weMail.store = new weMail.Vuex.Store({});

// Vue Router instance
function mapRoute(route, routeObj) {
    if (!routeObj) {
        routeObj = {
            path: route.path,
            name: route.name,
            component: {
                render(render) {
                    return render(weMail.registeredComponents[route.component]);
                }
            },
            children: []
        };

        if (route.redirect) {
            routeObj.redirect = {
                name: route.redirect
            };
        }
    }

    if (route.children && route.children.length) {
        route.children.forEach((childRoute) => {
            const child = {
                path: childRoute.path,
                name: childRoute.name,
                component: {
                    render: (render) => {
                        return render(weMail.registeredComponents[childRoute.component]);
                    }
                },
                children: []
            };

            routeObj.children.push(child);

            mapRoute(childRoute, child);
        });
    }

    return routeObj;
}

weMail.router = new weMail.VueRouter({
    routes: weMail.routes.map((route) => {
        return mapRoute(route);
    })
});

// Lazy load router components
weMail.router.beforeEach((to, from, next) => {
    const rootRoute = weMail._.filter(weMail.routes, {
        name: to.matched[0].name
    })[0];

    function getComponentScript() {
        const TIMEOUT = 400;

        $.getScript(rootRoute.requires).done(() => {
            setTimeout(() => {
                weMail.router.app.showLoadingAnime = false;
                next();
            }, TIMEOUT);
        }).fail((jqxhr, settings, exception) => {
            console.error(exception);
        });
    }

    if (rootRoute && rootRoute.scrollTo) {
        const scrollTo = (rootRoute.scrollTo === 'top') ? 0 : rootRoute.scrollTo;
        const DURATION = 200;

        $('body').animate({
            scrollTop: scrollTo
        }, DURATION);
    }

    if (rootRoute) {
        weMail.router.app.showLoadingAnime = true;

        weMail
            .ajax
            .get(`get_route_data_${to.name}`)
            .done((response) => {
                if (response.data) {
                    weMail._.forEach(response.data, (routeData, routeName) => {
                        weMail.registerStore(routeName, {
                            state: routeData
                        });
                    });
                }

                if (rootRoute.requires && !weMail.registeredComponents[rootRoute.component]) {
                    if (rootRoute.dependencies && rootRoute.dependencies.length) {
                        let depLoaded = 0;

                        rootRoute.dependencies.forEach((dep) => {
                            $.getScript(dep).done(() => {
                                ++depLoaded;

                                if (depLoaded === rootRoute.dependencies.length) {
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
    } else {
        next();
    }
});
