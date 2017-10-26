/* eslint-disable global-require */

import RouterView from '../RouterView.js';

function Index(resolve) {
    require.ensure(['./Index.vue'], () => {
        resolve(require('./Index.vue'));
    });
}

function Show(resolve) {
    require.ensure(['./Show.vue'], () => {
        resolve(require('./Show.vue'));
    });
}

export default {
    path: '/lists',
    component: RouterView,
    children: [
        {
            path: '',
            component: Index,
            name: 'listsIndex'
        },
        {
            path: ':id',
            component: Show,
            name: 'listsShow'
        }
    ]
};
