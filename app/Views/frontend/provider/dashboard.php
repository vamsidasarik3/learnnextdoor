<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('css') ?>
<style>
    .provider-dashboard-hero {
        background: linear-gradient(135deg, #3F3590 0%, #7778F6 100%);
        padding: 5rem 0;
        color: #fff;
        border-radius: 0 0 2rem 2rem;
    }
    .option-card {
        background: #fff;
        border-radius: 1.5rem;
        padding: 2.5rem;
        border: 2px solid transparent;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        height: 100%;
        text-align: center;
        text-decoration: none !important;
        display: block;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
    }
    .option-card:hover {
        transform: translateY(-10px);
        border-color: #3F3590;
        box-shadow: 0 20px 40px rgba(79, 70, 229, 0.15);
    }
    .option-icon {
        width: 80px;
        height: 80px;
        background: rgba(79, 70, 229, 0.08);
        border-radius: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: #3F3590;
        margin: 0 auto 1.5rem;
        transition: all 0.3s;
    }
    .option-card:hover .option-icon {
        background: #3F3590;
        color: #fff;
        transform: rotate(5deg) scale(1.1);
    }
    .option-title {
        color: #111827;
        font-weight: 800;
        margin-bottom: 0.75rem;
        font-size: 1.5rem;
    }
    .option-desc {
        color: #6b7280;
        font-size: 1rem;
        line-height: 1.5;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<section class="provider-dashboard-hero text-center mb-5">
    <div class="container">
        <h1 class="display-4 fw-900 mb-3">Welcome, <?= esc($user->name) ?>!</h1>
        <p class="lead opacity-90 mx-auto" style="max-width: 600px;">
            Grow your teaching business. Choose an action below to manage your academy.
        </p>
    </div>
</section>

<?php if ($user->provider_verification_status === 'pending' || $user->provider_verification_status === 'rejected'): ?>
<div class="container mt-n4 mb-4">
    <?php if ($user->provider_verification_message): ?>
        <!-- 🔔 Verification Update Badge -->
        <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-3 d-flex align-items-center gap-3 animate__animated animate__headShake">
            <div class="bg-danger text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                <i class="bi bi-bell-fill fs-5"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1">🔔 Verification Update</h6>
                <p class="mb-1 small fw-bold text-dark">Admin message:</p>
                <p class="mb-0 opacity-90 small italic">"<?= esc($user->provider_verification_message) ?>"</p>
            </div>
            <a href="<?= base_url('provider/verification') ?>" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">Fix Now</a>
        </div>
    <?php endif; ?>

    <?php if ($user->provider_verification_status === 'pending' && !$user->provider_verification_message): ?>
    <div class="alert alert-warning border-0 shadow-sm rounded-4 p-4 d-flex align-items-center gap-3">
        <div class="bg-warning text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
            <i class="bi bi-clock-history fs-5"></i>
        </div>
        <div>
            <h6 class="fw-bold mb-1">Account Under Verification</h6>
            <p class="mb-0 opacity-75 small">Your provider account is under verification. Classes you create will go live after approval.</p>
        </div>
        <a href="<?= base_url('provider/verification') ?>" class="btn btn-sm btn-outline-warning ms-auto rounded-pill px-3 fw-bold">Check Status</a>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<div class="container pb-5">
    <div class="row g-4 justify-content-center">
        <!-- Option 1: Create a Class -->
        <div class="col-md-5 col-lg-4">
            <a href="<?= base_url('provider/listings/create') ?>" class="option-card">
                <div class="option-icon">
                    <i class="bi bi-plus-square-dotted"></i>
                </div>
                <h3 class="option-title">Create a Class</h3>
                <p class="option-desc">
                    Launch a new recurring class, one-time workshop, or multi-day course.
                </p>
            </a>
        </div>

        <!-- Option 2: My Classes -->
        <div class="col-md-5 col-lg-4">
            <a href="<?= base_url('provider/listings') ?>" class="option-card">
                <div class="option-icon">
                    <i class="bi bi-grid-1x2"></i>
                </div>
                <h3 class="option-title">My Classes</h3>
                <p class="option-desc">
                    Manage your active listings, track student counts, and view feedback.
                </p>
            </a>
        </div>

        <!-- Option 3: Student Payments -->
        <div class="col-md-5 col-lg-4">
            <a href="<?= base_url('provider/bookings') ?>" class="option-card">
                <div class="option-icon" style="background: rgba(16, 185, 129, 0.08); color: #10b981;">
                    <i class="bi bi-people-fill"></i>
                </div>
                <h3 class="option-title">Student List</h3>
                <p class="option-desc">
                    View list of students who have booked your classes and their payment status.
                </p>
            </a>
        </div>

        <!-- Option 4: Payouts -->
        <div class="col-md-5 col-lg-4">
            <a href="<?= base_url('provider/payouts') ?>" class="option-card">
                <div class="option-icon" style="background: rgba(245, 158, 11, 0.08); color: #f59e0b;">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <h3 class="option-title">Earnings & Payouts</h3>
                <p class="option-desc">
                    Track your total earnings, commissions, and upcoming settlements.
                </p>
            </a>
        </div>

        <!-- Option 5: Instructors -->
        <div class="col-md-5 col-lg-4">
            <a href="<?= base_url('provider/instructors') ?>" class="option-card">
                <div class="option-icon" style="background: rgba(124, 77, 255, 0.08); color: #7C4DFF;">
                    <i class="bi bi-person-badge"></i>
                </div>
                <h3 class="option-title">Instructors</h3>
                <p class="option-desc">
                    Manage your team of instructors, their profiles, and verification status.
                </p>
            </a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
