<?= $this->extend('frontend/layout/base') ?>
<?= $this->section('css') ?>
<link rel="stylesheet" href="<?= base_url('assets/frontend/css/components.css') ?>">
<style>
/* ══ Browse Page — Pixel-Perfect Figma ══════════════════════════ */
.browse-page { background: #F0EFFE; min-height: 100vh; }

/* ── Page Header ── */
.browse-header {
  background: #F0EFFE;
  padding: 32px 5% 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
}
.browse-header-left h1 {
  font-size: 28px; font-weight: 700; color: #1A1640;
  font-family: 'Outfit', sans-serif; margin-bottom: 4px;
}
.browse-header-left h1 span { color: #3F3590; }
.browse-header-left p { font-size: 14px; color: #6B69A0; margin: 0; }
.browse-header-right {
  display: flex; align-items: center; gap: 10px; flex-shrink: 0;
}
/* Header search bar */
.browse-header-search {
  display: flex; align-items: center; gap: 8px;
  background: #fff; border: 1.5px solid #E0DEF7; border-radius: 50px;
  padding: 10px 18px; transition: border-color 0.2s; min-width: 220px;
}
.browse-header-search:focus-within { border-color: #3F3590; }
.browse-header-search i { color: #6B69A0; font-size: 14px; flex-shrink: 0; }
.browse-header-search input {
  border: none; outline: none; background: transparent;
  font-size: 14px; color: #1A1640; font-family: 'Outfit', sans-serif; width: 100%;
}
/* Filter icon button */
.browse-filter-icon-btn {
  width: 42px; height: 42px; border-radius: 12px;
  background: #fff; border: 1.5px solid #E0DEF7;
  display: flex; align-items: center; justify-content: center;
  color: #3F3590; cursor: pointer; font-size: 16px;
  transition: all 0.2s; flex-shrink: 0;
}
.browse-filter-icon-btn:hover { background: #f0effe; border-color: #3F3590; }

/* ── Type Tabs ── */
.browse-type-tabs {
  display: flex; align-items: center; gap: 8px;
  padding: 0 5% 20px;
  background: #F0EFFE;
  flex-wrap: wrap;
}
.browse-type-tab {
  padding: 8px 20px; border-radius: 50px;
  font-size: 14px; font-weight: 600; cursor: pointer;
  border: 1.5px solid #E0DEF7; background: #fff;
  color: #6B69A0; transition: all 0.2s ease;
  font-family: 'Outfit', sans-serif;
  text-decoration: none; display: inline-block;
}
.browse-type-tab:hover { border-color: #3F3590; color: #3F3590; }
.browse-type-tab.active {
  background: #3F3590; border-color: #3F3590;
  color: #fff; box-shadow: 0 4px 12px rgba(63,53,144,0.25);
}

/* ── Unified Filter Toolbar (type tabs + sort, single row) ── */
.browse-toolbar {
  display: flex; align-items: center;
  padding: 0 5% 20px;
  background: #F0EFFE;
  gap: 8px;
  flex-wrap: nowrap;
  overflow-x: auto;
  scrollbar-width: none;
}
.browse-toolbar::-webkit-scrollbar { display: none; }
/* Type tabs */
.browse-type-tab {
  padding: 9px 18px; border-radius: 50px;
  font-size: 14px; font-weight: 600; cursor: pointer;
  border: 1.5px solid #E0DEF7; background: #fff;
  color: #6B69A0; transition: all 0.2s ease;
  font-family: 'Outfit', sans-serif;
  text-decoration: none; display: inline-block;
  white-space: nowrap; flex-shrink: 0;
}
.browse-type-tab:hover { border-color: #3F3590; color: #3F3590; }
.browse-type-tab.active {
  background: #3F3590; border-color: #3F3590;
  color: #fff; box-shadow: 0 4px 12px rgba(63,53,144,0.25);
}
/* Divider */
.browse-toolbar-sep {
  width: 1px; height: 28px; background: #DDD;
  flex-shrink: 0;
}
/* Sort select */
.browse-sort-select {
  padding: 8px 30px 8px 14px; border-radius: 50px;
  font-size: 13px; font-weight: 600;
  border: 1.5px solid #E0DEF7; background: #fff;
  color: #6B69A0; cursor: pointer;
  font-family: 'Outfit', sans-serif;
  outline: none; appearance: none; -webkit-appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%236B69A0' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 10px center;
  white-space: nowrap; flex-shrink: 0;
}
.browse-sort-select:focus { border-color: #3F3590; }
@media (max-width: 768px) {
  .browse-header { flex-direction: column; align-items: flex-start; }
  .browse-header-right { width: 100%; }
  .browse-header-search { flex: 1; }
}

/* ── Cards Grid ── */
.browse-grid-section { padding: 0 5% 60px; background: #F0EFFE; }
.browse-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 24px;
}
@media (max-width: 1200px) { .browse-grid { grid-template-columns: repeat(3, 1fr); } }
@media (max-width: 900px)  { .browse-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 560px)  { .browse-grid { grid-template-columns: 1fr; } }

/* ── Class Card ── */
.browse-card {
  background: #fff; border-radius: 20px;
  overflow: hidden; cursor: pointer;
  transition: transform 0.2s ease, box-shadow 0.2s ease;
  box-shadow: 0 2px 12px rgba(0,0,0,0.06);
  text-decoration: none; color: inherit; display: block;
}
.browse-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 12px 32px rgba(63,53,144,0.14);
}

/* ── Card Image ── */
.browse-card-img {
  position: relative; width: 100%;
  padding-top: 62%; /* ~16:10 ratio */
  overflow: hidden; background: #eee;
}
.browse-card-img img {
  position: absolute; inset: 0; width: 100%; height: 100%;
  object-fit: cover; transition: transform 0.4s ease;
}
.browse-card:hover .browse-card-img img { transform: scale(1.05); }

/* ── Image Badges ── */
.img-badge-wrap {
  position: absolute; top: 12px; left: 12px;
  display: flex; gap: 6px; flex-wrap: wrap; z-index: 2;
}
.img-badge {
  padding: 4px 10px; border-radius: 50px;
  font-size: 11px; font-weight: 700; color: #fff;
  font-family: 'Outfit', sans-serif; text-transform: capitalize;
}
.img-badge-regular  { background: #3F3590; }
.img-badge-workshop { background: #F472B6; }
.img-badge-course   { background: #6366F1; }
.img-badge-trial    { background: #F9A05E; }
.img-badge-earlybird{ background: #EC4899; }

/* ── Spots Left Badge ── */
.spots-badge {
  position: absolute; bottom: 10px; right: 10px;
  background: #EF4444; color: #fff;
  padding: 4px 10px; border-radius: 50px;
  font-size: 11px; font-weight: 700;
  font-family: 'Outfit', sans-serif; z-index: 2;
}

/* ── Card Body ── */
.browse-card-body { padding: 16px; }

.browse-card-title {
  font-size: 15px; font-weight: 700; color: #1A1640;
  margin-bottom: 4px; line-height: 1.3;
  font-family: 'Outfit', sans-serif;
  display: -webkit-box; -webkit-line-clamp: 2;
  -webkit-box-orient: vertical; overflow: hidden;
}
.browse-card-provider {
  font-size: 12px; color: #6B69A0; margin-bottom: 2px;
  font-family: 'Outfit', sans-serif;
}
.browse-card-address {
  font-size: 12px; color: #6B69A0;
  display: flex; align-items: flex-start; gap: 4px;
  margin-bottom: 10px;
}
.browse-card-address i { font-size: 11px; flex-shrink: 0; margin-top: 2px; }

/* ── Card title row: title left, rating right ── */
.browse-card-title-row {
  display: flex; align-items: flex-start;
  justify-content: space-between; gap: 8px; margin-bottom: 4px;
}
.browse-card-title {
  font-size: 15px; font-weight: 700; color: #1A1640;
  line-height: 1.3; font-family: 'Outfit', sans-serif;
  flex: 1; min-width: 0;
  display: -webkit-box; -webkit-line-clamp: 2;
  -webkit-box-orient: vertical; overflow: hidden;
}
.browse-card-rating-inline {
  display: flex; align-items: center; gap: 3px;
  font-size: 12px; white-space: nowrap; flex-shrink: 0; margin-top: 1px;
}
.browse-card-rating-inline i { color: #F9A05E; font-size: 11px; }
.browse-card-rating-inline strong { color: #1A1640; font-weight: 700; }
.browse-card-rating-inline span { color: #6B69A0; }

/* ── Card Footer ── */
.browse-card-footer {
  display: flex; align-items: center; justify-content: space-between;
  margin-top: 8px; padding-top: 10px;
  border-top: 1px solid #F0EFFE;
}
.browse-card-price {
  font-size: 17px; font-weight: 700; color: #3F3590;
  font-family: 'Outfit', sans-serif;
}
.browse-card-price small {
  font-size: 12px; font-weight: 400; color: #6B69A0;
}
.browse-card-meta {
  display: flex; align-items: center; gap: 4px;
  font-size: 12px; color: #6B69A0;
}
.browse-card-meta i { font-size: 11px; }

/* ── No Results ── */
.browse-no-results {
  grid-column: 1 / -1; text-align: center; padding: 60px 20px;
}
.browse-no-results i { font-size: 3rem; color: #ccc; display: block; margin-bottom: 16px; }
.browse-no-results p { color: #6B69A0; font-size: 15px; }

/* ── Pagination ── */
.browse-pagination {
  display: flex; justify-content: center; gap: 6px;
  padding: 20px 5% 40px; flex-wrap: wrap; background: #F0EFFE;
}
.browse-pagination a, .browse-pagination span {
  width: 38px; height: 38px; border-radius: 10px;
  display: flex; align-items: center; justify-content: center;
  font-size: 14px; font-weight: 600; text-decoration: none;
  border: 1.5px solid #E0DEF7; color: #6B69A0;
  background: #fff; transition: all 0.2s;
  font-family: 'Outfit', sans-serif;
}
.browse-pagination a:hover { border-color: #3F3590; color: #3F3590; }
.browse-pagination .current { background: #3F3590; border-color: #3F3590; color: #fff; }
.browse-pagination .disabled { opacity: 0.4; pointer-events: none; }

/* ── Loading spinner ── */
#browseLoadingSpinner {
  grid-column: 1 / -1; text-align: center; padding: 60px;
  display: none;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="browse-page">

  <?php
    $activeType   = $current_type ?? 'regular';
    $activeSort   = $current_sort ?? 'relevancy';
    $activeRadius = $radius ?? 25;
    $rows         = $listings[$activeType] ?? [];
    $total        = $listings_total ?? 0;
  ?>

  <!-- ── Page Header ── -->
  <div class="browse-header">
    <h1>Browse <span>Classes</span></h1>
    <p><?= number_format($total) ?> classes found</p>
  </div>

  <!-- ── Unified Toolbar: Scrollable left (tabs+pills) + Fixed right (sort+search) ── -->
  <div class="browse-toolbar" id="browseToolbar">

    <!-- LEFT: Type tabs + divider + Category pills (horizontally scrollable) -->
    <div class="browse-toolbar-left">

      <!-- Type Tabs -->
      <?php foreach (['regular' => 'Regular Classes', 'workshop' => 'Workshops', 'course' => 'Courses'] as $tval => $tlabel): ?>
      <a href="<?= base_url('classes?type='.$tval.(!empty($current_category)?'&category='.$current_category:'').(!empty($activeSort)&&$activeSort!=='relevancy'?'&sort='.$activeSort:'')) ?>"
         class="browse-type-tab <?= $activeType === $tval ? 'active' : '' ?>"
         data-type="<?= $tval ?>">
        <?= $tlabel ?>
      </a>
      <?php endforeach; ?>

      <!-- Divider -->
      <span class="browse-toolbar-sep"></span>

      <!-- Category Pills -->
      <?php if (!empty($categories)): ?>
        <a href="<?= base_url('classes?type='.$activeType.($activeSort!=='relevancy'?'&sort='.$activeSort:'')) ?>"
           class="browse-cat-pill <?= empty($current_category) ? 'active' : '' ?>">All</a>
        <?php foreach (array_slice($categories, 0, 8) as $cat): ?>
        <a href="<?= base_url('classes?type='.$activeType.'&category='.(int)$cat['id'].($activeSort!=='relevancy'?'&sort='.$activeSort:'')) ?>"
           class="browse-cat-pill <?= ($current_category ?? null) == $cat['id'] ? 'active' : '' ?>">
          <?= esc($cat['name']) ?>
        </a>
        <?php endforeach; ?>
      <?php endif; ?>

    </div><!-- /.browse-toolbar-left -->

    <!-- RIGHT: Sort + Search (fixed, no wrap) -->
    <div class="browse-toolbar-right">

      <!-- Sort Select -->
      <form action="<?= base_url('classes') ?>" method="GET" id="sortForm" style="display:contents;">
        <input type="hidden" name="type" value="<?= esc($activeType) ?>">
        <?php if (!empty($current_category)): ?><input type="hidden" name="category" value="<?= (int)$current_category ?>"><?php endif; ?>
        <select name="sort" class="browse-sort-select" onchange="this.closest('form').submit()" aria-label="Sort listings">
          <option value="relevancy" <?= $activeSort === 'relevancy' ? 'selected' : '' ?>>Most Relevant</option>
          <option value="rating"    <?= $activeSort === 'rating'    ? 'selected' : '' ?>>Top Rated</option>
          <option value="price_asc" <?= $activeSort === 'price_asc' ? 'selected' : '' ?>>Price: Low &rarr; High</option>
          <option value="price_desc"<?= $activeSort === 'price_desc'? 'selected' : '' ?>>Price: High &rarr; Low</option>
        </select>
      </form>

      <!-- Search -->
      <form action="<?= base_url('classes') ?>" method="GET" class="browse-search-form">
        <input type="hidden" name="type" value="<?= esc($activeType) ?>">
        <?php if (!empty($current_category)): ?><input type="hidden" name="category" value="<?= (int)$current_category ?>"><?php endif; ?>
        <i class="bi bi-search"></i>
        <input type="text" name="q" placeholder="Search classes..." value="<?= esc(service('request')->getGet('q') ?? '') ?>">
      </form>

    </div><!-- /.browse-toolbar-right -->

  </div><!-- /.browse-toolbar -->

  <!-- ── Cards Grid ── -->
  <div class="browse-grid-section">
    <div class="browse-grid" id="browseGrid">

      <?php if (empty($rows)): ?>
      <div class="browse-no-results">
        <?php if (!$location_selected): ?>
          <i class="bi bi-geo-alt"></i>
          <p class="fw-bold mb-2">Set your location to find classes nearby</p>
          <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#locationModal">
            <i class="bi bi-geo-alt-fill me-1"></i> Set Location
          </button>
        <?php else: ?>
          <i class="bi bi-journal-x"></i>
          <p class="fw-bold mb-1">No classes found in your area</p>
          <p class="small">Try a wider radius or browse another category.</p>
        <?php endif; ?>
      </div>

      <?php else: foreach ($rows as $listing):
        $l = is_array($listing) ? $listing : (array)$listing;
        $lid   = $l['id'] ?? 0;
        $ltype = $l['type'] ?? 'regular';
        $price = (float)($l['price'] ?? 0);
        $rating = (float)($l['avg_rating'] ?? 0);
        $reviews = (int)($l['review_count'] ?? 0);
        $totalStudents = (int)($l['total_students'] ?? 0);
        $spotsLeft = ($l['batch_size'] ?? 0) > 0 ? max(0, (int)$l['batch_size'] - $totalStudents) : null;

        // Price unit
        $unit = ($ltype === 'workshop') ? '/session' : (($ltype === 'course') ? '' : '/mo');

        // Date meta
        $dateMeta = '';
        $dateIcon = '';
        if ($ltype === 'workshop' && !empty($l['start_date'])) {
          $dateMeta = date('d M', strtotime($l['start_date']));
          $dateIcon = 'bi-calendar3';
        } elseif ($ltype === 'course' && !empty($l['course_duration'])) {
          $dateMeta = $l['course_duration'] . ' ' . ($l['course_duration_type'] ?? 'Weeks');
          $dateIcon = 'bi-clock';
        } elseif ($ltype === 'course' && !empty($l['end_date'])) {
          $dateMeta = date('d M', strtotime($l['end_date']));
          $dateIcon = 'bi-calendar3';
        }

        $imgUrl = listing_img_url($l['cover_image'] ?? '');
        $hasTrialBadge = !empty($l['free_trial']);
        $hasEarlyBird  = false; // can be extended
      ?>
      <a href="<?= base_url('classes/'.$lid) ?>" class="browse-card">
        <div class="browse-card-img">
          <img src="<?= esc($imgUrl) ?>" alt="<?= esc($l['title'] ?? '') ?>" loading="lazy">

          <!-- Type + Free Trial badges -->
          <div class="img-badge-wrap">
            <span class="img-badge img-badge-<?= esc($ltype) ?>"><?= ucfirst($ltype) ?></span>
            <?php if ($hasTrialBadge): ?>
            <span class="img-badge img-badge-trial">Free Trial</span>
            <?php endif; ?>
          </div>

          <!-- Spots left -->
          <?php if ($spotsLeft !== null && $spotsLeft <= 5 && $spotsLeft > 0): ?>
          <div class="spots-badge"><?= $spotsLeft ?> spots left</div>
          <?php endif; ?>
        </div>

        <div class="browse-card-body">
          <!-- Title -->
          <div class="browse-card-title"><?= esc($l['title'] ?? 'Class') ?></div>

          <!-- Provider · Category -->
          <?php if (!empty($l['provider_name']) || !empty($l['category_name'])): ?>
          <div class="browse-card-provider">
            <?= esc($l['provider_name'] ?? '') ?><?= (!empty($l['provider_name']) && !empty($l['category_name'])) ? ' · ' : '' ?><?= esc($l['category_name'] ?? '') ?>
          </div>
          <?php endif; ?>

          <!-- Address -->
          <?php if (!empty($l['address'])): ?>
          <div class="browse-card-address">
            <i class="bi bi-geo-alt-fill"></i>
            <span><?= esc(character_limiter($l['address'], 40)) ?></span>
          </div>
          <?php endif; ?>

          <!-- Rating -->
          <?php if ($rating > 0): ?>
          <div class="browse-card-rating">
            <i class="bi bi-star-fill"></i>
            <strong><?= number_format($rating, 1) ?></strong>
            <span>(<?= $reviews ?>)</span>
          </div>
          <?php endif; ?>

          <!-- Footer: price + date -->
          <div class="browse-card-footer">
            <div class="browse-card-price">
              ₹<?= number_format($price) ?><small><?= $unit ?></small>
            </div>
            <?php if ($dateMeta): ?>
            <div class="browse-card-meta">
              <i class="bi <?= $dateIcon ?>"></i>
              <?= esc($dateMeta) ?>
            </div>
            <?php endif; ?>
          </div>
        </div>
      </a>
      <?php endforeach; endif; ?>

    </div><!-- /.browse-grid -->
  </div><!-- /.browse-grid-section -->

  <!-- ── Pagination ── -->
  <?php if (!empty($total_pages) && $total_pages > 1):
    $cp = $current_page ?? 1;
    $tp = $total_pages;
    $bq = 'type='.urlencode($activeType).'&sort='.urlencode($activeSort).'&page=';
  ?>
  <div class="browse-pagination">
    <?php if ($cp > 1): ?>
      <a href="<?= base_url('classes?'.$bq.($cp-1)) ?>"><i class="bi bi-chevron-left"></i></a>
    <?php else: ?>
      <span class="disabled"><i class="bi bi-chevron-left"></i></span>
    <?php endif; ?>

    <?php for ($p = max(1, $cp - 2); $p <= min($tp, $cp + 2); $p++): ?>
      <?php if ($p === $cp): ?>
        <span class="current"><?= $p ?></span>
      <?php else: ?>
        <a href="<?= base_url('classes?'.$bq.$p) ?>"><?= $p ?></a>
      <?php endif; ?>
    <?php endfor; ?>

    <?php if ($cp < $tp): ?>
      <a href="<?= base_url('classes?'.$bq.($cp+1)) ?>"><i class="bi bi-chevron-right"></i></a>
    <?php else: ?>
      <span class="disabled"><i class="bi bi-chevron-right"></i></span>
    <?php endif; ?>
  </div>
  <?php endif; ?>

</div><!-- /.browse-page -->

<!-- ── Filter Modal ── -->
<div class="modal fade" id="browseFilterModal" tabindex="-1" aria-labelledby="browseFilterLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border-radius: 20px; border: none;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="browseFilterLabel" style="font-family: 'Outfit', sans-serif; color: #1A1640;">Filters</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="filterForm" action="<?= base_url('classes') ?>" method="GET">
          <input type="hidden" name="type" value="<?= esc($activeType) ?>">

          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Sort By</label>
            <select name="sort" class="form-select" style="border-radius: 12px;">
              <option value="relevancy" <?= $activeSort === 'relevancy' ? 'selected' : '' ?>>Most Relevant</option>
              <option value="rating"    <?= $activeSort === 'rating'    ? 'selected' : '' ?>>Top Rated</option>
              <option value="price_asc" <?= $activeSort === 'price_asc' ? 'selected' : '' ?>>Price: Low → High</option>
              <option value="price_desc"<?= $activeSort === 'price_desc'? 'selected' : '' ?>>Price: High → Low</option>
            </select>
          </div>

          <?php if (!empty($categories)): ?>
          <div class="mb-3">
            <label class="form-label small fw-bold text-muted">Category</label>
            <div class="d-flex flex-wrap gap-2">
              <a href="<?= base_url('classes?type='.$activeType) ?>" class="btn btn-sm rounded-pill <?= empty($current_category) ? 'btn-primary' : 'btn-outline-secondary' ?>">All</a>
              <?php foreach ($categories as $cat): ?>
              <a href="<?= base_url('classes?type='.$activeType.'&category='.$cat['id']) ?>"
                 class="btn btn-sm rounded-pill <?= ($current_category ?? null) == $cat['id'] ? 'btn-primary' : 'btn-outline-secondary' ?>">
                <?= esc($cat['name']) ?>
              </a>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endif; ?>

          <div class="d-flex gap-2 mt-4">
            <button type="submit" class="btn btn-primary rounded-pill flex-grow-1 py-2" style="font-family: 'Outfit', sans-serif; font-weight: 700;">Apply Filters</button>
            <a href="<?= base_url('classes?type='.$activeType) ?>" class="btn btn-light rounded-pill px-4 py-2">Reset</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
(function() {
  'use strict';

  // Highlight active type tab
  document.querySelectorAll('.browse-type-tab').forEach(function(tab) {
    tab.addEventListener('click', function(e) {
      document.querySelectorAll('.browse-type-tab').forEach(t => t.classList.remove('active'));
      this.classList.add('active');
    });
  });

  // Animate cards on scroll
  if ('IntersectionObserver' in window) {
    const cards = document.querySelectorAll('.browse-card');
    const io = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (e.isIntersecting) {
          e.target.style.opacity = '1';
          e.target.style.transform = 'translateY(0)';
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.08 });

    cards.forEach((c, i) => {
      c.style.opacity = '0';
      c.style.transform = 'translateY(20px)';
      c.style.transition = `opacity 0.4s ease ${i * 0.05}s, transform 0.4s ease ${i * 0.05}s`;
      io.observe(c);
    });
  }
})();
</script>
<?= $this->endSection() ?>
