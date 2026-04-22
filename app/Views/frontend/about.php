<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('css') ?>
<style>
    .about-hero {
        background: linear-gradient(135deg, #1a1040 0%, #2d1b69 100%);
        padding: 8rem 0 6rem;
        color: #fff;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    .about-hero::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        top: 0; left: 0;
        background: radial-gradient(circle at 50% 50%, rgba(249, 160, 94, 0.15) 0%, transparent 70%);
    }
    .about-title {
        font-size: clamp(2.5rem, 6vw, 4rem);
        font-weight: 900;
        margin-bottom: 1.5rem;
    }
    .feature-icon {
        width: 70px;
        height: 70px;
        background: rgba(249, 160, 94, 0.1);
        color: var(--cnd-accent);
        border-radius: 1.25rem;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }
    .feature-card:hover .feature-icon {
        background: var(--cnd-accent);
        color: #fff;
        transform: scale(1.1) rotate(5deg);
    }
    .testimonial-section {
        background: #f8f9fa;
        padding: 6rem 0;
    }
    .testimonial-card {
        background: #fff;
        padding: 2.5rem;
        border-radius: 1.5rem;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        height: 100%;
        border: 1px solid rgba(0,0,0,0.03);
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<section class="about-hero">
    <div class="container relative z-10">
        <h1 class="about-title">Why <span class="cnd-text-gradient">Class Next Door</span>?</h1>
        <p class="lead mx-auto opacity-75" style="max-width: 700px;">
            We're on a mission to help every child discover their passion by connecting parents with the best local educators and academies.
        </p>
    </div>
</section>

<section class="py-5 mt-5">
    <div class="container py-lg-5">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h2 class="display-5 fw-900 mb-4">Our <span class="text-accent">Vision</span></h2>
                <p class="text-muted fs-5 mb-4">
                    Founded in 2024, Class Next Door was born out of a simple problem: parents spending hours searching for quality classes. 
                </p>
                <div class="row g-4 mt-2">
                    <div class="col-sm-6">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                            <h5 class="fw-bold">Verified Providers</h5>
                            <p class="small text-muted">We manually vet every instructor and academy on our platform.</p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="feature-card">
                            <div class="feature-icon"><i class="bi bi-geo-alt"></i></div>
                            <h5 class="fw-bold">Hyper-Local</h5>
                            <p class="small text-muted">Find the best classes right in your neighborhood with precise geolocation.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="<?= base_url('assets/images/about-vision.jpg') ?>" class="img-fluid rounded-5 shadow-lg" alt="About Us">
            </div>
        </div>
    </div>
</section>

<?php if (!empty($testimonials)): ?>
<section class="testimonial-section">
    <div class="container text-center mb-5">
        <h2 class="fw-900 display-6">Trusted by <span class="text-primary">Thousands of Families</span></h2>
        <p class="text-muted">Real stories from parents exactly like you.</p>
    </div>
    <div class="container">
        <div class="row g-4 overflow-hidden">
            <?php foreach ($testimonials as $t): ?>
            <div class="col-md-4 mb-4">
                <div class="testimonial-card">
                    <div class="text-warning mb-3">
                        <?php for($i=1;$i<=5;$i++): ?>
                            <i class="bi bi-star<?= $i<=$t->rating?'-fill':'' ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <p class="fs-5 text-dark mb-4 italic">"<?= esc($t->feedback) ?>"</p>
                    <div class="d-flex align-items-center">
                        <img src="<?= base_url($t->user_image ?: 'assets/images/default-avatar.png') ?>" class="rounded-circle border" width="45" height="45">
                        <div class="ms-3 text-start">
                            <h6 class="fw-bold mb-0"><?= esc($t->user_name) ?></h6>
                            <small class="text-muted">Parent</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="py-5 bg-dark text-white text-center">
    <div class="container py-5">
        <h2 class="display-5 fw-bold mb-4">Ready to find your child's favorite class?</h2>
        <a href="<?= base_url('classes') ?>" class="btn btn-lg cnd-btn-gold rounded-pill px-5 py-3 fw-bold">Browse Classes Now</a>
    </div>
</section>

<?= $this->endSection() ?>
