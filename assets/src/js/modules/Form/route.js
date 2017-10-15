/* eslint-disable global-require */

function Form(resolve) {
    require.ensure(['./Form.vue'], () => {
        resolve(require('./Form.vue'));
    });
}

function Forms(resolve) {
    require.ensure(['./Forms.vue'], () => {
        resolve(require('./Forms.vue'));
    });
}

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
