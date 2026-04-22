<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 fw-900 border-left border-4 border-success pl-3">Advanced Analytics</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Filters Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <form method="get" class="card-body p-4">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="small fw-bold text-uppercase text-muted">Date Range</label>
                        <div class="input-group">
                            <input type="date" name="start_date" class="form-control rounded-left" value="<?= $filters['start_date'] ?>">
                            <input type="date" name="end_date" class="form-control rounded-right" value="<?= $filters['end_date'] ?>">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-uppercase text-muted">Category</label>
                        <select name="category" class="form-control select2">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat->id ?>" <?= $filters['category'] == $cat->id ? 'selected' : '' ?>><?= esc($cat->name) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="small fw-bold text-uppercase text-muted">Location (State)</label>
                        <select name="state" class="form-control select2">
                            <option value="">All States</option>
                            <?php foreach ($states as $st): ?>
                                <option value="<?= $st->state ?>" <?= $filters['state'] == $st->state ? 'selected' : '' ?>><?= esc($st->state) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-success btn-block rounded-pill fw-bold">
                            <i class="fas fa-filter mr-1"></i> Apply Analytics Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="row">
            <!-- Growth Chart -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h3 class="card-title fw-bold text-dark"><i class="fas fa-chart-line mr-2 text-success"></i> Growth & Trends</h3>
                    </div>
                    <div class="card-body">
                        <canvas id="growthChart" height="280"></canvas>
                    </div>
                </div>
            </div>
            
            <!-- Category Mix -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 h-100">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h3 class="card-title fw-bold text-dark"><i class="fas fa-chart-pie mr-2 text-warning"></i> Category Mix</h3>
                    </div>
                    <div class="card-body d-flex align-items-center">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- User Distribution -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h3 class="card-title fw-bold text-dark"><i class="fas fa-users mr-2 text-primary"></i> User Breakdown</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted uppercase small fw-bold">
                                <tr>
                                    <th class="px-4">User Type</th>
                                    <th>Total Users</th>
                                    <th class="px-4 text-right">Growth %</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-4 fw-bold">Parents</td>
                                    <td><?= number_format($user_stats['parents']) ?></td>
                                    <td class="px-4 text-right text-success small">+<?= $user_stats['parent_growth'] ?>%</td>
                                </tr>
                                <tr>
                                    <td class="px-4 fw-bold">Providers</td>
                                    <td><?= number_format($user_stats['providers']) ?></td>
                                    <td class="px-4 text-right text-success small">+<?= $user_stats['provider_growth'] ?>%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Provider Performance -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4 mb-4">
                    <div class="card-header bg-white py-3 border-bottom-0">
                        <h3 class="card-title fw-bold text-dark"><i class="fas fa-star mr-2 text-gold"></i> Top Providers</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted uppercase small fw-bold">
                                <tr>
                                    <th class="px-4">Provider Name</th>
                                    <th>Classes</th>
                                    <th class="px-4 text-right">Average Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($top_providers as $p): ?>
                                <tr>
                                    <td class="px-4 fw-bold"><?= esc($p->name) ?></td>
                                    <td><?= $p->listing_count ?></td>
                                    <td class="px-4 text-right">
                                        <div class="badge badge-warning rounded-pill px-2"><?= number_format($p->avg_rating, 1) ?> <i class="fas fa-star text-white ml-1"></i></div>
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
</section>

<style>
.fw-900 { font-weight: 900; }
.rounded-4 { border-radius: 1.25rem !important; }
.text-gold { color: #f9a05e; }
.rounded-left { border-top-right-radius: 0 !important; border-bottom-right-radius: 0 !important; }
.rounded-right { border-top-left-radius: 0 !important; border-bottom-left-radius: 0 !important; }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
$(function() {
    // 1. Growth Chart (Daily Bookings)
    const ctxGrowth = document.getElementById('growthChart').getContext('2d');
    new Chart(ctxGrowth, {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($trend_data, 'date')) ?>,
            datasets: [{
                label: 'Bookings',
                data: <?= json_encode(array_column($trend_data, 'count')) ?>,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointBackgroundColor: '#28a745'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                x: { grid: { display: false } }
            }
        }
    });

    // 2. Category Pie Chart
    const ctxCat = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCat, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($category_counts, 'name')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($category_counts, 'count')) ?>,
                backgroundColor: ['#4f46e5', '#f9a05e', '#10b981', '#ef4444', '#f59e0b', '#8b5cf6', '#ec4899'],
                borderWidth: 0,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { weight: 'bold' } } }
            },
            cutout: '70%'
        }
    });
});
</script>
<?= $this->endSection() ?>
