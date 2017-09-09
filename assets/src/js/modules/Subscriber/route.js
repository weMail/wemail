/* eslint-disable func-style */
/* eslint-disable global-require */

const Subscriber = (resolve) => {
    require.ensure(['./Subscriber.vue'], () => {
        resolve(require('./Subscriber.vue'));
    });
};

const Subscribers = (resolve) => {
    require.ensure(['./Subscribers.vue'], () => {
        resolve(require('./Subscribers.vue'));
    });
};

export default [
    {
        path: '/subscribers/:id',
        name: 'subscriber',
        component: Subscriber
    },
    {
        path: '/subscribers',
        name: 'subscribers',
        component: Subscribers
    }
];
