/**
 * Class Next Door — Frontend JavaScript  (app.js)
 * ─────────────────────────────────────────────────────────────
 * 1. Read Google Maps API key from body data attribute
 * 2. AUTO-DETECT location on first visit:
 *      a) Try browser Geolocation silently (no modal needed)
 *      b) Reverse-geocode with Google Geocoding API
 *      c) If denied/unavailable → auto-open location modal
 * 3. Location modal:
 *      - "Use my current location" button
 *      - Manual input with Google Places Autocomplete (India-restricted)
 *      - "No classes" pre-check against /api/listings/nearby
 * 4. Persist location: sessionStorage + POST /set-location (PHP session + cookies)
 * 5. Sync all navbar / hero location display elements
 * 6. Dispatch 'cnd:location-changed' event for AJAX listeners
 * ─────────────────────────────────────────────────────────────
 */
(function () {
  'use strict';

  /* ── CONFIG ──────────────────────────────────────────────── */
  var BODY = document.body;
  var BASE_URL = (BODY.dataset.baseUrl || (window.location.origin + '/')).trim();
  if (BASE_URL.slice(-1) !== '/') BASE_URL += '/';
  var GMAPS_KEY = BODY.dataset.googleMapsKey || window.CND_GMAPS_KEY || '';
  var NO_LOCATION = BODY.dataset.noLocation === '1';   // true = no cookie set yet
  var DEFAULT_RADIUS = 25; // km

  /* ── ELEMENT REFS ────────────────────────────────────────── */
  var navbar = document.getElementById('cnd-navbar');
  var useDeviceBtn = document.getElementById('useDeviceLocationBtn');
  var geoSpinner = document.getElementById('geoSpinner');
  var geoDeniedAlert = document.getElementById('geoPermissionDenied');
  var noClassesAlert = document.getElementById('noClassesAlert');
  var confirmBtn = document.getElementById('confirmLocationBtn');
  var locationInput = document.getElementById('locationSearchInput');
  var modalEl = document.getElementById('locationModal');

  /* ═══════════════════════════════════════════════════════════
     1. NAVBAR SCROLL SHADOW
  ═══════════════════════════════════════════════════════════ */
  if (navbar) {
    var onScroll = function () {
      navbar.classList.toggle('cnd-scrolled', window.scrollY > 10);
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  /* ═══════════════════════════════════════════════════════════
     2. LOCATION HELPERS
  ═══════════════════════════════════════════════════════════ */
  function getCookie(name) {
    var m = document.cookie.match(
      new RegExp('(^|;)\\s*' + name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '\\s*=\\s*([^;]+)')
    );
    return m ? decodeURIComponent(m[2]) : null;
  }

  function getSavedLocation() {
    try {
      var s = sessionStorage.getItem('cnd_location');
      if (s) return JSON.parse(s);
    } catch (e) { }
    var name = getCookie('cnd_location_name');
    var lat = getCookie('cnd_lat');
    var lng = getCookie('cnd_lng');
    var state = getCookie('cnd_state');
    return name ? { name: name, lat: parseFloat(lat) || null, lng: parseFloat(lng) || null, state: state || '' } : null;
  }

  function saveLocation(loc, cb) {
    try { sessionStorage.setItem('cnd_location', JSON.stringify(loc)); } catch (e) { }

    // ── localStorage flag — primary guard against re-showing the modal ──
    // This is the most reliable store: survives page reloads, unaffected
    // by cookie-domain/path issues, and readable before any XHR completes.
    try { localStorage.setItem('cnd_location_set', '1'); } catch (e) { }

    // Set cookies client-side immediately (7-day expiry)
    var exp = new Date(Date.now() + 7 * 864e5).toUTCString();
    document.cookie = 'cnd_location_name=' + encodeURIComponent(loc.name || '') + '; expires=' + exp + '; path=/';
    document.cookie = 'cnd_lat=' + encodeURIComponent(loc.lat || '') + '; expires=' + exp + '; path=/';
    document.cookie = 'cnd_lng=' + encodeURIComponent(loc.lng || '') + '; expires=' + exp + '; path=/';
    document.cookie = 'cnd_state=' + encodeURIComponent(loc.state || '') + '; expires=' + exp + '; path=/';

    // POST to server so PHP session is also set.
    // IMPORTANT: CSRF token must be in the POST body (CI4 requirement), not just a header.
    var csrf = window.CND_CSRF || {};
    var body = 'location_name=' + encodeURIComponent(loc.name || '')
      + '&lat=' + encodeURIComponent(loc.lat || '')
      + '&lng=' + encodeURIComponent(loc.lng || '')
      + '&state=' + encodeURIComponent(loc.state || '');
    if (csrf.name && csrf.token) {
      body += '&' + encodeURIComponent(csrf.name) + '=' + encodeURIComponent(csrf.token);
    }

    var xhr = new XMLHttpRequest();
    xhr.open('POST', BASE_URL + 'set-location', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onreadystatechange = function () {
      if (xhr.readyState === 4 && cb) cb(xhr.status === 200);
    };
    xhr.send(body);
  }

  function applyLocationToUI(loc) {
    if (!loc || !loc.name) return;

    var parts = loc.name.split(',');
    var short = parts[0].trim();
    // If name or short name is just coords, try to clean it
    var isCoord = function (s) { return /^-?\d+\.\d+$/.test(s.trim()); };

    if (isCoord(short)) {
      if (parts.length > 1 && !isCoord(parts[1])) {
        short = parts[1].trim();
      } else {
        short = 'Near you';
      }
    }

    var displayName = loc.name;
    if (isCoord(displayName.split(',')[0])) {
      displayName = short;
    }

    var els = [
      document.getElementById('desk-location-text'),
      document.getElementById('mob-location-text')
    ];
    els.forEach(function (el) { if (el) el.textContent = short; });

    var bar = document.querySelector('.cnd-location-label');
    if (bar) bar.textContent = displayName;

    var hero = document.getElementById('hero-location-display');
    if (hero) {
      hero.textContent = displayName;
      hero.style.color = '#2D2D3A';
    }

    if (locationInput) locationInput.value = (isCoord(loc.name.split(',')[0]) ? '' : loc.name);

    if (confirmBtn) {
      confirmBtn.removeAttribute('disabled');
      confirmBtn.dataset.location = JSON.stringify(loc);
    }
  }

  /* ═══════════════════════════════════════════════════════════
     3. REVERSE GEOCODING — Google Geocoding API
        Uses the same key as Places/Maps; no extra billing tier.
  ═══════════════════════════════════════════════════════════ */
  function reverseGeocode(lat, lng, cb) {
    // ────────────────────────────────────────────────────────────────
    // 1. Try Google Maps JS SDK Geocoder (preferred, uses loaded API)
    // ────────────────────────────────────────────────────────────────
    if (window.google && google.maps && google.maps.Geocoder) {
      var geocoder = new google.maps.Geocoder();
      geocoder.geocode({ location: { lat: lat, lng: lng } }, function (results, status) {
        if (status === 'OK' && results[0]) {
          var res = results[0];
          var city = '', state = '', country = '';
          (res.address_components || []).forEach(function (c) {
            if (c.types.indexOf('locality') !== -1) city = c.long_name;
            if (c.types.indexOf('administrative_area_level_1') !== -1) state = c.long_name;
            if (c.types.indexOf('country') !== -1) country = c.long_name;
          });
          var display = [city, state, country].filter(Boolean).join(', ');
          cb({ displayName: display || res.formatted_address, state: state, city: city });
        } else {
          _fallbackToHttpGeocode(lat, lng, cb);
        }
      });
      return;
    }

    _fallbackToHttpGeocode(lat, lng, cb);
  }

  function _fallbackToHttpGeocode(lat, lng, cb) {
    if (GMAPS_KEY) {
      // Google Geocoding API via HTTP
      var geoUrl = 'https://maps.googleapis.com/maps/api/geocode/json'
        + '?latlng=' + lat + ',' + lng
        + '&key=' + encodeURIComponent(GMAPS_KEY)
        + '&language=en&region=IN';

      var xhr = new XMLHttpRequest();
      xhr.open('GET', geoUrl, true);
      xhr.onreadystatechange = function () {
        if (xhr.readyState !== 4) return;
        if (xhr.status !== 200) { _nominatimFallback(lat, lng, cb); return; }
        try {
          var data = JSON.parse(xhr.responseText);
          if (data.status !== 'OK' || !data.results || !data.results.length) {
            _nominatimFallback(lat, lng, cb); return;
          }
          var result = data.results[0];
          var comps = result.address_components || [];
          var city = '', state = '', country = '';
          comps.forEach(function (c) {
            var t = c.types;
            if (t.indexOf('locality') !== -1) city = c.long_name;
            if (t.indexOf('administrative_area_level_1') !== -1) state = c.long_name;
            if (t.indexOf('country') !== -1) country = c.long_name;
          });
          var display = [city, state, country].filter(Boolean).join(', ');
          cb({ displayName: display || result.formatted_address, state: state, city: city });
        } catch (e) { _nominatimFallback(lat, lng, cb); }
      };
      xhr.send();
    } else {
      _nominatimFallback(lat, lng, cb);
    }
  }

  function _nominatimFallback(lat, lng, cb) {
    var url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat='
      + lat + '&lon=' + lng + '&addressdetails=1';
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.setRequestHeader('Accept', 'application/json');
    // Note: Nominatim technically requires a User-Agent, but browsers won't let you set it easily.
    xhr.onreadystatechange = function () {
      if (xhr.readyState !== 4) return;
      try {
        var d = JSON.parse(xhr.responseText);
        var a = d.address || {};
        var city = a.city || a.town || a.village || a.suburb || '';
        var state = a.state || '';
        var display = [city, state, a.country || ''].filter(Boolean).join(', ');
        cb({ displayName: display || d.display_name, state: state });
      } catch (e) { cb(null); }
    };
    xhr.send();
  }

  /* ═══════════════════════════════════════════════════════════
     4. NEARBY LISTINGS PRE-CHECK
  ═══════════════════════════════════════════════════════════ */
  function checkNearbyListings(lat, lng, cb) {
    var url = BASE_URL + 'api/listings/nearby'
      + '?type=regular&lat=' + lat + '&lng=' + lng
      + '&radius=' + DEFAULT_RADIUS + '&limit=1';
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onreadystatechange = function () {
      if (xhr.readyState !== 4) return;
      try {
        var data = JSON.parse(xhr.responseText);
        cb(data.location_served === true || (data.total && data.total > 0));
      } catch (e) { cb(true); } // fail-open
    };
    xhr.send();
  }

  /* ═══════════════════════════════════════════════════════════
     5. BROWSER GEOLOCATION HANDLER
  ═══════════════════════════════════════════════════════════ */
  function doGeolocate(opts) {
    /**
     * opts.silent      — true: no modal spinner, no denied alert (auto-detect on load)
     * opts.onSuccess   — callback(loc)
     * opts.onDenied    — callback()
     */
    opts = opts || {};
    if (!navigator.geolocation) { if (opts.onDenied) opts.onDenied(); return; }

    if (!opts.silent) setGeoLoading(true);

    navigator.geolocation.getCurrentPosition(
      function (pos) {
        var lat = pos.coords.latitude;
        var lng = pos.coords.longitude;
        if (!opts.silent) setGeoLoading(false);

        reverseGeocode(lat, lng, function (geo) {
          var locName = geo ? geo.displayName : 'Your Location';
          var state = geo ? (geo.state || '') : '';
          var loc = { name: locName, lat: lat, lng: lng, state: state };
          if (opts.onSuccess) opts.onSuccess(loc);
        });
      },
      function (err) {
        if (!opts.silent) setGeoLoading(false);
        // Geolocation may be blocked on HTTP (requires HTTPS in production) — silently handle
        if (opts.onDenied) opts.onDenied();
      },
      { timeout: 10000, maximumAge: 300000 }
    );
  }

  function setGeoLoading(on) {
    if (geoSpinner) geoSpinner.classList.toggle('d-none', !on);
    if (useDeviceBtn) useDeviceBtn.disabled = on;
  }
  function showGeoDenied() {
    if (geoDeniedAlert) geoDeniedAlert.classList.remove('d-none');
    if (noClassesAlert) noClassesAlert.classList.add('d-none');
  }
  function showNoClasses() {
    if (noClassesAlert) noClassesAlert.classList.remove('d-none');
    if (confirmBtn) confirmBtn.setAttribute('disabled', true);
  }

  /* ═══════════════════════════════════════════════════════════
     6. "Use my current location" BUTTON (inside modal)
  ═══════════════════════════════════════════════════════════ */
  if (useDeviceBtn) {
    useDeviceBtn.addEventListener('click', function () {
      if (geoDeniedAlert) geoDeniedAlert.classList.add('d-none');
      if (noClassesAlert) noClassesAlert.classList.add('d-none');
      doGeolocate({
        silent: false,
        onSuccess: function (loc) {
          // Auto-confirm immediately — no need for user to click "Confirm" again
          applyLocationToUI(loc);
          _closeModal();
          _doConfirm(loc);
        },
        onDenied: showGeoDenied
      });
    });
  }

  /* ═══════════════════════════════════════════════════════════
     7. MANUAL INPUT — Google Places Autocomplete
  ═══════════════════════════════════════════════════════════ */
  function initGooglePlaces() {
    if (!locationInput || !GMAPS_KEY) return;
    if (window._cndPlacesReady) return;
    window._cndPlacesReady = true;

    if (window.google && window.google.maps && window.google.maps.places) {
      _attachAutocomplete();
    } else {
      // Load Maps JS SDK lazily
      window._cndInitPlaces = _attachAutocomplete;
      var script = document.createElement('script');
      script.src = 'https://maps.googleapis.com/maps/api/js'
        + '?key=' + encodeURIComponent(GMAPS_KEY)
        + '&libraries=places&callback=_cndInitPlaces&loading=async';
      script.async = true;
      script.defer = true;
      document.head.appendChild(script);
    }
  }

  function _attachAutocomplete() {
    if (!locationInput) return;
    try {
      var ac = new google.maps.places.Autocomplete(locationInput, {
        componentRestrictions: { country: 'IN' },
        fields: ['geometry', 'formatted_address', 'address_components'],
        types: ['geocode'],
      });
      ac.addListener('place_changed', function () {
        var place = ac.getPlace();
        if (!place.geometry || !place.geometry.location) return;

        var lat = place.geometry.location.lat();
        var lng = place.geometry.location.lng();
        var state = '';
        (place.address_components || []).forEach(function (c) {
          if (c.types.indexOf('administrative_area_level_1') !== -1) state = c.long_name;
        });

        var loc = { name: place.formatted_address, lat: lat, lng: lng, state: state };
        applyLocationToUI(loc);
        checkNearbyListings(lat, lng, function (served) {
          if (!served) showNoClasses();
          else if (noClassesAlert) noClassesAlert.classList.add('d-none');
        });
      });
    } catch (e) {
      console.warn('Places Autocomplete init failed:', e);
    }
  }

  // Wire Places init to modal open
  if (modalEl) {
    modalEl.addEventListener('shown.bs.modal', function () {
      initGooglePlaces();
      if (locationInput) locationInput.focus();
      // Note: pac-container stays in <body> — its z-index is set to 1060
      // in app.css so it appears above the Bootstrap modal overlay (1055).
    });
  }

  /* ═══════════════════════════════════════════════════════════
     8. CONFIRM LOCATION BUTTON
  ═══════════════════════════════════════════════════════════ */
  if (confirmBtn) {
    confirmBtn.addEventListener('click', function () {
      var locData = this.dataset.location;
      var loc = null;

      // ── Try to parse location from dataset (set by autocomplete / geo) ──
      if (locData) {
        try { loc = JSON.parse(locData); } catch (e) { loc = null; }
      }

      // ── Fallback: if user typed a city name but didn't pick from dropdown ──
      if (!loc && locationInput && locationInput.value.trim()) {
        loc = { name: locationInput.value.trim(), lat: null, lng: null, state: '' };
      }

      if (!loc || !loc.name) return; // nothing to save

      // ── Close the modal IMMEDIATELY — don't wait for XHR ──
      _closeModal();

      // ── Save & reload ──
      if (loc.lat && loc.lng) {
        // We have coordinates → check nearby listings then save
        checkNearbyListings(loc.lat, loc.lng, function (served) {
          // Whether served or not, still save and reload
          // (user confirmed, they know their area)
          _doConfirm(loc);
        });
      } else {
        // Free-text only → save as-is
        _doConfirm(loc);
      }
    });
  }

  function _closeModal() {
    if (!modalEl || !window.bootstrap) return;
    try {
      // getOrCreateInstance always returns a valid object (unlike getInstance which can be null)
      var bsModal = bootstrap.Modal.getOrCreateInstance(modalEl);
      bsModal.hide();
    } catch (e) {
      // Last-resort: manually hide via Bootstrap data API
      modalEl.classList.remove('show');
      modalEl.style.display = 'none';
      document.body.classList.remove('modal-open');
      var backdrop = document.querySelector('.modal-backdrop');
      if (backdrop) backdrop.remove();
    }
  }

  function _doConfirm(loc) {
    applyLocationToUI(loc);
    window.dispatchEvent(new CustomEvent('cnd:location-changed', { detail: loc }));

    var _reloading = false;
    function _doReload() {
      if (_reloading) return;
      _reloading = true;
      window.location.reload();
    }

    // Save to server + cookies + localStorage, then reload
    saveLocation(loc, function () {
      setTimeout(_doReload, 200);
    });

    // Safety net: reload after 1.5s even if XHR hangs or CSRF fails
    setTimeout(_doReload, 1500);
  }

  /* ═══════════════════════════════════════════════════════════
     9. AUTO-DETECT ON PAGE LOAD
        If no location cookie exists → try geolocation silently.
        On success: save & reload (no modal needed).
        On denied: open location modal so user can pick manually.
  ═══════════════════════════════════════════════════════════ */
  function openLocationModal() {
    if (!modalEl || !window.bootstrap) return;
    var bsModal = bootstrap.Modal.getOrCreateInstance(modalEl, {
      backdrop: 'static',   // don't dismiss by clicking outside
      keyboard: false       // don't dismiss with Esc
    });
    bsModal.show();
  }

  // Already has location? Just update UI and done.
  var _saved = getSavedLocation();
  if (_saved) {
    applyLocationToUI(_saved);
  }

  // ── Modal auto-open guard ───────────────────────────────────────
  // Do NOT open the modal if any of the following are true:
  //   1. localStorage flag says user has already picked a location
  //   2. Client-side cookie cnd_location_name is already set
  //   3. PHP server says NO_LOCATION = false (session/cookie readable server-side)
  //
  // The localStorage flag is the most reliable — it won't be affected
  // by cookie-domain issues, PHP session expiry, or CSRF failures.
  var _locFlag = false;
  try { _locFlag = !!localStorage.getItem('cnd_location_set'); } catch (e) { }
  var _locCookie = !!getCookie('cnd_location_name');

  var _shouldOpenModal = NO_LOCATION && !_locFlag && !_locCookie;

  if (_shouldOpenModal) {
    // Small delay so Bootstrap JS has fully initialized
    setTimeout(function () {
      // Try silent geolocation first
      doGeolocate({
        silent: true,
        onSuccess: function (loc) {
          // Got a fix — save it and reload (listings will now appear)
          saveLocation(loc, function () {
            window.dispatchEvent(new CustomEvent('cnd:location-changed', { detail: loc }));
            window.location.reload();
          });
        },
        onDenied: function () {
          // Couldn't get location — open modal so user can pick manually
          openLocationModal();
        }
      });
    }, 800);
  }

})();
