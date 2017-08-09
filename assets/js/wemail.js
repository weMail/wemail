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

    weMail.api.get = function (route, query) {
        var url = route ? weMail.apiRootEndPoint + route : weMail.apiRootEndPoint;

        return Ajax(url, 'get', $.extend(true, {
            apiKey: weMail.apiKey
        }, query));
    }

    weMail.api.post = function (route, query) {
        var url = route ? weMail.apiRootEndPoint + route : weMail.apiRootEndPoint;

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
})(jQuery);
