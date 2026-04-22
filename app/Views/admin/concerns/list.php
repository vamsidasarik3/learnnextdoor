<?= $this->extend('admin/layout/default') ?>
<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 fw-900 border-left border-4 border-warning pl-3">Provider Concerns</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white py-3">
                <h3 class="card-title fw-bold text-muted small text-uppercase">Active Issues & Support Requests</h3>
                <div class="card-tools">
                    <span class="badge badge-warning rounded-pill px-3">Resolved issues older than 7 days are auto-deleted</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 datatable-concerns">
                        <thead class="bg-light text-muted uppercase small fw-bold">
                            <tr>
                                <th class="px-4 py-3">ID / Date</th>
                                <th>Provider Info</th>
                                <th>Concern / Issue Details</th>
                                <th>Status</th>
                                <th class="px-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($concerns)): ?>
                                <tr><td colspan="5" class="text-center py-5 text-muted">No provider concerns found.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($concerns as $c): ?>
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-bold">#CON-<?= $c->id ?></div>
                                        <small class="text-muted"><?= date('d M Y, h:i A', strtotime($c->created_at)) ?></small>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark"><?= esc($c->provider_name) ?></div>
                                        <small class="text-muted"><?= esc($c->provider_phone) ?></small>
                                    </td>
                                    <td>
                                        <div class="text-dark small lh-base" style="max-width: 400px; white-space: normal;">
                                            <?= nl2br(esc($c->issue)) ?>
                                        </div>
                                        <?php if ($c->status == 'resolved'): ?>
                                            <div class="mt-2 p-2 bg-light rounded-3 border-left border-3 border-success small">
                                                <strong class="text-success text-uppercase text-xs d-block">Resolution Note:</strong>
                                                <?= esc($c->resolution_note) ?>
                                                <div class="text-xs text-muted mt-1 italic">Resolved on <?= date('d M, h:i A', strtotime($c->resolved_at)) ?></div>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($c->status == 'open'): ?>
                                            <span class="badge badge-danger rounded-pill px-3 py-1 fw-600 shadow-sm">
                                                <i class="fas fa-exclamation-circle mr-1"></i> OPEN
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-success rounded-pill px-3 py-1 fw-600">
                                                <i class="fas fa-check-circle mr-1"></i> RESOLVED
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-4 text-right">
                                        <?php if ($c->status == 'open'): ?>
                                            <button class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold shadow-sm" onclick="openResolveModal(<?= $c->id ?>)">
                                                <i class="fas fa-comment-dots mr-1"></i> Resolve
                                            </button>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-link text-muted" disabled>
                                                <i class="fas fa-check"></i> Handled
                                            </button>
                                        <?php endif; ?>
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

<!-- Resolve Modal -->
<div class="modal fade" id="resolveModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-tools mr-2"></i> Resolve Concern</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <form id="resolveForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="concernId">
                    <div class="form-group mb-0">
                        <label class="small fw-bold text-uppercase">Resolution Note <span class="text-danger">*</span></label>
                        <textarea name="note" class="form-control rounded-3 border-primary shadow-sm" rows="4" placeholder="Explain how this issue was resolved or provide an answer..." required></textarea>
                        <small class="form-text text-muted mt-2">This note will be logged and visible in future audits. Please be detailed.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold" id="resolveSubmitBtn">Mark as Resolved</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.fw-900 { font-weight: 900; }
.rounded-4 { border-radius: 1rem !important; }
.italic { font-style: italic; }
.text-xs { font-size: 0.7rem; }
</style>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(document).ready(function() {
    $('.datatable-concerns').DataTable({
        "order": [[ 0, "desc" ]]
    });
});

function openResolveModal(id) {
    $('#concernId').val(id);
    $('#resolveModal').modal('show');
}

$('#resolveForm').on('submit', function(e) {
    e.preventDefault();
    const btn = $('#resolveSubmitBtn');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Resolving...');

    $.post('<?= url('admin/api/concerns/resolve') ?>', $(this).serialize(), function(res) {
        if (res.success) {
            toastr.success(res.message);
            setTimeout(() => location.reload(), 1000);
        } else {
            toastr.error(res.message);
            btn.prop('disabled', false).text('Mark as Resolved');
        }
    });
});
</script>
<?= $this->endSection() ?>
