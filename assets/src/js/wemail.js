// jQuery ajax wrapper
function Ajax(url, method, headers, data) {
    return $.ajax({
        url,
        method,
        dataType: 'json',
        headers,
        data
    });
}

// Ajax get method
weMail.ajax.get = (action, options) => {
    return Ajax(weMail.ajaxurl, 'get', {}, $.extend(true, {
        action: `wemail_${action}`,
        _wpnonce: weMail.nonce
    }, options));
};

// Ajax post method
weMail.ajax.post = (action, options) => {
    return Ajax(weMail.ajaxurl, 'post', {}, $.extend(true, {
        action: `wemail_${action}`,
        _wpnonce: weMail.nonce
    }, options));
};

// weMail REST API
const API = {
    root: weMail.api.root,
    cdn: weMail.api.cdn,
    site: weMail.api.site,
    user: weMail.api.user,
    _query: {},
    _url: '',

    getHeaders() {
        return {
            site: this.site,
            user: this.user
        };
    },

    resetProps() {
        this._url = '';
        this._query = {};
    },

    query(query) {
        this._query = $.extend(true, this._query, query);

        return this;
    },

    buildQuery(url, query) {
        if (url) {
            url = `${this.root}${url}`;

        } else if (this._url) {
            url = `${this.root}${this._url}`;
        }

        if (query) {
            this._query = query;
        }

        return url;
    },

    get(url, query) {
        url = this.buildQuery(url, query);

        const response = Ajax(url, 'get', this.getHeaders(), $.extend(true, {}, this._query));

        this.resetProps();

        return response;
    },

    post(url, data) {
        url = this.buildQuery(url);

        const response = Ajax(url, 'post', this.getHeaders(), $.extend(true, {}, data));

        this.resetProps();

        return response;
    },

    create(data) {
        return this.post(null, data);
    },

    save(data) {
        return this.post(null, data);
    }
};

weMail.api = new Proxy(API, {
    get(api, field) {
        if (api.hasOwnProperty(field)) {
            return api[field];
        }

        return function (param) {
            this._url += `/${weMail._.kebabCase(field)}`;

            if (param) {
                this._url += `/${param}`;
            }

            return this;
        };
    }
});

// Register vuex stores
weMail.registerStore = function (routeName, store) {
    if (!this.stores[routeName]) {
        this.stores[routeName] = {};
    }

    this.stores[routeName] = $.extend(true, this.stores[routeName], store);
};

// Register vue mixins
weMail.registerMixins = function (mixins) {
    weMail._.forEach(mixins, (mixin, name) => {
        if (!weMail.mixins[name]) {
            weMail.mixins[name] = mixin;
        }
    });
};

weMail.getMixins = function (...mixins) {
    return mixins.map((mixin) => weMail.mixins[mixin]);
};

// weMail VueRouter api
weMail.childRoutes = {};

weMail.registerChildRoute = function (parent, childRoute) {
    if (!weMail.childRoutes[parent]) {
        weMail.childRoutes[parent] = [];
    }

    weMail.childRoutes[parent].push(childRoute);
};

weMail.getChildRoutes = function (parent) {
    return weMail.childRoutes[parent] || [];
};
