document.addEventListener('DOMContentLoaded', function() {
    const navbarToggler = document.querySelector('.navbar-toggler');
    const navbarContent = document.querySelector('#navbarContent');

    navbarToggler.addEventListener('click', function() {
        navbarContent.classList.toggle('show');
    });

    document.addEventListener('click', function(event) {
        const isNavbar = event.target.closest('.navbar');
        if (!isNavbar && navbarContent.classList.contains('show')) {
            navbarContent.classList.remove('show');
        }
    });
});