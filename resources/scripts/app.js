import '../../resources/styles/app.scss';

document.addEventListener('DOMContentLoaded', () => {

    // Get all "navbar-burger" elements
    const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

    // Check if there are any navbar burgers
    if ($navbarBurgers.length > 0) {
        // Add a click event on each of them
        $navbarBurgers.forEach(el => {
            el.addEventListener('click', () => {

                // Get the target from the "data-target" attribute
                const target = el.dataset.target;
                const $target = document.getElementById(target);

                // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
                el.classList.toggle('is-active');
                $target.classList.toggle('is-active');

            });
        });
    }

    const notifications = document.querySelectorAll('.notification-wrapper');
    setTimeout(function () {
        notifications.forEach(function (el) {
            removeFadeOut(el, 1000);
        });
    }, 6500);

    if (document.getElementById('ct')) {
        display_ct();
    }
});

function removeFadeOut(el, speed) {
    el.style.transition = 'opacity ' + speed + 'ms ease';
    el.style.opacity = '0';
    setTimeout(function () {
        el.parentNode.removeChild(el);
    }, speed);
}

function display_ct() {
    const x = new Date();
    let hour = x.getHours();
    let ampm = hour >= 12 ? 'pm' : 'am';
    hour = hour % 12;
    hour = hour ? hour : 12;
    let minute = x.getMinutes();
    let second = x.getSeconds();
    if (minute < 10) {
        minute = '0' + minute;
    }
    if (second < 10) {
        second = '0' + second;
    }
    document.getElementById('ct').innerHTML = hour + ':' + minute + ':' + second + ' ' + ampm;
    setTimeout(display_ct, 1000);
}

document.addEventListener('DOMContentLoaded', () => {
    const navbar = Array.prototype.slice.call(document.querySelectorAll('.navbar a'), 0);
    for (let i = 0; i < navbar.length; i++) {
        if (navbar[i].href === window.location.href) {
            navbar[i].classList.add("is-info", "is-active");
            navbar[i].classList.remove("is-light");
        } else {
            navbar[i].classList.remove("is-info", "is-active");
            navbar[i].classList.add("is-light");
        }
    }
});
