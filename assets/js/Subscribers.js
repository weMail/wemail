weMail.routeComponents.Subscribers = {
    template: '<p>Subscribers component <test-comp></test-comp></p>'
};

weMail.component('test-comp', {
    template: '<strong>{{ test }}</strong>',

    data: function () {
        return {
            test: 'hello from test Subscribers.js'
        }
    }
});
