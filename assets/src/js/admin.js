import '../sass/admin.scss';

// The main Vue instance
weMail.admin = new weMail.Vue({
    el: '#wemail-admin',
    router: weMail.router,
    data: {
        showLoadingAnime: true
    }
});

// Admin menu hack
const menuRoot = $('#toplevel_page_wemail');

menuRoot.on('click', 'a', function () {
    const self = $(this);

    $('ul.wp-submenu li', menuRoot).removeClass('current');

    if (self.hasClass('wp-has-submenu')) {
        $('li.wp-first-item', menuRoot).addClass('current');
    } else {
        self.parents('li').addClass('current');
    }
});

// select the current sub menu on page load
const currentURL = window.location.href;
const currentPath = currentURL.substr(currentURL.indexOf('admin.php'));

$('ul.wp-submenu a', menuRoot).each((index, el) => {
    if ($(el).attr('href') === currentPath) {
        $(el).parent().addClass('current');
    }
});
