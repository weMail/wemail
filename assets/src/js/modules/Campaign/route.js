/* eslint-disable func-style */
/* eslint-disable global-require */

import RouterView from '../RouterView.js';

const Index = (resolve) => {
    require.ensure(['./Index.vue'], () => {
        resolve(require('./Index.vue'));
    });
};

const Create = (resolve) => {
    require.ensure(['./Create.vue'], () => {
        resolve(require('./Create.vue'));
    });
};

const Show = (resolve) => {
    require.ensure(['./Show.vue'], () => {
        resolve(require('./Show.vue'));
    });
};

const Edit = (resolve) => {
    require.ensure(['./Edit.vue'], () => {
        resolve(require('./Edit.vue'));
    });
};

const EditSetup = (resolve) => {
    require.ensure(['./EditSetup.vue'], () => {
        resolve(require('./EditSetup.vue'));
    });
};

const EditTemplate = (resolve) => {
    require.ensure(['./EditTemplate.vue'], () => {
        resolve(require('./EditTemplate.vue'));
    });
};

const EditDesign = (resolve) => {
    require.ensure(['./EditDesign.vue'], () => {
        resolve(require('./EditDesign.vue'));
    });
};

const EditSend = (resolve) => {
    require.ensure(['./EditSend.vue'], () => {
        resolve(require('./EditSend.vue'));
    });
};

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
