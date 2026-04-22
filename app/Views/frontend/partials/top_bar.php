<?php
/**
 * Partial: partials/top_bar.php
 * ─────────────────────────────────────────────
 * Reusable announcement / notification top bar.
 *
 * Props (passed via the $topBar array, set in the controller or view):
 *   $topBar['message']  — string  — announcement text (HTML allowed)
 *   $topBar['bgColor']  — string  — CSS color/variable (default: var(--cnd-primary))
 *   $topBar['textColor']— string  — CSS color          (default: #fff)
 *   $topBar['visible']  — bool    — show/hide          (default: true)
 *
 * Usage in a view:
 *   <?= $this->include('frontend/partials/top_bar', ['topBar' => $topBar]) ?>
 *
 * Or set $topBar in the controller and it flows through the layout.
 * ─────────────────────────────────────────────
 */
$topBar = $topBar ?? [];
$tbMsg     = $topBar['message']   ?? '✨ Join 1000+ local learners in your neighbourhood today!';
$tbBg      = $topBar['bgColor']   ?? 'var(--cnd-primary)';
$tbColor   = $topBar['textColor'] ?? '#ffffff';
$tbVisible = $topBar['visible']   ?? true;

if (!$tbVisible) return;
?>
<div class="cnd-top-bar"
     role="banner"
     aria-label="Site announcement"
     style="background:<?= esc($tbBg, 'attr') ?>;color:<?= esc($tbColor, 'attr') ?>;">
  <?= $tbMsg ?>
</div>
