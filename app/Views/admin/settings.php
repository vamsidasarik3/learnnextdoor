<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 fw-900 border-left border-4 border-info pl-3">System Settings & Controls</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Admin User Management -->
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom-0">
                        <h3 class="card-title fw-bold text-dark"><i class="fas fa-users-cog mr-2 text-info"></i> Administrator Access Control</h3>
                        <button class="btn btn-sm btn-info rounded-pill px-3 shadow-sm" data-toggle="modal" data-target="#adminModal">
                            <i class="fas fa-plus mr-1"></i> New Admin
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted uppercase small fw-bold">
                                    <tr>
                                        <th class="px-4">Name / ID</th>
                                        <th>Role / Permissions</th>
                                        <th>Status</th>
                                        <th class="px-4 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($admins as $admin): ?>
                                    <tr>
                                        <td class="px-4">
                                            <div class="fw-bold text-dark"><?= esc($admin->name) ?></div>
                                            <small class="text-muted"><?= esc($admin->email) ?></small>
                                        </td>
                                        <td>
                                            <span class="badge badge-outline-info rounded-pill px-2">Super Admin</span>
                                        </td>
                                        <td>
                                            <?php if ($admin->status == 1): ?>
                                                <span class="badge badge-success rounded-pill px-2">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-danger rounded-pill px-2">Suspended</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-4 text-right">
                                            <?php if ($admin->id != logged('id')): ?>
                                                <div class="dropdown d-inline-block">
                                                    <button class="btn btn-sm btn-link text-muted" data-toggle="dropdown"><i class="fas fa-ellipsis-v"></i></button>
                                                    <div class="dropdown-menu dropdown-menu-right rounded-3 border-0 shadow-sm">
                                                        <a class="dropdown-item small" href="javascript:void(0)" onclick="resetPass(<?= $admin->id ?>)"><i class="fas fa-key mr-2 opacity-50"></i> Reset Password</a>
                                                        <?php if ($admin->status == 1): ?>
                                                            <a class="dropdown-item small text-danger" href="javascript:void(0)" onclick="toggleSuspension(<?= $admin->id ?>, 0)"><i class="fas fa-user-slash mr-2"></i> Suspend Access</a>
                                                        <?php else: ?>
                                                            <a class="dropdown-item small text-success" href="javascript:void(0)" onclick="toggleSuspension(<?= $admin->id ?>, 1)"><i class="fas fa-user-plus mr-2"></i> Reactivate Access</a>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <small class="text-muted italic">Self (Active)</small>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Platform Bank Details (Masked) -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h3 class="card-title fw-bold text-dark"><i class="fas fa-university mr-2 text-primary"></i> Platform Payout Account</h3>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-light border-0 small text-muted mb-4">
                            <i class="bi bi-info-circle-fill me-1"></i> These details are used for receiving platform commissions and handling settlement reserves.
                        </div>
                        <div class="form-group border-bottom pb-3">
                            <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Settlement Account (Masked)</label>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="h5 fw-900 mb-0 font-monospace">XXXX-XXXX-<?= substr($bank['account_number'] ?? '12345678', -4) ?></span>
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="revealBankDetails()">
                                    <i class="fas fa-eye mr-1"></i> View / Edit
                                </button>
                            </div>
                        </div>
                        <div class="form-group border-bottom py-3">
                            <label class="small fw-bold text-muted text-uppercase mb-2 d-block">Bank / IFSC Code</label>
                            <div class="fw-bold text-dark"><?= esc($bank['bank_name'] ?? 'HDFC Bank') ?> - <span class="text-primary"><?= esc($bank['ifsc'] ?? 'HDFC0001234') ?></span></div>
                        </div>
                        <div class="pt-2">
                            <button class="btn btn-sm btn-link text-muted p-0" onclick="reAuthenticate()"><i class="fas fa-shield-alt mr-1"></i> Trigger Mandatory Re-Auth</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Register Admin Modal -->
<div class="modal fade" id="adminModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-info text-white border-0">
                <h5 class="modal-title fw-bold">Register New Administrator</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form action="<?= url('admin/api/settings/create-admin') ?>" method="post">
                <div class="modal-body p-4">
                    <div class="form-group">
                        <label class="small fw-bold text-uppercase">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="small fw-bold text-uppercase">Email Address</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="small fw-bold text-uppercase">Initial Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="alert alert-warning small border-0 mb-0">
                        <i class="fas fa-exclamation-triangle mr-1"></i> New admins will have full control. Ensure the email is valid.
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info rounded-pill px-4 fw-bold">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.fw-900 { font-weight: 900; }
.rounded-4 { border-radius: 1.25rem !important; }
.font-monospace { font-family: 'Courier New', Courier, monospace; letter-spacing: 2px; }
.badge-outline-info { border: 1px solid var(--info); color: var(--info); }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
function revealBankDetails() {
    Swal.fire({
        title: 'Confirm Admin Password',
        text: 'Accessing sensitive bank information requires re-authentication.',
        input: 'password',
        inputAttributes: { autocapitalize: 'off' },
        showCancelButton: true,
        confirmButtonText: 'Authorize',
        confirmButtonColor: '#4f46e5',
        showLoaderOnConfirm: true,
        preConfirm: (pass) => {
            return $.post('<?= url('admin/api/settings/verify-auth') ?>', { password: pass })
                    .then(res => {
                        if (!res.success) throw new Error(res.message);
                        return res;
                    }).catch(err => {
                        Swal.showValidationMessage(`Request failed: ${err}`);
                    });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Authorized',
                html: `<strong>Account Number:</strong> <?= esc($bank['account_number'] ?? 'XXXXXXXXXX') ?><br><strong>Current IFSC:</strong> <?= esc($bank['ifsc'] ?? 'XXXXXXX') ?>`,
                icon: 'success'
            });
        }
    });
}

function resetPass(id) {
    if (!confirm('Send password reset instructions/trigger?')) return;
    $.post('<?= url('admin/api/settings/reset-admin-pass') ?>', { id: id }, function(res) {
        if (res.success) toastr.success(res.message);
        else toastr.error(res.message);
    });
}

function toggleSuspension(id, status) {
    const action = status ? 'reactivate' : 'suspend';
    if (!confirm(`Are you sure you want to ${action} this admin?`)) return;
    $.post('<?= url('admin/api/settings/toggle-admin-status') ?>', { id: id, status: status }, function(res) {
        if (res.success) location.reload();
    });
}
</script>
<?= $this->endSection() ?>
