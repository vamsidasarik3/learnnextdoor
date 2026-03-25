<?= $this->extend('admin/layout/default') ?>

<?= $this->section('content') ?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Provider Verification</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?= url('admin/dashboard') ?>">Home</a></li>
          <li class="breadcrumb-item"><a href="<?= url('admin/listings') ?>">Listings</a></li>
          <li class="breadcrumb-item active">Verify Provider</li>
        </ol>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    
    <div class="row">
      <div class="col-md-4">
        <!-- Profile Card -->
        <div class="card card-primary card-outline">
          <div class="card-body box-profile">
            <div class="text-center">
              <img class="profile-user-img img-fluid img-circle"
                   src="<?= userProfile($provider->id) ?>"
                   alt="User profile picture">
            </div>

            <h3 class="profile-username text-center"><?= esc($provider->name) ?></h3>
            <p class="text-muted text-center"><?= $provider->role == 2 ? 'Verified Provider' : 'Parent (Applying)' ?></p>

            <ul class="list-group list-group-unbordered mb-3">
              <li class="list-group-item">
                <b>Phone</b> 
                <span class="float-right">
                    <?= esc($provider->phone) ?>
                    <?php if($provider->phone_verified): ?>
                        <i class="fas fa-check-circle text-success ml-1" title="Verified"></i>
                    <?php endif; ?>
                </span>
              </li>
              <li class="list-group-item">
                <b>Email</b> <a class="float-right"><?= esc($provider->email) ?></a>
              </li>
              <li class="list-group-item">
                <b>Status</b> 
                <span class="float-right badge <?= $provider->status == 'active' ? 'badge-success' : ($provider->status == 'banned' ? 'badge-danger' : 'badge-warning') ?>">
                    <?= strtoupper($provider->status) ?>
                </span>
              </li>
            </ul>

            <?php if (in_array($provider->role, [2, 3]) && $provider->provider_verification_status !== 'approved'): ?>
            <button class="btn btn-primary btn-block mb-2" onclick="openReviewModal()"><b>REVIEW APPLICATION</b></button>
            <?php endif; ?>

            <?php if ($provider->provider_verification_status === 'approved'): ?>
            <div class="alert alert-success py-2 px-3 small text-center rounded-3">
               <i class="fas fa-check-circle mr-1"></i> VERIFIED PROVIDER
            </div>
            <?php endif; ?>

            <?php if($provider->status !== 'banned'): ?>
            <button class="btn btn-danger btn-block" onclick="blockUserModal()"><b>BLOCK USER</b></button>
            <?php else: ?>
            <p class="text-danger mt-2 small"><strong>Remarks:</strong> <?= esc($provider->status_remarks) ?></p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Payout Details -->
        <div class="card card-secondary">
          <div class="card-header">
            <h3 class="card-title">Payout Info (UPI)</h3>
          </div>
          <div class="card-body">
            <strong><i class="bi bi-wallet2 mr-1"></i> UPI ID</strong>
            <p class="text-primary fw-bold"><?= esc($provider->upi_id ?: 'Not Set') ?></p>
            <hr>
            <strong><i class="bi bi-person-check mr-1"></i> Verified Account Name</strong>
            <p class="text-muted"><?= esc($provider->bank_name ?: 'Pending/Unverified') ?></p>
            <hr>
            <div class="bg-light p-2 border rounded">
                <strong><i class="fas fa-credit-card mr-1 text-primary"></i> Razorpay Account ID</strong>
                <div class="input-group input-group-sm mt-1">
                    <input type="text" id="rzp_account_id" class="form-control" value="<?= esc($provider->razorpay_account_id) ?>" placeholder="acc_XXXXXXXXXXXXXX">
                    <span class="input-group-append">
                        <button type="button" class="btn btn-primary" onclick="updateRzpAccount()">Update</button>
                    </span>
                </div>
                <small class="text-muted">For automated settlements via Route.</small>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-8">
        <!-- KYC Documents -->
        <div class="card card-info">
          <div class="card-header">
            <h3 class="card-title">Verification Documents (KYC)</h3>
          </div>
          <div class="card-body">
            <?php if(empty($docs)): ?>
                <div class="alert alert-warning">No documents uploaded yet.</div>
            <?php else: ?>
                <div class="row">
                    <?php foreach($docs as $doc): ?>
                    <div class="col-sm-6 mb-4">
                        <div class="card h-100 border shadow-sm">
                            <div class="card-header py-2 bg-light">
                                <span class="badge badge-primary"><?= strtoupper($doc->document_type) ?></span>
                                <span class="float-right small text-muted"><?= date('d M Y', strtotime($doc->created_at)) ?></span>
                            </div>
                            <div class="card-body p-0 text-center bg-gray-light" style="min-height: 200px; display: flex; align-items: center; justify-content: center;">
                                <?php 
                                    $is_img = preg_match('/\.(jpg|jpeg|png|webp)$/i', $doc->file_path);
                                ?>
                                <?php if($is_img): ?>
                                    <img src="<?= base_url($doc->file_path) ?>" class="img-fluid" style="max-height: 250px;" onclick="window.open(this.src)">
                                <?php else: ?>
                                    <div>
                                        <i class="fas fa-file-pdf fa-4x text-danger mb-2"></i><br>
                                        <a href="<?= base_url($doc->file_path) ?>" target="_blank" class="btn btn-sm btn-outline-dark">View Document</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer py-2 text-center">
                                <?php if($doc->verified_status == 'pending'): ?>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-success" onclick="verifyDocument(<?= $doc->id ?>, 'verified')" title="Verify Document">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="verifyDocument(<?= $doc->id ?>, 'rejected')" title="Reject Document">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <span class="badge <?= $doc->verified_status == 'verified' ? 'badge-success' : 'badge-danger' ?>">
                                        <?= strtoupper($doc->verified_status) ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

  </div>
</section>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h4 class="modal-title">Review Provider Application</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="reviewForm">
        <input type="hidden" name="id" value="<?= $provider->id ?>">
        <div class="modal-body">
          <div class="form-group">
            <label>Action</label>
            <select name="status" class="form-control" id="review_action" required onchange="updateReviewText()">
                <option value="approved">Approve Provider</option>
                <option value="rejected">Reject Application</option>
                <option value="more_info">Request More Information</option>
            </select>
          </div>
          <div class="form-group">
            <label id="remarks_label">Message to Provider</label>
            <textarea name="remarks" class="form-control" rows="4" placeholder="Enter message for the provider..."></textarea>
            <small class="text-muted" id="remarks_hint">This message will be sent to the provider's email/dashboard.</small>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="reviewSubmitBtn">Submit Decision</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Block Modal -->
<div class="modal fade" id="blockModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h4 class="modal-title">Block User Account</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="blockForm">
        <input type="hidden" name="id" value="<?= $provider->id ?>">
        <div class="modal-body">
          <p>This will <strong>ban</strong> the user and <strong>deactivate</strong> all their listings. Proceed with caution.</p>
          <div class="form-group">
            <label>Reason for Blocking</label>
            <textarea name="remarks" class="form-control" rows="3" required placeholder="Terms violation, fraud, etc..."></textarea>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger" id="blockConfirmBtn">Block Permanently</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
function blockUserModal() {
    $('#blockModal').modal('show');
}

$('#blockForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#blockConfirmBtn');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Blocking...');

    $.post('<?= url('admin/api/block-user') ?>', $(this).serialize(), function(res) {
        if(res.success) {
            toastr.error('User has been blocked.');
            setTimeout(() => location.reload(), 1000);
        } else {
            toastr.error(res.message);
            btn.prop('disabled', false).text('Block Permanently');
        }
    });
});

function updateRzpAccount() {
    const accId = $('#rzp_account_id').val();
    $.post('<?= url('admin/api/update-rzp-account') ?>', {
        id: '<?= $provider->id ?>',
        account_id: accId
    }, function(res) {
        if(res.success) {
            toastr.success('Razorpay Account ID updated.');
        } else {
            toastr.error(res.message);
        }
    });
}
function openReviewModal() {
    updateReviewText(); // Initialize required state
    $('#reviewModal').modal('show');
}

function updateReviewText() {
    const action = $('#review_action').val();
    const hint = $('#remarks_hint');
    const label = $('#remarks_label');
    
    if (action === 'approved') {
        label.text('Approval Message (Optional)');
        hint.text('Send a welcome message or instructions to the new provider.');
        $('[name="remarks"]').prop('required', false);
    } else if (action === 'rejected') {
        label.text('Rejection Reason (Required)');
        hint.text('Explain why the application was rejected.');
        $('[name="remarks"]').prop('required', true);
    } else {
        label.text('Required Documents/Info (Required)');
        hint.text('List the specific documents or information needed to proceed.');
        $('[name="remarks"]').prop('required', true);
    }
}

$('#reviewForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#reviewSubmitBtn');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Submitting...');

    $.post('<?= url('admin/api/provider/review') ?>', $(this).serialize(), function(res) {
        if(res.success) {
            toastr.success(res.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            toastr.error(res.message);
            btn.prop('disabled', false).text('Submit Decision');
        }
    });
});

function verifyDocument(docId, status) {
    if(!confirm("Are you sure you want to mark this document as " + status.toUpperCase() + "?")) return;
    
    $.post('<?= url('admin/api/document/verify') ?>', { id: docId, status: status }, function(res) {
        if(res.success) {
            toastr.success(res.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            toastr.error(res.message);
        }
    });
}
</script>
<?= $this->endSection() ?>
