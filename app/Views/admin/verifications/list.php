<?= $this->extend('admin/layout/default') ?>

<?= $this->section('content') ?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Provider Verifications</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?= url('admin/dashboard') ?>">Home</a></li>
          <li class="breadcrumb-item active">Verifications</li>
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
        <h3 class="card-title fw-900 text-dark">Provider Applications</h3>
      </div>
      
      <div class="card-body p-0">
        <div class="table-responsive">
          <table id="verificationsTable" class="table table-hover align-middle mb-0">
            <thead class="bg-light">
              <tr>
                <th class="px-4 py-3 border-0">Provider Name</th>
                <th class="py-3 border-0">Contact Info</th>
                <th class="py-3 border-0 text-center">Docs</th>
                <th class="py-3 border-0">Submitted date</th>
                <th class="py-3 border-0 text-center">Status</th>
                <th class="px-4 py-3 border-0 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($providers as $p): ?>
              <tr>
                <td class="px-4">
                  <div class="d-flex align-items-center">
                    <div class="avatar mr-3 bg-primary rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px; font-weight: bold;">
                        <?= strtoupper(substr($p->name, 0, 1)) ?>
                    </div>
                    <div class="d-flex flex-column">
                        <span class="fw-bold text-dark"><?= esc($p->name) ?></span>
                        <small class="text-muted">User ID: #<?= $p->id ?></small>
                    </div>
                  </div>
                </td>
                <td>
                  <div class="d-flex flex-column">
                    <span class="small"><i class="fas fa-envelope text-muted mr-1"></i> <?= esc($p->email) ?></span>
                    <span class="small">
                        <i class="fas fa-phone text-muted mr-1"></i> <?= esc($p->phone) ?>
                        <?php if($p->phone_verified): ?>
                            <i class="fas fa-check-circle text-success ml-1" style="font-size: 0.8rem;" title="Verified"></i>
                        <?php endif; ?>
                    </span>
                  </div>
                </td>
                <td class="text-center">
                    <span class="badge badge-info px-3 py-2"><?= $p->doc_count ?></span>
                </td>
                <td>
                  <span class="text-muted small"><?= $p->provider_submitted_at ? date('d M Y, h:i A', strtotime($p->provider_submitted_at)) : '-' ?></span>
                </td>
                <td class="text-center">
                    <?php 
                        $badge = 'secondary';
                        if($p->provider_verification_status === 'approved') $badge = 'success';
                        elseif($p->provider_verification_status === 'pending') $badge = 'warning';
                        elseif($p->provider_verification_status === 'rejected') $badge = 'danger';
                    ?>
                    <span class="badge badge-<?= $badge ?> px-3 py-2 text-uppercase"><?= $p->provider_verification_status ?></span>
                </td>
                <td class="px-4 text-right">
                   <a href="<?= url('admin/provider/' . $p->id) ?>" class="btn btn-sm btn-primary rounded-pill px-3">
                      <i class="fas fa-search mr-1"></i> Review
                   </a>
                </td>
              </tr>
              <?php endforeach; ?>
              <?php if(empty($providers)): ?>
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted">
                        <i class="fas fa-user-clock fa-3x mb-3 d-block"></i>
                        No provider applications found.
                    </td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->

  </div><!-- /.container-fluid -->
</section>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
  $(function () {
    $("#verificationsTable").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "order": [[3, "desc"]]
    });
  });
</script>
<?= $this->endSection() ?>
