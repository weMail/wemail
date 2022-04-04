;(function($) {
    $(document).ready(function() {
        $(document).on("click", '.wemail-review-notice-flex-container .notice-dismiss', function() {
            var url = new URL(location.href);
            url.searchParams.append("dismiss_wemail_review_notice", 1);
            location.href = url;
        });
    });
})(jQuery);
