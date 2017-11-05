/* eslint-disable global-require */

function Settings(resolve) {
    require.ensure(['./Settings.vue'], () => {
        resolve(require('./Settings.vue'));
    });
}

export default {
    path: '/settings',
    component: Settings,
    redirect: '/settings/company',
    children: [
        {
            path: ':name',
            name: 'settings'
        }
    ]
};
