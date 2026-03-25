<?= $this->extend('frontend/layout/base') ?>

<?= $this->section('content') ?>
<!-- ══ HERO SECTION ══════════════════════════════════════ -->
<section class="cnd-provider-hero py-5" style="background: linear-gradient(135deg, #7C4DFF 0%, #FF68B4 100%);">
  <div class="container py-4">
    <div class="row align-items-center">
      <div class="col-lg-8 text-white">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb cnd-breadcrumb">
            <li class="breadcrumb-item"><a href="<?= base_url('provider/listings') ?>" class="text-white opacity-75">Dashboard</a></li>
            <li class="breadcrumb-item active text-white" aria-current="page">Instructors</li>
          </ol>
        </nav>
        <h1 class="display-5 fw-bold mb-2">My Instructors</h1>
        <p class="lead opacity-90 mb-0">Manage and verify instructors for your classes. Verified instructors can be reused across multiple batches.</p>
      </div>
      <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
          <button class="btn btn-white rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#instructorModal">
              <i class="bi bi-person-plus-fill me-2"></i>Add Instructor
          </button>
      </div>
    </div>
  </div>
</section>

<!-- ══ MAIN CONTENT ══════════════════════════════════════ -->
<section class="py-5 bg-light min-vh-100">
  <div class="container">
     <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
           <table class="table table-hover align-middle mb-0">
              <thead class="bg-white border-bottom">
                 <tr>
                    <th class="px-4 py-3 small text-uppercase fw-bold text-muted">Instructor</th>
                    <th class="px-4 py-3 small text-uppercase fw-bold text-muted">Experience</th>
                    <th class="px-4 py-3 small text-uppercase fw-bold text-muted">Social Link</th>
                    <th class="px-4 py-3 small text-uppercase fw-bold text-muted">KYC Status</th>
                    <th class="px-4 py-3 small text-uppercase fw-bold text-muted text-end">Action</th>
                 </tr>
              </thead>
              <tbody>
                 <?php if(empty($instructors)): ?>
                    <tr>
                       <td colspan="5" class="text-center py-5 text-muted">
                          <i class="bi bi-people display-4 opacity-25 d-block mb-3"></i>
                          No instructors added yet.
                       </td>
                    </tr>
                 <?php endif; ?>
                 <?php foreach($instructors as $inst): ?>
                    <tr>
                       <td class="px-4 py-3">
                          <div class="d-flex align-items-center gap-3">
                             <div class="bg-soft-pink text-pink rounded-circle p-2 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                                <i class="bi bi-person-fill"></i>
                             </div>
                             <div>
                                <div class="fw-bold"><?= esc($inst->name) ?></div>
                                <div class="x-small text-muted">Added <?= date('M d, Y', strtotime($inst->created_at)) ?></div>
                             </div>
                          </div>
                       </td>
                       <td class="px-4 py-3">
                          <div class="small text-truncate" style="max-width: 200px;" title="<?= esc($inst->experience) ?>">
                             <?= esc($inst->experience ?: 'No details shared') ?>
                          </div>
                       </td>
                       <td class="px-4 py-3">
                          <?php if($inst->social_links): ?>
                             <a href="<?= esc($inst->social_links) ?>" target="_blank" class="text-pink small"><i class="bi bi-link-45deg"></i> Portfolio</a>
                          <?php else: ?>
                             <span class="text-muted small">Not shared</span>
                          <?php endif; ?>
                       </td>
                       <td class="px-4 py-3">
                          <?php 
                             $status = $inst->kyc_status;
                             $badge = 'bg-warning-soft text-warning';
                             if($status === 'verified') $badge = 'bg-success-soft text-success';
                             if($status === 'rejected') $badge = 'bg-danger-soft text-danger';
                          ?>
                          <span class="badge <?= $badge ?> rounded-pill px-3 py-2 text-uppercase fs-tiny">
                             <?= $status ?>
                          </span>
                       </td>
                       <td class="px-4 py-3 text-end">
                          <button class="btn btn-sm btn-light rounded-pill px-3" onclick='editInstructor(<?= json_encode($inst) ?>)'>
                             <i class="bi bi-pencil-square"></i>
                          </button>
                       </td>
                    </tr>
                 <?php endforeach; ?>
              </tbody>
           </table>
        </div>
     </div>
  </div>
</section>

<!-- Instructor Modal -->
<div class="modal fade" id="instructorModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow rounded-4">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold" id="modalTitle">Add New Instructor</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="instructorForm">
        <div class="modal-body p-4">
           <input type="hidden" name="id" id="inst_id">
           <div class="mb-3">
              <label class="form-label small fw-bold">Full Name <span class="text-danger">*</span></label>
              <input type="text" name="name" id="inst_name" class="form-control rounded-3" required>
           </div>
           <div class="mb-3">
              <label class="form-label small fw-bold">Experience Details <span class="text-danger">*</span></label>
              <textarea name="experience" id="inst_exp" class="form-control rounded-3" rows="3" required placeholder="Tell parents about their qualifications..."></textarea>
           </div>
           <div class="mb-3">
              <label class="form-label small fw-bold">Social/LinkedIn URL</label>
              <input type="url" name="social_links" id="inst_links" class="form-control rounded-3" placeholder="https://...">
           </div>
           <div class="mb-0">
              <label class="form-label small fw-bold">KYC Document (Optional)</label>
              <input type="file" name="kyc_doc" id="inst_kyc" class="form-control rounded-3" accept=".pdf,image/*">
              <div class="x-small text-muted mt-1">Proof of ID or Certifications (ID, PAN, Passport)</div>
           </div>
        </div>
        <div class="modal-footer border-0 pt-0 p-4">
          <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-pink rounded-pill px-4 fw-bold" id="btnSaveInst">Save Instructor</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.bg-success-soft { background: rgba(46, 204, 113, 0.1); }
.bg-warning-soft { background: rgba(241, 196, 15, 0.1); }
.bg-danger-soft { background: rgba(231, 76, 60, 0.1); }
.bg-soft-pink { background: rgba(255, 104, 180, 0.1); }
.fs-tiny { font-size: 0.65rem; }
.btn-white { background: #fff; color: #7C4DFF; border: none; }
.btn-white:hover { background: #f8f9fa; color: #3F3590; }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
  const form = document.getElementById('instructorForm');
  const modal = new bootstrap.Modal(document.getElementById('instructorModal'));
  
  window.editInstructor = function(inst) {
      document.getElementById('modalTitle').innerText = 'Edit Instructor';
      document.getElementById('inst_id').value = inst.id;
      document.getElementById('inst_name').value = inst.name;
      document.getElementById('inst_exp').value = inst.experience;
      document.getElementById('inst_links').value = inst.social_links;
      modal.show();
  };

  document.getElementById('instructorModal').addEventListener('hidden.bs.modal', function () {
      form.reset();
      document.getElementById('modalTitle').innerText = 'Add New Instructor';
      document.getElementById('inst_id').value = '';
  });

  form.addEventListener('submit', async function(e){
      e.preventDefault();
      const btn = document.getElementById('btnSaveInst');
      btn.disabled = true;
      btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving...';

      const formData = new FormData(this);
      try {
          const res = await fetch('<?= base_url('provider/api/instructors/save') ?>', {
              method: 'POST',
              body: formData
          });
          const json = await res.json();
          if(json.success) {
              alert(json.message);
              window.location.reload();
          } else {
              alert(json.message || 'Error saving instructor');
              btn.disabled = false;
              btn.innerHTML = 'Save Instructor';
          }
      } catch(e) {
          console.error(e);
          alert('Network error');
          btn.disabled = false;
          btn.innerHTML = 'Save Instructor';
      }
  });
</script>
<?= $this->endSection() ?>
