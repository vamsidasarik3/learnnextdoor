/**
 * theme.js — Class Next Door, Theme v2
 * ─────────────────────────────────────────────────────────────
 * Handles: Navbar scroll shadow · Swiper carousel init + AJAX
 *          Home page tab/category AJAX · Skeleton loaders
 *          Category dropdown on mobile · "Not serving" alert
 *          Hero location display sync · Quick category links
 * ─────────────────────────────────────────────────────────────
 * Dependencies: Bootstrap 5 (global), Swiper 11 (global), app.js
 * No jQuery — vanilla ES5-compatible JS.
 * ─────────────────────────────────────────────────────────────
 */
(function () {
    'use strict';

    /* ────────────────────────────────────────────────────────────
       CONSTANTS
    ──────────────────────────────────────────────────────────── */
    var BASE_URL = (document.body.dataset.baseUrl || (window.location.origin + '/')).replace(/\/$/, '') + '/';
    var API_NEARBY = BASE_URL + 'api/listings/nearby';
    var API_CAROUSEL = BASE_URL + 'api/listings/carousel';
    var DEFAULT_RADIUS = 25;
    var SLIDE_INTERVAL = 5000; // ms

    /* ────────────────────────────────────────────────────────────
       1. NAVBAR — add shadow on scroll
    ──────────────────────────────────────────────────────────── */
    var navbar = document.getElementById('cnd-navbar');
    if (navbar) {
        var onScroll = function () {
            navbar.classList.toggle('cnd-scrolled', window.scrollY > 10);
        };
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll(); // init
    }

    /* ────────────────────────────────────────────────────────────
       2. HERO LOCATION DISPLAY — sync from cookie/session
    ──────────────────────────────────────────────────────────── */
    var heroLocDisplay = document.getElementById('hero-location-display');
    if (heroLocDisplay) {
        var locName = getCookie('cnd_location_name');
        if (locName) {
            heroLocDisplay.textContent = decodeURIComponent(locName).split(',')[0];
        }
    }

    /* ────────────────────────────────────────────────────────────
       3. SWIPER FEATURED CAROUSEL
       ─ Uses Swiper 11 (loaded from CDN in base.php)
       ─ Progress bar driven by autoplay timeLeft events
       ─ Keyboard + touch + accessibility built in
    ──────────────────────────────────────────────────────────── */
    var swiperEl = document.getElementById('featuredSwiper');
    var progressBar = document.getElementById('swiperProgressBar');
    var carouselOuter = document.getElementById('carouselOuter');
    var swiperInst = null;

    if (swiperEl && window.Swiper) {
        swiperInst = new Swiper(swiperEl, {
            // Layout
            loop: true,
            speed: 700,
            slidesPerView: 1,
            centeredSlides: true,
            grabCursor: true,

            // Auto-play
            autoplay: {
                delay: SLIDE_INTERVAL,
                disableOnInteraction: false,
                pauseOnMouseEnter: true,
            },

            // Navigation arrows (our custom buttons)
            navigation: {
                prevEl: '.cnd-swiper-arrow-prev',
                nextEl: '.cnd-swiper-arrow-next',
            },

            // Pagination bullets
            pagination: {
                el: '.cnd-swiper-pagination',
                clickable: true,
                renderBullet: function (index, className) {
                    return '<span class="' + className + '" role="tab" aria-label="Go to slide ' + (index + 1) + '"></span>';
                },
            },

            // Keyboard
            keyboard: { enabled: true, onlyInViewport: true },

            // A11y
            a11y: {
                prevSlideMessage: 'Previous class',
                nextSlideMessage: 'Next class',
            },

            // Transition effect — fade looks cinematic
            effect: 'fade',
            fadeEffect: { crossFade: true },

            // Events ── progress bar
            on: {
                autoplayTimeLeft: function (s, time, progress) {
                    if (!progressBar) return;
                    // progress: 1 → 0 (1 = start of interval, 0 = end)
                    var pct = (1 - progress) * 100;
                    progressBar.style.width = pct + '%';
                    progressBar.style.transitionDuration = '100ms';
                },
                slideChangeTransitionEnd: function () {
                    // Reset progress bar on slide change
                    if (progressBar) {
                        progressBar.style.transitionDuration = '0ms';
                        progressBar.style.width = '0%';
                    }
                },
            },
        });
    }

    /* ────────────────────────────────────────────────────────────
       3b. CAROUSEL AJAX REFRESH on location change
       When the user sets a new location, re-fetch slides via the
       /api/listings/carousel endpoint and rebuild the Swiper.
    ──────────────────────────────────────────────────────────── */
    window.addEventListener('cnd:location-changed', function (e) {
        if (!e.detail) return;
        var loc = e.detail;

        // Refresh the carousel for the new location
        refreshCarousel(loc.state || '', loc.lat || null, loc.lng || null);
    });

    function refreshCarousel(state, lat, lng) {
        if (!carouselOuter) return;
        var url = API_CAROUSEL + '?radius=' + DEFAULT_RADIUS;
        if (state) url += '&state=' + encodeURIComponent(state);
        if (lat) url += '&lat=' + lat;
        if (lng) url += '&lng=' + lng;

        var xhr = new XMLHttpRequest();
        xhr.open('GET', url, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;
            try {
                var data = JSON.parse(xhr.responseText);
                if (data.success && data.slides && data.slides.length > 0) {
                    rebuildSwiper(data.slides);
                } else {
                    showCarouselEmpty();
                }
            } catch (e) { /* keep existing slides */ }
        };
        xhr.send();
    }

    function rebuildSwiper(slides) {
        if (!swiperEl) return;

        // Destroy existing Swiper instance
        if (swiperInst) { swiperInst.destroy(true, true); swiperInst = null; }

        // Build new slide HTML
        var wrapperEl = swiperEl.querySelector('.swiper-wrapper');
        if (!wrapperEl) return;

        var html = '';
        slides.forEach(function (fl, fi) {
            var imgUrl = fl.cover_image
                ? BASE_URL + 'uploads/listings/' + fl.cover_image
                : BASE_URL + 'assets/frontend/img/class-placeholder.jpg';
            var detailUrl = BASE_URL + 'classes/' + (fl.listing_id || fl.id || '');
            var rating = parseFloat(fl.avg_rating || 0);
            var reviews = parseInt(fl.review_count || 0, 10);
            var price = parseFloat(fl.price || 0);
            var dist = fl.distance_km != null ? parseFloat(fl.distance_km) : null;
            var source = fl.source || 'algo';
            var type = esc(fl.type ? (fl.type.charAt(0).toUpperCase() + fl.type.slice(1)) : 'Class');
            var total = slides.length;

            // Stars
            var stars = '';
            if (rating >= 1) {
                var r = Math.round(rating);
                for (var s = 1; s <= 5; s++) {
                    stars += s <= r
                        ? '<i class="bi bi-star-fill" aria-hidden="true"></i>'
                        : '<i class="bi bi-star" aria-hidden="true"></i>';
                }
            }

            var badgeFeatured = source === 'admin'
                ? '<span class="cnd-swiper-badge-featured"><i class="bi bi-patch-check-fill" aria-hidden="true"></i> Featured</span>'
                : '<span class="cnd-swiper-badge-trending"><i class="bi bi-fire" aria-hidden="true"></i> Trending</span>';

            var metaHtml = '';
            if (fl.category_name) {
                metaHtml += '<span class="cnd-swiper-meta-cat"><i class="bi bi-tag-fill" aria-hidden="true"></i> ' + esc(fl.category_name) + '</span>';
            }
            if (rating >= 1) {
                metaHtml += '<span class="cnd-swiper-meta-rating" aria-label="Rated ' + rating.toFixed(1) + ' out of 5">'
                    + stars + '<strong>' + rating.toFixed(1) + '</strong><span class="opacity-75">(' + reviews + ')</span></span>';
            }
            if (price > 0) {
                metaHtml += '<span class="cnd-swiper-meta-price"><i class="bi bi-currency-rupee" aria-hidden="true"></i>'
                    + Number(price).toLocaleString('en-IN')
                    + '<span class="opacity-75"> / session</span></span>';
            } else {
                metaHtml += '<span class="cnd-swiper-meta-free">Free</span>';
            }
            if (dist !== null) {
                metaHtml += '<span class="cnd-swiper-meta-dist"><i class="bi bi-geo-alt-fill" aria-hidden="true"></i> ' + dist.toFixed(1) + ' km away</span>';
            }

            html +=
                '<div class="swiper-slide cnd-swiper-slide" role="group" aria-roledescription="slide" '
                + 'aria-label="Slide ' + (fi + 1) + ' of ' + total + ': ' + esc(fl.title || '') + '">'
                + '<div class="cnd-swiper-bg" style="background-image:url(\'' + imgUrl + '\')" aria-hidden="true"></div>'
                + '<div class="cnd-swiper-overlay" aria-hidden="true"></div>'
                + '<div class="cnd-swiper-content">'
                + '<div class="cnd-swiper-badges">'
                + '<span class="cnd-swiper-badge-type">' + type + '</span>'
                + badgeFeatured
                + '</div>'
                + '<h3 class="cnd-swiper-title">' + esc(fl.title || '') + '</h3>'
                + '<div class="cnd-swiper-meta">' + metaHtml + '</div>'
                + '<a href="' + detailUrl + '" class="btn cnd-swiper-cta" '
                + 'aria-label="View details for ' + esc(fl.title || '') + '">'
                + 'View Details <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>'
                + '</a>'
                + '</div>'
                + '<div class="cnd-swiper-num" aria-hidden="true">' + (fi + 1) + ' / ' + total + '</div>'
                + '</div>';
        });

        wrapperEl.innerHTML = html;

        // Re-init Swiper
        swiperInst = new Swiper(swiperEl, {
            loop: true,
            speed: 700,
            slidesPerView: 1,
            centeredSlides: true,
            grabCursor: true,
            autoplay: { delay: SLIDE_INTERVAL, disableOnInteraction: false, pauseOnMouseEnter: true },
            navigation: { prevEl: '.cnd-swiper-arrow-prev', nextEl: '.cnd-swiper-arrow-next' },
            pagination: { el: '.cnd-swiper-pagination', clickable: true },
            keyboard: { enabled: true, onlyInViewport: true },
            effect: 'fade',
            fadeEffect: { crossFade: true },
            on: {
                autoplayTimeLeft: function (s, time, progress) {
                    if (progressBar) {
                        progressBar.style.width = ((1 - progress) * 100) + '%';
                        progressBar.style.transitionDuration = '100ms';
                    }
                },
            },
        });

        // Show the outer wrapper in case it was hidden
        if (carouselOuter) carouselOuter.style.display = '';
        var emptyEl = document.getElementById('carouselEmpty');
        if (emptyEl) emptyEl.style.display = 'none';
    }

    function showCarouselEmpty() {
        if (carouselOuter) carouselOuter.style.display = 'none';
        var emptyEl = document.getElementById('carouselEmpty');
        if (emptyEl) emptyEl.style.display = '';
    }

    /* ────────────────────────────────────────────────────────────
       4. HOME PAGE — AJAX listing refresh on tab / category change
       Only active when #home-listings-area exists (home.php)
    ──────────────────────────────────────────────────────────── */
    var homeListingsArea = document.getElementById('home-listings-area');

    if (homeListingsArea) {
        var homeState = {
            type: 'regular',
            category: null,
            sort: 'relevancy',
            lat: null,
            lng: null,
            radius: DEFAULT_RADIUS
        };

        // Read persisted location
        (function loadLoc() {
            var lat = getCookie('cnd_lat'), lng = getCookie('cnd_lng');
            if (lat && lng) { homeState.lat = parseFloat(lat); homeState.lng = parseFloat(lng); }
        })();

        // ── Tab clicks ─────────────────────────────────────────
        document.querySelectorAll('#classTypeTabs .cnd-type-tab').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.cnd-type-tab[data-type]').forEach(function (b) {
                    var active = b.dataset.type === btn.dataset.type;
                    b.classList.toggle('active', active);
                    b.setAttribute('aria-selected', active ? 'true' : 'false');
                });
                homeState.type = btn.dataset.type || 'regular';
                homeState.category = null;
                resetCategoryBtns();
                homeAjaxFetch();
            });
        });

        // ── Category bubble clicks ──────────────────────────────
        document.querySelectorAll('.cnd-cats-section .cnd-cat-icon-pill[data-cat-id]').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                var catId = parseInt(this.dataset.catId, 10) || null;
                homeState.category = catId;
                document.querySelectorAll('.cnd-cat-icon-pill[data-cat-id]').forEach(function (l) {
                    l.classList.toggle('cnd-cat-active', parseInt(l.dataset.catId, 10) === catId);
                });
                homeAjaxFetch();
            });
        });

        // ── Home category dropdown (mobile) ────────────────────
        var catDropdown = document.getElementById('homeCatDropdown');
        if (catDropdown) {
            catDropdown.addEventListener('change', function () {
                homeState.category = this.value ? parseInt(this.value, 10) : null;
                homeAjaxFetch();
            });
        }

        // ── Sort select on home page ────────────────────────────
        var homeSort = document.getElementById('homeSort');
        if (homeSort) {
            homeSort.addEventListener('change', function () {
                homeState.sort = this.value;
                homeAjaxFetch();
            });
        }

        // ── Fetch + render ──────────────────────────────────────
        var _homeXhr = null;
        function homeAjaxFetch() {
            showSkeleton(homeListingsArea);
            if (_homeXhr) _homeXhr.abort();

            var url = API_NEARBY +
                '?type=' + encodeURIComponent(homeState.type) +
                '&sort=' + encodeURIComponent(homeState.sort) +
                '&radius=' + homeState.radius +
                '&limit=12';

            if (homeState.lat) url += '&lat=' + homeState.lat;
            if (homeState.lng) url += '&lng=' + homeState.lng;
            if (homeState.category) url += '&category=' + homeState.category;

            var xhr = new XMLHttpRequest();
            _homeXhr = xhr;
            xhr.open('GET', url, true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onreadystatechange = function () {
                if (xhr.readyState !== 4) return;
                try {
                    var data = JSON.parse(xhr.responseText);
                    if (data.success) {
                        if (!data.location_served || data.listings.length === 0) {
                            renderNotServing(homeListingsArea);
                        } else {
                            renderHomeCards(homeListingsArea, data.listings);
                        }
                    } else {
                        renderNotServing(homeListingsArea);
                    }
                } catch (e) {
                    renderAjaxError(homeListingsArea);
                }
            };
            xhr.onerror = function () { renderAjaxError(homeListingsArea); };
            xhr.send();
        }

        // ── Listen for location-changed event (from app.js) ─────
        window.addEventListener('cnd:location-changed', function (e) {
            if (e.detail && e.detail.lat) {
                homeState.lat = e.detail.lat;
                homeState.lng = e.detail.lng;
                homeAjaxFetch();
            }
        });

        function resetCategoryBtns() {
            document.querySelectorAll('.cnd-cat-icon-pill[data-cat-id]').forEach(function (l) {
                l.classList.remove('cnd-cat-active');
            });
        }
    }

    /* ────────────────────────────────────────────────────────────
       5. CATEGORY BUBBLE HOVER LIFT (home page non-AJAX click)
    ──────────────────────────────────────────────────────────── */
    document.querySelectorAll('.cnd-cat-icon-pill').forEach(function (pill) {
        pill.addEventListener('mouseenter', function () {
            this.querySelector('.cnd-cat-icon-bubble') &&
                (this.querySelector('.cnd-cat-icon-bubble').style.transform = 'scale(1.12)');
        });
        pill.addEventListener('mouseleave', function () {
            this.querySelector('.cnd-cat-icon-bubble') &&
                (this.querySelector('.cnd-cat-icon-bubble').style.transform = '');
        });
    });

    /* ────────────────────────────────────────────────────────────
       6. MOBILE CATEGORY ICONS → hidden on xs, dropdown shown
       Checks viewport on load + resize.
    ──────────────────────────────────────────────────────────── */
    var catRow = document.querySelector('.cnd-cats-row');
    var catDropWrap = document.getElementById('cnd-cat-dropdown-wrap');

    function toggleCatLayout() {
        if (!catRow && !catDropWrap) return;
        var isMobileSmall = window.innerWidth < 480;
        if (catRow) catRow.style.display = isMobileSmall ? 'none' : '';
        if (catDropWrap) catDropWrap.style.display = isMobileSmall ? 'block' : 'none';
    }

    window.addEventListener('resize', throttle(toggleCatLayout, 200));
    toggleCatLayout();

    /* ────────────────────────────────────────────────────────────
       7. RENDER HELPERS
    ──────────────────────────────────────────────────────────── */

    /** Skeleton loader — 4 ghost cards */
    function showSkeleton(container) {
        var html = '';
        for (var i = 0; i < 4; i++) {
            html +=
                '<div class="cnd-skeleton-card mb-3">' +
                '<div class="cnd-skeleton-img cnd-skeleton cnd-shimmer"></div>' +
                '<div class="cnd-skeleton-body">' +
                '<div class="cnd-skeleton-line cnd-skeleton cnd-shimmer w-100"></div>' +
                '<div class="cnd-skeleton-line cnd-skeleton-line-sm cnd-skeleton cnd-shimmer"></div>' +
                '<div class="cnd-skeleton-line cnd-skeleton-line-xs cnd-skeleton cnd-shimmer"></div>' +
                '</div>' +
                '</div>';
        }
        container.innerHTML = html;
    }

    /** "Sorry, we are currently not serving your selected location." */
    function renderNotServing(container) {
        container.innerHTML =
            '<div class="cnd-not-serving-alert" role="alert" aria-live="assertive">' +
            '<span class="cnd-alert-icon bi bi-exclamation-triangle-fill" aria-hidden="true"></span>' +
            '<div>' +
            '<strong>Not serving this location yet</strong>' +
            'Sorry, we are currently not serving your selected location. ' +
            'Try a different area or check back soon!' +
            '<br>' +
            '<button class="cnd-alert-action" ' +
            'data-bs-toggle="modal" data-bs-target="#locationModal" ' +
            'aria-label="Change location">' +
            '<i class="bi bi-geo-alt-fill" aria-hidden="true"></i> Change Location' +
            '</button>' +
            '</div>' +
            '</div>';
    }

    /** Generic AJAX error */
    function renderAjaxError(container) {
        container.innerHTML =
            '<div class="cnd-not-serving-alert" role="alert">' +
            '<span class="cnd-alert-icon bi bi-wifi-off" aria-hidden="true"></span>' +
            '<div>' +
            '<strong>Couldn\'t load listings</strong>' +
            'Please check your connection and try again.' +
            '<br>' +
            '<button class="cnd-alert-action" onclick="location.reload()">' +
            '<i class="bi bi-arrow-clockwise" aria-hidden="true"></i> Retry' +
            '</button>' +
            '</div>' +
            '</div>';
    }

    /** Render horizontal listing cards from AJAX data */
    function renderHomeCards(container, listings) {
        if (!listings || listings.length === 0) {
            renderNotServing(container);
            return;
        }
        var html = '<div class="d-flex flex-column gap-3" role="list">';
        listings.forEach(function (l) { html += buildCardHTML(l); });
        html += '</div>';
        container.innerHTML = html;
    }

    /** Build one horizontal card HTML string */
    function buildCardHTML(l) {
        var img = l.cover_image
            ? BASE_URL + 'uploads/listings/' + l.cover_image
            : BASE_URL + 'assets/frontend/img/class-placeholder.jpg';

        var stars = '';
        if (l.avg_rating && parseFloat(l.avg_rating) > 0) {
            var r = Math.round(parseFloat(l.avg_rating));
            for (var s = 1; s <= 5; s++) stars += s <= r ? '★' : '☆';
            stars =
                '<span class="cnd-lcard-stars" aria-label="Rating ' + l.avg_rating + ' out of 5">' + stars + '</span>' +
                '<span class="small text-muted ms-1">(' + (l.review_count || 0) + ')</span>';
        }

        var price = (l.price && parseFloat(l.price) > 0)
            ? '<span class="cnd-lcard-price ms-auto"><i class="bi bi-currency-rupee"></i>' + Number(l.price).toLocaleString('en-IN') + '</span>'
            : '<span class="cnd-lcard-free ms-auto">Free</span>';

        var badge = '<span class="cnd-badge-type-sm" aria-hidden="true">' + esc(l.type || 'class') + '</span>';
        var addr = l.address
            ? '<div class="cnd-lcard-address"><i class="bi bi-geo-alt-fill text-danger" aria-hidden="true"></i> <span>' + esc(l.address.substring(0, 45)) + '</span></div>'
            : '';
        var dist = (l.distance_km != null)
            ? '<span class="small text-muted"><i class="bi bi-geo-alt" aria-hidden="true"></i> ' + l.distance_km + ' km</span>'
            : '';
        var cat = l.category_name
            ? '<span class="small fw-600" style="color:var(--cnd-pink);">' + esc(l.category_name) + '</span>'
            : '';

        return (
            '<article class="cnd-lcard" role="listitem" aria-label="' + esc(l.title || 'Class') + '">' +
            '<a href="' + BASE_URL + 'classes/' + (l.listing_id || l.id || '#') + '" class="cnd-lcard-img" tabindex="-1" aria-hidden="true">' +
            '<img src="' + img + '" alt="" loading="lazy" width="120" height="120">' +
            badge +
            '</a>' +
            '<div class="cnd-lcard-body">' +
            '<div class="cnd-lcard-title"><a href="' + BASE_URL + 'classes/' + (l.listing_id || l.id || '#') + '">' + esc(l.title || 'Untitled') + '</a></div>' +
            addr +
            '<div class="cnd-lcard-meta">' + stars + cat + dist + price + '</div>' +
            '</div>' +
            '</article>'
        );
    }

    /* ────────────────────────────────────────────────────────────
       8. UTILITY
    ──────────────────────────────────────────────────────────── */
    function getCookie(name) {
        var m = document.cookie.match(new RegExp('(?:^|;\\s*)' + name + '=([^;]*)'));
        return m ? m[1] : null;
    }

    function esc(s) {
        return String(s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function throttle(fn, wait) {
        var last = 0;
        return function () {
            var now = Date.now();
            if (now - last >= wait) { last = now; fn(); }
        };
    }

    /* ────────────────────────────────────────────────────────────
       9. SMOOTH SCROLL to listings when hero "Find" is clicked
    ──────────────────────────────────────────────────────────── */
    var heroSearchBtn = document.getElementById('hero-search-btn');
    if (heroSearchBtn) {
        heroSearchBtn.addEventListener('click', function (e) {
            var target = document.getElementById('listings') || document.getElementById('class-types');
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    /* ────────────────────────────────────────────────────────────
       INIT COMPLETE
    ──────────────────────────────────────────────────────────── */

})();
