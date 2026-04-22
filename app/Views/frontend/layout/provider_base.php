<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Provider Portal | Class Next Door' ?></title>
    
    <!-- Bootstrap 5.3 Css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Provider Theme Styles -->
    <link rel="stylesheet" href="<?= base_url('assets/provider/css/style.css') ?>">
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <?= $this->renderSection('css') ?>
</head>
<body class="provider-body">

    <div class="provider-wrapper">
        <!-- Mobile Sidebar Overlay -->
        <div class="provider-sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Sidebar -->
        <aside class="provider-sidebar">
            <div class="sidebar-header" style="height: auto; min-height: 70px; display: flex; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid var(--provider-border);">
                <img src="<?= base_url('assets/frontend/img/logo.png') ?>" alt="Logo" style="height: 32px; width: auto;">
            </div>
            
            <nav class="sidebar-nav">
                <a href="<?= base_url('provider/dashboard') ?>" class="nav-link <?= current_url() == base_url('provider/dashboard') ? 'active' : '' ?>">
                    <i class="bi bi-grid"></i>
                    <span>Dashboard</span>
                </a>
                <a href="<?= base_url('provider/listings/create') ?>" class="nav-link <?= strpos(current_url(), base_url('provider/listings/create')) !== false ? 'active' : '' ?>">
                    <i class="bi bi-plus-lg"></i>
                    <span>Create a Class</span>
                </a>
                <a href="<?= base_url('provider/instructors') ?>" class="nav-link <?= strpos(current_url(), base_url('provider/instructors')) !== false ? 'active' : '' ?>">
                    <i class="bi bi-people"></i>
                    <span>Manage Instructors</span>
                </a>
                <a href="<?= base_url('provider/listings') ?>" class="nav-link <?= current_url() == base_url('provider/listings') ? 'active' : '' ?>">
                    <i class="bi bi-list-ul"></i>
                    <span>My Listed Classes</span>
                </a>
                <a href="<?= base_url('provider/payouts') ?>" class="nav-link <?= strpos(current_url(), base_url('provider/payouts')) !== false ? 'active' : '' ?>">
                    <i class="bi bi-wallet2"></i>
                    <span>Earnings & Payout</span>
                </a>
                <a href="<?= base_url('profile') ?>" class="nav-link <?= (strpos(current_url(), base_url('profile')) !== false || strpos(current_url(), '/profile') !== false)  ? 'active' : '' ?>">
                    <i class="bi bi-person"></i>
                    <span>My Account</span>
                </a>
                <a href="<?= base_url('provider/availability') ?>" class="nav-link <?= strpos(current_url(), base_url('provider/availability')) !== false ? 'active' : '' ?>">
                    <i class="bi bi-calendar-x"></i>
                    <span>Holiday & Cancellation</span>
                </a>
                <a href="<?= base_url('provider/support') ?>" class="nav-link <?= strpos(current_url(), base_url('provider/support')) !== false ? 'active' : '' ?>">
                    <i class="bi bi-headset"></i>
                    <span>Provider Support</span>
                </a>
            </nav>

            <div class="sidebar-footer">
                <a href="<?= base_url('logout') ?>" class="logout-link">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </div>
        </aside>


        <!-- Main Content -->
        <div class="provider-main">
            <!-- Header -->
            <header class="provider-header">
                <div class="header-left d-flex align-items-center gap-2 gap-md-4">
                    <!-- Hamburger (mobile only) -->
                    <button class="provider-sidebar-toggle" id="sidebarToggle" aria-label="Toggle menu" aria-expanded="false">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="user-profile d-flex align-items-center gap-2 gap-md-3">
                        <div class="user-avatar" style="width: 38px; height: 38px; background-color: #6366F1; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem;">
                            <?= substr($user->name ?? 'U', 0, 1) ?>
                        </div>
                        <div class="user-info d-none d-sm-flex flex-column" style="line-height: 1.1;">
                            <span class="user-name" style="font-size: 14px; font-weight: 700; color: #111827;"><?= esc($user->name ?? 'User') ?></span>
                            <?php 
                                $status = $user->provider_verification_status ?? 'pending';
                                $badgeClass = 'badge-pending';
                                if ($status === 'approved') $badgeClass = 'badge-approved';
                                if ($status === 'rejected') $badgeClass = 'badge-rejected';
                            ?>
                            <span class="user-badge <?= $badgeClass ?>" style="font-size: 8px; margin-top: 2px;"><?= $status ?></span>
                        </div>
                    </div>
                </div>
                <div class="header-right">
                    <div class="d-flex align-items-center gap-3 gap-md-4">
                        <a href="<?= base_url('provider/notifications') ?>" class="notification-btn" style="font-size: 1.3rem;">
                            <i class="bi bi-bell"></i>
                            <?php 
                                $unreadCount = model('App\Models\ActivityLogModel')->getUnreadCount(logged('id'));
                            ?>
                            <?php if ($unreadCount > 0): ?>
                                <span class="notification-count" style="width: 14px; height: 14px; font-size: 8px; top: -1px; right: -1px;">
                                    <?= $unreadCount > 9 ? '9+' : $unreadCount ?>
                                </span>
                            <?php endif; ?>
                        </a>
                        <a href="<?= base_url('provider/toggle-mode') ?>" class="exit-link d-flex align-items-center gap-1">
                            <i class="bi bi-box-arrow-right d-md-none" style="font-size: 1.3rem;"></i>
                            <span class="d-none d-md-inline">Exit Provider Portal</span>
                        </a>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="provider-content">
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap & Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <!-- Provider Mobile Sidebar JS -->
    <script>
    (function () {
        'use strict';
        var toggle  = document.getElementById('sidebarToggle');
        var overlay = document.getElementById('sidebarOverlay');
        var sidebar = document.querySelector('.provider-sidebar');

        function openSidebar() {
            if (!sidebar) return;
            sidebar.classList.add('open');
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
            if (toggle) toggle.setAttribute('aria-expanded', 'true');
        }
        function closeSidebar() {
            if (!sidebar) return;
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
            document.body.style.overflow = '';
            if (toggle) toggle.setAttribute('aria-expanded', 'false');
        }

        if (toggle)  toggle.addEventListener('click', openSidebar);
        if (overlay) overlay.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeSidebar();
        });

        // Close sidebar when a nav link is tapped on mobile
        if (sidebar) {
            sidebar.querySelectorAll('.nav-link, .logout-link').forEach(function (link) {
                link.addEventListener('click', function () {
                    if (window.innerWidth <= 768) closeSidebar();
                });
            });
        }
    })();
    </script>

    <?= $this->renderSection('js') ?>
</body>
</html>
