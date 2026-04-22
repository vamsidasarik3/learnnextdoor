<?= $this->extend('frontend/layout/provider_base') ?>

<?= $this->section('css') ?>
<style>
    .cnd-provider-hero { background: linear-gradient(135deg, #10B981 0%, #34D399 100%); }
    .table-card { background: #fff; border-radius: 1.25rem; border: none; box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.05); overflow: hidden; }
    .table thead { background: #f9fafb; }
    .table thead th { font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; color: #6b7280; border: none; padding: 1.25rem 1rem; }
    .table tbody td { padding: 1.25rem 1rem; vertical-align: middle; border-bottom: 1px solid #f3f4f6; }
    .status-badge { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; padding: 0.3rem 0.8rem; border-radius: 2rem; }
    .status-active { background: #d4f8e8; color: #1a7a4a; }
    .status-inactive { background: #fef4e8; color: #9c6800; }
    .status-pending { background: #fff8e1; color: #f57c00; }
    .status-suspended { background: #fee8e8; color: #b02a37; }
    .status-cancelled { background: #f8d7da; color: #842029; }
    .status-completed { background: #e2e3e5; color: #41464b; }
    .stat-card { background: #fff; border-radius: 1.2rem; padding: 1.5rem; border: none; box-shadow: 0 4px 6px rgba(0,0,0,0.02); }
    .payment-badge { font-size: 0.7rem; font-weight: 700; border-radius: 0.5rem; padding: 0.25rem 0.6rem; }
    .payment-paid { background: #e0f2fe; color: #0369a1; }
    .payment-trial { background: #fef3c7; color: #92400e; }
    .listing-type-tag { font-size: 0.6rem; text-transform: uppercase; background: #f3f4f6; padding: 0.1rem 0.4rem; border-radius: 0.25rem; margin-left: 0.5rem; }
</style>
<?= $this->endSection() ?>

<div class="mb-5">
    <h1 class="dashboard-title">Student Enrollments</h1>
    <p class="dashboard-subtitle">Track and manage all students enrolled in your classes. Privacy protected by default.</p>
</div>

<div class="container-fluid py-2">
        <div class="table-card table-responsive">
            <table class="table mb-0">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Age</th>
                        <th>Class / Course</th>
                        <th>Enrollment Date</th>
                        <th>End Date</th>
                        <th>Payment</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($enrollments)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="bi bi-people d-block fs-1 mb-3 opacity-25"></i>
                                No enrollments found yet.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($enrollments as $e): 
                            $isEnded = ($e->enrollment_end_date && strtotime($e->enrollment_end_date) < time());
                            $isActive = !$isEnded;
                            $statusClass = $isActive ? 'status-active' : 'status-inactive';
                            $statusText = $isActive ? 'Active' : 'Inactive';
                            $payType = (stripos($e->batch_name, 'trial') !== false || $e->payment_amount == 0) ? 'Free Trial' : 'Paid';
                            $payClass = ($payType === 'Paid') ? 'payment-paid' : 'payment-trial';
                        ?>
                            <tr>
                                <td><span class="fw-bold text-dark"><?= htmlspecialchars($e->student_name) ?></span></td>
                                <td><?= $e->student_age ?> Yrs</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="listing-type-tag text-muted"><?= $e->listing_type ?></span>
                                    </div>
                                    <div class="small text-muted" style="font-size: 0.7rem;"><?= htmlspecialchars($e->batch_name) ?></div>
                                </td>
                                <td><?= date('d-m-Y', strtotime($e->created_at)) ?></td>
                                <td>
                                    <?php if ($e->listing_type === 'workshop'): ?>
                                        <span class="text-muted small">One-time</span>
                                    <?php else: ?>
                                        <?= $e->enrollment_end_date ? date('d-m-Y', strtotime($e->enrollment_end_date)) : '<span class="text-muted italic small">Ongoing</span>' ?>
                                    <?php endif; ?>
                                </td>
                                <td><span class="payment-badge <?= $payClass ?>"><?= $payType ?></span></td>
                                <td><span class="status-badge <?= $statusClass ?>"><?= $statusText ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
