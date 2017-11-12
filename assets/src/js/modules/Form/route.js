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

export default {
    path: '/forms',
    component: RouterView,
    children: [
        {
            path: '',
            component: Index,
            name: 'formIndex',
            children: [
                {
                    path: 'status/:status',
                    name: 'formIndexStatus'
                },
                {
                    path: 'create',
                    component: Create,
                    name: 'formCreate'
                }
            ]
        },
        {
            path: ':id',
            component: RouterView,
            redirect: {
                name: 'formEdit'
            },
            children: [
                {
                    path: 'edit',
                    component: Edit,
                    name: 'formEdit'
                }
            ]
        }
    ]
};
