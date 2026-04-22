<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 fw-900 border-left border-4 border-info pl-3">Booking Management</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3">
                <h3 class="card-title fw-bold">Recent Platform Bookings</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="bookingTable">
                        <thead class="bg-light text-muted uppercase small fw-bold">
                            <tr>
                                <th class="px-4 py-3">ID / Date</th>
                                <th>Student & Parent</th>
                                <th>Class Info</th>
                                <th>Payment</th>
                                <th>Status</th>
                                <th class="px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($bookings as $b): ?>
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-bold">#<?= $b->id ?></div>
                                        <small class="text-muted"><?= date('d M Y, h:i A', strtotime($b->created_at)) ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= esc($b->student_name) ?></div>
                                        <small class="text-muted"><?= esc($b->parent_name ?: $b->parent_phone) ?></small>
                                    </td>
                                    <td>
                                        <div class="text-dark fw-600"><?= esc($b->listing_title) ?></div>
                                        <span class="badge badge-light border rounded-pill px-2 text-xs"><?= strtoupper($b->booking_type) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">₹<?= number_format($b->payment_amount, 2) ?></div>
                                        <?php if ($b->payment_status == 'paid'): ?>
                                            <small class="text-success text-xs fw-bold"><i class="fas fa-check-circle"></i> PAID</small>
                                        <?php else: ?>
                                            <small class="text-warning text-xs fw-bold"><i class="fas fa-clock"></i> PENDING</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $badgeClass = 'secondary';
                                            if ($b->booking_status == 'confirmed') $badgeClass = 'success';
                                            if ($b->booking_status == 'completed') $badgeClass = 'info';
                                            if ($b->booking_status == 'cancelled') $badgeClass = 'danger';
                                        ?>
                                        <span class="badge badge-<?= $badgeClass ?> rounded-pill px-3 py-1">
                                            <?= strtoupper($b->booking_status) ?>
                                        </span>
                                    </td>
                                    <td class="px-4 text-right">
                                        <?php if ($b->payment_status == 'paid' && $b->booking_status != 'refunded'): ?>
                                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="openRefundModal(<?= $b->id ?>, '<?= esc($b->student_name) ?>', '<?= esc($b->listing_title) ?>', <?= $b->payment_amount ?>)">
                                                <i class="fas fa-undo mr-1"></i> Refund
                                            </button>
                                        <?php else: ?>
                                            <span class="text-muted small italic">No actions</span>
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

<!-- Refund Initiation Modal -->
<div class="modal fade" id="refundModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-undo mr-2"></i> Initiate Refund</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="refundForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="booking_id" id="refundBookingId">
                    
                    <div class="alert alert-light border rounded-3 small mb-4">
                        <strong>Student:</strong> <span id="refStudentName"></span><br>
                        <strong>Class:</strong> <span id="refListingTitle"></span><br>
                        <strong>Paid Amount:</strong> ₹<span id="refPaidAmount"></span>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small fw-bold text-uppercase">Refund Type</label>
                        <select name="refund_type" id="refundTypeSelect" class="form-control rounded-3" onchange="toggleRefundAmount()">
                            <option value="full">Full Refund (100%)</option>
                            <option value="prorata">Pro-rata / Custom Refund</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="small fw-bold text-uppercase">Refund Amount (₹)</label>
                        <input type="number" name="amount" id="refundAmountInput" class="form-control rounded-3" step="0.01" readonly required>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small fw-bold text-uppercase">Reason for Refund <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control rounded-3" rows="3" placeholder="e.g. Provider cancelled class, parent requested withdrawal..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4" id="refundSubmitBtn">Initiate Refund</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
let currentPaidAmount = 0;

$(document).ready(function() {
    $('#bookingTable').DataTable({
        "order": [[ 0, "desc" ]]
    });
});

function openRefundModal(id, student, title, amount) {
    currentPaidAmount = parseFloat(amount);
    $('#refundBookingId').val(id);
    $('#refStudentName').text(student);
    $('#refListingTitle').text(title);
    $('#refPaidAmount').text(currentPaidAmount.toFixed(2));
    
    // Default to Full Refund
    $('#refundTypeSelect').val('full');
    $('#refundAmountInput').val(currentPaidAmount.toFixed(2)).prop('readonly', true);
    
    $('#refundModal').modal('show');
}

function toggleRefundAmount() {
    const type = $('#refundTypeSelect').val();
    if (type === 'full') {
        $('#refundAmountInput').val(currentPaidAmount.toFixed(2)).prop('readonly', true);
    } else {
        $('#refundAmountInput').prop('readonly', false).focus();
    }
}

$('#refundForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#refundSubmitBtn');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Initiating...');

    $.post('<?= url('admin/api/refunds/initiate') ?>', $(this).serialize(), function(res) {
        if (res.success) {
            toastr.success(res.message);
            setTimeout(() => window.location.href = '<?= url('admin/refunds') ?>', 1000);
        } else {
            toastr.error(res.message);
            btn.prop('disabled', false).text('Initiate Refund');
        }
    });
});
</script>
<?= $this->endSection() ?>
