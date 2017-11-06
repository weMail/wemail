/* eslint-disable global-require */

import RouterView from '../RouterView.js';

function Index(resolve) {
    require.ensure(['./Index.vue'], () => {
        resolve(require('./Index.vue'));
    });
}

function Subscribers(resolve) {
    require.ensure(['./Subscribers.vue'], () => {
        resolve(require('./Subscribers.vue'));
    });
}

export default {
    path: '/lists',
    component: RouterView,
    children: [
        {
            path: '',
            component: Index,
            name: 'listsIndex',
            children: [
                {
                    path: 'type/:type',
                    name: 'listsIndexType'
                }
            ]
        },
        {
            path: ':id',
            component: RouterView,
            redirect: {
                name: 'listsSubscribers'
            },
            children: [
                {
                    path: 'subscribers',
                    component: Subscribers,
                    name: 'listsSubscribers'
                }
            ]
        }
    ]
};
