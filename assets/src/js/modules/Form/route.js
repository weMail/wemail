/* eslint-disable func-style */
/* eslint-disable global-require */

const Forms = (resolve) => {
    require.ensure(['./Forms.vue'], () => {
        resolve(require('./Forms.vue'));
    });
};

const Form = (resolve) => {
    require.ensure(['./Form.vue'], () => {
        resolve(require('./Form.vue'));
    });
};

weMail.subMenuMap.push({
    name: 'form',
    submenu: '/forms'
});

export default [
    {
        path: '/forms',
        component: Forms,
        name: 'forms'
    },
    {
        path: '/form/:id',
        component: Form,
        name: 'form'
    }
];
