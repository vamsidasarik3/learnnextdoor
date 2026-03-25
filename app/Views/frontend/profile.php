<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('css') ?>
<style>
    .cnd-profile-container {
        padding-top: 40px;
        padding-bottom: 80px;
        background: #f8f9fa;
    }
    .profile-card {
        border: none;
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    .profile-sidebar {
        background: #fff;
        padding: 30px;
        text-align: center;
    }
    .profile-img-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 20px;
    }
    .profile-img {
        width: 140px;
        height: 140px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .profile-img-edit {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: var(--cnd-pink);
        color: #fff;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 2px solid #fff;
        transition: all 0.2s ease;
    }
    .profile-img-edit:hover {
        transform: scale(1.1);
        background: #e63e9c;
    }
    .profile-nav .nav-link {
        color: #6c757d;
        padding: 12px 20px;
        border-radius: 12px;
        margin-bottom: 5px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .profile-nav .nav-link:hover {
        background: rgba(108, 99, 255, 0.05);
        color: var(--cnd-grad-start);
    }
    .profile-nav .nav-link.active {
        background: var(--cnd-grad-start);
        color: #fff;
    }
    .profile-nav .nav-link i {
        font-size: 1.2rem;
    }
    .cnd-tab-content {
        background: #fff;
        padding: 40px;
        min-height: 500px;
    }
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }
    .form-control {
        border-radius: 10px;
        padding: 12px 15px;
        border: 1px solid #dee2e6;
    }
    .form-control:focus {
        border-color: var(--cnd-grad-start);
        box-shadow: 0 0 0 0.25rem rgba(108, 99, 255, 0.1);
    }
    .btn-save {
        padding: 12px 30px;
        border-radius: var(--cnd-radius-pill);
        font-weight: 700;
        transition: all 0.3s ease;
    }
    .kyc-status-badge {
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="cnd-profile-container">
    <div class="container">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-4 col-xl-3 mb-4">
                <div class="profile-card profile-sidebar">
                    <div class="profile-img-wrapper">
                        <img src="<?= userProfile($user->id) ?>" alt="Profile" class="profile-img" id="sidebarProfileImg">
                        <label for="profileImgInput" class="profile-img-edit">
                            <i class="bi bi-camera-fill"></i>
                        </label>
                    </div>
                    <h4 class="fw-bold mb-1"><?= esc($user->name) ?></h4>
                    <p class="text-muted small mb-1"><?= esc($user->email) ?></p>
                    
                    <?php if(!empty($user->is_verified)): ?>
                    <div class="mb-4">
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 fw-bold" style="font-size: 0.72rem;">
                            <i class="bi bi-patch-check-fill me-1"></i> Verified Provider
                        </span>
                    </div>
                    <?php else: ?>
                    <div class="mb-4 text-muted small">
                         <i class="bi bi-clock-history me-1"></i> Standard Account
                    </div>
                    <?php endif; ?>
                    
                    <div class="nav flex-column nav-pills profile-nav" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <button class="nav-link <?= ($activeTab == 'profile' || $activeTab == 'edit') ? 'active' : '' ?>" id="v-pills-profile-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profile" type="button" role="tab" aria-controls="v-pills-profile" aria-selected="true">
                            <i class="bi bi-person-circle"></i> Personal Details
                        </button>
                        <button class="nav-link <?= ($activeTab == 'change_password') ? 'active' : '' ?>" id="v-pills-security-tab" data-bs-toggle="pill" data-bs-target="#v-pills-security" type="button" role="tab" aria-controls="v-pills-security" aria-selected="false">
                            <i class="bi bi-shield-lock"></i> Password & Security
                        </button>
                        <?php if ($user->role->id == 2): ?>
                        <button class="nav-link" id="v-pills-kyc-tab" data-bs-toggle="pill" data-bs-target="#v-pills-kyc" type="button" role="tab" aria-controls="v-pills-kyc" aria-selected="false">
                            <i class="bi bi-patch-check"></i> KYC Verification
                        </button>
                        <?php endif; ?>
                        <a href="<?= base_url('logout') ?>" class="nav-link text-danger mt-3">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </div>
                </div>
            </div>

            <!-- Content Area -->
            <div class="col-lg-8 col-xl-9">
                <div class="profile-card cnd-tab-content">
                    <div class="tab-content" id="v-pills-tabContent">
                        
                        <!-- Tab 1: Personal Details -->
                        <div class="tab-pane fade <?= ($activeTab == 'profile' || $activeTab == 'edit') ? 'show active' : '' ?>" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                            <h3 class="fw-bold mb-4">Personal Details</h3>
                            <form action="<?= base_url('profile/updateProfile') ?>" method="POST" class="row g-4">
                                <?= csrf_field() ?>
                                <div class="col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control" value="<?= esc($user->name) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" value="<?= esc($user->username) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email Address</label>
                                    <input type="email" name="email" class="form-control" value="<?= esc($user->email) ?>" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="contact" class="form-control" value="<?= esc($user->phone) ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Mailing Address</label>
                                    <textarea name="address" class="form-control" rows="3" placeholder="Enter your full address"><?= esc($user->address) ?></textarea>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn cnd-btn-primary btn-save">Save Changes</button>
                                </div>
                            </form>
                        </div>

                        <!-- Tab 2: Security -->
                        <div class="tab-pane fade <?= ($activeTab == 'change_password') ? 'show active' : '' ?>" id="v-pills-security" role="tabpanel" aria-labelledby="v-pills-security-tab">
                            <h3 class="fw-bold mb-4">Password & Security</h3>
                            <p class="text-muted mb-4">Manage your account security and password settings.</p>
                            
                            <form action="<?= base_url('profile/updatePassword') ?>" method="POST" class="row g-4" style="max-width: 500px;">
                                <?= csrf_field() ?>
                                <div class="col-12">
                                    <label class="form-label">Current Password</label>
                                    <input type="password" name="old_password" class="form-control" required>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="password" class="form-control" minlength="6" required>
                                    <div class="form-text">Minimum 6 characters.</div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Confirm New Password</label>
                                    <input type="password" name="password_confirm" class="form-control" required>
                                </div>
                                <div class="col-12 mt-4">
                                    <button type="submit" class="btn cnd-btn-primary btn-save">Update Password</button>
                                </div>
                            </form>
                        </div>

                        <!-- Tab 3: KYC (For Providers) -->
                        <?php if ($user->role->id == 2): ?>
                        <div class="tab-pane fade" id="v-pills-kyc" role="tabpanel" aria-labelledby="v-pills-kyc-tab">
                            <h3 class="fw-bold mb-4">KYC Verification</h3>
                            <div class="p-4 rounded-4 mb-4" style="background: rgba(108, 99, 255, 0.05);">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="bg-white p-3 rounded-circle shadow-sm">
                                        <i class="bi bi-shield-check text-primary fs-3"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">Verify Your Business</h5>
                                        <p class="text-muted small mb-0">To ensure the safety of our community, all providers must complete verification.</p>
                                    </div>
                                </div>
                                <hr class="my-4 opacity-10">
                            </div>
                            
                            <p class="mb-4">Complete your KYC to unlock full access to booking payments and premium listing features.</p>
                            <a href="<?= base_url('provider/verification') ?>" class="btn cnd-btn-gold px-4 py-2" style="border-radius: 12px;">
                                Go to Verification Center <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Profile Pic Form -->
<form id="profilePicForm" action="<?= base_url('profile/updateProfilePic') ?>" method="POST" enctype="multipart/form-data" class="d-none">
    <?= csrf_field() ?>
    <input type="file" name="image" id="profileImgInput" accept="image/*" onchange="submitProfilePic()">
</form>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    function submitProfilePic() {
        const input = document.getElementById('profileImgInput');
        if (input.files && input.files[0]) {
            // Show preview immediately
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('sidebarProfileImg').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
            
            // Auto submit
            document.getElementById('profilePicForm').submit();
        }
    }

    // Auto-active tab from URL hash if exists
    $(document).ready(function() {
        const hash = window.location.hash;
        if (hash) {
            $('.nav-link' + hash + '-tab').tab('show');
        }
    });

    // Handle toast/notify from CI4 flashdata
    <?php if (session()->getFlashdata('notifySuccess')): ?>
        alert("<?= session()->getFlashdata('notifySuccess') ?>");
    <?php endif; ?>
    <?php if (session()->getFlashdata('notifyError')): ?>
        alert("<?= session()->getFlashdata('notifyError') ?>");
    <?php endif; ?>
</script>
<?= $this->endSection() ?>
