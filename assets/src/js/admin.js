import router from './router.js';
import '../scss/admin.scss';

__webpack_public_path__ = `${weMail.assetsURL}/js/`; // eslint-disable-line camelcase

// The main Vue instance
weMail.admin = new Vue({
    el: '#wemail-admin',
    store: weMail.store,
    router,
    data: {
        showLoadingAnime: false
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
                const pathParts = rootRoute.path.split('/');
                let parent = '';

                if (pathParts.length > 1) {
                    parent = `/${pathParts[1]}`;
                } else {
                    const route = _.chain(weMail.subMenuMap)
                        .filter({
                            name: rootRoute.name
                        })
                        .head()
                        .value();

                    if (route && route.submenu) {
                        parent = route.submenu;
                    }
                }

                mainMenu
                    .find(`a[href="admin.php?page=wemail#${parent}"]`)
                    .parent()
                    .addClass('current');
            }
        }
    }
});
