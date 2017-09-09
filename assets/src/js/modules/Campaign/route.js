const Campaigns = (resolve) => { // eslint-disable-line func-style
    require.ensure(['./Campaigns.vue'], () => {
        resolve(require('./Campaigns.vue')); // eslint-disable-line global-require
    });
};

export default {
    path: '/campaigns',
    component: Campaigns,
    name: 'campaigns'
};
