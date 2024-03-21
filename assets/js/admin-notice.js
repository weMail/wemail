;(function($) {
    $(document).ready(function() {
        $(document).on("click", '.wemail-connect-notice-flex-container .notice-dismiss', function() {
            var url = new URL(location.href);
            url.searchParams.append("dismiss_connect_notice", 1);
            url.searchParams.append("wemail_dismiss_notice_nonce", weMail.nonce);
            location.href = url;
        });
    });
})(jQuery);
