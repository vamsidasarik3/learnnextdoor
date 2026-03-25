<?= $this->extend('frontend/layout/base') ?>
<?= $this->section('css') ?>
<style>
.cnd-bookings-hero {
  background: var(--cnd-gradient);
  padding: 3rem 1.5rem 2rem;
  color: #fff;
}
.cnd-bookings-hero h1 { font-size: clamp(1.5rem, 4vw, 2.2rem); font-weight: 900; margin: 0; }
.cnd-bookings-hero p  { opacity: .85; margin: .5rem 0 0; font-size: .95rem; }
.cnd-booking-card {
  background: #fff;
  border-radius: 16px;
  border: 1.5px solid var(--cnd-card-border);
  box-shadow: var(--cnd-card-shadow);
  overflow: hidden;
  margin-bottom: 1.2rem;
  transition: box-shadow .2s, transform .2s;
}
.cnd-booking-card:hover { box-shadow: 0 8px 30px rgba(108,99,255,.12); transform: translateY(-2px); }
.cnd-booking-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem 1.3rem;
  border-bottom: 1px solid var(--cnd-card-border);
  flex-wrap: wrap;
  gap: .5rem;
}
.cnd-booking-title { font-weight: 800; font-size: .97rem; color: var(--cnd-dark); margin: 0; }
.cnd-booking-title a { color: inherit; text-decoration: none; }
.cnd-booking-title a:hover { color: var(--cnd-pink); }
.cnd-booking-badge {
  font-size: .7rem; font-weight: 700; padding: .25rem .7rem;
  border-radius: var(--cnd-radius-pill); text-transform: uppercase; letter-spacing: .04em;
}
.cnd-booking-badge.confirmed  { background: #dcfce7; color: #16a34a; }
.cnd-booking-badge.pending    { background: #fef9c3; color: #a16207; }
.cnd-booking-badge.cancelled  { background: #fee2e2; color: #dc2626; }
.cnd-booking-card-body { padding: 1rem 1.3rem; display: flex; flex-wrap: wrap; gap: 1rem; }
.cnd-booking-meta { font-size: .82rem; color: var(--cnd-muted); display: flex; align-items: center; gap: .4rem; }
.cnd-booking-meta i { color: var(--cnd-pink); font-size: .95rem; }
.cnd-booking-amount { font-size: 1.1rem; font-weight: 800; color: var(--cnd-dark); }
.cnd-booking-amount small { font-size: .72rem; font-weight: 500; color: var(--cnd-muted); }
.cnd-empty-bookings { text-align: center; padding: 5rem 2rem; }
.cnd-empty-bookings i { font-size: 3.5rem; color: var(--cnd-pink); opacity: .4; }
.cnd-empty-bookings h3 { font-size: 1.2rem; font-weight: 800; margin: 1rem 0 .5rem; color: var(--cnd-dark); }
.cnd-empty-bookings p  { color: var(--cnd-muted); margin-bottom: 1.5rem; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero -->
<div class="cnd-bookings-hero" style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); padding: 4rem 0;">
  <div class="container-fluid px-3 px-lg-5">
    <div class="row align-items-center">
      <div class="col-md-7">
        <h1 class="display-5 fw-900 mb-2">Parent Dashboard</h1>
        <p class="lead opacity-90">Manage your children's learning journey and track all your class bookings.</p>
      </div>
      <div class="col-md-5 text-md-end mt-4 mt-md-0">
        <a href="<?= base_url('classes') ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold shadow-sm">
          <i class="bi bi-search me-2"></i>Find New Classes
        </a>
      </div>
    </div>
  </div>
</div>

<div class="container-fluid px-3 px-lg-5 py-4" style="margin-top: -3rem;">
  
  <!-- Quick Stats -->
  <div class="row g-4 mb-5">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-bottom: 4px solid #6366f1 !important;">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-primary bg-opacity-10 text-primary rounded-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-calendar-check fs-4"></i>
          </div>
          <div>
            <h3 class="fw-900 mb-0"><?= count($bookings) ?></h3>
            <p class="text-muted small mb-0">Total Bookings</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <?php 
        $upcoming = array_filter($bookings, fn($b) => !empty($b['class_date']) && strtotime($b['class_date']) >= time());
      ?>
      <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-bottom: 4px solid #10b981 !important;">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-success bg-opacity-10 text-success rounded-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-hourglass-split fs-4"></i>
          </div>
          <div>
            <h3 class="fw-900 mb-0"><?= count($upcoming) ?></h3>
            <p class="text-muted small mb-0">Upcoming Classes</p>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <?php 
        $totalSpent = array_reduce($bookings, fn($sum, $b) => $sum + (float)($b['payment_amount'] ?? 0), 0);
      ?>
      <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="border-bottom: 4px solid #f59e0b !important;">
        <div class="d-flex align-items-center gap-3">
          <div class="bg-warning bg-opacity-10 text-warning rounded-4 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
            <i class="bi bi-wallet2 fs-4"></i>
          </div>
          <div>
            <h3 class="fw-900 mb-0">₹<?= number_format($totalSpent) ?></h3>
            <p class="text-muted small mb-0">Total Invested</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
  <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
    <i class="bi bi-check-circle-fill me-2"></i><?= esc(session()->getFlashdata('success')) ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <?php if (empty($bookings)): ?>
    <div class="cnd-empty-bookings">
      <i class="bi bi-calendar-x" aria-hidden="true"></i>
      <h3>No bookings yet</h3>
      <p>You haven't booked any classes yet. Start exploring!</p>
      <a href="<?= base_url('classes') ?>" class="btn cnd-btn-primary px-4 py-2">
        <i class="bi bi-search me-2"></i>Browse Classes
      </a>
    </div>
  <?php else: ?>
    <div class="row">
      <div class="col-lg-8 col-xl-7">
        <p class="text-muted mb-3" style="font-size:.85rem;">
          Showing <strong><?= count($bookings) ?></strong> booking<?= count($bookings) !== 1 ? 's' : '' ?>
        </p>
        <?php foreach ($bookings as $bk): ?>
        <?php
          $payStatus = $bk['payment_status'] ?? 'pending';
          $statusClass = 'pending';
          switch($payStatus) {
            case 'paid':
            case 'free':
            case 'confirmed':
                $statusClass = 'confirmed';
                break;
            case 'cancelled':
                $statusClass = 'cancelled';
                break;
          }
          $statusLabel = '⏳ Pending';
          switch($payStatus) {
            case 'paid':
                $statusLabel = '✓ Confirmed';
                break;
            case 'free':
                $statusLabel = '✓ Confirmed (Free)';
                break;
            case 'confirmed':
                $statusLabel = '✓ Confirmed';
                break;
            case 'cancelled':
                $statusLabel = '✗ Cancelled';
                break;
          }
        ?>
        <div class="cnd-booking-card" itemscope itemtype="https://schema.org/BookingAction">
          <div class="cnd-booking-card-header">
            <p class="cnd-booking-title">
              <?php if (!empty($bk['listing_id'])): ?>
              <a href="<?= base_url('classes/' . (int)$bk['listing_id']) ?>">
                <?= esc($bk['listing_title'] ?? 'Class') ?>
              </a>
              <?php else: ?>
              <?= esc($bk['listing_title'] ?? 'Class') ?>
              <?php endif; ?>
            </p>
            <span class="cnd-booking-badge <?= $statusClass ?>">
              <?= $statusLabel ?>
            </span>
          </div>
          <div class="cnd-booking-card-body">
            <?php if (!empty($bk['student_name'])): ?>
            <span class="cnd-booking-meta">
              <i class="bi bi-person-fill"></i> <?= esc($bk['student_name']) ?>
              <?php if (!empty($bk['student_age'])): ?>
              <span class="text-muted">(Age <?= (int)$bk['student_age'] ?>)</span>
              <?php endif; ?>
            </span>
            <?php endif; ?>
            <?php
              $bkType  = $bk['listing_type'] ?? 'regular';
              $d1 = !empty($bk['class_date']) ? date('d M Y', strtotime($bk['class_date'])) : (!empty($bk['listing_start_date']) ? date('d M Y', strtotime($bk['listing_start_date'])) : null);
              $t1 = !empty($bk['class_time']) ? date('g:i A', strtotime($bk['class_time'])) : (!empty($bk['listing_class_time']) ? date('g:i A', strtotime($bk['listing_class_time'])) : null);
              $d2 = !empty($bk['listing_end_date']) ? date('d M Y', strtotime($bk['listing_end_date'])) : null;
            ?>
            <span class="cnd-booking-meta">
              <i class="bi bi-calendar3"></i>
              <?php if ($bkType === 'course' && $d2): ?>
                <?= $d1 ?> to <?= $d2 ?>
              <?php else: ?>
                <?= $d1 ?: 'TBD' ?>
              <?php endif; ?>
              
              <?php if ($t1): ?>
                &nbsp; <i class="bi bi-clock"></i> <?= $t1 ?>
              <?php endif; ?>
            </span>
            <?php if (!empty($bk['listing_address'])): ?>
            <span class="cnd-booking-meta">
              <i class="bi bi-geo-alt-fill"></i>
              <?= esc(character_limiter($bk['listing_address'], 50)) ?>
            </span>
            <?php endif; ?>
            <?php if (!empty($bk['booking_type'])): ?>
            <span class="cnd-booking-meta">
              <i class="bi bi-tag-fill"></i>
              <?= esc(ucfirst($bk['booking_type'])) ?> booking
            </span>
            <?php endif; ?>
            <!-- Amount -->
            <div class="ms-auto">
              <?php $amt = (float)($bk['payment_amount'] ?? 0); ?>
              <div class="cnd-booking-amount">
                <?php if ($amt > 0): ?>
                <i class="bi bi-currency-rupee" aria-hidden="true" style="font-size:.85em;"></i><?= number_format($amt) ?>
                <small>paid</small>
                <?php else: ?>
                <span style="color: #16a34a; font-size:.9rem;">Free</span>
                <?php endif; ?>
              </div>
              <?php if (!empty($bk['created_at'])): ?>
              <div style="font-size:.72rem; color:var(--cnd-muted); margin-top:.2rem;">
                Booked <?= date('d M Y', strtotime($bk['created_at'])) ?>
              </div>
              <?php endif; ?>
              
              <!-- Review & Certificate -->
              <div class="mt-2 text-end">
                <?php if ($bkType !== 'regular'): ?>
                  <?php if (!($bk['has_reviewed'] ?? false)): ?>
                    <button class="btn btn-sm btn-outline-pink rounded-pill px-3 fw-bold" 
                            onclick="openReviewModal(<?= (int)$bk['listing_id'] ?>, '<?= esc($bk['listing_title']) ?>')">
                      Post Review
                    </button>
                  <?php else: ?>
                    <div class="d-flex flex-column gap-1 align-items-end">
                      <span class="badge bg-success bg-opacity-10 text-success rounded-pill mb-1">Reviewed</span>
                      <a href="<?= base_url('booking/certificate/' . (int)$bk['id']) ?>" class="btn btn-sm btn-pink rounded-pill px-3 fw-bold">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Certificate
                      </a>
                    </div>
                  <?php endif; ?>
                <?php endif; ?>
              </div>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Sidebar -->
      <div class="col-lg-4 col-xl-3 mt-4 mt-lg-0">
        <div class="cnd-sidebar-card">
          <p class="fw-700 mb-3" style="font-size:.9rem;">Quick Links</p>
          <a href="<?= base_url('classes') ?>" class="btn cnd-btn-primary w-100 mb-2">
            <i class="bi bi-search me-2"></i>Browse More Classes
          </a>
          <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger w-100" style="border-radius:var(--cnd-radius-pill);font-weight:700;font-size:.88rem;">
            <i class="bi bi-box-arrow-right me-2"></i>Logout
          </a>
        </div>
      </div>
    </div>
  <?php endif; ?>
</div>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-4 border-0 shadow-lg">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="reviewClassTitle">Post Review</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="reviewForm">
        <div class="modal-body">
          <input type="hidden" name="listing_id" id="reviewListingId">
          <div class="mb-3 text-center">
            <label class="form-label d-block small fw-bold text-uppercase letter-spacing-sm">Your Rating</label>
            <div class="fs-2 text-warning cursor-pointer" id="starInput">
               <i class="bi bi-star" data-value="1"></i>
               <i class="bi bi-star" data-value="2"></i>
               <i class="bi bi-star" data-value="3"></i>
               <i class="bi bi-star" data-value="4"></i>
               <i class="bi bi-star" data-value="5"></i>
            </div>
            <input type="hidden" name="rating" id="reviewRating" value="0">
          </div>
          <div class="mb-3">
            <label class="form-label small fw-bold text-uppercase letter-spacing-sm">Your Experience</label>
            <textarea name="review_text" class="form-control rounded-3 border-2" rows="4" placeholder="How was the class?"></textarea>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-pink rounded-pill px-4 fw-bold">Post Review</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
function openReviewModal(listingId, title) {
  document.getElementById('reviewListingId').value = listingId;
  document.getElementById('reviewClassTitle').textContent = 'Review: ' + title;
  
  var modal = new bootstrap.Modal(document.getElementById('reviewModal'));
  modal.show();
}

(function(){
  var starInput = document.getElementById('starInput');
  if (starInput) {
    var stars = starInput.querySelectorAll('i');
    stars.forEach(function(star){
      star.addEventListener('click', function(){
        var val = parseInt(this.dataset.value);
        document.getElementById('reviewRating').value = val;
        stars.forEach(function(s, i){
          if (i < val) {
            s.classList.replace('bi-star', 'bi-star-fill');
            s.style.color = '#ffc107';
          } else {
            s.classList.replace('bi-star-fill', 'bi-star');
            s.style.color = '';
          }
        });
      });
      star.addEventListener('mouseover', function(){
        var val = parseInt(this.dataset.value);
        stars.forEach(function(s, i){
           if(i < val) s.style.color = '#ffc107';
        });
      });
      star.addEventListener('mouseout', function(){
        var currentVal = parseInt(document.getElementById('reviewRating').value || 0);
        stars.forEach(function(s, i){
           if(i >= currentVal) s.style.color = '';
        });
      });
    });
  }

  document.getElementById('reviewForm').addEventListener('submit', function(e){
    e.preventDefault();
    var rating = document.getElementById('reviewRating').value;
    if (rating == 0) { alert('Please select a rating'); return; }

    var btn = this.querySelector('button[type="submit"]');
    var originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Posting...';

    var fd = new FormData(this);
    var body = {};
    fd.forEach((value, key) => body[key] = value);

    // Get CSRF from global if exists, or assume standard CI AJAX handling
    fetch('<?= base_url('submit-review') ?>', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify(body)
    })
    .then(r => r.json())
    .then(res => {
      if (res.success) {
        window.location.reload();
      } else {
        alert(res.message);
        btn.disabled = false;
        btn.textContent = originalText;
      }
    })
    .catch(() => {
        alert('Network error');
        btn.disabled = false;
        btn.textContent = originalText;
    });
  });
})();
</script>
<?= $this->endSection() ?>
