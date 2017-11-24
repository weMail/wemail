/* eslint-disable global-require */

import RouterView from '../RouterView.js';

function Site(resolve) {
    require.ensure(['./Site.vue'], () => {
        resolve(require('./Site.vue'));
    });
}

export default {
    path: '/auth',
    component: RouterView,
    children: [
        {
            path: '',
            component: RouterView,
            redirect: {
                name: 'authSite'
            },
            children: [
                {
                    path: 'site',
                    name: 'authSite',
                    component: Site
                }
            ]
        }
    ]
};
