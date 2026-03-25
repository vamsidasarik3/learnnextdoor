<?= $this->extend('frontend/layout/base') ?>
<?= $this->section('css') ?>
<style>
/* ── Booking Page ─────────────────────────────────────── */
.bk-hero {
  background: var(--cnd-gradient);
  padding: 2rem 1.5rem 1.5rem;
  color: #fff;
}
.bk-hero-back { color: rgba(255,255,255,.75); font-size:.85rem; text-decoration:none; display:inline-flex; align-items:center; gap:.35rem; margin-bottom:.9rem; }
.bk-hero-back:hover { color:#fff; }
.bk-hero-title { font-size:clamp(1.3rem,3.5vw,2rem); font-weight:900; line-height:1.2; margin:0 0 .3rem; }
.bk-hero-meta  { font-size:.85rem; opacity:.85; display:flex; align-items:center; gap:.8rem; flex-wrap:wrap; }

.bk-layout { display:flex; gap:2rem; align-items:flex-start; padding:2rem 0 3rem; }
.bk-main   { flex:1; min-width:0; }
.bk-aside  { width:300px; flex-shrink:0; position:sticky; top:calc(var(--cnd-navbar-h)+1rem); }
@media(max-width:991.98px){ .bk-layout{flex-direction:column;} .bk-aside{width:100%;position:static;order:-1;} }

/* Card */
.bk-card {
  background:#fff;
  border-radius:18px;
  border:1.5px solid var(--cnd-card-border);
  box-shadow: var(--cnd-card-shadow);
  overflow:hidden;
  margin-bottom:1.5rem;
}
.bk-card-head {
  background: linear-gradient(135deg,#f5f0ff,#fff0f6);
  padding:1rem 1.3rem;
  border-bottom:1px solid var(--cnd-card-border);
  font-weight:800; font-size:.95rem; color:var(--cnd-dark);
  display:flex; align-items:center; gap:.5rem;
}
.bk-card-head i { color:var(--cnd-pink); }
.bk-card-body { padding:1.3rem; }

/* Steps */
.bk-steps { display:flex; align-items:center; margin-bottom:1.5rem; }
.bk-step  { display:flex; align-items:center; gap:.4rem; font-size:.8rem; font-weight:700; color:var(--cnd-muted); }
.bk-step-dot { width:28px;height:28px;border-radius:50%;background:#e8e0ff;color:var(--cnd-grad-start);font-size:.75rem;font-weight:800;display:flex;align-items:center;justify-content:center;flex-shrink:0;transition:all .3s; }
.bk-step.active .bk-step-dot { background:var(--cnd-pink);color:#fff; }
.bk-step.done   .bk-step-dot { background:#22c55e;color:#fff; }
.bk-step.active { color:var(--cnd-pink); }
.bk-step-line   { flex:1;height:2px;background:#e8e0ff;margin:0 .4rem; }
.bk-step.done + .bk-step-line { background:#22c55e; }

/* Form inputs */
.bk-label { font-size:.8rem;font-weight:700;color:var(--cnd-dark);margin-bottom:.35rem;display:block; }
.bk-input {
  width:100%;padding:.75rem 1rem;
  border:2px solid var(--cnd-card-border);border-radius:12px;
  font-size:.95rem;font-family:'Poppins',sans-serif;
  outline:none;transition:border-color .2s,box-shadow .2s;
  background:#fafafa;color:var(--cnd-dark);
}
.bk-input:focus { border-color:var(--cnd-grad-start);box-shadow:0 0 0 4px rgba(108,99,255,.1);background:#fff; }
.bk-input-group { display:flex; }
.bk-input-prefix {
  background:#f0ebff;border:2px solid var(--cnd-card-border);border-right:none;
  border-radius:12px 0 0 12px;padding:.75rem 1rem;font-size:.95rem;font-weight:600;
  color:var(--cnd-grad-start);white-space:nowrap;
}
.bk-input-group .bk-input { border-radius:0 12px 12px 0; }

/* OTP */
.bk-otp-input {
  text-align:center;font-size:2rem;font-weight:800;letter-spacing:.5rem;
  padding:.75rem .5rem;
}

/* Buttons */
.bk-btn-primary {
  display:block;width:100%;padding:.9rem;
  background:var(--cnd-gradient);border:none;border-radius:var(--cnd-radius-pill);
  color:#fff;font-size:1rem;font-weight:700;font-family:'Poppins',sans-serif;
  cursor:pointer;transition:opacity .2s,transform .2s,box-shadow .2s;
  box-shadow:0 6px 20px rgba(108,99,255,.3);
}
.bk-btn-primary:hover { opacity:.9;transform:translateY(-2px);box-shadow:0 10px 28px rgba(108,99,255,.35); }
.bk-btn-primary:disabled { opacity:.6;cursor:not-allowed;transform:none; }
.bk-btn-outline {
  display:inline-flex;align-items:center;gap:.4rem;
  padding:.55rem 1.2rem;border:2px solid var(--cnd-card-border);
  background:#fff;border-radius:var(--cnd-radius-pill);font-size:.85rem;
  font-weight:600;color:var(--cnd-muted);cursor:pointer;transition:all .2s;
}
.bk-btn-outline:hover { border-color:var(--cnd-pink);color:var(--cnd-pink); }

/* Aside price card */
.bk-price-card {
  background:linear-gradient(135deg,#fdf4ff,#fff8e1);
  border:1.5px solid #eac3ff;
  border-radius:18px;padding:1.3rem;
}
.bk-price-amount { font-size:2.2rem;font-weight:900;color:var(--cnd-dark); }
.bk-price-amount sup { font-size:1rem;vertical-align:top;margin-top:.4rem;display:inline-block; }
.bk-price-label { font-size:.8rem;color:var(--cnd-muted);margin-top:.15rem; }
.bk-price-divider { height:1px;background:#eac3ff;margin:1rem 0; }
.bk-class-meta { font-size:.83rem; color:var(--cnd-dark);display:flex;align-items:flex-start;gap:.5rem;margin-bottom:.65rem; }
.bk-class-meta i { color:var(--cnd-pink);font-size:.95rem;flex-shrink:0;margin-top:.1rem; }
.bk-early-bird { background:linear-gradient(135deg,#fff8e1,#fef3c7);border:1.5px solid #fde68a;border-radius:10px;padding:.7rem .9rem;font-size:.82rem;margin-top:.8rem; }

/* Cover image */
.bk-cover { width:100%;height:180px;object-fit:cover;border-radius:14px;margin-bottom:1rem; }

/* Alert */
.bk-alert { border-radius:12px;padding:.75rem 1rem;font-size:.88rem;margin-bottom:1rem;display:none; }
.bk-alert.show { display:block; }
.bk-alert-error { background:#fff0f0;border:1.5px solid #fecaca;color:#dc2626; }
.bk-alert-success { background:#f0fdf4;border:1.5px solid #bbf7d0;color:#16a34a; }
.bk-alert-info  { background:#f0f9ff;border:1.5px solid #bae6fd;color:#0369a1; }

/* Dev OTP hint */
.bk-dev-hint { background:#fffbeb;border:1.5px solid #fde68a;border-radius:10px;padding:.6rem .9rem;font-size:.82rem;margin-bottom:.8rem;display:none; }
.bk-dev-hint.show { display:block; }

/* Strength */
.bk-pw-strength { height:4px;background:#e5e7eb;border-radius:2px;margin-top:.4rem;overflow:hidden; }
.bk-pw-bar { height:100%;border-radius:2px;transition:width .3s,background .3s;width:0; }
/* Batch Cards */
.batch-grid { display: grid; gap: 1rem; margin-top: 0.5rem; }
.batch-card {
  border: 1.5px solid var(--cnd-card-border);
  border-radius: 14px; padding: 1rem; cursor: pointer;
  transition: all 0.2s; position: relative;
  background: #fff;
}
.batch-card:hover { border-color: var(--cnd-grad-start); background: #f8f7ff; }
.batch-card.selected {
  border-color: var(--cnd-grad-start);
  background: #f0eeff;
  box-shadow: 0 0 0 4px rgba(108,99,255,0.1);
}
.batch-card.selected::after {
  content: '✓'; position: absolute; top: 10px; right: 10px;
  width: 22px; height: 22px; background: var(--cnd-grad-start);
  color: #fff; border-radius: 50%; display: flex;
  align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 900;
}
.batch-name { font-weight: 800; font-size: 0.95rem; color: var(--cnd-dark); margin-bottom: 0.4rem; }
.batch-info { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; font-size: 0.8rem; }
.batch-stat { display: flex; align-items: center; gap: 0.35rem; color: var(--cnd-muted); }
.batch-stat i { color: var(--cnd-pink); font-size: 0.9rem; }
.batch-price-tag {
  margin-top: 0.8rem; display: flex; align-items: center; justify-content: space-between;
  padding-top: 0.6rem; border-top: 1px dashed #e8e0ff;
}
.batch-price-val { font-weight: 900; color: var(--cnd-grad-start); font-size: 1.1rem; }
.batch-seats { font-size: 0.75rem; font-weight: 700; }
.batch-seats.low { color: #f59e0b; }
.batch-seats.full { color: #dc2626; }
.batch-seats.available { color: #16a34a; }

.batch-card.disabled { opacity: 0.6; cursor: not-allowed; pointer-events: none; background: #f9f9f9; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$l    = (array)$listing;
$lid  = (int)($l['id'] ?? 0);
$isFree  = ($price <= 0);
$hasTrial = !empty($l['free_trial']);
?>

<!-- Hero -->
<div class="bk-hero">
  <div class="container-fluid px-3 px-lg-5">
    <a href="<?= base_url('classes/' . $lid) ?>" class="bk-hero-back">
      <i class="bi bi-arrow-left"></i> Back to class
    </a>
    <h1 class="bk-hero-title"><?= esc($l['title'] ?? 'Book Class') ?></h1>
    <div class="bk-hero-meta">
      <?php if (!empty($l['category_name'])): ?>
      <span><i class="bi bi-tag-fill"></i> <?= esc($l['category_name']) ?></span>
      <?php endif; ?>
      <?php if (!empty($l['address'])): ?>
      <span><i class="bi bi-geo-alt-fill"></i> <?= esc($l['address']) ?></span>
      <?php endif; ?>
      <?php if ($isFree): ?>
      <span class="badge bg-success text-white px-3 py-2">Free</span>
      <?php else: ?>
      <span><i class="bi bi-currency-rupee"></i><?= number_format($price) ?></span>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- Layout -->
<div class="container-fluid px-3 px-lg-5">
  <div class="bk-layout">

    <!-- ── Main: Booking Form ── -->
    <div class="bk-main">

      <!-- Step indicator -->
      <div class="bk-steps mb-4">
        <div class="bk-step active" id="step-ind-1">
          <div class="bk-step-dot">1</div>
          <span class="d-none d-sm-inline">Your Details</span>
        </div>
        <div class="bk-step-line"></div>
        <div class="bk-step" id="step-ind-2">
          <div class="bk-step-dot">2</div>
          <span class="d-none d-sm-inline"><?= $isFree ? 'Confirmation' : 'Payment' ?></span>
        </div>
      </div>

      <!-- Alert -->
      <div id="bkAlert" class="bk-alert bk-alert-error" role="alert" aria-live="polite"></div>

      <!-- ── STEP 1: Student Info ── -->
      <div id="bkStep1">
        <div class="bk-card">
          <div class="bk-card-head"><i class="bi bi-person-fill"></i> Student Details</div>
          <div class="bk-card-body">
            <form id="bkForm1" novalidate>
              <div class="mb-3">
                <label for="bkStudentName" class="bk-label">Student Name <span class="text-danger">*</span></label>
                <input type="text" id="bkStudentName" class="bk-input" placeholder="e.g. Arya Sharma"
                       required minlength="2" maxlength="150" autocomplete="name">
              </div>
              <div class="row g-3 mb-3">
                <div class="col-6">
                  <label for="bkStudentAge" class="bk-label">Student Age</label>
                  <input type="number" id="bkStudentAge" class="bk-input" placeholder="8" min="1" max="18">
                </div>
                <div class="col-6">
                  <label for="bkBookingType" class="bk-label">Booking Type</label>
                  <select id="bkBookingType" class="bk-input" style="cursor:pointer;">
                    <option value="regular">Regular</option>
                    <?php if ($hasTrial): ?>
                    <option value="trial">Free Trial</option>
                    <?php endif; ?>
                  </select>
                </div>
              </div>
              <?php if ($l['type'] === 'regular' && !empty($l['batches'])): ?>
              <div class="mb-4">
                <label class="bk-label mb-2">Available Batches <span class="text-danger">*</span></label>
                <input type="hidden" id="bkBatch" name="batch_index" value="" required>
                <div class="batch-grid">
                  <?php foreach($l['batches'] as $idx => $batch): 
                    $booked = (int)($batch_counts[$idx] ?? 0);
                    $max = (int)($batch['batch_size'] ?? 0);
                    $available = $max > 0 ? ($max - $booked) : 999;
                    $isFull = ($max > 0 && $available <= 0);
                  ?>
                  <div class="batch-card <?= $isFull ? 'disabled' : '' ?>" 
                       data-index="<?= $idx ?>" 
                       data-price="<?= (float)($batch['price'] ?? 0) ?>"
                       onclick="selectBatch(this)">
                    <div class="batch-name"><?= esc($batch['name']) ?></div>
                    <div class="batch-info">
                      <div class="batch-stat">
                        <i class="bi bi-calendar3"></i>
                        <span>Starts <?= date('D, d M', strtotime($batch['batch_start_date'])) ?></span>
                      </div>
                      <div class="batch-stat">
                        <i class="bi bi-clock"></i>
                        <span><?= date('g:i A', strtotime($batch['from_time'])) ?> - <?= date('g:i A', strtotime($batch['to_time'])) ?></span>
                      </div>
                    </div>
                    <div class="batch-price-tag">
                      <div class="batch-price-val">₹<?= number_format((float)($batch['price'] ?? 0)) ?></div>
                      <div class="batch-seats <?= $isFull ? 'full' : ($available < 5 ? 'low' : 'available') ?>">
                        <?php if ($isFull): ?>
                          <i class="bi bi-x-circle-fill"></i> Full
                        <?php elseif ($max > 0): ?>
                          <i class="bi bi-people-fill"></i> <?= $available ?> seats left
                        <?php else: ?>
                          <i class="bi bi-check-circle-fill"></i> Available
                        <?php endif; ?>
                      </div>
                    </div>
                  </div>
                  <?php endforeach; ?>
                </div>
              </div>
              <?php endif; ?>

              <?php if (!empty($slots)): ?>
              <div class="mb-3">
                <label for="bkSlot" class="bk-label">Preferred Session</label>
                <select id="bkSlot" class="bk-input" style="cursor:pointer;">
                  <option value="">— Any / Contact the provider —</option>
                  <?php foreach(array_slice($slots, 0, 10) as $slot): ?>
                  <option value="<?= esc($slot['available_date'].'|'.$slot['available_time']) ?>">
                    <?= date('D, d M', strtotime($slot['available_date'])) ?> at <?= date('g:i A', strtotime($slot['available_time'])) ?>
                  </option>
                  <?php endforeach; ?>
                </select>
              </div>
              <?php endif; ?>
              <div class="mb-3">
                <label for="bkPhone" class="bk-label">Your Mobile Number <span class="text-danger">*</span></label>
                <div class="bk-input-group">
                  <span class="bk-input-prefix">+91</span>
                  <input type="tel" id="bkPhone" class="bk-input"
                         placeholder="9876543210" maxlength="10" pattern="[6-9][0-9]{9}"
                         required autocomplete="tel-national"
                         value="<?= esc($user['phone'] ?? '') ?>">
                </div>
                <p style="font-size:.75rem;color:var(--cnd-muted);margin-top:.3rem;">Mobile No will be used for session updates.</p>
              </div>
              <button type="submit" class="bk-btn-primary" id="bkStep1Btn">
                <span id="bkStep1BtnTxt">Book Now <i class="bi bi-arrow-right ms-1"></i></span>
                <span id="bkStep1Spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- ── STEP 2: Payment / Confirm (Razorpay launches) ── -->
      <div id="bkStep2" style="display:none;">
        <div class="bk-card">
          <div class="bk-card-head"><i class="bi bi-credit-card-fill"></i>
            <?= $isFree ? 'Confirm Booking' : 'Complete Payment' ?>
          </div>
          <div class="bk-card-body text-center py-4">
            <?php if ($isFree): ?>
              <div style="font-size:3rem;">✅</div>
              <h3 class="fw-800 mt-2 mb-1" style="font-size:1.2rem;">Booking Confirmed!</h3>
              <p class="text-muted small">Your class booking has been completed successfully.</p>
            <?php else: ?>
              <div style="font-size:3rem;">💳</div>
              <h3 class="fw-800 mt-2 mb-1" style="font-size:1.2rem;">Ready to Pay</h3>
              <p class="text-muted small mb-3">
                Click below to complete your payment of <strong>₹<?= number_format($price) ?></strong> securely via Razorpay.
              </p>
              <button id="bkPayBtn" class="bk-btn-primary" style="max-width:320px;margin:0 auto;">
                <span id="bkPayBtnTxt"><i class="bi bi-lock-fill me-2"></i>Pay ₹<?= number_format($price) ?> Securely</span>
                <span id="bkPaySpinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
              </button>
              <p style="font-size:.75rem;color:var(--cnd-muted);margin-top:.8rem;">
                <i class="bi bi-shield-lock me-1"></i>Secured by Razorpay.
              </p>
            <?php endif; ?>
          </div>
        </div>
      </div>

    </div>

    <!-- ── Aside: Class Summary ── -->
    <aside class="bk-aside">
      <img src="<?= listing_img_url($cover) ?>" alt="<?= esc($l['title'] ?? '') ?>" class="bk-cover">

      <div class="bk-price-card">
        <div class="bk-price-amount">
          <?php if ($isFree): ?>
          <span style="color:#22c55e;font-size:1.6rem;">Free</span>
          <?php else: ?>
          <sup>₹</sup><?= number_format($price) ?>
          <?php endif; ?>
        </div>
        <div class="bk-price-label">
          <?= $isFree ? 'Free trial class' : 'per ' . esc($l['pricing_unit'] ?? 'session') ?>
        </div>

        <?php if ($early_bird): ?>
        <div class="bk-early-bird">
          <span style="font-weight:800;color:#b45309;">🐣 Early Bird Price!</span><br>
          <span style="color:#92400e;">Original: ₹<?= number_format((float)($l['price'] ?? 0)) ?></span>
        </div>
        <?php endif; ?>

        <div class="bk-price-divider"></div>

        <?php if (!empty($l['address'])): ?>
        <div class="bk-class-meta">
          <i class="bi bi-geo-alt-fill"></i>
          <span><?= esc($l['address']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($l['type'])): ?>
        <div class="bk-class-meta">
          <i class="bi bi-tag-fill"></i>
          <span><?= esc(ucfirst($l['type'])) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($l['age_group'])): ?>
        <div class="bk-class-meta">
          <i class="bi bi-people-fill"></i>
          <span>Age Group: <?= esc($l['age_group']) ?></span>
        </div>
        <?php endif; ?>
        <?php if (!empty($l['duration'])): ?>
        <div class="bk-class-meta">
          <i class="bi bi-clock-fill"></i>
          <span><?= esc($l['duration']) ?> mins per session</span>
        </div>
        <?php endif; ?>
      </div>

      <!-- Logged in as -->
      <div class="mt-3 p-3" style="background:#f8f4ff;border-radius:14px;border:1.5px solid #e8e0ff;font-size:.82rem;">
        <p class="mb-1 fw-700" style="color:var(--cnd-grad-start);">
          <i class="bi bi-person-circle me-1"></i>Booking as:
        </p>
        <p class="mb-0 fw-600" style="color:var(--cnd-dark);"><?= esc($user['name']) ?></p>
        <p class="mb-0 text-muted"><?= esc($user['email']) ?></p>
      </div>
    </aside>

  </div>
</div>

<!-- ── Booking data for JS ── -->
<script id="bkData" type="application/json">
<?= json_encode([
  'listing_id'  => $lid,
  'title'       => $l['title'] ?? '',
  'price'       => $price,
  'is_free'     => $isFree,
  'rp_key'      => $rp_key,
  'dev_mode'    => $dev_mode,
  'user_name'   => $user['name'] ?? '',
  'user_email'  => $user['email'] ?? '',
  'user_phone'  => $user['phone'] ?? '',
  'pricing_unit'=> $l['pricing_unit'] ?? 'session',
  'csrf_name'   => csrf_token(),
  'csrf_token'  => csrf_hash(),
  'base_url'    => rtrim(base_url(), '/'),
], JSON_HEX_TAG | JSON_HEX_AMP) ?>
</script>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- Razorpay SDK -->
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
(function(){
  'use strict';

  var D    = JSON.parse(document.getElementById('bkData').textContent);
  var BASE = D.base_url + '/';
  var CSRF = { name: D.csrf_name, token: D.csrf_token };
  var phone = D.user_phone || '';
  var rpOrderData = null;

  /* ── CSRF refresh helper ─────────────── */
  function refreshCsrf(cb) {
    fetch(BASE + 'api/csrf-refresh', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(function(r){ return r.json(); })
      .then(function(j){ if(j.token){ CSRF.token = j.token; } if(cb) cb(); })
      .catch(function(){ if(cb) cb(); });
  }

  /* ── Alert ───────────────────────────── */
  function showAlert(msg, type) {
    var el = document.getElementById('bkAlert');
    el.className = 'bk-alert bk-alert-' + (type||'error') + ' show';
    el.innerHTML = '<i class="bi bi-exclamation-circle-fill me-2"></i>' + msg;
    el.scrollIntoView({ behavior:'smooth', block:'nearest' });
  }
  function hideAlert() {
    var el = document.getElementById('bkAlert');
    el.className = 'bk-alert bk-alert-error';
    el.style.display = '';
  }

  /* ── Step nav ─────────────────────────── */
  function goStep(n) {
    ['bkStep1','bkStep2'].forEach(function(id,i){
      document.getElementById(id).style.display = (i+1===n) ? '' : 'none';
    });
    [1,2].forEach(function(s){
      var el = document.getElementById('step-ind-'+s);
      if(!el) return;
      el.classList.remove('active','done');
      if(s < n) el.classList.add('done');
      if(s === n) el.classList.add('active');
    });
    hideAlert();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  /* ── Loading ──────────────────────────── */
  function setLoading(btnId, spinnerId, txtId, state) {
    var btn = document.getElementById(btnId);
    var sp  = document.getElementById(spinnerId);
    var txt = document.getElementById(txtId);
    if(btn) btn.disabled = state;
    if(sp)  sp.classList.toggle('d-none', !state);
    if(txt) txt.classList.toggle('d-none', state);
  }

  /* ── POST helper ─────────────────────── */
  function postJson(url, body, cb) {
    body[CSRF.name] = CSRF.token;
    fetch(BASE + url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
      body: JSON.stringify(body),
    })
    .then(function(r){ return r.json(); })
    .then(function(j){
      // Refresh CSRF token from response header if available
      CSRF.token = j.csrf_token || CSRF.token;
      cb(j);
    })
    .catch(function(){ cb({ success:false, message:'Network error. Please try again.' }); });
  }

  /* ── Batch selection logic ────────── */
  window.selectBatch = function(el) {
    if (el.classList.contains('disabled')) return;
    
    // UI Update
    document.querySelectorAll('.batch-card').forEach(function(c){ c.classList.remove('selected'); });
    el.classList.add('selected');
    
    // Value Update
    var idx = el.dataset.index;
    var price = parseFloat(el.dataset.price || 0);
    document.getElementById('bkBatch').value = idx;
    
    // Update Aside Price
    var priceDisplay = document.querySelector('.bk-price-amount');
    if (priceDisplay) {
      if (price <= 0) {
        priceDisplay.innerHTML = '<span style="color:#22c55e;font-size:1.6rem;">Free</span>';
      } else {
        priceDisplay.innerHTML = '<sup>₹</sup>' + price.toLocaleString();
      }
    }
    
    // Update Pay Button context
    var payBtnTxt = document.getElementById('bkPayBtnTxt');
    if (payBtnTxt && price > 0) {
      payBtnTxt.innerHTML = '<i class="bi bi-lock-fill me-2"></i>Pay ₹' + price.toLocaleString() + ' Securely';
    }
  };

  /* ── Booking type logic ────────────── */
  document.getElementById('bkBookingType').addEventListener('change', function() {
    var isTrial = (this.value === 'trial');
    var priceDisplay = document.querySelector('.bk-price-amount');
    var priceLabel = document.querySelector('.bk-price-label');
    
    if (isTrial) {
      if (priceDisplay) priceDisplay.innerHTML = '<span style="color:#22c55e;font-size:1.6rem;">Free</span>';
      if (priceLabel) priceLabel.textContent = 'Free trial class';
    } else {
      // Re-apply selected batch price or base price
      var selectedBatch = document.querySelector('.batch-card.selected');
      var price = selectedBatch ? parseFloat(selectedBatch.dataset.price) : D.price;
      
      if (priceDisplay) {
        if (price <= 0) {
          priceDisplay.innerHTML = '<span style="color:#22c55e;font-size:1.6rem;">Free</span>';
        } else {
          priceDisplay.innerHTML = '<sup>₹</sup>' + price.toLocaleString();
        }
      }
      if (priceLabel) priceLabel.textContent = 'per ' + (D.pricing_unit || 'session');
    }
  });

  /* ═══════════════════════════════════°
     STEP 1 — Student info + Send OTP
  °═══════════════════════════════════ */
  document.getElementById('bkForm1').addEventListener('submit', function(e){
    e.preventDefault();
    hideAlert();
    var name  = document.getElementById('bkStudentName').value.trim();
    var age   = document.getElementById('bkStudentAge').value.trim();
    var btype = document.getElementById('bkBookingType').value;
    var ph    = document.getElementById('bkPhone').value.replace(/\D/g,'').trim();
    var slot  = document.getElementById('bkSlot') ? document.getElementById('bkSlot').value : '';
    var batchInput = document.getElementById('bkBatch');
    var batchIdx = batchInput ? batchInput.value : null;

    if(!name)  { showAlert('Please enter the student\'s name.'); return; }
    if(!/^[6-9][0-9]{9}$/.test(ph)) { showAlert('Enter a valid 10-digit Indian mobile number (starts with 6-9).'); return; }
    if(batchInput && (batchIdx === null || batchIdx === "")) { showAlert('Please select a batch.'); return; }

    var parts = slot ? slot.split('|') : [];
    var body = {
      listing_id:   D.listing_id,
      booking_type: btype,
      student_name: name,
      student_age:  age || null,
      phone:        ph,
      class_date:   parts[0] || null,
      class_time:   parts[1] || null,
      batch_index:  batchIdx
    };

    phone = ph;
    setLoading('bkStep1Btn','bkStep1Spinner','bkStep1BtnTxt',true);
    postJson('booking/init', body, function(res){
      setLoading('bkStep1Btn','bkStep1Spinner','bkStep1BtnTxt',false);
      if(!res.success){
        if(res.auth_required){
          window.location.href = res.login_url || (BASE + 'login');
          return;
        }
        var err = res.errors ? Object.values(res.errors).join(' ') : (res.message||'Error');
        showAlert(err); return;
      }

      if (res.otp_skipped) {
        if (!res.paid) {
           // Free booking confirmed
           goStep(2);
        } else {
           // Paid booking - show payment step
           rpOrderData = res;
           goStep(2);
        }
        return;
      }

      document.getElementById('bkPhoneDisplay').textContent = '+91 ' + ph.replace(/(\d{2})(\d{4})(\d{4})/,'$1** ****');
      if(res.dev_otp){
        document.getElementById('bkDevHint').classList.add('show');
        document.getElementById('bkDevOtpVal').textContent = res.dev_otp;
        document.getElementById('bkOtp').value = res.dev_otp;
      }
      goStep(2);
    });
  });

  /* ── Phone digit filter ── */
  document.getElementById('bkPhone').addEventListener('input', function(){
    this.value = this.value.replace(/\D/g,'').slice(0,10);
  });

  /* ═══════════════════════════════════°
     STEP 3 — Razorpay Payment
  °═══════════════════════════════════ */
  var payBtn = document.getElementById('bkPayBtn');
  if(payBtn){
    payBtn.addEventListener('click', function(){
      if(!rpOrderData){ showAlert('Payment session expired. Please refresh.'); return; }
      setLoading('bkPayBtn','bkPaySpinner','bkPayBtnTxt',true);

      var options = {
        key:         rpOrderData.rp_key || D.rp_key,
        amount:      rpOrderData.amount,
        currency:    rpOrderData.currency || 'INR',
        name:        'Class Next Door',
        description: rpOrderData.description || 'Class Booking',
        order_id:    rpOrderData.order_id,
        prefill: {
          name:    D.user_name,
          email:   D.user_email,
          contact: (rpOrderData.prefill && rpOrderData.prefill.contact) || ('+91' + phone),
        },
        theme: { color: '#3F3590' },
        modal: {
          ondismiss: function(){
            setLoading('bkPayBtn','bkPaySpinner','bkPayBtnTxt',false);
            showAlert('Payment cancelled. You can try again.','info');
          }
        },
        handler: function(response){
          // Verify payment on server
          postJson('booking/confirm-payment', {
            razorpay_payment_id:  response.razorpay_payment_id,
            razorpay_order_id:    response.razorpay_order_id,
            razorpay_signature:   response.razorpay_signature,
          }, function(res){
            if(res.success && res.redirect_url){
              window.location.href = res.redirect_url;
            } else {
              setLoading('bkPayBtn','bkPaySpinner','bkPayBtnTxt',false);
              showAlert(res.message || 'Payment verification failed. Please contact support.');
            }
          });
        },
      };

      try {
        var rzp = new Razorpay(options);
        rzp.on('payment.failed', function(response){
          setLoading('bkPayBtn','bkPaySpinner','bkPayBtnTxt',false);
          showAlert('Payment failed: ' + (response.error.description || 'Unknown error') + '. Please try again.');
        });
        rzp.open();
      } catch(err){
        setLoading('bkPayBtn','bkPaySpinner','bkPayBtnTxt',false);
        showAlert('Could not launch payment gateway. Please refresh and try again.');
      }
    });
  }

  /* ── Auto-select first batch if only one or from URL ── */
  var batches = document.querySelectorAll('.batch-card');
  var urlParams = new URLSearchParams(window.location.search);
  var preselectBatch = urlParams.get('batch');
  
  if (preselectBatch) {
    var found = false;
    batches.forEach(function(b){
      if (b.querySelector('.batch-name').textContent.trim() === preselectBatch) {
        selectBatch(b);
        found = true;
      }
    });
    if (!found && batches.length === 1) selectBatch(batches[0]);
  } else if (batches.length === 1) {
    selectBatch(batches[0]);
  }

})();
</script>
<?= $this->endSection() ?>
