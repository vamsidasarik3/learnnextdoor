<?php
/**
 * Partial: partials/category_grid_section.php
 * ─────────────────────────────────────────────
 * Reusable "Explore Categories" section.
 * Renders a <section> containing the section-header + an auto-fit grid
 * of category_card partials.
 *
 * Props ($categoriesSection array):
 *   title         string  — section heading (default: 'Explore Categories')
 *   ctaText       string  — CTA link label  (default: 'View All Categories')
 *   ctaUrl        string  — CTA link href   (set null to hide)
 *   categories[]  array   — array of category data maps, each with:
 *                           id, name, icon (BI class), iconBg, iconColor, url
 *   fallbackCategoryColors array — palette to cycle through when iconBg not set
 *   sectionId     string  — id attribute for <section> (default: 'categories')
 *
 * The categories array comes directly from the controller ($categories from DB).
 * We normalise the icon / color here using a keyword-to-style map so existing
 * DB categories Just Work without any schema changes.
 * ─────────────────────────────────────────────
 */
$categoriesSection = $categoriesSection ?? [];

$csTitle   = $categoriesSection['title']    ?? 'Explore Categories';
$csCta     = $categoriesSection['ctaText']  ?? 'View All Categories';
$csCtaUrl  = $categoriesSection['ctaUrl']   ?? base_url('classes');
$csId      = $categoriesSection['sectionId']?? 'categories';
$csData    = $categoriesSection['categories']
             ?? ($categories ?? []);   // fall-through to $categories from layout

/* ── Static demo categories (shown when DB has no data) ── */
$staticDemoCategories = [
    ['id'=>0,'name'=>'Performing Arts','icon'=>'bi-music-note-beamed','iconBg'=>'rgba(63,53,144,.1)','iconColor'=>'#3F3590'],
    ['id'=>0,'name'=>'Art & Craft',    'icon'=>'bi-palette-fill',     'iconBg'=>'rgba(119,120,246,.1)','iconColor'=>'#7778F6'],
    ['id'=>0,'name'=>'Sports Lab',     'icon'=>'bi-trophy-fill',      'iconBg'=>'rgba(249,160,94,.1)', 'iconColor'=>'#F9A05E'],
    ['id'=>0,'name'=>'Tech & Coding',  'icon'=>'bi-code-slash',       'iconBg'=>'rgba(255,104,180,.1)','iconColor'=>'#FF68B4'],
    ['id'=>0,'name'=>'Brain Boost',    'icon'=>'bi-lightbulb-fill',   'iconBg'=>'rgba(63,53,144,.1)', 'iconColor'=>'#3F3590'],
    ['id'=>0,'name'=>'Culinary Arts',  'icon'=>'bi-egg-fried',        'iconBg'=>'rgba(249,160,94,.1)', 'iconColor'=>'#F9A05E'],
];

/* ── Keyword → icon/colour palette map ── */
$iconPalette = [
    'dance'      => ['icon'=>'bi-music-note-beamed',   'iconBg'=>'rgba(63,53,144,.1)',  'iconColor'=>'#3F3590'],
    'performing' => ['icon'=>'bi-music-note-beamed',   'iconBg'=>'rgba(63,53,144,.1)',  'iconColor'=>'#3F3590'],
    'music'      => ['icon'=>'bi-music-note-list',     'iconBg'=>'rgba(119,120,246,.1)','iconColor'=>'#7778F6'],
    'art'        => ['icon'=>'bi-palette-fill',        'iconBg'=>'rgba(119,120,246,.1)','iconColor'=>'#7778F6'],
    'craft'      => ['icon'=>'bi-scissors',            'iconBg'=>'rgba(249,160,94,.1)', 'iconColor'=>'#F9A05E'],
    'sport'      => ['icon'=>'bi-trophy-fill',         'iconBg'=>'rgba(249,160,94,.1)', 'iconColor'=>'#F9A05E'],
    'fitness'    => ['icon'=>'bi-person-arms-up',      'iconBg'=>'rgba(249,160,94,.1)', 'iconColor'=>'#F9A05E'],
    'yoga'       => ['icon'=>'bi-person-arms-up',      'iconBg'=>'rgba(119,120,246,.1)','iconColor'=>'#7778F6'],
    'coding'     => ['icon'=>'bi-code-slash',          'iconBg'=>'rgba(255,104,180,.1)','iconColor'=>'#FF68B4'],
    'tech'       => ['icon'=>'bi-cpu-fill',            'iconBg'=>'rgba(255,104,180,.1)','iconColor'=>'#FF68B4'],
    'brain'      => ['icon'=>'bi-lightbulb-fill',      'iconBg'=>'rgba(63,53,144,.1)',  'iconColor'=>'#3F3590'],
    'academic'   => ['icon'=>'bi-book-half',           'iconBg'=>'rgba(63,53,144,.1)',  'iconColor'=>'#3F3590'],
    'language'   => ['icon'=>'bi-chat-dots-fill',      'iconBg'=>'rgba(119,120,246,.1)','iconColor'=>'#7778F6'],
    'cooking'    => ['icon'=>'bi-egg-fried',           'iconBg'=>'rgba(249,160,94,.1)', 'iconColor'=>'#F9A05E'],
    'culinary'   => ['icon'=>'bi-egg-fried',           'iconBg'=>'rgba(249,160,94,.1)', 'iconColor'=>'#F9A05E'],
    'default'    => ['icon'=>'bi-star-fill',           'iconBg'=>'rgba(63,53,144,.1)',  'iconColor'=>'#3F3590'],
];

/**
 * Normalise a raw DB category object/array into the standard category map.
 * Guard prevents fatal PHP errors when this partial is included multiple times.
 */
if (!function_exists('normaliseCatToCard')) {
    function normaliseCatToCard($cat, array $iconPalette): array
    {
        $cat    = is_array($cat) ? $cat : (array)$cat;
        $catId  = $cat['id']   ?? 0;
        $name   = $cat['name'] ?? 'Category';
        $nameLo = strtolower($name);

        $style = $iconPalette['default'];
        foreach ($iconPalette as $kw => $st) {
            if ($kw !== 'default' && strpos($nameLo, $kw) !== false) {
                $style = $st;
                break;
            }
        }

        $rawIcon = $cat['icon'] ?? '';
        if ($rawIcon) {
            $icon = (strpos($rawIcon, 'bi-') === 0 || strpos($rawIcon, 'bi ') === 0)
                    ? $rawIcon
                    : 'bi-' . ltrim($rawIcon, 'bi-');
            $style['icon'] = $icon;
        }

        return [
            'id'        => $catId,
            'name'      => $name,
            'icon'      => $style['icon'],
            'iconBg'    => $cat['iconBg']    ?? $style['iconBg'],
            'iconColor' => $cat['iconColor'] ?? $style['iconColor'],
            'url'       => base_url('classes?category=' . (int)$catId),
        ];
    }
}
?>
<section class="cnd-categories-section" id="<?= esc($csId, 'attr') ?>" aria-label="Explore Categories">
  <div class="cnd-section-inner">

    <?= $this->include('frontend/partials/section_header', [
        'sectionHeader' => [
            'title'     => $csTitle,
            'ctaText'   => $csCta,
            'ctaUrl'    => $csCtaUrl,
            'headingTag'=> 'h2',
            'headingId' => 'categories-heading',
        ]
    ]) ?>

    <div class="categories-grid" role="list" aria-labelledby="categories-heading">
      <?php
      $catList = !empty($csData) ? $csData : $staticDemoCategories;
      foreach ($catList as $rawCat):
          if (isset($rawCat['icon'], $rawCat['iconBg'])) {
              // Already normalised (static demo)
              $cardData = $rawCat;
          } else {
              $cardData = normaliseCatToCard($rawCat, $iconPalette);
          }
      ?>
      <div role="listitem">
        <?= $this->include('frontend/partials/category_card', ['category' => $cardData]) ?>
      </div>
      <?php endforeach; ?>
    </div><!-- /.categories-grid -->

  </div>
</section>
