<?= $this->extend('admin/layout/default') ?>

<?= $this->section('content') ?>

<section class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1>Subcategory Management</h1>
      </div>
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="<?= url('admin/dashboard') ?>">Home</a></li>
          <li class="breadcrumb-item active">Subcategories</li>
        </ol>
      </div>
    </div>
  </div>
</section>

<section class="content">
  <div class="container-fluid">
    
    <div class="mb-3">
        <button class="btn btn-primary rounded-pill px-4 shadow-sm" onclick="addSubcategory()">
            <i class="fas fa-plus mr-2"></i> Add New Subcategory
        </button>
    </div>

    <?php if(session()->getFlashdata('success')): ?>
      <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif; ?>

    <?php if(session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
    <?php endif; ?>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
      <div class="card-header border-0 bg-white py-3">
        <h3 class="card-title fw-900 text-dark">All Subcategories</h3>
      </div>
      
      <div class="card-body p-0">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0" id="subcategoriesTable">
            <thead class="bg-light">
              <tr>
                <th class="px-4 py-3 border-0">ID</th>
                <th class="py-3 border-0">Subcategory</th>
                <th class="py-3 border-0">Parent Category</th>
                <th class="py-3 border-0">Status</th>
                <th class="px-4 py-3 border-0 text-right">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach($subcategories as $s): ?>
              <tr>
                <td class="px-4 text-muted">#<?= $s->id ?></td>
                <td>
                    <div class="fw-bold"><?= esc($s->name) ?></div>
                    <small class="text-muted"><?= esc($s->slug) ?></small>
                </td>
                <td class="text-dark fw-600"><?= esc($s->category_name) ?></td>
                <td>
                  <span class="badge rounded-pill px-3 py-2 <?= $s->status == 'active' ? 'bg-success' : 'bg-secondary' ?>">
                    <?= strtoupper(esc($s->status)) ?>
                  </span>
                </td>
                <td class="px-4 text-right">
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3 mr-2" 
                      onclick='editSubcategory(<?= json_encode($s) ?>)'>
                      <i class="fas fa-edit"></i> Edit
                    </button>
                    <a href="<?= url('admin/subcategories/delete/' . $s->id) ?>" 
                       class="btn btn-sm btn-outline-danger rounded-pill px-3"
                       onclick="return confirm('Are you sure you want to delete this subcategory?')">
                      <i class="fas fa-trash"></i> Delete
                    </a>
                  </div>
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

<!-- Subcategory Modal -->
<div class="modal fade" id="subcategoryModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg rounded-4">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-900" id="modalTitle">Add Subcategory</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form action="<?= url('admin/subcategories/save') ?>" method="POST">
        <div class="modal-body py-4">
          <input type="hidden" name="id" id="subId">
          
          <div class="form-group mb-4">
            <label class="fw-700 small text-uppercase text-muted">Parent Category</label>
            <select name="category_id" id="subCatId" class="form-control rounded-3" required>
              <option value="">-- Select Category --</option>
              <?php foreach($categories as $cat): ?>
                <option value="<?= $cat->id ?>"><?= esc($cat->name) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group mb-4">
            <label class="fw-700 small text-uppercase text-muted">Subcategory Name</label>
            <input type="text" name="name" id="subName" class="form-control rounded-3" required placeholder="e.g. Piano, Football">
          </div>

          <div class="form-group mb-4">
            <label class="fw-700 small text-uppercase text-muted">Description (Optional)</label>
            <textarea name="description" id="subDesc" class="form-control rounded-3" rows="3" placeholder="Brief description..."></textarea>
          </div>

          <div class="form-group">
            <label class="fw-700 small text-uppercase text-muted">Status</label>
            <select name="status" id="subStatus" class="form-control rounded-3">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-link text-muted fw-600" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary rounded-pill px-4">Save Subcategory</button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
  function addSubcategory() {
    $('#subId').val('');
    $('#subCatId').val('');
    $('#subName').val('');
    $('#subDesc').val('');
    $('#subStatus').val('active');
    $('#modalTitle').text('Add Subcategory');
    $('#subcategoryModal').modal('show');
  }

  function editSubcategory(sub) {
    $('#subId').val(sub.id);
    $('#subCatId').val(sub.category_id);
    $('#subName').val(sub.name);
    $('#subDesc').val(sub.description);
    $('#subStatus').val(sub.status);
    $('#modalTitle').text('Edit Subcategory');
    $('#subcategoryModal').modal('show');
  }

  $(function() {
    $('#subcategoriesTable').DataTable({
      "responsive": true,
      "autoWidth": false,
      "order": [[2, "asc"], [1, "asc"]]
    });
  });
</script>
<?= $this->endSection() ?>
