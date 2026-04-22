<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<!-- Content Header (Page header) -->
<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1><?php echo lang('App.users') ?></h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?php echo url('/') ?>"><?php echo lang('App.home') ?></a></li>
          <li class="breadcrumb-item active"><?php echo lang('App.users') ?></li>
        </ol>
      </div>
    </div>
  </div><!-- /.container-fluid -->
</section>

<!-- Main content -->
<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
          <div class="card-header border-0 bg-white py-3 d-flex align-items-center">
            <h3 class="card-title fw-900 text-dark"><?= lang('App.users') ?></h3>
            <div class="ml-auto">
                <?php if (hasPermissions('users_add')): ?>
                  <a href="<?= url('users/add') ?>" class="btn btn-primary rounded-pill px-4 fw-bold">
                    <i class="fa fa-plus mr-2"></i> <?= lang('App.new_user') ?>
                  </a>
                <?php endif ?>
            </div>
          </div>
          
          <div class="card-body p-0">
            <div class="table-responsive">
                <table id="example1" class="table table-hover align-middle mb-0">
                  <thead class="bg-light">
                  <tr>
                    <th class="px-4 py-3 border-0"><?= lang('App.id') ?></th>
                    <th class="py-3 border-0 text-center"><?= lang('App.user_image') ?></th>
                    <th class="py-3 border-0"><?= lang('App.user_name') ?></th>
                    <th class="py-3 border-0"><?= lang('App.user_email') ?></th>
                    <th class="py-3 border-0"><?= lang('App.user_role') ?></th>
                    <th class="py-3 border-0"><?= lang('App.user_status') ?></th>
                    <th class="px-4 py-3 border-0 text-right"><?= lang('App.action') ?></th>
                  </tr>
                  </thead>
                  <tbody>
                  <?php foreach ($users as $row): ?>
                    <tr class="align-middle">
                      <td width="80" class="px-4 text-muted">#<?= $row->id ?></td>
                      <td width="70" class="text-center">
                        <img src="<?= userProfile($row->id) ?>" width="40" height="40" alt="" class="rounded-circle shadow-sm border">
                      </td>
                      <td>
                        <span class="fw-bold text-dark"><?= $row->name ?></span>
                      </td>
                      <td class="text-muted"><?= $row->email ?></td>
                      <td>
                        <span class="badge badge-light border px-3 py-2 rounded-pill fw-600">
                            <?= ucfirst(model('App\Models\RoleModel')->getRowById($row->role, 'title')) ?>
                        </span>
                      </td>
                      <td>
                        <?php if (logged('id')!==$row->id && $row->id != 1): ?>
                          <?php if ($row->status == 1): ?>
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="suspendUser(<?= $row->id ?>, '<?= esc($row->name) ?>')">
                              <i class="fas fa-user-slash mr-1"></i> Suspend
                            </button>
                          <?php else: ?>
                            <button class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="reinstateUser(<?= $row->id ?>, '<?= esc($row->name) ?>')">
                              <i class="fas fa-user-check mr-1"></i> Reinstate
                            </button>
                            <?php if($row->status_remarks): ?>
                              <div class="small text-muted mt-1 italic" style="max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= esc($row->status_remarks) ?>">
                                <i class="fas fa-info-circle mr-1"></i> <?= esc($row->status_remarks) ?>
                              </div>
                            <?php endif; ?>
                          <?php endif; ?>
                        <?php else: ?>
                          <span class="badge badge-success px-3 py-2 rounded-pill"><?= lang('App.user_active') ?></span>
                        <?php endif ?>
                      </td>
                      <td class="px-4 text-right">
                        <div class="btn-group shadow-sm rounded-pill overflow-hidden bg-white">
                            <?php if (hasPermissions('users_edit')): ?>
                              <a href="<?= url('users/edit/'.$row->id) ?>" class="btn btn-sm btn-white border-right" title="<?= lang('App.edit_user') ?>"><i class="fas fa-edit text-primary"></i></a>
                            <?php endif ?>
                            <?php if (hasPermissions('users_view')): ?>
                              <a href="<?= url('users/view/'.$row->id) ?>" class="btn btn-sm btn-white border-right" title="<?= lang('App.view_user') ?>"><i class="fa fa-eye text-info"></i></a>
                            <?php endif ?>
                            <?php if (hasPermissions('users_delete')): ?>
                              <?php if ($row->id!=1 && logged('id')!=$row->id): ?>
                                <a href="<?= url('users/delete/'.$row->id) ?>" class="btn btn-sm btn-white" onclick="return confirm('Do you really want to delete this user ?')" title="<?= lang('App.delete_user') ?>"><i class="fa fa-trash text-danger"></i></a>
                              <?php else: ?>
                                <button class="btn btn-sm btn-white" disabled><i class="fa fa-trash text-muted"></i></button>
                              <?php endif ?>
                            <?php endif ?>
                        </div>
                      </td>
                    </tr>
                  <?php endforeach ?>
                  </tbody>
                </table>
            </div>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </div>
  <!-- /.container-fluid -->
</section>

<!-- ══ SUSPENSION MODAL ══════════════════════════════════════════ -->
<div class="modal fade" id="suspendModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header bg-danger text-white border-0">
        <h5 class="modal-title fw-bold"><i class="fas fa-user-slash mr-2"></i> Suspend User</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="suspendForm">
        <div class="modal-body p-4">
          <input type="hidden" name="id" id="suspendUserId">
          <input type="hidden" name="status" value="0">
          <p class="mb-3">Are you sure you want to suspend <strong id="suspendUserName"></strong>? They will not be able to log in to the platform.</p>
          <div class="form-group">
            <label class="text-xs text-uppercase fw-900 opacity-50">Reason for Suspension (Required)</label>
            <textarea name="remarks" class="form-control rounded-3" rows="3" placeholder="e.g. Policy violation, suspicious activity..." required minlength="5"></textarea>
          </div>
        </div>
        <div class="modal-footer border-0 p-4 pt-0">
          <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold" id="confirmSuspendBtn">Confirm Suspension</button>
        </div>
      </form>
    </div>
  </div>
</div>
    <!-- /.content -->



    <?= $this->endSection() ?>
<?= $this->section('js') ?>

<script>
window.suspendUser = (id, name) => {
  $('#suspendUserId').val(id);
  $('#suspendUserName').text(name);
  $('#suspendModal').modal('show');
}

window.reinstateUser = (id, name) => {
  if (confirm(`Are you sure you want to REINSTATE user ${name}?`)) {
    $.post('<?php echo url('admin/users/change_status') ?>/'+id, {
      status: 'true',
      remarks: ''
    }, (res) => {
      if (res.success) {
        toastr.success(res.message);
        setTimeout(() => location.reload(), 1000);
      } else {
        toastr.error(res.message);
      }
    });
  }
}

$('#suspendForm').on('submit', function(e) {
  e.preventDefault();
  const id = $('#suspendUserId').val();
  const btn = $('#confirmSuspendBtn');
  btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

  $.post('<?php echo url('admin/users/change_status') ?>/'+id, $(this).serialize(), function(res) {
    if (res.success) {
      toastr.success(res.message);
      location.reload();
    } else {
      toastr.error(res.message);
      btn.prop('disabled', false).text('Confirm Suspension');
    }
  });
});
</script>
<?=  $this->endSection() ?>
