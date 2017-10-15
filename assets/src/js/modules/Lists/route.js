/* eslint-disable global-require */

function List(resolve) {
    require.ensure(['./List.vue'], () => {
        resolve(require('./List.vue'));
    });
}

function Lists(resolve) {
    require.ensure(['./Lists.vue'], () => {
        resolve(require('./Lists.vue'));
    });
}

export default [
    {
        path: '/lists/:id',
        name: 'list',
        component: List
    },
    {
        path: '/lists',
        name: 'lists',
        component: Lists
    }
];
