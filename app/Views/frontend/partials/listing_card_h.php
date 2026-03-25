<?php
/**
 * Partial: listing_card_h.php
 * Horizontal listing card — image left, details right.
 * Used by: classes.php, home.php mobile strip
 *
 * Expects: $listing (array with keys: id, title, address,
 *   cover_image, type, price, avg_rating, review_count,
 *   distance_km, category_name, availability_days)
 */
$listing = $listing ?? [];
$id    = $listing['id']    ?? '#';
$title = $listing['title'] ?? 'Class';
$addr  = $listing['address']        ?? '';
$img   = listing_img_url($listing['cover_image'] ?? '');
$type  = ucfirst($listing['type']   ?? 'class');
$price = $listing['price']          ?? 0;
$avg   = (float)($listing['avg_rating'] ?? 0);
$cnt   = (int)($listing['review_count'] ?? 0);
$dist  = isset($listing['distance_km']) ? round((float)$listing['distance_km'], 1) : null;
$cat   = $listing['category_name']  ?? '';
$days  = isset($listing['availability_days']) && is_array($listing['availability_days'])
       ? $listing['availability_days']
       : [];
?>
<article class="cnd-lcard" role="listitem" aria-label="<?= esc($title) ?>">

  <!-- Image -->
  <a href="<?= base_url('classes/' . esc($id)) ?>"
     class="cnd-lcard-img"
     tabindex="-1" aria-hidden="true">
    <img src="<?= $img ?>" alt="" loading="lazy" width="120" height="120">
    <span class="cnd-badge-type-sm"><?= esc($type) ?></span>
  </a>

  <!-- Details -->
  <div class="cnd-lcard-body">

    <!-- Title -->
    <h3 class="cnd-lcard-title">
      <a href="<?= base_url('classes/' . esc($id)) ?>">
        <?= esc(character_limiter($title, 55)) ?>
      </a>
    </h3>

    <!-- Address -->
    <?php if ($addr): ?>
    <div class="cnd-lcard-address">
      <i class="bi bi-geo-alt-fill" style="color:#e25;flex-shrink:0;margin-top:1px;" aria-hidden="true"></i>
      <span><?= esc(character_limiter($addr, 48)) ?></span>
    </div>
    <?php endif; ?>

    <!-- Schedule info (Date & Time) -->
    <?php if (!empty($listing['start_date'])): ?>
    <div class="small text-muted mb-2 d-flex align-items-center gap-2" style="font-size: 0.72rem;">
      <i class="bi bi-calendar-event text-primary" aria-hidden="true"></i>
      <span>
        <?= date('d M', strtotime($listing['start_date'])) ?>
        <?php if (!empty($listing['end_date']) && ($listing['type'] === 'workshop' || $listing['type'] === 'course')): ?>
          - <?= date('d M Y', strtotime($listing['end_date'])) ?>
        <?php endif; ?>
      </span>
      <?php if (!empty($listing['class_time'])): ?>
        <span class="text-secondary opacity-50">|</span>
        <i class="bi bi-clock text-info" aria-hidden="true"></i>
        <span>
          <?= date('h:i A', strtotime($listing['class_time'])) ?>
          <?php if (!empty($listing['class_end_time'])): ?>
            - <?= date('h:i A', strtotime($listing['class_end_time'])) ?>
          <?php endif; ?>
        </span>
      <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- Day pills -->
    <?php if (!empty($days)): ?>
    <div class="cnd-day-pills" aria-label="Available days">
      <?php foreach (array_slice($days, 0, 5) as $day): ?>
      <span class="cnd-day-pill"><?= esc(mb_substr($day, 0, 3)) ?></span>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <!-- Meta row: stars · category · distance · price -->
    <div class="cnd-lcard-meta">

      <!-- Star rating -->
      <?php if ($avg > 0): ?>
      <span class="d-inline-flex align-items-center gap-1"
            aria-label="Rating <?= number_format($avg, 1) ?> out of 5">
        <span class="cnd-lcard-stars">
          <?php for ($s = 1; $s <= 5; $s++) echo $s <= round($avg) ? '★' : '☆'; ?>
        </span>
        <span class="small text-muted fw-600">(&nbsp;<?= $cnt ?>&nbsp;)</span>
      </span>
      <?php endif; ?>

      <!-- Category & Subcategory badge -->
      <?php if ($cat): ?>
      <span class="small" style="color:var(--cnd-primary);font-weight:600;">
        <?= esc($cat) ?><?= !empty($listing['subcategory_name']) ? ' > ' . esc($listing['subcategory_name']) : '' ?>
      </span>
      <?php endif; ?>

      <!-- Distance -->
      <?php if ($dist !== null): ?>
      <span class="small text-muted">
        <i class="bi bi-geo-alt" aria-hidden="true"></i> <?= $dist ?> km
      </span>
      <?php endif; ?>

      <!-- Price (pushed to right) -->
      <?php if ($price > 0): ?>
      <span class="cnd-lcard-price ms-auto">
        <?php 
          $pSuffix = '';
          if ($listing['type'] === 'regular') {
            $pType = $listing['price_type'] ?? 'monthly';
            $pSuffix = ($pType === 'quarterly') ? '/qt' : '/mo';
          }
        ?>
        <i class="bi bi-currency-rupee" aria-hidden="true"></i><?= number_format($price) ?><span style="font-size:0.65rem;opacity:0.75;font-weight:600;"><?= $pSuffix ?></span>
      </span>
      <?php else: ?>
      <span class="cnd-lcard-free ms-auto">Free</span>
      <?php endif; ?>

    </div><!-- /.cnd-lcard-meta -->

  </div><!-- /.cnd-lcard-body -->

</article>
