/* ============================================================
   CrossWay Darjeeling Travel — shared site behaviour
   ============================================================ */

/* ---- WhatsApp ------------------------------------------------ */
var WHATSAPP_NUMBER = '917797970234';
var SITE_PHONE = '7797970234';
var SITE_EMAIL = 'crosswaydarjeelingtravel@gmail.com';

function waLink(text) {
    var base = 'https://wa.me/' + WHATSAPP_NUMBER;
    return text ? base + '?text=' + encodeURIComponent(text) : base;
}

function openWhatsApp(text) {
    window.open(waLink(text), '_blank', 'noopener');
}

/* ---- Small helpers ------------------------------------------ */
function esc(value) {
    if (value === null || value === undefined) return '';
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function truncate(text, length) {
    if (!text) return '';
    text = String(text);
    return text.length > length ? text.slice(0, length) + '...' : text;
}

/* Renders a price, or "Price on request" when no price is set. */
function formatPrice(price, unit) {
    if (price === null || price === undefined || price === '' || Number(price) <= 0) {
        return '<span class="on-request">Price on request</span>';
    }
    var amount = Number(price).toLocaleString('en-IN', { maximumFractionDigits: 0 });
    return '₹' + amount + (unit ? ' <small>/ ' + esc(unit) + '</small>' : '');
}

/* ---- Navbar ------------------------------------------------- */
function initNavbar() {
    var navbar = document.getElementById('mainNavbar');

    if (navbar) {
        window.addEventListener('scroll', function () {
            var y = window.pageYOffset || document.documentElement.scrollTop;
            navbar.classList.toggle('scrolled', y > 80);
        });
    }

    /* Highlight the current page. Derived from the filename so the markup
       stays identical on every page. */
    var file = window.location.pathname.split('/').pop() || 'index.html';
    var links = document.querySelectorAll('.main-navbar .nav-link');
    var matched = false;
    Array.prototype.forEach.call(links, function (link) {
        var href = link.getAttribute('href');
        if (href === file) {
            link.classList.add('active');
            matched = true;
        }
    });
    /* package-detail.html has no nav item of its own — light up Packages. */
    if (!matched && file.indexOf('package-detail') === 0) {
        var pkg = document.querySelector('.main-navbar .nav-link[href="packages.html"]');
        if (pkg) pkg.classList.add('active');
    }

    /* Close the mobile menu after tapping a link. */
    var collapse = document.getElementById('navbarNav');
    Array.prototype.forEach.call(links, function (link) {
        link.addEventListener('click', function () {
            if (collapse && collapse.classList.contains('show')) {
                collapse.classList.remove('show');
            }
        });
        link.addEventListener('mouseenter', function () {
            if (!this.classList.contains('active')) this.style.transform = 'translateY(-2px)';
        });
        link.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0)';
        });
    });
}

/* ---- Footer year -------------------------------------------- */
function initFooterYear() {
    var el = document.getElementById('footerYear');
    if (el) el.textContent = new Date().getFullYear();
}

/* ---- Lightbox ----------------------------------------------- */
/* galleryData is populated by render.js before this is used. */
var galleryData = [];
var currentLightboxIndex = 0;

function setGalleryData(items) {
    galleryData = items || [];
}

function openLightbox(index) {
    if (!galleryData.length) return;
    currentLightboxIndex = index;
    updateLightbox();
    var overlay = document.getElementById('lightboxOverlay');
    if (overlay) {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
}

function closeLightbox(event) {
    if (event) {
        /* Only close on the backdrop or the explicit close/nav controls. */
        var isBackdrop = event.target.id === 'lightboxOverlay';
        var isControl = event.currentTarget && event.currentTarget.classList
            && event.currentTarget.classList.contains('lightbox-close');
        if (!isBackdrop && !isControl) return;
    }
    var overlay = document.getElementById('lightboxOverlay');
    if (overlay) {
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
}

function navigateLightbox(step, event) {
    if (event) event.stopPropagation();
    if (!galleryData.length) return;
    currentLightboxIndex = (currentLightboxIndex + step + galleryData.length) % galleryData.length;
    updateLightbox();
}

function updateLightbox() {
    var item = galleryData[currentLightboxIndex];
    if (!item) return;

    var img = document.getElementById('lightboxImage');
    if (img) {
        img.src = item.image_path;
        img.alt = item.title || '';
    }

    var title = document.getElementById('lightboxTitle');
    if (title) title.textContent = item.title || '';

    var category = document.getElementById('lightboxCategory');
    if (category) category.textContent = item.category || 'Himalayan Beauty';

    /* gallery.html has a description line; index.html does not. */
    var description = document.getElementById('lightboxDescription');
    if (description) description.textContent = item.description || '';
}

function initLightboxKeys() {
    document.addEventListener('keydown', function (e) {
        var overlay = document.getElementById('lightboxOverlay');
        if (!overlay || !overlay.classList.contains('active')) return;
        if (e.key === 'Escape') closeLightbox();
        if (e.key === 'ArrowLeft') navigateLightbox(-1);
        if (e.key === 'ArrowRight') navigateLightbox(1);
    });
}

/* ---- Alerts -------------------------------------------------- */
/* Reuses the .alert-custom markup the PHP version used for its
   success/error messages. */
function showAlert(type, message) {
    var host = document.getElementById('formAlerts');
    if (!host) {
        window.alert(message);
        return;
    }
    var isError = type === 'error';
    host.innerHTML =
        '<div class="alert alert-custom ' + (isError ? 'alert-danger' : 'alert-success') + '" role="alert">' +
            '<i class="fas ' + (isError ? 'fa-exclamation-circle' : 'fa-check-circle') + ' me-2"></i> ' +
            esc(message) +
            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
        '</div>';
    host.scrollIntoView({ behavior: 'smooth', block: 'center' });

    var alertEl = host.querySelector('.alert');
    window.setTimeout(function () {
        if (!alertEl) return;
        alertEl.style.transition = 'opacity 0.5s ease';
        alertEl.style.opacity = '0';
        window.setTimeout(function () { host.innerHTML = ''; }, 500);
    }, 6000);
}

/* ---- Contact form → WhatsApp -------------------------------- */
/* Same validation rules the PHP handler used: name, email, subject and
   message required, and the email must look like an email. */
function initContactForm() {
    var form = document.getElementById('contactForm');
    if (!form) return;

    form.addEventListener('submit', function (e) {
        e.preventDefault();

        var get = function (field) {
            var el = form.elements[field];
            return el ? el.value.trim() : '';
        };

        var name = get('name');
        var email = get('email');
        var phone = get('phone');
        var subject = get('subject');
        var message = get('message');

        if (!name || !email || !subject || !message) {
            showAlert('error', 'Please fill all required fields.');
            return;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) {
            showAlert('error', 'Please enter a valid email address.');
            return;
        }

        var lines = [
            'Hi CrossWay Travel!',
            'Name: ' + name,
            'Phone: ' + (phone || 'not provided'),
            'Email: ' + email,
            'Subject: ' + subject,
            'Message: ' + message
        ];

        openWhatsApp(lines.join('\n'));
        showAlert('success', 'Opening WhatsApp with your enquiry. If nothing happens, message us on ' + SITE_PHONE + '.');
        form.reset();
    });

    /* If someone arrives from a "Book Now" link, prefill the subject. */
    var pkg = new URLSearchParams(window.location.search).get('package');
    if (pkg && form.elements.subject && !form.elements.subject.value) {
        form.elements.subject.value = pkg;
    }
}

/* ---- Boot --------------------------------------------------- */
document.addEventListener('DOMContentLoaded', function () {
    initNavbar();
    initFooterYear();
    initLightboxKeys();
    initContactForm();
});
