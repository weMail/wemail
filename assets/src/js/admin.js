import '../scss/admin.scss';

// The main Vue instance
weMail.admin = new weMail.Vue({
    el: '#wemail-admin',
    store: weMail.store,
    router: weMail.router,
    data: {
        showLoadingAnime: true
    },

    watch: {
        $route(to, from) {
            // Add current class to matching submenu
            const mainMenu = $('#toplevel_page_wemail');
            const rootRoute = to.matched[0];
            const path = rootRoute.path.match(/^\//) ? rootRoute.path : `/${rootRoute.path}`;
            const anch = mainMenu.find(`a[href="admin.php?page=wemail#${path}"]`);

            $('ul.wp-submenu li', mainMenu).removeClass('current');

            if (anch.length) {
                anch.parent().addClass('current');
            } else {
                const route = weMail._.filter(weMail.routes, {
                    name: rootRoute.name
                })[0];

                if (route.submenu) {
                    mainMenu
                        .find(`a[href="admin.php?page=wemail#${route.submenu}"]`)
                        .parent()
                        .addClass('current');
                }
            }
        }
    }
});
