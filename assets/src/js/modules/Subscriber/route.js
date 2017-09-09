/* eslint-disable func-style */
/* eslint-disable global-require */

const Subscribers = (resolve) => {
    require.ensure(['./Subscribers.vue'], () => {
        resolve(require('./Subscribers.vue'));
    });
};

const Subscriber = (resolve) => {
    require.ensure(['./Subscriber.vue'], () => {
        resolve(require('./Subscriber.vue'));
    });
};

weMail.subMenuMap.push({
    name: 'subscriber',
    submenu: '/subscribers'
});

export default [
    {
        path: '/subscribers',
        component: Subscribers,
        name: 'subscribers'
    },
    {
        path: '/subscriber/:id',
        component: Subscriber,
        name: 'subscriber'
    }
];
