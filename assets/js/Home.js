weMail.routeComponents.Home = {
    template: `
        <div>
            <p>Home component</p>

            <p><input type="text" v-wemail-datepicker:foo.a.b="message" id="hook-arguments-example"></p>
            <partial-home></partial-home>
        </div>
    `,

    data() {
        return {
            message: 'this is message string',
            buttonLabel: 'this is button label'
        }
    }
};


weMail.component('partial-home', {
    mixins: [weMail.mixins.partials],

    computed: {
        partials: function () {
            return weMail.partials.home || [];
        }
    },
});
