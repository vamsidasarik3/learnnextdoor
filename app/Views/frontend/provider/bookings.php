<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('css') ?>
<style>
.provider-bookings-hero {
  background: var(--cnd-gradient);
  padding: 3rem 1.5rem 2rem;
  color: #fff;
}
.pb-card {
  background: #fff;
  border-radius: 16px;
  border: 1.5px solid var(--cnd-card-border);
  box-shadow: var(--cnd-card-shadow);
  overflow: hidden;
  margin-bottom: 1.2rem;
}
.pb-card-header {
  padding: 1.2rem;
  border-bottom: 1px solid var(--cnd-card-border);
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: #fafafa;
}
.pb-student-name { font-weight: 800; font-size: 1.1rem; color: var(--cnd-dark); margin:0;}
.pb-listing-title { font-size: 0.85rem; color: var(--cnd-muted); font-weight: 600; margin-top: 2px; }
.pb-card-body { padding: 1.2rem; }
.pb-meta-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; }
.pb-meta-item { display: flex; align-items: flex-start; gap: 0.6rem; font-size: 0.88rem; color: var(--cnd-dark); }
.pb-meta-item i { color: var(--cnd-pink); margin-top: 2px; }
.pb-meta-label { display: block; font-size: 0.72rem; color: var(--cnd-muted); font-weight: 700; text-transform: uppercase; margin-bottom: 2px; }
.pb-status-badge {
  font-size: .72rem; font-weight: 700; padding: .3rem .8rem;
  border-radius: var(--cnd-radius-pill);
}
.status-paid { background: #dcfce7; color: #16a34a; }
.status-pending { background: #fef9c3; color: #a16207; }

.empty-state { text-align: center; padding: 5rem 2rem; color: var(--cnd-muted); }
.empty-state i { font-size: 4rem; opacity: 0.3; margin-bottom: 1.5rem; display: block; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="provider-bookings-hero">
  <div class="container-fluid px-3 px-lg-5">
    <div class="row align-items-center">
      <div class="col-md-8">
        <h1 class="fw-900 mb-1">Student Bookings</h1>
        <p class="opacity-90">View and manage all enrollments for your classes.</p>
      </div>
      <div class="col-md-4 text-md-end">
        <div class="bg-white bg-opacity-20 rounded-pill px-4 py-2 d-inline-block">
          <span class="fw-bold"><?= count($bookings) ?> Total Bookings</span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid px-3 px-lg-5 py-5">

  <?php if (empty($bookings)): ?>
    <div class="empty-state">
      <i class="bi bi-person-badge"></i>
      <h3>No students have booked yet</h3>
      <p>Once students book your classes, they will appear here.</p>
      <a href="<?= base_url('provider/listings') ?>" class="btn cnd-btn-primary mt-3">Manage My Listings</a>
    </div>
  <?php else: ?>
    
    <div class="row">
      <div class="col-xl-9">
        <?php foreach ($bookings as $bk): ?>
          <div class="pb-card">
            <div class="pb-card-header">
              <div>
                <h3 class="pb-student-name"><?= esc($bk->student_name) ?></h3>
                <div class="pb-listing-title">
                  <i class="bi bi-bookmark-fill me-1"></i>
                  <?= esc($bk->listing_title) ?>
                  <span class="mx-2 text-opacity-25 opacity-25">|</span>
                  <span class="text-uppercase" style="font-size: 0.7rem;letter-spacing: 0.05em;"><?= esc($bk->listing_type) ?></span>
                  <?php if (!empty($bk->batch_name)): ?>
                    <span class="mx-2 text-opacity-25 opacity-25">|</span>
                    <span class="badge bg-soft-pink text-pink fw-bold" style="font-size: 0.65rem;"><?= esc($bk->batch_name) ?></span>
                  <?php endif; ?>
                </div>
              </div>
              <div class="text-end">
                <span class="pb-status-badge <?= ($bk->payment_status === 'paid' || $bk->payment_status === 'free' || $bk->payment_status === 'confirmed') ? 'status-paid' : 'status-pending' ?>">
                  <?= ($bk->payment_status === 'paid' || $bk->payment_status === 'free' || $bk->payment_status === 'confirmed') ? '✓ CONFIRMED' : '⏳ PENDING' ?>
                </span>
                <div class="mt-2 fw-800 text-dark">
                  <?php if ((float)$bk->payment_amount > 0): ?>
                    ₹<?= number_format($bk->payment_amount) ?>
                  <?php else: ?>
                    <span class="text-success">Free</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
            <div class="pb-card-body">
              <div class="pb-meta-grid">
                
                <div class="pb-meta-item">
                  <i class="bi bi-calendar-event"></i>
                  <div>
                    <span class="pb-meta-label">Schedule</span>
                    <?php if ($bk->class_date): ?>
                      <?= date('D, d M Y', strtotime($bk->class_date)) ?>
                      <?php if($bk->class_time): ?> at <?= date('g:i A', strtotime($bk->class_time)) ?><?php endif; ?>
                    <?php else: ?>
                      Check listing schedule
                    <?php endif; ?>
                  </div>
                </div>

                <div class="pb-meta-item">
                  <i class="bi bi-telephone"></i>
                  <div>
                    <span class="pb-meta-label">Contact</span>
                    <?= esc($bk->parent_phone) ?>
                    <?php if($bk->parent_email): ?>
                      <div class="small text-muted"><?= esc($bk->parent_email) ?></div>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="pb-meta-item">
                  <i class="bi bi-info-circle"></i>
                  <div>
                    <span class="pb-meta-label">Student Info</span>
                    Age: <?= (int)$bk->student_age ?: 'N/A' ?>
                  </div>
                </div>

                <div class="pb-meta-item">
                  <i class="bi bi-hash"></i>
                  <div>
                    <span class="pb-meta-label">Booking ID</span>
                    #<?= str_pad($bk->id, 6, '0', STR_PAD_LEFT) ?>
                    <div class="small text-muted"><?= date('d M, Y', strtotime($bk->created_at)) ?></div>
                  </div>
                </div>

              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 sticky-top" style="top: 100px;">
          <h4 class="fw-900 mb-3" style="font-size: 1.1rem;">Quick Filters</h4>
          <p class="text-muted small">Showing all bookings for your active listings.</p>
          <hr class="my-3 opacity-50">
          <a href="<?= base_url('provider/listings') ?>" class="btn btn-outline-primary w-100 rounded-pill fw-bold py-2 mb-2" style="font-size: 0.85rem;">
            <i class="bi bi-grid-fill me-2"></i>Manage Listings
          </a>
          <a href="<?= base_url('provider/verification') ?>" class="btn btn-outline-secondary w-100 rounded-pill fw-bold py-2" style="font-size: 0.85rem;">
            <i class="bi bi-shield-lock me-2"></i>KYC Status
          </a>
        </div>
      </div>
    </div>

  <?php endif; ?>

</div>

<?= $this->endSection() ?>
