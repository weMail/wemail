/* eslint-disable func-style */
/* eslint-disable global-require */

const Form = (resolve) => {
    require.ensure(['./Form.vue'], () => {
        resolve(require('./Form.vue'));
    });
};

const Forms = (resolve) => {
    require.ensure(['./Forms.vue'], () => {
        resolve(require('./Forms.vue'));
    });
};

export default [
    {
        path: '/forms/:id',
        component: Form,
        name: 'form'
    },
    {
        path: '/forms',
        component: Forms,
        name: 'forms'
    }
];
