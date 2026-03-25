<!--
  LOCATION MODAL — frontend/layout/location_modal.php
  ─────────────────────────────────────────────────────
  Shown automatically on first visit (no location cookie).
  User can:
    1. Allow browser geolocation (auto-detect)
    2. Type an Indian city / pin with Google Places Autocomplete
    3. Skip — browse without location (limited UX)
-->
<div
  class="modal fade"
  id="locationModal"
  tabindex="-1"
  role="dialog"
  aria-labelledby="locationModalTitle"
  aria-describedby="locationModalDesc">

  <div class="modal-dialog modal-dialog-centered cnd-location-modal-dialog" role="document">
    <div class="modal-content cnd-modal-content">

      <!-- Header -->
      <div class="modal-header border-0 pb-0">
        <div>
          <h2 class="modal-title cnd-modal-title" id="locationModalTitle">
            <i class="bi bi-geo-alt-fill text-primary me-2" aria-hidden="true"></i>
            Find Classes Near You
          </h2>
          <p class="text-muted small mb-0" id="locationModalDesc">
            Share your location to discover the best classes in your neighbourhood.
          </p>
        </div>
        <!-- Only show close ✕ if location is already known -->
        <button type="button"
          class="btn-close"
          data-bs-dismiss="modal"
          id="locationModalClose"
          aria-label="Close location modal"></button>
      </div>

      <!-- Body -->
      <div class="modal-body pt-3">

        <!-- ── Option 1: Auto-detect ── -->
        <button
          class="btn cnd-location-auto-btn w-100 mb-3"
          type="button"
          id="useDeviceLocationBtn"
          aria-label="Detect my location automatically">
          <div class="d-flex align-items-center gap-3">
            <span class="cnd-location-auto-icon" aria-hidden="true">
              <i class="bi bi-crosshair2"></i>
            </span>
            <div class="text-start">
              <div class="fw-600">Use my current location</div>
              <div class="small text-muted">Quick & automatic — powered by GPS</div>
            </div>
            <!-- Loading spinner shown while fetching -->
            <span class="spinner-border spinner-border-sm ms-auto d-none" id="geoSpinner"
                  role="status" aria-label="Detecting location…"></span>
          </div>
        </button>

        <!-- ── Divider ── -->
        <div class="cnd-or-divider" aria-hidden="true">
          <span>or search manually</span>
        </div>

        <!-- ── Option 2: Manual Entry with Google Places ── -->
        <div class="mt-3">
          <label for="locationSearchInput" class="form-label fw-500">
            City, area, or pin code
          </label>
          <div class="input-group cnd-location-input-group">
            <span class="input-group-text" aria-hidden="true">
              <i class="bi bi-search"></i>
            </span>
            <!--
              id="locationSearchInput" is wired to Google Places Autocomplete in app.js.
              autocomplete="off" disables browser's own suggestions so Places takes over.
            -->
            <input
              type="text"
              class="form-control"
              id="locationSearchInput"
              name="location"
              placeholder="e.g. Banjara Hills, Hyderabad"
              autocomplete="off"
              aria-label="Search for your location"
              aria-autocomplete="list"
              aria-controls="locationSuggestions">
          </div>

          <!-- Suggestions dropdown (populated by Places SDK or JS) -->
          <ul
            class="list-group cnd-location-suggestions mt-1 d-none"
            id="locationSuggestions"
            role="listbox"
            aria-label="Location suggestions">
          </ul>
        </div>

        <!-- ── Geolocation permission denied ── -->
        <div
          class="alert alert-secondary mt-3 d-none"
          id="geoPermissionDenied"
          role="alert">
          <i class="bi bi-exclamation-triangle me-2" aria-hidden="true"></i>
          Location access was denied by your browser. Please type your city above.
        </div>

        <!-- ── No classes in area warning ── -->
        <div
          class="alert alert-warning cnd-no-classes-alert mt-3 d-none"
          id="noClassesAlert"
          role="alert"
          aria-live="polite">
          <i class="bi bi-info-circle-fill me-2" aria-hidden="true"></i>
          <strong>We're not in your area yet</strong> — but you can still browse! Try a larger city nearby.
        </div>

      </div><!-- /.modal-body -->

      <!-- Footer -->
      <div class="modal-footer border-0 pt-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <button
          class="btn cnd-btn-primary"
          type="button"
          id="confirmLocationBtn"
          aria-label="Confirm selected location"
          disabled>
          <i class="bi bi-check-lg me-1" aria-hidden="true"></i>
          Confirm Location
        </button>
        <!-- Skip button — hides modal but site is still usable -->
        <button
          type="button"
          class="btn btn-link text-muted small p-0 cnd-skip-location"
          data-bs-dismiss="modal"
          aria-label="Skip location and browse all classes">
          Skip for now
        </button>
      </div>

    </div><!-- /.modal-content -->
  </div>
</div>
