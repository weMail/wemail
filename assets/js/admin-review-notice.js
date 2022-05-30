;(function($) {
    $(document).ready(function() {
        $(document).on("click", '.wemail-review-notice-flex-container .notice-dismiss', function() {
            var url = new URL(location.href);
            url.searchParams.append("dismiss_wemail_review_notice", 1);
            url.searchParams.append('review_nonce', $(this).parent().data('nonce'));
            location.href = url;
        });
    });
})(jQuery);
