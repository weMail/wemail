// Vue Router instance
function mapRoute(route, routeObj) {
    weMail.namedRoutes[route.name] = route;

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

const router = new weMail.VueRouter({
    routes: weMail.routes.map((route) => {
        return mapRoute(route);
    })
});

// Lazy load router components
router.beforeEach((to, from, next) => {
    const rootRoute = weMail.namedRoutes[to.matched[0].name];

    // Get the root route component script
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

    function getRequiredScripts() {
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
    }

    // Decides which routes require to pull data from backend
    function getRequiredRouteData() {
        let routes = [];
        let i = 0;

        routes = to.matched.map((route) => {
            return route.name;
        });

        // /foo/bar -> /foo/buzz - get data only for buzz
        // /foo/bar -> /hello - get data for hello
        if (from.matched.length) {
            routes = [];

            for (i = 0; i < to.matched.length; i++) {
                if (!from.matched[i] || (to.matched[i].name !== from.matched[i].name)) {
                    routes.push(to.matched[i].name);
                }
            }
        }

        // /foo/bar -> /foo - get data for foo
        // /foo -> /foo?s=search - get data for foo with query
        if (!routes.length) {
            const currentRouteName = to.matched[to.matched.length - 1].name;
            const routeProps = weMail.namedRoutes[currentRouteName];

            if (routeProps.ignoreDataRefetch && routeProps.ignoreDataRefetch.query) {
                const match = routeProps.ignoreDataRefetch.query.filter((query) => {
                    return to.query.hasOwnProperty(query);
                });

                if (!match.length) {
                    routes.push(currentRouteName);
                }
            } else {
                routes.push(currentRouteName);
            }
        }

        return routes;
    }

    if (rootRoute && rootRoute.scrollTo) {
        const scrollTo = (rootRoute.scrollTo === 'top') ? 0 : rootRoute.scrollTo;
        const DURATION = 200;

        $('body').animate({
            scrollTop: scrollTo
        }, DURATION);
    }

    if (rootRoute) {
        const routeNames = getRequiredRouteData();

        if (routeNames.length) {
            weMail.router.app.showLoadingAnime = true;

            weMail
                .ajax
                .get('get_route_data', {
                    routes: routeNames,
                    params: to.params,
                    query: to.query
                })
                .done((response) => {
                    if (response.data) {
                        weMail._.forEach(response.data, (routeData, routeName) => {
                            weMail.registerStore(routeName, {
                                state: routeData
                            });
                        });
                    }

                    getRequiredScripts();
                });
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;
