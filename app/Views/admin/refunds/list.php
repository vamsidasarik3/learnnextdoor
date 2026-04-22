<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 fw-900 border-left border-4 border-danger pl-3">Refund Management</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Summary Tabs -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white p-0 border-0">
                        <ul class="nav nav-pills custom-pills nav-fill" id="refundTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active py-3 rounded-0 text-danger" id="pending-tab" data-toggle="pill" href="#pending" role="tab">
                                    <i class="fas fa-hand-holding-usd mr-2"></i> Pending Queue (<?= count($pending) ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 rounded-0 text-success" id="completed-tab" data-toggle="pill" href="#completed" role="tab">
                                    <i class="fas fa-check-circle mr-2"></i> Processed (<?= count($completed) ?>)
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content" id="refundTabContent">
            <!-- PENDING TAB -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 datatable-refunds">
                                <thead class="bg-light text-muted uppercase small fw-bold">
                                    <tr>
                                        <th class="px-4 py-3">Req ID / Booking</th>
                                        <th>Student / Parent</th>
                                        <th>Reason / Type</th>
                                        <th>Refund Amount</th>
                                        <th class="px-4 text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($pending)): ?>
                                        <tr><td colspan="5" class="text-center py-5 text-muted">No pending refunds in queue.</td></tr>
                                    <?php endif; ?>
                                    <?php foreach ($pending as $r): ?>
                                        <tr>
                                            <td class="px-4">
                                                <div class="fw-bold">#REF-<?= $r->id ?></div>
                                                <small class="text-muted">Booking #<?= $r->booking_id ?></small>
                                                <div class="text-xs text-primary font-italic"><?= esc($r->listing_title) ?></div>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark"><?= esc($r->student_name) ?></div>
                                                <small class="text-muted"><?= esc($r->parent_name) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge badge-<?= $r->refund_type == 'full' ? 'danger' : 'warning' ?> rounded-pill px-2 mb-1">
                                                    <?= strtoupper($r->refund_type) ?>
                                                </span>
                                                <div class="small text-muted text-truncate" style="max-width: 200px;" title="<?= esc($r->reason) ?>">
                                                    <?= esc($r->reason) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="fw-900 text-danger h5 mb-0">₹<?= number_format($r->amount, 2) ?></div>
                                                <small class="text-xs text-muted">Orig. Paid: ₹<?= $r->payment_amount ?></small>
                                            </td>
                                            <td class="px-4 text-right">
                                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold" onclick="openProcessModal(<?= $r->id ?>, <?= $r->amount ?>)">
                                                    <i class="fas fa-check mr-1"></i> Mark Processed
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- COMPLETED TAB -->
            <div class="tab-pane fade" id="completed" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 datatable-refunds">
                                <thead class="bg-light text-muted uppercase small fw-bold">
                                    <tr>
                                        <th class="px-4 py-3">Req ID / Ref</th>
                                        <th>Booking Details</th>
                                        <th>Amount</th>
                                        <th>Processed Date</th>
                                        <th class="px-4 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($completed as $r): ?>
                                        <tr>
                                            <td class="px-4">
                                                <div class="fw-bold">#REF-<?= $r->id ?></div>
                                                <small class="text-success fw-bold"><?= esc($r->transaction_reference) ?></small>
                                            </td>
                                            <td>
                                                <div class="text-dark fw-bold"><?= esc($r->listing_title) ?></div>
                                                <small class="text-muted">Student: <?= esc($r->student_name) ?></small>
                                            </td>
                                            <td>
                                                <div class="fw-900 text-dark">₹<?= number_format($r->amount, 2) ?></div>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?= date('d M Y, h:i A', strtotime($r->processed_at)) ?></small>
                                            </td>
                                            <td class="px-4 text-right">
                                                <span class="badge badge-success rounded-pill px-3 py-2 fw-600">
                                                    <i class="fas fa-check-circle mr-1"></i> REFUNDED
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Processing Modal -->
<div class="modal fade" id="processModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-file-invoice-dollar mr-2"></i> Confirm Refund Payment</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="processForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="refundId">
                    <div class="text-center mb-4">
                        <small class="text-xs text-uppercase fw-900 opacity-50">Refund Amount</small>
                        <h2 class="fw-900 text-danger" id="displayAmount">₹0.00</h2>
                    </div>
                    
                    <div class="form-group mb-0">
                        <label class="small fw-bold text-uppercase">Refund Txn Reference (Razorpay/Bank) <span class="text-danger">*</span></label>
                        <input type="text" name="transaction_reference" class="form-control form-control-lg rounded-3 border-danger" placeholder="e.g. rfnd_KH29s19as" required>
                        <small class="form-text text-muted">Enter the reference ID from Razorpay or your bank statement.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold" id="processSubmitBtn">Mark as Refunded</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.custom-pills .nav-link { color: #6c757d; font-weight: 700; border-bottom: 3px solid transparent; transition: all 0.3s; }
.custom-pills .nav-link.active.text-danger { background: rgba(220, 53, 69, 0.05); color: #dc3545 !important; border-bottom-color: #dc3545; }
.custom-pills .nav-link.active.text-success { background: rgba(40, 167, 69, 0.05); color: #28a745 !important; border-bottom-color: #28a745; }
.fw-900 { font-weight: 900; }
.rounded-4 { border-radius: 1rem !important; }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    $('.datatable-refunds').DataTable({
        "order": [[ 0, "desc" ]]
    });
});

function openProcessModal(id, amount) {
    $('#refundId').val(id);
    $('#displayAmount').text('₹' + parseFloat(amount).toLocaleString('en-IN', { minimumFractionDigits: 2 }));
    $('#processModal').modal('show');
}

$('#processForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#processSubmitBtn');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

    $.post('<?= url('admin/api/refunds/process') ?>', $(this).serialize(), function(res) {
        if (res.success) {
            toastr.success(res.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            toastr.error(res.message);
            btn.prop('disabled', false).text('Mark as Refunded');
        }
    });
});
</script>
<?= $this->endSection() ?>
