<?= $this->extend('frontend/layout/provider_base') ?>

<?= $this->section('css') ?>
<style>
.stepper-title { font-size: 2rem; font-weight: 800; color: #111827; letter-spacing: -0.025em; margin-bottom: 0.25rem; }
.filter-nav { border: 1px solid #E5E7EB; }
.filter-pill { border: none; background: #F3F4F6; padding: 0.4rem 1.25rem; font-size: 0.75rem; font-weight: 700; color: #4B5563; border-radius: 9999px; transition: all 0.2s; text-transform: uppercase; }
.filter-pill:hover { background: #E5E7EB; color: #111827; }
.filter-pill.active { background: #4F46E5; color: white; box-shadow: 0 4px 6px -1px rgba(79, 70, 229, 0.2); }

.instructor-card { transition: transform 0.2s, box-shadow 0.2s; border-color: #F1F5F9 !important; }
.instructor-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -10px rgba(0, 0, 0, 0.1); }

/* Status Badges */
.status-badge-pill { padding: 4px 12px; border-radius: 9999px; font-size: 11px; font-weight: 700; display: inline-flex; align-items: center; }
.status-verified { background-color: #DCFCE7; color: #15803D; }
.status-pending { background-color: #FEF3C7; color: #B45309; }
.status-rejected { background-color: #FEE2E2; color: #B91C1C; }
.status-missing { background-color: #F1F5F9; color: #475569; }

.btn-light-primary { background-color: #EEF2FF; color: #4F46E5; padding: 0.625rem; border-radius: 8px; transition: all 0.2s; }
.btn-light-primary:hover { background-color: #E0E7FF; }

.fw-600 { font-weight: 600 !important; }

.btn-primary-provider {
    background-color: #4F46E5 !important;
    border: none !important;
    font-weight: 600;
    font-size: 0.9375rem;
    padding: 0.625rem 1.75rem;
    border-radius: 5px;
    color: white !important;
    transition: all 0.2s;
}
.btn-primary-provider:hover { background-color: #4338CA !important; transform: translateY(-1px); }

.form-label { font-size: 0.875rem; font-weight: 600; color: #374151; margin-bottom: 0.5rem; display: block; }
.form-control { border-radius: 8px; border: 1px solid #D1D5DB; padding: 0.625rem 0.875rem; font-size: 0.9375rem; }
.form-control:focus { border-color: #4F46E5; box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); outline: none; }

.hover-opacity-100 { transition: opacity 0.2s; }
.hover-opacity-100:hover { opacity: 1 !important; color: #4F46E5 !important; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="instructors-header mb-4 mt-2">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="stepper-title mb-1">Manage Instructors</h1>
            <p class="text-muted small mb-0">Add and manage instructors for your class</p>
        </div>
        <div class="col-md-6 text-md-end mt-3 mt-md-0">
            <button class="btn btn-primary-provider shadow-sm" data-bs-toggle="modal" data-bs-target="#instructorModal">
                <i class="bi bi-plus-lg me-2"></i> Add New Instructor
            </button>
        </div>
    </div>
</div>

<!-- Filter Navigation -->
<div class="filter-nav mb-5 bg-white p-3 rounded-4 border d-flex align-items-center gap-3 shadow-sm">
    <span class="text-secondary small fw-bold">Filter by status:</span>
    <div class="d-flex flex-wrap gap-2">
        <button class="filter-pill active" data-status="all">All</button>
        <button class="filter-pill" data-status="verified">VERIFIED</button>
        <button class="filter-pill" data-status="pending">PENDING</button>
        <button class="filter-pill" data-status="rejected">REJECTED</button>
        <button class="filter-pill" data-status="not_uploaded">NOT UPLOADED</button>
    </div>
</div>

<!-- Instructors Grid -->
<div id="instructorsWrapper">
    <?php if(empty($instructors)): ?>
        <div class="text-center py-5">
            <div class="bg-white rounded-circle shadow-sm mx-auto d-flex align-items-center justify-content-center mb-4" style="width: 120px; height: 120px; border: 1px solid var(--provider-border);">
                <i class="bi bi-people fs-1 text-muted opacity-25"></i>
            </div>
            <h3 class="fw-bold">No instructors found</h3>
            <p class="text-muted">You haven't added any instructors yet.</p>
            <button class="btn-next mt-3" data-bs-toggle="modal" data-bs-target="#instructorModal">Add Your First Instructor</button>
        </div>
    <?php else: ?>
        <div class="row g-4" id="instructorsGrid">
            <?php foreach($instructors as $inst): ?>
                <?php 
                    $status = strtolower($inst->kyc_status ?: 'not_uploaded');
                    $statusConfig = [
                        'verified' => ['class' => 'status-verified', 'icon' => 'bi-check2-circle', 'label' => 'Verified'],
                        'pending' => ['class' => 'status-pending', 'icon' => 'bi-clock-history', 'label' => 'Pending'],
                        'rejected' => ['class' => 'status-rejected', 'icon' => 'bi-x-circle', 'label' => 'Rejected'],
                        'not_uploaded' => ['class' => 'status-missing', 'icon' => 'bi-exclamation-circle', 'label' => 'Not Uploaded']
                    ][$status] ?? ['class' => 'status-missing', 'icon' => 'bi-question-circle', 'label' => strtoupper($status)];
                ?>
                <div class="col-md-6 col-lg-4 instructor-item" data-status="<?= $status ?>">
                    <div class="instructor-card p-4 bg-white border rounded-4 shadow-sm h-100 position-relative">
                        <!-- Top Icons -->
                        <div class="position-absolute top-0 end-0 p-3 d-flex gap-2">
                            <button class="btn btn-link text-secondary p-0 shadow-none border-0 hover-opacity-100" onclick='editInstructor(<?= json_encode($inst) ?>)' title="Edit">
                                <i class="bi bi-pencil-square fs-5"></i>
                            </button>
                            <button class="btn btn-link text-secondary p-0 shadow-none border-0 hover-opacity-100" onclick="deleteInstructor(<?= $inst->id ?>)" title="Delete">
                                <i class="bi bi-trash fs-5"></i>
                            </button>
                        </div>

                        <h3 class="h5 fw-bold mb-1 text-dark"><?= esc($inst->name) ?></h3>
                        <p class="text-muted small mb-4"><?= esc($inst->experience ?: 'Professional expertise details') ?></p>

                        <div class="mb-4">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="text-muted small fw-600">Document Status</span>
                                <div class="status-badge-pill <?= $statusConfig['class'] ?>">
                                    <i class="bi <?= $statusConfig['icon'] ?> me-1"></i> <?= $statusConfig['label'] ?>
                                </div>
                            </div>
                            
                            <div class="social-section pt-3 border-top mt-3">
                                <span class="d-block text-muted small fw-600 mb-1">Social Profile</span>
                                <?php if($inst->social_links): ?>
                                    <a href="<?= esc($inst->social_links) ?>" target="_blank" class="text-decoration-none small text-primary text-truncate d-block">
                                        <?= esc($inst->social_links) ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted opacity-50 small">No Social Profile Link</span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="pt-3 border-top mb-3 mt-auto">
                            <span class="small fw-600 text-dark">Active Batches: <span class="fw-bold ms-1"><?= $inst->active_batches_count ?? 0 ?></span></span>
                        </div>

                        <?php if($status === 'not_uploaded'): ?>
                            <button class="btn btn-light-primary w-100 fw-bold border-0 mt-1" onclick='editInstructor(<?= json_encode($inst) ?>)'>
                                Upload Document
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Instructor Modal -->
<div class="modal fade" id="instructorModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle">Add New Instructor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="instructorForm">
                <input type="hidden" name="id" id="inst_id">
                <input type="hidden" name="remove_kyc" id="remove_kyc" value="0">
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="form-label">Full Name <span>*</span></label>
                        <input type="text" name="name" id="inst_name" class="form-control" placeholder="e.g. John Doe" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Experience / Designation <span>*</span></label>
                        <textarea name="experience" id="inst_exp" class="form-control" rows="3" placeholder="Describe their expertise and background..." required></textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Social Profile Link</label>
                        <input type="url" name="social_links" id="inst_links" class="form-control" placeholder="LinkedIn, Instagram or Website URL">
                    </div>
                    <div class="form-group mb-0">
                        <label class="form-label">Identity Document (Optional)</label>
                        <div class="upload-area border-dashed rounded-lg p-5 text-center bg-light" id="kycDropZone" style="cursor: pointer; border: 2px dashed #D1D5DB; border-radius: 12px;">
                            <i class="bi bi-upload fs-2 text-muted opacity-50 mb-3 d-block"></i>
                            <p class="small mb-1 fw-bold text-dark">Upload Aadhaar/PAN/Passport</p>
                        </div>
                        <input type="file" name="kyc_doc" id="inst_kyc" class="d-none" accept=".pdf,image/*">
                        <div id="kycFileName" class="small text-primary mt-2 fw-bold text-center"></div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 d-flex gap-3">
                    <button type="button" class="btn btn-outline-secondary flex-fill fw-bold py-3" data-bs-dismiss="modal" style="border-radius: 10px; border-color: #E5E7EB; color: #374151;">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary-provider flex-fill fw-bold py-3" id="btnSaveInst" style="border-radius: 10px;">
                        Add Instructor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(function() {
    const modal = new bootstrap.Modal(document.getElementById('instructorModal'));
    const form = document.getElementById('instructorForm');

    // Filter Logic
    $('.filter-pill').on('click', function() {
        $('.filter-pill').removeClass('active');
        $(this).addClass('active');
        
        const status = $(this).data('status');
        if (status === 'all') {
            $('.instructor-item').fadeIn(200);
        } else {
            $('.instructor-item').hide();
            $(`.instructor-item[data-status="${status}"]`).fadeIn(200);
        }
    });

    // Modal Handling
    window.editInstructor = function(inst) {
        $('#modalTitle').text('Edit Instructor');
        $('#inst_id').val(inst.id);
        $('#inst_name').val(inst.name);
        $('#inst_exp').val(inst.experience);
        $('#inst_links').val(inst.social_links);
        $('#remove_kyc').val('0');

        if (inst.kyc_doc) {
            $('#kycFileName').html(`
                <div class="d-grid gap-2 border rounded-3 p-2 bg-white mt-1 shadow-sm">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-file-check-fill text-success fs-5"></i>
                        <div class="overflow-hidden">
                            <span class="text-dark small d-block text-truncate fw-bold">${inst.kyc_doc.split('/').pop()}</span>
                            <span class="x-small text-success font-monospace">Already Uploaded</span>
                        </div>
                        <div class="ms-auto d-flex gap-1">
                            <a href="<?= base_url() ?>/${inst.kyc_doc}" target="_blank" class="btn btn-sm btn-light border p-1 px-2" title="View Document">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-outline-danger p-1 px-2" onclick="removeInstructorKYC()" title="Delete Document">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `);
        } else {
            $('#kycFileName').empty();
        }
        modal.show();
    };

    window.removeInstructorKYC = function() {
        $('#remove_kyc').val('1');
        $('#kycFileName').empty();
    };

    $('#instructorModal').on('hidden.bs.modal', function () {
        form.reset();
        $('#modalTitle').text('Add New Instructor');
        $('#inst_id').val('');
        $('#remove_kyc').val('0');
        $('#kycFileName').empty();
    });

    // File Upload Preview
    $('#kycDropZone').on('click', () => $('#inst_kyc').click());
    $('#inst_kyc').on('change', function() {
        const fileName = this.files.length > 0 ? this.files[0].name : '';
        $('#kycFileName').text(fileName ? 'Selected: ' + fileName : '');
    });

    // Form Submit
    $('#instructorForm').on('submit', async function(e) {
        e.preventDefault();
        const btn = $('#btnSaveInst');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Saving...');

        const formData = new FormData(this);
        try {
            const res = await fetch('<?= base_url('provider/api/instructors/save') ?>', {
                method: 'POST',
                body: formData
            });
            const json = await res.json();
            if(json.success) {
                Swal.fire({ icon: 'success', title: 'Saved!', text: json.message }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Oops', text: json.message });
                btn.prop('disabled', false).text('Save Instructor');
            }
        } catch(e) {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Network error occurred.' });
            btn.prop('disabled', false).text('Save Instructor');
        }
    });

    window.deleteInstructor = function(id) {
        Swal.fire({
            title: 'Delete Instructor?',
            text: "This may affect classes assigned to them.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#4F46E5',
            cancelButtonColor: '#DC2626',
            confirmButtonText: 'Yes, delete!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.post('<?= base_url('provider/api/instructors/delete') ?>', {id: id}, function(res) {
                    if (res.success) {
                        Swal.fire('Deleted!', '', 'success').then(() => window.location.reload());
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                });
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
