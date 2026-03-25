<?= $this->extend('frontend/layout/base') ?>
<?= $this->section('css') ?>
<style>
  /* ── Home Page Responsiveness Fixes ── */
  @media (max-width: 767.98px) {
    .cnd-cats-section { padding: 1rem 0; }
    .cnd-type-section { padding: .8rem 0; }
    .cnd-section-title { font-size: 1.25rem; }
  }

  /* Horizontal category bubbles scroll */
  .cnd-cats-row {
    display: flex;
    overflow-x: auto;
    padding: .8rem 0;
    gap: 1.5rem;
    scrollbar-width: none;
    -ms-overflow-style: none;
    justify-content: flex-start;
  }
  .cnd-cats-row::-webkit-scrollbar { display: none; }

  @media (min-width: 768px) {
    .cnd-cats-row {
      justify-content: center;
      flex-wrap: wrap;
      overflow-x: visible;
    }
  }

  .cnd-cat-icon-pill {
    flex: 0 0 auto;
    width: 80px;
    text-align: center;
    transition: transform 0.2s ease;
  }
  .cnd-cat-icon-pill:hover { transform: translateY(-3px); }

  .cnd-cat-icon-bubble {
    width: 60px;
    height: 60px;
    margin: 0 auto .5rem;
    font-size: 1.6rem;
  }

  /* ── Hero carousel (promoted to top) ── */
  .cnd-hero-carousel-section {
    padding: 0;
    background: var(--cnd-dark, #0f0f1a);
    position: relative;
  }

  /* Swiper responsive heights — taller as hero */
  .cnd-hero-swiper {
    border-radius: 0;
    height: 580px;
  }
  @media (max-width: 991.98px) { .cnd-hero-swiper { height: 460px; } }
  @media (max-width: 767.98px) { .cnd-hero-swiper { height: 380px; } }
  @media (max-width: 480px)    { .cnd-hero-swiper { height: 320px; } }

  .cnd-swiper-slide { position: relative; overflow: hidden; }
  .cnd-swiper-bg {
    position: absolute; inset: 0;
    background-size: cover; background-position: center;
    transition: transform 6s ease;
  }
  .swiper-slide-active .cnd-swiper-bg { transform: scale(1.04); }

  .cnd-swiper-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(0deg, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.45) 45%, rgba(0,0,0,0.15) 100%);
  }
  .cnd-swiper-content {
    position: absolute; bottom: 0; left: 0; right: 0;
    padding: 2.5rem 3rem; color: #fff; z-index: 5;
  }
  @media (max-width: 767.98px) { .cnd-swiper-content { padding: 1.5rem; } }

  .cnd-swiper-eyebrow {
    display: inline-flex; align-items: center; gap: .4rem;
    font-size: .72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .12em; color: rgba(255,255,255,.75);
    margin-bottom: .6rem;
  }

  .cnd-swiper-title {
    font-size: clamp(1.4rem, 4.5vw, 2.6rem);
    font-weight: 900; margin-bottom: .6rem;
    text-shadow: 0 3px 14px rgba(0,0,0,0.5);
    line-height: 1.2;
  }
  .cnd-swiper-meta { display: flex; flex-wrap: wrap; gap: 1rem; font-size: .88rem; margin-bottom: 1.5rem; }
  @media (max-width: 767.98px) { .cnd-swiper-meta { gap: .5rem; font-size: .75rem; margin-bottom: 1.1rem; } }

  .cnd-swiper-badges { display: flex; gap: .5rem; margin-bottom: .85rem; }
  .cnd-swiper-badge-type     { background: var(--cnd-pink); border-radius: 4px; padding: .15rem .65rem; font-size: .7rem; font-weight: 700; text-transform: uppercase; }
  .cnd-swiper-badge-featured { background: var(--cnd-gold); color: var(--cnd-dark); border-radius: 4px; padding: .15rem .65rem; font-size: .7rem; font-weight: 700; }
  .cnd-swiper-badge-trending  { background: #ff4d4d; color: #fff; border-radius: 4px; padding: .15rem .65rem; font-size: .7rem; font-weight: 700; }

  .cnd-swiper-cta {
    background: var(--cnd-pink) !important; color: #fff !important;
    border-radius: var(--cnd-radius-pill) !important;
    padding: .65rem 1.75rem !important; font-weight: 700 !important;
    font-size: .95rem !important;
    box-shadow: 0 4px 18px rgba(255, 104, 180, 0.45) !important;
    border: none !important;
  }
  @media (max-width: 767.98px) { .cnd-swiper-cta { padding: .5rem 1.2rem !important; font-size: .82rem !important; } }

  /* Search overlay on hero — now just a prominent "Find Classes" button */
  .cnd-hero-search-bar {
    position: absolute; bottom: 2.5rem; right: 3rem;
    z-index: 10;
  }
  @media (max-width: 991.98px) { .cnd-hero-search-bar { bottom: 2rem; right: 2rem; } }
  @media (max-width: 768px) { .cnd-hero-search-bar { display: none; } }
  
  .cnd-hero-search-bar .btn-hero-find {
    background: #fff; color: var(--cnd-dark);
    font-weight: 700; border-radius: var(--cnd-radius-pill);
    padding: .7rem 2.2rem; border: none; white-space: nowrap;
    font-size: 1rem;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
    transition: all 0.3s ease;
  }
  .cnd-hero-search-bar .btn-hero-find:hover { 
    transform: translateY(-3px);
    box-shadow: 0 8px 30px rgba(0,0,0,0.4);
    background: var(--cnd-gold);
  }

  /* Empty hero */
  .cnd-hero-empty-bg {
    background: linear-gradient(135deg, #1a1040 0%, #2d1b69 50%, #3d0d4e 100%);
    height: 580px; display: flex; align-items: center; justify-content: center;
    position: relative; overflow: hidden;
  }
  @media (max-width: 767.98px) { .cnd-hero-empty-bg { height: 340px; } }
  .cnd-hero-empty-bg::before {
    content: '';
    position: absolute; inset: 0;
    background: radial-gradient(ellipse 60% 70% at 50% 50%, rgba(109,40,217,.4) 0%, transparent 70%);
  }
  .cnd-hero-empty-inner { position: relative; text-align: center; color: #fff; padding: 2rem; }
  .cnd-hero-empty-inner h1 { font-size: clamp(1.6rem, 5vw, 3rem); font-weight: 900; margin-bottom: 1rem; }
  .cnd-hero-empty-inner p  { font-size: 1.05rem; opacity: .8; margin-bottom: 1.5rem; }

  /* Progress bar */
  .cnd-swiper-progress { height: 3px; background: rgba(255,255,255,.2); position: relative; z-index: 20; }
  .cnd-swiper-progress-bar { height: 100%; background: var(--cnd-pink); width: 0; transition: width linear; }

  /* Custom arrow styles for hero */
  .cnd-swiper-arrow {
    background: rgba(255,255,255,.15) !important;
    backdrop-filter: blur(6px);
    border: 1px solid rgba(255,255,255,.25) !important;
    border-radius: 50% !important;
    width: 48px !important; height: 48px !important;
    transition: background .2s;
  }
  .cnd-swiper-arrow:hover { background: rgba(255,255,255,.3) !important; }
  .cnd-swiper-arrow::after { font-size: 1rem !important; color: #fff !important; font-weight: 700 !important; }

  /* Slide counter chip */
  .cnd-swiper-num {
    position: absolute; top: 1.2rem; right: 1.5rem; z-index: 10;
    background: rgba(0,0,0,.45); backdrop-filter: blur(4px);
    color: #fff; font-size: .75rem; font-weight: 700;
    border-radius: 20px; padding: .25rem .75rem;
    border: 1px solid rgba(255,255,255,.2);
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!--
  HOME PAGE — app/Views/frontend/home.php
  ─────────────────────────────────────────────────────────────
  Theme: Purple→Pink→Yellow gradient
  Sections:
  1. Hero Carousel (admin-managed)
  2. Category Icon Bubbles
  3. Regular Classes Near You (4 cards)
  4. Workshops Near You (4 cards)
  5. Courses Near You (4 cards)
  ─────────────────────────────────────────────────────────────
-->

<!-- ══ 1. HERO CAROUSEL (admin-managed, Swiper.js) ════════════ -->
<section class="cnd-hero-carousel-section" aria-labelledby="hero-carousel-heading" id="hero">

  <?php if (empty($featured_listings)): ?>

    <!-- Empty hero state — shows when no slides available -->
    <div class="cnd-hero-empty-bg" role="banner">
      <div class="cnd-hero-empty-inner">
        <h1 id="hero-carousel-heading">
          Find the <span class="cnd-text-gradient">best classes</span><br>
          for your child
        </h1>
        <p>Discover dance, sports, arts, coding, and more — from trusted local providers.</p>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <button
            class="btn btn-lg cnd-btn-primary"
            type="button"
            data-bs-toggle="modal"
            data-bs-target="#locationModal"
            id="hero-location-btn"
            aria-label="Enter your location to see classes near you">
            <i class="bi bi-geo-alt-fill me-2" aria-hidden="true"></i> Set Location
          </button>
          <a href="<?= base_url('classes') ?>" class="btn btn-lg cnd-btn-gold" id="hero-find-btn">
            <i class="bi bi-search me-1" aria-hidden="true"></i> Browse Classes
          </a>
        </div>
      </div>
    </div>

  <?php else: ?>

    <!-- ── Auto-play progress bar ── -->
    <div class="cnd-swiper-progress" id="swiperProgress" aria-hidden="true">
      <div class="cnd-swiper-progress-bar" id="swiperProgressBar"></div>
    </div>

    <div class="swiper cnd-hero-swiper" id="featuredSwiper"
         aria-label="Featured classes — use arrow keys or swipe to navigate"
         aria-labelledby="hero-carousel-heading"
         aria-roledescription="carousel">

      <div class="swiper-wrapper">
        <?php foreach ($featured_listings as $fi => $fl):
          $imgUrl    = listing_img_url($fl['cover_image'] ?? '');
          $detailUrl = base_url('classes/' . esc($fl['listing_id'] ?? ($fl['id'] ?? '#')));
          $rating    = (float)($fl['avg_rating']  ?? 0);
          $reviews   = (int)  ($fl['review_count'] ?? 0);
          $price     = (float)($fl['price']        ?? 0);
          $dist      = $fl['distance_km'] ?? null;
          $source    = $fl['source']       ?? 'algo';
          $type      = ucfirst($fl['type'] ?? 'class');
        ?>
        <div class="swiper-slide cnd-swiper-slide"
             role="group"
             aria-roledescription="slide"
             aria-label="Slide <?= $fi + 1 ?> of <?= count($featured_listings) ?>: <?= esc($fl['title'] ?? '') ?>">

          <!-- Background image -->
          <div class="cnd-swiper-bg"
               style="background-image:url('<?= $imgUrl ?>')"
               aria-hidden="true">
          </div>

          <!-- Gradient overlay -->
          <div class="cnd-swiper-overlay" aria-hidden="true"></div>

          <!-- Slide counter -->
          <div class="cnd-swiper-num" aria-hidden="true">
            <?= $fi + 1 ?> / <?= count($featured_listings) ?>
          </div>

          <!-- Content -->
          <div class="cnd-swiper-content">

            <!-- Eyebrow -->
            <div class="cnd-swiper-eyebrow">
              <i class="bi bi-stars" aria-hidden="true"></i>
              <?php if ($fi === 0): ?><h1 id="hero-carousel-heading" style="display:inline;font-size:inherit;font-weight:inherit;margin:0;"><?php endif; ?>
              <?php if ($fi === 0): ?>Featured <?= esc($type) ?><?php else: ?>Featured<?php endif; ?>
              <?php if ($fi === 0): ?></h1><?php endif; ?>
            </div>

            <!-- Badges -->
            <div class="cnd-swiper-badges">
              <span class="cnd-swiper-badge-type"><?= esc($type) ?></span>
              <?php if ($source === 'admin'): ?>
                <span class="cnd-swiper-badge-featured">
                  <i class="bi bi-patch-check-fill" aria-hidden="true"></i> Featured
                </span>
              <?php else: ?>
                <span class="cnd-swiper-badge-trending">
                  <i class="bi bi-fire" aria-hidden="true"></i> Trending
                </span>
              <?php endif; ?>
              <?php if (!empty($fl['provider_verified'])): ?>
                <span class="badge bg-success rounded-1 ms-1 d-inline-flex align-items-center gap-1" style="font-size:0.7rem; font-weight:700;">
                   <i class="bi bi-patch-check-fill"></i> VERIFIED
                </span>
              <?php endif; ?>
            </div>

            <!-- Title -->
            <p class="cnd-swiper-title"><?= esc($fl['title'] ?? '') ?></p>

            <!-- Meta -->
            <div class="cnd-swiper-meta">
              <?php if (!empty($fl['category_name'])): ?>
              <span class="cnd-swiper-meta-cat">
                <i class="bi bi-tag-fill" aria-hidden="true"></i>
                <?= esc($fl['category_name']) ?>
              </span>
              <?php endif; ?>

              <?php if ($rating >= 1): ?>
              <span class="cnd-swiper-meta-rating" aria-label="Rated <?= number_format($rating,1) ?> out of 5">
                <?php for ($s=1;$s<=5;$s++) echo $s<=(int)round($rating)
                     ? '<i class="bi bi-star-fill" aria-hidden="true"></i>'
                     : '<i class="bi bi-star" aria-hidden="true"></i>'; ?>
                <strong><?= number_format($rating,1) ?></strong>
                <span class="opacity-75">(<?= $reviews ?>)</span>
              </span>
              <?php endif; ?>

              <?php if ($price > 0): ?>
              <span class="cnd-swiper-meta-price">
                <i class="bi bi-currency-rupee" aria-hidden="true"></i><?= number_format($price) ?>
                <span class="opacity-75">/ session</span>
              </span>
              <?php else: ?>
              <span class="cnd-swiper-meta-free">Free</span>
              <?php endif; ?>

              <?php if ($dist !== null): ?>
              <span class="cnd-swiper-meta-dist">
                <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
                <?= number_format($dist, 1) ?> km away
              </span>
              <?php endif; ?>
            </div>

            <!-- CTA -->
            <a href="<?= $detailUrl ?>"
               class="btn cnd-swiper-cta"
               aria-label="View details for <?= esc($fl['title'] ?? 'this class') ?>">
              View Details
              <i class="bi bi-arrow-right ms-1" aria-hidden="true"></i>
            </a>

          </div><!-- /.cnd-swiper-content -->

        </div><!-- /.swiper-slide -->
        <?php endforeach; ?>
      </div><!-- /.swiper-wrapper -->

      <!-- Navigation Arrows -->
      <button class="swiper-button-prev cnd-swiper-arrow cnd-swiper-arrow-prev"
              aria-label="Previous slide"></button>
      <button class="swiper-button-next cnd-swiper-arrow cnd-swiper-arrow-next"
              aria-label="Next slide"></button>

      <!-- Pagination dots -->
      <div class="swiper-pagination cnd-swiper-pagination"
           role="tablist"
           aria-label="Go to slide"></div>

    </div><!-- /.swiper -->

    <!-- Desktop search shortcut overlaid on hero (right side) -->
    <div class="cnd-hero-search-bar" aria-hidden="true">
      <a href="<?= base_url('classes') ?>" class="btn btn-hero-find">
        <i class="bi bi-search me-2"></i> Find Classes Near You
      </a>
    </div>

  <?php endif; ?>

</section>

<?php /* Pass slide data to JS for AJAX refresh after location change */ ?>
<script id="carouselSlidesData" type="application/json">
<?= json_encode(array_map(function($fl) {
  return [
    'listing_id'    => $fl['listing_id'] ?? ($fl['id'] ?? null),
    'title'         => $fl['title']         ?? '',
    'type'          => $fl['type']          ?? '',
    'category_name' => $fl['category_name'] ?? '',
    'cover_image'   => $fl['cover_image']   ?? null,
    'provider_verified' => $fl['provider_verified'] ?? 0,
    'avg_rating'    => $fl['avg_rating']    ?? 0,
    'review_count'  => $fl['review_count']  ?? 0,
    'price'         => $fl['price']         ?? 0,
    'distance_km'   => $fl['distance_km']   ?? null,
    'source'        => $fl['source']        ?? 'algo',
    'address'       => $fl['address']       ?? '',
  ];
}, $featured_listings ?? []), JSON_HEX_TAG | JSON_HEX_AMP) ?>
</script>

<!-- ══ 2. CATEGORY ICON BUBBLES ══════════════════════════════ -->
<section class="cnd-cats-section" aria-label="Browse by category">
  <div class="container-fluid px-3 px-lg-5">
      <div class="cnd-cats-row" role="list">
        <?php
        $cat_icons = [
          'music'     => ['cls' => 'bubble-music'],
          'performing' => ['cls' => 'bubble-dance'],
          'sports'    => ['cls' => 'bubble-sports'],
          'fitness'   => ['cls' => 'bubble-sports'],
          'coding'    => ['cls' => 'bubble-coding'],
          'technology' => ['cls' => 'bubble-coding'],
          'art'       => ['cls' => 'bubble-art'],
          'academic'  => ['cls' => 'bubble-tuitions'],
          'brain'     => ['cls' => 'bubble-yoga'],
          'life'      => ['cls' => 'bubble-language'],
          'cooking'   => ['cls' => 'bubble-yoga'],
        ];

        foreach ($categories as $cat):
          $nameLower = strtolower($cat->name);
          $cls = 'bubble-coding'; // default
          foreach($cat_icons as $key => $iconInfo) {
              if (strpos($nameLower, $key) !== false) {
                  $cls = $iconInfo['cls'];
                  break;
              }
          }
          $icon = !empty($cat->icon) ? $cat->icon : 'bi-star';
        ?>
          <a href="<?= base_url('classes?category=' . (int)$cat->id) ?>"
             class="cnd-cat-icon-pill p-3 text-decoration-none"
             role="listitem"
             data-cat-id="<?= (int)$cat->id ?>"
             aria-label="Browse <?= esc($cat->name) ?> classes">
            <span class="cnd-cat-icon-bubble <?= $cls ?> mb-2 shadow-sm">
              <i class="bi <?= $icon ?>" aria-hidden="true"></i>
            </span>
            <div class="fw-600 text-dark small text-truncate"><?= esc($cat->name) ?></div>
          </a>
        <?php endforeach; ?>
      </div>
  </div>
</section>

<!-- Mobile category dropdown (< 480px) -->
<div id="cnd-cat-dropdown-wrap" class="container-fluid px-3 py-2 d-none" style="background:#fff;border-bottom:1px solid var(--cnd-card-border);">
  <label for="homeCatDropdown" class="visually-hidden">Filter by category</label>
  <select class="cnd-cat-dropdown w-100" id="homeCatDropdown" aria-label="Filter by category">
    <option value="">All Categories</option>
    <?php foreach ($categories as $cat): ?>
    <option value="<?= (int)$cat->id ?>"><?= esc($cat->name) ?></option>
    <?php endforeach; ?>
  </select>
</div>

<!-- ══ 3. LISTING SECTIONS (Regular, Workshops, Courses) ══ -->
<section class="cnd-listings-sections pb-5" id="listings">
  <div class="container-fluid px-3 px-lg-5">

    <?php
    $sections = [
        'regular'  => ['title' => 'Regular Classes Near You', 'icon' => 'calendar3'],
        'workshop' => ['title' => 'Workshops Near You',       'icon' => 'lightning-charge'],
        'course'   => ['title' => 'Courses Near You',        'icon' => 'journal-richtext'],
    ];

    foreach ($sections as $type => $info):
    ?>
      <div class="pt-5 mb-4">
        <h2 class="cnd-section-title mb-4 d-flex align-items-center gap-2">
          <i class="bi bi-<?= $info['icon'] ?> text-pink" aria-hidden="true"></i>
          <?= $info['title'] ?>
        </h2>

        <?php if (empty($listings[$type])): ?>
          <div class="text-muted p-4 border rounded-4 text-center bg-light">
            <p class="mb-0">No <?= $type ?>s available near you.</p>
          </div>
        <?php else: ?>
          <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
            <?php foreach ($listings[$type] as $listing):
                $detailUrl = base_url('classes/' . esc($listing['id']));
                $rating    = (float)($listing['avg_rating'] ?? 0);
            ?>
              <div class="col">
                <article class="cnd-listing-card h-100 shadow-sm border-0 rounded-4 overflow-hidden bg-white">
                  <!-- Image -->
                  <div class="position-relative">
                    <a href="<?= $detailUrl ?>">
                      <img src="<?= listing_img_url($listing['cover_image'] ?? '') ?>"
                           class="w-100" alt="<?= esc($listing['title']) ?>"
                           style="height:180px; object-fit:cover;">
                    </a>
                  </div>

                  <!-- Body -->
                  <div class="p-3 d-flex flex-column h-100">
                    <h5 class="fw-700 mb-1 text-truncate d-flex align-items-center gap-1">
                      <a href="<?= $detailUrl ?>" class="text-dark text-decoration-none text-truncate"><?= esc($listing['title']) ?></a>
                      <?php if(!empty($listing['provider_verified'])): ?>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-0 fw-bold d-inline-flex align-items-center" style="font-size: 0.6rem; height: 18px;">
                          <i class="bi bi-patch-check-fill me-1" style="font-size: 0.65rem;"></i> Verified
                        </span>
                      <?php endif; ?>
                    </h5>

                    <!-- Description snippet -->
                    <p class="text-muted small mb-2 line-clamp-2" style="font-size: .82rem; height: 2.5rem; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                      <?= esc(character_limiter(strip_tags($listing['description'] ?? ''), 80)) ?>
                    </p>

                    <div class="mb-2 d-flex align-items-center gap-1 text-muted fw-600" style="font-size:.72rem;">
                      <i class="bi bi-geo-alt-fill text-pink" aria-hidden="true"></i>
                      <span><?= esc(implode(', ', array_filter([$listing['locality'] ?? '', $listing['city'] ?? '']))) ?: 'Multiple Locations' ?></span>
                    </div>

                    <!-- Category & Subcategory -->
                    <div class="mb-2 d-flex align-items-center gap-1 text-muted fw-600" style="font-size:.72rem;">
                      <i class="bi bi-tag-fill text-pink" aria-hidden="true"></i>
                      <span><?= esc($listing['category_name']) ?><?= !empty($listing['subcategory_names']) ? ' &rsaquo; ' . esc($listing['subcategory_names']) : '' ?></span>
                    </div>

                    <!-- Date/Time labels -->
                    <div class="mb-3 d-flex flex-wrap gap-2">
                      <div class="d-flex align-items-center gap-1 text-muted fw-500" style="font-size:.78rem;">
                        <i class="bi bi-calendar-event text-pink" aria-hidden="true"></i>
                        <span>
                          <?= !empty($listing['start_date']) ? date('M d', strtotime($listing['start_date'])) : 'TBA' ?>
                        </span>
                      </div>
                      <div class="d-flex align-items-center gap-1 text-muted fw-500" style="font-size:.78rem;">
                        <i class="bi bi-clock text-info" aria-hidden="true"></i>
                        <span>
                          <?= !empty($listing['class_time']) ? date('h:i A', strtotime($listing['class_time'])) : 'TBA' ?>
                        </span>
                      </div>
                    </div>

                    <!-- Meta: rating + price -->
                    <div class="mt-auto pt-2 border-top d-flex align-items-center justify-content-between">
                      <div class="small fw-600">
                        <i class="bi bi-star-fill text-warning me-1"></i>
                        <?= number_format($rating, 1) ?>
                        <span class="text-muted fw-400">(<?= (int)($listing['review_count'] ?? 0) ?>)</span>
                      </div>
                      <div class="fw-700 text-pink">
                        <?php if ($listing['price'] > 0): ?>
                          ₹<?= number_format($listing['price']) ?><?= $listing['type'] === 'regular' ? '<small class="opacity-75" style="font-size:0.65em;">/mo</small>' : '' ?>
                        <?php else: ?>
                          Free
                        <?php endif; ?>
                      </div>
                    </div>

                    <a href="<?= $detailUrl ?>" class="btn btn-sm cnd-btn-primary w-100 mt-3 rounded-pill">View Details</a>
                  </div>
                </article>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>

  </div>
</section>

<?= $this->endSection() ?>
