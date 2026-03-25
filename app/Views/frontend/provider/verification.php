<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('content') ?>
<!-- ══ VERIFICATION HEADER ══════════════════════════════════════ -->
<section class="cnd-provider-hero py-5" style="background: linear-gradient(135deg, #3F3590 0%, #FF68B4 100%);">
  <div class="container py-4">
    <div class="row align-items-center">
      <div class="col-lg-8 text-white">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb cnd-breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('provider/listings') ?>" class="text-white opacity-75">Dashboard</a></li>
            <li class="breadcrumb-item active text-white" aria-current="page">Verification & KYC</li>
          </ol>
        </nav>
        <h1 class="display-5 fw-bold mb-2">Get Verified</h1>
        <p class="lead opacity-90 mb-0">Complete your profile to build trust with parents and enable payouts.</p>
      </div>
    </div>
  </div>
</section>

<!-- ══ VERIFICATION CONTENT ══════════════════════════════════════ -->
<section class="py-5 bg-light min-vh-100">
  <div class="container">

    <div class="row g-4">
       <!-- Left Side: Phone & Email -->
       <div class="col-lg-6">
          <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
             <h5 class="fw-bold mb-4"><i class="bi bi-shield-check text-pink me-2"></i>Contact Verification</h5>
             

             <!-- Phone Verification -->
             <div class="d-flex align-items-center justify-content-between p-3 bg-light rounded-3 border-start border-4 <?= $user->phone_verified ? 'border-success' : 'border-warning' ?> mb-4">
                <div>
                   <div class="small text-muted text-uppercase fw-bold letter-spacing-sm">Phone Number</div>
                   <div class="fw-bold"><?= esc($user->phone ?: 'Not set') ?></div>
                </div>
                <div>
                   <?php if($user->phone_verified): ?>
                      <span class="badge bg-success-soft text-success rounded-pill px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Verified</span>
                   <?php elseif($user->phone): ?>
                      <button class="btn btn-pink btn-sm rounded-pill px-3" onclick="verifyPhoneDirectly()">Verify Now</button>
                   <?php else: ?>
                      <a href="<?= base_url('profile') ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">Set Phone</a>
                   <?php endif; ?>
                </div>
             </div>

             <!-- Proceed to KYC Button -->
             <?php if($user->phone_verified): ?>
                <div id="kycUnlockBtnBox">
                   <button class="btn btn-pink w-100 py-3 rounded-pill fw-bold shadow-sm" onclick="unlockKyc()" id="btnProceedKyc">
                      Proceed to KYC <i class="bi bi-arrow-right ms-2"></i>
                   </button>
                </div>
             <?php else: ?>
                <div class="alert alert-warning small rounded-4 p-3 mb-0 border-0">
                   <i class="bi bi-info-circle me-1"></i> Verify your <strong>Phone Number</strong> to proceed to document upload.
                </div>
             <?php endif; ?>
          </div>

          <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
             <h5 class="fw-bold mb-4"><i class="bi bi-credit-card text-pink me-2"></i>UPI Payout Setup</h5>
             <form id="payoutForm">
                <div class="bg-soft-pink p-3 rounded-3 mb-4">
                   <p class="small mb-0"><i class="bi bi-info-circle me-1"></i> We only support payouts via UPI for faster processing. Enter your UPI ID below.</p>
                </div>
                
                <div class="mb-4">
                   <label class="form-label small fw-bold text-muted text-uppercase">UPI ID</label>
                   <div class="input-group input-group-lg">
                      <span class="input-group-text bg-white border-end-0 rounded-start-3"><i class="bi bi-wallet2 text-pink"></i></span>
                      <input type="text" name="upi_id" id="upi_id" class="form-control border-start-0 rounded-end-3 fs-6" value="<?= esc($user->upi_id) ?>" placeholder="yourname@upi" required>
                   </div>
                </div>

                <!-- Verification Feedback -->
                <div id="upiFeedback" class="d-none mb-4 p-3 rounded-3 border">
                   <div class="d-flex align-items-center gap-3">
                      <div class="bg-success-soft text-success rounded-circle p-2">
                         <i class="bi bi-check-lg"></i>
                      </div>
                      <div>
                         <div class="small text-muted text-uppercase fw-bold letter-spacing-sm">Verified Account Name</div>
                         <div class="fw-bold fs-5" id="verifiedNameDisplay"><?= esc($user->bank_name ?: '-') ?></div>
                      </div>
                   </div>
                </div>

                <button type="submit" class="btn btn-pink w-100 py-3 rounded-pill fw-bold" id="verifyUpiBtn">
                   <span id="upiSpin" class="spinner-border spinner-border-sm d-none me-2"></span>
                   <?= $user->upi_id ? 'Update UPI' : 'Verify & Save UPI' ?>
                </button>
             </form>
          </div>
       </div>

       <!-- Right Side: KYC Documents -->
       <div class="col-lg-6" id="kycSection" style="<?= (empty($documents) && !($user->phone_verified)) ? 'opacity: 0.5; filter: grayscale(1); pointer-events: none;' : '' ?>">
          <div class="card border-0 shadow-sm rounded-4 p-4 min-vh-50 h-100 position-relative">
             <?php if(empty($documents) && !($user->phone_verified)): ?>
             <div class="position-absolute top-50 start-50 translate-middle text-center" style="z-index: 10;">
                <i class="bi bi-lock-fill display-4 text-muted opacity-50"></i>
                <div class="text-muted fw-bold">Unlock after Verification</div>
             </div>
             <?php endif; ?>

             <div class="<?= (empty($documents) && !($user->phone_verified)) ? 'opacity-25' : '' ?>">
                <h5 class="fw-bold mb-4"><i class="bi bi-file-earmark-medical text-pink me-2"></i>KYC Documents</h5>
                
                <?php 
                  $isPending = false;
                  foreach($documents as $d) if($d->verified_status === 'pending') $isPending = true;
                ?>

                <?php if($isPending): ?>
                <div class="alert alert-warning border-0 rounded-4 p-3 mb-4 d-flex align-items-center">
                   <span class="pulse-yellow me-3"></span>
                   <div>
                      <h6 class="fw-bold mb-0">KYC is under verification</h6>
                      <p class="small mb-0">Our team is reviewing your documents. You'll be notified soon.</p>
                   </div>
                </div>
                <?php endif; ?>

                <div class="bg-soft-pink p-3 rounded-3 mb-4">
                   <p class="small mb-0">Upload a clear photo of your <strong>Aadhaar, PAN, or Passport</strong>. Max file size: 2MB (JPG, PNG, PDF).</p>
                </div>

                <!-- Document Upload Form -->
                <form id="kycForm" class="mb-5">
                   <div class="row g-2">
                      <div class="col-md-5">
                         <select name="document_type" class="form-select rounded-3" required>
                            <option value="aadhaar">Aadhaar Card</option>
                            <option value="pan">PAN Card</option>
                            <option value="passport">Passport</option>
                            <option value="gst">GST Certificate</option>
                            <option value="portfolio">Portfolio / Certs</option>
                         </select>
                      </div>
                      <div class="col-md-4">
                         <input type="file" name="document_file" class="form-control rounded-3" accept="image/*,.pdf" required>
                      </div>
                      <div class="col-md-3">
                         <button type="submit" class="btn btn-pink w-100 rounded-3 text-nowrap">
                            <i class="bi bi-upload me-1"></i> Upload
                         </button>
                      </div>
                   </div>
                </form>

                <!-- Uploaded Documents List -->
                <h6 class="fw-bold small text-uppercase mb-3">Recently Uploaded</h6>
                <div class="list-group list-group-flush">
                   <?php if(empty($documents)): ?>
                      <div class="text-center py-4 text-muted small">No documents uploaded yet.</div>
                   <?php endif; ?>
                   <?php foreach($documents as $doc): ?>
                      <div class="list-group-item px-0 py-3 bg-transparent d-flex align-items-center justify-content-between">
                         <div class="d-flex align-items-center gap-3">
                            <div class="bg-white shadow-sm rounded-3 p-2 border">
                               <i class="bi bi-file-earmark-text fs-4 text-pink"></i>
                            </div>
                            <div>
                               <div class="fw-bold small text-capitalize"><?= $doc->document_type ?></div>
                               <div class="text-muted" style="font-size: 0.65rem;"><?= date('d M Y, h:i A', strtotime($doc->created_at)) ?></div>
                            </div>
                         </div>
                         <div>
                            <?php 
                              $statusClass = 'text-warning'; 
                              if($doc->verified_status === 'verified') $statusClass = 'text-success';
                              if($doc->verified_status === 'rejected') $statusClass = 'text-danger';
                            ?>
                            <span class="small fw-bold <?= $statusClass ?> text-uppercase" style="font-size: 0.65rem;">
                               <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> <?= $doc->verified_status ?>
                            </span>
                         </div>
                      </div>
                   <?php endforeach; ?>
                </div>

                <!-- ══ VERIFICATION STATUS/ACTION (Inside Documents Card) ════ -->
                <div class="mt-5 pt-4 border-top text-center">
                   <?php if($user->role == 2 && $user->provider_verification_status === 'pending'): ?>
                      <div class="py-2">
                         <div class="bg-warning-soft text-warning rounded-circle p-3 d-inline-block mb-3">
                            <i class="bi bi-clock-history fs-2"></i>
                         </div>
                         <?php if ($user->provider_verification_message): ?>
                            <div class="alert alert-danger border-0 rounded-4 px-4 py-3 mb-4 text-start">
                               <h6 class="fw-bold mb-2"><i class="bi bi-bell-fill me-2"></i> Action Required from Admin</h6>
                               <p class="mb-0 small italic">"<?= esc($user->provider_verification_message) ?>"</p>
                            </div>
                            <h5 class="fw-bold">Update & Resubmit</h5>
                            <p class="text-muted small">Please address the feedback above by corecting your profile or uploading new documents, then click below.</p>
                            <button class="btn btn-danger btn-lg rounded-pill px-5 fw-bold shadow-sm" id="btnMainSubmit" onclick="submitForVerification()">
                               Verify KYC & Resubmit
                            </button>
                         <?php else: ?>
                            <h5 class="fw-bold">Verification Pending</h5>
                            <p class="text-muted small">Your application is currently being reviewed by our team. You already have access to the Provider Dashboard to start setting up your profile.</p>
                            <a href="<?= base_url('provider/dashboard') ?>" class="btn btn-pink rounded-pill px-4">Go to Dashboard</a>
                         <?php endif; ?>
                      </div>
                   <?php elseif($user->role == 2 && $user->provider_verification_status === 'approved'): ?>
                      <div class="py-2">
                         <div class="bg-success-soft text-success rounded-circle p-3 d-inline-block mb-3">
                            <i class="bi bi-patch-check-fill fs-2"></i>
                         </div>
                         <h5 class="fw-bold">Profile Verified</h5>
                         <p class="text-muted small">Congratulations! Your provider profile is fully verified. You can now publish classes and receive bookings.</p>
                         <a href="<?= base_url('provider/dashboard') ?>" class="btn btn-success rounded-pill px-4">Go to Dashboard</a>
                      </div>
                   <?php elseif($user->provider_verification_status === 'rejected'): ?>
                      <div class="py-2">
                         <div class="bg-danger-soft text-danger rounded-circle p-3 d-inline-block mb-3">
                            <i class="bi bi-exclamation-triangle-fill fs-2"></i>
                         </div>
                         <h5 class="fw-bold">Verification Rejected</h5>
                         <div class="alert alert-danger border-0 rounded-4 px-4 py-3 mb-4 text-start">
                            <h6 class="fw-bold mb-1">Reason for Rejection:</h6>
                            <p class="mb-0 small italic">"<?= esc($user->provider_verification_message ?: 'Please review your profile and docs.') ?>"</p>
                         </div>
                         <p class="text-muted small mb-4">You can update your information and resubmit for another review.</p>
                         <button class="btn btn-pink btn-lg rounded-pill px-5 fw-bold shadow-sm" id="btnMainSubmit" onclick="submitForVerification()">Verify KYC & Resubmit</button>
                      </div>
                   <?php else: ?>
                      <div class="py-2">
                         <h5 class="fw-bold mb-3">Verify KYC</h5>
                         <p class="small text-muted mb-4">Once you have verified your phone and uploaded your KYC documents, click the button below to submit your profile for admin verification.</p>
                         <button class="btn btn-pink btn-lg rounded-pill px-5 fw-bold shadow-sm" id="btnMainSubmit" onclick="submitForVerification()" 
                            <?= (!$user->phone_verified || empty($documents)) ? 'disabled' : '' ?>>
                            Verify KYC & Submit
                         </button>
                         <?php if(!$user->phone_verified || empty($documents)): ?>
                            <p class="text-danger small mt-2 mb-0"><i class="bi bi-info-circle me-1"></i> Phone verification and at least one document required.</p>
                         <?php endif; ?>
                      </div>
                   <?php endif; ?>
                </div>
              </div>
           </div>
        </div>

     </div>
     


  </div>
</section>


<style>
.bg-success-soft { background: rgba(46, 204, 113, 0.1); }
.bg-warning-soft { background: rgba(241, 196, 15, 0.1); }
.bg-danger-soft { background: rgba(231, 76, 60, 0.1); }
.bg-soft-pink { background: rgba(255, 104, 180, 0.1); }
.letter-spacing-lg { letter-spacing: 0.4rem; }
.min-vh-50 { min-height: 50vh; }
.pulse-yellow {
  width: 12px;
  height: 12px;
  background: #f1c40f;
  border-radius: 50%;
  display: inline-block;
  box-shadow: 0 0 0 0 rgba(241, 196, 15, 0.7);
  animation: pulse-yellow 2s infinite;
}
@keyframes pulse-yellow {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(241, 196, 15, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(241, 196, 15, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(241, 196, 15, 0); }
}
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>

  // Unlock KYC Logic
  window.unlockKyc = async function() {
     const btn = document.getElementById('btnProceedKyc');
     btn.disabled = true;
     btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Unlocking...';

     try {
        const section = document.getElementById('kycSection');
        section.style.opacity = '1';
        section.style.filter = 'none';
        section.style.pointerEvents = 'auto';
        
        const lockOverlay = section.querySelector('.translate-middle');
        if(lockOverlay) lockOverlay.remove();
        section.querySelector('.opacity-25').classList.remove('opacity-25');
        
        document.getElementById('kycUnlockBtnBox').innerHTML = '<div class="alert alert-success small rounded-4 p-3 mb-0 border-0 shadow-sm"><i class="bi bi-check-circle-fill me-1"></i> Form Unlocked</div>';
        section.scrollIntoView({ behavior: 'smooth', block: 'center' });
     } catch(e) { 
        console.error(e); 
        btn.disabled = false;
        btn.innerHTML = 'Proceed to KYC <i class="bi bi-arrow-right ms-2"></i>';
     }
  };

  // Temporary workaround: Mark verified directly
  window.verifyPhoneDirectly = async function() {
     try {
        const res = await fetch('<?= base_url('provider/api/verifyphone/mark-verified') ?>', { method: 'POST' });
        const json = await res.json();
        if(json.success) {
           // Success! Update UI without reload
           const container = document.querySelector('.border-warning');
           if(container) {
              container.classList.replace('border-warning', 'border-success');
              const actionDiv = container.querySelector('div:last-child');
              actionDiv.innerHTML = '<span class="badge bg-success-soft text-success rounded-pill px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> Verified</span>';
           }
           
           // Auto-unlock KYC section
           unlockKyc();
           
           // Update main submit button if possible
           const mainBtn = document.getElementById('btnMainSubmit');
           if(mainBtn) {
               // We still need at least one document, so don't fully enable unless docs exist
               // But for now, just show the message
           }

           alert(json.message);
        } else {
           alert(json.message);
        }
     } catch(e) {
        console.error(e);
        alert('Error verifying phone.');
     }
  };


  // Payout Form (UPI Verification)
  const payoutForm = document.getElementById('payoutForm');
  const upiBtn = document.getElementById('verifyUpiBtn');
  const upiSpin = document.getElementById('upiSpin');
  const upiFeedback = document.getElementById('upiFeedback');
  const nameDisplay = document.getElementById('verifiedNameDisplay');

  payoutForm.addEventListener('submit', async function(e){
    e.preventDefault();
    const upiId = document.getElementById('upi_id').value.trim();
    if(!upiId || !upiId.includes('@')) return alert('Please enter a valid UPI ID (e.g. name@bank)');

    upiBtn.disabled = true;
    upiSpin.classList.remove('d-none');
    upiFeedback.classList.add('d-none');
    
    try {
      const formData = new FormData(this);
      const res = await fetch('<?= base_url('provider/api/payout/update') ?>', { method: 'POST', body: formData });
      const json = await res.json();
      
      if(json.success) {
         // Success! Show the verified name
         nameDisplay.innerText = json.verified_name;
         upiFeedback.classList.remove('d-none');
         upiFeedback.classList.add('animate__animated', 'animate__fadeIn');
         upiBtn.innerHTML = '<i class="bi bi-patch-check-fill me-2"></i> Verified & Saved';
         upiBtn.classList.replace('btn-pink', 'btn-success');
         
         setTimeout(() => {
            alert('UPI Details Verified: ' + json.verified_name);
            window.location.reload();
         }, 1500);
      } else {
         if (json.errors && json.errors.upi_id) alert(json.errors.upi_id);
         else alert(json.message || 'Verification failed. Please check your UPI ID.');
      }
    } catch(e) { 
       console.error(e); 
       alert('Server error during verification. Please try again later.');
    } finally {
       upiBtn.disabled = false;
       upiSpin.classList.add('d-none');
    }
  });

  // KYC Form
  document.getElementById('kycForm').addEventListener('submit', async function(e){
    e.preventDefault();
    const formData = new FormData(this);
    
    try {
      const res = await fetch('<?= base_url('provider/api/kyc/upload') ?>', { method: 'POST', body: formData });
      const json = await res.json();
      alert(json.message);
      if(json.success) window.location.reload();
    } catch(e) { console.error(e); }
  });

  // Submit for Admin Verification
  window.submitForVerification = async function() {
     const btn = document.getElementById('btnMainSubmit');
     const originalHtml = btn ? btn.innerHTML : 'Submit';
     
     if(btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Submitting...';
     }

     try {
        const res = await fetch('<?= base_url('provider/api/verify/submit') ?>', { 
           method: 'POST',
           headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();
        
        if(json.success) {
           alert(json.message);
           if(json.redirect) window.location.href = json.redirect;
           else window.location.reload();
        } else {
           alert(json.message);
           if(btn) {
              btn.disabled = false;
              btn.innerHTML = originalHtml;
           }
        }
     } catch(e) {
        console.error(e);
        alert('Server error. Please try again later.');
        if(btn) {
           btn.disabled = false;
           btn.innerHTML = originalHtml;
        }
     }
  };

</script>
<?= $this->endSection() ?>
