<?= $this->extend('frontend/layout/provider_base') ?>

<?= $this->section('content') ?>
<div class="dashboard-intro">
    <h1 class="dashboard-title">Dashboard</h1>
    <p class="dashboard-subtitle">Welcome back! Here's your overview</p>
</div>

<!-- Verification Status Banner -->
<?php if ($user->role == 2 && $user->provider_verification_status !== 'approved'): ?>
    <?php 
        $statusClass = 'alert-warning';
        $statusIcon = 'bi-clock-history';
        $statusTitle = 'Verification Pending';
        $statusDesc = 'Your profile is currently under review. You can still set up your classes, but they will be live once verified.';
        
        if ($user->provider_verification_status === 'rejected') {
            $statusClass = 'alert-danger';
            $statusIcon = 'bi-exclamation-triangle-fill';
            $statusTitle = 'Verification Action Required';
            $statusDesc = 'Your verification was not approved. Please update your details and resubmit.';
        }
    ?>
    <div class="alert <?= $statusClass ?> border-0 rounded-4 p-2 px-3 mb-4 shadow-sm d-flex align-items-center gap-3">
        <div class="flex-shrink-0 bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
            <i class="bi <?= $statusIcon ?> fs-5 <?= str_replace('alert-', 'text-', $statusClass) ?>"></i>
        </div>
        <div class="flex-grow-1">
            <h6 class="fw-bold mb-0 text-dark" style="font-size: 0.9rem;"><?= $statusTitle ?></h6>
            <?php if ($user->provider_verification_message): ?>
                <div class="small opacity-75 mt-0 italic d-none d-md-block" style="font-size: 0.75rem;">
                    <i class="bi bi-chat-left-text me-1"></i> "<?= esc($user->provider_verification_message) ?>"
                </div>
            <?php endif; ?>
        </div>
        <div class="flex-shrink-0">
            <a href="<?= base_url('profile') ?>" class="btn <?= str_replace('alert-', 'btn-', $statusClass) ?> btn-sm rounded-pill px-4 fw-bold">Update KYC</a>
        </div>
    </div>
<?php endif; ?>

<!-- Stats Swiper (Desktop & Mobile) -->
<div class="stats-swiper-container position-relative mb-5">
    <div class="swiper statsSwiper">
        <div class="swiper-wrapper">
            <!-- Active Listings -->
            <div class="swiper-slide">
                <div class="stat-card">
                    <div class="stat-icon-wrapper" style="background-color: #3B82F6;">
                        <i class="bi bi-list-check"></i>
                    </div>
                    <div class="stat-value"><?= number_format($stats->active_listings) ?></div>
                    <div class="stat-label">Active Listings</div>
                </div>
            </div>
            <!-- Total Enrollments -->
            <div class="swiper-slide">
                <div class="stat-card">
                    <div class="stat-icon-wrapper" style="background-color: #10B981;">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div class="stat-value"><?= number_format($stats->mtd_enrollments) ?></div>
                    <div class="stat-label">Total Enrollments (MTD)</div>
                </div>
            </div>
            <!-- Upcoming Settlement -->
            <div class="swiper-slide">
                <div class="stat-card">
                    <div class="stat-icon-wrapper" style="background-color: #8B5CF6;">
                        <i class="bi bi-currency-rupee"></i>
                    </div>
                    <div class="stat-value">₹<?= number_format($stats->upcoming_settlement) ?></div>
                    <div class="stat-label">Upcoming Settlement</div>
                </div>
            </div>
            <!-- Pending Actions -->
            <div class="swiper-slide">
                <div class="stat-card">
                    <div class="stat-icon-wrapper" style="background-color: #F97316;">
                        <i class="bi bi-exclamation-circle"></i>
                    </div>
                    <div class="stat-value"><?= number_format($stats->pending_actions) ?></div>
                    <div class="stat-label">Pending Actions</div>
                </div>
            </div>
            <!-- Classes Today -->
            <div class="swiper-slide">
                <div class="stat-card" style="border-right-color: transparent !important;">
                    <div class="stat-icon-wrapper" style="background-color: #6366F1;">
                        <i class="bi bi-clock"></i>
                    </div>
                    <div class="stat-value"><?= number_format($stats->classes_today) ?></div>
                    <div class="stat-label">Classes Today</div>
                </div>
            </div>
        </div>
        <!-- Navigation Arrows -->
        <div class="swiper-button-next swiper-nav-custom d-md-none"></div>
        <div class="swiper-button-prev swiper-nav-custom d-md-none"></div>
    </div>
</div>

<div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="section-headline mb-0">Quick Actions</h2>
    <div class="d-flex gap-2">
        <div class="swiper-btn-prev-actions swiper-nav-control"><i class="bi bi-chevron-left"></i></div>
        <div class="swiper-btn-next-actions swiper-nav-control"><i class="bi bi-chevron-right"></i></div>
    </div>
</div>

<!-- Actions Swiper -->
<div class="actions-swiper-container mb-5">
    <div class="swiper actionsSwiper">
        <div class="swiper-wrapper">
            <!-- Create a Class -->
            <div class="swiper-slide">
                <a href="<?= base_url('provider/listings/create') ?>" class="action-card">
                    <div class="action-icon-square" style="background-color: #6366F1;">
                        <i class="bi bi-plus-lg"></i>
                    </div>
                    <div class="action-content">
                        <h3 class="action-title">Create a Class</h3>
                        <p class="action-desc">Launch multi-step listing creation wizard</p>
                    </div>
                </a>
            </div>

            <!-- Manage Instructors -->
            <div class="swiper-slide">
                <a href="<?= base_url('provider/instructors') ?>" class="action-card">
                    <div class="action-icon-square" style="background-color: #3B82F6;">
                        <i class="bi bi-people"></i>
                    </div>
                    <div class="action-content">
                        <h3 class="action-title">Manage Instructors</h3>
                        <p class="action-desc">Add, edit, delete instructors and their KYC docs</p>
                    </div>
                </a>
            </div>

            <!-- My Listed Classes -->
            <div class="swiper-slide">
                <a href="<?= base_url('provider/listings') ?>" class="action-card">
                    <div class="action-icon-square" style="background-color: #10B981;">
                        <i class="bi bi-list-ul"></i>
                    </div>
                    <div class="action-content">
                        <h3 class="action-title">My Listed Classes</h3>
                        <p class="action-desc">View all listings with status and quick actions</p>
                    </div>
                </a>
            </div>

            <!-- Earnings & Payout -->
            <div class="swiper-slide">
                <a href="<?= base_url('provider/payouts') ?>" class="action-card">
                    <div class="action-icon-square" style="background-color: #A855F7;">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="action-content">
                        <h3 class="action-title">Earnings & Payout</h3>
                        <p class="action-desc">View settlements, request payouts, raise queries</p>
                    </div>
                </a>
            </div>

            <!-- My Account -->
            <div class="swiper-slide">
                <a href="<?= base_url('profile') ?>" class="action-card">
                    <div class="action-icon-square" style="background-color: #EC4899;">
                        <i class="bi bi-person"></i>
                    </div>
                    <div class="action-content">
                        <h3 class="action-title">My Account</h3>
                        <p class="action-desc">Edit personal details, re-verify contact info</p>
                    </div>
                </a>
            </div>

            <!-- Holiday & Cancellation -->
            <div class="swiper-slide">
                <a href="<?= base_url('provider/availability') ?>" class="action-card">
                    <div class="action-icon-square" style="background-color: #F97316;">
                        <i class="bi bi-calendar-x"></i>
                    </div>
                    <div class="action-content">
                        <h3 class="action-title">Holiday & Cancellation</h3>
                        <p class="action-desc">Declare holidays, cancel sessions, manage refunds</p>
                    </div>
                </a>
            </div>

            <!-- Provider Support -->
            <div class="swiper-slide">
                <a href="javascript:void(0)" class="action-card" data-bs-toggle="modal" data-bs-target="#concernModal">
                    <div class="action-icon-square" style="background-color: #14B8A6;">
                        <i class="bi bi-headset"></i>
                    </div>
                    <div class="action-content">
                        <h3 class="action-title">Provider Support</h3>
                        <p class="action-desc">Raise tickets, track status</p>
                    </div>
                </a>
            </div>

            <!-- Notifications -->
            <div class="swiper-slide">
                <a href="<?= base_url('provider/notifications') ?>" class="action-card">
                    <div class="action-icon-square" style="background-color: #EF4444;">
                        <i class="bi bi-bell"></i>
                    </div>
                    <div class="action-content">
                        <h3 class="action-title">Notifications</h3>
                        <p class="action-desc">All admin-to-provider communications</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Today's Schedule -->
<div class="d-flex align-items-center justify-content-between mb-3">
    <h2 class="section-headline mb-0">Today's Schedule</h2>
    <a href="<?= base_url('provider/listings') ?>" class="text-primary text-decoration-none fw-600" style="font-size: 0.85rem;">View All Classes →</a>
</div>

<div class="schedule-container bg-white rounded-4 border shadow-sm overflow-hidden mb-5">
    <?php if (empty($scheduleToday)): ?>
        <div class="p-5 text-center text-muted">
            <i class="bi bi-calendar-x d-block fs-1 mb-3 opacity-25"></i>
            <p class="mb-0">No classes scheduled for today.</p>
        </div>
    <?php else: ?>
        <ul class="list-group list-group-flush">
            <?php foreach ($scheduleToday as $item): ?>
                <li class="list-group-item p-3 px-4 d-flex align-items-center justify-content-between border-bottom-1">
                    <div class="schedule-info">
                        <h4 class="mb-1 fw-700" style="font-size: 0.95rem; color: #111827;"><?= esc($item->title) ?></h4>
                        <span class="text-muted" style="font-size: 0.8rem;"><?= esc($item->time) ?></span>
                    </div>
                    <span class="badge rounded-pill px-3 py-2 fw-600" style="background-color: #EEF2FF; color: #6366F1; font-size: 0.75rem;">
                        <?= $item->students ?> students
                    </span>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
/* Stats Swiper Customization */
.stats-swiper-container {
    width: 100%;
}
.swiper.statsSwiper {
    padding: 2px 2px 10px 2px;
}
.swiper-nav-custom {
    color: #4F46E5 !important;
    width: 32px !important;
    height: 32px !important;
    background: white;
    border-radius: 50%;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    top: 50% !important;
    transform: translateY(-50%);
}
.swiper-nav-custom::after {
    font-size: 14px !important;
    font-weight: 800;
}
.swiper-button-next.swiper-nav-custom { right: -5px !important; }
.swiper-button-prev.swiper-nav-custom { left: -5px !important; }

.stat-card {
    height: 100%;
    margin-bottom: 0 !important;
    border-radius: 12px;
    padding: 1.5rem;
    transition: box-shadow 0.2s;
}
.stat-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.stat-icon-wrapper {
    width: 38px;
    height: 38px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
    margin-bottom: 1.25rem;
}
.stat-value {
    font-size: 1.6rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.25rem;
}
.stat-label {
    font-size: 0.8rem;
    color: #6B7280;
    font-weight: 500;
}

/* Actions Swiper Refresh */
.actions-swiper-container {
    width: 100%;
}
.swiper.actionsSwiper {
    padding: 2px;
}
.swiper-nav-control {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: white;
    border: 1px solid #E5E7EB;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #4B5563;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 0.9rem;
}
.swiper-nav-control:hover {
    background: #F9FAFB;
    color: #4F46E5;
    border-color: #4F46E5;
}
.swiper-button-disabled {
    opacity: 0.4;
    cursor: not-allowed;
}

.action-card {
    padding: 1.5rem;
    border-radius: 16px;
    height: 100%;
    margin-bottom: 0 !important;
}
.action-icon-square {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    margin-bottom: 1rem;
}
.action-title {
    font-size: 1rem;
    margin-bottom: 0.4rem;
    color: #111827;
    font-weight: 700;
}
.action-desc {
    font-size: 0.8rem;
    margin-bottom: 0;
    color: #6B7280;
    line-height: 1.4;
}

/* Schedule */
.schedule-container .list-group-item {
    border-left: 0;
    border-right: 0;
}
.schedule-container .list-group-item:last-child {
    border-bottom: 0;
}

/* Responsive Overrides */
@media (max-width: 480px) {
    .stat-card { padding: 1.25rem; }
    .stat-value { font-size: 1.4rem; }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(function() {
    // Initialize Stats Swiper
    const statsSwiper = new Swiper('.statsSwiper', {
        slidesPerView: 1.2,
        spaceBetween: 12,
        freeMode: true,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            480: { slidesPerView: 2.2 },
            768: { slidesPerView: 3.2 },
            1200: { 
                slidesPerView: 5,
                spaceBetween: 20,
                allowTouchMove: false 
            }
        }
    });

    // Initialize Quick Actions Swiper
    const actionsSwiper = new Swiper('.actionsSwiper', {
        slidesPerView: 1.15,
        spaceBetween: 16,
        freeMode: true,
        navigation: {
            nextEl: '.swiper-btn-next-actions',
            prevEl: '.swiper-btn-prev-actions',
        },
        breakpoints: {
            480: { slidesPerView: 2.2 },
            768: { slidesPerView: 3.2 },
            1200: { 
                slidesPerView: 4,
                spaceBetween: 20,
                grid: {
                    rows: 2,
                    fill: 'row'
                },
                allowTouchMove: false
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
