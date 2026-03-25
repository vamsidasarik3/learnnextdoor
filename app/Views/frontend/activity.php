<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('content') ?>

<!-- ══ HERO SECTION ══════════════════════════════════════════ -->
<section class="cnd-hero-mini bg-light py-5">
  <div class="container text-center py-4">
    <h1 class="display-5 fw-bold mb-2">My <span class="text-pink">Classes</span></h1>
    <p class="text-muted lead">Keep track of your learning journey & certificates.</p>
  </div>
</section>

<!-- ══ ACTIVITY TABS ══════════════════════════════════════════ -->
<section class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10">
        
        <!-- Tab Nav -->
        <ul class="nav nav-pills cnd-nav-pills mb-5 justify-content-center" id="activityTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link <?= $active_tab == 'upcoming' ? 'active' : '' ?>" id="upcoming-tab" data-bs-toggle="pill" data-bs-target="#upcoming" type="button" role="tab">
               <i class="bi bi-calendar-event me-2"></i>Upcoming (<?= count($upcoming) ?>)
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link <?= $active_tab == 'history' ? 'active' : '' ?>" id="completed-tab" data-bs-toggle="pill" data-bs-target="#completed" type="button" role="tab">
               <i class="bi bi-award me-2"></i>History & Certificates (<?= count($completed) ?>)
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link <?= $active_tab == 'payments' ? 'active' : '' ?>" id="payments-tab" data-bs-toggle="pill" data-bs-target="#payments" type="button" role="tab">
               <i class="bi bi-credit-card me-2"></i>Payments (<?= count($payments) ?>)
            </button>
          </li>
        </ul>

        <div class="tab-content" id="activityTabContent">
          
          <!-- ── Upcoming Tab ── -->
          <div class="tab-pane fade <?= $active_tab == 'upcoming' ? 'show active' : '' ?>" id="upcoming" role="tabpanel">
            <?php if (empty($upcoming)): ?>
              <div class="text-center py-5">
                <div class="mb-3 text-muted" style="font-size: 3.5rem;"><i class="bi bi-calendar2-x"></i></div>
                <h5 class="fw-bold">No upcoming classes found.</h5>
                <p class="text-muted mb-4">Ready to learn something new today?</p>
                <a href="<?= base_url('classes') ?>" class="btn btn-pink rounded-pill px-5 py-2 fw-bold">Browse Classes</a>
              </div>
            <?php else: ?>
              <div class="row g-4">
                <?php foreach ($upcoming as $b): ?>
                  <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-hover">
                      <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                          <span class="badge bg-soft-pink text-pink rounded-pill px-3 py-2 small">Confirmed</span>
                          <span class="text-muted small">Ref: #<?= str_pad($b->id, 6, '0', STR_PAD_LEFT) ?></span>
                        </div>
                        <h5 class="fw-bold mb-2"><?= esc($b->listing_title) ?></h5>
                        <p class="text-muted small mb-3">Attendee: <span class="text-dark fw-600"><?= esc($b->student_name) ?></span></p>
                        
                        <?php
                          $bkType = $b->listing_type ?? 'regular';
                          $d1 = !empty($b->class_date) ? date('d M Y', strtotime($b->class_date)) : (!empty($b->listing_start_date) ? date('d M Y', strtotime($b->listing_start_date)) : null);
                          $t1 = !empty($b->class_time) ? date('g:i A', strtotime($b->class_time)) : (!empty($b->listing_class_time) ? date('g:i A', strtotime($b->listing_class_time)) : null);
                          $d2 = !empty($b->listing_end_date) ? date('d M Y', strtotime($b->listing_end_date)) : null;
                        ?>
                        <div class="d-flex flex-wrap gap-2 mb-4">
                          <div class="bg-light rounded-3 px-3 py-2 small border">
                            <i class="bi bi-calendar3 me-2 text-pink"></i>
                            <?php if ($bkType === 'course' && $d2): ?>
                               <?= $d1 ?> to <?= $d2 ?>
                            <?php else: ?>
                               <?= $d1 ?: 'TBD' ?>
                            <?php endif; ?>
                          </div>
                          <?php if ($t1): ?>
                          <div class="bg-light rounded-3 px-3 py-2 small border">
                            <i class="bi bi-clock me-2 text-pink"></i>
                            <?= $t1 ?>
                          </div>
                          <?php endif; ?>
                        </div>

                        <?php if ($b->listing_address): ?>
                          <div class="d-flex text-muted small">
                            <i class="bi bi-geo-alt-fill me-2 text-pink mt-1"></i>
                            <span><?= esc($b->listing_address) ?></span>
                          </div>
                        <?php endif; ?>
                      </div>
                      <div class="card-footer bg-white border-0 p-4 pt-0 d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div class="fs-5 fw-bold text-dark">₹<?= number_format($b->payment_amount, 2) ?></div>
                        <div class="d-flex gap-2">
                          <button class="btn btn-outline-pink btn-sm rounded-pill px-3 btn-invoice-download" 
                            data-bid="<?= $b->id ?>" 
                            data-title="<?= esc($b->listing_title) ?>" 
                            data-student="<?= esc($b->student_name) ?>" 
                            data-amount="<?= $b->payment_amount ?>" 
                            data-payid="<?= esc($b->payment_id) ?>" 
                            data-date="<?= date('d M Y', strtotime($b->created_at)) ?>"
                            data-class-date="<?= $d1 ?>"
                            data-class-time="<?= $t1 ?>">
                            <i class="bi bi-file-earmark-pdf"></i> Invoice
                          </button>
                          <a href="<?= base_url('classes/'.$b->listing_id) ?>" class="btn btn-outline-dark btn-sm rounded-pill px-3">View Info</a>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

          <!-- ── Completed Tab ── -->
          <div class="tab-pane fade <?= $active_tab == 'history' ? 'show active' : '' ?>" id="completed" role="tabpanel">
             <?php if (empty($completed)): ?>
              <div class="text-center py-5">
                <div class="mb-3 text-muted" style="font-size: 3.5rem;"><i class="bi bi-award"></i></div>
                <h5 class="fw-bold">No completed classes yet.</h5>
                <p class="text-muted">Certificates will appear here once the provider marks the class as completed.</p>
              </div>
            <?php else: ?>
              <div class="row g-4">
                <?php foreach ($completed as $b): ?>
                  <div class="col-md-6">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden card-hover">
                      <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                          <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 small">Completed</span>
                          <span class="text-muted small">#<?= str_pad($b->id, 6, '0', STR_PAD_LEFT) ?></span>
                        </div>
                        <h5 class="fw-bold mb-2"><?= esc($b->listing_title) ?></h5>
                        <p class="text-muted small mb-3">Student: <span class="text-dark fw-600"><?= esc($b->student_name) ?></span></p>
                        
                        <div class="p-3 bg-light rounded-4 mb-4 border border-white shadow-inner">
                           <div class="d-flex align-items-center">
                             <div class="bg-white rounded-circle p-2 me-3 text-pink shadow-sm">
                               <i class="bi bi-patch-check-fill fs-5"></i>
                             </div>
                             <div>
                               <div class="text-dark fw-bold small">Achievement Unlocked!</div>
                               <div class="text-muted small">Completed on <?= date('d M, Y', strtotime($b->completed_at)) ?></div>
                             </div>
                           </div>
                        </div>

                         <div class="d-grid gap-2">
                             <button class="btn btn-pink rounded-pill py-2 btn-cert-download shadow-sm" 
                                     data-bid="<?= $b->id ?>"
                                     data-student="<?= esc($b->student_name) ?>"
                                     data-class="<?= esc($b->listing_title) ?>"
                                     data-date="<?= date('d F Y', strtotime($b->completed_at)) ?>"
                                     data-category="<?= esc($b->category_name ?? 'Class') ?>">
                               <i class="bi bi-award-fill me-2"></i>Download Certificate
                             </button>

                           <button class="btn btn-light rounded-pill py-2 btn-invoice-download border" 
                             data-bid="<?= $b->id ?>" 
                             data-title="<?= esc($b->listing_title) ?>" 
                             data-student="<?= esc($b->student_name) ?>" 
                             data-amount="<?= $b->payment_amount ?>" 
                             data-payid="<?= esc($b->payment_id) ?>" 
                             data-date="<?= date('d M Y', strtotime($b->created_at)) ?>"
                             data-class-date="<?= date('d M Y', strtotime($b->class_date ?: ($b->listing_start_date ?? ''))) ?>"
                             data-class-time="<?= !empty($b->class_time) ? date('g:i A', strtotime($b->class_time)) : (!empty($b->listing_class_time) ? date('g:i A', strtotime($b->listing_class_time)) : '') ?>">
                             <i class="bi bi-file-earmark-pdf me-2 text-pink"></i>Download Invoice
                           </button>
                           
                           <?php if (!$b->has_reviewed): ?>
                              <button class="btn btn-outline-dark rounded-pill py-2 btn-rate-class" 
                                      data-bid="<?= $b->id ?>" 
                                      data-lid="<?= $b->listing_id ?>"
                                      data-title="<?= esc($b->listing_title) ?>">
                                <i class="bi bi-star-fill me-2 text-warning"></i>Share Feedback
                              </button>
                           <?php else: ?>
                              <div class="text-center py-2">
                                <span class="badge bg-light text-muted fw-normal rounded-pill px-3 py-2">Review Submitted <i class="bi bi-check2 ms-1"></i></span>
                              </div>
                           <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
          
          <!-- ── Payments Tab ── -->
          <div class="tab-pane fade <?= $active_tab == 'payments' ? 'show active' : '' ?>" id="payments" role="tabpanel">
             <?php if (empty($payments)): ?>
              <div class="text-center py-5">
                <div class="mb-3 text-muted" style="font-size: 3.5rem;"><i class="bi bi-credit-card"></i></div>
                <h5 class="fw-bold">No payment history found.</h5>
                <p class="text-muted">Once you book a class, your transaction details will appear here.</p>
              </div>
            <?php else: ?>
              <div class="table-responsive bg-white rounded-4 shadow-sm border p-3">
                 <table class="table align-middle">
                    <thead class="bg-light">
                       <tr>
                          <th class="border-0 small text-uppercase">Txn ID</th>
                          <th class="border-0 small text-uppercase">Date</th>
                          <th class="border-0 small text-uppercase">Reference</th>
                          <th class="border-0 small text-uppercase">Amount</th>
                          <th class="border-0 small text-uppercase">Status</th>
                          <th class="border-0 small text-uppercase text-end">Invoice</th>
                       </tr>
                    </thead>
                    <tbody>
                       <?php foreach($payments as $tx): ?>
                       <tr>
                          <td class="small fw-bold text-pink">#<?= esc(strtoupper(substr($tx->razorpay_id ?? '', -8))) ?></td>
                          <td class="small text-muted"><?= date('d M Y', strtotime($tx->created_at)) ?></td>
                          <td class="small">Booking #<?= str_pad($tx->booking_id, 6, '0', STR_PAD_LEFT) ?></td>
                          <td class="fw-bold">₹<?= number_format($tx->amount, 2) ?></td>
                          <td><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2 py-1 small" style="font-size: 0.65rem;">SUCCESS</span></td>
                          <td class="text-end">
                             <button class="btn btn-sm btn-outline-pink rounded-pill btn-invoice-download" 
                               data-bid="<?= $tx->booking_id ?>" 
                               data-title="Class Booking" 
                               data-student="User" 
                               data-amount="<?= $tx->amount ?>" 
                               data-payid="<?= esc($tx->razorpay_id ?? '') ?>" 
                               data-date="<?= date('d M Y', strtotime($tx->created_at)) ?>">
                                <i class="bi bi-download"></i>
                             </button>
                          </td>
                       </tr>
                       <?php endforeach; ?>
                    </tbody>
                 </table>
              </div>
            <?php endif; ?>
          </div>

        </div>
      </div>
    </div>
  </div>

  <?php if (logged('role') == 3): ?>
  <!-- ── JOIN PROVIDER CTA ── -->
  <div class="row justify-content-center mt-5">
    <div class="col-lg-8">
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: linear-gradient(135deg, #3F3590 0%, #3B3486 100%);">
        <div class="card-body p-4 p-lg-5 text-white">
          <div class="row align-items-center">
            <div class="col-lg-8">
              <h3 class="fw-900 mb-2">Monetize Your Skills!</h3>
              <p class="opacity-90 mb-0">Join our growing community of teachers and coaches. List your own classes, workshops, or courses and reach thousands of parents.</p>
            </div>
            <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
              <a href="<?= base_url('provider/verification') ?>" class="btn btn-light rounded-pill px-4 py-3 fw-bold text-pink">
                <i class="bi bi-rocket-takeoff-fill me-2"></i>Join as a Provider
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

</section>

<!-- ══ REVIEW MODAL ══════════════════════════════════════════ -->
<div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
      <div class="modal-header bg-light border-0 py-3">
        <h5 class="modal-title fw-bold">How was your experience?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 text-center">
        <h6 id="rClassTitle" class="fw-bold mb-3 text-pink"></h6>
        <p class="text-muted small mb-4">Your feedback helps other parents choose the best classes for their children.</p>

        <!-- Star Rating -->
        <div class="mb-4">
           <div class="d-flex justify-content-center gap-2 mb-2" id="starContainer">
             <i class="bi bi-star fs-1 star-btn cursor-pointer text-muted" data-value="1"></i>
             <i class="bi bi-star fs-1 star-btn cursor-pointer text-muted" data-value="2"></i>
             <i class="bi bi-star fs-1 star-btn cursor-pointer text-muted" data-value="3"></i>
             <i class="bi bi-star fs-1 star-btn cursor-pointer text-muted" data-value="4"></i>
             <i class="bi bi-star fs-1 star-btn cursor-pointer text-muted" data-value="5"></i>
           </div>
           <div id="ratingLabel" class="fw-bold text-pink small">SELECT A RATING</div>
        </div>

        <div class="mb-4">
           <textarea id="reviewText" class="form-control rounded-3 border-2" rows="3" placeholder="Tell us more about the class (optional)"></textarea>
        </div>

        <div class="d-grid gap-2">
           <button id="submitReviewBtn" class="btn btn-pink py-3 rounded-pill fw-bold shadow-sm" disabled>
              <span id="reviewSpinner" class="spinner-border spinner-border-sm d-none me-2"></span>
              Submit Review
           </button>
           <button type="button" class="btn btn-link text-muted btn-sm text-decoration-none" data-bs-dismiss="modal">Remind me later</button>
        </div>
      </div>
    </div>
  </div>
</div>


<style>
:root { --cnd-pink: #FF68B4; --cnd-pink-dark: #FF1493; --cnd-muted: #6c757d; }
.text-pink { color: var(--cnd-pink); }
.btn-pink { background: var(--cnd-pink); color: #fff; border: none; }
.btn-pink:hover { background: var(--cnd-pink-dark); color: #fff; transform: translateY(-1px); }
.btn-outline-pink { border: 2px solid var(--cnd-pink); color: var(--cnd-pink); font-weight: 600; }
.btn-outline-pink:hover { background: var(--cnd-pink); color: #fff; }
.cnd-nav-pills .nav-link { border: 2px solid #f0f0f0; background: #fff; color: var(--cnd-muted); font-weight: 600; padding: 12px 25px; border-radius: 50px; margin: 0 8px; transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.cnd-nav-pills .nav-link.active { background: var(--cnd-pink); color: #fff; border-color: var(--cnd-pink); transform: scale(1.05); box-shadow: 0 10px 20px rgba(255, 104, 180, 0.2); }
.card-hover { transition: all 0.3s ease; border: 1px solid #f0f0f0 !important; }
.card-hover:hover { transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important; border-color: var(--cnd-pink) !important; }
.bg-soft-pink { background: rgba(255, 104, 180, 0.08); }
.shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.02); }
.letter-spacing-sm { letter-spacing: 0.1rem; }
.fw-600 { font-weight: 600; }
.cursor-pointer { cursor: pointer; }
.star-btn { transition: transform 0.2s ease, color 0.1s ease; }
.star-btn:hover { transform: scale(1.2); }
.star-btn.active { color: #ffc107 !important; }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- Required for client-side PDF generation -->
<script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>
<script src="https://unpkg.com/@pdf-lib/fontkit@1.1.1/dist/fontkit.umd.min.js"></script>

<script>
(function(){
  'use strict';


  function toggleLoading(btnId, spinId, show){
    var btn  = document.getElementById(btnId);
    var spin = document.getElementById(spinId);
    if(show){ btn.disabled=true; spin.classList.remove('d-none'); }
    else { btn.disabled=false; spin.classList.add('d-none'); }
  }

  // ── Review Flow ───────────────────────────────────
  
  var reviewModal = new bootstrap.Modal(document.getElementById('reviewModal'));
  var selectedRating = 0;
  var currentLid = 0;

  document.querySelectorAll('.btn-rate-class').forEach(function(btn){
    btn.addEventListener('click', function(){
      currentBid = this.dataset.bid;
      currentLid = this.dataset.lid;
      document.getElementById('rClassTitle').textContent = this.dataset.title;
      resetReview();
      reviewModal.show();
    });
  });

  const stars = document.querySelectorAll('.star-btn');
  stars.forEach(s => {
    s.addEventListener('click', function(){
      selectedRating = parseInt(this.dataset.value);
      updateStars(selectedRating);
      document.getElementById('submitReviewBtn').disabled = false;
      
      const labels = ['', 'QUITE POOR', 'COULD BE BETTER', 'GOOD EXPERIENCE', 'REALLY GREAT', 'EXCELLENT!'];
      document.getElementById('ratingLabel').textContent = labels[selectedRating];
    });
  });

  function updateStars(rating) {
    stars.forEach(s => {
      const val = parseInt(s.dataset.value);
      if(val <= rating) {
        s.classList.remove('bi-star', 'text-muted');
        s.classList.add('bi-star-fill', 'active');
      } else {
        s.classList.remove('bi-star-fill', 'active');
        s.classList.add('bi-star', 'text-muted');
      }
    });
  }

  function resetReview() {
    selectedRating = 0;
    updateStars(0);
    document.getElementById('reviewText').value = '';
    document.getElementById('submitReviewBtn').disabled = true;
    document.getElementById('ratingLabel').textContent = 'SELECT A RATING';
  }

  document.getElementById('submitReviewBtn').addEventListener('click', function(){
    if(selectedRating === 0) return;
    
    toggleLoading('submitReviewBtn', 'reviewSpinner', true);

    fetch('<?= base_url('api/reviews/submit') ?>', {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify({
        listing_id: currentLid,
        booking_id: currentBid,
        rating: selectedRating,
        review_text: document.getElementById('reviewText').value.trim()
      })
    })
    .then(r => r.json())
    .then(res => {
      toggleLoading('submitReviewBtn', 'reviewSpinner', false);
      if(res.success){
        reviewModal.hide();
        // Show success and reload
        alert('Thank you for your review!');
        location.reload();
      } else { alert(res.message || 'Error submitting review.'); }
    })
    .catch(() => toggleLoading('submitReviewBtn', 'reviewSpinner', false));
  });

  // ── PDF Certificate Generation ─────────────────────

  document.querySelectorAll('.btn-cert-download').forEach(function(btn){
    btn.addEventListener('click', function(){
      var data = this.dataset;
      generateCertificate(data);
    });
  });

  // ── PDF Certificate Generation ─────────────────────
  // (existing code...)

  // ── PDF Invoice Generation ─────────────────────────

  document.querySelectorAll('.btn-invoice-download').forEach(function(btn){
    btn.addEventListener('click', function(){
      var data = this.dataset;
      generateInvoice(data);
    });
  });

  async function generateInvoice(data) {
    try {
      const { PDFDocument, rgb, StandardFonts } = PDFLib;
      const pdfDoc = await PDFDocument.create();
      const page   = pdfDoc.addPage([595.28, 841.89]); // A4 Portrait
      const { width, height } = page.getSize();

      const fontBold = await pdfDoc.embedFont(StandardFonts.HelveticaBold);
      const fontReg  = await pdfDoc.embedFont(StandardFonts.Helvetica);

      // ── Header / Branding ──
      // Pink bar at top
      page.drawRectangle({ x: 0, y: height - 80, width: width, height: 80, color: rgb(1, 0.41, 0.7) });
      
      page.drawText('CLASS NEXT DOOR', {
        x: 40, y: height - 50, size: 24, font: fontBold, color: rgb(1, 1, 1)
      });
      page.drawText('www.classnextdoor.com', {
        x: 40, y: height - 65, size: 10, font: fontReg, color: rgb(1, 1, 1)
      });

      // ── Invoice Info ──
      page.drawText('INVOICE / RECEIPT', {
        x: width - 180, y: height - 50, size: 16, font: fontBold, color: rgb(1, 1, 1)
      });
      page.drawText(`Date: ${data.date}`, {
        x: width - 180, y: height - 65, size: 10, font: fontReg, color: rgb(1, 1, 1)
      });

      // ── Details Section ──
      let y = height - 130;
      page.drawText('BOOKING DETAILS', { x: 40, y: y, size: 12, font: fontBold, color: rgb(0.2, 0.2, 0.2) });
      y -= 25;
      
      const details = [
        ['Booking ID:', `#${data.bid.padStart(6, '0')}`],
        ['Class Name:', data.title],
        ['Scheduled Date:', data.classDate || 'TBD'],
        ['Scheduled Time:', data.classTime || 'TBD'],
        ['Attendee:', data.student]
      ];

      details.forEach(([label, value]) => {
        page.drawText(label, { x: 40, y: y, size: 10, font: fontBold, color: rgb(0.4, 0.4, 0.4) });
        page.drawText(String(value), { x: 150, y: y, size: 10, font: fontReg, color: rgb(0, 0, 0) });
        y -= 20;
      });

      // ── Payment Section ──
      y -= 20;
      page.drawText('PAYMENT INFORMATION', { x: 40, y: y, size: 12, font: fontBold, color: rgb(0.2, 0.2, 0.2) });
      y -= 25;

      const payments = [
        ['Payment Method:', 'Razorpay (Online)'],
        ['Transaction ID:', data.payid || 'N/A'],
        ['Payment Status:', 'SUCCESS / PAID']
      ];

      payments.forEach(([label, value]) => {
        page.drawText(label, { x: 40, y: y, size: 10, font: fontBold, color: rgb(0.4, 0.4, 0.4) });
        page.drawText(String(value), { x: 150, y: y, size: 10, font: fontReg, color: rgb(0, 0, 0) });
        y -= 20;
      });

      // ── Summary Table ──
      y -= 40;
      page.drawRectangle({ x: 40, y: y - 5, width: width - 80, height: 30, color: rgb(0.96, 0.96, 0.96) });
      page.drawText('Description', { x: 50, y: y + 5, size: 11, font: fontBold, color: rgb(0.2, 0.2, 0.2) });
      page.drawText('Amount (INR)', { x: width - 130, y: y + 5, size: 11, font: fontBold, color: rgb(0.2, 0.2, 0.2) });
      
      y -= 35;
      page.drawText(`Platform booking for "${data.title}"`, { x: 50, y: y, size: 10, font: fontReg });
      page.drawText(`Rs. ${parseFloat(data.amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}`, { x: width - 130, y: y, size: 10, font: fontReg });
      
      y -= 20;
      page.drawLine({ start: { x: 40, y: y }, end: { x: width - 40, y: y }, color: rgb(0.8, 0.8, 0.8), thickness: 1 });
      
      y -= 30;
      page.drawText('TOTAL PAID', { x: width - 250, y: y, size: 14, font: fontBold, color: rgb(1, 0.41, 0.7) });
      page.drawText(`Rs. ${parseFloat(data.amount).toLocaleString('en-IN', {minimumFractionDigits: 2})}`, { x: width - 130, y: y, size: 14, font: fontBold, color: rgb(0, 0, 0) });

      // ── Footer ──
      page.drawText('Thank you for choosing Class Next Door!', {
        x: width/2 - 110, y: 100, size: 10, font: fontBold, color: rgb(0.5, 0.5, 0.5)
      });
      page.drawText('This is a computer generated document and does not require a physical signature.', {
        x: width/2 - 180, y: 85, size: 8, font: fontReg, color: rgb(0.7, 0.7, 0.7)
      });

      const pdfBytes = await pdfDoc.save();
      const blob     = new Blob([pdfBytes], { type: 'application/pdf' });
      const link     = document.createElement('a');
      link.href      = URL.createObjectURL(blob);
      link.download  = `Invoice_${data.bid.padStart(6, '0')}.pdf`;
      link.click();

    } catch (err) {
      console.error('Invoice Generation Error:', err);
      alert('Failed to generate invoice. Please try again.');
    }
  }

  async function generateCertificate(data) {
    try {
      const { PDFDocument, rgb, StandardFonts } = PDFLib;

      const pdfDoc = await PDFDocument.create();
      const page   = pdfDoc.addPage([841.89, 595.28]); // A4 Landscape
      const { width, height } = page.getSize();

      // Border and Background Decor
      page.drawRectangle({
        x: 0, y: 0, width: width, height: height,
        color: rgb(1, 1, 1),
      });

      // Pink side bar
      page.drawRectangle({
        x: 0, y: 0, width: 40, height: height,
        color: rgb(1, 0.41, 0.7),
      });

      // Main inner border
      page.drawRectangle({
        x: 60, y: 30, width: width - 90, height: height - 60,
        borderColor: rgb(0.9, 0.9, 0.9),
        borderWidth: 2,
      });

      // Load Fonts
      const titleFont = await pdfDoc.embedFont(StandardFonts.HelveticaBold);
      const italicFont = await pdfDoc.embedFont(StandardFonts.HelveticaOblique);
      const bodyFont  = await pdfDoc.embedFont(StandardFonts.Helvetica);

      // Logo Placeholder text
      page.drawText('CLASS NEXT DOOR', {
        x: 100, y: height - 100, size: 24, font: titleFont, color: rgb(1, 0.41, 0.7)
      });

      // Content
      page.drawText('CERTIFICATE OF ACHIEVEMENT', {
        x: width / 2 - 190, y: height - 160, size: 28, font: titleFont, color: rgb(0.1, 0.1, 0.1)
      });

      page.drawText('This certificate is proudly awarded to', {
        x: width / 2 - 120, y: height - 210, size: 16, font: italicFont, color: rgb(0.4, 0.4, 0.4)
      });

      // Student Name
      const nameWidth = titleFont.widthOfTextAtSize(data.student.toUpperCase(), 42);
      page.drawText(data.student.toUpperCase(), {
        x: width / 2 - (nameWidth / 2), y: height - 275, size: 42, font: titleFont, color: rgb(1, 0.41, 0.7)
      });

      // Divider line
      page.drawLine({
        start: { x: width/2 - 150, y: height - 295 },
        end: { x: width/2 + 150, y: height - 295 },
        color: rgb(0.8, 0.8, 0.8), thickness: 1
      });

      // Success message
      const msg = `For successfully completing the ${data.category} program:`;
      const msgWidth = bodyFont.widthOfTextAtSize(msg, 16);
      page.drawText(msg, {
        x: width / 2 - (msgWidth/2), y: height - 340, size: 16, font: bodyFont, color: rgb(0.4, 0.4, 0.4)
      });

      const classWidth = titleFont.widthOfTextAtSize(data.class, 24);
      page.drawText(data.class, {
        x: width / 2 - (classWidth/2), y: height - 380, size: 24, font: titleFont, color: rgb(0, 0, 0)
      });

      // Footer info
      page.drawText(`Awarded on ${data.date}`, {
        x: 100, y: 100, size: 14, font: bodyFont, color: rgb(0.5, 0.5, 0.5)
      });

      page.drawText('Verified by Parent Identity', {
        x: width - 300, y: 100, size: 14, font: italicFont, color: rgb(0.5, 0.5, 0.5)
      });

      // Signature line
      page.drawLine({
        start: { x: width - 300, y: 120 },
        end: { x: width - 100, y: 120 },
        color: rgb(0, 0, 0), thickness: 0.5
      });

      // Finalize
      const pdfBytes = await pdfDoc.save();
      const blob     = new Blob([pdfBytes], { type: 'application/pdf' });
      const link     = document.createElement('a');
      link.href      = URL.createObjectURL(blob);
      link.download  = `Certificate_${data.student.replace(/\s+/g, '_')}.pdf`;
      link.click();
    } catch (err) {
      console.error('PDF Generation Error:', err);
      alert('Failed to generate PDF. Please ensure you have an active internet connection.');
    }
  }

})();
</script>
<?= $this->endSection() ?>
