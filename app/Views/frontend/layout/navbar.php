<?php
    $session          = session();
    $__user           = $session->get('cnd_user'); 
    $__role           = (int)($__user['role'] ?? 0);
    $__isProviderMode = ($__role == 2 && (session()->get('cnd_provider_mode') ?? true));
    $__savedLoc       = $session->get('cnd_location_name')
                  ?? (isset($_COOKIE['cnd_location_name']) && $_COOKIE['cnd_location_name'] !== ''
                      ? rawurldecode($_COOKIE['cnd_location_name'])
                      : null);
    $__mobLocLabel    = $__savedLoc ? htmlspecialchars(explode(',', $__savedLoc)[0], ENT_QUOTES) : 'Near you';
    $__verificationStatus = $__user['provider_verification_status'] ?? null;
    $showJoinLink = (!$__user || ($__role == 3 && empty($__verificationStatus)));

    /* User initials for avatar */
    $__uName     = $__user['name'] ?? '';
    $__firstName = $__uName ? explode(' ', trim($__uName))[0] : 'User';
    $__initials  = '';
    if ($__uName) {
        $parts = explode(' ', trim($__uName));
        $__initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
    }
?>

<?php if ($__isProviderMode): ?>
<!-- ── Provider Mode Banner ── -->
<div class="cnd-provider-banner py-2 px-3 d-flex justify-content-between align-items-center sticky-top"
     style="font-size: 0.85rem; background: #1a1a2e; top: 0; z-index: 1040; height: 40px; color:#fff;">
   <div class="d-flex align-items-center gap-2">
      <span class="badge rounded-pill px-2" style="background:#F9A05E;color:#1A1640;">Provider Mode Active</span>
      <span class="opacity-75 d-none d-md-inline">You are managing your classes. Navigation is restricted.</span>
   </div>
   <a href="<?= base_url('provider/toggle-mode') ?>" class="btn btn-outline-light btn-sm rounded-pill px-3 py-1 fw-bold" style="font-size: 0.75rem;">
      <i class="bi bi-box-arrow-right me-1"></i> Exit Portal
   </a>
</div>
<style>#cnd-navbar { top: 40px !important; }</style>
<?php endif; ?>

<style>
  /* ═══════════════════════════ NAVBAR CORE ═══════════════════════════ */
  #cnd-navbar {
    background: #ffffff;
    border-bottom: 1px solid #e8e6f5;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    min-height: 80px;
    padding: 0 5%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    position: sticky;
    top: 0;
    z-index: 1000;
    transition: box-shadow 0.3s;
  }
  #cnd-navbar.scrolled {
    box-shadow: 0 4px 24px rgba(63,53,144,0.10);
  }

  /* ═══════════════════════════ LOGO ═══════════════════════════ */
  #cnd-navbar .logo {
    font-size: 1.45rem;
    font-weight: 800;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 9px;
    color: #1A1640;
    font-family: 'Outfit', sans-serif;
    white-space: nowrap;
    flex-shrink: 0;
  }
  #cnd-navbar .logo-icon {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #3F3590, #7778F6);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }
  #cnd-navbar .logo span { color: #F9A05E; }

  /* ═══════════════════════════ NAV LINKS ═══════════════════════════ */
  .nav-links-menu {
    display: flex;
    align-items: center;
    gap: 4px;
    list-style: none;
    padding: 0;
    margin: 0 0 0 32px;
  }
  .nav-links-menu a {
    color: #4B5563;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    padding: 7px 13px;
    border-radius: 9px;
    transition: all 0.2s;
    font-family: 'Outfit', sans-serif;
  }
  .nav-links-menu a:hover,
  .nav-links-menu a.active {
    color: #3F3590;
    background: #EEF2FF;
  }

  /* ═══════════════════════════ SEARCH (middle) ═══════════════════════════ */
  #cnd-navbar .search-nav-container {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 0 24px;
  }
  #cnd-navbar .search-nav {
    width: 100%;
    max-width: 400px;
    position: relative;
  }
  #cnd-navbar .search-nav input {
    width: 100%;
    padding: 10px 18px 10px 42px;
    border-radius: 12px;
    border: 1.5px solid #E5E7EB;
    background-color: #F9FAFB;
    font-size: 0.88rem;
    outline: none;
    transition: all 0.25s;
    color: #1A1640;
    font-family: 'Outfit', sans-serif;
  }
  #cnd-navbar .search-nav input:focus {
    background-color: #fff;
    border-color: #3F3590;
    box-shadow: 0 0 0 3px rgba(63,53,144,0.10);
  }
  #cnd-navbar .search-nav i {
    position: absolute;
    left: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: #9CA3AF;
    font-size: 0.95rem;
    z-index: 2;
    pointer-events: none;
  }

  /* ═══════════════════════════ NAV ACTIONS (right) ═══════════════════════════ */
  #cnd-navbar .nav-actions-container {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
  }
  .nav-icon-link {
    font-size: 1.35rem;
    color: #374151;
    text-decoration: none;
    position: relative;
    display: flex;
    align-items: center;
    padding: 6px;
    border-radius: 8px;
    transition: background 0.2s;
  }
  .nav-icon-link:hover { background: #F3F4F6; }
  .nav-icon-badge {
    position: absolute;
    top: 0px; right: 0px;
    background: #F9A05E;
    color: #fff;
    font-size: 0.6rem;
    font-weight: 800;
    width: 16px; height: 16px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    border: 2px solid #fff;
  }
  #cnd-navbar .btn-login {
    background: #3F3590;
    color: #fff !important;
    padding: 10px 22px;
    border-radius: 10px;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.25s;
    font-size: 0.88rem;
    display: flex; align-items: center; gap: 7px;
    border: none;
    font-family: 'Outfit', sans-serif;
    white-space: nowrap;
  }
  #cnd-navbar .btn-login:hover {
    background: #2d2670;
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(63,53,144,0.25);
  }
  #cnd-navbar .btn-profile {
    background: #EEF2FF;
    color: #3F3590 !important;
    padding: 8px 16px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.88rem;
    display: flex; align-items: center; gap: 7px;
    border: 1.5px solid #C7D2FE;
    font-family: 'Outfit', sans-serif;
    cursor: pointer;
    transition: all 0.2s;
  }
  #cnd-navbar .btn-profile:hover {
    background: #E0E7FF;
    border-color: #3F3590;
  }

  /* Hamburger */
  .cnd-hamburger {
    display: none;
    background: none;
    border: none;
    padding: 6px;
    cursor: pointer;
    border-radius: 8px;
    color: #3F3590;
    font-size: 1.6rem;
    line-height: 1;
    transition: background 0.2s;
  }
  .cnd-hamburger:hover { background: #F3F4F6; }

  /* ═══════════════════════════ MOBILE DRAWER ═══════════════════════════ */
  .cnd-mobile-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(17,7,46,0.45);
    z-index: 1998;
    backdrop-filter: blur(3px);
    -webkit-backdrop-filter: blur(3px);
    animation: cndFadeIn 0.2s ease-out;
  }
  .cnd-mobile-drawer {
    position: fixed;
    top: 0; right: -100%;
    width: min(320px, 88vw);
    height: 100dvh;
    background: #fff;
    z-index: 1999;
    display: flex;
    flex-direction: column;
    transition: right 0.32s cubic-bezier(0.32,0,0.67,0);
    box-shadow: -8px 0 40px rgba(0,0,0,0.12);
    overflow-y: auto;
  }
  .cnd-mobile-drawer.open { right: 0; }
  .cnd-drawer-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 22px;
    border-bottom: 1px solid #F3F4F6;
  }
  .cnd-drawer-close {
    background: #F3F4F6;
    border: none;
    width: 36px; height: 36px;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem;
    color: #374151;
    cursor: pointer;
    transition: background 0.2s;
  }
  .cnd-drawer-close:hover { background: #E5E7EB; }
  .cnd-drawer-search {
    padding: 16px 22px;
    border-bottom: 1px solid #F3F4F6;
  }
  .cnd-drawer-search form {
    position: relative;
  }
  .cnd-drawer-search input {
    width: 100%;
    padding: 11px 16px 11px 40px;
    border-radius: 10px;
    border: 1.5px solid #E5E7EB;
    background: #F9FAFB;
    font-size: 0.9rem;
    font-family: 'Outfit', sans-serif;
    color: #1A1640;
    outline: none;
  }
  .cnd-drawer-search input:focus {
    border-color: #3F3590;
    background: #fff;
  }
  .cnd-drawer-search i {
    position: absolute;
    left: 13px; top: 50%;
    transform: translateY(-50%);
    color: #9CA3AF;
  }
  .cnd-drawer-nav {
    flex: 1;
    padding: 12px 14px;
  }
  .cnd-drawer-nav a {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 13px 14px;
    color: #374151;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.95rem;
    font-family: 'Outfit', sans-serif;
    border-radius: 10px;
    transition: all 0.2s;
    margin-bottom: 3px;
  }
  .cnd-drawer-nav a i { font-size: 1.05rem; color: #6B7280; width: 20px; }
  .cnd-drawer-nav a:hover, .cnd-drawer-nav a.active {
    background: #EEF2FF;
    color: #3F3590;
  }
  .cnd-drawer-nav a:hover i, .cnd-drawer-nav a.active i { color: #3F3590; }
  .cnd-drawer-divider {
    height: 1px;
    background: #F3F4F6;
    margin: 8px 14px;
  }
  .cnd-drawer-footer {
    padding: 16px 22px;
    border-top: 1px solid #F3F4F6;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .cnd-drawer-footer .btn-login {
    width: 100%;
    justify-content: center;
    padding: 12px;
    font-size: 0.95rem;
    border-radius: 12px;
  }
  .cnd-drawer-footer .btn-logout {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 11px;
    border-radius: 12px;
    border: 1.5px solid #FEE2E2;
    color: #DC2626;
    font-weight: 600;
    font-size: 0.9rem;
    text-decoration: none;
    font-family: 'Outfit', sans-serif;
    transition: background 0.2s;
  }
  .cnd-drawer-footer .btn-logout:hover { background: #FEF2F2; }
  .cnd-drawer-user-info {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 22px 10px;
    border-bottom: 1px solid #F3F4F6;
  }
  .cnd-drawer-avatar {
    width: 40px; height: 40px;
    border-radius: 50%;
    background: #3F3590;
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-weight: 700;
    font-size: 0.9rem;
    flex-shrink: 0;
  }
  .cnd-drawer-avatar-name { font-weight: 700; font-size: 0.9rem; color: #1A1640; }
  .cnd-drawer-avatar-role { font-size: 0.78rem; color: #6B7280; }

  @keyframes cndFadeIn { from { opacity:0; } to { opacity:1; } }

  /* ═══════════════════════════ RESPONSIVE BREAKPOINTS ═══════════════════════════ */
  @media (max-width: 1200px) {
    .nav-links-menu { display: none !important; }
  }
  @media (max-width: 991px) {
    #cnd-navbar .search-nav-container { display: none; }
  }
  @media (max-width: 767px) {
    #cnd-navbar { min-height: 64px; padding: 0 4%; }
    #cnd-navbar .logo { font-size: 1.25rem; }
    #cnd-navbar .logo-icon { width: 32px; height: 32px; border-radius: 9px; }
    #cnd-navbar .btn-login { display: none; }
    #cnd-navbar .nav-icon-link { display: none !important; }
    .cnd-hamburger { display: flex; align-items: center; }
    #cnd-navbar .btn-profile { display: none; }
  }
</style>

<header>
  <nav id="cnd-navbar">
    <!-- ── 1. LOGO & LINKS (Left) ── -->
    <div class="d-flex align-items-center">
      <a href="<?= base_url('/') ?>" class="logo">
        <div class="logo-icon">
          <svg width="22" height="22" viewBox="0 0 72 72" fill="none">
            <path d="M10 32 Q36 8 62 32" stroke="#F9A05E" stroke-width="6" stroke-linecap="round" fill="none"/>
            <path d="M26 32 Q36 22 46 32 L46 58 Q46 60 44 60 L28 60 Q26 60 26 58 Z" fill="white" opacity="0.9"/>
          </svg>
        </div>
        Learn <span>NextDoor</span>
      </a>
      <ul class="nav-links-menu">
        <li><a href="<?= base_url('/') ?>" <?= (current_url() == base_url('/') || current_url() == base_url()) ? 'class="active"' : '' ?>>Home</a></li>
        <li><a href="<?= base_url('classes') ?>" <?= strpos(current_url(), base_url('classes')) !== false ? 'class="active"' : '' ?>>Browse</a></li>
      </ul>
    </div>

    <!-- ── 2. SEARCH (Middle) ── -->
    <div class="search-nav-container">
      <form class="search-nav" action="<?= base_url('classes') ?>" method="GET">
        <i class="bi bi-search"></i>
        <input type="text" name="q" placeholder="Search classes, workshops..." value="<?= esc($q ?? '') ?>">
      </form>
    </div>

    <!-- ── 3. ACTIONS (Right) ── -->
    <div class="nav-actions-container">
      <?php if ($showJoinLink): ?>
        <a href="<?= base_url('join-as-provider') ?>" class="btn d-none d-lg-flex align-items-center gap-2 fw-700 text-white rounded-pill px-3 py-2 me-2" style="font-size: 0.8rem; background: #F9A05E;">
           Join as Provider
        </a>
      <?php endif; ?>

      <a href="<?= base_url('cart') ?>" class="nav-icon-link" aria-label="Cart">
        <i class="bi bi-bag"></i>
        <span class="nav-icon-badge">0</span>
      </a>

      <?php if (session()->get('user_id')): ?>
        <div class="dropdown d-none d-md-block">
          <button class="btn-profile dropdown-toggle" type="button" id="navProfileBtn" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle"></i> <?= esc($__firstName) ?>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2" style="border-radius:14px; min-width:210px;">
            <li><a class="dropdown-item rounded-3 px-3 py-2 fw-600" href="<?= base_url('profile') ?>"><i class="bi bi-person me-2 text-primary"></i> Profile</a></li>
            <li><a class="dropdown-item rounded-3 px-3 py-2 fw-600" href="<?= base_url('activity') ?>"><i class="bi bi-calendar-event me-2 text-primary"></i> Bookings</a></li>
            <li><hr class="dropdown-divider opacity-25"></li>
            <li><a class="dropdown-item rounded-3 px-3 py-2 text-danger fw-600" href="<?= base_url('logout') ?>"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
          </ul>
        </div>
      <?php else: ?>
        <a href="<?= base_url('login') ?>" class="btn-login">Login</a>
      <?php endif; ?>

      <!-- Mobile hamburger -->
      <button class="cnd-hamburger" id="cndHamburger" aria-label="Open menu" aria-expanded="false">
        <i class="bi bi-list"></i>
      </button>
    </div>
  </nav>
</header>

<!-- ── MOBILE OVERLAY ── -->
<div class="cnd-mobile-overlay" id="cndOverlay"></div>

<!-- ── MOBILE DRAWER ── -->
<div class="cnd-mobile-drawer" id="cndDrawer" role="dialog" aria-modal="true" aria-label="Navigation menu">
  <!-- Drawer header -->
  <div class="cnd-drawer-header">
    <a href="<?= base_url('/') ?>" class="logo" style="font-size:1.2rem;">
      <div class="logo-icon" style="width:30px;height:30px;border-radius:8px;">
        <svg width="18" height="18" viewBox="0 0 72 72" fill="none">
          <path d="M10 32 Q36 8 62 32" stroke="#F9A05E" stroke-width="6" stroke-linecap="round" fill="none"/>
          <path d="M26 32 Q36 22 46 32 L46 58 Q46 60 44 60 L28 60 Q26 60 26 58 Z" fill="white" opacity="0.9"/>
        </svg>
      </div>
      Learn <span>NextDoor</span>
    </a>
    <button class="cnd-drawer-close" id="cndDrawerClose" aria-label="Close menu">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  <?php if (session()->get('user_id')): ?>
  <!-- Logged-in user info -->
  <div class="cnd-drawer-user-info">
    <div class="cnd-drawer-avatar"><?= esc($__initials ?: '?') ?></div>
    <div>
      <div class="cnd-drawer-avatar-name"><?= esc($__uName) ?></div>
      <div class="cnd-drawer-avatar-role"><?= $__role == 2 ? 'Provider' : 'Learner' ?></div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Drawer search -->
  <div class="cnd-drawer-search">
    <form action="<?= base_url('classes') ?>" method="GET">
      <i class="bi bi-search"></i>
      <input type="text" name="q" placeholder="Search classes, workshops..." value="<?= esc($q ?? '') ?>">
    </form>
  </div>

  <!-- Drawer nav links -->
  <nav class="cnd-drawer-nav">
    <a href="<?= base_url('/') ?>" <?= (current_url() == base_url('/') || current_url() == base_url()) ? 'class="active"' : '' ?>>
      <i class="bi bi-house"></i> Home
    </a>
    <a href="<?= base_url('classes') ?>" <?= strpos(current_url(), base_url('classes')) !== false ? 'class="active"' : '' ?>>
      <i class="bi bi-grid"></i> Browse Classes
    </a>
    <?php if ($showJoinLink): ?>
    <a href="<?= base_url('join-as-provider') ?>" style="color: #F9A05E;">
      <i class="bi bi-mortarboard" style="color: #F9A05E;"></i> Join as Provider
    </a>
    <?php endif; ?>
    <?php if (session()->get('user_id')): ?>
    <div class="cnd-drawer-divider"></div>
    <a href="<?= base_url('profile') ?>">
      <i class="bi bi-person"></i> My Profile
    </a>
    <a href="<?= base_url('activity') ?>">
      <i class="bi bi-calendar-event"></i> My Bookings
    </a>
    <?php if ($__role == 2): ?>
    <a href="<?= base_url('provider/dashboard') ?>">
      <i class="bi bi-speedometer2"></i> Provider Dashboard
    </a>
    <?php endif; ?>
    <?php endif; ?>
  </nav>

  <!-- Drawer footer: auth actions -->
  <div class="cnd-drawer-footer">
    <?php if (session()->get('user_id')): ?>
      <a href="<?= base_url('logout') ?>" class="btn-logout">
        <i class="bi bi-box-arrow-right"></i> Logout
      </a>
    <?php else: ?>
      <a href="<?= base_url('login') ?>" class="btn-login">
        <i class="bi bi-person-circle"></i> Login to your account
      </a>
      <a href="<?= base_url('register') ?>" style="text-align:center; font-size:0.88rem; color:#6B7280; text-decoration:none; font-weight:600; font-family:'Outfit',sans-serif; padding:8px;">
        Don't have an account? <span style="color:#3F3590;">Sign up free</span>
      </a>
    <?php endif; ?>
  </div>
</div>

<script>
(function () {
  'use strict';

  var hamburger = document.getElementById('cndHamburger');
  var overlay   = document.getElementById('cndOverlay');
  var drawer    = document.getElementById('cndDrawer');
  var closeBtn  = document.getElementById('cndDrawerClose');
  var nav       = document.getElementById('cnd-navbar');

  function openDrawer() {
    drawer.classList.add('open');
    overlay.style.display = 'block';
    document.body.style.overflow = 'hidden';
    if (hamburger) hamburger.setAttribute('aria-expanded', 'true');
  }
  function closeDrawer() {
    drawer.classList.remove('open');
    overlay.style.display = 'none';
    document.body.style.overflow = '';
    if (hamburger) hamburger.setAttribute('aria-expanded', 'false');
  }

  if (hamburger) hamburger.addEventListener('click', openDrawer);
  if (overlay)   overlay.addEventListener('click', closeDrawer);
  if (closeBtn)  closeBtn.addEventListener('click', closeDrawer);

  // Close on ESC
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeDrawer();
  });

  // Navbar scroll shadow
  if (nav) {
    window.addEventListener('scroll', function () {
      nav.classList.toggle('scrolled', window.scrollY > 10);
    }, { passive: true });
  }
})();
</script>
