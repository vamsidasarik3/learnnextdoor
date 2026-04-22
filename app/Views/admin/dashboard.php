<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<!-- Content Header (Page header) -->
<div class="content-header dashboard-header">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 font-weight-bold" style="font-size: 2.25rem; letter-spacing: -1px;">Dashboard Overview</h1>
                <p class="text-muted mt-2" style="font-size: 1.1rem;">Welcome back, <span class="text-primary font-weight-bold"><?= logged('name') ?></span>. Here's your platform status.</p>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <!-- Alert System -->
        <?php if (!empty($alerts)): ?>
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex overflow-auto pb-2" style="gap: 1.5rem;">
                    <?php foreach ($alerts as $alert): ?>
                    <div class="flex-shrink-0" style="min-width: 320px;">
                        <a href="<?= $alert['link'] ?>" class="text-decoration-none">
                            <div class="alert alert-<?= $alert['type'] ?> border-0 shadow-sm d-flex align-items-center mb-0 p-3" style="border-radius: 12px; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                                <div class="bg-<?= $alert['type'] ?> text-white rounded-circle d-flex align-items-center justify-content-center mr-3" style="width: 44px; height: 44px; font-size: 1.25rem;">
                                    <i class="<?= $alert['icon'] ?>"></i>
                                </div>
                                <div class="flex-grow-1 overflow-hidden">
                                    <h6 class="alert-heading mb-0 font-weight-bold truncate text-dark" style="font-size: 0.95rem;"><?= $alert['title'] ?></h6>
                                    <p class="mb-0 text-muted small" style="font-size: 0.8rem;"><?= $alert['desc'] ?></p>
                                </div>
                                <div class="ml-2">
                                    <i class="fas fa-chevron-right text-muted small"></i>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Stats Grid -->
        <div class="row stats-grid">
            <div class="col-md-3">
                <div class="small-box bg-info-soft">
                    <div class="inner">
                        <h3><?= number_format($stats->total_bookings) ?></h3>
                        <p>Total Bookings</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-calendar-check" style="color: var(--info);"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-success-soft">
                    <div class="inner">
                        <h3>₹<?= number_format($stats->total_revenue) ?></h3>
                        <p>Total Revenue</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-wallet" style="color: var(--success);"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-warning-soft">
                    <div class="inner">
                        <h3><?= number_format($stats->total_providers) ?></h3>
                        <p>Verified Providers</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-user-tie" style="color: var(--warning);"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="small-box bg-primary-soft">
                    <div class="inner">
                        <h3><?= number_format($stats->total_parents) ?></h3>
                        <p>Registered Parents</p>
                    </div>
                    <div class="icon">
                        <i class="fas fa-users" style="color: var(--primary);"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card card-outline card-primary shadow-lg mb-4">
                    <div class="card-header d-flex align-items-center justify-content-between py-3 bg-white border-bottom">
                        <h3 class="card-title fw-800" style="font-size: 1.15rem;">
                            <i class="fas fa-chart-line mr-2 text-primary"></i> Platform Growth Trend
                        </h3>
                        <div class="card-tools">
                            <ul class="nav nav-pills ml-auto">
                                <li class="nav-item">
                                    <a class="nav-link active py-2 px-3 shadow-sm" href="#revenue-chart" data-toggle="tab" style="font-size: 13px; font-weight: 600;">Booking Trend</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-3 shadow-sm" href="#sales-chart" data-toggle="tab" style="font-size: 13px; font-weight: 600;">Categories</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="tab-content p-0">
                            <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 300px;">
                                <canvas id="revenue-chart-canvas" height="300"></canvas>                         
                            </div>
                            <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 300px;">
                                <canvas id="sales-chart-canvas" height="300"></canvas>                         
                            </div>  
                        </div>
                    </div>
                </div>

                <!-- Latest Bookings Table -->
                <div class="card shadow-lg border-0" style="border-radius: 12px;">
                    <div class="card-header bg-white py-3">
                        <h3 class="card-title fw-800" style="font-size: 1.15rem;">
                            <i class="fas fa-shopping-cart mr-2 text-info"></i> Latest Bookings
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="border-0 px-4">Class</th>
                                        <th class="border-0">Parent</th>
                                        <th class="border-0">Amount</th>
                                        <th class="border-0">Status</th>
                                        <th class="border-0">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($latestBookings as $b): ?>
                                    <tr>
                                        <td class="px-4 font-weight-bold text-dark"><?= esc($b['listing_title']) ?></td>
                                        <td><?= esc($b['parent_name'] ?: 'Guest') ?></td>
                                        <td>₹<?= number_format($b['payment_amount']) ?></td>
                                        <td><span class="badge badge-soft-<?= $b['payment_status'] === 'paid' ? 'success' : 'warning' ?>"><?= ucfirst($b['payment_status']) ?></span></td>
                                        <td class="text-muted"><?= date('M d, H:i', strtotime($b['created_at'] ?? 'now')) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-white text-center py-3">
                        <a href="<?= url('admin/settlements') ?>" class="text-sm font-weight-bold text-primary">View All Transactions <i class="fas fa-arrow-right ml-1"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Pending Items Quick Card -->
                <div class="card bg-white shadow-lg border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header border-bottom py-3">
                        <h3 class="card-title text-dark fw-800" style="font-size: 1.1rem;">
                            <i class="fas fa-exclamation-circle mr-2 text-warning"></i> Pending Reviews
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h6 class="text-muted mb-1" style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Pending KYC</h6>
                                <h4 class="mb-0 font-weight-bold"><?= $stats->pending_kyc ?> <span class="text-sm text-muted font-weight-normal">Providers</span></h4>
                            </div>
                            <a href="<?= url('admin/verifications') ?>" class="btn btn-sm btn-soft-warning px-3 rounded-pill">Review</a>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-0">
                            <div>
                                <h6 class="text-muted mb-1" style="font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">Pending Classes</h6>
                                <h4 class="mb-0 font-weight-bold"><?= $stats->pending_listings ?> <span class="text-sm text-muted font-weight-normal">Listings</span></h4>
                            </div>
                            <a href="<?= url('admin/listings') ?>" class="btn btn-sm btn-soft-primary px-3 rounded-pill">Approve</a>
                        </div>
                    </div>
                </div>

                <!-- Monthly Growth Card -->
                <div class="card bg-primary-soft shadow-lg border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <h6 class="text-primary font-weight-bold mb-3" style="font-size: 12px; text-transform: uppercase;">Growth This Month</h6>
                        <div class="row align-items-center">
                            <div class="col-6 border-right">
                                <h4 class="mb-0 font-weight-bold">₹<?= number_format($stats->revenue_this_month) ?></h4>
                                <p class="text-xs text-muted mb-0">New Revenue</p>
                            </div>
                            <div class="col-6">
                                <h4 class="mb-0 font-weight-bold"><?= $stats->users_this_month ?></h4>
                                <p class="text-xs text-muted mb-0">New Users</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Latest Users List -->
                <div class="card shadow-lg border-0" style="border-radius: 12px;">
                    <div class="card-header bg-white py-3">
                        <h3 class="card-title text-dark fw-800" style="font-size: 1.1rem;">
                            <i class="fas fa-user-plus mr-2 text-success"></i> New Users
                        </h3>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <?php foreach ($latestUsers as $u): ?>
                            <li class="list-group-item px-4 py-3 border-0 d-flex align-items-center">
                                <div class="avatar-sm mr-3 bg-light rounded-circle text-center d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-user text-muted"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-0 text-sm font-weight-bold"><?= esc($u['name']) ?></h6>
                                    <small class="text-muted"><?= $u['role'] == 2 ? 'Provider' : 'Parent' ?></small>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs text-muted"><?= date('m/d', strtotime($u['created_at'] ?? 'now')) ?></span>
                                </div>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
    <!-- /.content -->


<?= $this->endSection() ?>
<?= $this->section('js') ?>

<script src="<?php echo assets_url('admin') ?>/plugins/chart.js/Chart.min.js"></script>

<script>
$(function () {
  'use strict'

  // 1. Booking Trend Chart (Line)
  var trendData = <?= json_encode($trendData) ?>;
  var trendLabels = trendData.map(function(item) { return item.date; });
  var trendCounts = trendData.map(function(item) { return item.count; });

  var revenueChartCanvas = $('#revenue-chart-canvas').get(0).getContext('2d')
  var revenueChartData = {
    labels: trendLabels,
    datasets: [
      {
        label: 'Daily Paid Bookings',
        backgroundColor: 'rgba(60,141,188,0.3)',
        borderColor: 'rgba(60,141,188,0.8)',
        pointRadius: 4,
        pointColor: '#3b8bba',
        pointStrokeColor: 'rgba(60,141,188,1)',
        pointHighlightFill: '#fff',
        pointHighlightStroke: 'rgba(60,141,188,1)',
        data: trendCounts
      }
    ]
  }

  var revenueChartOptions = {
    maintainAspectRatio: false,
    responsive: true,
    legend: { display: true },
    scales: {
      xAxes: [{ gridLines: { display: false } }],
      yAxes: [{ gridLines: { display: false }, ticks: { beginAtZero: true, stepSize: 1 } }]
    }
  }

  new Chart(revenueChartCanvas, {
    type: 'line',
    data: revenueChartData,
    options: revenueChartOptions
  })

  // 2. Category Distribution Chart (Donut)
  var catData = <?= json_encode($categoryCounts) ?>;
  var catLabels = catData.map(function(item) { return item.name; });
  var catCounts = catData.map(function(item) { return item.count; });

  var salesChartCanvas = $('#sales-chart-canvas').get(0).getContext('2d')
  var salesData = {
    labels: catLabels,
    datasets: [
      {
        data: catCounts,
        backgroundColor: ['#6366f1', '#10b981', '#f59e0b', '#ef4444', '#0ea5e9', '#64748b', '#8b5cf6', '#ec4899', '#14b8a6', '#f97316']
      }
    ]
  }
  var salesOptions = {
    legend: { display: true, position: 'right' },
    maintainAspectRatio: false,
    responsive: true
  }

  new Chart(salesChartCanvas, {
    type: 'doughnut',
    data: salesData,
    options: salesOptions
  })
})
</script>

<?=  $this->endSection() ?>