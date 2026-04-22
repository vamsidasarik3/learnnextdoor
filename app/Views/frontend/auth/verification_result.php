<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('content') ?>
<section class="py-5 bg-light min-vh-100 d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
                    <?php if ($success): ?>
                        <div class="bg-success-soft text-success rounded-circle p-4 d-inline-block mb-4">
                            <i class="bi bi-patch-check-fill display-4"></i>
                        </div>
                        <h2 class="fw-bold mb-3">Email Verified!</h2>
                        <p class="text-muted mb-4"><?= esc($message) ?></p>
                        <a href="<?= base_url('provider/verification') ?>" class="btn btn-accent btn-lg rounded-pill px-5">Go to Profile</a>
                    <?php else: ?>
                        <div class="bg-danger-soft text-danger rounded-circle p-4 d-inline-block mb-4">
                            <i class="bi bi-exclamation-triangle-fill display-4"></i>
                        </div>
                        <h2 class="fw-bold mb-3">Verification Error</h2>
                        <p class="text-muted mb-4"><?= esc($message) ?></p>
                        <div class="d-grid gap-2">
                            <a href="<?= base_url('provider/verification') ?>" class="btn btn-accent btn-lg rounded-pill">Resend Link</a>
                            <a href="<?= base_url() ?>" class="btn btn-outline-secondary btn-lg rounded-pill">Back Home</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.bg-success-soft { background: rgba(46, 204, 113, 0.1); }
.bg-danger-soft { background: rgba(231, 76, 60, 0.1); }
</style>
<?= $this->endSection() ?>
