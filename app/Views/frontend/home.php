<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('css') ?>
<?php /* ── Load shared design-token stylesheet ── */ ?>
<link rel="stylesheet" href="<?= base_url('assets/frontend/css/components.css') ?>">
<style>
  /*
   * home.php — page-only overrides.
   * All base tokens live in components.css.
   */

  /* Bubble colours (category pill strip) */
  .bubble-dance   { background: linear-gradient(135deg,#7778F6,#3F3590); color:#fff; }
  .bubble-music   { background: linear-gradient(135deg,#FF68B4,#e14d91); color:#fff; }
  .bubble-art     { background: linear-gradient(135deg,#F9A05E,#e8903e); color:#fff; }
  .bubble-sports  { background: linear-gradient(135deg,#3F3590,#5b6bf1); color:#fff; }
  .bubble-coding  { background: linear-gradient(135deg,#FF68B4,#7778F6); color:#fff; }
  .bubble-tuitions{ background: linear-gradient(135deg,#F9A05E,#FF68B4); color:#fff; }
  .bubble-yoga    { background: linear-gradient(135deg,#7778F6,#FF68B4); color:#fff; }
  .bubble-language{ background: linear-gradient(135deg,#3F3590,#F9A05E); color:#fff; }

  .cnd-btn-book {
    background: #f0effe;
    color: var(--cnd-primary);
    border: none;
    padding: 10px 20px;
  }

  /* ── Promo Banner ── */
  .home-promo-section {
    padding: 20px 5%;
    background: var(--cnd-bg);
  }
  .home-promo-inner {
    background: linear-gradient(90deg, #F9A05E 0%, #F67098 100%);
    border-radius: 24px;
    padding: 48px 60px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 40px;
    flex-wrap: wrap;
  }
  .home-promo-text h2 {
    font-size: clamp(1.4rem, 3vw, 1.75rem);
    font-weight: 700;
    color: #fff;
    margin-bottom: 12px;
    font-family: 'Outfit', sans-serif;
  }
  .home-promo-text p {
    font-size: 1rem;
    color: rgba(255,255,255,0.9);
    margin-bottom: 24px;
    line-height: 1.6;
    max-width: 480px;
    font-family: 'Outfit', sans-serif;
  }
  .home-promo-btn {
    display: inline-block;
    background: #fff;
    color: #3F3590;
    padding: 13px 28px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.95rem;
    text-decoration: none;
    font-family: 'Outfit', sans-serif;
    transition: transform 0.2s, box-shadow 0.2s;
    white-space: nowrap;
  }
  .home-promo-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    color: #3F3590;
  }
  .home-promo-images {
    display: flex;
    gap: 16px;
    align-items: center;
    flex-shrink: 0;
  }
  .home-promo-images img {
    width: 130px;
    height: 130px;
    object-fit: cover;
    border-radius: 18px;
    border: 3px solid rgba(255,255,255,0.4);
  }

  /* ── Provider CTA ── */
  .home-provider-section {
    background-color: #F0EFFE;
    padding: clamp(48px, 8vw, 100px) 5%;
  }
  .home-provider-card {
    background-color: #1A1640;
    border-radius: clamp(24px, 5vw, 48px);
    padding: clamp(40px, 6vw, 80px) 40px;
    text-align: center;
    color: #FFFFFF;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    max-width: 900px;
    margin: 0 auto;
  }
  .home-provider-card h2 {
    font-size: clamp(1.6rem, 4vw, 2.5rem);
    font-weight: 700;
    margin-bottom: 20px;
    font-family: 'Outfit', sans-serif;
  }
  .home-provider-card p {
    font-size: clamp(0.95rem, 2vw, 1.1rem);
    color: rgba(255,255,255,0.8);
    margin-bottom: 36px;
    max-width: 560px;
    margin-left: auto;
    margin-right: auto;
    line-height: 1.6;
    font-family: 'Outfit', sans-serif;
  }
  .home-provider-cta-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(90deg, #F38472 0%, #F67098 100%);
    color: #FFFFFF;
    padding: clamp(13px, 2vw, 17px) clamp(28px, 4vw, 44px);
    border-radius: 50px;
    text-decoration: none;
    font-weight: 600;
    font-size: clamp(0.95rem, 2vw, 1.05rem);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: none;
    box-shadow: 0 10px 20px rgba(243,132,114,0.3);
    font-family: 'Outfit', sans-serif;
  }
  .home-provider-cta-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 16px 32px rgba(243,132,114,0.4);
    color: #fff;
  }

  /* ── Testimonials ── */
  .home-testimonials-section {
    background: var(--cnd-bg);
    padding: clamp(40px, 6vw, 80px) 5%;
  }
  .home-testimonials-section h2 {
    font-size: clamp(1.4rem, 3vw, 1.75rem);
    font-weight: 700;
    color: #1A1640;
    margin-bottom: clamp(24px, 4vw, 40px);
    font-family: 'Outfit', sans-serif;
    text-align: center;
  }

  /* Responsive overrides */
  @media (max-width: 767px) {
    .home-promo-inner  { padding: 32px 24px; border-radius: 18px; }
    .home-promo-images { display: none; }
    .home-provider-card { padding: 36px 24px; }
  }
  @media (max-width: 480px) {
    .home-promo-section { padding: 16px 4%; }
    .home-promo-inner   { padding: 26px 20px; }
    .home-provider-section { padding: 40px 4%; }
  }

  /* Listings section padding fix on mobile */
  @media (max-width: 767px) {
    .cnd-listings-sections > .container-fluid {
      padding-left: 4% !important;
      padding-right: 4% !important;
    }
  }
</style>
<?= $this->endSection() ?>





<?= $this->section('content') ?>

<?php
/* ═══════════════════════════════════════════════════════════════
   BUILD HERO CONFIG
   Merges controller-provided data with smart defaults.
   The heroImage falls back: featured_listings[0] → static asset.
═══════════════════════════════════════════════════════════════ */
$heroImgUrl = '';
if (!empty($featured_listings)) {
    $heroImgUrl = listing_img_url($featured_listings[0]['cover_image'] ?? '');
}
if (empty($heroImgUrl) || $heroImgUrl === listing_img_url('')) {
    // Exact match image from demo preview
    $heroImgUrl = 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=1000';
}

$heroConfig = [
    'title'            => 'Learn Something<br><span class="hero-h1-accent">Next Door</span>',
    'subtitle'         => 'Find the best local classes, courses, and workshops in your neighbourhood. From dance to coding — all just a doorstep away.',
    'searchAction'     => base_url('classes'),
    'searchParam'      => 'q',
    'searchPlaceholder'=> 'What do you want to learn?',
    'searchBtnText'    => 'Search',
    'heroImage'        => $heroImgUrl,
    'badges'           => [
        [
            'position'  => 'bottom-left',
            'iconClass' => 'bi-grid-fill',
            'iconBg'    => 'linear-gradient(135deg,#F9A05E,#FF68B4)',
            'title'     => '8+ Categories',
            'subtitle'  => 'To explore',
            'animDelay' => '0s',
        ],
        [
            'position'  => 'top-right',
            'iconClass' => 'bi-star-fill',
            'iconBg'    => 'linear-gradient(135deg,#3F3590,#7778F6)',
            'title'     => '4.7+ Rating',
            'subtitle'  => 'Avg. reviews',
            'animDelay' => '1s',
        ],
    ],
];
?>

<?php /* ── 1. HERO ─────────────────────────────────────────────── */ ?>
<?= $this->include('frontend/partials/hero_section', ['hero' => $heroConfig]) ?>

<?php /* Keep carousel slot alive for app.js location-refresh compatibility */ ?>
<script id="carouselSlidesData" type="application/json">[]</script>


<?php
/* ── 2. CATEGORIES SECTION — card-based grid (exact match: demo_preview.html) ─
 *
 * Keyword → icon/colour map. No DB schema change needed.
 * Falls back to 6 static demo cards when no DB categories exist.
 */
$_catIconMap = [
    'dance'      => ['icon'=>'bi-music-note-beamed',   'iconBg'=>'rgba(63,53,144,.1)',  'iconColor'=>'#3F3590'],
    'performing' => ['icon'=>'♫',  'iconBg'=>'rgba(63,53,144,.1)',  'iconColor'=>'#3F3590'],
    'music'      => ['icon'=>'♫',  'iconBg'=>'rgba(119,120,246,.1)','iconColor'=>'#7778F6'],
    'art'        => ['icon'=>'🎨', 'iconBg'=>'rgba(119,120,246,.1)','iconColor'=>'#7778F6'],
    'craft'      => ['icon'=>'🎨', 'iconBg'=>'rgba(249,160,94,.1)', 'iconColor'=>'#F9A05E'],
    'sport'      => ['icon'=>'⚽', 'iconBg'=>'rgba(249,160,94,.1)', 'iconColor'=>'#F9A05E'],
    'fitness'    => ['icon'=>'⚽', 'iconBg'=>'rgba(249,160,94,.1)', 'iconColor'=>'#F9A05E'],
    'yoga'       => ['icon'=>'🧘', 'iconBg'=>'rgba(119,120,246,.1)','iconColor'=>'#7778F6'],
    'coding'     => ['icon'=>'💻', 'iconBg'=>'rgba(255,104,180,.1)','iconColor'=>'#FF68B4'],
    'tech'       => ['icon'=>'💻', 'iconBg'=>'rgba(255,104,180,.1)','iconColor'=>'#FF68B4'],
    'brain'      => ['icon'=>'🧠', 'iconBg'=>'rgba(63,53,144,.1)',  'iconColor'=>'#3F3590'],
    'academic'   => ['icon'=>'📚', 'iconBg'=>'rgba(63,53,144,.1)',  'iconColor'=>'#3F3590'],
    'language'   => ['icon'=>'💬', 'iconBg'=>'rgba(119,120,246,.1)','iconColor'=>'#7778F6'],
    'cooking'    => ['icon'=>'🍳', 'iconBg'=>'rgba(249,160,94,.1)', 'iconColor'=>'#F9A05E'],
    'culinary'   => ['icon'=>'🍳', 'iconBg'=>'rgba(249,160,94,.1)', 'iconColor'=>'#F9A05E'],
    'default'    => ['icon'=>'bi-star-fill',           'iconBg'=>'rgba(63,53,144,.1)',  'iconColor'=>'#3F3590'],
];
$_staticDemoCats = [
    ['id'=>0,'name'=>'Performing Arts','icon'=>'♫', 'iconBg'=>'rgba(63,53,144,.1)',  'iconColor'=>'#3F3590'],
    ['id'=>0,'name'=>'Art & Craft',    'icon'=>'🎨','iconBg'=>'rgba(119,120,246,.1)','iconColor'=>'#7778F6'],
    ['id'=>0,'name'=>'Sports Lab',     'icon'=>'⚽','iconBg'=>'rgba(249,160,94,.1)', 'iconColor'=>'#F9A05E'],
    ['id'=>0,'name'=>'Tech & Coding',  'icon'=>'💻','iconBg'=>'rgba(255,104,180,.1)','iconColor'=>'#FF68B4'],
    ['id'=>0,'name'=>'Brain Boost',    'icon'=>'🧠','iconBg'=>'rgba(63,53,144,.1)',  'iconColor'=>'#3F3590'],
    ['id'=>0,'name'=>'Cooking',        'icon'=>'🍳','iconBg'=>'rgba(249,160,94,.1)', 'iconColor'=>'#F9A05E'],
];
$_catList = $_staticDemoCats;
?>

<section class="cnd-categories-section" id="categories" aria-label="Explore Categories">
  <div class="cnd-section-inner">

    <div class="cnd-section-header">
      <h2 class="cnd-section-title mb-0" id="categories-heading">Explore Categories</h2>
      <a href="<?= base_url('classes') ?>" class="cnd-view-all" aria-label="View All Categories">
        View All &rsaquo;
      </a>
    </div>

    <div class="categories-grid" role="list" aria-labelledby="categories-heading">
      <?php foreach ($_catList as $_rawCat):
        /* Normalise DB object or plain array */
        $_cat     = is_array($_rawCat) ? $_rawCat : (array)$_rawCat;
        $_catId   = $_cat['id']   ?? 0;
        $_catName = $_cat['name'] ?? 'Category';
        $_catUrl  = $_catId > 0 ? base_url('classes?category=' . (int)$_catId) : '#';

        /* Resolve style */
        if (isset($_cat['iconBg'])) {
            /* Pre-normalised (static demo) */
            $_icon    = $_cat['icon']      ?? 'bi-star-fill';
            $_iconBg  = $_cat['iconBg']    ?? 'rgba(63,53,144,.1)';
            $_iconClr = $_cat['iconColor'] ?? '#3F3590';
        } else {
            /* DB object — keyword match */
            $_nameLo  = strtolower($_catName);
            $_style   = $_catIconMap['default'];
            foreach ($_catIconMap as $_kw => $_st) {
                if ($_kw !== 'default' && strpos($_nameLo, $_kw) !== false) { $_style = $_st; break; }
            }
            /* DB icon column overrides keyword */
            $_rawIcon = $_cat['icon'] ?? '';
            $_icon    = $_rawIcon ?: $_style['icon'];
            if ($_rawIcon && strpos($_rawIcon, 'bi-') !== 0) $_icon = 'bi-' . ltrim($_rawIcon, 'bi-');
            $_iconBg  = $_style['iconBg'];
            $_iconClr = $_style['iconColor'];
        }
      ?>
      <div role="listitem">
        <a href="<?= esc($_catUrl) ?>" class="cat-card-link" aria-label="Browse <?= esc($_catName) ?> classes">
          <div class="cat-card">
            <div class="cat-icon"
                 style="color:<?= esc($_iconClr,'attr') ?>;">
              <?php if (mb_strlen($_icon) < 5): ?>
                <span style="font-family: initial !important;"><?= esc($_icon) ?></span>
              <?php else: ?>
                <i class="bi <?= esc($_icon) ?>" aria-hidden="true"></i>
              <?php endif; ?>
            </div>
            <h3><?= esc($_catName) ?></h3>
          </div>
        </a>
      </div>
      <?php endforeach; ?>
    </div><!-- /.categories-grid -->

  </div>
</section>

<?php /* Mobile category dropdown (< 480px) */ ?>
<div id="cnd-cat-dropdown-wrap"
     class="container-fluid px-3 py-2 d-none"
     style="background:#fff;border-bottom:1px solid #e8e6f5;">
  <label for="homeCatDropdown" class="visually-hidden">Filter by category</label>
  <select class="cnd-cat-dropdown w-100" id="homeCatDropdown" aria-label="Filter by category">
    <option value="">All Categories</option>
    <?php foreach ($_catList as $_mCat):
        $_mCat = is_array($_mCat) ? $_mCat : (array)$_mCat;
    ?>
    <option value="<?= (int)($_mCat['id'] ?? 0) ?>"><?= esc($_mCat['name'] ?? '') ?></option>
    <?php endforeach; ?>
  </select>
</div>


<?php /* ── 3. TOP RATED CLASSES ───────────────── */ ?>
<section class="cnd-listings-sections pb-5 pt-4" id="top-rated">
  <div class="container-fluid px-3 px-lg-5" style="max-width:1200px;margin:0 auto;">
    
    <div class="cnd-section-header">
      <h2 class="cnd-section-title mb-0">Top Rated Classes</h2>
      <a href="<?= base_url('classes') ?>" class="cnd-view-all">
        View All →
      </a>
    </div>

    <div class="classes-grid" role="list">
      <?php 
      // Use dynamic top_rated if available, else static demo fallback
      $_dynamicRows = !empty($top_rated) ? array_slice($top_rated, 0, 8) : []; 
      
      if (!empty($_dynamicRows)): 
        foreach ($_dynamicRows as $_l):
          $_l    = (array)$_l;
          $_id   = $_l['id'] ?? 0;
          $_type = $_l['type'] ?? 'regular';
          
          // Helper meta
          $_unit = ($_type === 'workshop') ? '/ session' : (($_type === 'course') ? '/ course' : '/ mo');
          $_badgeText = ''; $_badgeClr = 'var(--cnd-primary)';
          if (!empty($_l['is_featured'])) { $_badgeText = 'Featured'; $_badgeClr = '#FF68B4'; }
          elseif (!empty($_l['free_trial'])) { $_badgeText = 'Free Trial'; $_badgeClr = 'var(--cnd-primary)'; }
          elseif ($_type === 'workshop') { $_badgeText = 'Workshop'; $_badgeClr = '#E14D91'; }

          $_rating = (float)($_l['avg_rating'] ?? 0);
          $_reviews = (int)($_l['review_count'] ?? 0);
      ?>
      <div role="listitem">
        <article class="cnd-listing-home-card">
          <div class="cnd-card-img-wrap">
            <img src="<?= listing_img_url($_l['cover_image'] ?? '') ?>" alt="<?= esc($_l['title']) ?>">
            <?php if ($_badgeText): ?>
              <span class="cnd-card-type-badge" style="color:<?= esc($_badgeClr) ?>"><?= esc($_badgeText) ?></span>
            <?php endif; ?>
          </div>
          <div class="cnd-card-body">
            <div class="cnd-card-category-row">
              <span><?= esc($_l['category_name'] ?? 'Class') ?> • <?= ucfirst($_type) ?></span>
              <span class="cnd-card-rating">
                <i class="bi bi-star-fill"></i> <?= number_format($_rating, 1) ?> 
                <span style="color:var(--cnd-text-muted);font-weight:400;font-size:.75rem;">(<?= $_reviews ?>)</span>
              </span>
            </div>
            <a href="<?= base_url('classes/'.$_id) ?>" class="cnd-card-title"><?= esc($_l['title']) ?></a>
            <div class="cnd-card-footer-row">
              <div class="cnd-price-tag">₹<?= number_format((float)($_l['price'] ?? 0)) ?> <small><?= esc($_unit) ?></small></div>
              <a href="<?= base_url('classes/'.$_id) ?>" class="cnd-btn-book">Book Now</a>
            </div>
          </div>
        </article>
      </div>
      <?php endforeach; else: ?>
      
      <!-- Static Demo Fallback (when no dynamic listings exist) -->
      <div role="listitem">
        <article class="cnd-listing-home-card">
          <div class="cnd-card-img-wrap">
            <img src="https://images.unsplash.com/photo-1547153760-18fc86324498?auto=format&fit=crop&q=80&w=400" alt="Dance Workshop">
            <span class="cnd-card-type-badge" style="color:var(--cnd-primary)">Free Trial</span>
          </div>
          <div class="cnd-card-body">
            <div class="cnd-card-category-row">
              <span>Dance • Adults</span>
              <span class="cnd-card-rating"><i class="bi bi-star-fill"></i> 4.9 <span style="color:var(--cnd-text-muted);font-weight:400;font-size:.75rem;">(120)</span></span>
            </div>
            <a href="#" class="cnd-card-title">Contemporary Dance Workshop</a>
            <div class="cnd-card-footer-row">
              <div class="cnd-price-tag">₹499 <small>/ session</small></div>
              <a href="#" class="cnd-btn-book">Book Now</a>
            </div>
          </div>
        </article>
      </div>
      <div role="listitem">
        <article class="cnd-listing-home-card">
          <div class="cnd-card-img-wrap">
            <img src="https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&q=80&w=400" alt="Guitar Mastery">
          </div>
          <div class="cnd-card-body">
            <div class="cnd-card-category-row">
              <span>Music • Kids</span>
              <span class="cnd-card-rating"><i class="bi bi-star-fill"></i> 4.8 <span style="color:var(--cnd-text-muted);font-weight:400;font-size:.75rem;">(85)</span></span>
            </div>
            <a href="#" class="cnd-card-title">Beginner Guitar Mastery</a>
            <div class="cnd-card-footer-row">
              <div class="cnd-price-tag">₹1,200 <small>/ mo</small></div>
              <a href="#" class="cnd-btn-book">Book Now</a>
            </div>
          </div>
        </article>
      </div>
      <div role="listitem">
        <article class="cnd-listing-home-card">
          <div class="cnd-card-img-wrap">
            <img src="https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=400" alt="Python Coding">
            <span class="cnd-card-type-badge" style="color:#5B6BF1">New</span>
          </div>
          <div class="cnd-card-body">
            <div class="cnd-card-category-row">
              <span>Coding • Teens</span>
              <span class="cnd-card-rating"><i class="bi bi-star-fill"></i> 5.0 <span style="color:var(--cnd-text-muted);font-weight:400;font-size:.75rem;">(24)</span></span>
            </div>
            <a href="#" class="cnd-card-title">Python Game Development</a>
            <div class="cnd-card-footer-row">
              <div class="cnd-price-tag">₹2,500 <small>/ course</small></div>
              <a href="#" class="cnd-btn-book">Book Now</a>
            </div>
          </div>
        </article>
      </div>
      <?php endif; ?>
    </div>


<?php /* ── 4. PROMOTION BANNER (Pixel-perfect Figma) ──────────────────── */ ?>
<section class="home-promo-section">
  <div class="home-promo-inner">
    <div class="home-promo-text">
      <h2>Try Before You Commit!</h2>
      <p>Many classes offer free trial sessions. Explore, attend, and then decide — zero risk.</p>
      <a href="<?= base_url('classes?free_trial=1') ?>" class="home-promo-btn">Browse Free Trials</a>
    </div>
    <div class="home-promo-images d-none d-md-flex">
      <img src="https://images.unsplash.com/photo-1508700115892-45ecd05ae2ad?auto=format&fit=crop&q=80&w=200&h=200" alt="Dance class">
      <img src="https://images.unsplash.com/photo-1510915361894-db8b60106cb1?auto=format&fit=crop&q=80&w=200&h=200" alt="Guitar class">
    </div>
  </div>
</section>

<?php 
/* ── 5. UPCOMING WORKSHOPS & COURSES ───────────────────
 * Merges workshops and courses from the $listings variable.
 * Fallback to empty array if not set.
 */
$_upcomingSpecials = array_merge($listings['workshop'] ?? [], $listings['course'] ?? []);
// If no local ones, and you want to show SOMETHING, you could fetch more here
// or just show the section if not empty.
?>

<?php if (!empty($_upcomingSpecials)): ?>
<section class="cnd-listings-sections pb-5 pt-5" id="upcoming-specials" style="background:var(--cnd-bg);">
  <div class="container-fluid px-3 px-lg-5" style="max-width:1300px;margin:0 auto;">
    
    <div class="cnd-section-header">
      <h2 class="cnd-section-title mb-0">Upcoming Workshops & Courses</h2>
      <a href="<?= base_url('classes?type=workshop') ?>" class="cnd-view-all">
        View All <i class="bi bi-arrow-right"></i>
      </a>
    </div>

    <div class="classes-grid" role="list">
      <?php 
        foreach (array_slice($_upcomingSpecials, 0, 4) as $_l):
          $_l    = (array)$_l;
          $_id   = $_l['id'] ?? 0;
          $_type = $_l['type'] ?? 'workshop';
          
          $_unit = ($_type === 'workshop') ? '/ session' : '/ course';
          $_badgeText = ucfirst($_type);
          $_badgeClr = ($_type === 'workshop') ? '#E14D91' : '#5B6BF1';

          $_rating = (float)($_l['avg_rating'] ?? 0);
          $_reviews = (int)($_l['review_count'] ?? 0);
      ?>
      <div role="listitem">
        <article class="cnd-listing-home-card">
          <div class="cnd-card-img-wrap">
            <img src="<?= listing_img_url($_l['cover_image'] ?? '') ?>" alt="<?= esc($_l['title']) ?>">
            <span class="cnd-card-type-badge" style="color:<?= esc($_badgeClr) ?>"><?= esc($_badgeText) ?></span>
          </div>
          <div class="cnd-card-body">
            <div class="cnd-card-category-row">
              <span><?= esc($_l['category_name'] ?? 'Class') ?></span>
              <span class="cnd-card-rating">
                <i class="bi bi-star-fill"></i> <?= number_format($_rating, 1) ?> 
              </span>
            </div>
            <a href="<?= base_url('classes/'.$_id) ?>" class="cnd-card-title"><?= esc($_l['title']) ?></a>
            <div class="cnd-card-footer-row">
              <div class="cnd-price-tag">₹<?= number_format((float)($_l['price'] ?? 0)) ?> <small><?= esc($_unit) ?></small></div>
              <a href="<?= base_url('classes/'.$_id) ?>" class="cnd-btn-book">Book Slot</a>
            </div>
          </div>
        </article>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php /* ── 6. TESTIMONIALS (Pixel-perfect Figma) ──────────────────── */ ?>
<section class="home-testimonials-section">
  <div style="max-width:1300px; margin:0 auto;">
    <h2>What Learners Say</h2>

    <div class="row g-3 g-md-4">
      <?php 
      // Use static Figma-matching testimonials as fallback
      $_staticTestimonials = [
        ['initials'=>'PG','name'=>'Priya Gupta','desc'=>'Parent of 2','rating'=>5,'text'=>'"LearnNextDoor helped me find the perfect dance class for my daughter just 5 minutes away from home. The free trial option was a game changer!"'],
        ['initials'=>'DS','name'=>'Dev Sharma','desc'=>'Software Engineer','rating'=>5,'text'=>'"I enrolled in the Web Dev Bootcamp and it completely transformed my career. The early bird pricing made it very affordable."'],
        ['initials'=>'AN','name'=>'Anjali Nair','desc'=>'College Student','rating'=>5,'text'=>'"Love how easy it is to discover new workshops nearby. The pottery workshop was incredible!"'],
        ['initials'=>'RP','name'=>'Rohan Patel','desc'=>'Working Professional','rating'=>4,'text'=>'"Finally found a yoga class that fits my morning schedule. The booking process is super smooth."'],
      ];
      $_testimonialsToShow = !empty($testimonials) ? array_slice((array)$testimonials, 0, 4) : $_staticTestimonials;
      $__avatarColors = ['#3F3590','#7778F6','#3F3590','#7778F6'];
      foreach ($_testimonialsToShow as $_i => $_t):
        $_t = is_array($_t) ? $_t : (array)$_t;
        $_isStatic = isset($_t['initials']);
        $_name     = $_isStatic ? $_t['name']    : esc($_t['user_name'] ?? '');
        $_desc     = $_isStatic ? $_t['desc']     : 'Verified Learner';
        $_text     = $_isStatic ? $_t['text']     : '"'.esc($_t['feedback'] ?? '').'"';
        $_rating   = (int)($_t['rating'] ?? 5);
        // Build initials
        $_nameParts = explode(' ', trim($_name));
        $_ini = strtoupper(substr($_nameParts[0],0,1).(isset($_nameParts[1])?substr($_nameParts[1],0,1):''));
        $_avClr = $__avatarColors[$_i % 4];
      ?>
      <div class="col-6 col-md-6 col-lg-3">
        <div class="cnd-testimonial-card h-100" style="background: #fff; border: 1px solid #eee; border-radius: 16px; padding: 24px;">
          <div class="mb-3" style="color: #F9A05E; font-size: 0.85rem;">
            <?php for ($__s=1;$__s<=5;$__s++): ?>
              <i class="bi bi-star<?= $__s<=$_rating?'-fill':'' ?>"></i>
            <?php endfor; ?>
          </div>
          <p style="font-size: 14px; color: #555; line-height: 1.7; margin-bottom: 20px;"><?= $_text ?></p>
          <div class="d-flex align-items-center">
            <div style="width: 40px; height: 40px; border-radius: 50%; background: <?= $_avClr ?>; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px; flex-shrink: 0;"><?= esc($_ini) ?></div>
            <div class="ms-3">
              <div style="font-weight: 700; font-size: 14px; color: #1A1640;"><?= $_name ?></div>
              <div style="font-size: 12px; color: #999;"><?= esc($_desc) ?></div>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php /* ── 7. PROVIDER CTA (PIXEL-PERFECT FIGMA) ────────── */ ?>
<section class="home-provider-section">
  <div class="home-provider-card">
    <h2>Are You a Class Provider?</h2>
    <p>List your classes on LearnNextDoor and reach thousands of learners in your neighbourhood.</p>
    <a href="<?= base_url('join-as-provider') ?>" class="home-provider-cta-btn">
      Get Started as Provider
      <i class="bi bi-arrow-right"></i>
    </a>
  </div>
</section>

<?= $this->endSection() ?>


<?= $this->section('js') ?>
<script>
(function () {
  'use strict';

  /* ── Hero search: sync to navbar search on input ── */
  const heroInput = document.getElementById('hero-search-input');
  const navSearch = document.querySelector('.cnd-nav-search-wrap input');

  if (heroInput && navSearch) {
    heroInput.addEventListener('input', function () {
      navSearch.value = this.value;
    });
  }

  /* ── Mobile category dropdown → redirect ── */
  const catDrop = document.getElementById('homeCatDropdown');
  if (catDrop) {
    catDrop.addEventListener('change', function () {
      const val = this.value;
      if (val) window.location.href = window.CND_BASE_URL + 'classes?category=' + val;
    });
  }

  /* ── Show mobile dropdown only on very small screens ── */
  function toggleCatDropdown() {
    const wrap = document.getElementById('cnd-cat-dropdown-wrap');
    if (!wrap) return;
    if (window.innerWidth < 480) {
      wrap.classList.remove('d-none');
    } else {
      wrap.classList.add('d-none');
    }
  }
  toggleCatDropdown();
  window.addEventListener('resize', toggleCatDropdown, { passive: true });

  /* ── Animate cards into view (IntersectionObserver) ── */
  if ('IntersectionObserver' in window) {
    const cards = document.querySelectorAll(
      '.cnd-listing-home-card, .cat-card, .cnd-testimonial-card'
    );
    const io = new IntersectionObserver((entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) {
          e.target.style.animation = 'fadeInUp 0.5s ease-out both';
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.12 });
    cards.forEach((c) => io.observe(c));
  }
})();
</script>
<?= $this->endSection() ?>
