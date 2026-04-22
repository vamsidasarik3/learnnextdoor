<?php
/**
 * Partial: partials/class_card.php
 * ─────────────────────────────────────────────
 * Reusable Class / Listing Card — pixel-perfect match to demo_preview.html class-card.
 * Handles Regular, Workshop and Course listings.
 *
 * Props ($classCard array):
 *   id          int|string  — listing ID
 *   title       string      — class/listing title
 *   category    string      — category label (uppercase, muted)
 *   rating      float       — numeric rating (e.g. 4.9)
 *   reviewCount int         — number of reviews (optional)
 *   price       float|int   — price in INR (0 = Free)
 *   priceUnit   string      — '/ session' | '/ month' | '/ course'
 *   image       string      — cover image URL
 *   badge       string      — overlay badge text (e.g. 'Free Trial Available', 'Popular')
 *   badgeColor  string      — CSS color for badge text (default: var(--cnd-primary))
 *   btnText     string      — button label (default: 'Book Now')
 *   url         string      — detail page URL
 *   type        string      — 'regular' | 'workshop' | 'course'  (drives badge style)
 *   providerVerified bool   — show verified tick if true
 * ─────────────────────────────────────────────
 */
$classCard = $classCard ?? [];

$ccId        = $classCard['id']               ?? 0;
$ccTitle     = $classCard['title']            ?? 'Class';
$ccCat       = $classCard['category']         ?? '';
$ccRating    = (float)($classCard['rating']   ?? 0);
$ccReviews   = (int)($classCard['reviewCount']?? 0);
$ccPrice     = (float)($classCard['price']    ?? 0);
$ccUnit      = $classCard['priceUnit']        ?? '/ session';
$ccImage     = $classCard['image']            ?? base_url('assets/frontend/img/placeholder.jpg');
$ccBadge     = $classCard['badge']            ?? '';
$ccBadgeClr  = $classCard['badgeColor']       ?? 'var(--cnd-primary)';
$ccBtnText   = $classCard['btnText']          ?? 'Book Now';
$ccUrl       = $classCard['url']              ?? base_url('classes/' . (int)$ccId);
$ccType      = strtolower($classCard['type']  ?? 'regular');
$ccVerified  = (bool)($classCard['providerVerified'] ?? false);
?>
<article class="cnd-listing-home-card" data-listing-id="<?= (int)$ccId ?>">

  <!-- Image wrapper -->
  <div class="cnd-card-img-wrap">
    <img src="<?= esc($ccImage) ?>"
         alt="<?= esc($ccTitle) ?>"
         loading="lazy">

    <?php if ($ccBadge): ?>
    <span class="cnd-card-type-badge badge-<?= esc($ccType) ?>"
          style="color:<?= esc($ccBadgeClr, 'attr') ?>">
      <?= esc($ccBadge) ?>
      <?php if ($ccVerified): ?>
        &nbsp;<i class="bi bi-patch-check-fill text-success" aria-label="Verified"></i>
      <?php endif; ?>
    </span>
    <?php endif; ?>
  </div>

  <!-- Body -->
  <div class="cnd-card-body">

    <!-- Category + Rating row -->
    <div class="cnd-card-category-row">
      <span><?= esc($ccCat) ?></span>
      <?php if ($ccRating > 0): ?>
      <span class="cnd-card-rating" aria-label="Rating <?= number_format($ccRating,1) ?> out of 5">
        <i class="bi bi-star-fill" aria-hidden="true"></i>
        <?= number_format($ccRating, 1) ?>
        <?php if ($ccReviews > 0): ?>
        <span style="color:var(--cnd-text-muted);font-weight:400;font-size:.75rem;">
          (<?= $ccReviews ?>)
        </span>
        <?php endif; ?>
      </span>
      <?php endif; ?>
    </div>

    <!-- Title link -->
    <a href="<?= esc($ccUrl) ?>" class="cnd-card-title">
      <?= esc($ccTitle) ?>
    </a>

    <!-- Footer: price + CTA -->
    <div class="cnd-card-footer-row">
      <div class="cnd-price-tag">
        <?php if ($ccPrice > 0): ?>
          &#8377;<?= number_format($ccPrice) ?>
          <small><?= esc($ccUnit) ?></small>
        <?php else: ?>
          <span style="color:#28a745;font-size:1rem;">Free</span>
        <?php endif; ?>
      </div>
      <a href="<?= esc($ccUrl) ?>" class="cnd-btn-book" id="book-btn-<?= (int)$ccId ?>">
        <?= esc($ccBtnText) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i>
      </a>
    </div>

  </div><!-- /.cnd-card-body -->

</article>
