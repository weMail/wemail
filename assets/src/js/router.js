import Auth from './core/Auth/route.js';
import Overview from './core/Overview/route.js';
import Campaign from './core/Campaign/route.js';
import Subscriber from './core/Subscriber/route.js';
import Lists from './core/Lists/route.js';
import Form from './core/Form/route.js';
import Settings from './core/Settings/route.js';
import Import from './core/Import/route.js';
import FourZeroFour from './core/FourZeroFour/route.js';

const routes = [
    Auth,
    Overview,
    Campaign,
    Subscriber,
    Lists,
    Form,
    Settings,
    Import,
    FourZeroFour
];

const router = new VueRouter({
    routes
});

export default router;
