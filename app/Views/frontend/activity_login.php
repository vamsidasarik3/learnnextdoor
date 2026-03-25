<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('content') ?>

<section class="min-vh-100 d-flex align-items-center py-5 bg-light">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-5">
        
        <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
          <div class="card-body p-5">
            <div class="text-center mb-4">
               <div class="bg-soft-pink text-pink rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                  <i class="bi bi-person-badge fs-1"></i>
               </div>
               <h2 class="fw-bold">Welcome Back</h2>
               <p class="text-muted">Enter your registered mobile number to view your class activity and certificates.</p>
            </div>

            <!-- Login / Identify Form -->
            <form id="activityLoginForm">
               <div class="mb-4">
                  <label class="form-label small fw-bold text-uppercase letter-spacing-sm">Mobile Number</label>
                  <div class="input-group input-group-lg">
                     <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-phone"></i></span>
                     <input type="tel" id="loginPhone" class="form-control border-start-0 rounded-end-3" placeholder="98765 43210" maxlength="10" required pattern="[6-9][0-9]{9}">
                  </div>
                  <div class="form-text small">We'll send a quick OTP to verify it's you.</div>
               </div>

               <button type="submit" id="loginSubmitBtn" class="btn btn-pink w-100 py-3 rounded-pill fw-bold shadow-sm">
                  <span id="loginSpinner" class="spinner-border spinner-border-sm d-none me-2"></span>
                  Start Learning
               </button>
            </form>

            <div class="mt-5 text-center">
               <p class="text-muted small">New to Class Next Door? <br><a href="<?= base_url('classes') ?>" class="text-pink fw-bold text-decoration-none">Explore Nearby Classes</a></p>
            </div>
          </div>
        </div>

        <p class="text-center mt-4 text-muted small">&copy; <?= date('Y') ?> Class Next Door. All rights reserved.</p>

      </div>
    </div>
  </div>
</section>

<!-- ══ OTP MODAL ══════════════════════════════════════════════ -->
<div class="modal fade" id="loginOtpModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-body p-5 text-center">
        <div class="mb-4">
           <div class="bg-soft-pink text-pink rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
              <i class="bi bi-shield-lock fs-2"></i>
           </div>
           <h4 class="fw-bold">Verification Code</h4>
           <p class="text-muted">Enter the 6-digit code sent to <br><strong id="displayPhone"></strong></p>
        </div>

        <div class="mb-4">
           <input type="text" id="loginOtp" class="form-control form-control-lg text-center fw-bold fs-2 rounded-3" maxlength="6" placeholder="000000">
           <div id="otpHelp" class="mt-2 text-pink small fw-600 d-none">Dev Hint: OTP is <span id="hintVal"></span></div>
        </div>

        <button id="verifyLoginBtn" class="btn btn-pink w-100 py-3 rounded-pill fw-bold shadow-sm mb-3">
           Verify & Continue
        </button>
        <button class="btn btn-link btn-sm text-muted text-decoration-none" id="resendLoginOtp">Resend Code</button>
      </div>
    </div>
  </div>
</div>

<style>
.bg-soft-pink { background: rgba(255, 104, 180, 0.08); }
.text-pink { color: #FF68B4; }
.btn-pink { background: #FF68B4; color: #fff; border: none; }
.btn-pink:hover { background: #FF1493; color: #fff; }
.form-control:focus { border-color: #FF68B4; box-shadow: 0 0 0 0.25rem rgba(255, 104, 180, 0.1); }
.letter-spacing-sm { letter-spacing: 0.1rem; }
.fw-600 { font-weight: 600; }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
(function(){
  'use strict';

  var otpModal = new bootstrap.Modal(document.getElementById('loginOtpModal'));
  var phone    = '';

  document.getElementById('activityLoginForm').addEventListener('submit', function(e){
    e.preventDefault();
    phone = document.getElementById('loginPhone').value.trim();
    if(!/^[6-9][0-9]{9}$/.test(phone)){ alert('Invalid phone number.'); return; }

    toggleLoading(true);

    // Reuse existing booking/init for OTP if possible, or create a new endpoint. 
    // For now, let's use the booking/init logic but for "login".
    fetch('<?= base_url('booking/init') ?>', {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify({ phone: phone, identity_only: true })
    })
    .then(r => r.json())
    .then(res => {
      toggleLoading(false);
      if(res.success){
        document.getElementById('displayPhone').textContent = '+91 ' + phone;
        if(res.dev_otp){
          document.getElementById('otpHelp').classList.remove('d-none');
          document.getElementById('hintVal').textContent = res.dev_otp;
        }
        otpModal.show();
      } else { alert(res.message || 'Error sending OTP.'); }
    })
    .catch(() => toggleLoading(false));
  });

  document.getElementById('verifyLoginBtn').addEventListener('click', function(){
    var otp = document.getElementById('loginOtp').value.trim();
    if(!otp || otp.length !== 6){ alert('Enter 6-digit OTP.'); return; }

    fetch('<?= base_url('booking/verify-otp') ?>', {
      method: "POST",
      headers: { "Content-Type": "application/json", "X-Requested-With": "XMLHttpRequest", "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content },
      body: JSON.stringify({ phone: phone, otp: otp, identity_only: true })
    })
    .then(r => r.json())
    .then(res => {
      if(res.success){
        // Success! The controller should have set the session/cookie.
        location.reload();
      } else { alert(res.message || 'Verification failed.'); }
    });
  });

  function toggleLoading(show){
    var btn  = document.getElementById('loginSubmitBtn');
    var spin = document.getElementById('loginSpinner');
    if(show){ btn.disabled=true; spin.classList.remove('d-none'); }
    else { btn.disabled=false; spin.classList.add('d-none'); }
  }

})();
</script>
<?= $this->endSection() ?>
