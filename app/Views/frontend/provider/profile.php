<?= $this->extend('frontend/layout/provider_base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="mb-5">
    <h1 class="dashboard-title">My Account</h1>
    <p class="dashboard-subtitle" style="margin-bottom:0;">Manage your personal information and security settings</p>
</div>

<!-- ============================================================ -->
<!-- Card 1: Personal Information                                  -->
<!-- ============================================================ -->
<div class="figma-card mb-4">
    <div class="figma-card-header">
        <span class="figma-card-title">Personal Information</span>
        <a href="#" class="figma-edit-link" id="btnEditProfile">
            <i class="bi bi-pencil-square"></i> Edit
        </a>
    </div>
    <div class="figma-card-body">
        <form id="profileForm" action="<?= base_url('profile/updateProfile') ?>" method="POST">
            <?= csrf_field() ?>

            <!-- Full Legal Name -->
            <div class="figma-field-group">
                <label class="figma-field-label">Full Legal Name</label>
                <div class="figma-value-display" id="displayName"><?= esc($user->name) ?></div>
                <input type="text" name="name" class="figma-input d-none" value="<?= esc($user->name) ?>">
                <p class="figma-field-helper d-none" id="helperName">Contact support to change your legal name</p>
            </div>

            <!-- Email Address -->
            <div class="figma-field-group">
                <label class="figma-field-label">Email Address</label>
                <div class="figma-value-display" id="displayEmail"><?= esc($user->email) ?></div>
                <div class="d-none" id="emailEditBox">
                    <div class="d-flex gap-2">
                        <input type="email" id="emailInput" class="figma-input" value="<?= esc($user->email) ?>" style="flex: 1;">
                        <button type="button" class="btn-next py-2 px-4" id="btnVerifyEmail" onclick="profileSendEmailOtp()" style="white-space: nowrap; width: auto; font-size: 0.85rem;">Verify</button>
                    </div>
                </div>
                <!-- Email OTP Box -->
                <div class="mt-2 d-none" id="emailOtpBox">
                    <label class="small fw-bold text-accent mb-1 d-block">Enter 6-digit OTP sent to your email</label>
                    <div class="input-group shadow-sm rounded-8 overflow-hidden">
                        <input type="text" id="emailOtp" class="form-control border-0 bg-light py-2" placeholder="000000" maxlength="6" style="letter-spacing: 0.5rem; font-weight: 800; text-align: center;">
                        <button type="button" class="btn btn-accent px-3" onclick="profileVerifyEmailOtp()">Verify OTP</button>
                    </div>
                </div>
                <p class="figma-field-helper d-none" id="helperEmail">Changing email requires OTP verification</p>
            </div>

            <!-- Phone Number -->
            <div class="figma-field-group">
                <label class="figma-field-label">Phone Number</label>
                <div class="figma-value-display" id="displayPhone"><?= esc($user->phone) ?></div>
                <div class="d-none" id="phoneEditBox">
                    <div class="d-flex gap-2">
                        <div class="input-group flex-nowrap" style="flex: 1;">
                            <span class="input-group-text bg-light border-end-0" style="font-size: 0.9375rem; font-weight: 500;">+91</span>
                            <input type="text" id="phoneInput" class="form-control border-start-0 py-2" value="<?= esc($user->phone) ?>" maxlength="10" style="font-size: 0.9375rem; font-weight: 500; height: auto;">
                        </div>
                        <button type="button" class="btn-next py-2 px-4" id="btnVerifyPhone" onclick="profileSendPhoneOtp()" style="white-space: nowrap; width: auto; font-size: 0.85rem;">Verify</button>
                    </div>
                </div>
                <!-- Phone OTP Box -->
                <div class="mt-2 d-none" id="phoneOtpBox">
                    <label class="small fw-bold text-accent mb-1 d-block">Enter 6-digit OTP sent to WhatsApp</label>
                    <div class="input-group shadow-sm rounded-8 overflow-hidden">
                        <input type="text" id="phoneOtp" class="form-control border-0 bg-light py-2" placeholder="000000" maxlength="6" style="letter-spacing: 0.5rem; font-weight: 800; text-align: center;">
                        <button type="button" class="btn btn-accent px-3" onclick="profileVerifyPhoneOtp()">Verify OTP</button>
                    </div>
                </div>
                <p class="figma-field-helper d-none" id="helperPhone">Changing phone requires OTP verification</p>
            </div>

            <!-- UPI ID -->
            <div class="figma-field-group" style="margin-bottom:0;">
                <label class="figma-field-label">UPI ID</label>
                <div class="figma-value-display" id="displayUpi"><?= esc($user->upi_id ?: 'Not set') ?></div>
                <input type="text" name="upi_id" class="figma-input d-none" value="<?= esc($user->upi_id ?: '') ?>" placeholder="Enter UPI ID">
            </div>

            <!-- Save / Cancel (hidden until edit mode) -->
            <div class="d-none mt-4 pt-3 border-top" id="profileActionGroup">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn-next">Save Changes</button>
                    <button type="button" class="btn-back" id="btnCancelEdit">Cancel</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ============================================================ -->
<!-- Card 2: KYC Status                                           -->
<!-- ============================================================ -->
<div class="figma-card mb-4">
    <div class="figma-card-body">
        <!-- KYC Section Title (no separate header bar) -->
        <div class="d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-shield-check figma-shield-icon"></i>
            <span class="figma-section-title">KYC Status</span>
        </div>

        <!-- Row: Current Status -->
        <div class="figma-status-row">
            <span class="figma-status-label">Current Status</span>
            <?php if($user->provider_verification_status === 'approved'): ?>
                <span class="figma-badge-approved"><i class="bi bi-check-circle-fill"></i> APPROVED</span>
            <?php elseif($user->provider_verification_status === 'rejected'): ?>
                <span class="figma-badge-rejected"><i class="bi bi-x-circle-fill"></i> REJECTED</span>
            <?php else: ?>
                <span class="figma-badge-pending"><i class="bi bi-clock-fill"></i> <?= strtoupper($user->provider_verification_status ?: 'PENDING') ?></span>
            <?php endif; ?>
        </div>

        <!-- Row: Document Type -->
        <div class="figma-status-row">
            <span class="figma-status-label">Document Type</span>
            <?php
                $verifiedDoc = null;
                foreach (($documents ?? []) as $doc) {
                    if ($doc->verified_status === 'verified') { $verifiedDoc = $doc; break; }
                }
                if (!$verifiedDoc && !empty($documents)) $verifiedDoc = end($documents);
                $docTypeName = $verifiedDoc ? ucwords(str_replace('_', ' ', $verifiedDoc->document_type)) : 'Not Uploaded';
            ?>
            <span class="figma-status-value"><?= esc($docTypeName) ?></span>
        </div>

        <!-- Row: UPI Verification -->
        <div class="figma-status-row" style="border-bottom:none; margin-bottom:0;">
            <span class="figma-status-label">UPI Verification</span>
            <?php if($user->upi_id): ?>
                <span class="figma-verified-text"><i class="bi bi-check-circle-fill"></i> Verified</span>
            <?php else: ?>
                <span class="text-muted small">Not Set</span>
            <?php endif; ?>
        </div>

        <?php if ($user->provider_verification_status !== 'approved'): ?>
        <div class="mt-4 pt-3 border-top">
            <a href="<?= base_url('provider/verification') ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                <i class="bi bi-upload me-1"></i> Manage KYC Documents
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>


<!-- ============================================================ -->
<!-- Card 4: Delete Account                                        -->
<!-- ============================================================ -->
<div class="figma-card figma-delete-card mb-4">
    <div class="figma-card-body">
        <h6 class="figma-delete-title">Delete Account</h6>
        <p class="figma-delete-desc">Permanently delete your account and all associated data. This action cannot be undone. All pending settlements must be cleared first.</p>
        <button class="figma-btn-delete-account" id="btnDeleteAccount">
            Request Account Deletion
        </button>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
/* ============================================================
   Figma Account Page - Exact Match Styles
   ============================================================ */

/* Cards */
.figma-card {
    background: #FFFFFF;
    border-radius: 16px;
    border: 1px solid #E5E7EB;
    overflow: hidden;
}

.figma-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.375rem 1.5rem;
    border-bottom: 1px solid #F3F4F6;
}

.figma-card-title {
    font-size: 1.0625rem;
    font-weight: 700;
    color: #111827;
}

.figma-edit-link {
    font-size: 0.875rem;
    font-weight: 600;
    color: #4F46E5;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.35rem;
    transition: opacity 0.2s;
}

.figma-edit-link:hover {
    opacity: 0.75;
    color: #4F46E5;
}

.figma-card-body {
    padding: 1.5rem;
}

/* Form Fields */
.figma-field-group {
    margin-bottom: 1.25rem;
}

.figma-field-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: #374151;
    margin-bottom: 0.5rem;
}

.figma-input {
    width: 100%;
    padding: 0.6875rem 0.875rem;
    border: 1px solid #E5E7EB;
    border-radius: 8px;
    font-size: 0.9375rem;
    color: #111827;
    background-color: #F9FAFB;
    outline: none;
    transition: border-color 0.2s, box-shadow 0.2s;
    font-family: inherit;
}

.figma-input:not([readonly]):not([disabled]) {
    background-color: #FFFFFF;
    border-color: #D1D5DB;
}

.figma-input:not([readonly]):not([disabled]):focus {
    border-color: #4F46E5;
    box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
}

.figma-input[readonly],
.figma-input[disabled] {
    background-color: #F9FAFB;
    color: #374151;
    cursor: default;
}

.figma-field-helper {
    margin-top: 0.375rem;
    margin-bottom: 0;
    font-size: 0.8125rem;
    color: #D97706;
    line-height: 1.4;
}

/* KYC Section Header */
.figma-shield-icon {
    font-size: 1.25rem;
    color: #059669;
}

.figma-phone-icon {
    font-size: 1.1rem;
    color: #4F46E5;
}

.figma-section-title {
    font-size: 1.0625rem;
    font-weight: 700;
    color: #111827;
}

/* KYC Status Rows */
.figma-status-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.875rem 0;
    border-bottom: 1px solid #F3F4F6;
}

.figma-status-label {
    font-size: 0.9rem;
    color: #6B7280;
    font-weight: 400;
}

.figma-status-value {
    font-size: 0.9375rem;
    font-weight: 700;
    color: #111827;
}

.figma-badge-approved {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background-color: #DCFCE7;
    color: #15803D;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.3rem 0.75rem;
    border-radius: 9999px;
    text-transform: uppercase;
    letter-spacing: 0.02em;
}

.figma-badge-pending {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background-color: #FEF3C7;
    color: #B45309;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.3rem 0.75rem;
    border-radius: 9999px;
    text-transform: uppercase;
}

.figma-badge-rejected {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background-color: #FEE2E2;
    color: #B91C1C;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 0.3rem 0.75rem;
    border-radius: 9999px;
    text-transform: uppercase;
}

.figma-verified-text {
    font-size: 0.9rem;
    font-weight: 600;
    color: #16A34A;
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

/* Delete Account Card */
.figma-delete-card {
    background: #FFFFFF;
    border-color: #E5E7EB;
}

.figma-delete-title {
    font-size: 1.0625rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 0.5rem;
}

.figma-delete-desc {
    font-size: 0.875rem;
    color: #6B7280;
    line-height: 1.6;
    margin-bottom: 1.25rem;
}

.figma-btn-delete-account {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    background: transparent;
    border: 1.5px solid #DC2626;
    border-radius: 9999px;
    color: #DC2626;
    font-size: 0.875rem;
    font-weight: 600;
    padding: 0.5rem 1.25rem;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
    font-family: inherit;
}

.figma-btn-delete-account:hover {
    background: #DC2626;
    color: white;
}

.figma-header-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #4B5563;
}

/* NEW: Value display for non-edit mode */
.figma-value-display {
    font-size: 0.9375rem;
    color: #111827;
    font-weight: 500;
    padding: 0.35rem 0;
}

.figma-input.d-none {
    display: none !important;
}

.figma-field-helper.d-none {
    display: none !important;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function() {
    // ── Edit Profile Toggle ──────────────────────────────────────
    const profileForm    = $('#profileForm');
    const displayValues  = $('.figma-value-display');
    const inputs         = $('.figma-input');
    const helpers        = $('.figma-field-helper');
    const actionGroup    = $('#profileActionGroup');
    const btnEdit        = $('#btnEditProfile');

    btnEdit.on('click', function(e) {
        e.preventDefault();
        displayValues.addClass('d-none');
        inputs.removeClass('d-none');
        helpers.removeClass('d-none');
        actionGroup.removeClass('d-none');
        btnEdit.addClass('d-none');
        
        $('#emailEditBox').removeClass('d-none');
        $('#phoneEditBox').removeClass('d-none');
        
        inputs.first().focus();
    });

    // ── OTP Handlers ──────────────────────────────────────────
    window.profileSendEmailOtp = async function() {
        const email = $('#emailInput').val().trim();
        if(!email || !email.includes('@')) return Swal.fire('Error', 'Valid email required', 'error');
        
        const btn = $('#btnVerifyEmail');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        
        try {
            const fd = new FormData(); fd.append('email', email);
            const res = await fetch('<?= base_url('provider/api/verification/email/update') ?>', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const json = await res.json();
            if(json.success) {
                Swal.fire('Sent', json.message, 'success');
                $('#emailOtpBox').removeClass('d-none');
                $('#emailInput').prop('disabled', true);
                btn.addClass('d-none');
            } else {
                Swal.fire('Error', json.message, 'error');
            }
        } catch(e) { Swal.fire('Error', 'Server error', 'error'); }
        finally { btn.prop('disabled', false).text('Verify'); }
    };

    window.profileVerifyEmailOtp = async function() {
        const otp = $('#emailOtp').val().trim();
        if(otp.length !== 6) return Swal.fire('Error', 'Enter 6-digit OTP', 'error');
        
        try {
            const fd = new FormData(); fd.append('otp', otp);
            const res = await fetch('<?= base_url('provider/api/verification/email/verify-otp') ?>', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const json = await res.json();
            if(json.success) {
                Swal.fire('Success', 'Email verified and updated!', 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', json.message, 'error');
            }
        } catch(e) { Swal.fire('Error', 'Server error', 'error'); }
    };

    window.profileSendPhoneOtp = async function() {
        const phone = $('#phoneInput').val().trim();
        if(phone.length !== 10) return Swal.fire('Error', 'Valid 10-digit number required', 'error');
        
        const btn = $('#btnVerifyPhone');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        
        try {
            const fd = new FormData(); fd.append('phone', phone);
            const res = await fetch('<?= base_url('provider/api/verification/phone/update') ?>', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const json = await res.json();
            if(json.success) {
                if (json.auto_verified) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Verified',
                        text: json.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Sent', json.message, 'success');
                    $('#phoneOtpBox').removeClass('d-none');
                    $('#phoneInput').prop('disabled', true);
                    btn.addClass('d-none');
                }
            } else {
                Swal.fire('Error', json.message, 'error');
            }
        } catch(e) { Swal.fire('Error', 'Server error', 'error'); }
        finally { btn.prop('disabled', false).text('Verify'); }
    };

    window.profileVerifyPhoneOtp = async function() {
        const otp = $('#phoneOtp').val().trim();
        if(otp.length !== 6) return Swal.fire('Error', 'Enter 6-digit OTP', 'error');
        
        try {
            const fd = new FormData(); fd.append('otp', otp);
            const res = await fetch('<?= base_url('provider/api/verification/phone/verify-otp') ?>', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const json = await res.json();
            if(json.success) {
                Swal.fire('Success', 'Phone number verified and updated!', 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', json.message, 'error');
            }
        } catch(e) { Swal.fire('Error', 'Server error', 'error'); }
    };

    $('#btnCancelEdit').on('click', function() {
        location.reload();
    });

    // ── Delete Account ───────────────────────────────────────────
    $('#btnDeleteAccount').on('click', function() {
        Swal.fire({
            title: 'Are you absolutely sure?',
            text: 'This action is permanent and cannot be undone. You will lose all your data immediately.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, delete my account',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('profile/deleteAccount') ?>";
            }
        });
    });

    // ── Flash Notifications ──────────────────────────────────────
    <?php if (session()->getFlashdata('notifySuccess')): ?>
        Swal.fire({ icon: 'success', title: 'Success', text: "<?= session()->getFlashdata('notifySuccess') ?>", toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
    <?php endif; ?>
    <?php if (session()->getFlashdata('notifyError')): ?>
        Swal.fire({ icon: 'error', title: 'Error', text: "<?= session()->getFlashdata('notifyError') ?>", toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
