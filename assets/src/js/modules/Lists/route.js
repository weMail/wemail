const Lists = (resolve) => { // eslint-disable-line func-style
    require.ensure(['./Lists.vue'], () => {
        resolve(require('./Lists.vue')); // eslint-disable-line global-require
    });
};

export default {
    path: '/lists',
    component: Lists
};
