<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 fw-900 border-left border-4 border-primary pl-3">Settlement Management</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        
        <!-- Summary Stats / Tabs -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white p-0 border-0">
                        <ul class="nav nav-pills custom-pills nav-fill" id="settlementTabs" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active py-3 rounded-0" id="pending-tab" data-toggle="pill" href="#pending" role="tab">
                                    <i class="fas fa-clock mr-2"></i> Pending (<?= count($pending) ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 rounded-0" id="future-tab" data-toggle="pill" href="#future" role="tab">
                                    <i class="fas fa-calendar-alt mr-2"></i> Future (<?= count($future) ?>)
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 rounded-0" id="completed-tab" data-toggle="pill" href="#completed" role="tab">
                                    <i class="fas fa-check-double mr-2"></i> Completed (<?= count($completed) ?>)
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content" id="settlementTabContent">
            <!-- PENDING TAB -->
            <div class="tab-pane fade show active" id="pending" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <?php renderSettlementTable($pending, 'pending'); ?>
                    </div>
                </div>
            </div>

            <!-- FUTURE TAB -->
            <div class="tab-pane fade" id="future" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <?php renderSettlementTable($future, 'future'); ?>
                    </div>
                </div>
            </div>

            <!-- COMPLETED TAB -->
            <div class="tab-pane fade" id="completed" role="tabpanel">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <?php renderSettlementTable($completed, 'completed'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php 
function renderSettlementTable($data, $type) {
    ?>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 datatable-simple">
            <thead class="bg-light text-muted uppercase small fw-bold">
                <tr>
                    <th class="px-4 py-3">ID / Period</th>
                    <th>Provider</th>
                    <th>Listing</th>
                    <th>Net Amount</th>
                    <th>Status</th>
                    <th class="px-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($data)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No settlements found in this category.</td>
                    </tr>
                <?php endif; ?>
                <?php foreach ($data as $s): ?>
                    <tr>
                        <td class="px-4">
                            <div class="fw-bold">#<?= $s->id ?></div>
                            <small class="text-muted"><?= date('d M', strtotime($s->period_start)) ?> - <?= date('d M Y', strtotime($s->period_end)) ?></small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark"><?= esc($s->provider_name) ?></div>
                            <small class="text-primary"><i class="fas fa-wallet mr-1"></i> <?= esc($s->upi_id ?: 'Linked RZP') ?></small>
                        </td>
                        <td>
                            <div class="text-dark small"><?= esc($s->listing_title) ?></div>
                            <small class="text-muted">Payout Due: <?= date('d M Y', strtotime($s->payout_date)) ?></small>
                        </td>
                        <td>
                            <div class="fw-900 text-dark h5 mb-0">₹<?= number_format($s->net_amount, 2) ?></div>
                            <small class="text-muted text-xs">Gross: ₹<?= $s->gross_amount ?> (-₹<?= $s->commission_amount ?> Comm)</small>
                        </td>
                        <td>
                            <?php if ($s->status == 'completed'): ?>
                                <span class="badge badge-success rounded-pill px-3">COMPLETED</span>
                            <?php elseif ($s->status == 'pending' && strtotime($s->payout_date) <= time()): ?>
                                <span class="badge badge-warning rounded-pill px-3">DUE NOW</span>
                            <?php else: ?>
                                <span class="badge badge-light border rounded-pill px-3 text-muted">UPCOMING</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 text-right">
                            <?php if ($s->status == 'pending'): ?>
                                <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" onclick="openPayoutModal(<?= $s->id ?>, <?= $s->net_amount ?>)">
                                    <i class="fas fa-check-circle mr-1"></i> Mark as Paid
                                </button>
                            <?php else: ?>
                                <div class="text-muted small text-truncate" style="max-width: 150px;" title="<?= esc($s->notes) ?>">
                                    <i class="fas fa-receipt mr-1 text-success"></i> <?= esc($s->notes) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
?>

<!-- Payout Confirmation Modal -->
<div class="modal fade" id="payoutModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-money-check-alt mr-2"></i> Process Payout</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="payoutForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="settlementId">
                    <div class="text-center mb-4">
                        <small class="text-xs text-uppercase fw-900 opacity-50">Settlement Amount</small>
                        <h2 class="fw-900 text-primary" id="displayAmount">₹0.00</h2>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="small fw-bold text-uppercase">Transaction ID / Reference <span class="text-danger">*</span></label>
                        <input type="text" name="transaction_id" class="form-control form-control-lg rounded-3" placeholder="e.g. RZP_12345678 or Bank UTR" required minlength="8">
                        <small class="form-text text-muted">Paste the Razorpay or bank transfer reference here.</small>
                    </div>

                    <div class="form-group mb-0">
                        <label class="small fw-bold text-uppercase">Optional Notes</label>
                        <textarea name="notes" class="form-control rounded-3" rows="2" placeholder="Any internal notes..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4" id="payoutSubmitBtn">Complete Payout</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.custom-pills .nav-link { color: #6c757d; font-weight: 700; border-bottom: 3px solid transparent; transition: all 0.3s; }
.custom-pills .nav-link.active { background: rgba(var(--cnd-primary-rgb), 0.05); color: #3F3590; border-bottom-color: #3F3590; }
.fw-900 { font-weight: 900; }
.rounded-4 { border-radius: 1rem !important; }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    $('.datatable-simple').DataTable({
        "order": [[ 4, "desc" ]],
        "pageLength": 10
    });
});

function openPayoutModal(id, amount) {
    $('#settlementId').val(id);
    $('#displayAmount').text('₹' + parseFloat(amount).toLocaleString('en-IN', { minimumFractionDigits: 2 }));
    $('#payoutModal').modal('show');
}

$('#payoutForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#payoutSubmitBtn');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

    $.post('<?= url('admin/api/settlements/complete') ?>', $(this).serialize(), function(res) {
        if (res.success) {
            toastr.success(res.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            toastr.error(res.message);
            btn.prop('disabled', false).text('Complete Payout');
        }
    });
});
</script>
<?= $this->endSection() ?>
