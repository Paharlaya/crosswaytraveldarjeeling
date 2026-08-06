/* ============================================================
   CrossWay Darjeeling Travel — renders content from data/*.js
   Markup here matches what the old PHP loops produced, so the
   existing CSS applies unchanged.
   ============================================================ */

var PACKAGES = window.PACKAGES || [];
var GALLERY = window.GALLERY || [];
var SIGHTSEEING = window.SIGHTSEEING || [];

function emptyState(message) {
    return '<div class="data-empty"><i class="fas fa-folder-open"></i>' + esc(message) + '</div>';
}

/* ---- Package cards ------------------------------------------ */

/* Homepage "Featured Packages" — 3 cards, priced per person. */
function renderFeaturedPackages(host) {
    var featured = PACKAGES.filter(function (p) { return p.featured; }).slice(0, 3);
    if (!featured.length) featured = PACKAGES.slice(0, 3);
    if (!featured.length) {
        host.innerHTML = emptyState('No packages to show yet.');
        return;
    }

    host.innerHTML = featured.map(function (p) {
        return '' +
        '<div class="col-md-4">' +
            '<div class="package-card-modern">' +
                '<div class="image-wrapper">' +
                    '<img src="' + esc(p.image) + '" alt="' + esc(p.name) + '" loading="lazy" decoding="async">' +
                    (p.featured ? '<span class="badge-top-right"><i class="fas fa-star"></i> Featured</span>' : '') +
                    '<div class="price-overlay">' +
                        '<span class="price">' + formatPrice(p.price, p.priceUnit) + '</span>' +
                    '</div>' +
                '</div>' +
                '<div class="body-content">' +
                    '<h5>' + esc(p.name) + '</h5>' +
                    '<div class="meta">' +
                        '<span><i class="fas fa-clock"></i> ' + esc(p.duration) + '</span>' +
                        '<span><i class="fas fa-map-marker-alt"></i> ' + esc(p.destination) + '</span>' +
                    '</div>' +
                    '<p>' + esc(truncate(p.description, 70)) + '</p>' +
                    '<a href="package-detail.html?id=' + encodeURIComponent(p.id) + '" class="btn-view">' +
                        'View Details <i class="fas fa-arrow-right"></i></a>' +
                '</div>' +
            '</div>' +
        '</div>';
    }).join('');
}

/* Packages page — full grid, filterable. */
function renderPackagesGrid(host) {
    if (!PACKAGES.length) {
        host.innerHTML = emptyState('No packages to show yet.');
        return;
    }

    host.innerHTML = PACKAGES.map(function (p) {
        var inclusionsText = (p.inclusions || []).join(', ');
        return '' +
        '<div class="col-lg-4 col-md-6 mb-4 package-item"' +
             ' data-name="' + esc(String(p.name).toLowerCase()) + '"' +
             ' data-destination="' + esc(String(p.destination).toLowerCase()) + '">' +
            '<div class="package-card">' +
                '<div class="image-wrapper">' +
                    '<img src="' + esc(p.image) + '" alt="' + esc(p.name) + '" loading="lazy" decoding="async">' +
                    (p.featured ? '<span class="badge-top-right"><i class="fas fa-star"></i> Featured</span>' : '') +
                    '<div class="price-overlay">' +
                        '<span class="price">' + formatPrice(p.price, p.priceUnit) + '</span>' +
                    '</div>' +
                '</div>' +
                '<div class="body-content">' +
                    '<h5>' + esc(p.name) + '</h5>' +
                    '<div class="meta">' +
                        '<span class="badge-duration"><i class="fas fa-clock"></i> ' + esc(p.duration) + '</span>' +
                        (p.featured ? '<span class="badge-featured"><i class="fas fa-crown"></i> Featured</span>' : '') +
                    '</div>' +
                    '<p>' + esc(truncate(p.description, 100)) + '</p>' +
                    '<div class="destination">' +
                        '<i class="fas fa-map-marker-alt"></i> ' + esc(p.destination) +
                    '</div>' +
                    (inclusionsText ?
                    '<div class="inclusions">' +
                        '<strong><i class="fas fa-check-circle me-1" style="color: #1f6332;"></i> Inclusions:</strong>' +
                        '<p>' + esc(truncate(inclusionsText, 60)) + '</p>' +
                    '</div>' : '') +
                    '<div class="btn-group-buttons">' +
                        '<a href="package-detail.html?id=' + encodeURIComponent(p.id) + '" class="btn-detail">' +
                            '<i class="fas fa-info-circle"></i> View Details</a>' +
                        '<a href="' + esc(waLink("Hi! I'm interested in the " + p.name + ' package.')) + '"' +
                           ' target="_blank" rel="noopener" class="btn-book">' +
                            '<i class="fab fa-whatsapp"></i> Book Now</a>' +
                    '</div>' +
                '</div>' +
            '</div>' +
        '</div>';
    }).join('');
}

/* Destination dropdown, built from the data rather than hardcoded. */
function renderDestinationFilter(select) {
    var seen = {};
    var destinations = [];
    PACKAGES.forEach(function (p) {
        if (p.destination && !seen[p.destination]) {
            seen[p.destination] = true;
            destinations.push(p.destination);
        }
    });
    destinations.sort();

    select.innerHTML = '<option value="all">All Destinations</option>' +
        destinations.map(function (d) {
            return '<option value="' + esc(d) + '">' + esc(d) + '</option>';
        }).join('');
}

/* ---- Sightseeing cards -------------------------------------- */
function renderSightseeing(host) {
    if (!SIGHTSEEING.length) {
        host.innerHTML = emptyState('No destinations to show yet.');
        return;
    }

    host.innerHTML = SIGHTSEEING.map(function (item) {
        var points = (item.points || []).map(function (point, i) {
            return '' +
            '<div class="point-item">' +
                '<span class="point-icon">' + (i + 1) + '</span>' +
                '<span class="point-name">' + esc(point) + '</span>' +
            '</div>';
        }).join('');

        return '' +
        '<div class="col-md-4">' +
            '<div class="sightseeing-card">' +
                '<div class="card-header-custom" style="background: linear-gradient(135deg, ' +
                    esc(item.color) + ', ' + esc(item.color) + 'cc);">' +
                    '<span><i class="fas ' + esc(item.icon) + ' me-2"></i> ' + esc(item.title) + '</span>' +
                    '<span class="count-badge">' + esc(item.badge) + '</span>' +
                '</div>' +
                points +
            '</div>' +
        '</div>';
    }).join('');
}

/* ---- Gallery ------------------------------------------------- */

/* Homepage strip — first 4 images. */
function renderGalleryStrip(host) {
    var items = GALLERY.slice(0, 4);
    setGalleryData(items);
    if (!items.length) {
        host.innerHTML = emptyState('No photos to show yet.');
        return;
    }

    host.innerHTML = items.map(function (image, index) {
        return '' +
        '<div class="gallery-item-modern" data-index="' + index + '" onclick="openLightbox(' + index + ')">' +
            '<img src="' + esc(image.image_path) + '" alt="' + esc(image.title) + '" loading="lazy" decoding="async">' +
            '<div class="overlay">' +
                '<div>' +
                    '<h6>' + esc(image.title) + '</h6>' +
                    '<small>' + esc(image.category || 'Himalayan Beauty') + '</small>' +
                '</div>' +
                '<div class="icon-expand"><i class="fas fa-expand"></i></div>' +
            '</div>' +
        '</div>';
    }).join('');
}

/* Gallery page — every image, filterable. */
function renderGalleryGrid(host) {
    setGalleryData(GALLERY);
    if (!GALLERY.length) {
        host.innerHTML = emptyState('No photos to show yet.');
        return;
    }

    host.innerHTML = GALLERY.map(function (image, index) {
        return '' +
        '<div class="gallery-item-modern"' +
             ' data-index="' + index + '"' +
             ' data-title="' + esc(String(image.title).toLowerCase()) + '"' +
             ' data-category="' + esc(String(image.category || '').toLowerCase()) + '"' +
             ' onclick="openLightbox(' + index + ')">' +
            '<img src="' + esc(image.image_path) + '" alt="' + esc(image.title) + '" loading="lazy" decoding="async">' +
            '<div class="overlay">' +
                '<span class="category-badge">' + esc(image.category || 'Himalayan Beauty') + '</span>' +
                '<h6>' + esc(image.title) + '</h6>' +
                (image.description ?
                    '<p class="description">' + esc(truncate(image.description, 50)) + '</p>' : '') +
                '<div class="icon-expand"><i class="fas fa-expand"></i></div>' +
            '</div>' +
        '</div>';
    }).join('');
}

function renderCategoryFilter(select) {
    var seen = {};
    var categories = [];
    GALLERY.forEach(function (image) {
        if (image.category && !seen[image.category]) {
            seen[image.category] = true;
            categories.push(image.category);
        }
    });
    categories.sort();

    select.innerHTML = '<option value="all">All Categories</option>' +
        categories.map(function (c) {
            return '<option value="' + esc(c.toLowerCase()) + '">' + esc(c) + '</option>';
        }).join('');
}

/* ---- Package detail ----------------------------------------- */
function renderPackageDetail() {
    var host = document.getElementById('packageDetail');
    if (!host) return;

    var id = new URLSearchParams(window.location.search).get('id');
    var pkg = PACKAGES.filter(function (p) { return p.id === id; })[0];

    /* Replaces the old server-side header('Location: packages.php') with a
       message the visitor can actually act on. */
    if (!pkg) {
        host.innerHTML =
            '<div class="text-center py-5">' +
                '<i class="fas fa-map-signs fa-3x text-muted mb-3"></i>' +
                '<h3 style="color: #013565; font-weight: 700;">Package not found</h3>' +
                '<p class="text-muted">That package may have been renamed or removed.</p>' +
                '<a href="packages.html" class="btn btn-primary" style="border-radius: 50px; padding: 12px 35px; background: linear-gradient(135deg, #1f6332, #2e7d32); border: none;">' +
                    '<i class="fas fa-box me-2"></i> Browse all packages</a>' +
            '</div>';
        var crumb = document.getElementById('detailBreadcrumb');
        if (crumb) crumb.textContent = 'Not found';
        return;
    }

    document.title = 'CrossWay Travel - ' + pkg.name;
    var breadcrumb = document.getElementById('detailBreadcrumb');
    if (breadcrumb) breadcrumb.textContent = pkg.name;

    var list = function (items, icon, color) {
        return (items || []).map(function (item) {
            return '<li><i class="fas ' + icon + '"' + (color ? ' style="color: ' + color + ';"' : '') + '></i> ' +
                esc(item) + '</li>';
        }).join('');
    };

    var bookText = "Hi! I'm interested in the " + pkg.name + ' package.';

    host.innerHTML = '' +
    '<div class="package-detail-card">' +
        (pkg.image ? '<img src="' + esc(pkg.image) + '" alt="' + esc(pkg.name) + '" class="detail-image" decoding="async">' : '') +
        '<div class="detail-content">' +
            '<div class="row">' +
                '<div class="col-md-8">' +
                    '<h2>' + esc(pkg.name) + '</h2>' +
                    (pkg.featured ? '<span class="badge-featured-lg"><i class="fas fa-star"></i> Featured Package</span>' : '') +
                '</div>' +
                '<div class="col-md-4 text-md-end">' +
                    '<div class="detail-price">' + formatPrice(pkg.price, pkg.priceUnit) + '</div>' +
                '</div>' +
            '</div>' +

            '<div class="detail-meta">' +
                '<div class="meta-item"><i class="fas fa-clock"></i>' +
                    '<span>Duration: ' + esc(pkg.duration) + '</span></div>' +
                '<div class="meta-item"><i class="fas fa-map-marker-alt"></i>' +
                    '<span>Destination: ' + esc(pkg.destination) + '</span></div>' +
                (pkg.groupSize ?
                '<div class="meta-item"><i class="fas fa-users"></i>' +
                    '<span>Group Size: ' + esc(pkg.groupSize) + '</span></div>' : '') +
                (pkg.tourType ?
                '<div class="meta-item"><i class="fas fa-tag"></i>' +
                    '<span>Type: ' + esc(pkg.tourType) + '</span></div>' : '') +
                (pkg.featured ?
                '<div class="meta-item"><i class="fas fa-crown" style="color: #f9a825;"></i>' +
                    '<span style="color: #f57c00;">Featured Package</span></div>' : '') +
            '</div>' +

            '<div class="detail-description">' +
                '<h5><i class="fas fa-align-left me-2" style="color: #013565;"></i> Package Description</h5>' +
                '<p>' + esc(pkg.description) + '</p>' +
            '</div>' +

            (pkg.itinerary && pkg.itinerary.length ?
            '<div class="detail-section" style="border-left: 4px solid #013565;">' +
                '<h5><i class="fas fa-route me-2" style="color: #013565;"></i> Itinerary</h5>' +
                '<ul>' + list(pkg.itinerary, 'fa-calendar-day', '#013565') + '</ul>' +
            '</div>' : '') +

            (pkg.inclusions && pkg.inclusions.length ?
            '<div class="detail-section inclusions">' +
                '<h5><i class="fas fa-check-circle me-2" style="color: #1f6332;"></i> What\'s Included</h5>' +
                '<ul>' + list(pkg.inclusions, 'fa-check-circle') + '</ul>' +
            '</div>' : '') +

            (pkg.exclusions && pkg.exclusions.length ?
            '<div class="detail-section exclusions">' +
                '<h5><i class="fas fa-times-circle me-2" style="color: #dc3545;"></i> What\'s Not Included</h5>' +
                '<ul>' + list(pkg.exclusions, 'fa-times-circle') + '</ul>' +
            '</div>' : '') +

            '<div class="detail-actions">' +
                '<a href="' + esc(waLink(bookText)) + '" target="_blank" rel="noopener" class="btn-book-now">' +
                    '<i class="fab fa-whatsapp"></i> Book This Package Now</a>' +
                '<a href="packages.html" class="btn-back">' +
                    '<i class="fas fa-arrow-left"></i> Back to Packages</a>' +
            '</div>' +
        '</div>' +
    '</div>' +
    renderRelatedPackages(pkg);
}

/* Same intent as the old getRelatedPackages(): same destination first,
   then top up with other featured packages. */
function renderRelatedPackages(pkg) {
    var related = PACKAGES.filter(function (p) {
        return p.id !== pkg.id && p.destination === pkg.destination;
    });

    if (related.length < 3) {
        PACKAGES.forEach(function (p) {
            if (related.length >= 3) return;
            var alreadyIn = related.some(function (r) { return r.id === p.id; });
            if (p.id !== pkg.id && !alreadyIn && p.featured) related.push(p);
        });
    }
    related = related.slice(0, 3);
    if (!related.length) return '';

    return '' +
    '<div class="mt-5">' +
        '<h3 class="mb-4" style="color: #013565; font-weight: 700;">' +
            '<i class="fas fa-suitcase me-2"></i> You Might Also Like</h3>' +
        '<div class="row">' +
            related.map(function (p) {
                return '' +
                '<div class="col-lg-4 col-md-6 mb-4">' +
                    '<div class="package-card">' +
                        '<div class="image-wrapper">' +
                            '<img src="' + esc(p.image) + '" alt="' + esc(p.name) + '" loading="lazy" decoding="async">' +
                            '<div class="price-overlay">' +
                                '<span class="price">' + formatPrice(p.price, p.priceUnit) + '</span>' +
                            '</div>' +
                        '</div>' +
                        '<div class="body-content">' +
                            '<h6>' + esc(p.name) + '</h6>' +
                            '<p>' + esc(truncate(p.description, 60)) + '</p>' +
                            '<a href="package-detail.html?id=' + encodeURIComponent(p.id) + '"' +
                               ' class="btn btn-sm btn-outline-primary"' +
                               ' style="border-radius: 50px; border-color: #013565; color: #013565; width: 100%;">' +
                                'View Details <i class="fas fa-arrow-right ms-1"></i></a>' +
                        '</div>' +
                    '</div>' +
                '</div>';
            }).join('') +
        '</div>' +
    '</div>';
}

/* ---- Filters (called from inline handlers in the markup) ----- */
function filterPackages() {
    var term = (document.getElementById('searchPackages') || {}).value || '';
    var destination = (document.getElementById('filterDestination') || {}).value || 'all';
    term = term.toLowerCase();

    var items = document.querySelectorAll('.package-item');
    var visible = 0;

    Array.prototype.forEach.call(items, function (item) {
        var name = item.getAttribute('data-name') || '';
        var dest = item.getAttribute('data-destination') || '';
        var matchesSearch = name.indexOf(term) !== -1 || dest.indexOf(term) !== -1;
        var matchesDest = destination === 'all' || dest === destination.toLowerCase();

        if (matchesSearch && matchesDest) {
            item.style.display = '';
            visible++;
        } else {
            item.style.display = 'none';
        }
    });

    var noResults = document.getElementById('noResults');
    if (noResults) noResults.style.display = visible === 0 ? 'block' : 'none';
}

function filterGallery() {
    var term = (document.getElementById('searchGallery') || {}).value || '';
    var category = (document.getElementById('filterCategory') || {}).value || 'all';
    term = term.toLowerCase();

    var items = document.querySelectorAll('#galleryGrid .gallery-item-modern');
    var visible = 0;

    Array.prototype.forEach.call(items, function (item) {
        var title = item.getAttribute('data-title') || '';
        var cat = item.getAttribute('data-category') || '';
        var matchesSearch = title.indexOf(term) !== -1 || cat.indexOf(term) !== -1;
        var matchesCategory = category === 'all' || cat === category.toLowerCase();

        if (matchesSearch && matchesCategory) {
            item.style.display = '';
            visible++;
        } else {
            item.style.display = 'none';
        }
    });

    var noResults = document.getElementById('noResults');
    if (noResults) noResults.style.display = visible === 0 ? 'block' : 'none';
}

function resetFilters() {
    ['searchPackages', 'searchGallery'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.value = '';
    });
    ['filterDestination', 'filterCategory'].forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.value = 'all';
    });
    if (document.getElementById('packagesGrid')) filterPackages();
    if (document.getElementById('galleryGrid')) filterGallery();
}

/* ---- Boot --------------------------------------------------- */
document.addEventListener('DOMContentLoaded', function () {
    var featured = document.getElementById('featuredPackages');
    if (featured) renderFeaturedPackages(featured);

    var sightseeing = document.getElementById('sightseeingCards');
    if (sightseeing) renderSightseeing(sightseeing);

    var strip = document.getElementById('galleryStrip');
    if (strip) renderGalleryStrip(strip);

    var grid = document.getElementById('packagesGrid');
    if (grid) {
        renderPackagesGrid(grid);
        var destSelect = document.getElementById('filterDestination');
        if (destSelect) renderDestinationFilter(destSelect);
    }

    var galleryGrid = document.getElementById('galleryGrid');
    if (galleryGrid) {
        renderGalleryGrid(galleryGrid);
        var catSelect = document.getElementById('filterCategory');
        if (catSelect) renderCategoryFilter(catSelect);
        var noResults = document.getElementById('noResults');
        if (noResults) noResults.style.display = 'none';
    }

    renderPackageDetail();
});
