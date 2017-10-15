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

function Show(resolve) {
    require.ensure(['./Show.vue'], () => {
        resolve(require('./Show.vue'));
    });
}

function Edit(resolve) {
    require.ensure(['./Edit.vue'], () => {
        resolve(require('./Edit.vue'));
    });
}

function EditSetup(resolve) {
    require.ensure(['./EditSetup.vue'], () => {
        resolve(require('./EditSetup.vue'));
    });
}

function EditTemplate(resolve) {
    require.ensure(['./EditTemplate.vue'], () => {
        resolve(require('./EditTemplate.vue'));
    });
}

function EditDesign(resolve) {
    require.ensure(['./EditDesign.vue'], () => {
        resolve(require('./EditDesign.vue'));
    });
}

function EditSend(resolve) {
    require.ensure(['./EditSend.vue'], () => {
        resolve(require('./EditSend.vue'));
    });
}

export default {
    path: '/campaigns',
    component: RouterView,
    children: [
        {
            path: '',
            component: Index,
            name: 'campaignIndex'
        },
        {
            path: 'create',
            component: Create,
            name: 'campaignCreate'
        },
        {
            path: ':id',
            component: RouterView,
            children: [
                {
                    path: '',
                    component: Show,
                    name: 'campaignShow'
                },
                {
                    path: 'edit',
                    component: Edit,
                    name: 'campaignEdit',
                    redirect: {
                        name: 'campaignEditSetup'
                    },
                    children: [
                        {
                            path: 'setup',
                            component: EditSetup,
                            name: 'campaignEditSetup'
                        },
                        {
                            path: 'template',
                            component: EditTemplate,
                            name: 'campaignEditTemplate'
                        },
                        {
                            path: 'design',
                            component: EditDesign,
                            name: 'campaignEditDesign'
                        },
                        {
                            path: 'send',
                            component: EditSend,
                            name: 'campaignEditSend'
                        }
                    ]
                }
            ]
        }
    ]
};
