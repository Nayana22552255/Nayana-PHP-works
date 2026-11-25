document.addEventListener('DOMContentLoaded', function () {
    const menuToggle = document.querySelector('.site-menu-toggle');
    const navigation = document.getElementById('site-navigation');

    if (!menuToggle || !navigation) {
        return;
    }

    function closeNavigation() {
        menuToggle.setAttribute('aria-expanded', 'false');
        navigation.classList.remove('is-open');
    }

    menuToggle.addEventListener('click', function () {
        const isExpanded = menuToggle.getAttribute('aria-expanded') === 'true';
        menuToggle.setAttribute('aria-expanded', String(!isExpanded));
        navigation.classList.toggle('is-open', !isExpanded);
    });

    navigation.addEventListener('click', function (event) {
        if (event.target instanceof Element && event.target.closest('a')) {
            closeNavigation();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeNavigation();
        }
    });
});