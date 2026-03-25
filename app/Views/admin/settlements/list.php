<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Settlement Management</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header border-0 bg-white py-3">
                <h3 class="card-title fw-900 text-dark">Payout & Settlement Ledger</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="settlementTable">
                        <thead class="bg-light text-muted uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">
                            <tr>
                                <th class="px-4 py-3 border-0">Txn ID</th>
                                <th class="py-3 border-0">Provider Info</th>
                                <th class="py-3 border-0">Class Details</th>
                                <th class="py-3 border-0">Amount</th>
                                <th class="py-3 border-0">Settlement Status</th>
                                <th class="px-4 py-3 border-0 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($transactions as $txn): ?>
                                <tr>
                                    <td class="px-4 text-muted small"><?= $txn->razorpay_id ?></td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= esc($txn->provider_name) ?></div>
                                        <small class="text-muted">Settling to RZP Linked Account</small>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-dark fw-bold"><?= esc($txn->listing_title) ?></span>
                                            <div>
                                                <span class="badge badge-light border rounded-pill px-2 py-1 text-xs" style="font-size: 10px;"><?= strtoupper($txn->listing_type) ?></span>
                                                <small class="text-muted ml-1"><?= date('d M Y', strtotime($txn->class_date)) ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-900 text-dark" style="font-size: 1.1rem;">₹<?= number_format($txn->amount, 2) ?></div>
                                    </td>
                                    <td>
                                        <?php if ($txn->settled_at): ?>
                                            <span class="badge rounded-pill px-3 py-2 fw-600" style="background: #dcfce7; color: #16a34a; border: 1px solid #86efac;">
                                                <i class="fas fa-check-circle mr-1"></i> SETTLED
                                            </span>
                                        <?php elseif ($txn->is_blocked): ?>
                                            <div class="d-flex flex-column">
                                                <span class="badge rounded-pill px-3 py-2 fw-600 w-fit" style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5;">
                                                    <i class="fas fa-ban mr-1"></i> BLOCKED
                                                </span>
                                                <small class="text-xs text-danger mt-1"><?= esc($txn->block_reason) ?></small>
                                            </div>
                                        <?php else: ?>
                                            <span class="badge rounded-pill px-3 py-2 fw-600" style="background: #fef9c3; color: #a16207; border: 1px solid #fde047;">
                                                <i class="fas fa-clock mr-1"></i> PENDING
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 text-right">
                                        <?php if (!$txn->settled_at): ?>
                                            <?php if ($txn->is_blocked): ?>
                                                <button class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold" onclick="toggleBlock(<?= $txn->id ?>, 0)">
                                                    <i class="fas fa-unlock mr-1"></i> Unblock
                                                </button>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" onclick="showBlockModal(<?= $txn->id ?>)">
                                                    <i class="fas fa-lock mr-1"></i> Block
                                                </button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <div class="text-success small fw-bold"><i class="fas fa-check-double mr-1"></i> PROCESSED</div>
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
</section>

<!-- Block Modal -->
<div class="modal fade" id="blockModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Block Settlement</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="blockTxnId">
                <div class="form-group">
                    <label>Reason for Blocking</label>
                    <textarea id="blockReason" class="form-control" rows="3" placeholder="e.g. Disputed by parent, pending investigation..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="toggleBlock(null, 1)">Block Settlement</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    $('#settlementTable').DataTable({
        "order": [[ 4, "asc" ]]
    });
});

function showBlockModal(id) {
    $('#blockTxnId').val(id);
    $('#blockReason').val('');
    $('#blockModal').modal('show');
}

function toggleBlock(id, blocked) {
    if (id === null) id = $('#blockTxnId').val();
    const reason = $('#blockReason').val();

    if (blocked && !reason) {
        alert('Please provide a reason for blocking.');
        return;
    }

    $.post('<?= url('admin/api/settlements/block') ?>', {
        id: id,
        blocked: blocked,
        reason: reason
    }, function(res) {
        if (res.success) {
            location.reload();
        } else {
            alert(res.message || 'Operation failed');
        }
    });
}
</script>
<?= $this->endSection() ?>
