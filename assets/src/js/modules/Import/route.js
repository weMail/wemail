/* eslint-disable global-require */

import RouterView from '../RouterView.js';

function Index(resolve) {
    require.ensure(['./Index.vue'], () => {
        resolve(require('./Index.vue'));
    });
}

function MailChimp(resolve) {
    require.ensure(['./MailChimp/MailChimp.vue'], () => {
        resolve(require('./MailChimp/MailChimp.vue'));
    });
}

function MailChimpSettings(resolve) {
    require.ensure(['./MailChimp/Settings.vue'], () => {
        resolve(require('./MailChimp/Settings.vue'));
    });
}

function MailChimpList(resolve) {
    require.ensure(['./MailChimp/List.vue'], () => {
        resolve(require('./MailChimp/List.vue'));
    });
}

function MailChimpMap(resolve) {
    require.ensure(['./MailChimp/Map.vue'], () => {
        resolve(require('./MailChimp/Map.vue'));
    });
}

function MailChimpProgress(resolve) {
    require.ensure(['./MailChimp/Progress.vue'], () => {
        resolve(require('./MailChimp/Progress.vue'));
    });
}

function MailPoet(resolve) {
    require.ensure(['./MailPoet/MailPoet.vue'], () => {
        resolve(require('./MailPoet/MailPoet.vue'));
    });
}

function MailPoetList(resolve) {
    require.ensure(['./MailPoet/List.vue'], () => {
        resolve(require('./MailPoet/List.vue'));
    });
}

function MailPoetMap(resolve) {
    require.ensure(['./MailPoet/Map.vue'], () => {
        resolve(require('./MailPoet/Map.vue'));
    });
}

function MailPoetProgress(resolve) {
    require.ensure(['./MailPoet/Progress.vue'], () => {
        resolve(require('./MailPoet/Progress.vue'));
    });
}

export default {
    path: '/import',
    component: RouterView,
    children: [
        {
            path: '',
            component: Index,
            name: 'importIndex'
        },
        {
            path: 'mailchimp',
            component: MailChimp,
            children: [
                {
                    path: '',
                    name: 'importMailChimpSettings',
                    component: MailChimpSettings
                },
                {
                    path: 'select-list',
                    name: 'importMailChimpList',
                    component: MailChimpList
                },
                {
                    path: 'field-map',
                    name: 'importMailChimpMap',
                    component: MailChimpMap
                },
                {
                    path: 'progress',
                    name: 'importMailChimpProgress',
                    component: MailChimpProgress
                }
            ]
        },
        {
            path: 'mailpoet-v2',
            component: MailPoet,
            children: [
                {
                    path: '',
                    name: 'importMailPoetList',
                    component: MailPoetList
                },
                {
                    path: 'field-map',
                    name: 'importMailPoetMap',
                    component: MailPoetMap
                },
                {
                    path: 'progress',
                    name: 'importMailPoetProgress',
                    component: MailPoetProgress
                }
            ]
        }
        // {
        //     path: 'csv',
        //     name: 'importCsv'
        // },
    ]
};
