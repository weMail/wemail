;(function($) {
    'use strict';

    function Ajax(url, method, data) {
        return $.ajax({
            url: url,
            method: method,
            dataType: 'json',
            data: data
        });
    }

    weMail.ajax.get = function (action, options) {
        return Ajax(weMail.ajaxurl, 'get', $.extend(true, {
            action: 'wemail_' + action,
            _wpnonce: weMail.nonce,
        }, options));
    }

    weMail.ajax.post = function (action, options) {
        return Ajax(weMail.ajaxurl, 'post', $.extend(true, {
            action: 'wemail_' + action,
            _wpnonce: weMail.nonce,
        }, options));
    }

    weMail.api.get = function (route, query, rootEndPoint) {
        var url = !rootEndPoint ? (weMail.api.rootEndPoint + route) : (rootEndPoint + router);

        return Ajax(url, 'get', $.extend(true, {
            apiKey: weMail.api.key
        }, query));
    }

    weMail.api.post = function (route, query, rootEndPoint) {
        var url = !rootEndPoint ? (weMail.api.rootEndPoint + route) : (rootEndPoint + router);

        return Ajax(url, 'post', $.extend(true, {
            apiKey: weMail.apiKey
        }, query));
    }

    // Register vue component to prevent duplicate registration
    weMail.component = function (tagName, options) {
        if (this.registeredComponents.indexOf(tagName) >= 0) {
            return;
        }

        this.registeredComponents.push(tagName);

        Vue.component(tagName, options);
    }

    weMail.registerStore = function (routeName, store) {
        if (!this.stores[routeName]) {
            this.stores[routeName] = {};
        }

        this.stores[routeName] = $.extend(true, this.stores[routeName], store);
    }
})(jQuery);
