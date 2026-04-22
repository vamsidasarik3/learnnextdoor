<?php
/**
 * Partial: partials/category_card.php
 * ─────────────────────────────────────────────
 * Single reusable Category Card — exact visual match to demo_preview.html cat-card.
 *
 * Props ($category array):
 *   id          int|string  — category ID (used in URL)
 *   name        string      — display label
 *   icon        string      — Bootstrap Icon class e.g. 'bi-music-note-beamed'
 *   iconBg      string      — CSS background for icon wrapper (rgba or var)
 *   iconColor   string      — CSS color for the icon itself
 *   url         string      — optional override href (defaults to /classes?category=$id)
 * ─────────────────────────────────────────────
 */
$category = $category ?? [];
$catId      = $category['id']        ?? 0;
$catName    = $category['name']      ?? 'Category';
$catIcon    = $category['icon']      ?? 'bi-star';
$catIconBg  = $category['iconBg']    ?? 'rgba(63,53,144,0.1)';
$catIconClr = $category['iconColor'] ?? 'var(--cnd-primary)';
$catUrl     = $category['url']       ?? base_url('classes?category=' . (int)$catId);
?>
<a href="<?= esc($catUrl) ?>"
   class="cat-card-link"
   aria-label="Browse <?= esc($catName) ?> classes">
  <div class="cat-card">
    <div class="cat-icon"
         style="background-color:<?= esc($catIconBg, 'attr') ?>;color:<?= esc($catIconClr, 'attr') ?>;">
      <i class="bi <?= esc($catIcon) ?>" aria-hidden="true"></i>
    </div>
    <h3><?= esc($catName) ?></h3>
  </div>
</a>
