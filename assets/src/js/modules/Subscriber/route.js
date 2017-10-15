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
    path: '/subscribers',
    component: RouterView,
    children: [
        {
            path: '',
            component: Index,
            name: 'subscriberIndex'
        },
        {
            path: ':hash',
            component: Show,
            name: 'subscriberShow'
        }
    ]
};
