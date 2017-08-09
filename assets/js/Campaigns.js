weMail.routeComponents.Campaigns = {
    template: '<p>Campaigns component <test-comp></test-comp></p>'
};

weMail.component('test-comp', {
    template: '<strong>{{ test }}</strong>',

    data: function () {
        return {
            test: 'hello from test Campaigns.js'
        }
    }
});
