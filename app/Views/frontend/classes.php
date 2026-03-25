<?= $this->extend('frontend/layout/base') ?>
<?= $this->section('css') ?>
<style>
/* ── Browse Page Extras ─────────────────────────────── */
/* Local override for hero gradient to match image header */
.cnd-browse-hero {
  background: linear-gradient(90deg, #3F3590 0%, #FA9F5E 100%);
  padding: 3rem 0 4.5rem;
  position: relative;
}
.cnd-browse-hero::after {
  content: '';
  position: absolute;
  bottom: -1px; left: 0; right: 0; height: 60px;
  background: #fff;
  clip-path: ellipse(70% 100% at 50% 100%);
}
.cnd-browse-title {
  font-size: clamp(1.5rem,3.5vw,2.2rem);
  font-weight: 900;
  color: #fff;
  letter-spacing: -.5px;
  line-height: 1.2;
}
.cnd-browse-sub {
  color: rgba(255,255,255,.82);
  font-size: .95rem;
}

/* Type pill tabs ─── */
.cnd-type-strip {
  background: #fff;
  border-bottom: 1px solid var(--cnd-card-border);
  padding: 1rem 0;
  position: sticky;
  top: var(--cnd-navbar-h);
  z-index: 100;
}
.cnd-type-tabs { gap: .5rem; flex-wrap: nowrap; overflow-x: auto; }

/* Type tab pill buttons — matching design image */
.cnd-type-tab {
  border-radius: 50px !important;
  font-size: .82rem !important;
  font-weight: 700 !important;
  padding: .5rem 1.4rem !important;
  border: 1.5px solid #eee !important;
  background: #fff !important;
  color: var(--cnd-muted) !important;
  transition: all .24s ease !important;
  white-space: nowrap;
  box-shadow: 0 2px 8px rgba(0,0,0,.03) !important;
}
.cnd-type-tab:hover {
  border-color: var(--cnd-pink) !important;
  color: var(--cnd-pink) !important;
  background: #fff !important;
}
.cnd-type-tab.active {
  background: var(--cnd-primary) !important;
  border-color: var(--cnd-primary) !important;
  color: #fff !important;
  box-shadow: 0 5px 15px rgba(63, 53, 144, .3) !important;
}

/* Category filter row ─── */
.cnd-cat-filter-row {
  display: flex;
  gap: .6rem;
  flex-wrap: wrap;
  justify-content: center;
  padding: .4rem 0;
}

.cnd-cat-filter-btn {
  display: inline-flex;
  flex-direction: column;
  align-items: center;
  gap: .3rem;
  min-width: 64px;
  padding: .75rem .6rem .6rem;
  background: #fff;
  border: 1px solid #eee;
  border-radius: 12px;
  font-size: .62rem;
  font-weight: 700;
  color: #666;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
  transition: all var(--cnd-transition);
  white-space: nowrap;
  text-decoration: none;
  box-shadow: 0 3px 10px rgba(0,0,0,.03);
}
.cnd-cat-filter-btn i { font-size: 1.15rem; color: #888; margin-bottom: 2px; }
.cnd-cat-filter-btn:hover {
  border-color: var(--cnd-accent);
  color: var(--cnd-accent);
}
/* Orange "All" button - ONLY IF ACTIVE */
.cnd-cat-filter-btn.cat-all.active {
  background: var(--cnd-accent) !important;
  border-color: var(--cnd-accent) !important;
  color: #fff !important;
  min-width: 52px;
}
.cnd-cat-filter-btn.cat-all.active i { color: #fff !important; }

/* Default state for All button when not active */
.cnd-cat-filter-btn.cat-all {
  background: #fff !important;
  border-color: #eee !important;
  color: #666 !important;
  min-width: 52px;
}
.cnd-cat-filter-btn.cat-all i { color: #888 !important; }

/* Active category state */
.cnd-cat-filter-btn.active,
.cnd-cat-filter-btn[data-active="true"] {
  border-color: var(--cnd-primary) !important;
  color: var(--cnd-primary) !important;
  box-shadow: 0 5px 15px rgba(63, 53, 144, .12);
}
.cnd-cat-filter-btn.active i { color: var(--cnd-primary) !important; }

/* Listing card – horizontal strip */
.cnd-lcard {
  display: flex;
  background: #fff;
  border-radius: 16px;
  border: 1px solid #eee;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,0,0,.06);
  transition: transform .22s ease, box-shadow .22s ease;
}
.cnd-lcard:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 28px rgba(63,53,144,.14);
  border-color: rgba(119,120,246,.3);
}
.cnd-lcard-img {
  width: 120px;
  min-height: 120px;
  flex-shrink: 0;
  overflow: hidden;
  position: relative;
}
@media (max-width: 575.98px) {
  .cnd-lcard-img { width: 100px; min-height: 100px; }
}
.cnd-lcard-img img {
  width: 100%; height: 100%;
  object-fit: cover;
  transition: transform .4s ease;
}
.cnd-lcard:hover .cnd-lcard-img img { transform: scale(1.07); }

.cnd-badge-type-sm {
  position: absolute;
  top: 10px; left: 10px;
  background: var(--cnd-primary);
  color: #fff;
  font-size: .55rem;
  font-weight: 800;
  text-transform: uppercase;
  padding: .2rem .6rem;
  border-radius: 50px;
  z-index: 2;
  letter-spacing: .08em;
  box-shadow: 0 4px 10px rgba(0,0,0,.15);
}

.cnd-lcard-body {
  flex: 1;
  padding: .9rem 1.1rem .9rem 1rem;
  display: flex;
  flex-direction: column;
  justify-content: center;
}
.cnd-lcard-title {
  font-size: .95rem;
  font-weight: 700;
  color: #1a1a2e;
  margin-bottom: .18rem;
  line-height: 1.35;
}
@media (max-width: 575.98px) { .cnd-lcard-title { font-size: .88rem; } }

.cnd-lcard-title a { color: inherit; text-decoration: none; }
.cnd-lcard-title a:hover { color: var(--cnd-primary); }

.cnd-lcard-address {
  font-size: .74rem;
  color: var(--cnd-muted);
  margin-bottom: .35rem;
  display: flex; align-items: flex-start; gap: .22rem;
}
.cnd-lcard-address i { color: #e25; font-size: .8rem; flex-shrink:0; margin-top:1px; }

.cnd-day-pills { display: flex; flex-wrap: wrap; gap: .22rem; margin-bottom: .4rem; }
.cnd-day-pill {
  display: inline-block;
  background: var(--cnd-secondary);
  color: #fff;
  font-size: .63rem;
  font-weight: 700;
  padding: .15rem .52rem;
  border-radius: var(--cnd-radius-pill);
  letter-spacing: .02em;
}
.cnd-day-pill:nth-child(2) { background: var(--cnd-secondary); }
.cnd-day-pill:nth-child(3) { background: var(--cnd-accent); color: #fff; }
.cnd-day-pill:nth-child(4) { background: #2ECC71; }
.cnd-day-pill:nth-child(5) { background: #00C9FF; }

.cnd-lcard-stars {
  color: #F9A05E;
  font-size: .78rem;
  letter-spacing: .05em;
}
.cnd-lcard-meta {
  display: flex;
  align-items: center;
  gap: .6rem;
  flex-wrap: wrap;
  margin-top: .2rem;
}
.cnd-lcard-price {
  font-size: .92rem;
  font-weight: 800;
  color: var(--cnd-primary);
  margin-left: auto;
  white-space: nowrap;
}
.cnd-lcard-free { font-weight: 800; color: #2ECC71; font-size: .88rem; }

/* Category label link color - changed from pink to purple */
.cnd-lcard-meta .cat-label { color: var(--cnd-primary); font-weight: 600; font-size: .78rem; }

/* Side panel on desktop ─── */
@media (min-width: 992px) {
  .cnd-browse-layout { display: flex; gap: 1.5rem; align-items: flex-start; }
  .cnd-filter-panel  { width: 230px; flex-shrink: 0; position: sticky; top: calc(var(--cnd-navbar-h) + 120px); }
  .cnd-results-panel { flex: 1; min-width: 0; }
}

/* Filter sidebar card */
.cnd-filter-card {
  background: #fff;
  border: 1px solid #eee;
  border-radius: 18px;
  padding: 1.3rem;
  box-shadow: 0 4px 16px rgba(0,0,0,.06);
}
.cnd-filter-card h3 {
  font-size: .82rem;
  font-weight: 800;
  text-transform: uppercase;
  letter-spacing: .08em;
  color: var(--cnd-dark);
  margin-bottom: .85rem;
}
.cnd-radius-slider {
  accent-color: var(--cnd-secondary);
  width: 100%;
}

/* Apply / Reset buttons — matching brand */
#applyFilterBtn, .cnd-apply-btn {
  background: var(--cnd-primary);
  color: #fff;
  border: none;
  border-radius: 50px;
  font-weight: 700;
  font-size: .88rem;
  padding: .6rem 1.8rem;
  transition: all .2s;
  box-shadow: 0 5px 15px rgba(63, 53, 144, .35);
}
#applyFilterBtn:hover, .cnd-apply-btn:hover {
  background: var(--cnd-pink);
  box-shadow: 0 6px 18px rgba(255, 104, 180, .35);
  transform: translateY(-1px);
}
#resetFilterBtn {
  border-radius: 50px;
  font-weight: 600;
  font-size: .88rem;
  color: var(--cnd-muted);
  background: #f1f1f1;
  border: none;
}
#resetFilterBtn:hover {
  background: #e5e5e5;
  color: var(--cnd-dark);
}

/* Loading skeleton ─── */
.cnd-skeleton {
  background: linear-gradient(90deg, #f0eafa 25%, #ebe4f7 50%, #f0eafa 75%);
  background-size: 200% 100%;
  animation: cnd-shimmer 1.3s infinite;
  border-radius: var(--cnd-radius-xs);
}
@keyframes cnd-shimmer {
  0%   { background-position: 200% 0; }
  100% { background-position: -200% 0; }
}

/* AJAX loading overlay */
.cnd-loading-overlay {
  position: relative;
  min-height: 200px;
}
.cnd-loading-overlay::after {
  content: '';
  display: none;
  position: absolute; inset: 0;
  background: rgba(255,255,255,.7);
  backdrop-filter: blur(3px);
  border-radius: var(--cnd-radius-sm);
  z-index: 5;
}
.cnd-loading-overlay.loading::after { display: block; }
.cnd-loading-spinner {
  display: none;
  position: absolute; top: 50%; left: 50%;
  transform: translate(-50%, -50%);
  z-index: 6;
}
.cnd-loading-overlay.loading .cnd-loading-spinner { display: block; }

/* No results ─── */
.cnd-no-results {
  text-align: center;
  padding: 3rem 1rem;
  color: var(--cnd-muted);
}
.cnd-no-results-icon { font-size: 3rem; display: block; opacity: .3; margin-bottom: .8rem; }

/* Active filter chips ─── */
.cnd-filter-chips { display: flex; flex-wrap: wrap; gap: .4rem; margin-bottom: 1rem; }
.cnd-filter-chip {
  display: inline-flex; align-items: center; gap: .3rem;
  background: rgba(119,120,246,.1);
  border: 1px solid var(--cnd-secondary);
  color: var(--cnd-secondary);
  border-radius: var(--cnd-radius-pill);
  font-size: .75rem;
  font-weight: 600;
  padding: .22rem .7rem;
}
.cnd-filter-chip button {
  background: none; border: none;
  color: var(--cnd-secondary); cursor: pointer;
  padding: 0; line-height: 1; font-size: .85rem;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- ══ BROWSE HERO ══════════════════════════════════════════ -->
<section class="cnd-browse-hero" aria-labelledby="browse-heading">
  <div class="container-fluid px-3 px-lg-5">
    <div class="row align-items-center gy-3">
      <div class="col-12 col-lg-7">
        <h1 class="cnd-browse-title" id="browse-heading">
          Browse <span style="color:var(--cnd-gold)">Classes</span>
          <?php if (!empty($selected_location)): ?>
            <span style="font-size:clamp(1rem,2vw,1.2rem);font-weight:500;opacity:.9;">
              near <?= esc(explode(',', $selected_location)[0]) ?>
            </span>
          <?php endif; ?>
        </h1>
        <p class="cnd-browse-sub mt-2">
          Discover classes for your child
          <?php if (!empty($selected_location)): ?> · <span><?= esc($selected_location) ?></span><?php endif; ?>
        </p>
      </div>
      <div class="col-12 col-lg-5 d-flex justify-content-lg-end gap-2 flex-wrap">
        <!-- Location shortcut -->
        <button class="btn cnd-hero-location-btn"
          type="button"
          data-bs-toggle="modal"
          data-bs-target="#locationModal"
          style="padding:.5rem 1.1rem;font-size:.88rem;">
          <i class="bi bi-geo-alt-fill me-2"></i>
          <?= !empty($selected_location) ? esc(character_limiter($selected_location, 30)) : 'Set Location' ?>
        </button>
      </div>
    </div>
  </div>
</section>

<!-- ══ STICKY TYPE TABS ════════════════════════════════════ -->
<div class="cnd-type-strip" id="typeStrip">
  <div class="container-fluid px-3 px-lg-5">
    <ul class="nav cnd-type-tabs" id="classTypeTabs" role="tablist" aria-label="Class type">

      <?php foreach (['regular' => ['Regular Classes','bi-calendar3'], 'workshop' => ['Workshops','bi-lightning-charge'], 'course' => ['Courses','bi-journal-richtext']] as $tval => $tinfo): ?>
      <li class="nav-item" role="presentation">
        <button
          class="nav-link cnd-type-tab <?= ($current_type ?? 'regular') === $tval ? 'active' : '' ?>"
          data-type="<?= $tval ?>"
          type="button"
          role="tab"
          aria-selected="<?= ($current_type ?? 'regular') === $tval ? 'true' : 'false' ?>">
          <i class="bi <?= $tinfo[1] ?> me-1" aria-hidden="true"></i>
          <?= $tinfo[0] ?>
        </button>
      </li>
      <?php endforeach; ?>

    </ul>
  </div>
</div>

<!-- ══ MAIN BROWSE AREA ════════════════════════════════════ -->
<section class="py-4" style="background:var(--cnd-light);" id="browseArea">
  <div class="container-fluid px-3 px-lg-5">

    <!-- Filter chips moved inside results panel -->

    <div class="cnd-browse-layout">

      <!-- ── DESKTOP FILTER SIDEBAR ── -->
      <aside class="cnd-filter-panel d-none d-lg-block" aria-label="Filter sidebar">
        <div class="cnd-filter-card mb-3">
          <h3><i class="bi bi-funnel me-1"></i> Filters</h3>

          <!-- Radius slider -->
          <div class="mb-4">
            <label for="radiusSlider" class="form-label small fw-600">
              Radius: <span id="radiusLabel"><?= (int)($radius ?? 25) ?></span> km
            </label>
            <input type="range" class="cnd-radius-slider form-range" id="radiusSlider"
              min="5" max="50" step="5" value="<?= (int)($radius ?? 25) ?>"
              aria-label="Search radius in kilometres">
          </div>

          <!-- Sort -->
          <div class="mb-4">
            <label for="sidebarSort" class="form-label small fw-600">Sort by</label>
            <select class="form-select form-select-sm cnd-sort-select" id="sidebarSort" aria-label="Sort listings">
              <option value="relevancy"  <?= ($current_sort ?? 'relevancy') === 'relevancy'  ? 'selected' : '' ?>>Most Relevant</option>
              <option value="distance"   <?= ($current_sort ?? '') === 'distance'   ? 'selected' : '' ?>>Nearest First</option>
              <option value="rating"     <?= ($current_sort ?? '') === 'rating'     ? 'selected' : '' ?>>Top Rated</option>
              <option value="price_asc"  <?= ($current_sort ?? '') === 'price_asc'  ? 'selected' : '' ?>>Price: Low → High</option>
              <option value="price_desc" <?= ($current_sort ?? '') === 'price_desc' ? 'selected' : '' ?>>Price: High → Low</option>
            </select>
          </div>
          
          <!-- Date Filter (Happening) -->
          <div class="mb-4" id="sidebarDateSection" style="<?= ($current_type ?? 'regular') === 'workshop' ? 'display:block;' : 'display:none;' ?>">
            <label class="form-label small fw-800 mb-2 text-uppercase letter-spacing-1" style="font-size:0.7rem; color:var(--cnd-muted);">Happening</label>
            <div class="d-flex flex-column gap-1">
              <div class="form-check mb-1">
                <input class="form-check-input date-filter-radio" type="radio" name="date_filter" value="" id="date_all" <?= empty($current_date_filter) ? 'checked' : '' ?> style="cursor:pointer;">
                <label class="form-check-label small fw-500" for="date_all" style="cursor:pointer; font-size:0.82rem;">All Upcoming</label>
              </div>
              <div class="form-check mb-1">
                <input class="form-check-input date-filter-radio" type="radio" name="date_filter" value="this_week" id="date_this_week" <?= ($current_date_filter ?? '') === 'this_week' ? 'checked' : '' ?> style="cursor:pointer;">
                <label class="form-check-label small fw-500" for="date_this_week" style="cursor:pointer; font-size:0.82rem;">This Week</label>
              </div>
              <div class="form-check mb-1">
                <input class="form-check-input date-filter-radio" type="radio" name="date_filter" value="this_weekend" id="date_this_weekend" <?= ($current_date_filter ?? '') === 'this_weekend' ? 'checked' : '' ?> style="cursor:pointer;">
                <label class="form-check-label small fw-500" for="date_this_weekend" style="cursor:pointer; font-size:0.82rem;">This Weekend</label>
              </div>
            </div>
          </div>

          <!-- Subcategories (Desktop Sidebar) -->
          <div class="mb-4 d-none d-lg-block" id="sidebarSubcatSection" style="<?= !empty($subcategories) ? 'display:block;' : 'display:none;' ?>">
            <label class="form-label small fw-800 mb-2 text-uppercase letter-spacing-1" style="font-size:0.7rem; color:var(--cnd-muted);">Subcategories</label>
            <div id="sidebarSubcategories" class="d-flex flex-column gap-1" style="max-height: 250px; overflow-y: auto; padding-right: 5px;">
              <?php foreach ($subcategories as $sub): ?>
              <div class="form-check mb-1">
                <input class="form-check-input subcat-check" type="checkbox" 
                       value="<?= $sub['id'] ?>" id="sub_<?= $sub['id'] ?>" <?= in_array((int)$sub['id'], (array)($current_subcategory ?? [])) ? 'checked' : '' ?> style="cursor:pointer;">
                <label class="form-check-label small fw-500" for="sub_<?= $sub['id'] ?>" style="cursor:pointer; font-size:0.82rem;">
                  <?= esc($sub['name']) ?>
                </label>
              </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Apply / Reset buttons -->
          <div class="d-flex gap-2">
            <button class="btn cnd-btn-primary btn-sm flex-grow-1" id="applyFilterBtn">Apply</button>
            <button class="btn btn-light btn-sm" id="resetFilterBtn">Reset</button>
          </div>

        </div>
      </aside>

      <!-- ── RESULTS PANEL ── -->
      <div class="cnd-results-panel" id="resultsPanel">
        
        <!-- Filter chips (active filters display) -->
        <div class="cnd-filter-chips mb-3" id="activeFilterChips" aria-live="polite" aria-label="Active filters"></div>

        <!-- Mobile: category row + sort strip -->
        <div class="d-lg-none mb-3">
          <!-- Category icon row (scrollable) -->
          <div class="cnd-cat-filter-row mb-3" id="mobileCatRow" role="listbox" aria-label="Filter by category">
            <button class="cnd-cat-filter-btn cat-all <?= empty($current_category) ? 'active' : '' ?>"
              data-cat=""
              role="option" aria-selected="<?= empty($current_category) ? 'true' : 'false' ?>">
              <i class="bi bi-grid-fill"></i>All
            </button>
            <?php
            foreach ($categories as $cat):
              $icon = !empty($cat['icon']) ? $cat['icon'] : 'bi-star';
            ?>
            <button class="cnd-cat-filter-btn <?= ($current_category ?? null) == $cat['id'] ? 'active' : '' ?>"
              data-cat="<?= (int)$cat['id'] ?>"
              role="option"
              aria-selected="<?= ($current_category ?? null) == $cat['id'] ? 'true' : 'false' ?>"
              title="<?= esc($cat['name']) ?>">
              <i class="bi <?= $icon ?>"></i>
              <?= esc(mb_substr($cat['name'], 0, 8)) ?>
            </button>
            <?php endforeach; ?>
          </div>

          <!-- Sort select (mobile) -->
          <div class="d-flex align-items-center gap-2">
            <label for="mobileSortSelect" class="small fw-600 text-muted text-nowrap">Sort:</label>
            <select class="form-select form-select-sm cnd-sort-select flex-grow-1" id="mobileSortSelect" aria-label="Sort listings">
              <option value="relevancy"  <?= ($current_sort ?? 'relevancy') === 'relevancy'  ? 'selected' : '' ?>>Most Relevant</option>
              <option value="distance"   <?= ($current_sort ?? '') === 'distance'   ? 'selected' : '' ?>>Nearest First</option>
              <option value="rating"     <?= ($current_sort ?? '') === 'rating'     ? 'selected' : '' ?>>Top Rated</option>
              <option value="price_asc"  <?= ($current_sort ?? '') === 'price_asc'  ? 'selected' : '' ?>>Price: Low → High</option>
              <option value="price_desc" <?= ($current_sort ?? '') === 'price_desc' ? 'selected' : '' ?>>Price: High → Low</option>
            </select>
          </div>

          <!-- Mobile Date Filter Pills -->
          <div class="d-lg-none mt-3" id="mobileDateSection" style="<?= ($current_type ?? 'regular') === 'workshop' ? 'display:block;' : 'display:none;' ?>">
            <div class="d-flex gap-2 overflow-x-auto pb-1" role="listbox" aria-label="Filter by date">
               <button class="btn btn-sm rounded-pill px-3 date-filter-btn <?= empty($current_date_filter) ? 'active bg-primary text-white' : 'btn-outline-primary' ?>" data-date="" role="option" aria-selected="<?= empty($current_date_filter) ? 'true' : 'false' ?>">All Upcoming</button>
               <button class="btn btn-sm rounded-pill px-3 date-filter-btn <?= ($current_date_filter ?? '') === 'this_week' ? 'active bg-primary text-white' : 'btn-outline-primary' ?>" data-date="this_week" role="option" aria-selected="<?= ($current_date_filter ?? '') === 'this_week' ? 'true' : 'false' ?>">This Week</button>
               <button class="btn btn-sm rounded-pill px-3 date-filter-btn <?= ($current_date_filter ?? '') === 'this_weekend' ? 'active bg-primary text-white' : 'btn-outline-primary' ?>" data-date="this_weekend" role="option" aria-selected="<?= ($current_date_filter ?? '') === 'this_weekend' ? 'true' : 'false' ?>">This Weekend</button>
            </div>
          </div>
        </div>

        <!-- Desktop: category row (icon bubbles, horizontal scroll) -->
        <div class="d-none d-lg-flex cnd-cat-filter-row mb-3" id="desktopCatRow" role="listbox" aria-label="Filter by category">
          <button class="cnd-cat-filter-btn cat-all <?= empty($current_category) ? 'active' : '' ?>"
            data-cat="" role="option" aria-selected="<?= empty($current_category) ? 'true' : 'false' ?>">
            <i class="bi bi-grid-fill"></i>All
          </button>
          <?php foreach ($categories as $cat):
            $icon = !empty($cat['icon']) ? $cat['icon'] : 'bi-star'; ?>
          <button class="cnd-cat-filter-btn <?= ($current_category ?? null) == $cat['id'] ? 'active' : '' ?>"
            data-cat="<?= (int)$cat['id'] ?>"
            role="option"
            aria-selected="<?= ($current_category ?? null) == $cat['id'] ? 'true' : 'false' ?>"
            title="<?= esc($cat['name']) ?>">
            <i class="bi <?= $icon ?>"></i>
            <?= esc(mb_substr($cat['name'], 0, 10)) ?>
          </button>
          <?php endforeach; ?>
        </div>

        <!-- Mobile/Tablet subcategory row -->
        <div class="d-lg-none">
          <?php if (!empty($subcategories)): ?>
          <div class="d-flex flex-wrap gap-2 mb-4 justify-content-center" id="subcatRowMobile" role="listbox" aria-label="Filter by subcategory">
             <?php foreach($subcategories as $sub): ?>
             <button class="btn btn-sm rounded-pill px-3 py-1 <?= in_array((int)$sub['id'], (array)($current_subcategory ?? [])) ? 'active bg-pink text-white' : 'btn-outline-pink' ?> subcat-btn"
                     data-sub="<?= $sub['id'] ?>"
                     role="option"
                     aria-selected="<?= in_array((int)$sub['id'], (array)($current_subcategory ?? [])) ? 'true' : 'false' ?>">
                <?= esc($sub['name']) ?>
             </button>
             <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
          <div>
            <span class="fw-700 text-dark" id="resultsCount">
              <?= number_format($listings_total ?? 0) ?> classes
            </span>
            <span class="text-muted small ms-1">found</span>
          </div>
          <div></div> <!-- Spacer for flex alignment -->
        </div>

        <!-- Cards container (AJAX replaces innerHTML of this) -->
        <div class="cnd-loading-overlay position-relative" id="listingsContainer">
          <div class="cnd-loading-spinner">
            <div class="spinner-border" style="color:var(--cnd-pink);" role="status">
              <span class="visually-hidden">Loading…</span>
            </div>
          </div>

          <!-- Server-rendered initial listings -->
          <?php
          $activeType = $current_type ?? 'regular';
          $rows = $listings[$activeType] ?? [];
          ?>

          <?php if (empty($rows)): ?>
            <?php if (!$location_selected): ?>
            <!-- No location set -->
            <div class="cnd-no-results" id="noResultsState" role="status">
              <i class="bi bi-geo-alt cnd-no-results-icon" aria-hidden="true"></i>
              <p class="fw-600 mb-2">Set your location to find classes</p>
              <button class="btn cnd-btn-primary" data-bs-toggle="modal" data-bs-target="#locationModal">
                <i class="bi bi-geo-alt-fill me-1"></i> Set Location
              </button>
            </div>
            <?php else: ?>
            <!-- Location set but no classes -->
            <div class="cnd-no-results" id="noResultsState" role="status">
              <i class="bi bi-journal-x cnd-no-results-icon" aria-hidden="true"></i>
              <p class="fw-600 mb-1">No classes found in your area</p>
              <p class="small text-muted">Try a wider radius or a different category.</p>
            </div>
            <?php endif; ?>
          <?php else: ?>

            <!-- ── MOBILE / TABLET — vertical strip ── -->
            <div class="d-lg-none d-flex flex-column gap-3" id="mobileCards" role="list">
              <?php foreach ($rows as $listing): ?>
              <?= view('frontend/partials/listing_card_h', ['listing' => $listing]) ?>
              <?php endforeach; ?>
            </div>

            <!-- ── DESKTOP — 2 column grid ── -->
            <div class="d-none d-lg-block">
              <div class="row row-cols-1 row-cols-xl-2 g-3" id="desktopCards" role="list">
                <?php foreach ($rows as $listing): ?>
                <div class="col" role="listitem">
                  <?= view('frontend/partials/listing_card_h', ['listing' => $listing]) ?>
                </div>
                <?php endforeach; ?>
              </div>
            </div>

          <?php endif; ?>
        </div><!-- /#listingsContainer -->

        <!-- Pagination -->
        <?php if (!empty($total_pages) && $total_pages > 1): ?>
        <nav aria-label="Browse class pages" class="mt-4" id="paginationNav">
          <ul class="pagination justify-content-center gap-1">
            <?php
              $cp   = $current_page  ?? 1;
              $tp   = $total_pages   ?? 1;
              $cs   = $current_sort  ?? 'relevancy';
              $ct   = $current_type  ?? 'regular';
              $cc   = $current_category ?? '';
              $bq   = 'type=' . urlencode($ct) . '&sort=' . urlencode($cs) . ($cc ? '&category=' . $cc : '') . '&page=';
            ?>
            <li class="page-item <?= $cp <= 1 ? 'disabled' : '' ?>">
              <a class="page-link" href="<?= base_url('classes?' . $bq . ($cp - 1)) ?>" aria-label="Previous">
                <i class="bi bi-chevron-left"></i>
              </a>
            </li>
            <?php for ($p = max(1, $cp - 2); $p <= min($tp, $cp + 2); $p++): ?>
            <li class="page-item <?= $p === $cp ? 'active' : '' ?>" <?= $p === $cp ? 'aria-current="page"' : '' ?>>
              <a class="page-link" href="<?= base_url('classes?' . $bq . $p) ?>"><?= $p ?></a>
            </li>
            <?php endfor; ?>
            <li class="page-item <?= $cp >= $tp ? 'disabled' : '' ?>">
              <a class="page-link" href="<?= base_url('classes?' . $bq . ($cp + 1)) ?>" aria-label="Next">
                <i class="bi bi-chevron-right"></i>
              </a>
            </li>
          </ul>
        </nav>
        <?php endif; ?>

      </div><!-- /.cnd-results-panel -->
    </div><!-- /.cnd-browse-layout -->
  </div>
</section>

<!-- ══ CLASSES PAGE JAVASCRIPT ══════════════════════════ -->
<?= $this->section('js') ?>
<script>
(function () {
  'use strict';

  /* ── Config ───────────────────────────────────────────── */
  var BASE_URL    = (document.body.dataset.baseUrl || (window.location.origin + '/')).trim();
  if (BASE_URL.slice(-1) !== '/') BASE_URL += '/';
  var API_URL     = BASE_URL + 'api/listings/nearby';

  /* ── State ────────────────────────────────────────────── */
  var state = {
    type:        '<?= esc($current_type ?? 'regular') ?>',
    sort:        '<?= esc($current_sort ?? 'relevancy') ?>',
    category:    <?= json_encode($current_category) ?>,
    subcategory: <?= json_encode((array)($current_subcategory ?? [])) ?>,
    radius:      <?= (int)($radius ?? 25) ?>,
    date_filter: <?= json_encode($current_date_filter ?? '') ?>,
    page:        1,
    lat:         null,
    lng:         null,
  };

  /* ── Element Refs ─────────────────────────────────────── */
  var container   = document.getElementById('listingsContainer');
  var countEl     = document.getElementById('resultsCount');
  var filterChips = document.getElementById('activeFilterChips');
  var paginationEl= document.getElementById('paginationNav');

  /* ── Read saved location ──────────────────────────────── */
  (function initLocation() {
    try {
      var saved = JSON.parse(sessionStorage.getItem('cnd_location') || '{}');
      if (saved.lat) state.lat = parseFloat(saved.lat);
      if (saved.lng) state.lng = parseFloat(saved.lng);
    } catch(e) {}
    // also try cookies
    if (!state.lat) {
      var lat = getCookie('cnd_lat'), lng = getCookie('cnd_lng');
      if (lat) { state.lat = parseFloat(lat); state.lng = parseFloat(lng); }
    }
    // Initial refresh if category is set
    if (state.category) refreshSubcategories();
  })();

  /* ── TYPE TABS — click ────────────────────────────────── */
  document.querySelectorAll('.cnd-type-tab[data-type]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      state.type = this.dataset.type;
      state.page = 1;
      
      // Toggle date filter section based on type
      const dateSec = document.getElementById('sidebarDateSection');
      const mobileDateSec = document.getElementById('mobileDateSection');
      if (dateSec) dateSec.style.display = (state.type === 'workshop') ? 'block' : 'none';
      if (mobileDateSec) mobileDateSec.style.display = (state.type === 'workshop') ? 'block' : 'none';
      if (state.type !== 'workshop') {
        state.date_filter = '';
        updateDateFilterUI();
      }

      // Update all tab active classes
      document.querySelectorAll('.cnd-type-tab[data-type]').forEach(function(b) {
        var isA = b.dataset.type === state.type;
        b.classList.toggle('active', isA);
        b.setAttribute('aria-selected', isA ? 'true' : 'false');
      });
      fetchAndRender();
    });
  });

  /* ── DATE FILTER — change ──────────────────────────────── */
  document.querySelectorAll('.date-filter-radio').forEach(function(radio) {
    radio.addEventListener('change', function() {
      if (this.checked) {
        state.date_filter = this.value;
        state.page = 1;
        updateDateFilterUI();
        fetchAndRender();
      }
    });
  });

  /* ── DATE FILTER — click (Mobile) ─────────────────────── */
  document.querySelectorAll('.date-filter-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
      state.date_filter = this.dataset.date;
      state.page = 1;
      
      // Sync to desktop radio button
      const radio = document.querySelector(`.date-filter-radio[value="${state.date_filter}"]`);
      if (radio) radio.checked = true;
      
      updateDateFilterUI();
      fetchAndRender();
    });
  });

  function updateDateFilterUI() {
    // Mobile buttons
    document.querySelectorAll('.date-filter-btn').forEach(function(b) {
      const isA = b.dataset.date === state.date_filter;
      b.classList.toggle('active', isA);
      b.classList.toggle('bg-primary', isA);
      b.classList.toggle('text-white', isA);
      b.classList.toggle('btn-outline-primary', !isA);
      b.setAttribute('aria-selected', isA ? 'true' : 'false');
    });
    
    // Chips update is handled inside fetchAndRender -> updateFilterChips
  }

  /* ── CATEGORY BUTTONS — click ─────────────────────────── */
  function bindCatButtons(selector) {
    document.querySelectorAll(selector).forEach(function(btn) {
      btn.addEventListener('click', function() {
        var val = this.dataset.cat;
        state.category = val === '' ? null : parseInt(val, 10);
        state.subcategory = []; // Reset subcategory when category changes
        state.page = 1;
        // Sync all cat button groups
        document.querySelectorAll('[data-cat]').forEach(function(b) {
          var isA = b.dataset.cat === (val === '' ? '' : String(state.category));
          b.classList.toggle('active', isA);
          b.setAttribute('aria-selected', isA ? 'true' : 'false');
          if (b.hasAttribute('data-active')) b.dataset.active = isA ? 'true' : 'false';
        });
        
        refreshSubcategories();
        updateFilterChips();
        fetchAndRender();
      });
    });
  }
  bindCatButtons('.cnd-cat-filter-btn');

  async function refreshSubcategories() {
      const desktopSidebar = document.getElementById('sidebarSubcategories');
      const sidebarSection = document.getElementById('sidebarSubcatSection');
      const mobileRow = document.getElementById('subcatRowMobile');
      const resultsPanel = document.getElementById('resultsPanel');
      
      if (!state.category) {
          if (sidebarSection) sidebarSection.style.display = 'none';
          if (desktopSidebar) desktopSidebar.innerHTML = '';
          if (mobileRow) mobileRow.remove();
          return;
      }
      
      try {
          const res = await fetch(BASE_URL + 'api/subcategories?category_id=' + state.category);
          const data = await res.json();
          if (data.success && data.data.length > 0) {
              // Populate Desktop Sidebar
              if (sidebarSection) sidebarSection.style.display = 'block';
              if (desktopSidebar) {
                  let sidebarHtml = '';
                  data.data.forEach(sub => {
                      const isA = state.subcategory.indexOf(parseInt(sub.id, 10)) > -1;
                      sidebarHtml += `
                          <div class="form-check mb-1">
                              <input class="form-check-input subcat-check" type="checkbox" 
                                     value="${sub.id}" id="sub_${sub.id}" ${isA ? 'checked' : ''} style="cursor:pointer;">
                              <label class="form-check-label small fw-500" for="sub_${sub.id}" style="cursor:pointer; font-size:0.82rem;">
                                  ${esc(sub.name)}
                              </label>
                          </div>`;
                  });
                  desktopSidebar.innerHTML = sidebarHtml;
              }

              // Populate Mobile Row
              let mobileHtml = '';
              data.data.forEach(sub => {
                  const isA = state.subcategory.indexOf(parseInt(sub.id, 10)) > -1;
                  const cls = isA ? 'active bg-pink text-white' : 'btn-outline-pink';
                  mobileHtml += `<button class="btn btn-sm rounded-pill px-3 py-1 ${cls} subcat-btn" data-sub="${sub.id}" role="option" aria-selected="${isA}">${esc(sub.name)}</button>`;
              });
              
              let mRow = document.getElementById('subcatRowMobile');
              if (!mRow) {
                  mRow = document.createElement('div');
                  mRow.id = 'subcatRowMobile';
                  mRow.className = 'd-lg-none d-flex flex-wrap gap-2 mb-4 justify-content-center';
                  // Insert before active filters or at top of results panel for mobile
                  const filters = document.getElementById('activeFilterChips');
                  if (filters) filters.insertAdjacentElement('afterend', mRow);
                  else resultsPanel.prepend(mRow);
              }
              mRow.innerHTML = mobileHtml;
              
              bindSubcatButtons();
          } else {
              if (sidebarSection) sidebarSection.style.display = 'none';
              if (mobileRow) mobileRow.remove();
          }
      } catch(e) {}
  }

  function bindSubcatButtons() {
    // Buttons (Mobile)
    document.querySelectorAll('.subcat-btn').forEach(function(btn) {
      btn.addEventListener('click', function() {
        toggleSubcategory(parseInt(this.dataset.sub, 10));
      });
    });
    // Checkboxes (Desktop)
    document.querySelectorAll('.subcat-check').forEach(function(chk) {
      chk.addEventListener('change', function() {
        toggleSubcategory(parseInt(this.value, 10));
      });
    });
  }

  function toggleSubcategory(val) {
    if (!Array.isArray(state.subcategory)) state.subcategory = [];
    var idx = state.subcategory.indexOf(val);
    
    if (idx > -1) {
        state.subcategory.splice(idx, 1);
    } else {
        state.subcategory.push(val);
    }
    
    state.page = 1;

    // Sync state to UI
    document.querySelectorAll('.subcat-btn').forEach(function(b) {
      var isA = state.subcategory.indexOf(parseInt(b.dataset.sub, 10)) > -1;
      b.classList.toggle('active', isA);
      b.classList.toggle('bg-pink', isA);
      b.classList.toggle('text-white', isA);
      b.classList.toggle('btn-outline-pink', !isA);
      b.setAttribute('aria-selected', isA ? 'true' : 'false');
    });
    document.querySelectorAll('.subcat-check').forEach(function(c) {
      c.checked = state.subcategory.indexOf(parseInt(c.value, 10)) > -1;
    });
    
    fetchAndRender();
  }

  /* ── SORT SELECTS ─────────────────────────────────────── */
  ['mobileSortSelect', 'desktopSortSelect', 'sidebarSort'].forEach(function(id) {
    var el = document.getElementById(id);
    if (!el) return;
    el.addEventListener('change', function() {
      state.sort = this.value;
      state.page = 1;
      // Sync all sort selects
      ['mobileSortSelect', 'desktopSortSelect', 'sidebarSort'].forEach(function(oid) {
        var o = document.getElementById(oid);
        if (o && o !== el) o.value = state.sort;
      });
      fetchAndRender();
    });
  });

  /* ── RADIUS SLIDER ────────────────────────────────────── */
  var radiusSlider = document.getElementById('radiusSlider');
  var radiusLabel  = document.getElementById('radiusLabel');
  if (radiusSlider) {
    radiusSlider.addEventListener('input', function() {
      state.radius = parseInt(this.value, 10);
      if (radiusLabel) radiusLabel.textContent = state.radius;
    });
  }

  /* ── APPLY / RESET ────────────────────────────────────── */
  var applyBtn = document.getElementById('applyFilterBtn');
  var resetBtn = document.getElementById('resetFilterBtn');
  if (applyBtn) applyBtn.addEventListener('click', function() { state.page = 1; fetchAndRender(); });
  if (resetBtn) resetBtn.addEventListener('click', function() {
    state.category = null; state.radius = 25; state.sort = 'relevancy'; state.page = 1; state.date_filter = '';
    if (radiusSlider) radiusSlider.value = 25;
    if (radiusLabel)  radiusLabel.textContent = 25;
    ['mobileSortSelect','desktopSortSelect','sidebarSort'].forEach(function(id) {
      var el = document.getElementById(id); if (el) el.value = 'relevancy';
    });
    document.querySelectorAll('[data-cat]').forEach(function(b) {
      var isA = b.dataset.cat === '';
      b.classList.toggle('active', isA);
      b.setAttribute('aria-selected', isA ? 'true' : 'false');
    });

    // Reset date filters
    const rAll = document.getElementById('date_all');
    if (rAll) rAll.checked = true;
    updateDateFilterUI();

    updateFilterChips();
    fetchAndRender();
  });

  /* ── FETCH + RENDER ───────────────────────────────────── */
  var _xhrAbort = null;
  function fetchAndRender() {
    // Show loading
    container.classList.add('loading');

    // Abort previous request if pending
    if (_xhrAbort) { _xhrAbort.abort(); }

    var url = API_URL +
      '?type='     + encodeURIComponent(state.type) +
      '&sort='     + encodeURIComponent(state.sort) +
      '&radius='   + state.radius +
      '&page='     + state.page +
      '&limit=20';
    if (state.lat) url += '&lat=' + state.lat;
    if (state.lng) url += '&lng=' + state.lng;
    if (state.category) url += '&category=' + state.category;
    if (state.subcategory && state.subcategory.length > 0) url += '&subcategory=' + state.subcategory.join(',');
    if (state.date_filter) url += '&date_filter=' + state.date_filter;

    var xhr = new XMLHttpRequest();
    _xhrAbort = xhr;
    xhr.open('GET', url, true);

    // Sync state to URL
    var u = new URL(window.location.href);
    if (state.type && state.type !== 'regular') u.searchParams.set('type', state.type); else u.searchParams.delete('type');
    if (state.category) u.searchParams.set('category', state.category); else u.searchParams.delete('category');
    if (state.subcategory && state.subcategory.length > 0) u.searchParams.set('subcategory', state.subcategory.join(',')); else u.searchParams.delete('subcategory');
    if (state.sort && state.sort !== 'relevancy') u.searchParams.set('sort', state.sort); else u.searchParams.delete('sort');
    if (state.radius && state.radius !== 25) u.searchParams.set('radius', state.radius); else u.searchParams.delete('radius');
    if (state.page > 1) u.searchParams.set('page', state.page); else u.searchParams.delete('page');
    if (state.date_filter) u.searchParams.set('date_filter', state.date_filter); else u.searchParams.delete('date_filter');
    window.history.replaceState({}, '', u.toString());

    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.onreadystatechange = function() {
      if (xhr.readyState !== 4) return;
      container.classList.remove('loading');
      try {
        var data = JSON.parse(xhr.responseText);
        renderResults(data);
      } catch(e) {
        renderError();
      }
    };
    xhr.onerror = function() { container.classList.remove('loading'); renderError(); };
    xhr.send();
  }

  /* ── RENDER HELPERS ───────────────────────────────────── */
  function renderResults(data) {
    var listings = data.listings || [];
    var total    = data.total    || 0;

    if (countEl) countEl.textContent = total.toLocaleString() + ' classes';

    var mobileW = document.getElementById('mobileCards');
    var desktopW = document.getElementById('desktopCards');

    if (listings.length === 0) {
      var emptyHtml = renderEmpty(data.location_served);
      if (mobileW)  mobileW.innerHTML = emptyHtml;
      if (desktopW) desktopW.innerHTML = emptyHtml;
      return;
    }

    var baseUrl = BASE_URL;
    var mobileHtml = '';
    var desktopHtml = '';

    listings.forEach(function(l) {
      var card = buildCardHTML(l, baseUrl);
      mobileHtml += card;
      desktopHtml += '<div class="col" role="listitem">' + card + '</div>';
    });

    if (mobileW)  mobileW.innerHTML = mobileHtml;
    if (desktopW) desktopW.innerHTML = desktopHtml;

    // Remove no-results-state if it exists
    var noRes = document.getElementById('noResultsState');
    if (noRes) noRes.remove();
  }

  function renderEmpty(served) {
    if (!served) {
      return '<div class="cnd-no-results" role="status">' +
        '<i class="bi bi-map cnd-no-results-icon" aria-hidden="true"></i>' +
        '<p class="fw-600 mb-1">No classes found in your area</p>' +
        '<p class="small text-muted">Try widening the radius or a different filter.</p>' +
        '</div>';
    }
    return '<div class="cnd-no-results" role="status">' +
      '<i class="bi bi-journal-x cnd-no-results-icon" aria-hidden="true"></i>' +
      '<p class="fw-600 mb-1">No classes match your filters</p>' +
      '<p class="small text-muted">Try removing a filter.</p></div>';
  }

  function renderNoLocation() {
    var html = '<div class="cnd-no-results" role="status">' +
      '<i class="bi bi-geo-alt cnd-no-results-icon" aria-hidden="true"></i>' +
      '<p class="fw-600 mb-2">Set your location to find classes</p>' +
      '<button class="btn cnd-btn-primary" data-bs-toggle="modal" data-bs-target="#locationModal">' +
      '<i class="bi bi-geo-alt-fill me-1"></i> Set Location</button></div>';

    var mobileW = document.getElementById('mobileCards');
    var desktopW = document.getElementById('desktopCards');
    if (mobileW)  mobileW.innerHTML = html;
    if (desktopW) desktopW.innerHTML = html;
  }

  function renderError() {
    var html = '<div class="cnd-no-results" role="alert">' +
      '<i class="bi bi-wifi-off cnd-no-results-icon"></i>' +
      '<p class="fw-600 mb-1">Failed to load classes</p>' +
      '<button class="btn btn-light btn-sm mt-2" onclick="location.reload()">Retry</button></div>';

    var mobileW = document.getElementById('mobileCards');
    var desktopW = document.getElementById('desktopCards');
    if (mobileW)  mobileW.innerHTML = html;
    if (desktopW) desktopW.innerHTML = html;
  }

  /* ── BUILD CARD HTML ──────────────────────────────────── */
  function buildCardHTML(l, baseUrl) {
    var rawImg = (l.cover_image || '').replace(/^uploads[\\/\\]listings[\\/\\]/i, '').replace(/^uploads[\\/\\]/i, '');
    var img = rawImg
      ? baseUrl + 'uploads/listings/' + rawImg
      : baseUrl + 'assets/frontend/img/class-placeholder.jpg';
    var title   = esc(l.title || 'Class');
    var addr    = l.address ? '<div class="cnd-lcard-address"><i class="bi bi-geo-alt-fill text-danger"></i> ' + esc(l.address.substring(0, 45)) + '</div>' : '';
    var stars   = '';
    if (l.avg_rating) {
      var r = Math.round(l.avg_rating);
      for (var s = 1; s <= 5; s++) stars += s <= r ? '★' : '☆';
      stars = '<span class="cnd-lcard-stars">' + stars + '</span> <span class="small text-muted">(' + (l.review_count || 0) + ')</span>';
    }
    var price = l.price && l.price > 0
      ? '<span class="cnd-lcard-price"><i class="bi bi-currency-rupee"></i>' + Number(l.price).toLocaleString('en-IN') + '</span>'
      : '<span class="cnd-lcard-free">Free</span>';
    var badge = l.type ? '<span style="position:absolute;top:.5rem;left:.5rem;background:var(--cnd-primary);color:#fff;font-size:.6rem;font-weight:700;padding:.15rem .45rem;border-radius:6px;text-transform:uppercase;">' + esc(l.type) + '</span>' : '';
    var catName = l.category_name ? esc(l.category_name) : '';
    var subName = l.subcategory_name ? ' > ' + esc(l.subcategory_name) : '';
    var cat = catName ? '<span class="small" style="color:var(--cnd-primary);font-weight:600;">' + catName + subName + '</span>' : '';
    var dist = l.distance_km ? '<span class="small text-muted"><i class="bi bi-geo-alt"></i> ' + l.distance_km + ' km</span>' : '';

    return '<article class="cnd-lcard" role="listitem" aria-label="' + title + '">' +
      '<div class="cnd-lcard-img">' +
        '<img src="' + img + '" alt="" loading="lazy">' +
        badge +
      '</div>' +
      '<div class="cnd-lcard-body">' +
        '<div class="cnd-lcard-title"><a href="' + baseUrl + 'classes/' + (l.id || '#') + '">' + title + '</a></div>' +
        addr +
        '<div class="cnd-lcard-meta">' + stars + cat + dist + price + '</div>' +
      '</div>' +
      '</article>';
  }

  /* ── ACTIVE FILTER CHIPS ──────────────────────────────── */
  function updateFilterChips() {
    if (!filterChips) return;
    filterChips.innerHTML = '';
    if (state.category) {
      var btn = document.querySelector('[data-cat="' + state.category + '"]');
      var name = btn ? btn.textContent.trim() : 'Category #' + state.category;
      var chip = document.createElement('span');
      chip.className = 'cnd-filter-chip';
      chip.innerHTML = '<i class="bi bi-tag-fill"></i> ' + esc(name) + '<button aria-label="Remove category filter">×</button>';
      chip.querySelector('button').addEventListener('click', function() {
        state.category = null;
        state.subcategory = []; // Clear subcategories as well
        document.querySelectorAll('[data-cat]').forEach(function(b) {
          b.classList.toggle('active', b.dataset.cat === '');
        });
        refreshSubcategories(); // Hide subcategory row
        updateFilterChips();
        fetchAndRender();
      });
      filterChips.appendChild(chip);
    }
    if (state.date_filter) {
      var label = state.date_filter === 'this_week' ? 'This Week' : 'This Weekend';
      var chip = document.createElement('span');
      chip.className = 'cnd-filter-chip';
      chip.innerHTML = '<i class="bi bi-calendar-check"></i> ' + label + '<button aria-label="Remove date filter">×</button>';
      chip.querySelector('button').addEventListener('click', function() {
        state.date_filter = '';
        state.page = 1;
        var rAll = document.getElementById('date_all');
        if (rAll) rAll.checked = true;
        updateFilterChips();
        fetchAndRender();
      });
      filterChips.appendChild(chip);
    }
  }

  /* ── UTILITY ──────────────────────────────────────────── */
  function esc(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }
  function getCookie(name) {
    var m = document.cookie.match(new RegExp('(^|;)\\s*' + name + '\\s*=\\s*([^;]+)'));
    return m ? decodeURIComponent(m[2]) : null;
  }

  /* ── INIT ─────────────────────────────────────────────── */
  updateFilterChips();

  // Auto-fetch if location is available but page was loaded without location (e.g. page refresh after setting location)
  // Only trigger if the page has no server-rendered results yet
  var noResultsEl = document.getElementById('noResultsState');
  if (noResultsEl && state.lat && state.lng) {
    fetchAndRender();
  }

  // Re-fetch when location is confirmed in modal (custom event from app.js)
  window.addEventListener('cnd:location-changed', function(e) {
    if (e.detail && e.detail.lat) {
      state.lat = e.detail.lat;
      state.lng = e.detail.lng;
      state.page = 1;
      fetchAndRender();
    }
  });

})();
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
