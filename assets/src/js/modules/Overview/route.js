/* eslint-disable func-style */
/* eslint-disable global-require */

const Overview = (resolve) => {
    require.ensure(['./Overview.vue'], () => {
        resolve(require('./Overview.vue'));
    });
};

const route = {
    path: '/',
    component: Overview,
    name: 'overview'
};

export default route;
