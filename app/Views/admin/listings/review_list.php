<?= $this->extend('admin/layout/default') ?>

<?= $this->section('content') ?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Listing Management</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?= url('admin/dashboard') ?>">Home</a></li>
          <li class="breadcrumb-item active">Listings</li>
        </ol>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
      <div class="card-header border-0 bg-white py-3">
        <h3 class="card-title fw-900 text-dark">Class Modaration Queue</h3>
      </div>
      
      <div class="card-body p-0">
        <div class="table-responsive">
          <table id="listingsTable" class="table table-hover align-middle mb-0">
            <thead class="bg-light">
              <tr>
                <th class="px-4 py-3 border-0">ID</th>
                <th class="py-3 border-0">Class Details</th>
                <th class="py-3 border-0">Provider Info</th>
                <th class="py-3 border-0">Review Status</th>
                <th class="px-4 py-3 border-0 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($listings as $l): ?>
              <tr>
                <td class="px-4 text-muted">#<?= $l->id ?></td>
                <td>
                  <div class="d-flex flex-column">
                    <span class="fw-bold text-dark" style="font-size: 1rem;"><?= esc($l->title) ?></span>
                    <small class="text-muted">
                       <i class="fas fa-layer-group mr-1"></i> <?= esc($l->category_name) ?><?= !empty($l->subcategory_names) ? ' &rsaquo; ' . esc($l->subcategory_names) : '' ?>
                    </small>
                  </div>
                </td>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="provider-avatar mr-3 bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="fas fa-user-tie text-secondary"></i>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="<?= url('admin/provider/' . $l->provider_id) ?>" class="text-primary fw-bold text-decoration-none">
                            <?= esc($l->provider_name) ?>
                        </a>
                        <small class="text-muted"><?= esc($l->provider_email) ?></small>
                    </div>
                  </div>
                </td>
                <td>
                  <?php if($l->review_status == 'pending'): ?>
                      <span class="badge rounded-pill px-3 py-2 fw-600" style="background: #fef9c3; color: #a16207; border: 1px solid #fde047;">PENDING</span>
                  <?php elseif($l->review_status == 'approved'): ?>
                      <span class="badge rounded-pill px-3 py-2 fw-600" style="background: #dcfce7; color: #16a34a; border: 1px solid #86efac;">APPROVED</span>
                  <?php else: ?>
                      <span class="badge rounded-pill px-3 py-2 fw-600" style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;">REJECTED</span>
                  <?php endif; ?>
                </td>
                <td class="px-4 text-right">
                  <div class="btn-group shadow-sm rounded-pill overflow-hidden bg-white border">
                    <button type="button" class="btn btn-sm btn-white border-right" onclick="viewDetails(<?= $l->id ?>)" title="View Live">
                      <i class="fas fa-external-link-alt text-info"></i>
                    </button>
                    <?php if($l->review_status != 'approved'): ?>
                    <button type="button" class="btn btn-sm btn-white border-right" onclick="approveListing(<?= $l->id ?>)" title="Approve">
                      <i class="fas fa-check-circle text-success"></i>
                    </button>
                    <?php endif; ?>
                    <?php if($l->review_status != 'rejected'): ?>
                    <button type="button" class="btn btn-sm btn-white" onclick="rejectListing(<?= $l->id ?>)" title="Reject">
                      <i class="fas fa-times-circle text-danger"></i>
                    </button>
                    <?php endif; ?>
                  </div>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->

  </div><!-- /.container-fluid -->
</section>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="modalTitle">Review Listing</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="reviewForm">
        <input type="hidden" name="id" id="reviewId">
        <input type="hidden" name="status" id="reviewStatus">
        <div class="modal-body">
          <p id="reviewPrompt"></p>
          <div class="form-group">
            <label id="remarks_label">Admin Remarks / Reason</label>
            <textarea name="remarks" class="form-control" rows="3" placeholder="Explain the approval or reason for rejection..."></textarea>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="confirmBtn">Confirm Action</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
  $(function () {
    $("#listingsTable").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "order": [[4, "asc"], [0, "desc"]]
    });
  });

  function approveListing(id) {
    $('#reviewId').val(id);
    $('#reviewStatus').val('approved');
    $('#modalTitle').text('Approve Class');
    $('#reviewPrompt').html('Are you sure you want to <strong>APPROVE</strong> this listing? It will go live immediately.');
    $('#confirmBtn').removeClass('btn-danger').addClass('btn-success').text('Approve Now');
    $('#remarks_label').text('Approval Message (Optional)');
    $('[name="remarks"]').prop('required', false).attr('placeholder', 'Send a welcome message or instructions to the provider (Optional).');
    $('#reviewModal').modal('show');
  }

  function rejectListing(id) {
    $('#reviewId').val(id);
    $('#reviewStatus').val('rejected');
    $('#modalTitle').text('Reject Class');
    $('#reviewPrompt').html('Are you sure you want to <strong>REJECT</strong> this listing? Provide a reason below.');
    $('#confirmBtn').removeClass('btn-success').addClass('btn-danger').text('Confirm Rejection');
    $('#remarks_label').text('Rejection Reason (Required)');
    $('[name="remarks"]').prop('required', true).attr('placeholder', 'Explain why the listing was rejected (Required).');
    $('#reviewModal').modal('show');
  }

  $('#reviewForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#confirmBtn');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

    $.post('<?= url('admin/api/listings/review') ?>', $(this).serialize(), function(res) {
      if(res.success) {
        toastr.success(res.message);
        setTimeout(() => location.reload(), 1000);
      } else {
        toastr.error(res.message);
        btn.prop('disabled', false).text('Confirm Action');
      }
    });
  });

  function viewDetails(id) {
      window.open('<?= base_url('classes') ?>/' + id, '_blank');
  }
</script>
<?= $this->endSection() ?>
