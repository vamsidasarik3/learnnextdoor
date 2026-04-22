<?php
/**
 * Partial: partials/section_header.php
 * ─────────────────────────────────────────────
 * Reusable section title + optional "View All" CTA link.
 *
 * Props ($sectionHeader array):
 *   title       string  — section heading text
 *   ctaText     string  — link label (default: 'View All')
 *   ctaUrl      string  — href (omit or set null to hide CTA)
 *   ctaHide     bool    — force-hide the CTA link
 *   headingTag  string  — 'h2' | 'h3' (default: 'h2')
 *   headingId   string  — optional id attribute on the heading
 * ─────────────────────────────────────────────
 */
$sectionHeader = $sectionHeader ?? [];
$shTitle      = $sectionHeader['title']      ?? 'Section';
$shCta        = $sectionHeader['ctaText']    ?? 'View All';
$shCtaUrl     = $sectionHeader['ctaUrl']     ?? null;
$shCtaHide    = $sectionHeader['ctaHide']    ?? false;
$shTag        = $sectionHeader['headingTag'] ?? 'h2';
$shId         = $sectionHeader['headingId']  ?? '';
$shIdAttr     = $shId ? ' id="' . esc($shId, 'attr') . '"' : '';
?>
<div class="cnd-section-header">
  <<?= $shTag . $shIdAttr ?> class="cnd-section-title mb-0">
    <?= $shTitle ?>
  </<?= $shTag ?>>

  <?php if (!$shCtaHide && $shCtaUrl): ?>
  <a href="<?= esc($shCtaUrl) ?>" class="cnd-view-all" aria-label="<?= esc($shCta) ?>">
    <?= esc($shCta) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i>
  </a>
  <?php endif; ?>
</div>
