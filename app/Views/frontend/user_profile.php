<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('content') ?>

<div class="py-5 bg-light min-vh-100">
    <div class="container">
        <!-- Page Header -->
        <div class="mb-5">
            <h1 class="fw-bold text-dark">My Account</h1>
            <p class="text-muted">Manage your personal information and account settings</p>
        </div>

        <!-- Join as Provider CTA -->
        <div class="alert alert-indigo border-0 rounded-4 p-4 mb-5 shadow-sm d-flex align-items-center justify-content-between overflow-hidden position-relative" style="background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);">
            <div class="position-relative z-1">
                <h4 class="fw-bold text-white mb-1">Want to teach?</h4>
                <p class="text-white opacity-75 mb-0">List your classes and reach thousands of parents in your area.</p>
            </div>
            <div class="position-relative z-1 ms-3">
                <a href="<?= base_url('join-as-provider') ?>" class="btn btn-light rounded-pill px-4 fw-bold">Join as a Provider</a>
            </div>
            <!-- Subtle background decoration -->
            <div class="position-absolute top-0 end-0 opacity-10 d-none d-md-block" style="transform: translate(10%, -10%);">
                <i class="bi bi-mortarboard-fill" style="font-size: 8rem;"></i>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- ============================================================ -->
                <!-- Card 1: Personal Information                                  -->
                <!-- ============================================================ -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white border-bottom-0 p-4 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">Personal Information</h5>
                        <a href="#" class="text-primary fw-bold text-decoration-none small" id="btnEditProfile">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </a>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <form id="profileForm" action="<?= base_url('profile/updateProfile') ?>" method="POST">
                            <?= csrf_field() ?>

                            <!-- Full Name -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Full Name</label>
                                <div class="figma-value-display py-2 fw-medium fs-5" id="displayName"><?= esc($user->name) ?></div>
                                <input type="text" name="name" class="form-control rounded-3 d-none" value="<?= esc($user->name) ?>">
                            </div>

                            <!-- Email Address -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Email Address</label>
                                <div class="figma-value-display py-2 fw-medium fs-5" id="displayEmail"><?= esc($user->email) ?></div>
                                <input type="email" name="email" class="form-control rounded-3 d-none" value="<?= esc($user->email) ?>" disabled>
                                <p class="small text-warning mt-1 d-none" id="helperEmail"><i class="bi bi-info-circle me-1"></i> Email cannot be changed here.</p>
                            </div>

                            <!-- Phone Number -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase">Phone Number</label>
                                <div class="figma-value-display py-2 fw-medium fs-5" id="displayPhone"><?= esc($user->phone ?: 'Not set') ?></div>
                                <div class="d-none" id="phoneEditBox">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">+91</span>
                                        <input type="text" id="phoneInput" class="form-control border-start-0" value="<?= esc($user->phone) ?>" maxlength="10">
                                        <button type="button" class="btn btn-outline-primary" id="btnVerifyPhone" onclick="profileSendPhoneOtp()">Verify</button>
                                    </div>
                                    <!-- Phone OTP Box -->
                                    <div class="mt-2 d-none" id="phoneOtpBox">
                                        <div class="input-group">
                                            <input type="text" id="phoneOtp" class="form-control" placeholder="6-digit OTP" maxlength="6">
                                            <button type="button" class="btn btn-primary" onclick="profileVerifyPhoneOtp()">Verify OTP</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Save / Cancel (hidden until edit mode) -->
                            <div class="d-none mt-4 pt-3 border-top" id="profileActionGroup">
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Save Changes</button>
                                    <button type="button" class="btn btn-light border rounded-pill px-4 fw-bold" id="btnCancelEdit">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- ============================================================ -->
                <!-- Card 2: Account Actions                                       -->
                <!-- ============================================================ -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3 text-danger">Danger Zone</h5>
                        <p class="text-muted small mb-4">You can permanently delete your account and all your booking history. This action is irreversible.</p>
                        <button class="btn btn-outline-danger rounded-pill px-4 fw-bold" id="btnDeleteAccount">
                            Delete My Account
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Sidebar Quick Links -->
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">Quick Navigation</h6>
                        <div class="list-group list-group-flush">
                            <a href="<?= base_url('my-bookings') ?>" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center gap-2">
                                <i class="bi bi-calendar-check text-primary"></i> My Bookings
                            </a>
                            <a href="<?= base_url('classes') ?>" class="list-group-item list-group-item-action border-0 px-0 d-flex align-items-center gap-2">
                                <i class="bi bi-search text-primary"></i> Explore Classes
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
/* Sleek Profile Styles */
.card.rounded-4 { border-radius: 16px !important; }
.card.shadow-sm { box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06) !important; }

.figma-value-display {
    font-size: 0.9375rem;
    color: #111827;
    font-weight: 500;
    padding: 0.35rem 0;
}

.form-label.text-uppercase {
    letter-spacing: 0.025em;
    font-size: 0.75rem;
}

.btn-primary {
    background-color: #4F46E5 !important;
    border-color: #4F46E5 !important;
}

.btn-outline-danger:hover {
    background-color: #DC2626 !important;
}

.list-group-item-action:hover {
    background-color: #F9FAFB !important;
    color: #4F46E5 !important;
}
</style>
<?= $this->endSection() ?>
<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function() {
    // Edit Profile Logic
    $('#btnEditProfile').on('click', function(e) {
        e.preventDefault();
        $('.figma-value-display').addClass('d-none');
        $('input.form-control').first().removeClass('d-none'); // Show name input
        $('#phoneEditBox').removeClass('d-none');
        $('#profileActionGroup').removeClass('d-none');
        $('#helperEmail').removeClass('d-none');
        $(this).hide();
    });

    $('#btnCancelEdit').on('click', function() {
        location.reload();
    });

    // OTP Handlers (Matching Provider Profile)
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

    // Delete Account Logic
    $('#btnDeleteAccount').on('click', function() {
        Swal.fire({
            title: 'Are you sure?',
            text: 'This will permanently delete your account and booking history.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            cancelButtonColor: '#6B7280',
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "<?= base_url('profile/deleteAccount') ?>";
            }
        });
    });

    // Notifications
    <?php if (session()->getFlashdata('notifySuccess')): ?>
        Swal.fire({ icon: 'success', title: 'Success', text: "<?= session()->getFlashdata('notifySuccess') ?>", toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
    <?php endif; ?>
    <?php if (session()->getFlashdata('notifyError')): ?>
        Swal.fire({ icon: 'error', title: 'Error', text: "<?= session()->getFlashdata('notifyError') ?>", toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
