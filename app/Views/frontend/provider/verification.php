<?= $this->extend('frontend/layout/provider_base') ?>

<?= $this->section('content') ?>

<div class="dashboard-intro mb-4">
    <h1 class="dashboard-title">Verification & KYC</h1>
    <p class="dashboard-subtitle">Complete your profile to build trust with parents and enable payouts.</p>
</div>

<div class="row g-4">
       <!-- Left Side: Phone & Email -->
       <div class="col-lg-6">
          <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
             <h5 class="fw-bold mb-4"><i class="bi bi-shield-check text-accent me-2"></i>Contact Verification</h5>
             

             <!-- Phone Verification -->
             <div class="p-4 bg-light rounded-4 border-start border-4 <?= $user->phone_verified ? 'border-success' : 'border-warning' ?> mb-4 shadow-sm">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
                   <div class="small text-muted text-uppercase fw-bold letter-spacing-sm">Mobile number</div>
                   <div id="phoneActionBox">
                      <?php if($user->phone_verified): ?>
                         <span class="badge bg-success-soft text-success rounded-pill px-3 py-2 fw-bold"><i class="bi bi-patch-check-fill me-1"></i> Verified</span>
                      <?php else: ?>
                         <span class="badge bg-warning-soft text-warning rounded-pill px-3 py-2 small fw-bold">Unverified</span>
                      <?php endif; ?>
                   </div>
                </div>
                
                <div id="phoneViewBox" class="<?= $user->phone_verified ? '' : 'd-none' ?>">
                   <div class="fw-bold fs-5 text-dark" id="userPhoneDisplay"><?= esc($user->phone ?: 'Not set') ?></div>
                </div>
                
                <div id="phoneEditBox" class="<?= $user->phone_verified ? 'd-none' : '' ?>">
                   <div class="input-group mb-3">
                      <input type="text" id="phoneInput" class="form-control border-end-0 bg-white" style="border-radius: 8px 0 0 8px;" value="<?= esc($user->phone) ?>" placeholder="Enter 10 digit mobile">
                      <button class="btn-primary-provider" style="border-radius: 0 8px 8px 0;" onclick="submitUpdatePhone()">Verify</button>
                   </div>
                </div>
             </div>

             <!-- Email Verification -->
             <div class="p-4 bg-light rounded-4 border-start border-4 <?= ($user->email_verified) ? 'border-success' : 'border-warning' ?> mb-4 shadow-sm">
                <div class="d-flex flex-wrap align-items-center justify-content-between mb-3 gap-2">
                   <div class="small text-muted text-uppercase fw-bold letter-spacing-sm">Email</div>
                   <div id="emailActionBox">
                      <?php if($user->email_verified): ?>
                         <span class="badge bg-success-soft text-success rounded-pill px-3 py-2 fw-bold"><i class="bi bi-patch-check-fill me-1"></i> Verified</span>
                      <?php else: ?>
                         <span class="badge bg-warning-soft text-warning rounded-pill px-3 py-2 small fw-bold">Unverified</span>
                      <?php endif; ?>
                   </div>
                </div>

                <div id="emailViewBox" class="<?= ($user->email_verified) ? '' : 'd-none' ?>">
                   <div class="fw-bold fs-5 text-dark" id="userEmailDisplay"><?= esc($user->email ?: 'Not set') ?></div>
                </div>

                <div id="emailEditBox" class="<?= ($user->email_verified) ? 'd-none' : '' ?>">
                   <div class="input-group mb-3">
                      <input type="email" id="emailInput" class="form-control border-end-0 bg-white" style="border-radius: 8px 0 0 8px;" value="<?= esc($user->email) ?>" placeholder="Enter email address" <?= $user->email_verified ? 'disabled' : '' ?>>
                      <?php if(!$user->email_verified): ?>
                         <button class="btn-primary-provider" style="border-radius: 0 8px 8px 0;" id="btnSendEmailOtp" onclick="submitUpdateEmail()">Verify</button>
                      <?php endif; ?>
                   </div>
                   
                   <!-- OTP Input for Email -->
                   <div id="emailOtpBox" class="mt-3 p-3 bg-white rounded-3 border d-none shadow-sm">
                      <label class="form-label small fw-bold text-muted text-uppercase mb-2 text-center d-block">Enter OTP sent to your email</label>
                      <input type="text" id="emailOtpInput" class="form-control letter-spacing-lg fw-bold text-center mb-3" maxlength="6" placeholder="000000" style="font-size: 1.25rem; height: 50px; border-radius: 8px; background: #f8f9fa;">
                      <button class="btn-primary-provider w-100" onclick="verifyEmailOtp()">
                         Verify OTP <i class="bi bi-arrow-right-short ms-1"></i>
                      </button>
                      <div class="mt-3 text-center">
                         <a href="javascript:void(0)" class="small text-decoration-none text-accent fw-bold" onclick="submitUpdateEmail()">Didn't receive? Resend OTP</a>
                      </div>
                   </div>
                </div>
             </div>

             <!-- KYC section is now unlocked by default -->

          </div>

          <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
             <div class="d-flex flex-wrap align-items-center justify-content-between mb-4 gap-2">
                <h5 class="fw-bold mb-0"><i class="bi bi-credit-card text-accent me-2"></i>UPI Payout Setup</h5>
                <?php $hasUpi = !empty($user->upi_id) && !empty($user->bank_name); ?>
                <div id="upiTitleVerifiedBadge">
                   <?php if($hasUpi): ?>
                      <span class="badge bg-success-soft text-success rounded-pill px-3 py-2"><i class="bi bi-patch-check-fill me-1"></i> Verified</span>
                   <?php endif; ?>
                </div>
             </div>
             <form id="payoutForm">
                <div class="bg-soft-accent p-3 rounded-3 mb-4">
                   <p class="small mb-0"><i class="bi bi-info-circle me-1"></i> We only support payouts via UPI.</p>
                </div>
                
                <div class="mb-4">
                   <label class="form-label small fw-bold text-muted text-uppercase">UPI ID</label>
                   <?php $hasUpi = !empty($user->upi_id) && !empty($user->bank_name); ?>
                   <div class="input-group">
                      <span class="input-group-text bg-white border-end-0" style="border-radius: 8px 0 0 8px;"><i class="bi bi-wallet2 text-accent"></i></span>
                      <input type="text" name="upi_id" id="upi_id" class="form-control border-start-0 <?= $hasUpi ? 'border-end-0' : '' ?> fs-6" style="<?= $hasUpi ? '' : 'border-radius: 0 8px 8px 0;' ?>" value="<?= esc($user->upi_id) ?>" placeholder="yourname@upi" required>
                      <span id="upiVerifiedTick" class="input-group-text bg-white border-start-0 <?= $hasUpi ? '' : 'd-none' ?>" style="border-radius: 0 8px 8px 0;" data-bs-toggle="tooltip" data-bs-placement="top" title="UPI Verified">
                         <i class="bi bi-patch-check-fill text-success fs-5"></i>
                      </span>
                   </div>
                </div>


                <button type="submit" class="btn-primary-provider w-100" id="verifyUpiBtn">
                   <span id="upiSpin" class="spinner-border spinner-border-sm d-none me-2"></span>
                   <span id="upiBtnText"><?= $hasUpi ? '<i class="bi bi-arrow-repeat me-2"></i> Update UPI' : 'Verify & Save UPI' ?></span>
                </button>
             </form>
          </div>
       </div>

       <!-- Right Side: KYC Documents -->
       <div class="col-lg-6" id="kycSection">
          <div class="card border-0 shadow-sm rounded-4 p-4 min-vh-50 h-100 position-relative">


             <div>
                <h5 class="fw-bold mb-4"><i class="bi bi-file-earmark-medical text-accent me-2"></i>KYC Documents</h5>
                
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

                <div class="bg-soft-accent p-3 rounded-3 mb-4">
                   <p class="small mb-0">Upload a clear photo of your <strong>Aadhaar, PAN, or Passport</strong>. Max file size: 2MB (JPG, PNG, WEBP, PDF).</p>
                </div>

                <!-- Document Upload Form -->
                 <form id="kycForm" class="mb-5 bg-white border border-dashed rounded-4 p-4">
                    <div class="mb-4 text-start">
                       <label class="small fw-bold text-muted text-uppercase mb-2 d-block">1. Choose Document Type</label>
                       <select name="document_type" class="form-select rounded-3 border-secondary-subtle" required id="kycType">
                          <option value="aadhaar">Aadhaar Card</option>
                          <option value="pan">PAN Card</option>
                          <option value="passport">Passport</option>
                          <option value="gst">GST Certificate</option>
                          <option value="portfolio">Portfolio / Certs</option>
                          <option value="other">Other Supporting Info</option>
                       </select>
                    </div>
                    <div class="mb-3 text-start">
                       <label class="small fw-bold text-muted text-uppercase mb-2 d-block">2. Select File (Max 2MB)</label>
                       <input type="file" name="document_file" id="kycInput" class="form-control rounded-3 border-secondary-subtle" accept="image/*,.pdf" required>
                    </div>

                    <!-- PREVIEW AREA (Hidden by default, used as auto-uploading indicator) -->
                    <div id="kycPreviewer" class="mt-4 p-3 bg-light rounded-4 d-none border shadow-sm">
                       <div class="d-flex align-items-center gap-3">
                          <div id="kycPreviewMedia" class="rounded-3 border overflow-hidden d-flex align-items-center justify-content-center bg-white shadow-sm" style="width: 64px; height: 64px; min-width: 64px;">
                             <!-- Image or PDF icon will go here -->
                          </div>
                          <div class="flex-grow-1 text-start overflow-hidden">
                             <h6 class="mb-0 fw-bold small text-truncate" id="kycFileName">document.pdf</h6>
                             <p class="mb-0 text-muted small" id="kycFileSize">Loading...</p>
                          </div>
                          <div class="text-center px-3" id="kycUploadingState">
                             <div class="spinner-border spinner-border-sm text-accent mb-1" role="status"></div>
                             <div class="small fw-bold text-accent" style="font-size: 0.65rem;">UPLOADING...</div>
                          </div>
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
                       <div class="list-group-item px-0 py-3 bg-transparent">
                          <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                             <div class="d-flex align-items-center gap-3">
                                <?php 
                                   $ext = pathinfo($doc->file_path, PATHINFO_EXTENSION);
                                   $isImg = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                ?>
                                <div class="bg-white shadow-sm rounded-3 overflow-hidden border d-flex align-items-center justify-content-center" style="width: 54px; height: 54px; min-width: 54px;">
                                   <?php if($isImg): ?>
                                      <a href="<?= base_url($doc->file_path) ?>" target="_blank">
                                        <img src="<?= base_url($doc->file_path) ?>" class="w-100 h-100 object-fit-cover" alt="KYC">
                                      </a>
                                   <?php else: ?>
                                      <a href="<?= base_url($doc->file_path) ?>" target="_blank" class="text-decoration-none">
                                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-3"></i>
                                      </a>
                                   <?php endif; ?>
                                </div>
                                <div>
                                   <div class="fw-bold text-dark text-capitalize"><?= esc($doc->document_type) ?></div>
                                   <div class="text-muted" style="font-size: 0.75rem;"><?= date('d M Y, h:i A', strtotime($doc->created_at)) ?></div>
                                </div>
                             </div>
                             <div class="d-flex align-items-center gap-3 ms-auto ms-sm-0">
                                <?php 
                                  $statusClass = 'text-warning'; 
                                  if($doc->verified_status === 'verified') $statusClass = 'text-success';
                                  if($doc->verified_status === 'rejected') $statusClass = 'text-danger';
                                ?>
                                <span class="badge rounded-pill border <?= str_replace('text-', 'border-', $statusClass) ?> <?= $statusClass ?> bg-white small px-2 py-1" style="font-size: 0.65rem;">
                                   <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> <?= strtoupper($doc->verified_status) ?>
                                </span>

                                <?php if($doc->verified_status === 'pending'): ?>
                                   <button class="btn btn-light btn-sm text-danger border rounded-circle p-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" onclick="deleteKyc(<?= $doc->id ?>)" data-bs-toggle="tooltip" title="Delete Document">
                                      <i class="bi bi-trash3"></i>
                                   </button>
                                <?php endif; ?>
                             </div>
                          </div>
                       </div>
                    <?php endforeach; ?>
                </div>

                <!-- ══ VERIFICATION STATUS/ACTION (Inside Documents Card) ════ -->
                <div class="mt-3 pt-3 border-top text-center">
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
                            <button class="btn-danger-provider px-5" id="btnMainSubmit" onclick="submitForVerification()">
                               Resubmit Profile
                            </button>
                         <?php else: ?>
                            <h5 class="fw-bold">Verification Pending</h5>
                            <p class="text-muted small">Your application is currently being reviewed by our team. You already have access to the Provider Dashboard to start setting up your profile.</p>
                            <a href="<?= base_url('provider/dashboard') ?>" class="btn btn-accent rounded-pill px-4">Go to Dashboard</a>
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
                         <button class="btn-primary-provider px-5 w-100 w-md-auto" id="btnMainSubmit" onclick="submitForVerification()">Resubmit Profile</button>
                      </div>
                   <?php else: ?>
                      <div class="py-2">
                         <button class="btn-primary-provider px-5 w-100 w-md-auto" id="btnMainSubmit" onclick="submitForVerification()" 
                            <?= (empty($documents)) ? 'disabled' : '' ?>>
                            Submit Profile
                         </button>
                         <?php if(empty($documents)): ?>
                            <p class="text-danger small mt-2 mb-0"><i class="bi bi-info-circle me-1"></i> At least one document required.</p>
                         <?php endif; ?>
                      </div>
                   <?php endif; ?>
                </div>
              </div>
           </div>
        </div>

   </div>


<style>
.bg-success-soft { background: rgba(46, 204, 113, 0.1); }
.bg-warning-soft { background: rgba(241, 196, 15, 0.1); }
.bg-danger-soft { background: rgba(231, 76, 60, 0.1); }
.bg-soft-accent { background: rgba(249, 160, 94, 0.1); }
.letter-spacing-lg { letter-spacing: 0.4rem; }
.min-vh-50 { min-height: 50vh; }
@media (max-width: 768px) {
    #btnMainSubmit {
        width: 100% !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
    }
}
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
           }
           
           const actionBox = document.getElementById('phoneActionBox');
           if(actionBox) {
              actionBox.innerHTML = '<span class="badge bg-success-soft text-success rounded-pill px-3 py-2 fw-bold"><i class="bi bi-patch-check-fill me-1"></i> Verified</span>';
           }

           // Ensure the phone number is displayed
           const phoneDisplay = document.getElementById('userPhoneDisplay');
           if(phoneDisplay && json.phone) {
              phoneDisplay.innerText = json.phone;
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


  // Tooltip Initialization
  const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  const payoutForm = document.getElementById('payoutForm');
  const upiBtn = document.getElementById('verifyUpiBtn');
  const upiSpin = document.getElementById('upiSpin');
  const upiInput = document.getElementById('upi_id');
  const upiTick = document.getElementById('upiVerifiedTick');

  if(upiInput) {
      upiInput.addEventListener('input', () => {
          if(upiTick) {
              upiTick.classList.add('d-none');
              upiInput.classList.add('rounded-end-3');
              upiInput.classList.remove('border-end-0');
          }
      });
  }

  payoutForm.addEventListener('submit', async function(e){
    e.preventDefault();
    const upiId = document.getElementById('upi_id').value.trim();
    if(!upiId || !upiId.includes('@')) return alert('Please enter a valid UPI ID (e.g. name@bank)');

    upiBtn.disabled = true;
    upiSpin.classList.remove('d-none');
    
    try {
      const formData = new FormData(this);
      const res = await fetch('<?= base_url('provider/api/payout/update') ?>', { method: 'POST', body: formData });
      const json = await res.json();
      
      if(json.success) {
         // Success! Show the verified tick
         if(upiTick) {
            upiTick.classList.remove('d-none');
            upiInput.classList.remove('rounded-end-3');
            upiInput.classList.add('border-end-0');
            // Re-init tooltip for the new element if needed (or just ensure it works)
         }
         
         upiBtn.innerHTML = '<i class="bi bi-arrow-repeat me-2"></i> Update UPI';
         
         const titleBadge = document.getElementById('upiTitleVerifiedBadge');
         if(titleBadge) {
            titleBadge.innerHTML = '<span class="badge bg-success-soft text-success rounded-pill px-3 py-2"><i class="bi bi-patch-check-fill me-1"></i> Verified</span>';
         }
         
         setTimeout(() => {
            alert('UPI Details Verified!');
            window.location.reload();
         }, 1000);
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

  // KYC Form Preview Logic
  const kycInput = document.getElementById('kycInput');
  const kycPreviewer = document.getElementById('kycPreviewer');
  
  if(kycInput) {
    kycInput.addEventListener('change', async function(e) {
       const file = e.target.files[0];
       if(!file) return cancelKycSelection();
       
       // 1. Update Preview Info
       document.getElementById('kycFileName').textContent = file.name;
       const sizeMB = (file.size / (1024 * 1024)).toFixed(2);
       document.getElementById('kycFileSize').textContent = `${sizeMB} MB`;
       
       // 2. Preview Media Icon
       const mediaBox = document.getElementById('kycPreviewMedia');
       mediaBox.innerHTML = ''; 
       if(file.type.startsWith('image/')) {
          const reader = new FileReader();
          reader.onload = function(re) {
             const img = document.createElement('img');
             img.src = re.target.result;
             img.className = 'w-100 h-100 object-fit-cover';
             mediaBox.appendChild(img);
          };
          reader.readAsDataURL(file);
       } else if(file.type === 'application/pdf') {
          mediaBox.innerHTML = '<i class="bi bi-file-earmark-pdf-fill text-danger fs-1"></i>';
       } else {
          mediaBox.innerHTML = '<i class="bi bi-file-earmark-text text-muted fs-1"></i>';
       }
       
       kycPreviewer.classList.remove('d-none');

       // 3. AUTO UPLOAD IMMEDIATELY
       const formData = new FormData(document.getElementById('kycForm'));
       kycInput.disabled = true; // Prevent double trigger
       try {
          const res = await fetch('<?= base_url('provider/api/kyc/upload') ?>', { method: 'POST', body: formData });
          const json = await res.json();
           if(json.success) {
              location.reload();
           } else {
              // Show specific error if provided, otherwise generic
              alert(json.message || 'Upload failed. Ensure file is under 2MB and in a valid format (JPG, PNG, WEBP, PDF).');
              cancelKycSelection();
              kycInput.disabled = false;
           }
       } catch(err) {
          console.error(err);
          alert('Server error during upload.');
          cancelKycSelection();
          kycInput.disabled = false;
       }
    });
  }

  window.cancelKycSelection = function() {
     const input = document.getElementById('kycInput');
     if(input) input.value = '';
     const previewer = document.getElementById('kycPreviewer');
     if(previewer) previewer.classList.add('d-none');
  };

  // Manual KYC Form Submission (Now handled by auto-upload)
  document.getElementById('kycForm').addEventListener('submit', e => e.preventDefault());

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

  // Delete KYC Document
  window.deleteKyc = async function(docId) {
     if(!confirm('Are you sure you want to delete this document? This will reset your verification status if no documents remain.')) return;
     
     try {
        const res = await fetch('<?= base_url('provider/api/kyc/delete') ?>/' + docId, { 
           method: 'POST',
           headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const json = await res.json();
        
        if(json.success) {
           alert(json.message);
           location.reload();
        } else {
           alert(json.message);
        }
     } catch(e) {
        console.error(e);
        alert('Error deleting document.');
     }
  };

  // Update Phone
  window.submitUpdatePhone = async function() {
     const phoneBox = document.getElementById('phoneInput');
     if(!phoneBox) return;
     const phone = phoneBox.value.trim();
     if(!phone || phone.length !== 10 || isNaN(phone)) {
        return alert('Please enter a valid 10-digit mobile number.');
     }

     try {
        const formData = new FormData();
        formData.append('phone', phone);
        const res = await fetch('<?= base_url('provider/api/verification/phone/update') ?>', { method: 'POST', body: formData });
        const json = await res.json();
        
        if(json.success) {
           alert(json.message);
           location.reload(); 
        } else {
           alert(json.message);
        }
     } catch(e) {
        console.error(e);
        alert('Error updating phone.');
     }
  };

  // Update Email
  window.submitUpdateEmail = async function() {
     const emailBox = document.getElementById('emailInput');
     const btn = event.currentTarget;
     if(!emailBox) return;
     const email = emailBox.value.trim();
     if(!email || !email.includes('@')) {
        return alert('Please enter a valid email address.');
     }

     const originalHtml = btn.innerHTML;
     btn.disabled = true;
     btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sending...';

     try {
        const formData = new FormData();
        formData.append('email', email);
        const res = await fetch('<?= base_url('provider/api/verification/email/update') ?>', { method: 'POST', body: formData });
        const json = await res.json();
        
        if(json.success) {
           if (json.otp_sent) {
              alert(json.message);
              const otpBox = document.getElementById('emailOtpBox');
              otpBox.classList.remove('d-none');
              document.getElementById('emailInput').disabled = true;
              btn.classList.add('d-none');
              
              // Focus the OTP input
              const otpInput = document.getElementById('emailOtpInput');
              otpInput.value = '';
              setTimeout(() => {
                 otpInput.focus();
                 otpInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
              }, 300);
           } else {
              alert(json.message);
              location.reload();
           }
         } else {
           alert(json.message);
           btn.disabled = false;
           btn.innerHTML = originalHtml;
         }
      } catch(e) {
         console.error(e);
         alert('Error sending OTP.');
         btn.disabled = false;
         btn.innerHTML = originalHtml;
      }
   };

   // Verify Email OTP
   window.verifyEmailOtp = async function() {
      const otpBox = document.getElementById('emailOtpInput');
      const otp = otpBox.value.trim();
      if(otp.length !== 6) {
         return alert('Please enter the 6-digit OTP.');
      }

      const btn = event.currentTarget;
      const originalHtml = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

      try {
         const formData = new FormData();
         formData.append('otp', otp);
         const res = await fetch('<?= base_url('provider/api/verification/email/verify-otp') ?>', { method: 'POST', body: formData });
         const json = await res.json();
         
         if(json.success) {
            alert(json.message);
            location.reload();
         } else {
            alert(json.message);
            btn.disabled = false;
            btn.innerHTML = originalHtml;
         }
      } catch(e) {
         console.error(e);
         alert('Error verifying OTP.');
         btn.disabled = false;
         btn.innerHTML = originalHtml;
      }
   };

</script>
<?= $this->endSection() ?>
