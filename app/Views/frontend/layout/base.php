<!DOCTYPE html>
<html lang="en">
<head>
  <!-- ============================================================
       META & SEO
  ============================================================ -->
  <meta charset="UTF-8">
  <!--
    Viewport meta tag ensures the layout scales correctly on all
    screen sizes — mobile, tablet, and desktop.
  -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
  <meta name="description" content="<?= $meta_description ?? 'Class Next Door – Discover and book the best kids\' classes near you.' ?>">
  <meta name="theme-color" content="#3F3590">
  <!-- CSRF token — read by app.js for AJAX POST requests -->
  <meta name="csrf-token" content="<?= csrf_hash() ?>">
  <meta name="vapid-public-key" content="<?= esc(getenv('VAPID_PUBLIC_KEY') ?: '') ?>">
  <title><?= $page_title ?? 'Class Next Door' ?></title>

  <!-- Canonical URL -->
  <link rel="canonical" href="<?= current_url() ?>">

  <!-- Open Graph (for WhatsApp / social sharing) -->
  <meta property="og:title"       content="<?= $page_title ?? 'Class Next Door' ?>">
  <meta property="og:description" content="<?= $meta_description ?? 'Find the best kids\' classes near you.' ?>">
  <meta property="og:image"       content="<?= base_url('assets/frontend/img/logo-full.png') ?>">
  <meta property="og:url"         content="<?= current_url() ?>">
  <meta property="og:type"        content="website">

  <!-- ============================================================
       FAVICONS
  ============================================================ -->
  <link rel="icon" type="image/png" href="<?= base_url('assets/frontend/img/logo-icon.png') ?>">

  <!-- ============================================================
       BOOTSTRAP 5.3 CSS  (CDN – no local install needed)
       Bootstrap 5 uses a mobile-first responsive grid system with
       breakpoints: xs(<576) sm(≥576) md(≥768) lg(≥992) xl(≥1200)
  ============================================================ -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
    crossorigin="anonymous">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Swiper.js 11 — touch carousel -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

  <!-- Google Fonts — Poppins (rounded, friendly — matches the theme) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

  <!-- Custom Frontend Stylesheet -->
  <link rel="stylesheet" href="<?= base_url('assets/frontend/css/app.css') ?>">

  <!-- jQuery (Required by many child views and libraries) -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

  <!-- Page-level CSS (injected by child views) -->
  <?= $this->renderSection('css') ?>
</head>
<body class="cnd-body"
  data-base-url="<?= base_url() ?>"
  data-google-maps-key="<?= esc(env('GOOGLE_MAP_API_KEY', '')) ?>"
  data-no-location="<?php
    // Read raw $_COOKIE — catches JS-set cookies regardless of CI4 cookie config
    $__loc = session()->get('cnd_location_name')
          ?? (isset($_COOKIE['cnd_location_name']) && $_COOKIE['cnd_location_name'] !== ''
              ? rawurldecode($_COOKIE['cnd_location_name'])
              : null);
    echo empty($__loc) ? '1' : '0';
  ?>">

  <!-- ============================================================
       SKIP NAVIGATION (Accessibility: keyboard & screen-readers)
  ============================================================ -->
  <a class="visually-hidden-focusable skip-link" href="#main-content">
    Skip to main content
  </a>

  <!-- ============================================================
       NAVBAR — included as partial
  ============================================================ -->
  <?= $this->include('frontend/layout/navbar') ?>

  <!-- ============================================================
       LOCATION BAR — shown only on home/search pages
       Displays current selected location and changes it
  ============================================================ -->
  <?php
    // Only show the location bar when user has actually selected a location.
    // When no location is set, the navbar button already invites them to pick one.
    $__showLocBar = !empty($show_location_bar) && !empty($selected_location);
  ?>
  <?php if ($__showLocBar): ?>
  <div class="cnd-location-bar" id="cnd-location-bar" role="status" aria-live="polite">
    <div class="container-fluid">
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <i class="bi bi-geo-alt-fill" aria-hidden="true" style="color:var(--cnd-pink);"></i>
        <span class="cnd-location-label"><strong><?= esc($selected_location) ?></strong></span>
        <button
          class="btn btn-link btn-sm p-0 cnd-change-location"
          type="button"
          data-bs-toggle="modal"
          data-bs-target="#locationModal"
          aria-label="Change location"
          style="font-size:.8rem;color:var(--cnd-grad-start);font-weight:600;">Change</button>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- ============================================================
       MAIN CONTENT — rendered by child views
  ============================================================ -->
  <main id="main-content" role="main" tabindex="-1">
    <?= $this->renderSection('content') ?>
  </main>

  <!-- ============================================================
       FOOTER — included as partial
  ============================================================ -->
  <?= $this->include('frontend/layout/footer') ?>

  <!-- ============================================================
       LOCATION MODAL — included globally so any page can trigger
  ============================================================ -->
  <?= $this->include('frontend/layout/location_modal') ?>

  <!-- ============================================================
       BOOTSTRAP 5.3 BUNDLE JS (includes Popper)
  ============================================================ -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

  <!-- Swiper.js 11 bundle -->
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

  <!-- ============================================================
       CORE FRONTEND JS — location handling, misc helpers
  ============================================================ -->
  <script src="<?= base_url('assets/frontend/js/app.js') ?>"></script>
  <script src="<?= base_url('assets/frontend/js/theme.js') ?>"></script>
  <!-- Google Maps key available to all inline scripts -->
  <script>window.CND_GMAPS_KEY = '<?= esc(env('GOOGLE_MAP_API_KEY', '')) ?>';
  window.CND_PUSH_PUBLIC_KEY = '<?= esc(getenv('VAPID_PUBLIC_KEY') ?: '') ?>';
  window.CND_BASE_URL = '<?= base_url() ?>';
  window.CND_CSRF = { name: '<?= csrf_token() ?>', token: '<?= csrf_hash() ?>' };
  </script>

  <!-- Web Push manager -->
  <script src="<?= base_url('assets/frontend/js/push.js') ?>"></script>

  <!-- Page-level JS (injected by child views) -->
  <?= $this->renderSection('js') ?>

</body>
</html>
