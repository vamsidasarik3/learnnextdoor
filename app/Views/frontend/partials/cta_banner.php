<?php
/**
 * Partial: partials/cta_banner.php
 * ─────────────────────────────────────────────
 * Reusable CTA / Provider Call-to-Action Banner.
 * Exact match to demo_preview.html cta-banner block.
 *
 * Props ($ctaBanner array):
 *   title       string  — headline text
 *   subtitle    string  — description paragraph
 *   btnText     string  — button label
 *   btnUrl      string  — href
 *   btnId       string  — HTML id on the button (for testing)
 *   gradFrom    string  — CSS gradient start color (default: var(--cnd-accent))
 *   gradTo      string  — CSS gradient end color   (default: var(--cnd-highlight))
 *   visible     bool    — show/hide the whole banner
 * ─────────────────────────────────────────────
 */
$ctaBanner = $ctaBanner ?? [];
$ctaTitle    = $ctaBanner['title']    ?? 'Start Teaching Today!';
$ctaSub      = $ctaBanner['subtitle'] ?? 'Join our community of expert instructors and share your passion with eager learners in your area. Easy listing, secure payouts.';
$ctaBtnText  = $ctaBanner['btnText']  ?? 'Start as Provider';
$ctaBtnUrl   = $ctaBanner['btnUrl']   ?? base_url('provider/login');
$ctaBtnId    = $ctaBanner['btnId']    ?? 'cta-provider-btn';
$ctaFrom     = $ctaBanner['gradFrom'] ?? 'var(--cnd-accent)';
$ctaTo       = $ctaBanner['gradTo']   ?? 'var(--cnd-highlight)';
$ctaVisible  = $ctaBanner['visible']  ?? true;

if (!$ctaVisible) return;
?>
<div class="cnd-cta-banner"
     style="background:linear-gradient(to right,<?= esc($ctaFrom,'attr') ?>,<?= esc($ctaTo,'attr') ?>);"
     role="complementary"
     aria-label="Become a provider">
  <div class="cnd-cta-content">
    <h2><?= esc($ctaTitle) ?></h2>
    <p><?= $ctaSub /* HTML allowed for em/strong */ ?></p>
  </div>
  <a href="<?= esc($ctaBtnUrl) ?>"
     class="cnd-btn-cta"
     id="<?= esc($ctaBtnId, 'attr') ?>">
    <?= esc($ctaBtnText) ?> <i class="bi bi-arrow-right" aria-hidden="true"></i>
  </a>
</div>
