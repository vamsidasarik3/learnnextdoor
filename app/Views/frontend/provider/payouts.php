<?= $this->extend('frontend/layout/provider_base') ?>

<?= $this->section('content') ?>
<div class="mb-4">
    <h1 class="dashboard-title">Earnings & Payout</h1>
    <p class="dashboard-subtitle">Track your earnings and settlement history</p>
</div>

<!-- Summary Cards -->
<div class="row g-4 mb-5">
    <!-- Total Earnings This Month -->
    <div class="col-lg-4">
        <div class="earnings-card bg-indigo-gradient">
            <i class="bi bi-wallet2 card-icon"></i>
            <div>
                <div class="card-label">Total Earnings (This Month)</div>
                <div class="card-value">₹<?= number_format($upcoming['gross'], 2) ?></div>
            </div>
            <div class="card-footer">
                Based on current settlement period
            </div>
        </div>
    </div>
    <!-- Next Settlement -->
    <div class="col-lg-4">
        <div class="earnings-card bg-emerald-gradient">
            <i class="bi bi-bank card-icon"></i>
            <div>
                <div class="card-label">Next Settlement</div>
                <div class="card-value">₹<?= number_format($upcoming['net'], 2) ?></div>
            </div>
            <div class="card-footer">
                <i class="bi bi-calendar-check me-1"></i> Due on <?= date('d M Y', strtotime($upcoming['payout_date'])) ?>
            </div>
        </div>
    </div>
    <!-- Lifetime Earnings -->
    <?php 
        $lifetime = array_sum(array_column($history, 'net_amount')) + $upcoming['net'];
    ?>
    <div class="col-lg-4">
        <div class="earnings-card bg-orange-gradient">
            <i class="bi bi-graph-up-arrow card-icon"></i>
            <div>
                <div class="card-label">Lifetime Earnings</div>
                <div class="card-value">₹<?= number_format($lifetime, 2) ?></div>
            </div>
            <div class="card-footer">
                Total earnings since joining
            </div>
        </div>
    </div>
</div>

<!-- Tabbed Content Section -->
<div class="mb-4">
    <ul class="nav nav-tabs earnings-tabs border-bottom" id="earningsTabs" role="tablist" style="gap: 1rem;">
        <li class="nav-item">
            <button class="nav-link active" id="upcoming-tab" data-bs-toggle="tab" data-bs-target="#tab-upcoming">Upcoming Settlements</button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#tab-history">Settlement History</button>
        </li>
    </ul>
</div>

<div class="tab-content" id="earningsTabContent">
    <!-- Upcoming Tab -->
    <div class="tab-pane fade show active" id="tab-upcoming">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold m-0 p-2">Period: <?= date('d M', strtotime($upcoming['period_start'])) ?> – <?= date('d M Y', strtotime($upcoming['period_end'])) ?></h6>
            <span class="payout-status status-processing">
                <span class="spinner-grow spinner-grow-sm" role="status"></span>
                Processing
            </span>
        </div>
        
        <div class="earnings-table-container">
            <div class="table-responsive">
                <table class="table earnings-table mb-0">
                    <thead>
                        <tr>
                            <th>Class Name</th>
                            <th>Settlement Date</th>
                            <th>Gross Amount</th>
                            <th>Commission</th>
                            <th>Net Amount</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($upcoming['breakdown'])): ?>
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No transactions recorded in this period yet.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($upcoming['breakdown'] as $listingId => $data): ?>
                                <?php 
                                    $listingComm = $data['gross'] * 0.10;
                                    $listingNet = $data['gross'] - $listingComm - $data['refunds'];
                                ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?= esc($data['title']) ?></div>
                                        <div class="small text-muted">ID: #<?= $listingId ?></div>
                                    </td>
                                    <td><?= date('d M Y', strtotime($upcoming['payout_date'])) ?></td>
                                    <td>₹<?= number_format($data['gross'], 2) ?></td>
                                    <td class="amt-negative">-₹<?= number_format($listingComm + $data['refunds'], 2) ?></td>
                                    <td class="amt-positive fw-bold">₹<?= number_format($listingNet, 2) ?></td>
                                    <td>
                                        <span class="badge bg-warning-light text-warning small px-2 py-1 rounded-pill">Pending</span>
                                    </td>
                                    <td class="text-end">
                                        <div class="d-flex justify-content-end gap-2">
                                            <button class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="showBreakdown(<?= htmlspecialchars(json_encode($data)) ?>)">
                                                Details
                                            </button>
                                            <a href="#" class="btn btn-link text-primary btn-sm text-decoration-none p-0" title="Download Receipt">
                                                <i class="bi bi-download"></i> Receipt
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- History Tab -->
    <div class="tab-pane fade" id="tab-history">
        <div class="earnings-table-container">
            <div class="table-responsive">
                <table class="table earnings-table mb-0">
                    <thead>
                        <tr>
                            <th>Class Name</th>
                            <th>Settlement Date</th>
                            <th>Amount Paid</th>
                            <th>Paid To</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($history)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">No completed settlements found.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($history as $item): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold">Consolidated Payout</div>
                                        <div class="small text-muted">Ref: #SET<?= str_pad($item->id, 6, '0', STR_PAD_LEFT) ?></div>
                                    </td>
                                    <td><?= date('d M Y', strtotime($item->payout_date)) ?></td>
                                    <td class="amt-positive fw-bold">₹<?= number_format($item->net_amount, 2) ?></td>
                                    <td>
                                        <div class="small fw-bold">UPI Transfer</div>
                                        <div class="x-small text-muted"><?= $item->upi_id ?: 'Verified Account' ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-light text-success px-3 py-1 rounded-pill">Paid</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="#" class="btn btn-link text-primary text-decoration-none fw-bold p-0">Download Receipt</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Settlement Info Box -->
<div class="info-box mt-5">
    <i class="bi bi-info-circle-fill"></i>
    <div>
        <p class="fw-bold mb-1">Settlement Schedule</p>
        <p>Settlements are processed bi-monthly: 1st-15th settled on 20th, 16th-end settled on 5th of next month.</p>
    </div>
</div>

<!-- Details Modal -->
<div class="modal fade" id="detailsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold" id="modalListingTitle">Listing Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-lg">
                            <div class="small text-muted mb-1">Gross Bookings</div>
                            <div class="h5 fw-bold mb-0" id="modalGross">₹0</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-light rounded-lg">
                            <div class="small text-muted mb-1">Commission</div>
                            <div class="h5 fw-bold text-danger mb-0" id="modalCommission">₹0</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="p-3 bg-emerald-50 rounded-lg">
                            <div class="small text-emerald-600 mb-1">Net Payout</div>
                            <div class="h5 fw-bold text-emerald-700 mb-0" id="modalNet">₹0</div>
                        </div>
                    </div>
                </div>

                <h6 class="fw-bold mb-3">Student Breakdown</h6>
                <div class="table-responsive" style="max-height: 300px;">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr class="small text-muted">
                                <th>Student Name</th>
                                <th>Date</th>
                                <th class="text-end">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="modalStudentBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn-next w-100" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
function showBreakdown(data) {
    const comm = data.gross * 0.10;
    const net = data.gross - comm - data.refunds;

    $('#modalListingTitle').text(data.title);
    $('#modalGross').text('₹' + parseFloat(data.gross).toFixed(2));
    $('#modalCommission').text('-₹' + parseFloat(comm + data.refunds).toFixed(2));
    $('#modalNet').text('₹' + parseFloat(net).toFixed(2));

    let html = '';
    data.students.forEach(s => {
        html += `<tr>
            <td class="fw-bold">${s.name}</td>
            <td>${new Date(s.date).toLocaleDateString('en-GB', {day: '2-digit', month: 'short'})}</td>
            <td class="text-end fw-bold">₹${parseFloat(s.amount).toFixed(2)}</td>
        </tr>`;
    });
    $('#modalStudentBody').html(html);

    new bootstrap.Modal(document.getElementById('detailsModal')).show();
}
</script>
<?= $this->endSection() ?>
