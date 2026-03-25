<?= $this->extend('frontend/layout/base') ?>
<?= $this->section('content') ?>

<?php
$base        = base_url();
$q           = $query ?? '';
$curType     = $current_type ?? null;
$curSort     = $current_sort ?? 'relevancy';
$curCat      = $current_category ?? null;
$curRadius   = $radius ?? 25;
$curPage     = $current_page ?? 1;
$totalPages  = $total_pages ?? 1;
$totalFound  = $listings_total ?? 0;
$loc_set     = $location_selected ?? false;
?>

<!-- ══ SEARCH HERO ══════════════════════════════════════════════ -->
<section class="cnd-search-hero" aria-label="Search Classes">
  <div class="container-fluid px-3 px-lg-5 py-4">

    <h1 class="cnd-search-title">
      <i class="bi bi-search" aria-hidden="true"></i>
      <?php if ($q !== ''): ?>
        Results for <em>"<?= esc($q) ?>"</em>
      <?php else: ?>
        Find Classes Near You
      <?php endif; ?>
    </h1>

    <!-- ── Main search bar ── -->
    <form action="<?= base_url('search') ?>" method="get" role="search"
          class="cnd-search-bar-form" id="searchForm" autocomplete="off">
      <div class="cnd-search-bar-wrap">
        <label for="searchInput" class="visually-hidden">Search for classes</label>
        <i class="bi bi-search cnd-search-bar-icon" aria-hidden="true"></i>
        <input
          type="search"
          id="searchInput"
          name="q"
          class="cnd-search-bar-input"
          value="<?= esc($q) ?>"
          placeholder="Search classes, sports, music, dance…"
          aria-label="Search classes"
          minlength="1"
          maxlength="120">
        <?php if ($curType): ?>
          <input type="hidden" name="type" value="<?= esc($curType) ?>">
        <?php endif; ?>
        <?php if ($curCat): ?>
          <input type="hidden" name="category" id="hiddenCat" value="<?= (int)$curCat ?>">
        <?php endif; ?>
        <button type="submit" class="cnd-search-bar-btn" aria-label="Search">
          Search
        </button>
      </div>
    </form>

  </div>
</section>

<!-- ══ FILTER BAR ═══════════════════════════════════════════════ -->
<section class="cnd-search-filters" aria-label="Filter and sort results">
  <div class="container-fluid px-3 px-lg-5 py-3">
    <div class="d-flex align-items-center gap-3 flex-wrap">

      <!-- Type Tabs -->
      <div class="cnd-type-tabs d-flex gap-1 flex-shrink-0" role="group" aria-label="Filter by class type" id="searchTypeTabs">
        <?php
        $tabs = [null => 'All', 'regular' => 'Regular', 'workshop' => 'Workshop', 'course' => 'Course'];
        foreach ($tabs as $tv => $tl):
          $active = ($curType === $tv) ? 'active' : '';
        ?>
        <button class="cnd-tab-pill <?= $active ?>"
                data-type="<?= $tv ?? '' ?>"
                aria-pressed="<?= $active ? 'true' : 'false' ?>"
                type="button">
          <?php if ($tv === 'regular'): ?><i class="bi bi-arrow-repeat" aria-hidden="true"></i>
          <?php elseif ($tv === 'workshop'): ?><i class="bi bi-lightning-fill" aria-hidden="true"></i>
          <?php elseif ($tv === 'course'): ?><i class="bi bi-book" aria-hidden="true"></i>
          <?php else: ?><i class="bi bi-grid-3x3-gap" aria-hidden="true"></i>
          <?php endif; ?>
          <?= $tl ?>
        </button>
        <?php endforeach; ?>
      </div>

      <!-- Sort Dropdown -->
      <div class="cnd-sort-wrap ms-auto">
        <label for="searchSortSelect" class="visually-hidden">Sort results by</label>
        <select id="searchSortSelect" class="form-select form-select-sm cnd-sort-select" aria-label="Sort results">
          <option value="relevancy"  <?= $curSort === 'relevancy'  ? 'selected' : '' ?>>Most Relevant</option>
          <option value="distance"   <?= $curSort === 'distance'   ? 'selected' : '' ?>>Nearest First</option>
          <option value="rating"     <?= $curSort === 'rating'     ? 'selected' : '' ?>>Top Rated</option>
          <option value="price_asc"  <?= $curSort === 'price_asc'  ? 'selected' : '' ?>>Price: Low → High</option>
          <option value="price_desc" <?= $curSort === 'price_desc' ? 'selected' : '' ?>>Price: High → Low</option>
        </select>
      </div>

      <!-- Radius Slider (only when location set) -->
      <?php if ($loc_set): ?>
      <div class="cnd-radius-wrap d-flex align-items-center gap-2" title="Search radius">
        <i class="bi bi-geo text-muted" aria-hidden="true"></i>
        <input type="range" class="form-range cnd-radius-range" id="searchRadiusRange"
               min="5" max="50" step="5" value="<?= (int)$curRadius ?>"
               aria-label="Search radius in km">
        <span class="cnd-radius-label" id="searchRadiusLabel"><?= (int)$curRadius ?> km</span>
      </div>
      <?php endif; ?>

    </div>

    <style>
      .cnd-cat-row {
        display: flex;
        flex-wrap: wrap;
        gap: .6rem;
        justify-content: center;
        padding-top: .5rem;
      }
      .cnd-cat-chip {
        border-radius: 20px;
        padding: .4rem 1.2rem;
        border: 1px solid #dee2e6;
        background: #fff;
        color: #6c757d;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
      }
      .cnd-cat-chip.active {
        background: var(--cnd-pink);
        color: #fff;
        border-color: var(--cnd-pink);
        box-shadow: 0 4px 12px rgba(255, 104, 180,0.3);
      }
      .cnd-cat-chip:hover:not(.active) {
        border-color: var(--cnd-pink);
        color: var(--cnd-pink);
      }
    </style>
    <!-- Category pills (responsive centered row) -->
    <div class="cnd-cat-row mt-3" role="group" aria-label="Filter by category" id="searchCatRow">
      <button class="cnd-cat-chip <?= $curCat === null ? 'active' : '' ?>"
              data-cat="" type="button" aria-pressed="<?= $curCat === null ? 'true' : 'false' ?>">
        All Categories
      </button>
      <?php foreach ($categories as $cat): ?>
      <button class="cnd-cat-chip <?= (int)($curCat ?? -1) === (int)$cat['id'] ? 'active' : '' ?>"
              data-cat="<?= (int)$cat['id'] ?>" type="button"
              aria-pressed="<?= (int)($curCat ?? -1) === (int)$cat['id'] ? 'true' : 'false' ?>">
        <?= esc($cat['name']) ?>
      </button>
      <?php endforeach; ?>
    </div>

    <!-- Subcategory Filter Row (Initially Empty or SSR) -->
    <div class="cnd-cat-row mt-2 <?= empty($subcategories) ? 'd-none' : '' ?>" role="group" aria-label="Filter by subcategory" id="searchSubcatRow" style="font-size: 0.9em;">
       <?php if (!empty($subcategories)): ?>
          <button class="cnd-cat-chip <?= empty($current_subcategory) ? 'active' : '' ?>" data-sub="">All Subcategories</button>
          <?php foreach ($subcategories as $sub): ?>
             <button class="cnd-cat-chip <?= in_array((int)$sub['id'], (array)($current_subcategory ?? [])) ? 'active' : '' ?>" data-sub="<?= $sub['id'] ?>">
                <?= esc($sub['name']) ?>
             </button>
          <?php endforeach; ?>
       <?php endif; ?>
    </div>

  </div>
</section>

<!-- ══ RESULTS AREA ══════════════════════════════════════════════ -->
<section class="cnd-listings-section" id="searchResults" aria-live="polite" aria-label="Search results">
  <div class="container-fluid px-3 px-lg-5 pb-5">

    <!-- Result count bar -->
    <div class="cnd-sort-bar d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
      <p class="mb-0 text-muted small" id="searchResultCount" role="status">
        <?php if ($q !== '' || $loc_set): ?>
          <?php if ($totalFound > 0): ?>
            <strong><?= $totalFound ?></strong> result<?= $totalFound !== 1 ? 's' : '' ?> found
            <?= $q ? "for <strong>\"" . esc($q) . "\"</strong>" : '' ?>
          <?php else: ?>
            No results found<?= $q ? " for <strong>\"" . esc($q) . "\"</strong>" : '' ?>.
            <?php if (!$loc_set): ?> <a href="#" data-bs-toggle="modal" data-bs-target="#locationModal">Set your location</a> to find classes near you.<?php endif; ?>
          <?php endif; ?>
        <?php else: ?>
          Enter a keyword or <a href="#" data-bs-toggle="modal" data-bs-target="#locationModal">set your location</a> to discover classes.
        <?php endif; ?>
      </p>
    </div>

    <!-- Results grid (SSR on first load, replaced by AJAX on filter change) -->
    <div id="searchListingsGrid">
      <?php if (!empty($listings)): ?>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-3 g-lg-4" role="list">
          <?php foreach ($listings as $l): ?>
          <div class="col" role="listitem">
            <article class="cnd-listing-card h-100" aria-label="<?= esc($l['title'] ?? 'Class') ?>">
              <!-- Image -->
              <a href="<?= base_url('classes/' . (int)($l['id'] ?? 0)) ?>"
                 class="cnd-card-img-wrap" tabindex="-1" aria-hidden="true">
                <img
                  src="<?= listing_img_url($l['cover_image'] ?? '') ?>"
                  alt=""
                  class="cnd-card-img"
                  loading="lazy" width="400" height="240">
                <span class="cnd-badge-type-sm"><?= esc(ucfirst($l['type'] ?? 'class')) ?></span>
                <?php if (!empty($l['free_trial'])): ?>
                <span class="cnd-badge-trial">Free Trial</span>
                <?php endif; ?>
              </a>
              <!-- Body -->
              <div class="cnd-card-body">
                <h3 class="cnd-card-title d-flex align-items-center gap-1">
                  <a href="<?= base_url('classes/' . (int)($l['id'] ?? 0)) ?>" class="cnd-card-title-link text-truncate">
                    <?= esc($l['title'] ?? 'Untitled') ?>
                  </a>
                  <?php if(!empty($l['provider_verified'])): ?>
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-0 fw-bold d-inline-flex align-items-center" style="font-size: 0.6rem; height: 18px; flex-shrink: 0;">
                      <i class="bi bi-patch-check-fill me-1" style="font-size: 0.65rem;"></i> Verified
                    </span>
                  <?php endif; ?>
                </h3>
                <?php if (!empty($l['locality']) || !empty($l['city'])): ?>
                <div class="cnd-card-address">
                  <i class="bi bi-geo-alt-fill text-pink" aria-hidden="true"></i>
                  <span><?= esc(implode(', ', array_filter([$l['locality'] ?? '', $l['city'] ?? '']))) ?></span>
                </div>
                <?php elseif (!empty($l['address'])): ?>
                <div class="cnd-card-address">
                  <i class="bi bi-geo-alt-fill text-danger" aria-hidden="true"></i>
                  <span><?= esc(character_limiter($l['address'], 50)) ?></span>
                </div>
                <?php endif; ?>
                <!-- Price + type pill row -->
                <div class="cnd-card-meta d-flex align-items-center gap-2 flex-wrap mt-2">
                  <?php if (!empty($l['category_name'])): ?>
                  <span class="badge rounded-pill" style="background:var(--cnd-pink-soft);color:var(--cnd-pink);font-weight:600;font-size:.68rem;">
                    <?= esc($l['category_name']) ?>
                  </span>
                  <?php endif; ?>
                  <?php if (!empty($l['subcategory_names'])): ?>
                  <span class="text-muted small border-start ps-2" style="font-size: 0.65rem;">
                    <?= esc($l['subcategory_names']) ?>
                  </span>
                  <?php endif; ?>
                  <?php if (!empty($l['avg_rating'])): ?>
                  <span class="cnd-meta-rating" aria-label="Rating <?= number_format($l['avg_rating'], 1) ?> of 5">
                    <span class="cnd-stars"><?php for ($s=1;$s<=5;$s++) echo $s<=(int)round($l['avg_rating']) ? '★' : '☆'; ?></span>
                    <span class="small fw-600"> <?= number_format($l['avg_rating'], 1) ?></span>
                    <span class="text-muted small"> (<?= (int)($l['review_count'] ?? 0) ?>)</span>
                  </span>
                  <?php endif; ?>
                  <?php if (!empty($l['distance_km'])): ?>
                  <span class="ms-auto cnd-badge-dist">
                    <i class="bi bi-geo-alt" aria-hidden="true"></i> <?= number_format($l['distance_km'], 1) ?> km
                  </span>
                  <?php endif; ?>
                </div>
              </div>
              <!-- Footer: price + CTA -->
              <div class="cnd-card-footer d-flex align-items-center justify-content-between">
                <div class="cnd-card-price">
                  <?php
                    $pd = !empty($l['price_breakdown']) ? json_decode($l['price_breakdown'], true) : [];
                    $billing = $pd['billing'] ?? 'once';
                    $fee    = (float)($l['price'] ?? 0);
                  ?>
                  <?php if ($fee > 0): ?>
                    ₹<?= number_format($fee, 0) ?>
                    <?php if ($billing === 'monthly'): ?>
                      <span class="cnd-price-period">/month</span>
                    <?php endif; ?>
                  <?php else: ?>
                    <span class="cnd-badge-free">FREE</span>
                  <?php endif; ?>
                </div>
                <a href="<?= base_url('classes/' . (int)($l['id'] ?? 0)) ?>"
                   class="cnd-btn-details" aria-label="View details for <?= esc($l['title'] ?? '') ?>">
                  View Details
                </a>
              </div>
            </article>
          </div>
          <?php endforeach; ?>
        </div>

        <!-- Load More (if more pages) -->
        <?php if ($totalPages > 1): ?>
        <div class="text-center mt-4" id="searchPagination">
          <button class="cnd-btn-outline" id="loadMoreBtn"
                  data-page="<?= $curPage ?>"
                  data-total-pages="<?= $totalPages ?>">
            <i class="bi bi-arrow-down-circle" aria-hidden="true"></i>
            Load More Results
          </button>
        </div>
        <?php endif; ?>

      <?php elseif ($q !== '' || $loc_set): ?>
        <!-- Empty state -->
        <div class="cnd-not-serving-alert my-4" role="status">
          <span class="cnd-alert-icon bi bi-journal-x" aria-hidden="true"></span>
          <div>
            <strong>No classes found</strong>
            Try a different keyword, change category, or expand your radius.
          </div>
        </div>
      <?php endif; ?>
    </div>
    <!-- /#searchListingsGrid -->

  </div>
</section>

<?= $this->section('js') ?>
<script>
(function () {
  'use strict';

  /* ── State ─────────────────────────────────────────── */
  var state = {
    q        : <?= json_encode($q) ?>,
    type     : <?= json_encode($curType) ?>,
    sort     : <?= json_encode($curSort) ?>,
    category : <?= json_encode($curCat) ?>,
    subcategory : <?= json_encode((array)($current_subcategory ?? [])) ?>,
    radius   : <?= (float)$curRadius ?>,
    lat      : parseFloat(getCookie('cnd_lat'))  || null,
    lng      : parseFloat(getCookie('cnd_lng'))  || null,
    page     : 1
  };
  var _xhr = null;
  var BASE = <?= json_encode(base_url()) ?>;
  var API  = BASE + 'api/listings/search';

  /* ── Refs ──────────────────────────────────────────── */
  var grid     = document.getElementById('searchListingsGrid');
  var countEl  = document.getElementById('searchResultCount');
  var pagEl    = document.getElementById('searchPagination');
  var loadMore = document.getElementById('loadMoreBtn');
  var radRange = document.getElementById('searchRadiusRange');
  var radLabel = document.getElementById('searchRadiusLabel');

  /* ── Utility ────────────────────────────────────────── */
  function getCookie(name) {
    var m = document.cookie.match('(?:^|; )' + name + '=([^;]*)');
    return m ? decodeURIComponent(m[1]) : null;
  }
  function esc(s) {
    var d = document.createElement('div');
    d.textContent = String(s);
    return d.innerHTML;
  }

  /* ── Skeleton ───────────────────────────────────────── */
  function showSkeleton() {
    var html = '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-3">';
    for (var i=0; i<8; i++) {
      html += '<div class="col"><div class="cnd-skeleton-card">'
            + '<div class="cnd-sk-img"></div>'
            + '<div class="cnd-sk-line wide"></div>'
            + '<div class="cnd-sk-line"></div>'
            + '<div class="cnd-sk-line narrow"></div>'
            + '</div></div>';
    }
    html += '</div>';
    grid.innerHTML = html;
  }

  /* ── Render results ─────────────────────────────────── */
  function renderResults(data, append) {
    // update count
    if (countEl) {
      var q = state.q;
      if (data.total > 0) {
        countEl.innerHTML = '<strong>' + data.total + '</strong> result' + (data.total === 1 ? '' : 's') + ' found'
          + (q ? ' for <strong>"' + esc(q) + '"</strong>' : '');
      } else {
        countEl.innerHTML = 'No results found' + (q ? ' for <strong>"' + esc(q) + '"</strong>' : '') + '.';
      }
    }

    if (!data.listings || !data.listings.length) {
      if (!append) {
        grid.innerHTML = '<div class="cnd-not-serving-alert my-4" role="status">'
          + '<span class="cnd-alert-icon bi bi-journal-x" aria-hidden="true"></span>'
          + '<div><strong>No classes found</strong> Try a different keyword, expand your radius, or clear filters.</div>'
          + '</div>';
      }
      if (pagEl) pagEl.classList.add('d-none');
      return;
    }

    // Build cards HTML
    var cards = data.listings.map(function(l) {
      var price = parseFloat(l.price) || 0;
      var pd    = {};
      try { pd = JSON.parse(l.price_breakdown || '{}'); } catch(e){}
      var billing  = pd.billing || 'once';
      var priceHtml = price > 0
        ? '₹' + Math.round(price).toLocaleString('en-IN')
          + (billing === 'monthly' ? '<span class="cnd-price-period">/month</span>' : '')
        : '<span class="cnd-badge-free">FREE</span>';

      var stars = '';
      if (l.avg_rating) {
        var r = Math.round(l.avg_rating);
        for (var s=1;s<=5;s++) stars += (s<=r ? '★' : '☆');
      }
      var distBadge = l.distance_km != null
        ? '<span class="ms-auto cnd-badge-dist"><i class="bi bi-geo-alt" aria-hidden="true"></i> ' + l.distance_km + ' km</span>'
        : '';
      // Normalise image path
      var rawImg = l.cover_image || '';
      rawImg = rawImg.replace(/^uploads[\/\\]listings[\/\\]/i, '').replace(/^uploads[\/\\]/i, '');
      var img = rawImg
        ? BASE + 'uploads/listings/' + rawImg
        : BASE + 'assets/frontend/img/class-placeholder.jpg';
      
      var trial = l.free_trial ? '<span class="cnd-badge-trial">Free Trial</span>' : '';
      var cat   = l.category_name ? '<span class="badge rounded-pill" style="background:var(--cnd-pink-soft);color:var(--cnd-pink);font-weight:600;font-size:.68rem;">' + esc(l.category_name) + '</span>' : '';
      var subcat = l.subcategory_names ? '<span class="text-muted small border-start ps-2" style="font-size: 0.65rem;">' + esc(l.subcategory_names) + '</span>' : '';
      var rating = l.avg_rating ? '<span class="cnd-meta-rating"><span class="cnd-stars">' + stars + '</span> <span class="small fw-600">' + l.avg_rating + '</span> <span class="text-muted small">(' + (l.review_count||0) + ')</span></span>' : '';
      var verified = l.provider_verified == 1 ? ' <span class="ms-1 text-success" title="Verified Provider"><i class="bi bi-patch-check-fill"></i></span>' : '';

      return '<div class="col" role="listitem">'
        + '<article class="cnd-listing-card h-100" aria-label="' + esc(l.title || 'Class') + '">'
        + '<a href="' + BASE + 'classes/' + l.id + '" class="cnd-card-img-wrap" tabindex="-1" aria-hidden="true">'
        + '<img src="' + img + '" alt="" class="cnd-card-img" loading="lazy" width="400" height="240">'
        + '<span class="cnd-badge-type-sm">' + esc((l.type||'class').charAt(0).toUpperCase()+(l.type||'class').slice(1)) + '</span>'
        + trial + '</a>'
        + '<div class="cnd-card-body">'
        + '<h3 class="cnd-card-title"><a href="' + BASE + 'classes/' + l.id + '" class="cnd-card-title-link">' + esc(l.title||'Untitled') + '</a>' + verified + '</h3>'
        + ((l.locality || l.city) ? '<div class="cnd-card-address"><i class="bi bi-geo-alt-fill text-pink" aria-hidden="true"></i> <span>' + esc([l.locality, l.city].filter(Boolean).join(', ')) + '</span></div>' : (l.address ? '<div class="cnd-card-address"><i class="bi bi-geo-alt-fill text-danger" aria-hidden="true"></i> <span>' + esc((l.address||'').substring(0,50)) + '</span></div>' : ''))
        + '<div class="cnd-card-meta d-flex align-items-center gap-2 flex-wrap mt-2">' + cat + subcat + rating + distBadge + '</div>'
        + '</div>'
        + '<div class="cnd-card-footer d-flex align-items-center justify-content-between">'
        + '<div class="cnd-card-price">' + priceHtml + '</div>'
        + '<a href="' + BASE + 'classes/' + l.id + '" class="cnd-btn-details">View Details</a>'
        + '</div></article></div>';
    });

    // Append mode (load more) or replace
    if (append) {
      var row = grid.querySelector('.row');
      if (row) row.insertAdjacentHTML('beforeend', cards.join(''));
    } else {
      grid.innerHTML = '<div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-3 g-lg-4" role="list">'
        + cards.join('') + '</div>';
    }

    // Load More button
    if (pagEl) {
      if (data.has_more) {
        pagEl.classList.remove('d-none');
        if (loadMore) {
          loadMore.dataset.page = data.page;
          loadMore.dataset.totalPages = data.total_pages;
        }
      } else {
        pagEl.classList.add('d-none');
      }
    }
  }

  /* ── AJAX fetch ─────────────────────────────────────── */
  function doSearch(append) {
    if (!append) { showSkeleton(); state.page = 1; }

    if (_xhr) _xhr.abort();
    var url = API + '?sort=' + encodeURIComponent(state.sort)
      + '&radius=' + state.radius
      + '&page='   + state.page
      + '&limit=12';
    if (state.q)        url += '&q='       + encodeURIComponent(state.q);
    if (state.type)     url += '&type='    + encodeURIComponent(state.type);
    if (state.category)    url += '&category='+ state.category;
    if (state.subcategory && state.subcategory.length > 0) url += '&subcategory='+ state.subcategory.join(',');
    if (state.lat)      url += '&lat='     + state.lat + '&lng=' + state.lng;

    var xhr = new XMLHttpRequest();
    _xhr = xhr;
    xhr.open('GET', url, true);

    // Sync state to URL
    var u = new URL(window.location.href);
    if (state.q) u.searchParams.set('q', state.q); else u.searchParams.delete('q');
    if (state.type) u.searchParams.set('type', state.type); else u.searchParams.delete('type');
    if (state.category) u.searchParams.set('category', state.category); else u.searchParams.delete('category');
    if (state.subcategory && state.subcategory.length > 0) u.searchParams.set('subcategory', state.subcategory.join(',')); else u.searchParams.delete('subcategory');
    if (state.sort && state.sort !== 'relevancy') u.searchParams.set('sort', state.sort); else u.searchParams.delete('sort');
    if (state.radius && state.radius !== 25) u.searchParams.set('radius', state.radius); else u.searchParams.delete('radius');
    if (state.page > 1) u.searchParams.set('page', state.page); else u.searchParams.delete('page');
    window.history.replaceState({}, '', u.toString());

    xhr.onreadystatechange = function() {
      if (xhr.readyState !== 4) return;
      _xhr = null;
      if (xhr.status === 200) {
        try {
          var data = JSON.parse(xhr.responseText);
          renderResults(data, append);
        } catch(e) {
          if (!append) grid.innerHTML = '<div class="alert alert-danger">Error parsing results.</div>';
        }
      } else if (xhr.status !== 0) {
        if (!append) grid.innerHTML = '<div class="alert alert-danger">Error loading results. Please try again.</div>';
      }
    };
    xhr.send();
  }

  /* ── Throttle ───────────────────────────────────────── */
  function throttle(fn, ms) {
    var t; return function() { clearTimeout(t); t = setTimeout(fn, ms); };
  }

  /* ── Event Wiring ───────────────────────────────────── */
  // Search bar live input (debounced 400ms)
  var searchInput = document.getElementById('searchInput');
  if (searchInput) {
    searchInput.addEventListener('input', throttle(function() {
      state.q = searchInput.value.trim();
      // Update browser URL without reload
      var url = new URL(window.location.href);
      if (state.q) url.searchParams.set('q', state.q); else url.searchParams.delete('q');
      window.history.replaceState({}, '', url.toString());
      doSearch(false);
    }, 400));
  }

  // Search form submit (prevent default, call AJAX)
  var form = document.getElementById('searchForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      state.q = searchInput ? searchInput.value.trim() : '';
      doSearch(false);
    });
  }

  // Type tabs
  document.querySelectorAll('#searchTypeTabs .cnd-tab-pill').forEach(function(btn) {
    btn.addEventListener('click', function() {
      document.querySelectorAll('#searchTypeTabs .cnd-tab-pill').forEach(function(b) {
        b.classList.remove('active'); b.setAttribute('aria-pressed','false');
      });
      btn.classList.add('active'); btn.setAttribute('aria-pressed','true');
      state.type = btn.dataset.type || null;
      doSearch(false);
    });
  });

  // Sort select
  var sortSel = document.getElementById('searchSortSelect');
  if (sortSel) {
    sortSel.addEventListener('change', function() {
      state.sort = sortSel.value;
      doSearch(false);
    });
  }

  // Radius slider
  if (radRange) {
    radRange.addEventListener('input', function() {
      state.radius = parseInt(radRange.value, 10);
      if (radLabel) radLabel.textContent = state.radius + ' km';
    });
    radRange.addEventListener('change', throttle(function() {
      state.radius = parseInt(radRange.value, 10);
      doSearch(false);
    }, 300));
  }

  // Category chips
  const searchSubcatRow = document.getElementById('searchSubcatRow');
  
  async function loadSubcatFilters(catId) {
     if(!catId) {
        searchSubcatRow.classList.add('d-none');
        searchSubcatRow.innerHTML = '';
        return;
     }

     try {
        const res = await fetch(`<?= base_url('api/subcategories') ?>?category_id=${catId}`);
        const data = await res.json();
        
        if(data && data.length > 0) {
           let html = '<button class="cnd-cat-chip ' + (state.subcategory.length === 0 ? 'active' : '') + '" data-sub="">All Subcategories</button>';
           data.forEach(sub => {
              var isA = state.subcategory.indexOf(parseInt(sub.id, 10)) > -1;
              html += `<button class="cnd-cat-chip ${isA ? 'active' : ''}" data-sub="${sub.id}">${esc(sub.name)}</button>`;
           });
           searchSubcatRow.innerHTML = html;
           searchSubcatRow.classList.remove('d-none');
           
           bindSearchSubcatClicks();
        } else {
           searchSubcatRow.classList.add('d-none');
        }
     } catch(e) { console.error(e); }
  }

   function bindSearchSubcatClicks() {
       searchSubcatRow.querySelectorAll('.cnd-cat-chip').forEach(btn => {
          btn.addEventListener('click', function() {
             var val = this.dataset.sub;
             if (val === '') {
                 state.subcategory = [];
             } else {
                 val = parseInt(val, 10);
                 var idx = state.subcategory.indexOf(val);
                 if (idx > -1) {
                     state.subcategory.splice(idx, 1);
                 } else {
                     state.subcategory.push(val);
                 }
             }
             
             searchSubcatRow.querySelectorAll('.cnd-cat-chip').forEach(b => {
                 var isA = (b.dataset.sub === '') ? (state.subcategory.length === 0) : (state.subcategory.indexOf(parseInt(b.dataset.sub, 10)) > -1);
                 b.classList.toggle('active', isA);
             });
             doSearch(false);
          });
       });
   }

  document.querySelectorAll('#searchCatRow .cnd-cat-chip').forEach(function(chip) {
    chip.addEventListener('click', function() {
      document.querySelectorAll('#searchCatRow .cnd-cat-chip').forEach(function(c) {
        c.classList.remove('active'); c.setAttribute('aria-pressed','false');
      });
      chip.classList.add('active'); chip.setAttribute('aria-pressed','true');
      state.category = chip.dataset.cat || null;
      state.subcategory = []; // reset subcat on cat change
      loadSubcatFilters(state.category);
      doSearch(false);
    });
  });

  // Initial subcat load if category is preset
  if(state.category) {
     if (searchSubcatRow.children.length > 0) {
         bindSearchSubcatClicks();
     } else {
         loadSubcatFilters(state.category);
     }
  }

  // Load More
  if (loadMore) {
    loadMore.addEventListener('click', function() {
      var p    = parseInt(loadMore.dataset.page, 10) + 1;
      var max  = parseInt(loadMore.dataset.totalPages, 10);
      if (p > max) return;
      state.page = p;
      doSearch(true);
    });
  }

  // Listen for location change
  window.addEventListener('cnd:location-changed', function() {
    state.lat = parseFloat(getCookie('cnd_lat')) || null;
    state.lng = parseFloat(getCookie('cnd_lng')) || null;
    if (state.lat) doSearch(false);
  });

  // Paginate search via pagEl if it exists
  if (pagEl && !pagEl.classList.contains('d-none')) { pagEl.classList.remove('d-none'); }

}());
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
