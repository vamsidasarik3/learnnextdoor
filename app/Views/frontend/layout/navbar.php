<!--
  NAVBAR PARTIAL — frontend/layout/navbar.php
  Theme: Purple→Pink→Yellow gradient background
  ─────────────────────────────────────────────
  · Brand: "Class Next Door" in white + gold dot
  · Mobile: location pill + hamburger
  · Desktop: nav links + location pill + gold "List a Class" button
-->
<header>
  <nav
    class="navbar navbar-expand-lg cnd-navbar"
    id="cnd-navbar"
    role="navigation"
    aria-label="Main navigation">

    <div class="container-fluid px-3 px-lg-4">

      <!-- ── Brand / Logo ── -->
      <a class="navbar-brand cnd-brand d-flex align-items-center gap-2" href="<?= base_url('/') ?>" aria-label="Learn Next Door — Home" style="text-decoration:none;">
         <img src="<?= base_url('assets/frontend/img/logo-icon.png') ?>" alt="Learn Next Door icon" height="40" class="d-inline-block flex-shrink-0">
         <span style="font-size:1.15rem;font-weight:800;line-height:1;color:#fff;letter-spacing:-.3px;">Learn <span style="color:var(--cnd-accent);">Next Door</span></span>
      </a>

      <?php
        $__savedLoc = session()->get('cnd_location_name')
                   ?? (isset($_COOKIE['cnd_location_name']) && $_COOKIE['cnd_location_name'] !== ''
                       ? rawurldecode($_COOKIE['cnd_location_name'])
                       : null);
        $__mobLocLabel = $__savedLoc ? htmlspecialchars(explode(',', $__savedLoc)[0], ENT_QUOTES) : 'Near you';
      ?>
      <!-- ── Location Pill (mobile — always visible next to hamburger) ── -->
      <button
        class="btn cnd-location-pill d-lg-none ms-auto me-2"
        type="button"
        data-bs-toggle="modal"
        data-bs-target="#locationModal"
        aria-label="Select location"
        id="mob-location-btn">
        <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
        <span class="cnd-location-pill-text" id="mob-location-text"><?= $__mobLocLabel ?></span>
        <i class="bi bi-chevron-down small" aria-hidden="true"></i>
      </button>

      <!-- ── Hamburger Toggle ── -->
      <button
        class="navbar-toggler"
        type="button"
        data-bs-toggle="collapse"
        data-bs-target="#cndNavMenu"
        aria-controls="cndNavMenu"
        aria-expanded="false"
        aria-label="Toggle navigation menu">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- ── Collapsible Menu ── -->
      <div class="collapse navbar-collapse" id="cndNavMenu">
        <ul class="navbar-nav mb-2 mb-lg-0 gap-1" role="list">

          <!-- Home -->
          <li class="nav-item" role="listitem">
            <a class="nav-link cnd-nav-link <?= (uri_string() === '' || uri_string() === '/') ? 'active' : '' ?>"
               href="<?= base_url('/') ?>"
               <?= (uri_string() === '' || uri_string() === '/') ? 'aria-current="page"' : '' ?>>
              <i class="bi bi-house-door" aria-hidden="true"></i>
              <span>Home</span>
            </a>
          </li>

          <!-- Browse Classes -->
          <li class="nav-item" role="listitem">
            <a class="nav-link cnd-nav-link <?= str_starts_with(uri_string(), 'classes') ? 'active' : '' ?>"
               href="<?= base_url('classes') ?>"
               <?= str_starts_with(uri_string(), 'classes') ? 'aria-current="page"' : '' ?>>
              <i class="bi bi-search" aria-hidden="true"></i>
              <span>Browse Classes</span>
            </a>
          </li>


          <li class="nav-item" role="listitem">
            <a class="nav-link cnd-nav-link <?= str_starts_with(uri_string(), 'contact') ? 'active' : '' ?>"
               href="<?= base_url('contact') ?>"
               <?= str_starts_with(uri_string(), 'contact') ? 'aria-current="page"' : '' ?>>
              <i class="bi bi-chat-dots" aria-hidden="true"></i>
              <span>Contact Us</span>
            </a>
          </li>

        </ul>

        <?php 
          $__user = session()->get('cnd_user'); 
          $__role = (int)($__user['role'] ?? 0);
          $__verificationStatus = $__user['provider_verification_status'] ?? null;
          
          // Hide "Join as Provider" if already logged in as Provider (role 2) 
          // OR if role 3 but verification is pending/approved
          $showJoinLink = (!$__user || ($__role == 3 && empty($__verificationStatus)));
        ?>

        <?php if ($showJoinLink): ?>
        <!-- ── Join as Provider CTA (Visible to Guests & Parents not yet applying) ── -->
        <div class="mx-lg-auto">
          <a href="<?= base_url('join-as-provider') ?>" class="nav-link cnd-nav-link fw-bold px-3 py-2 d-flex align-items-center gap-2" style="background: rgba(255,255,255,0.15); border-radius: 12px; border: 1px solid rgba(255,255,255,0.15); margin: 0.5rem 0;">
            <i class="bi bi-mortarboard-fill" style="font-size: 1.1rem;"></i>
            <span>Join as Provider</span>
          </a>
        </div>
        <?php else: ?>
          <div class="mx-lg-auto"></div> 
        <?php endif; ?>

        <!-- ── Right-side: Location pill + Auth button ── -->
        <div class="d-flex align-items-center gap-2 ms-lg-auto flex-wrap flex-lg-nowrap">

          <!-- Location selector (desktop) -->
          <?php
            $__deskLocLabel = $__savedLoc
                ? htmlspecialchars(explode(',', $__savedLoc)[0], ENT_QUOTES)
                : 'Select Location';
          ?>
          <button
            class="btn cnd-location-btn d-none d-lg-inline-flex"
            type="button"
            data-bs-toggle="modal"
            data-bs-target="#locationModal"
            aria-label="Select your location"
            id="desk-location-btn">
            <i class="bi bi-geo-alt-fill" aria-hidden="true"></i>
            <span id="desk-location-text" class="text-truncate" style="max-width:130px;"><?= $__deskLocLabel ?></span>
            <i class="bi bi-chevron-down small" aria-hidden="true"></i>
          </button>

          <!-- Auth / Account Link (Dropdown for logged-in users) -->
          <?php if (session()->get('user_id')): 
            $user_data = session()->get('cnd_user');
          ?>
            <div class="dropdown">
              <button 
                class="btn cnd-btn-gold dropdown-toggle d-flex align-items-center gap-2" 
                type="button" 
                id="navAccountDropdown" 
                data-bs-toggle="dropdown" 
                aria-expanded="false"
                style="padding: .55rem 1.4rem; border-radius: var(--cnd-radius-pill);">
                <i class="bi bi-person-circle" style="font-size: 1.1rem;"></i>
                <span class="d-none d-sm-inline">My Account</span>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 mt-2 p-2" aria-labelledby="navAccountDropdown" style="border-radius: 12px; min-width: 220px;">
                <?php if ($user_data['role'] == 3): // Parent ?>
                  <li>
                    <a class="dropdown-item rounded-3 px-3 py-2 d-flex align-items-center gap-2" href="<?= base_url('activity') ?>">
                      <i class="bi bi-calendar-check text-pink"></i>
                      <span>My Booked Classes</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item rounded-3 px-3 py-2 d-flex align-items-center gap-2" href="<?= base_url('profile') ?>">
                      <i class="bi bi-person-gear text-pink"></i>
                      <span>My Account</span>
                    </a>
                  </li>
                <?php elseif ($user_data['role'] == 2): // Provider ?>
                  <li>
                    <a class="dropdown-item rounded-3 px-3 py-2 d-flex align-items-center gap-2" href="<?= base_url('provider/dashboard') ?>">
                      <i class="bi bi-speedometer2 text-pink"></i>
                      <span>Provider Dashboard</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item rounded-3 px-3 py-2 d-flex align-items-center gap-2" href="<?= base_url('provider/listings/create') ?>">
                      <i class="bi bi-plus-circle text-pink"></i>
                      <span>Create a New Class</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item rounded-3 px-3 py-2 d-flex align-items-center gap-2" href="<?= base_url('provider/listings') ?>">
                      <i class="bi bi-card-list text-pink"></i>
                      <span>My Classes</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item rounded-3 px-3 py-2 d-flex align-items-center gap-2" href="<?= base_url('provider/instructors') ?>">
                      <i class="bi bi-people text-pink"></i>
                      <span>My Instructors</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item rounded-3 px-3 py-2 d-flex align-items-center gap-2" href="<?= base_url('activity') ?>">
                      <i class="bi bi-calendar-check text-pink"></i>
                      <span>My Booked Classes</span>
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item rounded-3 px-3 py-2 d-flex align-items-center gap-2" href="<?= base_url('profile') ?>">
                      <i class="bi bi-person-gear text-pink"></i>
                      <span>My Account</span>
                    </a>
                  </li>
                <?php elseif ($user_data['role'] == 1): // Admin ?>
                  <li>
                    <a class="dropdown-item rounded-3 px-3 py-2 d-flex align-items-center gap-2" href="<?= base_url('admin/dashboard') ?>">
                      <i class="bi bi-shield-lock text-pink"></i>
                      <span>Admin Panel</span>
                    </a>
                  </li>
                <?php endif; ?>

                <li><hr class="dropdown-divider opacity-50"></li>
                <li>
                  <a class="dropdown-item rounded-3 px-3 py-2 d-flex align-items-center gap-2 text-danger" href="<?= base_url('logout') ?>">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                  </a>
                </li>
              </ul>
            </div>
          <?php else: ?>
            <a href="<?= base_url('login') ?>" class="btn cnd-btn-gold px-4 py-2" id="nav-login-btn" style="border-radius: var(--cnd-radius-pill);">
              <i class="bi bi-box-arrow-in-right me-1"></i> Login
            </a>
          <?php endif; ?>

        </div>
      </div>
      <!-- /#cndNavMenu -->

    </div>
    <!-- /.container-fluid -->

  </nav>
</header>
