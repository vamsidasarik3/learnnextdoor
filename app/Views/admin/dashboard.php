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
            <div class="col-sm-6 text-sm-right mt-3 mt-sm-0">
                <span class="badge badge-soft-primary px-3 py-2">
                    <i class="fas fa-shield-alt mr-2"></i> Security Level: High
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
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
                <div class="card card-outline card-primary shadow-lg" style="min-height: 500px;">
                    <div class="card-header d-flex align-items-center justify-content-between py-4 bg-white border-bottom">
                        <h3 class="card-title fw-800" style="font-size: 1.25rem;">
                            <i class="fas fa-chart-line mr-2 text-primary"></i> Platform Growth Trend
                        </h3>
                        <div class="card-tools">
                            <ul class="nav nav-pills ml-auto">
                                <li class="nav-item">
                                    <a class="nav-link active py-2 px-4 shadow-sm" href="#revenue-chart" data-toggle="tab" style="font-size: 14px; font-weight: 600;">Booking Trend</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link py-2 px-4 shadow-sm" href="#sales-chart" data-toggle="tab" style="font-size: 14px; font-weight: 600;">Categories</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="tab-content p-0">
                            <!-- Morris chart - Sales -->
                            <div class="chart tab-pane active" id="revenue-chart" style="position: relative; height: 380px;">
                                <canvas id="revenue-chart-canvas" height="380"></canvas>                         
                            </div>
                            <div class="chart tab-pane" id="sales-chart" style="position: relative; height: 380px;">
                                <canvas id="sales-chart-canvas" height="380"></canvas>                         
                            </div>  
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card bg-white h-100 shadow-lg border-0" style="border-radius: var(--radius-lg);">
                    <div class="card-header border-bottom py-4">
                        <h3 class="card-title text-dark fw-800" style="font-size: 1.25rem;">
                            <i class="fas fa-microchip mr-2 text-success"></i> System Health
                        </h3>
                    </div>
                    <div class="card-body p-4 pt-1">
                        <div class="system-stats mt-4">
                            <div class="mb-5">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-xs font-weight-bold text-uppercase tracking-wider">Active Sessions</span>
                                    <span class="text-xs text-primary font-weight-bold">4% Loaded</span>
                                </div>
                                <div class="progress progress-xxs" style="height: 10px; border-radius: 20px; background: #f1f5f9;">
                                    <div class="progress-bar bg-gradient-primary rounded-pill shadow-sm" style="width: 4%"></div>
                                </div>
                            </div>
                            <div class="mb-5">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-xs font-weight-bold text-uppercase tracking-wider">Server CPU Load</span>
                                    <span class="text-xs text-success font-weight-bold">12% / Stable</span>
                                </div>
                                <div class="progress progress-xxs" style="height: 10px; border-radius: 20px; background: #f1f5f9;">
                                    <div class="progress-bar bg-gradient-success rounded-pill shadow-sm" style="width: 12%"></div>
                                </div>
                            </div>
                            <div class="mt-5">
                                <h6 class="text-xs text-uppercase font-weight-bold text-muted mb-4 tracking-wider">Infrastructure Details</h6>
                                <div class="p-4 rounded-xl bg-light border-0 d-flex flex-column gap-3" style="border-radius: 16px; background: #f8fafc;">
                                    <div class="d-flex justify-content-between align-items-center py-1">
                                        <span class="text-sm text-secondary font-weight-medium">Database</span>
                                        <span class="text-sm font-weight-bold text-dark">MySQL 8.0</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-1 border-top" style="border-top: 1px dashed #e2e8f0 !important;">
                                        <span class="text-sm text-secondary font-weight-medium pt-2">PHP Version</span>
                                        <span class="text-sm font-weight-bold text-dark pt-2">8.2.0</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center py-1 border-top" style="border-top: 1px dashed #e2e8f0 !important;">
                                        <span class="text-sm text-secondary font-weight-medium pt-2">Uptime</span>
                                        <span class="text-sm font-weight-bold text-dark pt-2">99.9%</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center pt-2">
                                        <span class="text-sm text-secondary font-weight-medium">App Node</span>
                                        <span class="badge badge-soft-success px-3 py-1">Healthy</span>
                                    </div>
                                </div>
                            </div>
                        </div>
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