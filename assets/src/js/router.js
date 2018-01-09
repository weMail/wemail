import Auth from './modules/Auth/route.js';
import Overview from './modules/Overview/route.js';
import Campaign from './modules/Campaign/route.js';
import Subscriber from './modules/Subscriber/route.js';
import Lists from './modules/Lists/route.js';
import Form from './modules/Form/route.js';
import Settings from './modules/Settings/route.js';
import Import from './modules/Import/route.js';
import FourZeroFour from './modules/FourZeroFour/route.js';

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
