/* eslint-disable global-require */

function Settings(resolve) {
    require.ensure(['./Settings.vue'], () => {
        resolve(require('./Settings.vue'));
    });
}

function Company(resolve) {
    require.ensure(['./Company.vue'], () => {
        resolve(require('./Company.vue'));
    });
}

function SocialNetworks(resolve) {
    require.ensure(['./SocialNetworks.vue'], () => {
        resolve(require('./SocialNetworks.vue'));
    });
}

weMail.registerChildRoute('settings', {
    path: 'company',
    component: Company,
    name: 'settingsCompany'
});

weMail.registerChildRoute('settings', {
    path: 'social-networks',
    component: SocialNetworks,
    name: 'settingsSocialNetworks'
});

const route = {
    path: '/settings',
    component: Settings,
    name: 'settings',
    redirect: {
        name: 'settingsCompany'
    },
    children: weMail.getChildRoutes('settings')
};

export default route;
