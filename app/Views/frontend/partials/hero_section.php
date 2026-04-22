<?php
/**
 * Partial: partials/hero_section.php
 * ─────────────────────────────────────────────
 * Reusable Hero Section — pixel-perfect match of Figma v2 design.
 *
 * Props ($hero array):
 *   title           string   — heading text (HTML allowed; use <span> for accent)
 *   subtitle        string   — paragraph below heading
 *   searchAction    string   — form action URL
 *   searchParam     string   — GET param name (default: 'q')
 *   searchPlaceholder string — input placeholder
 *   searchBtnText   string   — button label (default: 'Search')
 *   heroImage       string   — background-image URL for the right card
 *   badges[]        array    — list of floating badge configs (see below)
 * ─────────────────────────────────────────────
 */
$hero = $hero ?? [];

// Defaults  
$heroTitle         = $hero['title']             ?? 'Learn Something <span class="hero-h1-accent">Next Door</span>';
$heroSubtitle      = $hero['subtitle']          ?? 'Find the best local classes, courses, and workshops in your neighbourhood. From dance to coding — all just a doorstep away.';
$heroSearchAction  = $hero['searchAction']      ?? base_url('classes');
$heroSearchParam   = $hero['searchParam']       ?? 'q';
$heroSearchPh      = $hero['searchPlaceholder'] ?? 'What do you want to learn?';
$heroSearchBtn     = $hero['searchBtnText']     ?? 'Search';
$heroSearchValue   = $hero['searchValue']       ?? '';
$heroImage         = !empty($hero['heroImage']) ? $hero['heroImage'] : 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?auto=format&fit=crop&q=80&w=1000';

$heroBadges        = $hero['badges'] ?? [];
?>
<section id="hero" aria-labelledby="hero-heading">
  <div class="hero-container">

    <!-- LEFT: Heading + Subtitle + Search -->
    <div class="hero-content">
      <!-- Discovery badge pill (Figma) -->
      <div class="hero-discovery-pill">
        <span class="hero-pill-dot">⚡</span>
        <span>Discover 100+ local classes near you</span>
      </div>

      <h1 id="hero-heading" class="hero-h1">
        <?= $heroTitle ?>
      </h1>
      <p class="hero-sub">
        <?= $heroSubtitle ?>
      </p>

      <!-- Search bar -->
      <form action="<?= esc($heroSearchAction) ?>"
            method="GET"
            class="hero-search-form"
            role="search"
            id="hero-search-form">
        <div class="hero-search-input-wrap">
          <i class="bi bi-search hero-search-icon"></i>
          <input type="text"
                 name="<?= esc($heroSearchParam, 'attr') ?>"
                 value="<?= esc($heroSearchValue, 'attr') ?>"
                 placeholder="<?= esc($heroSearchPh, 'attr') ?>"
                 autocomplete="off"
                 aria-label="Search classes"
                 id="hero-search-input">
        </div>
        <button type="submit" class="hero-search-btn" id="hero-search-btn">
          <?= esc($heroSearchBtn) ?>
        </button>
      </form>

      <!-- Quick filter tags -->
      <div class="hero-discovery-tags">
        <span class="hero-tags-label">Popular:</span>
        <a href="<?= base_url('classes?q=dance') ?>" class="hero-tag">Dance</a>
        <a href="<?= base_url('classes?q=guitar') ?>" class="hero-tag">Guitar</a>
        <a href="<?= base_url('classes?q=yoga') ?>" class="hero-tag">Yoga</a>
        <a href="<?= base_url('classes?q=coding') ?>" class="hero-tag">Coding</a>
        <a href="<?= base_url('classes?q=art') ?>" class="hero-tag">Art</a>
      </div>
    </div><!-- /.hero-content -->

    <!-- RIGHT: Image card with floating badges -->
    <div class="hero-visual" aria-hidden="true">
      <!-- Wrap all hero visuals in a position:relative container -->
      <div class="hero-visual-wrap">

        <!-- Animated image only — no children inside -->
        <div class="hero-visual-anim">
          <div class="hero-image-card"
               style="background-image:url('<?= esc($heroImage, 'attr') ?>');"
               role="img"
               aria-label="Featured class image">
          </div>
        </div>

        <!-- Badges are siblings of the animated div, NOT children -->
        <div class="hero-float-badge hero-float-bottom-left">
          <div class="hero-float-badge-icon" style="background:linear-gradient(135deg,#F9A05E,#FF68B4);">
            <i class="bi bi-grid-fill" aria-hidden="true"></i>
          </div>
          <div>
            <div class="hero-float-badge-title">8+ Categories</div>
            <div class="hero-float-badge-sub">To explore</div>
          </div>
        </div>

        <div class="hero-float-badge hero-float-top-right">
          <div class="hero-float-badge-icon" style="background:linear-gradient(135deg,#3F3590,#7778F6);">
            <i class="bi bi-star-fill" aria-hidden="true"></i>
          </div>
          <div>
            <div class="hero-float-badge-title">4.7+ Rating</div>
            <div class="hero-float-badge-sub">Avg. reviews</div>
          </div>
        </div>

      </div>
    </div><!-- /.hero-visual -->

  </div><!-- /.hero-container -->
</section>

<style>
/* ── Hero-only overrides (Figma v2 pixel match) ── */

.hero-discovery-pill {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(255,255,255,0.12);
  border: 1px solid rgba(255,255,255,0.22);
  padding: 7px 18px;
  border-radius: 50px;
  margin-bottom: 28px;
  font-size: 0.875rem;
  font-weight: 600;
  color: rgba(255,255,255,0.95);
  font-family: var(--cnd-font);
  animation: cnd-slideInLeft 0.6s ease-out both;
  backdrop-filter: blur(8px);
}

.hero-pill-dot {
  color: #F9A05E;
  font-size: 1rem;
}

.hero-discovery-tags {
  display: flex;
  align-items: center;
  gap: 10px;
  flex-wrap: wrap;
  margin-top: 24px;
  animation: cnd-slideInLeft 1.4s ease-out both;
}

.hero-tags-label {
  font-size: 0.82rem;
  font-weight: 600;
  color: rgba(255,255,255,0.6);
  font-family: var(--cnd-font);
}

.hero-tag {
  display: inline-flex;
  align-items: center;
  padding: 6px 16px;
  border: 1px solid rgba(255,255,255,0.25);
  border-radius: 50px;
  font-size: 0.82rem;
  font-weight: 600;
  color: rgba(255,255,255,0.9);
  text-decoration: none;
  font-family: var(--cnd-font);
  transition: all 0.2s ease;
  backdrop-filter: blur(4px);
}

.hero-tag:hover {
  background: rgba(255,255,255,0.18);
  border-color: rgba(255,255,255,0.5);
  color: #fff;
  transform: translateY(-2px);
}

/* Center tags on mobile */
@media (max-width: 1024px) {
  .hero-discovery-tags { justify-content: center; }
  .hero-discovery-pill { display: inline-flex; }
}

/* Header search integration */
.hero-search-icon {
  font-size: 1rem;
  color: #9896b8;
  flex-shrink: 0;
}
</style>
