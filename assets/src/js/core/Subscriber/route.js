/* eslint-disable global-require */

import RouterView from '../RouterView.js';

function Index(resolve) {
    require.ensure(['./Index.vue'], () => {
        resolve(require('./Index.vue'));
    });
}

function Create(resolve) {
    require.ensure(['./Create.vue'], () => {
        resolve(require('./Create.vue'));
    });
}

function Edit(resolve) {
    require.ensure(['./Edit.vue'], () => {
        resolve(require('./Edit.vue'));
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
            name: 'subscriberIndex',
            children: [
                {
                    path: 'status/:status',
                    name: 'subscriberIndexStatus'
                },
                {
                    path: 'life-stage/:lifeStage',
                    name: 'subscriberIndexLifeStage'
                },
                {
                    path: 'create',
                    name: 'subscriberCreate',
                    component: Create
                },
                {
                    path: ':id/edit',
                    name: 'subscriberEdit',
                    component: Edit
                }
            ]
        },
        {
            path: ':id',
            component: Show,
            name: 'subscriberShow'
        }
    ]
};
