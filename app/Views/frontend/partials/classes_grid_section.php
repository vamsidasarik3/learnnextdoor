<?php
/**
 * Partial: partials/classes_grid_section.php
 * ─────────────────────────────────────────────
 * Reusable "Curated Classes" section.
 * Renders a <section> with section-header + an auto-fill grid of class_card partials.
 *
 * Props ($classesSection array):
 *   title         string  — section heading
 *   ctaText       string  — CTA link label
 *   ctaUrl        string  — CTA link href (null = hide CTA)
 *   type          string  — 'regular' | 'workshop' | 'course' (drives badge color + price unit)
 *   listings[]    array   — raw listing rows from the controller
 *   emptyMessage  string  — message when listings array is empty
 *   sectionId     string  — id attribute for the outer <div>
 *   maxCards      int     — cap how many cards to render (0 = no cap)
 *
 * Each listing row uses the same keys as the controller output:
 *   id, title, category_name, avg_rating, review_count,
 *   price, price_type, cover_image, type, provider_verified
 * ─────────────────────────────────────────────
 */
$classesSection = $classesSection ?? [];

$csTitle    = $classesSection['title']        ?? 'Curated For You';
$csCta      = $classesSection['ctaText']      ?? 'Discover More';
$csCtaUrl   = $classesSection['ctaUrl']       ?? base_url('classes');
$csType     = strtolower($classesSection['type'] ?? 'regular');
$csListings = $classesSection['listings']     ?? [];
$csEmpty    = $classesSection['emptyMessage'] ?? 'No classes available near you right now.';
$csSecId    = $classesSection['sectionId']    ?? 'classes-' . $csType;
$csMax      = (int)($classesSection['maxCards'] ?? 0);

/* ── Badge text & price-unit per listing type ── */
$typeMeta = [
    'regular'  => ['priceUnit' => '/ month',   'btnText' => 'Book Now',    'badgeLabel' => 'Regular'],
    'workshop' => ['priceUnit' => '/ session',  'btnText' => 'Enroll Now',  'badgeLabel' => 'Workshop'],
    'course'   => ['priceUnit' => '/ course',   'btnText' => 'Enroll Now',  'badgeLabel' => 'Course'],
];
$meta = $typeMeta[$csType] ?? $typeMeta['regular'];

/**
 * Map a raw controller listing row to the class_card props format.
 * Guard prevents fatal PHP errors when partial is included 3× (one per type).
 */
if (!function_exists('normaliseListingToCard')) {
    function normaliseListingToCard(array $l, array $meta, string $type): array
    {
        $price     = (float)($l['price'] ?? 0);
        $priceUnit = $meta['priceUnit'];

        if ($type === 'regular') {
            $priceUnit = (($l['price_type'] ?? 'monthly') === 'quarterly') ? '/ quarter' : '/ month';
        }

        $badgeText = '';
        $badgeClr  = 'var(--cnd-primary)';
        if (!empty($l['is_featured']))    { $badgeText = 'Featured';             $badgeClr = '#FF68B4'; }
        elseif (!empty($l['free_trial'])) { $badgeText = 'Free Trial Available'; $badgeClr = 'var(--cnd-primary)'; }
        elseif ($type === 'workshop')     { $badgeText = 'Workshop';             $badgeClr = '#E14D91'; }
        elseif ($type === 'course')       { $badgeText = 'Course';               $badgeClr = '#5B6BF1'; }
        else                              { $badgeText = 'Regular';              $badgeClr = 'var(--cnd-primary)'; }

        return [
            'id'               => $l['id']              ?? 0,
            'title'            => $l['title']            ?? 'Class',
            'category'         => $l['category_name']   ?? ucfirst($type),
            'rating'           => (float)($l['avg_rating']   ?? 0),
            'reviewCount'      => (int)($l['review_count']   ?? 0),
            'price'            => $price,
            'priceUnit'        => $priceUnit,
            'image'            => listing_img_url($l['cover_image'] ?? ''),
            'badge'            => $badgeText,
            'badgeColor'       => $badgeClr,
            'btnText'          => $meta['btnText'],
            'url'              => base_url('classes/' . esc((string)($l['id'] ?? 0))),
            'type'             => $type,
            'providerVerified' => !empty($l['provider_verified']),
        ];
    }
}
?>
<div class="classes-grid-section" id="<?= esc($csSecId, 'attr') ?>">

  <?= $this->include('frontend/partials/section_header', [
      'sectionHeader' => [
          'title'     => $csTitle,
          'ctaText'   => $csCta,
          'ctaUrl'    => $csCtaUrl,
          'headingTag'=> 'h2',
      ]
  ]) ?>

  <?php if (empty($csListings)): ?>
    <div class="cnd-empty-state" role="status">
      <i class="bi bi-inbox cnd-empty-icon" aria-hidden="true"></i>
      <p class="mb-0"><?= esc($csEmpty) ?></p>
    </div>

  <?php else: ?>
    <div class="classes-grid" role="list">
      <?php
      $rendered = 0;
      foreach ($csListings as $listing):
          if ($csMax > 0 && $rendered >= $csMax) break;
          $cardProps = normaliseListingToCard((array)$listing, $meta, $csType);
      ?>
      <div role="listitem">
        <?= $this->include('frontend/partials/class_card', ['classCard' => $cardProps]) ?>
      </div>
      <?php
          $rendered++;
      endforeach;
      ?>
    </div><!-- /.classes-grid -->
  <?php endif; ?>

</div>
